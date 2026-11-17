<?php

namespace Tests\Feature;

use App\Filament\Resources\InvitationGuests\InvitationGuestResource;
use App\Livewire\GuestRosterManager;
use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SmartGuestAndQrCheckInTest extends TestCase
{
    use RefreshDatabase;

    private User $couple;

    private User $otherCouple;

    private User $admin;

    private InvitationTemplate $template;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->couple = User::factory()->create(['role' => 'user']);
        $this->otherCouple = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->template = InvitationTemplate::create([
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
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف فهد وسارة',
            'slug' => 'fahad-sara-'.uniqid(),
            'groom_name' => 'فهد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(25),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_QR,
            'max_guests_per_rsvp' => 5,
        ]);
    }

    public function test_editor_switches_all_four_invitation_modes_and_syncs_rsvp_flag(): void
    {
        $editor = Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id]);

        $editor->set('invitation_mode', Invitation::MODE_QR_ONLY)->call('saveChanges');
        $this->invitation->refresh();
        $this->assertSame(Invitation::MODE_QR_ONLY, $this->invitation->invitation_mode);
        $this->assertFalse($this->invitation->is_rsvp_enabled);
        $this->assertFalse($this->invitation->allowsRsvp());
        $this->assertTrue($this->invitation->supportsQrPass());

        $editor->set('invitation_mode', Invitation::MODE_RSVP_ONLY)->call('saveChanges');
        $this->invitation->refresh();
        $this->assertSame(Invitation::MODE_RSVP_ONLY, $this->invitation->invitation_mode);
        $this->assertTrue($this->invitation->is_rsvp_enabled);
        $this->assertTrue($this->invitation->allowsRsvp());
        $this->assertFalse($this->invitation->supportsQrPass());

        $editor->set('invitation_mode', Invitation::MODE_ANNOUNCEMENT)->call('saveChanges');
        $this->invitation->refresh();
        $this->assertSame(Invitation::MODE_ANNOUNCEMENT, $this->invitation->invitation_mode);
        $this->assertFalse($this->invitation->is_rsvp_enabled);
        $this->assertFalse($this->invitation->allowsRsvp());
        $this->assertFalse($this->invitation->supportsQrPass());
    }

    public function test_open_rsvp_in_rsvp_qr_mode_auto_issues_qr_pass_and_rsvp_only_omits_qr(): void
    {
        // 1. RSVP + QR mode
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'خالد بن عبدالله')
            ->set('phone_country', 'SA')
            ->set('phone', '0501234567')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 3)
            ->call('submit')
            ->assertSet('isSubmitted', true)
            ->assertSee('بطاقة الدعوة الشخصية الخاصة بكم');

        $guest = InvitationGuest::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($guest);
        $this->assertSame('attending', $guest->rsvp_status);
        $this->assertSame(3, $guest->confirmed_adults);

        // Pass URL renders clean vector QR
        $this->get($guest->getPassUrl())
            ->assertOk()
            ->assertSee('<svg xmlns="http://www.w3.org/2000/svg"', false)
            ->assertSee($guest->pass_code);

        // 2. RSVP-only mode disables QR pass route
        $this->invitation->update(['invitation_mode' => Invitation::MODE_RSVP_ONLY]);

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'سلمان الحربي')
            ->set('phone_country', 'SA')
            ->set('phone', '0559876543')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertSet('isSubmitted', true)
            ->assertSet('issuedPassUrl', null);

        $this->get($guest->getPassUrl())->assertNotFound();
    }

    public function test_qr_only_and_announcement_modes_behave_accurately(): void
    {
        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'الأمير تركي',
            'phone' => '+966501112233',
            'category' => InvitationGuest::CATEGORY_VIP,
            'max_adults' => 2,
            'max_children' => 1,
        ]);

        // QR-only: shows direct pass for personal link, hides form
        $this->invitation->update(['invitation_mode' => Invitation::MODE_QR_ONLY]);

        $this->get('/invitation/'.$this->invitation->slug.'?g='.$guest->invite_token)
            ->assertOk()
            ->assertSee('الأمير تركي')
            ->assertSee('بطاقة الدخول المباشرة (QR Pass)')
            ->assertSee($guest->pass_code)
            ->assertDontSee('إرسال تأكيد الحضور');

        // Announcement mode: hides both RSVP and QR card
        $this->invitation->update(['invitation_mode' => Invitation::MODE_ANNOUNCEMENT]);

        $this->get('/invitation/'.$this->invitation->slug.'?g='.$guest->invite_token)
            ->assertOk()
            ->assertSee('الأمير تركي')
            ->assertDontSee('بطاقة الدخول المباشرة')
            ->assertDontSee('إرسال تأكيد الحضور');
    }

    public function test_personalized_link_pre_fills_enforces_quotas_and_prevents_cross_invitation_idor(): void
    {
        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'د. مازن السعيد',
            'phone' => '+966504445566',
            'category' => InvitationGuest::CATEGORY_FAMILY_GROOM,
            'max_adults' => 2,
            'max_children' => 1,
        ]);

        // Exceeding adult quota rejected
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id, 'guestId' => $guest->id])
            ->assertSet('guest_name', 'د. مازن السعيد')
            ->assertSet('maxGuests', 2)
            ->assertSet('maxChildren', 1)
            ->set('guest_count', 4)
            ->call('submit')
            ->assertHasErrors(['guest_count']);

        // Exceeding children quota rejected
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id, 'guestId' => $guest->id])
            ->set('guest_count', 2)
            ->set('children_count', 3)
            ->call('submit')
            ->assertHasErrors(['children_count']);

        // Valid within-quota confirmation links RSVP and guest
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id, 'guestId' => $guest->id])
            ->set('guest_count', 2)
            ->set('children_count', 1)
            ->set('attendance_status', 'attending')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $guest->refresh();
        $this->assertSame('attending', $guest->rsvp_status);
        $this->assertSame(2, $guest->confirmed_adults);
        $this->assertSame(1, $guest->confirmed_children);
        $this->assertNotNull($guest->rsvp_response_id);
        $this->assertDatabaseHas('rsvp_responses', [
            'id' => $guest->rsvp_response_id,
            'invitation_guest_id' => $guest->id,
            'guest_count' => 2,
            'children_count' => 1,
        ]);

        // Cross-invitation token leak check (Wedding B + ?g=Wedding A token must ignore token)
        $otherInvitation = Invitation::create([
            'user_id' => $this->otherCouple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف آخر',
            'slug' => 'other-wedding-'.uniqid(),
            'groom_name' => 'أحمد',
            'bride_name' => 'ليلى',
            'wedding_date' => now()->addDays(40),
            'timezone' => 'Africa/Cairo',
            'venue_name' => 'فندق النيل',
            'status' => 'published',
        ]);

        $this->get('/invitation/'.$otherInvitation->slug.'?g='.$guest->invite_token)
            ->assertOk()
            ->assertDontSee('د. مازن السعيد');
    }

    public function test_owner_can_manage_roster_while_non_owner_is_forbidden(): void
    {
        Livewire::actingAs($this->otherCouple)
            ->test(GuestRosterManager::class, ['invitationId' => $this->invitation->id])
            ->assertForbidden();

        Livewire::actingAs($this->couple)
            ->test(GuestRosterManager::class, ['invitationId' => $this->invitation->id])
            ->set('guestName', 'الأميرة العنود')
            ->set('guestPhoneCountry', 'SA')
            ->set('guestPhone', '0509988776')
            ->set('guestCategory', InvitationGuest::CATEGORY_VIP)
            ->set('maxAdults', 3)
            ->set('maxChildren', 2)
            ->call('addGuest')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('invitation_guests', [
            'invitation_id' => $this->invitation->id,
            'name' => 'الأميرة العنود',
            'phone' => '+966509988776',
            'category' => InvitationGuest::CATEGORY_VIP,
            'max_adults' => 3,
            'max_children' => 2,
        ]);
    }

    public function test_bulk_csv_import_and_roster_export_neutralize_formula_injection(): void
    {
        $csvContent = "الاسم,الهاتف,بالغين,أطفال,الفئة\n"
            ."=cmd('calc'),+966507778899,2,1,vip\n"
            ."منى الشريف,01001234567,3,0,family_bride\n";

        $file = UploadedFile::fake()->createWithContent('guests.csv', $csvContent);

        Livewire::actingAs($this->couple)
            ->test(GuestRosterManager::class, ['invitationId' => $this->invitation->id])
            ->set('csvFile', $file)
            ->call('importBulkGuests')
            ->assertHasNoErrors();

        $guests = InvitationGuest::where('invitation_id', $this->invitation->id)->get();
        $this->assertCount(2, $guests);

        $formulaGuest = $guests->firstWhere('category', 'vip');
        $this->assertNotNull($formulaGuest);
        $this->assertStringStartsNotWith('=', $formulaGuest->name);

        // WhatsApp tracking marks invited_at then reminded_at
        $component = Livewire::actingAs($this->couple)
            ->test(GuestRosterManager::class, ['invitationId' => $this->invitation->id]);

        $component->call('markInvited', $formulaGuest->id);
        $this->assertNotNull($formulaGuest->fresh()->invited_at);
        $this->assertNull($formulaGuest->fresh()->reminded_at);

        $component->call('markInvited', $formulaGuest->id);
        $this->assertNotNull($formulaGuest->fresh()->reminded_at);

        // Export CSV works for owner and blocks non-owner
        $this->actingAs($this->otherCouple)
            ->get(route('dashboard.invitations.guests.roster-export', $this->invitation->id))
            ->assertNotFound();

        $exportResponse = $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests.roster-export', $this->invitation->id));

        $exportResponse->assertOk();
        $body = $exportResponse->streamedContent();
        $this->assertStringContainsString('اسم الضيف', $body);
        $this->assertStringContainsString('منى الشريف', $body);
    }

    public function test_filament_admin_resource_allows_super_admin_and_blocks_couples(): void
    {
        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'ضيف إداري',
            'category' => InvitationGuest::CATEGORY_VIP,
            'max_adults' => 2,
        ]);

        // Couple cannot access Filament resource or policy
        $this->assertFalse($this->couple->can('viewAny', InvitationGuest::class));
        $this->assertFalse($this->couple->can('create', InvitationGuest::class));
        $this->actingAs($this->couple)
            ->get(InvitationGuestResource::getUrl('index'))
            ->assertForbidden();

        // Admin can view index & view pages
        $this->assertTrue($this->admin->can('viewAny', InvitationGuest::class));
        $this->assertFalse($this->admin->can('create', InvitationGuest::class));
        $this->actingAs($this->admin)
            ->get(InvitationGuestResource::getUrl('index'))
            ->assertOk()
            ->assertSee('ضيف إداري')
            ->assertSee($guest->pass_code)
            ->assertDontSee($guest->qr_token)
            ->assertDontSee($guest->invite_token);

        $this->actingAs($this->admin)
            ->get(InvitationGuestResource::getUrl('view', ['record' => $guest]))
            ->assertOk()
            ->assertSee('ضيف إداري')
            ->assertDontSee($guest->qr_token)
            ->assertDontSee($guest->invite_token);
    }

    public function test_open_rsvp_cannot_hijack_pre_invited_guest_quota_or_pass_by_phone(): void
    {
        $vip = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'الأمير خالد',
            'phone' => '+966501112233',
            'category' => InvitationGuest::CATEGORY_VIP,
            'max_adults' => 1,
            'max_children' => 0,
        ]);

        // Unauthenticated attacker tries using VIP's phone on the open RSVP form
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'مهاجم')
            ->set('phone', '0501112233')
            ->set('phone_country', 'SA')
            ->set('attendance_status', 'declined')
            ->set('guest_count', 5)
            ->call('submit')
            ->assertHasErrors(['phone'])
            ->assertSet('issuedPassUrl', null)
            ->assertSet('issuedPassCode', null);

        $vip->refresh();
        $this->assertSame('الأمير خالد', $vip->name);
        $this->assertSame(InvitationGuest::STATUS_PENDING, $vip->rsvp_status);
        $this->assertSame(1, $vip->max_adults);

        // Even AFTER VIP confirms via their secret ?g= token, open form cannot overwrite or leak their pass
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id, 'guestId' => $vip->id])
            ->set('guest_name', 'الأمير خالد')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 1)
            ->call('submit')
            ->assertHasNoErrors();

        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'مهاجم ثانٍ')
            ->set('phone', '0501112233')
            ->set('phone_country', 'SA')
            ->set('attendance_status', 'declined')
            ->call('submit')
            ->assertHasErrors(['phone'])
            ->assertSet('issuedPassUrl', null);

        $this->assertSame(InvitationGuest::STATUS_ATTENDING, $vip->fresh()->rsvp_status);
    }

    public function test_open_rsvp_re_submission_does_not_leak_existing_qr(): void
    {
        // First open RSVP creates self-registered pass and shows pass URL once
        $first = Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'نورة السالم')
            ->set('phone', '0509998877')
            ->set('phone_country', 'SA')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->set('children_count', 1)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertNotNull($first->get('issuedPassUrl'));
        $selfGuest = InvitationGuest::where('invitation_id', $this->invitation->id)->where('phone', '+966509998877')->firstOrFail();
        $this->assertTrue($selfGuest->is_self_registered);

        // Second open submission with same phone updates headcount WITHOUT re-disclosing bearer pass credentials
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'نورة السالم')
            ->set('phone', '0509998877')
            ->set('phone_country', 'SA')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 3)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('issuedPassUrl', null)
            ->assertSet('issuedPassCode', null)
            ->assertSet('passPreviouslyIssued', true);
    }

    public function test_invitation_mode_prevails_on_simultaneous_save_and_blocks_pass_when_qr_disabled(): void
    {
        // Passing contradictory is_rsvp_enabled alongside invitation_mode resolves from invitation_mode
        $this->invitation->update([
            'invitation_mode' => Invitation::MODE_QR_ONLY,
            'is_rsvp_enabled' => true,
        ]);
        $this->assertFalse($this->invitation->fresh()->is_rsvp_enabled);

        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'ضيف وضع QR',
            'max_adults' => 2,
        ]);

        $this->get($guest->getPassUrl())->assertOk();

        // Switching to rsvp_only preserves guest records while blocking /pass
        $this->invitation->update(['invitation_mode' => Invitation::MODE_RSVP_ONLY]);

        $this->assertDatabaseHas('invitation_guests', ['id' => $guest->id]);
        $this->get($guest->getPassUrl())->assertNotFound();
    }

    public function test_guest_roster_stats_and_dashboard_children_counts_stay_accurate(): void
    {
        InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'عائلة أولى',
            'max_adults' => 2,
            'max_children' => 1,
            'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
            'confirmed_adults' => 2,
            'confirmed_children' => 1,
        ]);
        InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'عائلة ثانية',
            'max_adults' => 3,
            'max_children' => 1,
            'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
            'confirmed_adults' => 3,
            'confirmed_children' => 1,
        ]);
        InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'ضيف بانتظار الرد',
            'max_adults' => 2,
            'rsvp_status' => InvitationGuest::STATUS_PENDING,
        ]);

        $stats = $this->invitation->guestRosterStats();
        $this->assertSame(3, $stats['total_invited']);
        $this->assertSame(2, $stats['attending_parties']);
        $this->assertSame(7, $stats['attending_heads']);
        $this->assertSame(1, $stats['pending_parties']);

        // Dashboard guests summary & CSV include children_count
        Livewire::test(RsvpForm::class, ['invitationId' => $this->invitation->id])
            ->set('guest_name', 'ضيف مع أطفال')
            ->set('phone', '01055544332')
            ->set('phone_country', 'EG')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->set('children_count', 3)
            ->call('submit')
            ->assertHasNoErrors();

        $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests', $this->invitation->id))
            ->assertOk()
            ->assertSee('5');

        $csv = $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests.export', $this->invitation->id))
            ->assertOk()
            ->streamedContent();
        $this->assertStringContainsString('عدد الأطفال', $csv);
    }

    public function test_capability_urls_emit_privacy_headers_and_models_hide_secret_tokens(): void
    {
        $guest = InvitationGuest::create([
            'invitation_id' => $this->invitation->id,
            'name' => 'ضيف الخصوصية',
            'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
            'confirmed_adults' => 1,
        ]);

        $this->assertArrayNotHasKey('qr_token', $guest->toArray());
        $this->assertArrayNotHasKey('invite_token', $guest->toArray());

        foreach ([$guest->getPassUrl(), $guest->getPersonalInviteUrl()] as $url) {
            $response = $this->get($url)->assertOk();
            $response->assertHeader('Referrer-Policy', 'no-referrer');
            $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
    }
}
