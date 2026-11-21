<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_route_includes_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_authenticated_dashboard_route_includes_security_headers(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_health_check_endpoint_returns_ok(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_performance_indexes_migration_applies_and_rolls_back_safely(): void
    {
        $this->assertTrue(Schema::hasIndex('invitations', 'invitations_user_id_status_index'));
        $this->assertTrue(Schema::hasIndex('invitations', 'idx_invitations_user_created'));
        $this->assertTrue(Schema::hasIndex('invitations', 'idx_invitations_status_wedding_date'));

        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'rsvp_responses_invitation_id_attendance_status_index'));
        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_phone'));
        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_created'));

        $migration = require database_path('migrations/2026_11_14_000001_add_performance_indexes.php');

        // Rollback only drops indexes introduced by the performance migration
        $migration->down();

        $this->assertFalse(Schema::hasIndex('invitations', 'idx_invitations_user_created'));
        $this->assertFalse(Schema::hasIndex('invitations', 'idx_invitations_status_wedding_date'));
        $this->assertFalse(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_phone'));
        $this->assertFalse(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_created'));

        $this->assertTrue(Schema::hasIndex('invitations', 'invitations_user_id_status_index'));
        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'rsvp_responses_invitation_id_attendance_status_index'));

        // Re-apply cleanly and verify idempotency on repeated up() execution
        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasIndex('invitations', 'idx_invitations_user_created'));
        $this->assertTrue(Schema::hasIndex('invitations', 'idx_invitations_status_wedding_date'));
        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_phone'));
        $this->assertTrue(Schema::hasIndex('rsvp_responses', 'idx_rsvp_invitation_created'));
    }

    public function test_filament_admin_routes_include_security_headers(): void
    {
        $guestResponse = $this->get('/admin/login');
        $guestResponse->assertOk();
        $guestResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $guestResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $guestResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $guestResponse->assertHeader('X-XSS-Protection', '1; mode=block');

        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertOk();
        $adminResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $adminResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $adminResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $adminResponse->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_mail_and_queue_config_hygiene(): void
    {
        $resendConfig = config('mail.mailers.resend');

        $this->assertIsArray($resendConfig);
        $this->assertArrayNotHasKey('encryption', $resendConfig);
        $this->assertArrayHasKey('scheme', $resendConfig);
        $this->assertSame('smtp.resend.com', $resendConfig['host'] ?? null);
        $this->assertSame(465, (int) ($resendConfig['port'] ?? 0));
        $this->assertTrue((bool) config('queue.connections.database.after_commit'));

        $envKeys = ['RESEND_API_KEY', 'MAIL_PASSWORD'];
        $backupEnv = [];
        foreach ($envKeys as $key) {
            $backupEnv[$key] = [
                'env_exists' => array_key_exists($key, $_ENV),
                'env' => $_ENV[$key] ?? null,
                'server_exists' => array_key_exists($key, $_SERVER),
                'server' => $_SERVER[$key] ?? null,
                'getenv' => getenv($key),
            ];
        }

        $_ENV['RESEND_API_KEY'] = '';
        $_SERVER['RESEND_API_KEY'] = '';
        putenv('RESEND_API_KEY=');
        $_ENV['MAIL_PASSWORD'] = 're_fallback_secret';
        $_SERVER['MAIL_PASSWORD'] = 're_fallback_secret';
        putenv('MAIL_PASSWORD=re_fallback_secret');

        try {
            $loadedMail = require config_path('mail.php');
            $this->assertSame('re_fallback_secret', $loadedMail['mailers']['resend']['password']);
        } finally {
            foreach ($envKeys as $key) {
                if ($backupEnv[$key]['env_exists']) {
                    $_ENV[$key] = $backupEnv[$key]['env'];
                } else {
                    unset($_ENV[$key]);
                }

                if ($backupEnv[$key]['server_exists']) {
                    $_SERVER[$key] = $backupEnv[$key]['server'];
                } else {
                    unset($_SERVER[$key]);
                }

                putenv($backupEnv[$key]['getenv'] === false ? $key : "{$key}={$backupEnv[$key]['getenv']}");
            }
        }

        $envExample = file_get_contents(base_path('.env.example'));
        $this->assertIsString($envExample);
        $this->assertDoesNotMatchRegularExpression('/^RESEND_API_KEY=\s*$/m', $envExample);

        $composer = json_decode((string) file_get_contents(base_path('composer.json')), true);
        $this->assertIsArray($composer);
        $this->assertSame('^8.4.1', $composer['require']['php'] ?? null);
    }
}
