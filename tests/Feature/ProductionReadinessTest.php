<?php

namespace Tests\Feature;

use App\Filament\Pages\OperationsMonitor;
use App\Jobs\PurgeInvitationMediaJob;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use App\Notifications\NewRsvpNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\RsvpConfirmationNotification;
use App\Support\TemplateStyleCatalog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery;
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

    public function test_scheduled_orphaned_media_cleanup_command_is_registered_daily(): void
    {
        $schedule = app(Schedule::class);
        $matchingEvents = collect($schedule->events())
            ->filter(fn ($event): bool => str_contains((string) $event->command, 'zaffe:cleanup-orphaned-media'));

        $this->assertCount(1, $matchingEvents);
        $this->assertSame('0 0 * * *', $matchingEvents->first()->expression);
    }

    public function test_database_queue_processes_notifications_discards_deleted_models_and_retries_failed_media_purge_jobs(): void
    {
        $previousQueue = config('queue.default');
        $previousAfterCommit = config('queue.connections.database.after_commit');
        $previousMail = config('mail.default');

        config([
            'queue.default' => 'database',
            'queue.connections.database.after_commit' => false,
            'mail.default' => 'array',
        ]);

        try {
            $couple = User::factory()->create(['role' => 'user']);
            $template = InvitationTemplate::create([
                'name_ar' => 'ورد الطائف',
                'name_en' => 'Taif Rose',
                'slug' => 'taif-rose-queue-test',
                'view_name' => 'templates.taif_rose',
                'is_active' => true,
            ]);

            $invitation = Invitation::create([
                'user_id' => $couple->id,
                'template_id' => $template->id,
                'title' => 'حفل اختبار الطابور',
                'slug' => 'queue-test-card',
                'groom_name' => 'بندر',
                'bride_name' => null,
                'wedding_date' => now()->addDays(25)->toDateString(),
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قاعة المملكة',
                'status' => 'published',
                'is_rsvp_enabled' => true,
                'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            ]);

            $rsvp = RsvpResponse::create([
                'invitation_id' => $invitation->id,
                'guest_name' => 'سلطان القحطاني',
                'phone' => '+966501112233',
                'attendance_status' => 'attending',
                'guest_count' => 2,
            ]);

            // 1. Queued password reset mail drains cleanly via database worker
            Mail::fake();
            $couple->notify(new ResetPasswordNotification('db-queue-token-123'));
            $this->assertSame(1, DB::table('jobs')->count());

            Artisan::call('queue:work', [
                'connection' => 'database',
                '--stop-when-empty' => true,
            ]);

            $this->assertSame(0, DB::table('jobs')->count());
            $this->assertSame(0, DB::table('failed_jobs')->count());

            // 2. Deleted-model RSVP notifications (mail + database channels & guest confirmation) are silently discarded ($deleteWhenMissingModels = true)
            $couple->notify(new NewRsvpNotification($invitation, $rsvp));
            Notification::route('mail', 'guest.deleted@example.com')->notify(new RsvpConfirmationNotification($invitation, $rsvp));
            $this->assertSame(3, DB::table('jobs')->count());

            $invitation->delete();

            Artisan::call('queue:work', [
                'connection' => 'database',
                '--stop-when-empty' => true,
            ]);

            $this->assertSame(0, DB::table('jobs')->count());
            $this->assertSame(0, DB::table('failed_jobs')->count());

            // 3. PurgeInvitationMediaJob retries with backoff while disk errors, records failure on exhaustion, and succeeds on recovery
            Storage::fake('public');
            Log::spy();
            $realFakeDisk = Storage::disk('public');
            $strandedPath = UploadedFile::fake()->image('queue-retry.png')->store('invitations/covers', 'public');

            $failingDisk = Mockery::mock(Filesystem::class);
            $failingDisk->shouldReceive('url')
                ->with('')
                ->andReturn(rtrim((string) config('filesystems.disks.public.url', 'http://localhost/storage'), '/').'/');
            $failingDisk->shouldReceive('exists')->andReturn(true);
            $failingDisk->shouldReceive('delete')->with($strandedPath)->andThrow(new \RuntimeException('Transient disk offline'));

            Storage::set('public', $failingDisk);

            try {
                PurgeInvitationMediaJob::dispatch([$strandedPath]);
                $this->assertSame(1, DB::table('jobs')->count());

                // Attempt 1 fails and reschedules back into jobs with attempts = 1
                Artisan::call('queue:work', [
                    'connection' => 'database',
                    '--once' => true,
                ]);

                $queuedJob = DB::table('jobs')->first();
                $this->assertNotNull($queuedJob);
                $this->assertSame(1, (int) $queuedJob->attempts);
                $this->assertSame(0, DB::table('failed_jobs')->count());

                // Simulate reaching final allowed attempt (tries = 5) while disk remains offline
                DB::table('jobs')
                    ->where('id', $queuedJob->id)
                    ->update([
                        'attempts' => 4,
                        'available_at' => now()->subSecond()->getTimestamp(),
                    ]);

                Artisan::call('queue:work', [
                    'connection' => 'database',
                    '--once' => true,
                ]);

                $this->assertSame(0, DB::table('jobs')->count());
                $this->assertSame(1, DB::table('failed_jobs')->count());

                $viewData = (new OperationsMonitor)->getViewData();
                $this->assertSame(1, $viewData['delivery']['failed_jobs']);
                $this->assertStringContainsString($strandedPath, $viewData['recentFailedJobs'][0]['error_summary']);
            } finally {
                Storage::set('public', $realFakeDisk);
            }

            // Recovery run removes stranded file cleanly
            $realFakeDisk->assertExists($strandedPath);
            PurgeInvitationMediaJob::dispatch([$strandedPath]);
            Artisan::call('queue:work', [
                'connection' => 'database',
                '--stop-when-empty' => true,
            ]);

            $this->assertSame(0, DB::table('jobs')->count());
            $realFakeDisk->assertMissing($strandedPath);
        } finally {
            config([
                'queue.default' => $previousQueue,
                'queue.connections.database.after_commit' => $previousAfterCommit,
                'mail.default' => $previousMail,
            ]);
        }
    }

    public function test_all_fourteen_templates_render_photo_free_with_zero_empty_img_sources(): void
    {
        $couple = User::factory()->create(['role' => 'user']);
        $views = TemplateStyleCatalog::bladeViews();
        $this->assertCount(14, $views);

        foreach ($views as $idx => $viewName) {
            $tmpl = InvitationTemplate::create([
                'name_ar' => 'قالب صافي '.$idx,
                'name_en' => 'Photo Free '.$idx,
                'slug' => 'photo-free-'.$idx,
                'view_name' => $viewName,
                'default_styles' => TemplateStyleCatalog::DEFAULTS,
                'is_premium' => false,
                'is_active' => true,
            ]);

            $invitation = Invitation::create([
                'user_id' => $couple->id,
                'template_id' => $tmpl->id,
                'title' => 'حفل زفاف فهد',
                'slug' => 'photo-free-card-'.$idx,
                'groom_name' => 'فهد العتيبي',
                'bride_name' => null,
                'wedding_date' => now()->addDays(20)->toDateString(),
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الرياض',
                'cover_photo' => null,
                'status' => 'published',
                'is_rsvp_enabled' => false,
                'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            ]);

            $response = $this->get(route('invitation.show', $invitation->slug));
            $response->assertOk();

            $html = (string) $response->getContent();
            $this->assertStringContainsString('dir="rtl"', $html);
            $this->assertDoesNotMatchRegularExpression('/<img\b[^>]*\bsrc\s*=\s*(?:["\']\s*["\']|["\']?\/?["\']?)/i', $html);
            $this->assertStringNotContainsString('Black Tie', $html);
        }
    }
}
