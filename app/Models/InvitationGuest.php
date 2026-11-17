<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvitationGuest extends Model
{
    use HasFactory;

    public const CATEGORY_GENERAL = 'general';

    public const CATEGORY_VIP = 'vip';

    public const CATEGORY_FAMILY_GROOM = 'family_groom';

    public const CATEGORY_FAMILY_BRIDE = 'family_bride';

    public const CATEGORY_KIDS = 'kids';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ATTENDING = 'attending';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_TENTATIVE = 'tentative';

    private const PASS_CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    protected $fillable = [
        'invitation_id',
        'name',
        'phone',
        'category',
        'max_adults',
        'max_children',
        'invite_token',
        'qr_token',
        'pass_code',
        'rsvp_status',
        'confirmed_adults',
        'confirmed_children',
        'rsvp_response_id',
        'is_self_registered',
        'invited_at',
        'reminded_at',
        'notes',
    ];

    protected $hidden = [
        'invite_token',
        'qr_token',
    ];

    protected $casts = [
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'confirmed_adults' => 'integer',
        'confirmed_children' => 'integer',
        'is_self_registered' => 'boolean',
        'invited_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (InvitationGuest $guest): void {
            if (empty($guest->invite_token)) {
                $guest->invite_token = Str::random(36);
            }

            if (empty($guest->qr_token)) {
                $guest->qr_token = Str::random(36);
            }

            if (empty($guest->pass_code)) {
                $guest->pass_code = self::generateUniquePassCode((int) $guest->invitation_id);
            }
        });
    }

    public static function generateUniquePassCode(int $invitationId): string
    {
        $alphabet = self::PASS_CODE_ALPHABET;
        $maxIndex = strlen($alphabet) - 1;

        for ($attempt = 0; $attempt < 25; $attempt++) {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, $maxIndex)];
            }

            $exists = static::query()
                ->where('invitation_id', $invitationId)
                ->where('pass_code', $code)
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        return strtoupper(Str::random(8));
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_GENERAL,
            self::CATEGORY_VIP,
            self::CATEGORY_FAMILY_GROOM,
            self::CATEGORY_FAMILY_BRIDE,
            self::CATEGORY_KIDS,
        ];
    }

    public static function categoryLabel(string $category, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $isEn = $locale === 'en';

        return match ($category) {
            self::CATEGORY_VIP => $isEn ? 'VIP' : 'كبار الشخصيات (VIP)',
            self::CATEGORY_FAMILY_GROOM => $isEn ? "Groom's Family" : 'أهل العريس',
            self::CATEGORY_FAMILY_BRIDE => $isEn ? "Bride's Family" : 'أهل العروس',
            self::CATEGORY_KIDS => $isEn ? 'Kids Invited' : 'دعوة أطفال',
            default => $isEn ? 'General' : 'عام',
        };
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvpResponse(): BelongsTo
    {
        return $this->belongsTo(RsvpResponse::class);
    }

    public function getPersonalInviteUrl(): string
    {
        return route('invitation.show', ['slug' => $this->invitation->slug])
            .'?g='.$this->invite_token;
    }

    public function getPassUrl(): string
    {
        return route('guest.pass', ['qrToken' => $this->qr_token]);
    }

    public function getAllocatedTotal(): int
    {
        return max(1, $this->max_adults + $this->max_children);
    }

    public function getExpectedPartySize(): int
    {
        if ($this->rsvp_status === self::STATUS_DECLINED) {
            return 0;
        }

        if ($this->rsvp_status === self::STATUS_ATTENDING) {
            $confirmed = $this->confirmed_adults + $this->confirmed_children;

            return $confirmed > 0 ? $confirmed : $this->getAllocatedTotal();
        }

        return $this->getAllocatedTotal();
    }

    public function canReceiveQrPass(): bool
    {
        if (! $this->invitation || ! $this->invitation->supportsQrPass()) {
            return false;
        }

        if ($this->rsvp_status === self::STATUS_DECLINED) {
            return false;
        }

        if ($this->invitation->effectiveMode() === Invitation::MODE_QR_ONLY) {
            return true;
        }

        return in_array($this->rsvp_status, [self::STATUS_ATTENDING, self::STATUS_TENTATIVE], true);
    }
}
