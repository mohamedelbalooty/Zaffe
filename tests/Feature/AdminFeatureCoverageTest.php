<?php

namespace Tests\Feature;

use App\Filament\Pages\OperationsMonitor;
use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\InvitationTemplates\Pages\CreateInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\EditInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\ListInvitationTemplates;
use App\Filament\Resources\PlatformSettings\Pages\EditPlatformSetting;
use App\Filament\Resources\PlatformSettings\Pages\ListPlatformSettings;
use App\Filament\Resources\RsvpResponses\Pages\ListRsvpResponses;
use App\Filament\Resources\RsvpResponses\Pages\ViewRsvpResponse;
use App\Filament\Resources\TemplateCategories\Pages\EditTemplateCategory;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Widgets\PlatformStatsOverview;
use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationTemplate;
use App\Models\PlatformSetting;
use App\Models\RsvpResponse;
use App\Models\SubscriptionPlan;
use App\Models\TemplateCategory;
use App\Models\User;
use App\Support\PlatformSettingCatalog;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFeatureCoverageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $couple;

    private User $otherCouple;

    private TemplateCategory $category;

    private InvitationTemplate $template;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@zaffe.app',
            'role' => 'admin',
        ]);

        $this->couple = User::factory()->create([
            'name' => 'Tariq & Laila',
            'email' => 'couple1@zaffe.app',
            'role' => 'user',
        ]);

        $this->otherCouple = User::factory()->create([
            'name' => 'Omar & Nour',
            'email' => 'couple2@zaffe.app',
            'role' => 'user',
        ]);

        $this->category = TemplateCategory::create([
            'name_ar' => 'فخامة ملكية',
            'name_en' => 'Royal Luxury',
            'slug' => 'luxury',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->template = InvitationTemplate::create([
            'category_id' => $this->category->id,
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'view_name' => 'templates.royal_gold',
            'default_styles' => ['primary_color' => '#C6A76B'],
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف طارق وليلى',
            'slug' => 'tariq-and-laila',
            'groom_name' => 'طارق',
            'bride_name' => 'ليلى',
            'wedding_date' => now()->addMonths(2)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الرياض',
            'custom_styles' => ['primary_color' => '#C6A76B'],
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'max_guests_per_rsvp' => 5,
        ]);
    }

    public function test_policies_enforce_role_and_ownership_boundaries_across_all_models(): void
    {
        $rsvp = RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'خالد',
            'phone' => '+966500000001',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        // Couple cannot manage platform resources
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', TemplateCategory::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', PlatformSetting::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', InvitationTemplate::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', User::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', RsvpResponse::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('create', RsvpResponse::class));

        // Couple can view own invitation RSVP but not another couple's
        $this->assertTrue(Gate::forUser($this->couple)->allows('view', $rsvp));
        $this->assertFalse(Gate::forUser($this->otherCouple)->allows('view', $rsvp));
        $this->assertFalse(Gate::forUser($this->couple)->allows('update', $rsvp));
        $this->assertFalse(Gate::forUser($this->couple)->allows('delete', $rsvp));

        // Admin cannot create or mutate guest RSVP records, only view/delete
        $this->assertTrue(Gate::forUser($this->admin)->allows('viewAny', RsvpResponse::class));
        $this->assertTrue(Gate::forUser($this->admin)->allows('view', $rsvp));
        $this->assertFalse(Gate::forUser($this->admin)->allows('create', RsvpResponse::class));
        $this->assertFalse(Gate::forUser($this->admin)->allows('update', $rsvp));
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $rsvp));

        // Deferred F11 subscription plans blocked for both
        $this->assertFalse(Gate::forUser($this->admin)->allows('viewAny', SubscriptionPlan::class));
        $this->assertFalse(Gate::forUser($this->couple)->allows('viewAny', SubscriptionPlan::class));
    }

    public function test_admin_cannot_delete_self_or_last_remaining_admin(): void
    {
        // Single admin cannot delete self
        $this->assertFalse(Gate::forUser($this->admin)->allows('delete', $this->admin));

        $secondAdmin = User::factory()->create([
            'role' => 'admin',
            'email' => 'second-admin@zaffe.app',
        ]);

        // With 2 admins present, deleting another admin is allowed, self-delete remains blocked
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $secondAdmin));
        $this->assertFalse(Gate::forUser($this->admin)->allows('delete', $this->admin));

        $secondAdmin->delete();

        // Back down to 1 admin: still protected against self deletion
        $this->assertFalse(Gate::forUser($this->admin)->allows('delete', $this->admin));
    }

    public function test_admin_can_create_and_edit_user_without_overwriting_password_or_breaking_role(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        // Edit couple without providing password
        $originalHash = $this->couple->password;

        Livewire::test(EditUser::class, ['record' => $this->couple->id])
            ->fillForm([
                'name' => 'طارق وليلى المعدل',
                'phone' => '+966501234567',
                'role' => 'user',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->couple->refresh();
        $this->assertSame('طارق وليلى المعدل', $this->couple->name);
        $this->assertSame('+966501234567', $this->couple->phone);
        $this->assertSame($originalHash, $this->couple->password);

        // Duplicate email must trigger validation error instead of DB crash
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Duplicate User',
                'email' => $this->couple->email,
                'password' => 'StrongPass123!',
                'role' => 'user',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);

        // Reject invalid role values outside constrained options
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Invalid Role User',
                'email' => 'invalid-role@zaffe.app',
                'password' => 'StrongPass123!',
                'role' => 'super_hacker',
            ])
            ->call('create')
            ->assertHasFormErrors(['role']);

        Livewire::test(ListUsers::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$this->admin, $this->couple]);
    }

    public function test_admin_editing_invitation_preserves_custom_styles_array_moderates_status_and_cleans_media_on_delete(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/test-cover.jpg', 'img');
        Storage::disk('public')->put('gallery/test-photo.jpg', 'img');

        $this->invitation->update(['cover_photo' => 'covers/test-cover.jpg']);
        InvitationPhoto::create([
            'invitation_id' => $this->invitation->id,
            'image_path' => 'gallery/test-photo.jpg',
            'sort_order' => 1,
        ]);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        // Edit via Filament with KeyValue custom_styles + Asia/Riyadh timezone
        Livewire::test(EditInvitation::class, ['record' => $this->invitation->id])
            ->fillForm([
                'timezone' => 'Asia/Riyadh',
                'custom_styles' => ['primary_color' => '#0F766E'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->invitation->refresh();
        $this->assertIsArray($this->invitation->custom_styles);
        $this->assertSame('#0F766E', $this->invitation->custom_styles['primary_color']);

        // Public invitation renders cleanly with admin-updated custom styles
        auth()->logout();
        $this->get(route('invitation.show', $this->invitation->slug))
            ->assertOk()
            ->assertSee('#0F766E');

        // Admin unpublishes invitation via moderation action -> guests see unpublished gate
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(ListInvitations::class)
            ->callTableAction('unpublish', $this->invitation);

        $this->invitation->refresh();
        $this->assertSame('draft', $this->invitation->status);

        auth()->logout();
        $this->get(route('invitation.show', $this->invitation->slug))
            ->assertForbidden()
            ->assertViewIs('pages.unpublished');

        // Couple owner can still preview their own draft
        $this->actingAs($this->couple)
            ->get(route('invitation.show', $this->invitation->slug))
            ->assertOk()
            ->assertViewIs('pages.invitation-show');

        // Admin republishes invitation -> public access restored
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(ListInvitations::class)
            ->callTableAction('publish', $this->invitation);

        auth()->logout();
        $this->get(route('invitation.show', $this->invitation->slug))
            ->assertOk()
            ->assertViewIs('pages.invitation-show');

        // Admin deletes invitation -> DB row and uploaded storage files cleaned up
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(EditInvitation::class, ['record' => $this->invitation->id])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing('invitations', ['id' => $this->invitation->id]);
        Storage::disk('public')->assertMissing('covers/test-cover.jpg');
        Storage::disk('public')->assertMissing('gallery/test-photo.jpg');
    }

    public function test_admin_template_and_category_deactivation_propagates_to_customer_site(): void
    {
        $secondTemplate = InvitationTemplate::create([
            'category_id' => $this->category->id,
            'name_ar' => 'رومانسي ناعم',
            'name_en' => 'Floral Romance',
            'slug' => 'floral-romance',
            'view_name' => 'templates.floral_romance',
            'default_styles' => ['primary_color' => '#C97B84'],
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        // Edit template preserves default_styles array & forces is_premium = false
        Livewire::test(EditInvitationTemplate::class, ['record' => $this->template->id])
            ->fillForm([
                'default_styles' => ['primary_color' => '#B59556'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->template->refresh();
        $this->assertIsArray($this->template->default_styles);
        $this->assertFalse((bool) $this->template->is_premium);
        $this->assertSame('#B59556', $this->template->default_styles['primary_color']);

        // Deactivate template via Filament table action
        Livewire::test(ListInvitationTemplates::class)
            ->callTableAction('toggleActive', $secondTemplate);

        $secondTemplate->refresh();
        $this->assertFalse($secondTemplate->is_active);

        // Customer cannot preview or create invitation with deactivated template
        $this->actingAs($this->couple)
            ->get(route('templates.preview', $secondTemplate->slug))
            ->assertNotFound();

        $this->actingAs($this->couple)
            ->post(route('dashboard.invitations.store'), [
                'template_id' => $secondTemplate->id,
                'title' => 'Attempt Disabled Template',
                'groom_name' => 'أحمد',
                'bride_name' => 'سارة',
                'wedding_date' => now()->addMonths(3)->toDateString(),
                'timezone' => 'Africa/Cairo',
                'venue_name' => 'القاعة الملكية',
            ])
            ->assertSessionHasErrors('template_id');

        // Re-activate template, then deactivate its entire TemplateCategory
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(ListInvitationTemplates::class)
            ->callTableAction('toggleActive', $secondTemplate);

        Livewire::test(EditTemplateCategory::class, ['record' => $this->category->id])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->category->refresh();
        $this->assertFalse($this->category->is_active);

        // Customer site hides templates under inactive category
        $this->actingAs($this->couple)
            ->get(route('templates.index'))
            ->assertOk()
            ->assertDontSee($this->template->name_ar);

        $this->actingAs($this->couple)
            ->get(route('templates.preview', $this->template->slug))
            ->assertNotFound();

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->call('changeTemplate', $secondTemplate->id)
            ->assertNotFound();

        $this->assertSame($this->template->id, $this->invitation->fresh()->template_id);
    }

    public function test_admin_rsvp_monitoring_is_read_only_supports_formula_safe_csv_export_and_spam_delete(): void
    {
        $rsvp = RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => '=CMD("calc")',
            'phone' => '+966500000010',
            'email' => 'guest@example.com',
            'attendance_status' => 'attending',
            'guest_count' => 3,
            'message' => '+1+2',
            'dietary_notes' => '@SUM(1,2)',
        ]);

        // Couple forbidden on admin RSVPs
        $this->actingAs($this->couple)
            ->get('/admin/rsvp-responses')
            ->assertForbidden();

        // Admin views index & view page cleanly; create & edit routes removed
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        $this->get('/admin/rsvp-responses')->assertOk();
        $this->get("/admin/rsvp-responses/{$rsvp->id}")->assertOk();
        $this->get('/admin/rsvp-responses/create')->assertNotFound();
        $this->get("/admin/rsvp-responses/{$rsvp->id}/edit")->assertNotFound();

        Livewire::test(ViewRsvpResponse::class, ['record' => $rsvp->id])
            ->assertOk()
            ->assertSee($this->invitation->title);

        // Formula-safe CSV export via Filament Header Action
        $csvResponse = Livewire::test(ListRsvpResponses::class)
            ->callAction('exportCsv');

        $csvResponse->assertFileDownloaded('admin-rsvps-'.now()->format('Y-m-d').'.csv');

        $decodedCsv = base64_decode((string) ($csvResponse->effects['download']['content'] ?? ''), true);
        $this->assertNotFalse($decodedCsv);
        $this->assertStringStartsWith(chr(0xEF).chr(0xBB).chr(0xBF), $decodedCsv);
        $this->assertStringContainsString("'=CMD", $decodedCsv);
        $this->assertStringContainsString("'+1+2", $decodedCsv);
        $this->assertStringContainsString("'@SUM(1,2)", $decodedCsv);

        // Delete spam RSVP via moderation action -> removed from couple's guest view
        Livewire::test(ListRsvpResponses::class)
            ->callTableAction('delete', $rsvp);

        $this->assertDatabaseMissing('rsvp_responses', ['id' => $rsvp->id]);
    }

    public function test_admin_platform_settings_update_reflects_live_on_customer_pages_and_table(): void
    {
        $settingEmail = PlatformSetting::create([
            'key' => 'contact_email',
            'value' => 'support@zaffe.app',
            'group' => 'contact',
        ]);

        $settingPhone = PlatformSetting::create([
            'key' => 'contact_phone',
            'value' => '+20 100 000 0000',
            'group' => 'contact',
        ]);

        $settingWa = PlatformSetting::create([
            'key' => 'whatsapp_support',
            'value' => '201000000000',
            'group' => 'contact',
        ]);

        $settingName = PlatformSetting::create([
            'key' => 'site_name_ar',
            'value' => 'زفّة',
            'group' => 'general',
        ]);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        // Table renders actual setting values
        Livewire::test(ListPlatformSettings::class)
            ->assertOk()
            ->assertSee('support@zaffe.app')
            ->assertSee('+20 100 000 0000');

        // Update values via Filament Admin
        Livewire::test(EditPlatformSetting::class, ['record' => $settingEmail->id])
            ->fillForm(['value' => 'care@zaffe.sa'])
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(EditPlatformSetting::class, ['record' => $settingPhone->id])
            ->fillForm(['value' => '+966 55 123 4567'])
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(EditPlatformSetting::class, ['record' => $settingWa->id])
            ->fillForm(['value' => '+966551234567'])
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(EditPlatformSetting::class, ['record' => $settingName->id])
            ->fillForm(['value' => 'زفة السعودية'])
            ->call('save')
            ->assertHasNoFormErrors();

        // Customer marketing & contact views reflect Admin updates immediately
        auth()->logout();
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('care@zaffe.sa')
            ->assertSee('+966 55 123 4567')
            ->assertSee('https://wa.me/966551234567')
            ->assertSee('زفة السعودية');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('care@zaffe.sa')
            ->assertSee('https://wa.me/966551234567')
            ->assertSee('زفة السعودية');
    }

    public function test_admin_platform_settings_workspace_hides_raw_keys_validates_and_saves_grouped_settings(): void
    {
        foreach (PlatformSettingCatalog::definitions() as $key => $definition) {
            PlatformSetting::create([
                'key' => $key,
                'value' => $definition['default'],
                'group' => 'general',
            ]);
        }

        app()->setLocale('ar');
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        $component = Livewire::test(ListPlatformSettings::class)
            ->assertOk()
            ->assertSeeText('اسم المنصة بالعربية')
            ->assertSeeText('البريد الإلكتروني للدعم الفني')
            ->assertSeeText('رقم واتساب خدمة العملاء')
            ->assertDontSeeText('site_name_ar')
            ->assertDontSeeText('site_name_en')
            ->assertDontSeeText('contact_email')
            ->assertDontSeeText('contact_phone')
            ->assertDontSeeText('whatsapp_support');

        // Rejects invalid email & invalid WhatsApp number
        $component
            ->set('settingsData.contact_email', 'not-an-email')
            ->set('settingsData.whatsapp_support', 'invalid-phone-letters')
            ->call('saveSettings')
            ->assertHasErrors([
                'settingsData.contact_email',
                'settingsData.whatsapp_support',
            ]);

        // Saves valid grouped settings & updates live customer views
        $component
            ->set('settingsData.site_name_ar', 'زفّة الملكية')
            ->set('settingsData.site_name_en', 'Zaffe Royal')
            ->set('settingsData.contact_email', 'concierge@zaffe.sa')
            ->set('settingsData.contact_phone', '+966 50 999 8888')
            ->set('settingsData.whatsapp_support', '+966509998888')
            ->call('saveSettings')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('platform_settings', [
            'key' => 'site_name_ar',
            'value' => 'زفّة الملكية',
            'group' => 'general',
        ]);
        $this->assertDatabaseHas('platform_settings', [
            'key' => 'contact_email',
            'value' => 'concierge@zaffe.sa',
            'group' => 'contact',
        ]);
        $this->assertDatabaseHas('platform_settings', [
            'key' => 'whatsapp_support',
            'value' => '+966509998888',
            'group' => 'contact',
        ]);

        // Edit page renders friendly localized heading without raw English key
        $emailSetting = PlatformSetting::where('key', 'contact_email')->firstOrFail();
        Livewire::test(EditPlatformSetting::class, ['record' => $emailSetting->id])
            ->assertOk()
            ->assertSeeText('البريد الإلكتروني للدعم الفني')
            ->assertDontSeeText('contact_email');

        auth()->logout();
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('concierge@zaffe.sa')
            ->assertSee('+966 50 999 8888')
            ->assertSee('https://wa.me/966509998888');
    }

    public function test_admin_operations_monitor_and_dashboard_stats_work_without_leaking_secrets(): void
    {
        config(['services.resend.key' => 're_super_secret_test_token_999']);

        DB::table('failed_jobs')->insert([
            'uuid' => 'test-uuid-001',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{"secret":"must_not_leak_payload"}',
            'exception' => "RuntimeException: Resend SMTP connection timeout\n#0 /var/www/app.php",
            'failed_at' => now(),
        ]);

        $this->actingAs($this->couple)
            ->get('/admin/operations-monitor')
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get('/admin/operations-monitor')
            ->assertOk()
            ->assertSee('RuntimeException: Resend SMTP connection timeout')
            ->assertDontSee('re_super_secret_test_token_999')
            ->assertDontSee('must_not_leak_payload');

        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(OperationsMonitor::class)->assertOk();
        Livewire::test(PlatformStatsOverview::class)->assertOk();
    }

    public function test_admin_panel_renders_arabic_localization_and_respects_locale_switch(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'أحمد علي',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        // Default Arabic panel rendering across core resources and monitor
        $this->actingAs($this->admin)
            ->get('/admin/invitation-templates')
            ->assertOk()
            ->assertSee('زفّــة | لوحة الإدارة')
            ->assertSee('قوالب الدعوات')
            ->assertSee('إضافة قالب دعوة')
            ->assertSee('دعوات الزفاف')
            ->assertSee('المستخدمون')
            ->assertSee('تأكيدات الحضور')
            ->assertSee('فئات القوالب')
            ->assertSee('إعدادات المنصة')
            ->assertSee('مراقبة العمليات والإشعارات');

        $this->actingAs($this->admin)
            ->get('/admin/invitation-templates/create')
            ->assertOk()
            ->assertDontSee('filament-forms::components.select.required_description');

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('حساب عرسان')
            ->assertSee('مدير المنصة');

        $this->actingAs($this->admin)
            ->get('/admin/invitations')
            ->assertOk()
            ->assertSee('منشورة');

        $this->actingAs($this->admin)
            ->get('/admin/rsvp-responses')
            ->assertOk()
            ->assertSee('حضور مؤكد');
    }

    public function test_admin_panel_renders_english_localization_when_locale_is_en(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->get('/admin/invitation-templates')
            ->assertOk()
            ->assertSee('Zaffe | Super Admin')
            ->assertSee('Invitation Templates')
            ->assertSee('Platform Settings');
    }

    public function test_admin_template_default_styles_editor_validation_presets_and_live_preview(): void
    {
        // Preserve an extra legacy custom key on edit while saving structured design tokens
        $this->template->update([
            'default_styles' => [
                'primary_color' => '#C6A76B',
                'custom_ribbon' => 'gold-foil',
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/admin/invitation-templates/{$this->template->id}/edit")
            ->assertOk()
            ->assertSee('بيانات القالب والهوية')
            ->assertSee('التنسيقات الافتراضية والهوية البصرية')
            ->assertSee('معاينة حية للتنسيقات الافتراضية')
            ->assertSee('تباين النص مع البطاقة')
            ->assertSee('معاينة القالب الحية')
            ->assertSee('إحصائيات واستخدام القالب');

        Filament::setCurrentPanel('admin');

        // Reject invalid hex color in structured default_styles
        Livewire::test(EditInvitationTemplate::class, ['record' => $this->template->id])
            ->fillForm([
                'default_styles' => [
                    'primary_color' => 'invalid-color',
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['default_styles.primary_color']);

        // Save normalized hex colors & typography choices and verify live preview + customer catalog reflection
        Livewire::test(EditInvitationTemplate::class, ['record' => $this->template->id])
            ->fillForm([
                'default_styles' => [
                    'primary_color' => '#0f4c3a',
                    'secondary_color' => '#1e293b',
                    'background_color' => '#f4f7f5',
                    'card_color' => '#ffffff',
                    'accent_color' => '#c6a76b',
                    'font_family' => 'Amiri',
                    'border_style' => 'arabesque-lattice',
                    'ornament_style' => 'islamic_star',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->template->refresh();
        $this->assertSame('#0F4C3A', $this->template->default_styles['primary_color']);
        $this->assertSame('Amiri', $this->template->default_styles['font_family']);
        $this->assertSame('arabesque-lattice', $this->template->default_styles['border_style']);
        $this->assertSame('islamic_star', $this->template->default_styles['ornament_style']);
        $this->assertSame('gold-foil', $this->template->default_styles['custom_ribbon']);

        // Verify updated primary color reflects live on customer templates catalog
        $this->get('/templates')
            ->assertOk()
            ->assertSee('#0F4C3A');

        // Verify Create template initializes sensible default styles and persists sanitized payload
        Livewire::test(CreateInvitationTemplate::class)
            ->fillForm([
                'category_id' => $this->category->id,
                'name_ar' => 'زمردي جديد',
                'name_en' => 'New Emerald',
                'slug' => 'new-emerald',
                'view_name' => 'templates.islamic_elegance',
                'sort_order' => 9,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = InvitationTemplate::where('slug', 'new-emerald')->firstOrFail();
        $this->assertSame('#C6A76B', $created->default_styles['primary_color']);
        $this->assertSame('Tajawal', $created->default_styles['font_family']);
        $this->assertFalse((bool) $created->is_premium);

        // Applying a quick preset action updates all 8 default style tokens cleanly
        Livewire::test(EditInvitationTemplate::class, ['record' => $this->template->id])
            ->callAction(TestAction::make('apply_preset_emerald_night')->schemaComponent('default_styles_section'))
            ->assertSchemaStateSet([
                'default_styles.primary_color' => '#E6C687',
                'default_styles.background_color' => '#0F172A',
                'default_styles.font_family' => 'Cairo',
                'default_styles.ornament_style' => 'constellation',
            ]);
    }
}
