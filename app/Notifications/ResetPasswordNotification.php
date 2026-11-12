<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        if (app()->getLocale() === 'en') {
            return (new MailMessage)
                ->subject('Reset Your Password — Zaffe')
                ->greeting('Hello ' . ($notifiable->name ?? 'Valued Couple') . '!')
                ->line('You are receiving this email because we received a password reset request for your Zaffe account.')
                ->action('Reset Password', $url)
                ->line("This password reset link will expire in {$expire} minutes.")
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation('Warm regards, The Zaffe Team');
        }

        return (new MailMessage)
            ->subject('استعادة كلمة المرور — زفّة')
            ->greeting('مرحباً ' . ($notifiable->name ?? 'عروسين زفّة') . '!')
            ->line('تصلكم هذه الرسالة لأننا تلقينا طلباً لاستعادة كلمة المرور الخاصة بحسابكم على منصة زفّة.')
            ->action('إعادة تعيين كلمة المرور', $url)
            ->line("صلاحية هذا الرابط تنتهي تلقائياً خلال {$expire} دقيقة حفاظاً على أمان حسابكم.")
            ->line('إذا لم تطلب استعادة كلمة المرور بنفسك، يمكنك تجاهل هذه الرسالة بأمان.')
            ->salutation('مع أطيب التمنيات بدوام الفرح، فريق زفّــة');
    }
}

