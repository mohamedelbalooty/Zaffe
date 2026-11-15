<?php

namespace App\Models;

use App\Services\InvitationDateFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Invitation extends Model
{
    use HasFactory;

    public const MODE_RSVP_QR = 'rsvp_qr';

    public const MODE_RSVP_ONLY = 'rsvp_only';

    public const MODE_QR_ONLY = 'qr_only';

    public const MODE_ANNOUNCEMENT = 'announcement';

    protected static function booted(): void
    {
        static::saving(function (Invitation $invitation): void {
            if ($invitation->isDirty('invitation_mode') && ! $invitation->isDirty('is_rsvp_enabled')) {
                $invitation->is_rsvp_enabled = in_array(
                    $invitation->invitation_mode,
                    [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY],
                    true
                );
            } elseif ($invitation->isDirty('is_rsvp_enabled') && ! $invitation->isDirty('invitation_mode')) {
                if (! $invitation->is_rsvp_enabled
                    && in_array($invitation->invitation_mode ?: self::MODE_RSVP_QR, [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true)) {
                    $invitation->invitation_mode = self::MODE_ANNOUNCEMENT;
                } elseif ($invitation->is_rsvp_enabled && $invitation->invitation_mode === self::MODE_ANNOUNCEMENT) {
                    $invitation->invitation_mode = self::MODE_RSVP_QR;
                }
            }
        });

        static::deleting(function (Invitation $invitation): void {
            $isLocalPath = static fn (?string $path): bool => filled($path)
                && ! str_starts_with($path, 'http://')
                && ! str_starts_with($path, 'https://')
                && ! str_starts_with($path, '/images/');

            if ($isLocalPath($invitation->cover_photo)) {
                Storage::disk('public')->delete($invitation->cover_photo);
            }

            foreach ($invitation->photos()->get(['image_path']) as $photo) {
                if ($isLocalPath($photo->image_path)) {
                    Storage::disk('public')->delete($photo->image_path);
                }
            }
        });
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

    public function gates(): HasMany
    {
        return $this->hasMany(InvitationGate::class)->latest();
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
            self::MODE_QR_ONLY => $isEn ? 'Direct QR Entry Pass Only' : 'كود دخول QR فقط (بدون استمارة رد)',
            self::MODE_ANNOUNCEMENT => $isEn ? 'Announcement / Card Only' : 'دعوة للعرض فقط (بدون رد أو باركود)',
            default => $isEn ? 'RSVP + Single-Use QR Entry Pass' : 'تأكيد الحضور + كود دخول QR ذكي',
        };
    }

    public function effectiveMode(): string
    {
        $mode = $this->invitation_mode ?: self::MODE_RSVP_QR;

        if (! $this->is_rsvp_enabled && in_array($mode, [self::MODE_RSVP_QR, self::MODE_RSVP_ONLY], true)) {
            return self::MODE_ANNOUNCEMENT;
        }

        return $mode;
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
        if ($lang === 'en') {
            return "We are delighted to invite you to share in our special day ❤️\n\n".
                "Wedding of {$this->groom_name} & {$this->bride_name}\n".
                'Date: '.$this->wedding_date->format('M d, Y')."\n".
                "Venue: {$this->venue_name}\n\n".
                "Please view your invitation & confirm attendance here:\n".$url;
        }

        return "يسعدنا ويشرفنا دعوتكم لمشاركتنا أجمل أيام حياتنا وبدء فصل جديد معاً ❤️\n\n".
            "حفل زفاف: {$this->groom_name} و {$this->bride_name}\n".
            'الموعد: '.InvitationDateFormatter::formatGregorianDate($this, 'ar')."\n".
            "المكان: {$this->venue_name}\n\n".
            "تفاصيل الدعوة وتأكيد الحضور:\n".$url;
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

        if ($lang === 'en') {
            $intro = $isReminder
                ? "Friendly reminder for our upcoming celebration — we would love to celebrate with {$guest->name}"
                : "Dear {$guest->name}, we are delighted to invite you to the wedding of {$this->groom_name} & {$this->bride_name}";

            $text = "{$intro}\n"
                ."Date: {$date}\n"
                ."Venue: {$this->venue_name}\n"
                ."Allocated seats: {$guest->max_adults} adult(s)".($guest->max_children > 0 ? ", {$guest->max_children} child(ren)" : '')."\n\n"
                ."Your personal invitation link:\n{$personalUrl}";

            if ($this->effectiveMode() === self::MODE_QR_ONLY) {
                $text .= "\n\nYour direct QR entry pass:\n{$passUrl}";
            }
        } else {
            $intro = $isReminder
                ? "تذكير لطيف بحفل زفافنا السعيد — يسعدنا تشريفكم {$guest->name}"
                : "الأستاذ/ة {$guest->name} المحترم/ة، يسعدنا ويشرفنا دعوتكم لحضور حفل زفاف {$this->groom_name} و {$this->bride_name}";

            $kidsPart = $guest->max_children > 0 ? " و {$guest->max_children} أطفال" : '';
            $text = "{$intro}\n"
                ."الموعد: {$date}\n"
                ."المكان: {$this->venue_name}\n"
                ."المقاعد المخصصة: {$guest->max_adults} بالغين{$kidsPart}\n\n"
                ."رابط دعوتك الخاصة:\n{$personalUrl}";

            if ($this->effectiveMode() === self::MODE_QR_ONLY) {
                $text .= "\n\nبطاقة الدخول المباشرة (QR):\n{$passUrl}";
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
    public function guestCheckInStats(): array
    {
        $guests = $this->guests()->get([
            'rsvp_status',
            'max_adults',
            'max_children',
            'confirmed_adults',
            'confirmed_children',
            'checked_in_at',
            'checked_in_count',
        ]);

        $attending = $guests->where('rsvp_status', InvitationGuest::STATUS_ATTENDING);
        $declined = $guests->where('rsvp_status', InvitationGuest::STATUS_DECLINED);
        $pending = $guests->whereIn('rsvp_status', [InvitationGuest::STATUS_PENDING, InvitationGuest::STATUS_TENTATIVE]);
        $checkedIn = $guests->filter(fn (InvitationGuest $g) => $g->checked_in_at !== null);

        $attendingAdults = (int) $attending->sum(fn (InvitationGuest $g) => $g->confirmed_adults > 0 ? $g->confirmed_adults : $g->max_adults);
        $attendingChildren = (int) $attending->sum('confirmed_children');
        $attendingHeads = $attendingAdults + $attendingChildren;
        $checkedInHeads = (int) $checkedIn->sum(fn (InvitationGuest $g) => $g->checked_in_count > 0 ? $g->checked_in_count : 1);

        $expectedTotalHeads = $attendingHeads > 0
            ? $attendingHeads
            : (int) $guests->reject(fn (InvitationGuest $g) => $g->rsvp_status === InvitationGuest::STATUS_DECLINED)
                ->sum(fn (InvitationGuest $g) => $g->getAllocatedTotal());

        return [
            'total_invited' => $guests->count(),
            'attending_parties' => $attending->count(),
            'attending_adults' => $attendingAdults,
            'attending_children' => $attendingChildren,
            'attending_heads' => $attendingHeads,
            'declined_parties' => $declined->count(),
            'pending_parties' => $pending->count(),
            'checked_in_parties' => $checkedIn->count(),
            'checked_in_heads' => $checkedInHeads,
            'remaining_parties' => max(0, $attending->count() - $checkedIn->count()),
            'remaining_heads' => max(0, $expectedTotalHeads - $checkedInHeads),
        ];
    }
}
