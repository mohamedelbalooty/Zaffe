<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Notifications\NewRsvpNotification;
use App\Notifications\RsvpConfirmationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RsvpForm extends Component
{
    #[Locked]
    public int $invitationId;
    public string $guest_name = '';
    public string $phone = '';
    public string $email = '';
    public string $attendance_status = 'attending';
    public int $guest_count = 1;
    public string $message = '';
    public string $dietary_notes = '';
    public bool $isSubmitted = false;
    #[Locked]
    public int $maxGuests = 5;

    public function mount(int $invitationId, int $maxGuests = 5)
    {
        $this->invitationId = $invitationId;
        $invitation = Invitation::find($invitationId);
        $this->maxGuests = $invitation ? $invitation->max_guests_per_rsvp : $maxGuests;
    }

    protected function rules(): array
    {
        $invitation = Invitation::find($this->invitationId);
        $max = $invitation ? $invitation->max_guests_per_rsvp : $this->maxGuests;

        return [
            'guest_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'attendance_status' => ['required', 'in:attending,declined,tentative'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:' . $max],
            'message' => ['nullable', 'string', 'max:1000'],
            'dietary_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'guest_name.required' => 'الرجاء إدخال الاسم الكريم.',
        'guest_name.min' => 'الاسم يجب ألا يقل عن 3 أحرف.',
        'guest_count.min' => 'عدد الضيوف لا يقل عن 1.',
        'guest_count.max' => 'أقصى عدد للضيوف مسموح به في هذا الحجز هو :max.',
    ];

    public function submit()
    {
        $invitation = Invitation::findOrFail($this->invitationId);

        // Security & business validation checks
        if (!$invitation->isPublished()) {
            $this->addError('guest_name', 'هذه الدعوة غير متاحة لتأكيد الحضور حالياً.');
            return;
        }

        if (!$invitation->is_rsvp_enabled) {
            $this->addError('guest_name', 'تم إغلاق استقبال تأكيدات الحضور لهذه الدعوة.');
            return;
        }

        if ($invitation->rsvp_deadline && now()->startOfDay()->gt($invitation->rsvp_deadline)) {
            $this->addError('guest_name', 'عذراً، لقد انتهت المهلة المحددة لتأكيد الحضور.');
            return;
        }

        // Rate limiting: max 10 submissions per minute per IP
        $rateKey = 'rsvp:' . request()->ip() . ':' . $invitation->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $this->addError('guest_name', 'يرجى الانتظار قليلاً قبل إرسال تأكيد حضور جديد.');
            return;
        }

        $this->validate();

        RateLimiter::hit($rateKey, 60);

        // Duplicate handling: if phone exists for this invitation, update existing response
        $rsvpRecord = null;
        if (!empty($this->phone)) {
            $existing = RsvpResponse::where('invitation_id', $invitation->id)
                ->where('phone', $this->phone)
                ->first();

            if ($existing) {
                $existing->update([
                    'guest_name' => $this->guest_name,
                    'email' => $this->email ?: $existing->email,
                    'attendance_status' => $this->attendance_status,
                    'guest_count' => $this->attendance_status === 'attending' ? $this->guest_count : 0,
                    'message' => $this->message ?: $existing->message,
                    'dietary_notes' => $this->dietary_notes ?: $existing->dietary_notes,
                    'ip_address' => request()->ip(),
                ]);

                $rsvpRecord = $existing;
            }
        }

        if (!$rsvpRecord) {
            $rsvpRecord = RsvpResponse::create([
                'invitation_id' => $invitation->id,
                'guest_name' => $this->guest_name,
                'phone' => $this->phone ?: null,
                'email' => $this->email ?: null,
                'attendance_status' => $this->attendance_status,
                'guest_count' => $this->attendance_status === 'attending' ? $this->guest_count : 0,
                'message' => $this->message ?: null,
                'dietary_notes' => $this->dietary_notes ?: null,
                'ip_address' => request()->ip(),
            ]);
        }

        // Dispatch notifications gracefully (errors in mail delivery must not fail submission)
        try {
            if ($invitation->user) {
                $invitation->user->notify(new NewRsvpNotification($invitation, $rsvpRecord));
            }

            if (!empty($rsvpRecord->email)) {
                Notification::route('mail', $rsvpRecord->email)
                    ->notify(new RsvpConfirmationNotification($invitation, $rsvpRecord));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch RSVP notifications: ' . $e->getMessage());
        }

        $this->isSubmitted = true;
    }

    public function resetForm()
    {
        $this->reset(['guest_name', 'phone', 'email', 'message', 'dietary_notes', 'isSubmitted']);
        $this->guest_count = 1;
        $this->attendance_status = 'attending';
    }

    public function render()
    {
        return view('livewire.rsvp-form');
    }
}

