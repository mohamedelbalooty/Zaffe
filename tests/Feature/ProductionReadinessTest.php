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
}
