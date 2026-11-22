<?php

namespace App\Models;

use App\Jobs\PurgeInvitationMediaJob;
use App\Services\InvitationDateFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Invitation extends Model
{
    use HasFactory;

    public const MODE_RSVP_QR = 'rsvp_qr';

    public const MODE_RSVP_ONLY = 'rsvp_only';

    public const MODE_QR_ONLY = 'qr_only';

    public const MODE_ANNOUNCEMENT = 'announcement';

    /**
     * Normalized public disk paths captured before SQL deletion and purged after transaction commit.
     *
     * @var array<int, string>
     */
    protected array $pendingMediaPurgePaths = [];

    protected static function booted(): void
    {
        static::saving(function (Invitation $invitation): void {
            if ($invitation->isDirty('invitation_mode')) {
                $invitation->is_rsvp_enabled = in_array(
                    $invitation->effectiveMode(),
                    [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY],
                    true
                );
            } elseif ($invitation->isDirty('is_rsvp_enabled')) {
                $rawMode = $invitation->invitation_mode;
                if (! $invitation->is_rsvp_enabled
                    && (empty($rawMode) || in_array($rawMode, [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true))) {
                    $invitation->invitation_mode = self::MODE_ANNOUNCEMENT;
                } elseif ($invitation->is_rsvp_enabled
                    && ! in_array($rawMode, [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true)) {
                    $invitation->invitation_mode = self::MODE_RSVP_ONLY;
                }
            }
        });

        static::deleting(function (Invitation $invitation): void {
            $invitation->pendingMediaPurgePaths = $invitation->collectMediaPaths();
        });

        static::deleted(function (Invitation $invitation): void {
            $paths = $invitation->pendingMediaPurgePaths;
            $invitation->pendingMediaPurgePaths = [];

            self::schedulePostCommitMediaPurge($paths, $invitation->getConnectionName());
        });
    }

    /**
     * Defer uploaded media cleanup until the active database transaction commits.
     *
     * @param  array<int, string>  $paths
     */
    public static function schedulePostCommitMediaPurge(array $paths, ?string $connectionName = null): void
    {
        $uniquePaths = array_values(array_unique(array_filter(
            $paths,
            static fn (mixed $path): bool => is_string($path) && $path !== ''
        )));

        if ($uniquePaths === []) {
            return;
        }

        DB::connection($connectionName)->afterCommit(static function () use ($uniquePaths): void {
            self::purgeMediaPaths($uniquePaths);
        });
    }

    public static function resolvePublicDiskPath(?string $pathOrUrl): ?string
    {
        if ($pathOrUrl === null) {
            return null;
        }

        $candidate = trim($pathOrUrl);
        if ($candidate === '' || str_starts_with($candidate, '/images/') || str_contains($candidate, '..')) {
            return null;
        }

        $disk = Storage::disk('public');
        $diskBaseUrl = rtrim((string) $disk->url(''), '/').'/';

        if (str_starts_with($candidate, $diskBaseUrl)) {
            $relative = substr($candidate, strlen($diskBaseUrl));
        } elseif (str_starts_with($candidate, 'http://') || str_starts_with($candidate, 'https://')) {
            $parsed = parse_url($candidate);
            $path = is_array($parsed) ? ($parsed['path'] ?? '') : '';
            $host = is_array($parsed) ? strtolower((string) ($parsed['host'] ?? '')) : '';

            $allowedHosts = array_filter([
                strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST)),
                strtolower((string) parse_url($diskBaseUrl, PHP_URL_HOST)),
                'localhost',
                '127.0.0.1',
            ]);

            if (! str_starts_with($path, '/storage/') || ($host !== '' && ! in_array($host, $allowedHosts, true))) {
                return null;
            }

            $relative = substr($path, strlen('/storage/'));
        } elseif (str_starts_with($candidate, '/storage/')) {
            $relative = substr($candidate, strlen('/storage/'));
        } elseif (str_starts_with($candidate, '/')) {
            return null;
        } else {
            $relative = $candidate;
        }

        $normalized = ltrim((string) $relative, '/');

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * Collect normalized public-disk media paths belonging to this invitation.
     *
     * @return array<int, string>
     */
    public function collectMediaPaths(): array
    {
        $rawPaths = [$this->cover_photo];

        $galleryPaths = $this->relationLoaded('photos')
            ? $this->photos->pluck('image_path')->all()
            : $this->photos()->pluck('image_path')->all();

        $normalized = [];
        foreach (array_merge($rawPaths, $galleryPaths) as $raw) {
            $resolved = self::resolvePublicDiskPath(is_string($raw) ? $raw : null);
            if ($resolved !== null) {
                $normalized[] = $resolved;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Best-effort post-commit purge of uploaded media paths with retry dispatch on failure.
     *
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    public static function purgeMediaPaths(array $paths, bool $dispatchRetryOnFailure = true): array
    {
        if ($paths === []) {
            return [];
        }

        $disk = Storage::disk('public');
        $failedPaths = [];

        foreach (array_values(array_unique($paths)) as $path) {
            if ($path === '') {
                continue;
            }

            try {
                if ($disk->exists($path) && ! $disk->delete($path)) {
                    $failedPaths[] = $path;
                    Log::warning('Failed to purge invitation media file from public disk.', [
                        'path' => $path,
                        'reason' => 'delete_returned_false',
                    ]);
                }
            } catch (\Throwable $e) {
                $failedPaths[] = $path;
                Log::warning('Failed to purge invitation media file from public disk.', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($failedPaths !== [] && $dispatchRetryOnFailure) {
            try {
                PurgeInvitationMediaJob::dispatch($failedPaths);
            } catch (\Throwable $e) {
                Log::error('Failed to enqueue retry job for invitation media purge.', [
                    'paths' => $failedPaths,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $failedPaths;
    }

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'slug',
        'groom_name',
        'bride_name',
        'wedding_date',
        'wedding_time',
        'timezone',
        'venue_name',
        'venue_address',
        'venue_map_url',
        'welcome_message',
        'invitation_message',
        'cover_photo',
        'music_url',
        'custom_styles',
        'status',
        'is_rsvp_enabled',
        'invitation_mode',
        'rsvp_deadline',
        'max_guests_per_rsvp',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'rsvp_deadline' => 'date',
            'custom_styles' => 'array',
            'is_rsvp_enabled' => 'boolean',
            'max_guests_per_rsvp' => 'integer',
            'view_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InvitationTemplate::class, 'template_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InvitationSection::class)->orderBy('sort_order');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InvitationPhoto::class)->orderBy('sort_order');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(RsvpResponse::class)->latest();
    }

    public function guests(): HasMany
    {
        return $this->hasMany(InvitationGuest::class)->latest();
    }

    public static function modes(): array
    {
        return [
            self::MODE_RSVP_QR,
            self::MODE_RSVP_ONLY,
            self::MODE_QR_ONLY,
            self::MODE_ANNOUNCEMENT,
        ];
    }

    public static function modeLabel(string $mode, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $isEn = $locale === 'en';

        return match ($mode) {
            self::MODE_RSVP_ONLY => $isEn ? 'RSVP Confirmation Only' : 'تأكيد الحضور فقط (بدون باركود)',
            self::MODE_QR_ONLY => $isEn ? 'Direct QR Pass Only' : 'بطاقة دعوة QR فقط (بدون استمارة رد)',
            self::MODE_ANNOUNCEMENT => $isEn ? 'Announcement / Card Only' : 'دعوة للعرض فقط (بدون رد أو باركود)',
            default => $isEn ? 'RSVP + Personal QR Pass' : 'تأكيد الحضور + بطاقة QR شخصية',
        };
    }

    public function effectiveMode(): string
    {
        $mode = $this->invitation_mode;
        if (empty($mode)) {
            $mode = $this->is_rsvp_enabled ? self::MODE_RSVP_ONLY : self::MODE_ANNOUNCEMENT;
        }

        if (! $this->is_rsvp_enabled && in_array($mode, [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true)) {
            return self::MODE_ANNOUNCEMENT;
        }

        return $mode;
    }

    public function hasBrideName(): bool
    {
        return filled(trim((string) ($this->bride_name ?? '')));
    }

    public function formattedCoupleNames(string $locale = 'ar', ?string $separator = null): string
    {
        $groom = trim((string) ($this->groom_name ?? ''));
        $bride = trim((string) ($this->bride_name ?? ''));

        if ($bride === '') {
            return $groom;
        }

        if ($groom === '') {
            return $bride;
        }

        $sep = $separator ?? ($locale === 'en' ? ' & ' : ' و ');

        return $groom.$sep.$bride;
    }

    public function hasLegacyRosterAccess(): bool
    {
        if (in_array($this->effectiveMode(), [self::MODE_RSVP_QR, self::MODE_QR_ONLY], true)) {
            return true;
        }

        return $this->relationLoaded('guests')
            ? $this->guests->isNotEmpty()
            : $this->guests()->exists();
    }

    public function allowsRsvp(): bool
    {
        return (bool) $this->is_rsvp_enabled
            && in_array($this->effectiveMode(), [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true);
    }

    public function supportsQrPass(): bool
    {
        return in_array($this->effectiveMode(), [self::MODE_RSVP_QR, self::MODE_QR_ONLY], true);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/invitation/'.$this->slug);
    }

    public function getEffectiveStylesAttribute(): array
    {
        $dateDefaults = [
            'locale' => InvitationDateFormatter::DEFAULT_LOCALE,
            'digits' => InvitationDateFormatter::DEFAULT_DIGITS,
            'show_hijri' => true,
        ];

        $defaultStyles = array_merge(
            $dateDefaults,
            $this->template?->default_styles ?? [
                'primary_color' => '#C6A76B',
                'secondary_color' => '#292521',
                'background_color' => '#FAF7F2',
                'accent_color' => '#D9B7B0',
                'font_family' => 'Tajawal',
            ]
        );

        return array_merge($defaultStyles, $this->custom_styles ?? []);
    }

    public function getWhatsAppShareText(string $lang = 'ar'): string
    {
        $url = $this->public_url;
        $coupleNames = $this->formattedCoupleNames($lang);
        $allowsRsvp = $this->allowsRsvp();

        if ($lang === 'en') {
            $gregorianDate = InvitationDateFormatter::formatGregorianDate($this, 'en');
            $time = InvitationDateFormatter::formatTime($this, 'en');

            $lines = [
                'You are cordially invited to attend our wedding celebration.',
                '',
                "Wedding of {$coupleNames}",
                "Date: {$gregorianDate}",
            ];

            if ($time !== null) {
                $lines[] = "Time: {$time}";
            }

            $lines[] = "Venue: {$this->venue_name}";
            $lines[] = '';
            $lines[] = $allowsRsvp
                ? "View full invitation details & confirm attendance:\n{$url}"
                : "View full invitation details & location:\n{$url}";

            return implode("\n", $lines);
        }

        $gregorianDate = InvitationDateFormatter::formatGregorianDate($this, 'ar');
        $hijriDate = InvitationDateFormatter::formatHijriDate($this, null, null, null, 'ar');
        $time = InvitationDateFormatter::formatTime($this, 'ar');

        $lines = [
            'السلام عليكم ورحمة الله وبركاته',
            "نتشرف بدعوتكم لحضور حفل زفاف {$coupleNames}",
            '',
            "التاريخ: {$gregorianDate}",
        ];

        if ($hijriDate !== null) {
            $lines[] = "الموافق: {$hijriDate}";
        }

        if ($time !== null) {
            $lines[] = "الوقت: {$time}";
        }

        $lines[] = "المكان: {$this->venue_name}";
        $lines[] = '';
        $lines[] = $allowsRsvp
            ? "للاطلاع على تفاصيل الدعوة والموقع وتأكيد الحضور:\n{$url}"
            : "للاطلاع على تفاصيل الدعوة والموقع:\n{$url}";

        return implode("\n", $lines);
    }

    public function getWhatsAppShareUrl(string $lang = 'ar'): string
    {
        $text = $this->getWhatsAppShareText($lang);

        return 'https://api.whatsapp.com/send?text='.rawurlencode($text);
    }

    public function getGuestWhatsAppInviteUrl(InvitationGuest $guest, string $lang = 'ar', bool $isReminder = false): string
    {
        $personalUrl = $guest->getPersonalInviteUrl();
        $passUrl = $guest->getPassUrl();
        $date = InvitationDateFormatter::formatGregorianDate($this, $lang);
        $coupleNames = $this->formattedCoupleNames($lang);

        if ($lang === 'en') {
            $intro = $isReminder
                ? "Friendly reminder for our upcoming celebration — we would love to celebrate with {$guest->name}"
                : "Dear {$guest->name}, we are delighted to invite you to the wedding of {$coupleNames}";

            $text = "{$intro}\n"
                ."Date: {$date}\n"
                ."Venue: {$this->venue_name}\n"
                ."Allocated seats: {$guest->max_adults} adult(s)".($guest->max_children > 0 ? ", {$guest->max_children} child(ren)" : '')."\n\n"
                ."Your personal invitation link:\n{$personalUrl}";

            if ($this->effectiveMode() === self::MODE_QR_ONLY) {
                $text .= "\n\nYour personal invitation pass:\n{$passUrl}";
            }
        } else {
            $intro = $isReminder
                ? "تذكير لطيف بحفل زفافنا السعيد — يسعدنا تشريفكم {$guest->name}"
                : "الأستاذ/ة {$guest->name} المحترم/ة، يسعدنا ويشرفنا دعوتكم لحضور حفل زفاف {$coupleNames}";

            $kidsPart = $guest->max_children > 0 ? " و {$guest->max_children} أطفال" : '';
            $text = "{$intro}\n"
                ."الموعد: {$date}\n"
                ."المكان: {$this->venue_name}\n"
                ."المقاعد المخصصة: {$guest->max_adults} بالغين{$kidsPart}\n\n"
                ."رابط دعوتك الخاصة:\n{$personalUrl}";

            if ($this->effectiveMode() === self::MODE_QR_ONLY) {
                $text .= "\n\nبطاقة الدعوة الشخصية المباشرة (QR):\n{$passUrl}";
            }
        }

        $digits = $guest->phone ? preg_replace('/\D+/', '', $guest->phone) : '';
        if ($digits !== '') {
            return 'https://wa.me/'.$digits.'?text='.rawurlencode($text);
        }

        return 'https://api.whatsapp.com/send?text='.rawurlencode($text);
    }

    /**
     * @return array<string, int>
     */
    public function guestRosterStats(): array
    {
        $guests = $this->guests()->get([
            'rsvp_status',
            'max_adults',
            'max_children',
            'confirmed_adults',
            'confirmed_children',
        ]);

        $attending = $guests->where('rsvp_status', InvitationGuest::STATUS_ATTENDING);
        $declined = $guests->where('rsvp_status', InvitationGuest::STATUS_DECLINED);
        $pending = $guests->whereIn('rsvp_status', [InvitationGuest::STATUS_PENDING, InvitationGuest::STATUS_TENTATIVE]);

        $attendingAdults = (int) $attending->sum(
            fn (InvitationGuest $g) => ($g->confirmed_adults + $g->confirmed_children) > 0
                ? $g->confirmed_adults
                : $g->max_adults
        );
        $attendingChildren = (int) $attending->sum(
            fn (InvitationGuest $g) => ($g->confirmed_adults + $g->confirmed_children) > 0
                ? $g->confirmed_children
                : $g->max_children
        );
        $attendingHeads = $attendingAdults + $attendingChildren;

        return [
            'total_invited' => $guests->count(),
            'attending_parties' => $attending->count(),
            'attending_adults' => $attendingAdults,
            'attending_children' => $attendingChildren,
            'attending_heads' => $attendingHeads,
            'declined_parties' => $declined->count(),
            'pending_parties' => $pending->count(),
        ];
    }
}
