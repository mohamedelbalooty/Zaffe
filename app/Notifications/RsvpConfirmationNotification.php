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
        $invitationUrl = url(route('invitation.show', $this->invitation->slug));

        if (app()->getLocale() === 'en') {
            $coupleNamesEn = "{$this->invitation->groom_name} & {$this->invitation->bride_name}";

            $mail = (new MailMessage)
                ->subject("RSVP Confirmation for {$coupleNamesEn}'s Wedding — Zaffe")
                ->greeting("Hello {$this->rsvp->guest_name}!")
                ->line("Thank you for confirming your response for the wedding of **{$coupleNamesEn}**.")
                ->line('**Wedding Date:** '.$this->invitation->wedding_date->format('Y-m-d').' at '.substr((string) $this->invitation->wedding_time, 0, 5))
                ->line("**Venue:** {$this->invitation->venue_name}")
                ->line("**Reserved Seats:** {$this->rsvp->guest_count}");

            if (! empty($this->invitation->venue_map_url)) {
                $mail->line("[Open Venue Location on Google Maps]({$this->invitation->venue_map_url})");
            }

            return $mail
                ->action('Open Digital Invitation', $invitationUrl)
                ->salutation('With warmest regards, The Couple & Family');
        }

        $coupleNames = "{$this->invitation->groom_name} و {$this->invitation->bride_name}";

        $mail = (new MailMessage)
            ->subject("تأكيد تسجيلك لحفل زفاف {$coupleNames} — زفّة")
            ->greeting("أهلاً بك {$this->rsvp->guest_name}!")
            ->line("نشكرك على تأكيد حضورك ومشاركتنا فرحة حفل زفاف **{$coupleNames}**.")
            ->line('**موعد الحفل:** '.$this->invitation->wedding_date->format('Y-m-d').' الساعة '.substr((string) $this->invitation->wedding_time, 0, 5))
            ->line("**مكان الحفل:** {$this->invitation->venue_name}")
            ->line("**عدد المقاعد المحجوزة باسمك:** {$this->rsvp->guest_count}");

        if (! empty($this->invitation->venue_map_url)) {
            $mail->line("[اضغط هنا للوصول إلى موقع القاعة عبر خرائط Google]({$this->invitation->venue_map_url})");
        }

        return $mail
            ->action('فتح بطاقة الدعوة الإلكترونية', $invitationUrl)
            ->salutation('نتشرف بحضوركم ونتطلع للقياكم، عائلة العروسين');
    }
}
