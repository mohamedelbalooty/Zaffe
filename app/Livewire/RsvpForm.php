<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use Livewire\Component;

class RsvpForm extends Component
{
    public int $invitationId;
    public string $guest_name = '';
    public string $phone = '';
    public string $email = '';
    public string $attendance_status = 'attending';
    public int $guest_count = 1;
    public string $message = '';
    public string $dietary_notes = '';
    public bool $isSubmitted = false;
    public int $maxGuests = 5;

    public function mount(int $invitationId, int $maxGuests = 5)
    {
        $this->invitationId = $invitationId;
        $this->maxGuests = $maxGuests;
    }

    protected function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'attendance_status' => ['required', 'in:attending,declined,tentative'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:' . $this->maxGuests],
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
        $this->validate();

        $invitation = Invitation::findOrFail($this->invitationId);

        RsvpResponse::create([
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

