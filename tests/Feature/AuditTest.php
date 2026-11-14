<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_non_admin_cannot_access_filament_admin_panel()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $response = $this->actingAs($user)->get('/admin');
        
        // Should be 403 forbidden
        $this->assertTrue(in_array($response->status(), [403]));
    }

    public function test_admin_can_access_filament_admin_panel()
    {
        $admin = User::where('email', 'admin@zaffe.app')->first();
        $response = $this->actingAs($admin)->get('/admin');
        
        $this->assertEquals(200, $response->status());
    }

    public function test_unauthorized_user_cannot_mount_invitation_editor_for_other_user()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        $attacker = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($attacker)->get("/dashboard/invitations/{$invitation->id}/editor");
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_view_draft_invitation()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => InvitationTemplate::first()->id,
            'title' => 'حفل خاص جداً',
            'slug' => 'secret-wedding',
            'groom_name' => 'طارق',
            'bride_name' => 'ندى',
            'wedding_date' => now()->addDays(20),
            'venue_name' => 'فندق ماريوت',
            'status' => 'draft',
        ]);

        $response = $this->get('/invitation/secret-wedding');
        $response->assertStatus(403);
    }

    public function test_owner_can_view_their_own_draft_invitation()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => InvitationTemplate::first()->id,
            'title' => 'حفل خاص جداً',
            'slug' => 'secret-wedding-owner',
            'groom_name' => 'طارق',
            'bride_name' => 'ندى',
            'wedding_date' => now()->addDays(20),
            'venue_name' => 'فندق ماريوت',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($owner)->get('/invitation/secret-wedding-owner');
        $response->assertStatus(200);
        $response->assertSee('هذه الدعوة ما زالت في وضع (مسودة خاصة)');
    }

    public function test_editor_updates_and_persists_wedding_details()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('groom_name', 'كريم عبد الله')
            ->set('bride_name', 'مريم النجار')
            ->set('wedding_date', '2026-12-25')
            ->set('wedding_time', '21:00')
            ->set('venue_name', 'قاعة الماسة الملكية')
            ->set('venue_map_url', 'https://maps.google.com/?q=Almasa')
            ->set('welcome_message', 'أهلاً بكم في ليلتنا الجميلة')
            ->set('invitation_message', 'نتشرف بدعوتكم لحضور زفافنا')
            ->set('primary_color', '#8B263E')
            ->set('font_family', 'Cairo')
            ->call('saveChanges');

        $invitation->refresh();

        $this->assertEquals('كريم عبد الله', $invitation->groom_name);
        $this->assertEquals('مريم النجار', $invitation->bride_name);
        $this->assertEquals('2026-12-25', $invitation->wedding_date->format('Y-m-d'));
        $this->assertEquals('21:00', substr($invitation->wedding_time, 0, 5));
        $this->assertEquals('قاعة الماسة الملكية', $invitation->venue_name);
        $this->assertEquals('https://maps.google.com/?q=Almasa', $invitation->venue_map_url);
        $this->assertEquals('#8B263E', $invitation->effective_styles['primary_color']);
        $this->assertEquals('Cairo', $invitation->effective_styles['font_family']);
    }

    public function test_editor_template_switching_preserves_custom_details()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();
        $newTemplate = InvitationTemplate::where('slug', 'floral-romance')->first();

        Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('groom_name', 'حسام الدين')
            ->set('bride_name', 'سارة جمال')
            ->call('changeTemplate', $newTemplate->id);

        $invitation->refresh();

        $this->assertEquals($newTemplate->id, $invitation->template_id);
        $this->assertEquals('حسام الدين', $invitation->groom_name);
        $this->assertEquals('سارة جمال', $invitation->bride_name);
    }

    public function test_editor_toggles_publish_status()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        $this->assertEquals('published', $invitation->status);

        Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->call('togglePublish');

        $invitation->refresh();
        $this->assertEquals('draft', $invitation->status);

        Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->call('togglePublish');

        $invitation->refresh();
        $this->assertEquals('published', $invitation->status);
    }

    public function test_editor_upload_and_delete_gallery_photo()
    {
        Storage::fake('public');

        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        $file = UploadedFile::fake()->image('wedding-memory.png');

        $component = Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('new_gallery_photo', $file);

        $photo = InvitationPhoto::where('invitation_id', $invitation->id)->latest()->first();
        $this->assertNotNull($photo);

        $component->call('deleteGalleryPhoto', $photo->id);
        $this->assertNull(InvitationPhoto::find($photo->id));
    }

    public function test_rsvp_respects_max_guest_limit()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();
        $invitation->update(['max_guests_per_rsvp' => 3]);

        Livewire::test(RsvpForm::class, [
            'invitationId' => $invitation->id,
            'maxGuests' => 3,
        ])
            ->set('guest_name', 'عادل إمام')
            ->set('phone', '+201011112222')
            ->set('guest_count', 5)
            ->call('submit')
            ->assertHasErrors(['guest_count']);
    }

    public function test_rsvp_cannot_be_submitted_when_rsvp_is_disabled()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();
        $invitation->update(['is_rsvp_enabled' => false]);

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'محمد صبحي')
            ->set('phone', '+201099998888')
            ->set('guest_count', 1)
            ->call('submit');

        $this->assertDatabaseMissing('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'محمد صبحي',
        ]);
    }

    public function test_rsvp_duplicate_submission_handling()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        // First RSVP
        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'خالد النبوي')
            ->set('phone', '+201077776666')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'phone' => '+201077776666',
            'guest_count' => 2,
        ]);

        // Duplicate RSVP from same phone
        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'خالد النبوي')
            ->set('phone', '+201077776666')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 3)
            ->call('submit');

        // Should not create 2 separate records for the same phone!
        $count = RsvpResponse::where('invitation_id', $invitation->id)
            ->where('phone', '+201077776666')
            ->count();

        $this->assertEquals(1, $count);
    }

    public function test_unauthorized_user_cannot_delete_other_user_gallery_photo()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();
        $photo = $invitation->photos()->first();

        $attacker = User::factory()->create(['role' => 'user']);

        // Attacker creates their own invitation
        $attackerInvitation = Invitation::create([
            'user_id' => $attacker->id,
            'template_id' => $invitation->template_id,
            'title' => 'Attacker Wedding',
            'slug' => 'attacker-wedding',
            'groom_name' => 'Attacker',
            'bride_name' => 'Target',
            'wedding_date' => now()->addDays(10),
            'venue_name' => 'Hall',
            'status' => 'draft',
        ]);

        // Attacker mounts their own editor, but attempts to delete owner's photo
        Livewire::actingAs($attacker)
            ->test(InvitationEditor::class, ['id' => $attackerInvitation->id])
            ->call('deleteGalleryPhoto', $photo->id);

        // Photo should NOT be deleted!
        $this->assertNotNull(InvitationPhoto::find($photo->id));
    }

    public function test_unauthorized_user_cannot_mutate_another_users_invitation_via_editor()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();
        $originalGroomName = $invitation->groom_name;

        $attacker = User::factory()->create(['role' => 'user']);
        $attackerInvitation = Invitation::create([
            'user_id' => $attacker->id,
            'template_id' => $invitation->template_id,
            'title' => 'Attacker Wedding',
            'slug' => 'attacker-wedding-2',
            'groom_name' => 'Attacker',
            'bride_name' => 'Target',
            'wedding_date' => now()->addDays(10),
            'venue_name' => 'Hall',
            'status' => 'draft',
        ]);

        // Attacker attempts to change invitationId to owner's invitation
        try {
            Livewire::actingAs($attacker)
                ->test(InvitationEditor::class, ['id' => $attackerInvitation->id])
                ->set('invitationId', $invitation->id)
                ->set('groom_name', 'HACKED')
                ->call('saveChanges');
        } catch (\Throwable $e) {
            // It could throw an exception or 403
        }

        $invitation->refresh();
        $this->assertEquals($originalGroomName, $invitation->groom_name, 'Owner invitation was hacked via IDOR!');
    }

    public function test_all_six_templates_preview_and_render_successfully()
    {
        $templates = InvitationTemplate::all();
        $this->assertCount(6, $templates);

        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        foreach ($templates as $tmpl) {
            // Test public template preview page
            $previewResponse = $this->get("/templates/{$tmpl->slug}/preview");
            $previewResponse->assertStatus(200);

            // Test assigning this template to actual invitation
            $invitation->update(['template_id' => $tmpl->id]);
            $invitationResponse = $this->get("/invitation/{$invitation->slug}");
            $invitationResponse->assertStatus(200);
            $invitationResponse->assertSee($invitation->groom_name);
            $invitationResponse->assertSee($invitation->bride_name);
        }
    }

    public function test_template_filtering_and_search()
    {
        // Test filter by category
        $response = $this->get('/templates?category=luxury');
        $response->assertStatus(200);
        $response->assertSee('الفخامة الذهبية');

        // Test search (URL encoded for non-ASCII)
        $searchResponse = $this->get('/templates?search=' . urlencode('الزهور'));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('سحر الزهور');
    }

    public function test_language_switcher_updates_locale()
    {
        $response = $this->get('/lang/en');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        $arResponse = $this->get('/lang/ar');
        $arResponse->assertRedirect();
        $arResponse->assertSessionHas('locale', 'ar');
    }

    public function test_auth_routes_are_rate_limited()
    {
        // Send 7 login attempts to verify throttle kicks in
        $response = null;
        for ($i = 0; $i < 7; $i++) {
            $response = $this->post('/login', [
                'email' => 'baduser' . $i . '@zaffe.app',
                'password' => 'wrongpassword',
            ]);
        }

        // 7th attempt should be 429 Too Many Requests
        $this->assertEquals(429, $response->status());
    }

    public function test_unique_slug_generation_on_duplicate_names()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $template = InvitationTemplate::first();

        // Create first invitation
        $this->actingAs($user)->post('/dashboard/invitations', [
            'template_id' => $template->id,
            'groom_name' => 'يوسف الشريف',
            'bride_name' => 'إنجي علاء',
            'wedding_date' => now()->addDays(40)->format('Y-m-d'),
            'venue_name' => 'قصر العروبة',
        ]);

        $first = Invitation::where('groom_name', 'يوسف الشريف')->latest()->first();

        // Create second invitation with exact same names
        $this->actingAs($user)->post('/dashboard/invitations', [
            'template_id' => $template->id,
            'groom_name' => 'يوسف الشريف',
            'bride_name' => 'إنجي علاء',
            'wedding_date' => now()->addDays(50)->format('Y-m-d'),
            'venue_name' => 'قصر النيل',
        ]);

        $second = Invitation::where('venue_name', 'قصر النيل')->first();

        $this->assertNotEquals($first->slug, $second->slug);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{7,8}$/', $first->slug);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{7,8}$/', $second->slug);
    }

    public function test_csv_export_sanitizes_formula_injection()
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $user->id)->first();

        // Create a malicious RSVP with Excel formula injection
        RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'guest_name' => '=CMD|"/C calc"!A0',
            'phone' => '+201099887766',
            'attendance_status' => 'attending',
            'guest_count' => 1,
            'message' => '@SUM(1+1)',
            'dietary_notes' => '+EVIL_PAYLOAD',
        ]);

        $response = $this->actingAs($user)->get("/dashboard/invitations/{$invitation->id}/guests/export");
        $response->assertStatus(200);
        $content = $response->streamedContent();

        // Formulas must be escaped with a leading single quote
        $this->assertStringContainsString("'=CMD", $content);
        $this->assertStringContainsString("'@SUM", $content);
        $this->assertStringContainsString("'+EVIL_PAYLOAD", $content);
    }

    public function test_unauthorized_user_cannot_access_or_export_other_user_guests()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        $attacker = User::factory()->create(['role' => 'user']);

        // Attacker attempts to view owner's guest list
        $viewResponse = $this->actingAs($attacker)->get("/dashboard/invitations/{$invitation->id}/guests");
        $viewResponse->assertStatus(404);

        // Attacker attempts to export owner's guest list
        $exportResponse = $this->actingAs($attacker)->get("/dashboard/invitations/{$invitation->id}/guests/export");
        $exportResponse->assertStatus(404);
    }

    public function test_unauthorized_user_cannot_delete_other_user_invitation()
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();

        $attacker = User::factory()->create(['role' => 'user']);

        $deleteResponse = $this->actingAs($attacker)->delete("/dashboard/invitations/{$invitation->id}");
        $deleteResponse->assertStatus(404);

        $this->assertNotNull(Invitation::find($invitation->id));
    }

    public function test_rsvp_cannot_be_submitted_after_deadline()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();
        $invitation->update(['rsvp_deadline' => now()->subDay()->toDateString()]);

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'محمد صبحي')
            ->set('phone', '+201099998888')
            ->set('guest_count', 1)
            ->call('submit');

        $this->assertDatabaseMissing('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'محمد صبحي',
        ]);
    }

    public function test_rsvp_declining_attendance_sets_guest_count_to_zero()
    {
        $invitation = Invitation::where('slug', 'ahmed-and-sarah')->first();

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'عمرو واكد')
            ->set('phone', '+201044445555')
            ->set('attendance_status', 'declined')
            ->set('guest_count', 4)
            ->call('submit')
            ->assertSet('isSubmitted', true);

        $rsvp = RsvpResponse::where('invitation_id', $invitation->id)
            ->where('phone', '+201044445555')
            ->first();

        $this->assertNotNull($rsvp);
        $this->assertEquals(0, $rsvp->guest_count);
        $this->assertEquals('declined', $rsvp->attendance_status);
    }
}
