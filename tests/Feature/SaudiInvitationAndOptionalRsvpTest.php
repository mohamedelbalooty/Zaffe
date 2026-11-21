<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\TemplateCategory;
use App\Models\User;
use App\Notifications\NewRsvpNotification;
use App\Notifications\RsvpConfirmationNotification;
use App\Support\TemplateStyleCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class SaudiInvitationAndOptionalRsvpTest extends TestCase
{
    use RefreshDatabase;

    private User $couple;

    private InvitationTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->couple = User::factory()->create([
            'name' => 'بندر العتيبي',
            'email' => 'bandar@zaffe.app',
            'role' => 'user',
        ]);

        $category = TemplateCategory::create([
            'name_ar' => 'تراث سعودي',
            'name_en' => 'Saudi Heritage',
            'slug' => 'saudi-heritage-pr2',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->template = InvitationTemplate::create([
            'category_id' => $category->id,
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold-pr2',
            'view_name' => 'templates.royal_gold',
            'default_styles' => TemplateStyleCatalog::presets()['royal_gold'],
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_all_fourteen_templates_render_cleanly_without_bride_name_with_style_overrides_and_arabic_indic_countdown(): void
    {
        $views = TemplateStyleCatalog::bladeViews();
        $this->assertCount(14, $views);

        foreach ($views as $idx => $viewName) {
            $slug = 'tmpl-pr2-'.$idx;
            $tmpl = $viewName === $this->template->view_name
                ? $this->template
                : InvitationTemplate::create([
                    'category_id' => $this->template->category_id,
                    'name_ar' => 'قالب '.$idx,
                    'name_en' => 'Template '.$idx,
                    'slug' => $slug,
                    'view_name' => $viewName,
                    'default_styles' => TemplateStyleCatalog::presets()[str_replace('templates.', '', $viewName)] ?? TemplateStyleCatalog::DEFAULTS,
                    'is_premium' => false,
                    'is_active' => true,
                    'sort_order' => $idx + 2,
                ]);

            $invitation = Invitation::create([
                'user_id' => $this->couple->id,
                'template_id' => $tmpl->id,
                'title' => 'حفل زفاف سعود العتيبي',
                'slug' => 'saud-no-bride-'.$idx,
                'groom_name' => 'سعود العتيبي',
                'bride_name' => null,
                'wedding_date' => now()->addDays(40)->toDateString(),
                'wedding_time' => '20:30',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الرياض للاحتفالات',
                'welcome_message' => 'رسالة ترحيب خاصة مخصصة',
                'status' => 'published',
                'is_rsvp_enabled' => false,
                'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
                'custom_styles' => [
                    'primary_color' => '#0F766E',
                    'secondary_color' => '#F8FAFC',
                    'background_color' => '#102A24',
                    'card_color' => '#163830',
                    'font_family' => 'Cairo',
                    'digits' => 'arabic_indic',
                ],
            ]);

            InvitationSection::create([
                'invitation_id' => $invitation->id,
                'section_type' => 'countdown',
                'title_ar' => 'العد التنازلي',
                'is_enabled' => true,
            ]);

            $response = $this->get(route('invitation.show', $invitation->slug));
            $response->assertOk();

            $html = $response->getContent();
            $this->assertIsString($html);
            $this->assertStringContainsString('سعود العتيبي', $html, "Missing groom_name in {$viewName}");
            $this->assertStringNotContainsString('Black Tie', $html, "Unexpected Black Tie placeholder in {$viewName}");

            // Ensure no orphan couple separators around the main heading when bride_name is null
            $this->assertDoesNotMatchRegularExpression('/<h1[^>]*>\s*سعود العتيبي\s*<\/h1>\s*(?:<[^>]+>\s*(?:و|وَ|&amp;|&)\s*<\/[^>]+>)/u', $html);

            // Countdown respects arabic_indic digits
            $this->assertStringContainsString('arabicIndic: true', $html);
            $this->assertStringContainsString('>٠<', $html);

            if ($viewName === 'templates.islamic_elegance') {
                $this->assertStringContainsString('رسالة ترحيب خاصة مخصصة', $html);
            }

            if (in_array($viewName, ['templates.emerald_night', 'templates.desert_dusk'], true)) {
                $this->assertStringContainsString('#0F766E', $html);
                $this->assertStringContainsString('#102A24', $html);
            }
        }
    }

    public function test_optional_bride_name_omits_orphan_separators_in_dashboard_whatsapp_and_notifications(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف تركي الدوسري',
            'slug' => 'turki-no-bride',
            'groom_name' => 'تركي الدوسري',
            'bride_name' => null,
            'wedding_date' => now()->addDays(30)->toDateString(),
            'wedding_time' => '21:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة نيارة',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
        ]);

        $this->assertFalse($invitation->hasBrideName());
        $this->assertSame('تركي الدوسري', $invitation->formattedCoupleNames('ar'));
        $this->assertSame('تركي الدوسري', $invitation->formattedCoupleNames('en'));
        $this->assertStringNotContainsString(' و ', $invitation->getWhatsAppShareText('ar'));
        $this->assertStringContainsString("Wedding of تركي الدوسري\n", $invitation->getWhatsAppShareText('en'));
        $this->assertStringNotContainsString('تركي الدوسري &', $invitation->getWhatsAppShareText('en'));

        $this->actingAs($this->couple)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('تركي الدوسري')
            ->assertDontSee('تركي الدوسري &amp;', false);

        $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.index'))
            ->assertOk()
            ->assertDontSee('تركي الدوسري &amp;', false);

        $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests', $invitation->id))
            ->assertOk()
            ->assertDontSee('تركي الدوسري &amp;', false);
    }

    public function test_dec_02_optional_rsvp_toggle_transitions_to_rsvp_only_without_qr_and_is_non_destructive(): void
    {
        Notification::fake();

        // Create via Customer Dashboard controller (DEC-02 defaults to announcement / RSVP off, Asia/Riyadh)
        $this->actingAs($this->couple)
            ->post(route('dashboard.invitations.store'), [
                'template_id' => $this->template->id,
                'groom_name' => 'عبدالعزيز',
                'wedding_date' => now()->addDays(45)->toDateString(),
                'venue_name' => 'فندق الفورسيزونز الرياض',
            ])
            ->assertRedirect();

        $invitation = Invitation::where('groom_name', 'عبدالعزيز')->firstOrFail();
        $this->assertFalse($invitation->is_rsvp_enabled);
        $this->assertSame(Invitation::MODE_ANNOUNCEMENT, $invitation->invitation_mode);
        $this->assertSame('Asia/Riyadh', $invitation->timezone);

        // New card omits RSVP form while disabled
        auth()->logout();
        $this->get(route('invitation.show', $invitation->slug))->assertForbidden(); // draft first
        $invitation->update(['status' => 'published']);
        $this->get(route('invitation.show', $invitation->slug))
            ->assertOk()
            ->assertDontSee('إرسال تأكيد الحضور');

        // Direct model toggle (Invitation::booted) moves announcement -> rsvp_only (never rsvp_qr)
        $invitation->update(['is_rsvp_enabled' => true]);
        $invitation->refresh();
        $this->assertSame(Invitation::MODE_RSVP_ONLY, $invitation->invitation_mode);
        $this->assertFalse($invitation->supportsQrPass());

        // Flip back off then toggle on through InvitationEditor Livewire component
        $invitation->update(['is_rsvp_enabled' => false]);
        $invitation->refresh();
        $this->assertSame(Invitation::MODE_ANNOUNCEMENT, $invitation->invitation_mode);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->assertSet('is_rsvp_enabled', false)
            ->assertSet('invitation_mode', Invitation::MODE_ANNOUNCEMENT)
            ->set('activeTab', 'rsvp')
            ->assertSee('id="ed_is_rsvp_enabled"', false)
            ->set('is_rsvp_enabled', true)
            ->assertSee('max="20"', false)
            ->call('saveChanges')
            ->assertHasNoErrors();

        $invitation->refresh();
        $this->assertTrue($invitation->is_rsvp_enabled);
        $this->assertSame(Invitation::MODE_RSVP_ONLY, $invitation->invitation_mode);

        // Guest submits RSVP -> zero QR pass created, response stored cleanly
        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'نايف السبيعي')
            ->set('phone_country', 'SA')
            ->set('phone', '0507778899')
            ->set('attendance_status', 'attending')
            ->set('guest_count', 2)
            ->call('submit')
            ->assertSet('isSubmitted', true)
            ->assertSet('issuedPassUrl', null);

        $this->assertDatabaseCount('invitation_guests', 0);
        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'نايف السبيعي',
            'guest_count' => 2,
        ]);

        // Disabling RSVP hides form without deleting collected RSVP responses
        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('is_rsvp_enabled', false)
            ->call('saveChanges');

        $this->get(route('invitation.show', $invitation->slug))
            ->assertOk()
            ->assertDontSee('إرسال تأكيد الحضور');

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'نايف السبيعي',
        ]);
    }

    public function test_dec_01_hides_roster_on_new_phase_1_invitations_while_preserving_legacy_roster_and_qr_wording(): void
    {
        // New Phase 1 RSVP-only invitation hides GuestRosterManager on /guests
        $newInvitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف فهد',
            'slug' => 'fahad-new-p1',
            'groom_name' => 'فهد',
            'bride_name' => null,
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
        ]);

        RsvpResponse::create([
            'invitation_id' => $newInvitation->id,
            'guest_name' => 'خالد الشمري',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests', $newInvitation->id))
            ->assertOk()
            ->assertSee('خالد الشمري')
            ->assertDontSeeLivewire('guest-roster-manager');

        // Legacy invitation (rsvp_qr OR having existing InvitationGuest rows) preserves roster + personal pass wording
        $legacyInvitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف سالم ونورة',
            'slug' => 'salem-legacy-qr',
            'groom_name' => 'سالم',
            'bride_name' => 'نورة',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة الفريدة',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_QR,
        ]);

        $legacyGuest = InvitationGuest::create([
            'invitation_id' => $legacyInvitation->id,
            'name' => 'الشيخ ماجد',
            'phone' => '+966501112233',
            'max_adults' => 3,
            'max_children' => 1,
            'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
        ]);

        $this->actingAs($this->couple)
            ->get(route('dashboard.invitations.guests', $legacyInvitation->id))
            ->assertOk()
            ->assertSeeLivewire('guest-roster-manager');

        $this->get($legacyGuest->getPersonalInviteUrl())
            ->assertOk()
            ->assertSee('الشيخ ماجد')
            ->assertSee('بطاقة الدعوة الشخصية (QR)')
            ->assertDontSee('بطاقة الدخول');

        $this->get($legacyGuest->getPassUrl())
            ->assertOk()
            ->assertSee('بطاقة الدعوة الشخصية')
            ->assertDontSee('بطاقة الدخول');
    }

    public function test_dec_04_3_suppresses_unedited_timeline_and_dress_code_while_rendering_genuine_saved_content(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف طلال',
            'slug' => 'talal-sections-test',
            'groom_name' => 'طلال',
            'bride_name' => null,
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة الشرق',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
        ]);

        // Even if enabled flag exists without custom content, dummy Black Tie / hardcoded schedule never renders
        InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'timeline',
            'is_enabled' => true,
            'content' => null,
        ]);
        InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'dress_code',
            'is_enabled' => true,
            'content' => null,
        ]);

        $this->get(route('invitation.show', $invitation->slug))
            ->assertOk()
            ->assertDontSee('Black Tie')
            ->assertDontSee('استقبال الضيوف الكرام')
            ->assertDontSee('بوفيه مفتوح');

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('activeTab', 'rsvp')
            ->assertDontSee('قواعد اللباس (Dress Code)')
            ->assertDontSee('جدول مواعيد وبرنامج الحفل');

        // Genuine saved custom content renders cleanly and unlocks toggle in Editor
        InvitationSection::where('invitation_id', $invitation->id)
            ->where('section_type', 'dress_code')
            ->update(['content' => ['title' => 'الزي الوطني السعودي', 'notes' => 'حضوركم يشرفنا بالبشت والثوب']]);

        $this->get(route('invitation.show', $invitation->slug))
            ->assertOk()
            ->assertSee('الزي الوطني السعودي')
            ->assertSee('حضوركم يشرفنا بالبشت والثوب');

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('activeTab', 'rsvp')
            ->assertSee('قواعد اللباس (Dress Code)');
    }

    public function test_c_6_editor_hides_music_url_for_new_cards_locks_property_and_preserves_or_clears_legacy_audio(): void
    {
        $newInvitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل جديد بدون صوت',
            'slug' => 'new-no-music',
            'groom_name' => 'وليد',
            'bride_name' => null,
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الخليج',
            'status' => 'draft',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
        ]);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $newInvitation->id])
            ->set('activeTab', 'content')
            ->assertDontSeeHtml('id="ed_music_url"')
            ->assertDontSee('إزالة الصوت');

        $legacyAudioInvitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل إرث بصوت محفوظ',
            'slug' => 'legacy-with-audio',
            'groom_name' => 'منصور',
            'bride_name' => 'لولوة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر المملكة',
            'music_url' => 'https://cdn.example.com/legacy-zaffe.mp3',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
        ]);

        // Editing unrelated fields preserves legacy music_url cleanly
        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $legacyAudioInvitation->id])
            ->set('activeTab', 'content')
            ->assertDontSeeHtml('id="ed_music_url"')
            ->assertSee('مقطع صوتي محفوظ مسبقاً')
            ->set('venue_name', 'قاعة المملكة الكبرى')
            ->call('saveChanges');

        $this->assertSame('https://cdn.example.com/legacy-zaffe.mp3', $legacyAudioInvitation->fresh()->music_url);

        // Owner can clear legacy audio via explicit button
        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $legacyAudioInvitation->id])
            ->call('clearLegacyMusicUrl');

        $this->assertNull($legacyAudioInvitation->fresh()->music_url);

        // Client cannot overwrite locked music_url property via Livewire
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($this->couple)
            ->test(InvitationEditor::class, ['id' => $newInvitation->id])
            ->set('music_url', 'https://evil.example.com/audio.mp3');
    }

    public function test_notifications_handle_declined_rsvp_children_count_legacy_pass_and_emoji_removal(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->couple->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف عبدالله',
            'slug' => 'abdullah-notif-test',
            'groom_name' => 'عبدالله',
            'bride_name' => null,
            'wedding_date' => now()->addDays(25)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة الريتز',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_QR,
        ]);

        $guest = InvitationGuest::create([
            'invitation_id' => $invitation->id,
            'name' => 'فيصل القحطاني',
            'max_adults' => 2,
            'max_children' => 2,
            'rsvp_status' => InvitationGuest::STATUS_ATTENDING,
        ]);

        $attendingRsvp = RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'invitation_guest_id' => $guest->id,
            'guest_name' => 'فيصل القحطاني',
            'phone' => null,
            'email' => 'faisal@example.com',
            'attendance_status' => 'attending',
            'guest_count' => 2,
            'children_count' => 2,
        ]);

        $declinedRsvp = RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'guest_name' => 'يوسف الغامدي',
            'email' => 'yousef@example.com',
            'attendance_status' => 'declined',
            'guest_count' => 0,
            'children_count' => 0,
        ]);

        app()->setLocale('ar');
        $coupleMail = (new NewRsvpNotification($invitation, $attendingRsvp))->toMail($this->couple);
        $coupleHtml = (string) $coupleMail->render();
        $this->assertStringNotContainsString('🎉', $coupleHtml);
        $this->assertStringContainsString('عدد الأطفال:', $coupleHtml);

        $guestAttendingMailAr = (string) (new RsvpConfirmationNotification($invitation, $attendingRsvp))->toMail($attendingRsvp)->render();
        $this->assertStringContainsString('عبدالله', $guestAttendingMailAr);
        $this->assertStringNotContainsString('عبدالله و ', $guestAttendingMailAr);
        $this->assertStringContainsString('عدد الأطفال المرافقين:', $guestAttendingMailAr);
        $this->assertStringContainsString($guest->getPassUrl(), $guestAttendingMailAr);

        $guestDeclinedMailAr = (new RsvpConfirmationNotification($invitation, $declinedRsvp))->toMail($declinedRsvp);
        $guestDeclinedHtmlAr = (string) $guestDeclinedMailAr->render();
        $this->assertStringContainsString('اعتذاركم', $guestDeclinedMailAr->subject);
        $this->assertStringNotContainsString('عدد المقاعد المحجوزة', $guestDeclinedHtmlAr);

        app()->setLocale('en');
        $guestDeclinedMailEn = (new RsvpConfirmationNotification($invitation, $declinedRsvp))->toMail($declinedRsvp);
        $guestDeclinedHtmlEn = (string) $guestDeclinedMailEn->render();
        $this->assertStringContainsString('Thank you for your response', $guestDeclinedMailEn->subject);
        $this->assertStringNotContainsString('Reserved Adult Seats', $guestDeclinedHtmlEn);
    }
}
