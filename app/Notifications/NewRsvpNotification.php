<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRsvpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Invitation $invitation,
        public readonly RsvpResponse $rsvp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'attending' => 'مؤكد الحضور (بكل سرور 🎉)',
            'declined' => 'اعتذر عن الحضور',
            'tentative' => 'ربما يحضر (متردد)',
        ];

        $statusText = $statusLabels[$this->rsvp->attendance_status] ?? $this->rsvp->attendance_status;
        $guestUrl = url(route('dashboard.invitations.guests', $this->invitation->id));

        $mail = (new MailMessage)
            ->subject("تأكيد حضور جديد: {$this->rsvp->guest_name} — {$this->invitation->title}")
            ->greeting("مرحباً {$notifiable->name}!")
            ->line("قام الضيف الكريم **{$this->rsvp->guest_name}** بالرد على دعوة زفافكم ({$this->invitation->title}).")
            ->line("**حالة الحضور:** {$statusText}")
            ->line("**عدد الحضور والمرافقين:** {$this->rsvp->guest_count}")
            ->line("**رقم الهاتف:** {$this->rsvp->phone}");

        if (!empty($this->rsvp->message)) {
            $mail->line("**تهنئة الضيف:** \"{$this->rsvp->message}\"");
        }

        if (!empty($this->rsvp->dietary_notes)) {
            $mail->line("**ملاحظات خاصة:** {$this->rsvp->dietary_notes}");
        }

        return $mail
            ->action('عرض وإدارة سجل الضيوف', $guestUrl)
            ->salutation('دامت دياركم عامرة بالأفراح والمسرات، فريق زفّــة');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'invitation_title' => $this->invitation->title,
            'rsvp_id' => $this->rsvp->id,
            'guest_name' => $this->rsvp->guest_name,
            'phone' => $this->rsvp->phone,
            'attendance_status' => $this->rsvp->attendance_status,
            'guest_count' => $this->rsvp->guest_count,
        ];
    }
}

