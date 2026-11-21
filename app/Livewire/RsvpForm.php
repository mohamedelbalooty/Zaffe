<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\RsvpResponse;
use App\Notifications\NewRsvpNotification;
use App\Notifications\RsvpConfirmationNotification;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RsvpForm extends Component
{
    #[Locked]
    public int $invitationId;

    #[Locked]
    public ?int $guestId = null;

    public string $guest_name = '';

    public string $phone = '';

    public string $phone_country = 'SA';

    public string $email = '';

    public string $attendance_status = 'attending';

    public int $guest_count = 1;

    public int $children_count = 0;

    public string $message = '';

    public string $dietary_notes = '';

    public array $selected_dietary = [];

    public bool $isSubmitted = false;

    #[Locked]
    public int $maxGuests = 5;

    #[Locked]
    public int $maxChildren = 0;

    #[Locked]
    public ?string $issuedPassUrl = null;

    #[Locked]
    public ?string $issuedPassCode = null;

    #[Locked]
    public bool $passPreviouslyIssued = false;

    public function mount(int $invitationId, int $maxGuests = 5, ?int $guestId = null): void
    {
        $this->invitationId = $invitationId;
        $invitation = Invitation::find($invitationId);

        $this->phone_country = ($invitation && $invitation->timezone === 'Africa/Cairo') ? 'EG' : 'SA';

        $guest = $guestId
            ? InvitationGuest::query()
                ->where('id', $guestId)
                ->where('invitation_id', $invitationId)
                ->first()
            : null;

        if ($guest) {
            $this->guestId = $guest->id;
            $this->guest_name = $guest->name;
            $this->maxGuests = max(1, $guest->max_adults);
            $this->maxChildren = max(0, $guest->max_children);
            $this->attendance_status = $guest->rsvp_status === InvitationGuest::STATUS_DECLINED
                ? 'declined'
                : 'attending';
            $this->guest_count = min(
                $this->maxGuests,
                max(1, $guest->confirmed_adults > 0 ? $guest->confirmed_adults : 1)
            );
            $this->children_count = min($this->maxChildren, max(0, $guest->confirmed_children));

            if ($guest->phone) {
                if (str_starts_with($guest->phone, '+966')) {
                    $this->phone_country = 'SA';
                    $this->phone = substr($guest->phone, 4);
                } elseif (str_starts_with($guest->phone, '+20')) {
                    $this->phone_country = 'EG';
                    $this->phone = substr($guest->phone, 3);
                } else {
                    $this->phone_country = 'OTHER';
                    $this->phone = $guest->phone;
                }
            }

            if ($guest->canReceiveQrPass()) {
                $this->issuedPassUrl = $guest->getPassUrl();
                $this->issuedPassCode = $guest->pass_code;
            }
        } else {
            $this->maxGuests = $invitation ? $invitation->max_guests_per_rsvp : $maxGuests;
        }
    }

    protected function rules(): array
    {
        $invitation = Invitation::find($this->invitationId);
        $guest = $this->guestId
            ? InvitationGuest::query()
                ->where('id', $this->guestId)
                ->where('invitation_id', $this->invitationId)
                ->first()
            : null;

        $max = $guest
            ? max(1, $guest->max_adults)
            : ($invitation ? $invitation->max_guests_per_rsvp : $this->maxGuests);
        $maxChildren = $guest ? max(0, $guest->max_children) : 10;

        return [
            'guest_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'phone_country' => ['required', 'string', 'in:SA,EG,OTHER'],
            'email' => ['nullable', 'email', 'max:255'],
            'attendance_status' => ['required', 'in:attending,declined,tentative'],
            'guest_count' => $this->attendance_status === 'declined'
                ? ['nullable', 'integer', 'min:0', 'max:'.$max]
                : ['required', 'integer', 'min:1', 'max:'.$max],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:'.$maxChildren],
            'message' => ['nullable', 'string', 'max:1000'],
            'dietary_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'guest_name.required' => 'الرجاء إدخال الاسم الكريم.',
        'guest_name.min' => 'الاسم يجب ألا يقل عن 3 أحرف.',
        'guest_count.min' => 'عدد الضيوف لا يقل عن 1.',
        'guest_count.max' => 'أقصى عدد للضيوف مسموح به في هذا الحجز هو :max.',
        'children_count.max' => 'أقصى عدد للأطفال المخصص لهذه الدعوة هو :max.',
    ];

    public function submit()
    {
        $invitation = Invitation::findOrFail($this->invitationId);

        // Security & business validation checks
        if (! $invitation->isPublished()) {
            $this->addError('guest_name', 'هذه الدعوة غير متاحة لتأكيد الحضور حالياً.');

            return;
        }

        if (! $invitation->allowsRsvp()) {
            $this->addError('guest_name', 'تم إغلاق استقبال تأكيدات الحضور لهذه الدعوة.');

            return;
        }

        if ($invitation->rsvp_deadline && now()->startOfDay()->gt($invitation->rsvp_deadline)) {
            $this->addError('guest_name', 'عذراً، لقد انتهت المهلة المحددة لتأكيد الحضور.');

            return;
        }

        // Rate limiting: max 10 submissions per minute per IP
        $rateKey = 'rsvp:'.request()->ip().':'.$invitation->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $this->addError('guest_name', 'يرجى الانتظار قليلاً قبل إرسال تأكيد حضور جديد.');

            return;
        }

        $this->validate();

        // Validate and normalize phone number if provided
        $normalizedPhone = null;
        if (! empty($this->phone)) {
            if (! PhoneService::isValid($this->phone, $this->phone_country)) {
                $this->addError('phone', 'رقم الهاتف المدخل غير صالح. يرجى التأكد من كتابة الرقم بشكل صحيح.');

                return;
            }
            $normalizedPhone = PhoneService::normalize($this->phone, $this->phone_country);
        }

        RateLimiter::hit($rateKey, 60);

        // Process dietary notes combining checkboxes and custom text
        $dietaryList = $this->selected_dietary;
        if (! empty($this->dietary_notes)) {
            $dietaryList[] = trim($this->dietary_notes);
        }
        $combinedDietary = ! empty($dietaryList) ? implode('، ', array_unique($dietaryList)) : null;

        $linkedGuest = $this->guestId
            ? InvitationGuest::query()
                ->where('id', $this->guestId)
                ->where('invitation_id', $invitation->id)
                ->first()
            : null;

        if (! $linkedGuest && $normalizedPhone !== null) {
            $rosterMatch = InvitationGuest::query()
                ->where('invitation_id', $invitation->id)
                ->where('phone', $normalizedPhone)
                ->where('is_self_registered', false)
                ->first();

            if ($rosterMatch) {
                $this->addError('phone', 'هذا الرقم مسجل في قائمة ضيوف الحفل الخاصة — يرجى استخدام رابط الدعوة الشخصي المرسل إليك عبر واتساب أو التواصل مع أصحاب الحفل.');

                return;
            }
        }

        $isAttending = $this->attendance_status === 'attending';
        $childrenCount = $isAttending ? max(0, (int) $this->children_count) : 0;

        // Duplicate handling: linked guest response first, then normalized phone match on same invitation
        $rsvpRecord = null;
        if ($linkedGuest && $linkedGuest->rsvp_response_id) {
            $rsvpRecord = RsvpResponse::query()
                ->where('invitation_id', $invitation->id)
                ->find($linkedGuest->rsvp_response_id);
        }

        if (! $rsvpRecord && ! empty($normalizedPhone)) {
            $rsvpRecord = RsvpResponse::where('invitation_id', $invitation->id)
                ->where('phone', $normalizedPhone)
                ->first();
        }

        if (! $linkedGuest && $rsvpRecord && $rsvpRecord->invitation_guest_id) {
            $boundGuest = InvitationGuest::query()
                ->where('invitation_id', $invitation->id)
                ->find($rsvpRecord->invitation_guest_id);

            if ($boundGuest && ! $boundGuest->is_self_registered) {
                $this->addError('phone', 'تعذر تعديل هذا الحجز عبر النموذج العام — يرجى استخدام رابط الدعوة الشخصي أو التواصل مع أصحاب الحفل.');

                return;
            }
        }

        if ($rsvpRecord) {
            $rsvpRecord->update([
                'invitation_guest_id' => $linkedGuest?->id ?? $rsvpRecord->invitation_guest_id,
                'guest_name' => $this->guest_name,
                'phone' => $normalizedPhone ?: $rsvpRecord->phone,
                'email' => $this->email ?: $rsvpRecord->email,
                'attendance_status' => $this->attendance_status,
                'guest_count' => $isAttending ? $this->guest_count : 0,
                'children_count' => $childrenCount,
                'message' => $this->message ?: $rsvpRecord->message,
                'dietary_notes' => $combinedDietary ?: $rsvpRecord->dietary_notes,
                'ip_address' => request()->ip(),
            ]);
        } else {
            $rsvpRecord = RsvpResponse::create([
                'invitation_id' => $invitation->id,
                'invitation_guest_id' => $linkedGuest?->id,
                'guest_name' => $this->guest_name,
                'phone' => $normalizedPhone,
                'email' => $this->email ?: null,
                'attendance_status' => $this->attendance_status,
                'guest_count' => $isAttending ? $this->guest_count : 0,
                'children_count' => $childrenCount,
                'message' => $this->message ?: null,
                'dietary_notes' => $combinedDietary,
                'ip_address' => request()->ip(),
            ]);
        }

        // Sync pre-invited guest or auto-issue QR pass when mode supports QR passes
        $guestPass = null;
        $updatedExistingOpenPass = false;
        if ($linkedGuest) {
            $linkedGuest->update([
                'name' => $this->guest_name,
                'phone' => $normalizedPhone ?: $linkedGuest->phone,
                'rsvp_status' => $this->attendance_status,
                'confirmed_adults' => $isAttending ? $this->guest_count : 0,
                'confirmed_children' => $childrenCount,
                'rsvp_response_id' => $rsvpRecord->id,
            ]);
            $guestPass = $linkedGuest->fresh();
        } else {
            $existingGuestPass = $rsvpRecord->invitation_guest_id
                ? InvitationGuest::where('invitation_id', $invitation->id)->find($rsvpRecord->invitation_guest_id)
                : null;

            if (! $existingGuestPass && $normalizedPhone) {
                $existingGuestPass = InvitationGuest::query()
                    ->where('invitation_id', $invitation->id)
                    ->where('phone', $normalizedPhone)
                    ->where('is_self_registered', true)
                    ->first();
            }

            if ($existingGuestPass) {
                $existingGuestPass->update([
                    'name' => $this->guest_name,
                    'phone' => $normalizedPhone ?: $existingGuestPass->phone,
                    'rsvp_status' => $this->attendance_status,
                    'confirmed_adults' => $isAttending ? $this->guest_count : 0,
                    'confirmed_children' => $childrenCount,
                    'rsvp_response_id' => $rsvpRecord->id,
                ]);
                $rsvpRecord->update(['invitation_guest_id' => $existingGuestPass->id]);
                $guestPass = $existingGuestPass->fresh();
                $updatedExistingOpenPass = true;
            } elseif ($invitation->supportsQrPass() && $isAttending) {
                $guestPass = InvitationGuest::create([
                    'invitation_id' => $invitation->id,
                    'name' => $this->guest_name,
                    'phone' => $normalizedPhone,
                    'category' => InvitationGuest::CATEGORY_GENERAL,
                    'max_adults' => $this->guest_count,
                    'max_children' => $childrenCount,
                    'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
                    'confirmed_adults' => $this->guest_count,
                    'confirmed_children' => $childrenCount,
                    'rsvp_response_id' => $rsvpRecord->id,
                    'is_self_registered' => true,
                ]);
                $rsvpRecord->update(['invitation_guest_id' => $guestPass->id]);
            }
        }

        if ($updatedExistingOpenPass) {
            // Never expose existing bearer QR credentials via unauthenticated phone lookup
            $this->issuedPassUrl = null;
            $this->issuedPassCode = null;
            $this->passPreviouslyIssued = $invitation->supportsQrPass() && $isAttending;
        } elseif ($guestPass && $invitation->supportsQrPass() && $isAttending) {
            $this->issuedPassUrl = $guestPass->getPassUrl();
            $this->issuedPassCode = $guestPass->pass_code;
            $this->passPreviouslyIssued = false;
        } else {
            $this->issuedPassUrl = null;
            $this->issuedPassCode = null;
            $this->passPreviouslyIssued = false;
        }

        // Dispatch notifications gracefully (errors in mail delivery must not fail submission)
        try {
            if ($invitation->user) {
                $invitation->user->notify(new NewRsvpNotification($invitation, $rsvpRecord));
            }

            if (! empty($rsvpRecord->email)) {
                Notification::route('mail', $rsvpRecord->email)
                    ->notify(new RsvpConfirmationNotification($invitation, $rsvpRecord));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch RSVP notifications: '.$e->getMessage());
        }

        $this->isSubmitted = true;
    }

    public function resetForm()
    {
        $this->reset(['guest_name', 'phone', 'email', 'message', 'dietary_notes', 'selected_dietary', 'isSubmitted']);
        $this->guest_count = 1;
        $this->children_count = 0;
        $this->passPreviouslyIssued = false;
        if (! $this->guestId) {
            $this->issuedPassUrl = null;
            $this->issuedPassCode = null;
        }
        $invitation = Invitation::find($this->invitationId);
        $this->phone_country = ($invitation && $invitation->timezone === 'Africa/Cairo') ? 'EG' : 'SA';
        $this->attendance_status = 'attending';

        if ($this->guestId) {
            $guest = InvitationGuest::where('invitation_id', $this->invitationId)->find($this->guestId);
            if ($guest) {
                $this->guest_name = $guest->name;
                $this->guest_count = min($this->maxGuests, max(1, $guest->confirmed_adults > 0 ? $guest->confirmed_adults : 1));
                $this->children_count = min($this->maxChildren, max(0, $guest->confirmed_children));
            }
        }
    }

    public function render()
    {
        return view('livewire.rsvp-form');
    }
}
