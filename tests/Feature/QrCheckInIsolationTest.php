<?php

namespace Tests\Feature;

use App\Livewire\GateScanner;
use App\Livewire\GuestRosterManager;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationGate;
use App\Models\InvitationGuest;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Policies\InvitationGatePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class QrCheckInIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $couple;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->couple = User::factory()->create(['role' => 'user']);

        $template = InvitationTemplate::create([
            'name_ar' => 'الذهبي الملكي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'description_ar' => 'وصف',
            'description_en' => 'Description',
            'view_name' => 'templates.royal_gold',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف معزول عن الماسح',
            'slug' => 'isolated-qr-'.uniqid(),
            'groom_name' => 'عمر',
            'bride_name' => 'ليلى',
            'wedding_date' => now()->addDays(20),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة النخيل',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_QR,
            'max_guests_per_rsvp' => 5,
        ]);
    }

    public function test_gatekeeper_routes_and_checkin_classes_are_not_active_in_dev(): void
    {
        $this->assertFalse(Route::has('gate.portal'));
        $this->assertFalse(class_exists(GateScanner::class));
        $this->assertFalse(class_exists(InvitationGate::class));
        $this->assertFalse(class_exists(InvitationGatePolicy::class));

        $this->get('/gate/sample-random-gate-token')->assertNotFound();
    }

    public function test_database_schema_remains_intact_without_destructive_migrations(): void
    {
        $this->assertTrue(Schema::hasTable('invitation_guests'));
        $this->assertTrue(Schema::hasTable('invitation_gates'));
        $this->assertTrue(Schema::hasColumns('invitation_guests', [
            'invite_token',
            'qr_token',
            'pass_code',
            'checked_in_at',
            'checked_in_count',
            'checked_in_by_gate_id',
        ]));
    }

    public function test_guest_roster_manager_excludes_gate_and_checkin_ui_while_preserving_roster(): void
    {
        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'الأستاذ فيصل',
            'phone' => '+966501112233',
            'category' => InvitationGuest::CATEGORY_VIP,
            'max_adults' => 2,
            'max_children' => 1,
        ]);

        Livewire::actingAs($this->couple)
            ->test(GuestRosterManager::class, ['invitationId' => $this->invitation->id])
            ->assertSee('الأستاذ فيصل')
            ->assertSee($guest->pass_code)
            ->assertDontSee('بوابات الاستقبال وفحص QR')
            ->assertDontSee('نسخ رابط الماسح')
            ->assertDontSee('دخل القاعة')
            ->assertDontSee('تسجيل دخول');

        $this->assertFalse(method_exists(GuestRosterManager::class, 'createGate'));
        $this->assertFalse(method_exists(GuestRosterManager::class, 'toggleGate'));
        $this->assertFalse(method_exists(GuestRosterManager::class, 'deleteGate'));
        $this->assertFalse(method_exists(GuestRosterManager::class, 'toggleCheckIn'));
    }

    public function test_qr_pass_generation_works_without_promising_gate_scanning(): void
    {
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'ريم القحطاني')
            ->set('phone_country', 'SA')
            ->set('phone', '0551112233')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true)
            ->assertDontSee('صالح لمرة واحدة عند البوابة');

        $guest = InvitationGuest::where('invitation_id', $this->invitation->id)->firstOrFail();

        $this->get($guest->getPassUrl())
            ->assertOk()
            ->assertSee('<svg xmlns="http://www.w3.org/2000/svg"', false)
            ->assertSee($guest->pass_code)
            ->assertDontSee('لمستقبل الضيوف عند بوابة القاعة')
            ->assertDontSee('للدخول مرة واحدة فقط');
    }
}
