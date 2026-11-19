<?php

namespace Tests\Feature;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Filament\Resources\InvitationTemplates\InvitationTemplateResource;
use App\Filament\Resources\PlatformSettings\PlatformSettingResource;
use App\Filament\Resources\RsvpResponses\RsvpResponseResource;
use App\Filament\Resources\SubscriptionPlans\SubscriptionPlanResource;
use App\Filament\Resources\TemplateCategories\TemplateCategoryResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $couple;
    private User $otherCouple;
    private InvitationTemplate $template;
    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'superadmin-f09@zaffe.app',
            'role' => 'admin',
        ]);

        $this->couple = User::factory()->create([
            'email' => 'couple-f09@zaffe.app',
            'role' => 'user',
        ]);

        $this->otherCouple = User::factory()->create([
            'email' => 'other-f09@zaffe.app',
            'role' => 'user',
        ]);

        $this->template = InvitationTemplate::create([
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold-f09',
            'view_name' => 'templates.royal_gold',
            'category' => 'classic',
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'فرح كريم ومريم',
            'slug' => 'karim-mariam-f09',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الماريوت',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_access_active_filament_resources(): void
    {
        $routes = [
            '/admin',
            '/admin/users/create',
            '/admin/invitations/create',
            '/admin/invitation-templates/create',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $response->assertOk();
        }

        $this->actingAs($this->admin);
        $this->assertTrue(UserResource::canAccess());
        $this->assertTrue(InvitationResource::canAccess());
        $this->assertTrue(InvitationTemplateResource::canAccess());
        $this->assertTrue(RsvpResponseResource::canAccess());

        // Filament table index views rely on Number::format() (requires PHP ext-intl).
        if (extension_loaded('intl')) {
            $this->actingAs($this->admin)->get('/admin/users')->assertOk();
        }
    }

    public function test_regular_user_cannot_access_admin_panel_or_resources(): void
    {
        $this->actingAs($this->couple)->get('/admin')->assertForbidden();
        $this->actingAs($this->couple)->get('/admin/users')->assertForbidden();
    }

    public function test_subscription_plan_resource_is_disabled_in_release_1(): void
    {
        $this->assertFalse(SubscriptionPlanResource::canAccess());
        $this->actingAs($this->admin)->get('/admin/subscription-plans')->assertForbidden();
    }

    public function test_invitation_policy_enforces_ownership_and_admin_access(): void
    {
        $this->assertTrue(Gate::forUser($this->couple)->allows('view', $this->invitation));
        $this->assertTrue(Gate::forUser($this->couple)->allows('update', $this->invitation));
        $this->assertTrue(Gate::forUser($this->couple)->allows('delete', $this->invitation));
        $this->assertFalse(Gate::forUser($this->couple)->allows('deleteAny', Invitation::class));

        $this->assertFalse(Gate::forUser($this->otherCouple)->allows('view', $this->invitation));
        $this->assertFalse(Gate::forUser($this->otherCouple)->allows('update', $this->invitation));
        $this->assertFalse(Gate::forUser($this->otherCouple)->allows('delete', $this->invitation));

        $this->assertTrue(Gate::forUser($this->admin)->allows('view', $this->invitation));
        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $this->invitation));
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $this->invitation));
        $this->assertTrue(Gate::forUser($this->admin)->allows('deleteAny', Invitation::class));
    }

    public function test_template_and_user_policies_restrict_crud_to_admins(): void
    {
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', InvitationTemplate::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('create', InvitationTemplate::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('update', $this->template));
        $this->assertFalse(Gate::forUser($this->couple)->allows('delete', $this->template));

        $this->assertTrue(Gate::forUser($this->admin)->allows('viewAny', InvitationTemplate::class));
        $this->assertTrue(Gate::forUser($this->admin)->allows('create', InvitationTemplate::class));
        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $this->template));
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $this->template));

        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', User::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('update', $this->otherCouple));
        $this->assertFalse(Gate::forUser($this->couple)->allows('delete', $this->otherCouple));

        $this->assertTrue(Gate::forUser($this->admin)->allows('viewAny', User::class));
        $this->assertTrue(Gate::forUser($this->admin)->allows('update', $this->couple));
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $this->couple));
    }

    public function test_unauthenticated_user_is_redirected_to_filament_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/users')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_access_all_required_filament_resources_and_regular_user_is_forbidden(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(TemplateCategoryResource::canAccess());
        $this->assertTrue(PlatformSettingResource::canAccess());

        $resourceIndexRoutes = [
            '/admin/users',
            '/admin/invitations',
            '/admin/invitation-templates',
            '/admin/template-categories',
            '/admin/rsvp-responses',
            '/admin/platform-settings',
        ];

        foreach ($resourceIndexRoutes as $url) {
            $this->actingAs($this->couple)->get($url)->assertForbidden();

            if (extension_loaded('intl')) {
                $this->actingAs($this->admin)->get($url)->assertOk();
            }
        }
    }

    public function test_logged_in_customer_receives_403_on_admin_until_logging_out_for_admin_session(): void
    {
        $this->post('/login', [
            'email' => $this->couple->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($this->couple);

        // An active regular-user web session is authenticated, so Filament aborts with 403 rather than redirecting to /admin/login.
        $this->get('/admin')->assertForbidden();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs($this->admin)->get('/admin')->assertOk();
    }

    public function test_production_seeding_does_not_create_demo_accounts_or_reset_admin_password(): void
    {
        $prodAdmin = User::factory()->create([
            'email' => 'ops-admin@zaffe.app',
            'role' => 'admin',
            'password' => Hash::make('ZaffeProdAdm!2026'),
        ]);

        $originalEnv = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $this->artisan('db:seed', [
                '--class' => DatabaseSeeder::class,
                '--force' => true,
            ])->assertExitCode(0);
        } finally {
            $this->app['env'] = $originalEnv;
        }

        $this->assertDatabaseMissing('users', ['email' => 'admin@zaffe.app']);
        $this->assertDatabaseMissing('users', ['email' => 'couple@zaffe.app']);
        $this->assertDatabaseMissing('invitations', ['slug' => 'ahmed-and-sarah']);

        $prodAdmin->refresh();
        $this->assertSame('admin', $prodAdmin->role);
        $this->assertTrue(Hash::check('ZaffeProdAdm!2026', $prodAdmin->password));
        $this->assertFalse(Hash::check('password123', $prodAdmin->password));

        $this->assertSame(6, TemplateCategory::count());
        $this->assertSame(15, InvitationTemplate::count()); // 1 from setUp + 14 seeded catalog templates
        $this->assertDatabaseHas('invitation_templates', ['slug' => 'royal-gold']);

        $seededSlugs = [
            'royal-gold',
            'floral-romance',
            'minimal-chic',
            'islamic-elegance',
            'emerald-night',
            'classic-aristocrat',
            'diriyah-heritage',
            'rawashin-elegance',
            'sadu-signature',
            'taif-rose',
            'palm-oasis',
            'red-sea-pearl',
            'desert-dusk',
            'modern-monogram',
        ];

        foreach ($seededSlugs as $slug) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $this->assertFalse($template->is_premium, "Template [{$slug}] must be free (is_premium = false) in Release 1.");
        }
    }

    public function test_release_1_free_templates_migration_clears_legacy_premium_flags_without_data_loss(): void
    {
        $this->seed(DatabaseSeeder::class);

        InvitationTemplate::whereIn('slug', ['islamic-elegance', 'emerald-night'])
            ->update(['is_premium' => true]);

        $migration = require database_path('migrations/2026_11_15_000001_set_release_1_templates_free.php');
        $migration->up();

        $this->assertSame(0, InvitationTemplate::where('is_premium', true)->count());
        $this->assertDatabaseHas('invitations', ['slug' => 'karim-mariam-f09']);
        $this->assertDatabaseHas('invitations', ['slug' => 'ahmed-and-sarah']);

        $migration->down();
        $this->assertTrue(InvitationTemplate::where('slug', 'islamic-elegance')->firstOrFail()->is_premium);
        $this->assertTrue(InvitationTemplate::where('slug', 'emerald-night')->firstOrFail()->is_premium);

        $migration->up();
        $this->assertSame(0, InvitationTemplate::where('is_premium', true)->count());
    }

    public function test_login_page_hides_demo_credentials_in_production(): void
    {
        $this->get('/login')->assertOk()->assertSee('couple@zaffe.app');

        $originalEnv = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $response = $this->get('/login');
            $response->assertOk();
            $response->assertDontSee('couple@zaffe.app');
            $response->assertDontSee('password123');
        } finally {
            $this->app['env'] = $originalEnv;
        }
    }

    public function test_create_admin_command_enforces_strong_password_and_preserves_existing_accounts(): void
    {
        $this->artisan('zaffe:create-admin')
            ->expectsQuestion('Admin full name', 'Weak Admin')
            ->expectsQuestion('Admin email address', 'weak-admin@zaffe.app')
            ->expectsQuestion('Admin password (min 12 characters, mixed case, number, symbol)', 'password123')
            ->expectsQuestion('Confirm admin password', 'password123')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'weak-admin@zaffe.app']);

        $existingPasswordHash = $this->couple->password;
        $this->artisan('zaffe:create-admin')
            ->expectsQuestion('Admin full name', 'Overwrite Attempt')
            ->expectsQuestion('Admin email address', $this->couple->email)
            ->assertExitCode(1);

        $this->couple->refresh();
        $this->assertSame('user', $this->couple->role);
        $this->assertSame($existingPasswordHash, $this->couple->password);

        $this->artisan('zaffe:create-admin')
            ->expectsQuestion('Admin full name', 'Production Super Admin')
            ->expectsQuestion('Admin email address', 'prod-super@zaffe.app')
            ->expectsQuestion('Admin password (min 12 characters, mixed case, number, symbol)', 'Zaffe$ecureProd2026!')
            ->expectsQuestion('Confirm admin password', 'Zaffe$ecureProd2026!')
            ->assertExitCode(0);

        $created = User::where('email', 'prod-super@zaffe.app')->first();
        $this->assertNotNull($created);
        $this->assertSame('admin', $created->role);
        $this->assertTrue($created->isAdmin());
        $this->assertTrue($created->canAccessPanel(Filament::getPanel('admin')));
        $this->assertTrue(Hash::check('Zaffe$ecureProd2026!', $created->password));
    }
}
