<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Models\User;
use App\Notifications\NewRsvpNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\RsvpConfirmationNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_reset_password_notification_implements_should_queue(): void
    {
        $notification = new ResetPasswordNotification('test-token');
        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    public function test_reset_password_notification_renders_localized_arabic_mail(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        app()->setLocale('ar');

        $notification = new ResetPasswordNotification('sample-token-123');
        $mail = $notification->toMail($user);

        $this->assertEquals('استعادة كلمة المرور — زفّة', $mail->subject);
        $this->assertStringContainsString('sample-token-123', $mail->actionUrl);
        $this->assertStringContainsString('couple%40zaffe.app', $mail->actionUrl);
    }

    public function test_reset_password_notification_renders_localized_english_mail(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        app()->setLocale('en');

        $notification = new ResetPasswordNotification('sample-token-456');
        $mail = $notification->toMail($user);

        $this->assertEquals('Reset Your Password — Zaffe', $mail->subject);
        $this->assertStringContainsString('sample-token-456', $mail->actionUrl);
    }

    public function test_submitting_rsvp_dispatches_notification_to_couple(): void
    {
        Notification::fake();

        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();
        $couple = $invitation->user;

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'أحمد زويل')
            ->set('phone', '+201012345678')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->set('message', 'مبارك الزفاف السعيد!')
            ->call('submit')
            ->assertSet('isSubmitted', true);

        Notification::assertSentTo(
            $couple,
            NewRsvpNotification::class,
            function (NewRsvpNotification $notification) use ($invitation) {
                $this->assertEquals($invitation->id, $notification->invitation->id);
                $this->assertEquals('أحمد زويل', $notification->rsvp->guest_name);
                $this->assertEquals(2, $notification->rsvp->guest_count);
                $this->assertEquals('attending', $notification->rsvp->attendance_status);
                return true;
            }
        );
    }

    public function test_submitting_rsvp_with_email_sends_confirmation_to_guest(): void
    {
        Notification::fake();

        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'هدى شعراوي')
            ->set('phone', '+201098765432')
            ->set('email', 'hoda@example.com')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 1)
            ->call('submit')
            ->assertSet('isSubmitted', true);

        Notification::assertSentOnDemand(
            RsvpConfirmationNotification::class,
            function (RsvpConfirmationNotification $notification, $channels, $notifiable) use ($invitation) {
                $this->assertContains('mail', $channels);
                $this->assertEquals('hoda@example.com', $notifiable->routes['mail']);
                $this->assertEquals('هدى شعراوي', $notification->rsvp->guest_name);
                return true;
            }
        );
    }

    public function test_submitting_rsvp_without_email_does_not_send_guest_confirmation(): void
    {
        Notification::fake();

        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'ضيف بلا بريد')
            ->set('phone', '+201055554444')
            ->set('email', '') // No email
            ->set('attendance_status', 'attending')
            ->set('guest_count', 1)
            ->call('submit')
            ->assertSet('isSubmitted', true);

        Notification::assertSentOnDemandTimes(RsvpConfirmationNotification::class, 0);
    }

    public function test_database_notification_channel_stores_record_for_couple(): void
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();
        $couple = $invitation->user;

        $rsvp = RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'guest_name' => 'سميرة موسى',
            'phone' => '+201011223344',
            'attendance_status' => 'attending',
            'guest_count' => 3,
        ]);

        $notification = new NewRsvpNotification($invitation, $rsvp);
        $couple->notify($notification);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $couple->id,
            'notifiable_type' => User::class,
            'type' => NewRsvpNotification::class,
        ]);

        $dbNotification = $couple->notifications()->latest()->first();
        $this->assertEquals('سميرة موسى', $dbNotification->data['guest_name']);
        $this->assertEquals(3, $dbNotification->data['guest_count']);
    }
}

