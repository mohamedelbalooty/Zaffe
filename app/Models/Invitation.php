<?php

namespace App\Models;

use App\Services\InvitationDateFormatter;
use App\Services\OgImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

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

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            if (empty($invitation->slug)) {
                $invitation->slug = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = Str::random($length);
        } while (static::where('slug', $code)->exists());

        return $code;
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

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getCodeAttribute(): string
    {
        return (string) $this->slug;
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/i/' . $this->slug);
    }

    public function getOgTitleAttribute(): string
    {
        $groom = trim((string) $this->groom_name);
        $bride = trim((string) $this->bride_name);

        if ($groom !== '' && $bride !== '') {
            $coupleLabel = "{$groom} و {$bride}";
        } else {
            $coupleLabel = $groom !== '' ? $groom : $bride;
        }

        $title = trim((string) $this->title);
        $hasGroom = $groom === '' || str_contains($title, $groom);
        $hasBride = $bride === '' || str_contains($title, $bride);

        if ($title !== '' && $hasGroom && $hasBride) {
            return "{$title} — زفّة";
        }

        if ($coupleLabel !== '') {
            return "حفل زفاف {$coupleLabel}" . ($title !== '' ? " ({$title})" : '') . ' — زفّة';
        }

        return ($title !== '' ? $title : 'دعوة زفاف رقمية') . ' — زفّة';
    }

    public function getOgDescriptionAttribute(): string
    {
        $message = trim((string) preg_replace('/\s+/u', ' ', (string) $this->invitation_message));
        if ($message !== '') {
            return Str::limit($message, 200);
        }

        $groom = trim((string) $this->groom_name);
        $bride = trim((string) $this->bride_name);
        $coupleLabel = ($groom !== '' && $bride !== '') ? "{$groom} و {$bride}" : ($groom !== '' ? $groom : $bride);
        $venue = trim((string) $this->venue_name);

        return 'نتشرف بدعوتكم لحضور حفل زفاف ' . $coupleLabel . ($venue !== '' ? ' في ' . $venue : '');
    }

    public function getOgImageUrlAttribute(): string
    {
        return app(OgImageService::class)->forInvitation($this);
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
            return "We are delighted to invite you to share in our special day ❤️\n\n" .
                "Wedding of {$this->groom_name} & {$this->bride_name}\n" .
                "Date: " . $this->wedding_date->format('M d, Y') . "\n" .
                "Venue: {$this->venue_name}\n\n" .
                "Please view your invitation & confirm attendance here:\n" . $url;
        }

        return "يسعدنا ويشرفنا دعوتكم لمشاركتنا أجمل أيام حياتنا وبدء فصل جديد معاً ❤️\n\n" .
            "حفل زفاف: {$this->groom_name} و {$this->bride_name}\n" .
            "الموعد: " . InvitationDateFormatter::formatGregorianDate($this, 'ar') . "\n" .
            "المكان: {$this->venue_name}\n\n" .
            "تفاصيل الدعوة وتأكيد الحضور:\n" . $url;
    }

    public function getWhatsAppShareUrl(?string $lang = null): string
    {
        $resolvedLang = $lang ?? ($this->effective_styles['locale'] ?? 'ar');
        $text = $this->getWhatsAppShareText($resolvedLang);

        return 'https://api.whatsapp.com/send?text=' . rawurlencode($text);
    }
}

