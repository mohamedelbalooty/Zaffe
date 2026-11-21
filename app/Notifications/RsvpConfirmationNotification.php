<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\InvitationGuest;
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
        $isDeclined = $this->rsvp->attendance_status === 'declined';
        $childrenCount = (int) ($this->rsvp->children_count ?? 0);

        $linkedGuest = $this->rsvp->invitation_guest_id
            ? InvitationGuest::query()
                ->with('invitation')
                ->where('invitation_id', $this->invitation->id)
                ->find($this->rsvp->invitation_guest_id)
            : null;
        $passUrl = (! $isDeclined && $linkedGuest && $linkedGuest->canReceiveQrPass())
            ? $linkedGuest->getPassUrl()
            : null;

        if (app()->getLocale() === 'en') {
            $coupleNamesEn = $this->invitation->formattedCoupleNames('en');

            if ($isDeclined) {
                return (new MailMessage)
                    ->subject("Thank you for your response for {$coupleNamesEn}'s Wedding — Zaffe")
                    ->greeting("Hello {$this->rsvp->guest_name},")
                    ->line("Thank you for letting us know your response for the wedding of **{$coupleNamesEn}**.")
                    ->line('We completely understand and appreciate your warm wishes and blessings.')
                    ->action('View Digital Invitation', $invitationUrl)
                    ->salutation('With warmest regards, The Hosts');
            }

            $mail = (new MailMessage)
                ->subject("RSVP Confirmation for {$coupleNamesEn}'s Wedding — Zaffe")
                ->greeting("Hello {$this->rsvp->guest_name}!")
                ->line("Thank you for confirming your attendance for the wedding of **{$coupleNamesEn}**.")
                ->line('**Wedding Date:** '.$this->invitation->wedding_date->format('Y-m-d').' at '.substr((string) $this->invitation->wedding_time, 0, 5))
                ->line("**Venue:** {$this->invitation->venue_name}")
                ->line("**Reserved Adult Seats:** {$this->rsvp->guest_count}");

            if ($childrenCount > 0) {
                $mail->line("**Children:** {$childrenCount}");
            }

            if ($passUrl) {
                $mail->line("[Open Personal Invitation Pass (QR)]({$passUrl})");
            }

            if (! empty($this->invitation->venue_map_url)) {
                $mail->line("[Open Venue Location on Google Maps]({$this->invitation->venue_map_url})");
            }

            return $mail
                ->action('Open Digital Invitation', $invitationUrl)
                ->salutation('With warmest regards, The Hosts');
        }

        $coupleNames = $this->invitation->formattedCoupleNames('ar');

        if ($isDeclined) {
            return (new MailMessage)
                ->subject("استلام اعتذاركم عن حفل زفاف {$coupleNames} — زفّة")
                ->greeting("أهلاً بكم {$this->rsvp->guest_name}،")
                ->line("نشكركم على إبلاغنا بردكم الكريم بخصوص حفل زفاف **{$coupleNames}**.")
                ->line('نقدّر ظروفكم ونعتز بمشاعركم الطيبة ودعواتكم الصادقة بالتوفيق والبركة.')
                ->action('عرض بطاقة الدعوة الإلكترونية', $invitationUrl)
                ->salutation('مع خالص التقدير والود، أصحاب الدعوة');
        }

        $mail = (new MailMessage)
            ->subject("تأكيد تسجيلك لحفل زفاف {$coupleNames} — زفّة")
            ->greeting("أهلاً بك {$this->rsvp->guest_name}!")
            ->line("نشكرك على تأكيد حضورك ومشاركتنا فرحة حفل زفاف **{$coupleNames}**.")
            ->line('**موعد الحفل:** '.$this->invitation->wedding_date->format('Y-m-d').' الساعة '.substr((string) $this->invitation->wedding_time, 0, 5))
            ->line("**مكان الحفل:** {$this->invitation->venue_name}")
            ->line("**عدد المقاعد المحجوزة باسمك:** {$this->rsvp->guest_count}");

        if ($childrenCount > 0) {
            $mail->line("**عدد الأطفال المرافقين:** {$childrenCount}");
        }

        if ($passUrl) {
            $mail->line("[عرض بطاقة الدعوة الشخصية (QR)]({$passUrl})");
        }

        if (! empty($this->invitation->venue_map_url)) {
            $mail->line("[اضغط هنا للوصول إلى موقع القاعة عبر خرائط Google]({$this->invitation->venue_map_url})");
        }

        return $mail
            ->action('فتح بطاقة الدعوة الإلكترونية', $invitationUrl)
            ->salutation('نتشرف بحضوركم ونتطلع للقياكم، أصحاب الدعوة');
    }
}
