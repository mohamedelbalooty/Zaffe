<?php

namespace Tests\Feature;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Filament\Resources\InvitationTemplates\InvitationTemplateResource;
use App\Filament\Resources\RsvpResponses\RsvpResponseResource;
use App\Filament\Resources\SubscriptionPlans\SubscriptionPlanResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
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
}
