<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\PhoneService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_register_with_egyptian_phone_and_it_normalizes_to_e164(): void
    {
        $response = $this->post('/register', [
            'name' => 'محمد ومريم',
            'email' => 'egyptian.couple@zaffe.app',
            'phone_country' => 'EG',
            'phone' => '01012345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard.invitations.create'));

        $user = User::where('email', 'egyptian.couple@zaffe.app')->first();
        $this->assertNotNull($user);
        $this->assertEquals('+201012345678', $user->phone);
    }

    public function test_user_can_register_with_saudi_phone_and_it_normalizes_to_e164(): void
    {
        $response = $this->post('/register', [
            'name' => 'سعود ونورة',
            'email' => 'saudi.couple@zaffe.app',
            'phone_country' => 'SA',
            'phone' => '0512345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard.invitations.create'));

        $user = User::where('email', 'saudi.couple@zaffe.app')->first();
        $this->assertNotNull($user);
        $this->assertEquals('+966512345678', $user->phone);
    }

    public function test_user_registration_fails_with_invalid_phone(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'فارس ورزان',
            'email' => 'invalid.phone@zaffe.app',
            'phone_country' => 'EG',
            'phone' => '12345', // Invalid Egyptian mobile
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_password_reset_link_dispatches_notification_with_valid_token(): void
    {
        Notification::fake();

        $user = User::where('email', 'couple@zaffe.app')->first();

        $response = $this->post('/forgot-password', [
            'email' => 'couple@zaffe.app',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $this->assertNotEmpty($notification->token);
            return true;
        });
    }

    public function test_password_reset_form_renders_cleanly(): void
    {
        $response = $this->get('/reset-password/sample-token?email=couple@zaffe.app');

        $response->assertStatus(200);
        $response->assertSee('تعيين كلمة المرور الجديدة');
        $response->assertSee('sample-token');
        $response->assertSee('couple@zaffe.app');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'couple@zaffe.app',
            'password' => 'new-secret-pass-123',
            'password_confirmation' => 'new-secret-pass-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Verify user can now log in with the new password
        $user->refresh();
        $this->assertTrue(Hash::check('new-secret-pass-123', $user->password));

        $loginResponse = $this->post('/login', [
            'email' => 'couple@zaffe.app',
            'password' => 'new-secret-pass-123',
        ]);

        $this->assertAuthenticatedAs($user);
        $loginResponse->assertRedirect(route('dashboard'));
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $oldPasswordHash = $user->password;

        $response = $this->post('/reset-password', [
            'token' => 'completely-bogus-token-123',
            'email' => 'couple@zaffe.app',
            'password' => 'new-secret-pass-123',
            'password_confirmation' => 'new-secret-pass-123',
        ]);

        $response->assertSessionHasErrors(['email']);

        $user->refresh();
        $this->assertEquals($oldPasswordHash, $user->password);
    }

    public function test_password_reset_token_cannot_be_reused(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $token = Password::broker()->createToken($user);

        // First reset (succeeds)
        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'couple@zaffe.app',
            'password' => 'first-new-password',
            'password_confirmation' => 'first-new-password',
        ])->assertRedirect(route('login'));

        // Second reset with exact same token (must fail)
        $secondResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'couple@zaffe.app',
            'password' => 'second-new-password',
            'password_confirmation' => 'second-new-password',
        ]);

        $secondResponse->assertSessionHasErrors(['email']);
    }

    public function test_phone_service_normalization_and_validation(): void
    {
        // Egypt standard and Arabic-Indic numerals
        $this->assertEquals('+201012345678', PhoneService::normalize('01012345678', 'EG'));
        $this->assertEquals('+201012345678', PhoneService::normalize('٠١٠١٢٣٤٥٦٧٨', 'EG'));
        $this->assertEquals('+201198765432', PhoneService::normalize('+20 11 9876 5432', 'EG'));
        $this->assertTrue(PhoneService::isValid('01012345678', 'EG'));
        $this->assertTrue(PhoneService::isValid('01198765432', 'EG'));
        $this->assertTrue(PhoneService::isValid('01234567890', 'EG'));
        $this->assertTrue(PhoneService::isValid('01512345678', 'EG'));
        $this->assertFalse(PhoneService::isValid('01312345678', 'EG')); // 013 is not mobile

        // Saudi Arabia standard and Arabic-Indic numerals
        $this->assertEquals('+966501234567', PhoneService::normalize('0501234567', 'SA'));
        $this->assertEquals('+966501234567', PhoneService::normalize('٠٥٠١٢٣٤٥٦٧', 'SA'));
        $this->assertEquals('+966598765432', PhoneService::normalize('+966 59 876 5432', 'SA'));
        $this->assertTrue(PhoneService::isValid('0501234567', 'SA'));
        $this->assertTrue(PhoneService::isValid('0598765432', 'SA'));
        $this->assertFalse(PhoneService::isValid('0101234567', 'SA')); // Doesn't start with 5
    }
}

