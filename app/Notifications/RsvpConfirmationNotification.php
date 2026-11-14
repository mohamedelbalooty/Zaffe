<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RsvpConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Invitation $invitation,
        public readonly RsvpResponse $rsvp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invitationUrl = $this->invitation->public_url;
        $coupleNames = "{$this->invitation->groom_name} و {$this->invitation->bride_name}";

        $mail = (new MailMessage)
            ->subject("تأكيد تسجيلك لحفل زفاف {$coupleNames} — زفّة")
            ->greeting("أهلاً بك {$this->rsvp->guest_name}!")
            ->line("نشكرك على تأكيد حضورك ومشاركتنا فرحة حفل زفاف **{$coupleNames}**.")
            ->line("**موعد الحفل:** " . $this->invitation->wedding_date->format('Y-m-d') . " الساعة " . substr((string) $this->invitation->wedding_time, 0, 5))
            ->line("**مكان الحفل:** {$this->invitation->venue_name}")
            ->line("**عدد المقاعد المحجوزة باسمك:** {$this->rsvp->guest_count}");

        if (!empty($this->invitation->venue_map_url)) {
            $mail->line("[اضغط هنا للوصول إلى موقع القاعة عبر خرائط Google]({$this->invitation->venue_map_url})");
        }

        return $mail
            ->action('فتح بطاقة الدعوة الإلكترونية', $invitationUrl)
            ->salutation('نتشرف بحضوركم ونتطلع للقياكم، عائلة العروسين');
    }
}

