<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\PlatformSetting;
use App\Models\RsvpResponse;
use App\Models\TemplateCategory;
use App\Models\User;
use App\Notifications\NewRsvpNotification;
use App\Notifications\RsvpConfirmationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class ProductQaValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $couple;

    private User $admin;

    private InvitationTemplate $template;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'QA Super Admin',
            'email' => 'qa-admin@zaffe.app',
            'role' => 'admin',
        ]);

        $this->couple = User::factory()->create([
            'name' => 'فيصل والعنود',
            'email' => 'qa-couple@zaffe.app',
            'phone' => '+966501234567',
            'role' => 'user',
        ]);

        $category = TemplateCategory::create([
            'name_ar' => 'ملكي فاخر',
            'name_en' => 'Luxury Royal',
            'slug' => 'luxury-qa',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->template = InvitationTemplate::create([
            'category_id' => $category->id,
            'name_ar' => 'الفخامة الذهبية',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold-qa',
            'view_name' => 'templates.royal_gold',
            'default_styles' => [
                'primary_color' => '#C6A76B',
                'secondary_color' => '#292521',
                'background_color' => '#FAF7F2',
                'font_family' => 'Tajawal',
            ],
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف فيصل والعنود',
            'slug' => 'faisal-and-alanoud-qa',
            'groom_name' => 'فيصل',
            'bride_name' => 'العنود',
            'wedding_date' => now()->addMonths(2)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة بالرياض',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'max_guests_per_rsvp' => 5,
            'custom_styles' => $this->template->default_styles,
        ]);
    }

    public function test_invitation_editor_validates_inputs_and_blocks_unsafe_urls_and_status_tampering(): void
    {
        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('groom_name', '')
            ->call('saveChanges')
            ->assertHasErrors(['groom_name']);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('timezone', 'Mars/Olympus')
            ->call('saveChanges')
            ->assertHasErrors(['timezone']);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('venue_map_url', 'javascript:alert(1)')
            ->call('saveChanges')
            ->assertHasErrors(['venue_map_url']);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('max_guests_per_rsvp', 99)
            ->call('saveChanges')
            ->assertHasErrors(['max_guests_per_rsvp']);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('status', 'archived');
    }

    public function test_rsvp_form_renders_email_field_and_allows_zero_guests_when_declining(): void
    {
        Notification::fake();

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->assertSeeHtml('id="rsvp_email"')
            ->set('guest_name', 'خالد العتيبي')
            ->set('phone_country', 'SA')
            ->set('phone', '0551234567')
            ->set('email', 'khalid@example.com')
            ->set('attendance_status', 'declined')
            ->set('guest_count', 0)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'خالد العتيبي',
            'phone' => '+966551234567',
            'email' => 'khalid@example.com',
            'attendance_status' => 'declined',
            'guest_count' => 0,
        ]);
    }

    public function test_marketing_layout_exposes_language_switcher_for_arabic_and_english(): void
    {
        $arResponse = $this->get(route('home'));
        $arResponse->assertOk();
        $arResponse->assertSee('dir="rtl"', false);
        $arResponse->assertSee(route('lang.switch', 'en'));

        $this->get(route('lang.switch', 'en'))->assertRedirect();

        $enResponse = $this->get(route('home'));
        $enResponse->assertOk();
        $enResponse->assertSee('dir="ltr"', false);
        $enResponse->assertSee(route('lang.switch', 'ar'));
    }

    public function test_profile_page_exposes_country_selector_and_rejects_invalid_phone(): void
    {
        $profilePage = $this->actingAs($this->couple)->get(route('dashboard.profile'));
        $profilePage->assertOk();
        $profilePage->assertSee('name="phone_country"', false);
        $profilePage->assertSee('value="SA" selected', false);

        $invalidResponse = $this->actingAs($this->couple)->put(route('dashboard.profile.update'), [
            'name' => 'فيصل والعنود',
            'email' => 'qa-couple@zaffe.app',
            'phone_country' => 'SA',
            'phone' => '123',
        ]);

        $invalidResponse->assertSessionHasErrors(['phone']);
        $this->assertSame('+966501234567', $this->couple->fresh()->phone);
    }

    public function test_resend_mailer_config_is_not_overwritten_and_rsvp_notifications_support_english_and_arabic(): void
    {
        $this->assertSame('smtp', config('mail.mailers.resend.transport'));
        $this->assertSame('resend', config('mail.mailers.resend_api.transport'));

        $rsvp = RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'Tariq Al-Harbi',
            'phone' => '+966509998888',
            'email' => 'tariq@example.com',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        app()->setLocale('en');
        $coupleMailEn = (new NewRsvpNotification($this->invitation, $rsvp))->toMail($this->couple);
        $guestMailEn = (new RsvpConfirmationNotification($this->invitation, $rsvp))->toMail($rsvp);

        $this->assertStringContainsString('New RSVP:', $coupleMailEn->subject);
        $this->assertStringContainsString('RSVP Confirmation', $guestMailEn->subject);

        app()->setLocale('ar');
        $coupleMailAr = (new NewRsvpNotification($this->invitation, $rsvp))->toMail($this->couple);
        $guestMailAr = (new RsvpConfirmationNotification($this->invitation, $rsvp))->toMail($rsvp);

        $this->assertStringContainsString('تأكيد حضور جديد:', $coupleMailAr->subject);
        $this->assertStringContainsString('تأكيد تسجيلك لحفل زفاف', $guestMailAr->subject);
    }

    public function test_admin_operations_monitor_and_platform_settings_preserve_secrets_and_reflect_live(): void
    {
        config(['services.resend.key' => 're_live_secret_qa_token_999']);

        PlatformSetting::set('contact_email', 'concierge@zaffe.app', 'contact');
        PlatformSetting::set('contact_phone', '+966 50 111 2222', 'contact');

        $contactResponse = $this->get(route('contact'));
        $contactResponse->assertOk();
        $contactResponse->assertSee('concierge@zaffe.app');
        $contactResponse->assertSee('+966 50 111 2222');

        $monitorResponse = $this->actingAs($this->admin)->get('/admin/operations-monitor');
        $monitorResponse->assertOk();
        $monitorResponse->assertDontSee('re_live_secret_qa_token_999');
    }
}
