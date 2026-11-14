<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getPublicUrlAttribute(): string
    {
        return url('/invitation/' . $this->slug);
    }

    public function getEffectiveStylesAttribute(): array
    {
        $defaultStyles = $this->template?->default_styles ?? [
            'primary_color' => '#C6A76B',
            'secondary_color' => '#292521',
            'background_color' => '#FAF7F2',
            'accent_color' => '#D9B7B0',
            'font_family' => 'Tajawal',
        ];

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
            "الموعد: " . $this->wedding_date->translatedFormat('l, d F Y') . "\n" .
            "المكان: {$this->venue_name}\n\n" .
            "تفاصيل الدعوة وتأكيد الحضور:\n" . $url;
    }

    public function getWhatsAppShareUrl(string $lang = 'ar'): string
    {
        $text = $this->getWhatsAppShareText($lang);
        return 'https://api.whatsapp.com/send?text=' . rawurlencode($text);
    }
}

