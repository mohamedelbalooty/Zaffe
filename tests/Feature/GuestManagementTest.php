<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private InvitationTemplate $template;
    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();

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

        $this->invitation = Invitation::create([
            'user_id' => $this->owner->id,
            'template_id' => $this->template->id,
            'title' => 'فرح أحمد وسارة',
            'slug' => 'ahmed-sara-guests',
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الفورسيزونز',
            'status' => 'published',
        ]);
    }

    public function test_owner_can_view_guest_list(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'م. حسام الدين',
            'phone' => '+201012345678',
            'attendance_status' => 'attending',
            'guest_count' => 2,
            'dietary_notes' => 'وجبة نباتية',
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests', $this->invitation->id));

        $response->assertOk();
        $response->assertSee('م. حسام الدين');
        $response->assertSee('وجبة نباتية');
    }

    public function test_non_owner_cannot_view_guest_list(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('dashboard.invitations.guests', $this->invitation->id));

        $response->assertNotFound();
    }

    public function test_owner_can_filter_guests_by_status(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'حاضر مؤكد',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'معتذر لطيف',
            'attendance_status' => 'declined',
            'guest_count' => 0,
        ]);

        $responseAttending = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests', [$this->invitation->id, 'status' => 'attending']));

        $responseAttending->assertOk();
        $responseAttending->assertSee('حاضر مؤكد');
        $responseAttending->assertDontSee('معتذر لطيف');

        $responseDeclined = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests', [$this->invitation->id, 'status' => 'declined']));

        $responseDeclined->assertOk();
        $responseDeclined->assertSee('معتذر لطيف');
        $responseDeclined->assertDontSee('حاضر مؤكد');
    }

    public function test_owner_can_search_guests_by_name(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'خالد عبد الرحمن',
            'attendance_status' => 'attending',
            'guest_count' => 1,
        ]);

        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'محمود سمير',
            'attendance_status' => 'attending',
            'guest_count' => 1,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests', [$this->invitation->id, 'search' => 'خالد']));

        $response->assertOk();
        $response->assertSee('خالد عبد الرحمن');
        $response->assertDontSee('محمود سمير');
    }

    public function test_csv_export_includes_headers_and_utf8_bom(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => 'م. حسام الدين',
            'phone' => '+201012345678',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests.export', $this->invitation->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Must start with UTF-8 BOM
        $expectedBom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $this->assertStringStartsWith($expectedBom, $content);
        $this->assertStringContainsString('اسم الضيف', $content);
        $this->assertStringContainsString('م. حسام الدين', $content);
    }

    public function test_csv_export_sanitizes_formula_injection(): void
    {
        RsvpResponse::create([
            'invitation_id' => $this->invitation->id,
            'guest_name' => '=cmd|"/C calc"!A0',
            'phone' => '+201012345678',
            'email' => '@evil.com',
            'message' => '+1+2',
            'dietary_notes' => '-SUM(A1:A10)',
            'attendance_status' => 'attending',
            'guest_count' => 1,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('dashboard.invitations.guests.export', $this->invitation->id));

        $content = $response->streamedContent();

        // Must prepend single quote to all formula injection triggers
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'+1+2", $content);
        $this->assertStringContainsString("'-SUM(A1:A10)", $content);
        $this->assertStringContainsString("'@evil.com", $content);
    }

    public function test_non_owner_cannot_export_csv(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('dashboard.invitations.guests.export', $this->invitation->id));

        $response->assertNotFound();
    }
}
