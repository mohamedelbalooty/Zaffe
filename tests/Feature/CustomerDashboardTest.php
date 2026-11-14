<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $couple;
    private InvitationTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->couple = User::factory()->create([
            'name' => 'أحمد وسارة',
            'email' => 'couple-f08@zaffe.app',
        ]);

        $this->template = InvitationTemplate::create([
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'view_name' => 'templates.royal_gold',
            'category' => 'classic',
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_couple_sees_accurate_dashboard_kpis_including_total_views(): void
    {
        $inv1 = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'دعوة الفرح الأولى',
            'slug' => 'first-wedding-f08',
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الفورسيزونز',
            'status' => 'published',
            'view_count' => 120,
        ]);

        $inv2 = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'دعوة الحنة',
            'slug' => 'henna-night-f08',
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(19)->toDateString(),
            'venue_name' => 'نادي الجزيرة',
            'status' => 'draft',
            'view_count' => 35,
        ]);

        RsvpResponse::create([
            'invitation_id' => $inv1->id,
            'guest_name' => 'كريم عادل',
            'attendance_status' => 'attending',
            'guest_count' => 4,
        ]);

        RsvpResponse::create([
            'invitation_id' => $inv1->id,
            'guest_name' => 'طارق حسن',
            'attendance_status' => 'declined',
            'guest_count' => 0,
        ]);

        $response = $this->actingAs($this->couple)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalInvitations', 2);
        $response->assertViewHas('publishedInvitations', 1);
        $response->assertViewHas('confirmedAttendees', 4);
        $response->assertViewHas('declinedCount', 1);
        $response->assertViewHas('totalViews', 155);
        $response->assertSee('155 مشاهدة');
    }

    public function test_dashboard_enforces_strict_tenant_isolation(): void
    {
        $otherCouple = User::factory()->create();

        $otherInv = Invitation::create([
            'user_id' => $otherCouple->id,
            'template_id' => $this->template->id,
            'title' => 'دعوة مستخدم آخر',
            'slug' => 'other-couple-wedding',
            'groom_name' => 'رامي',
            'bride_name' => 'ليلى',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'venue_name' => 'قاعة المؤتمرات',
            'status' => 'published',
            'view_count' => 500,
        ]);

        RsvpResponse::create([
            'invitation_id' => $otherInv->id,
            'guest_name' => 'ضيف غريب',
            'attendance_status' => 'attending',
            'guest_count' => 10,
        ]);

        $response = $this->actingAs($this->couple)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalInvitations', 0);
        $response->assertViewHas('totalViews', 0);
        $response->assertViewHas('confirmedAttendees', 0);
        $response->assertDontSee('رامي');
        $response->assertDontSee('ليلى');
    }

    public function test_profile_update_normalizes_egypt_and_saudi_phone_numbers(): void
    {
        // Update with Egyptian number
        $responseEg = $this->actingAs($this->couple)->put(route('dashboard.profile.update'), [
            'name' => 'أحمد وسارة المحدث',
            'email' => 'couple-f08@zaffe.app',
            'phone_country' => 'EG',
            'phone' => '01012345678',
        ]);

        $responseEg->assertSessionHas('success');
        $this->couple->refresh();
        $this->assertSame('أحمد وسارة المحدث', $this->couple->name);
        $this->assertSame('+201012345678', $this->couple->phone);

        // Update with Saudi number
        $responseSa = $this->actingAs($this->couple)->put(route('dashboard.profile.update'), [
            'name' => 'أحمد وسارة المحدث',
            'email' => 'couple-f08@zaffe.app',
            'phone_country' => 'SA',
            'phone' => '0501234567',
        ]);

        $responseSa->assertSessionHas('success');
        $this->couple->refresh();
        $this->assertSame('+966501234567', $this->couple->phone);
    }

    public function test_pricing_page_communicates_free_release_1_offer_without_checkout(): void
    {
        // Guest sees link to register
        $guestResponse = $this->get(route('pricing'));
        $guestResponse->assertOk();
        $guestResponse->assertSee('باقات الأسعار');
        $guestResponse->assertSee('هدية الإطلاق المجاني ١٠٠٪ في مصر والسعودية');
        $guestResponse->assertSee('ابدأ تصميم دعوتك مجاناً الآن');
        $guestResponse->assertSee(route('register'));
        $guestResponse->assertDontSee('اشترك وصمم دعوتك الملكية');

        // Authenticated user sees link directly to invitation creation
        $authResponse = $this->actingAs($this->couple)->get(route('pricing'));
        $authResponse->assertOk();
        $authResponse->assertSee(route('dashboard.invitations.create'));
    }
}
