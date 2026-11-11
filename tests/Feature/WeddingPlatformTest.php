<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\TemplateCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeddingPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_page_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('زفّــة');
        $response->assertSee('اصنع دعوتك الخاصة');
    }

    public function test_templates_gallery_is_accessible()
    {
        $response = $this->get('/templates');
        $response->assertStatus(200);
        $response->assertSee('معرض قوالب وتصاميم الزفاف');
        $response->assertSee('الفخامة الذهبية');
    }

    public function test_template_preview_page_is_accessible()
    {
        $response = $this->get('/templates/royal-gold/preview');
        $response->assertStatus(200);
        $response->assertSee('معاينة');
    }

    public function test_pricing_page_is_accessible()
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
        $response->assertSee('باقات الأسعار');
    }

    public function test_public_invitation_page_renders_cleanly()
    {
        $response = $this->get('/invitation/ahmed-and-sarah');
        $response->assertStatus(200);
        $response->assertSee('أحمد محمود العطار');
        $response->assertSee('سارة حسن الشربيني');
        $response->assertSee('فورسيزونز');
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'عمر ومريم',
            'email' => 'omar@example.com',
            'phone' => '+201011122233',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'omar@example.com']);
        $response->assertRedirect(route('dashboard.invitations.create'));
    }

    public function test_user_can_login()
    {
        $response = $this->post('/login', [
            'email' => 'couple@zaffe.app',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('لوحة التحكم');
    }

    public function test_user_can_create_new_invitation()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $template = InvitationTemplate::first();

        $response = $this->actingAs($user)->post('/dashboard/invitations', [
            'template_id' => $template->id,
            'groom_name' => 'زياد مصطفى',
            'bride_name' => 'يارا كريم',
            'wedding_date' => now()->addDays(60)->format('Y-m-d'),
            'venue_name' => 'فندق الماسة كابيتال',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('invitations', [
            'user_id' => $user->id,
            'groom_name' => 'زياد مصطفى',
            'bride_name' => 'يارا كريم',
        ]);

        $invitation = Invitation::where('groom_name', 'زياد مصطفى')->first();
        $response->assertRedirect(route('dashboard.invitations.editor', $invitation->id));
    }

    public function test_livewire_rsvp_submission_creates_record()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'محمد الشناوي')
            ->set('phone', '+201099887766')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->set('message', 'ألف مبروك بالرفاه والبنين يا غاليين!')
            ->call('submit')
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'محمد الشناوي',
            'guest_count' => 2,
        ]);
    }

    public function test_guest_list_and_csv_export()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        // Guest page
        $response = $this->actingAs($user)->get("/dashboard/invitations/{$invitation->id}/guests");
        $response->assertStatus(200);
        $response->assertSee('سجل ضيوف');

        // CSV export
        $csvResponse = $this->actingAs($user)->get("/dashboard/invitations/{$invitation->id}/guests/export");
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
