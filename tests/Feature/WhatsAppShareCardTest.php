<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationShareCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppShareCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_share_card_page_requires_auth_and_enforces_strict_owner_isolation(): void
    {
        $owner = User::where('email', 'couple@zaffe.app')->firstOrFail();
        $otherUser = User::factory()->create();
        $invitation = Invitation::where('user_id', $owner->id)->firstOrFail();

        // Guest redirected to login
        $this->get(route('dashboard.invitations.share', $invitation->id))
            ->assertRedirect(route('login'));

        // Non-owner receives 404 due to Auth::user()->invitations() scoping
        $this->actingAs($otherUser)
            ->get(route('dashboard.invitations.share', $invitation->id))
            ->assertNotFound();

        // Owner sees share card studio
        $response = $this->actingAs($owner)
            ->get(route('dashboard.invitations.share', $invitation->id));

        $response->assertOk();
        $response->assertSee('ZaffeShareCard', false);
        $response->assertSee('1080 × 1350', false);
        $response->assertSee('تحميل صورة الدعوة (PNG)');
        $response->assertSee('فتح واتساب بالرسالة والرابط');
        $response->assertSee('https://api.whatsapp.com/send?text=', false);
    }

    public function test_payload_and_whatsapp_message_handle_bride_name_present_and_omitted_cleanly(): void
    {
        $owner = User::factory()->create();
        $template = InvitationTemplate::where('slug', 'taif-rose')->firstOrFail();
        $service = app(InvitationShareCardService::class);

        $withBride = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف فيصل والجوهرة',
            'slug' => 'faisal-jawhara-share',
            'groom_name' => 'فيصل بن عبد العزيز آل مقرن',
            'bride_name' => 'الجوهرة بنت خالد السديري',
            'wedding_date' => '2026-12-20',
            'wedding_time' => '20:30:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر طويق بالرياض',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            'is_rsvp_enabled' => true,
            'custom_styles' => array_merge($template->default_styles ?? [], [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ]),
        ]);

        $payloadWithBride = $service->buildPayload($withBride);
        $this->assertTrue($payloadWithBride['invitation']['has_bride_name']);
        $this->assertSame('فيصل', $payloadWithBride['invitation']['groom_parts']['first']);
        $this->assertSame('الجوهرة', $payloadWithBride['invitation']['bride_parts']['first']);
        $this->assertSame('تتشرف العائلتان الكريمتان بدعوتكم لحفل زفاف', $payloadWithBride['copy']['eyebrow']);
        $this->assertStringContainsString('فيصل بن عبد العزيز آل مقرن و الجوهرة بنت خالد السديري', $withBride->getWhatsAppShareText('ar'));
        $this->assertStringContainsString('Faisal', str_replace('فيصل بن عبد العزيز آل مقرن', 'Faisal', $withBride->getWhatsAppShareText('en')));

        $withoutBride = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف سعود الدوسري',
            'slug' => 'saud-no-bride-share',
            'groom_name' => 'سعود بن فهد الدوسري',
            'bride_name' => null,
            'wedding_date' => '2026-12-25',
            'wedding_time' => '21:00:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة نيارة بالرياض',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'is_rsvp_enabled' => false,
            'custom_styles' => array_merge($template->default_styles ?? [], [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ]),
        ]);

        $payloadWithoutBride = $service->buildPayload($withoutBride);
        $this->assertFalse($payloadWithoutBride['invitation']['has_bride_name']);
        $this->assertSame(['first' => '', 'lineage' => ''], $payloadWithoutBride['invitation']['bride_parts']);
        $this->assertSame('نتشرف بدعوتكم لحضور حفل زفاف', $payloadWithoutBride['copy']['eyebrow']);

        $arText = $withoutBride->getWhatsAppShareText('ar');
        $enText = $withoutBride->getWhatsAppShareText('en');

        $this->assertStringContainsString('سعود بن فهد الدوسري', $arText);
        $this->assertStringNotContainsString(' و ', $arText);
        $this->assertStringContainsString("Wedding of سعود بن فهد الدوسري\n", $enText);
        $this->assertStringNotContainsString('سعود بن فهد الدوسري &', $enText);
    }

    public function test_whatsapp_message_and_share_card_adapt_to_rsvp_enabled_vs_disabled(): void
    {
        $owner = User::factory()->create();
        $template = InvitationTemplate::where('slug', 'diriyah-heritage')->firstOrFail();
        $service = app(InvitationShareCardService::class);

        $rsvpEnabled = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف نايف',
            'slug' => 'naif-rsvp-on-share',
            'groom_name' => 'نايف العتيبي',
            'bride_name' => null,
            'wedding_date' => '2027-01-10',
            'wedding_time' => '20:00:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الدرعية',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            'is_rsvp_enabled' => true,
        ]);

        $rsvpPayload = $service->buildPayload($rsvpEnabled);
        $this->assertTrue($rsvpPayload['invitation']['allows_rsvp']);
        $this->assertStringContainsString('تأكيد الحضور', $rsvpPayload['copy']['cta_badge']);

        $arEnabledText = $rsvpEnabled->getWhatsAppShareText('ar');
        $enEnabledText = $rsvpEnabled->getWhatsAppShareText('en');
        $this->assertStringContainsString('السلام عليكم ورحمة الله وبركاته', $arEnabledText);
        $this->assertStringContainsString('للاطلاع على تفاصيل الدعوة والموقع وتأكيد الحضور:', $arEnabledText);
        $this->assertStringContainsString($rsvpEnabled->public_url, $arEnabledText);
        $this->assertStringNotContainsString('❤️', $arEnabledText);
        $this->assertStringContainsString('confirm attendance', $enEnabledText);

        $rsvpDisabled = Invitation::create([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف سلطان',
            'slug' => 'sultan-rsvp-off-share',
            'groom_name' => 'سلطان القحطاني',
            'bride_name' => null,
            'wedding_date' => '2027-01-15',
            'wedding_time' => '20:00:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'is_rsvp_enabled' => false,
        ]);

        $noRsvpPayload = $service->buildPayload($rsvpDisabled);
        $this->assertFalse($noRsvpPayload['invitation']['allows_rsvp']);
        $this->assertStringNotContainsString('تأكيد الحضور', $noRsvpPayload['copy']['cta_badge']);

        $arDisabledText = $rsvpDisabled->getWhatsAppShareText('ar');
        $enDisabledText = $rsvpDisabled->getWhatsAppShareText('en');
        $this->assertStringNotContainsString('تأكيد الحضور', $arDisabledText);
        $this->assertStringContainsString('للاطلاع على تفاصيل الدعوة والموقع:', $arDisabledText);
        $this->assertStringContainsString($rsvpDisabled->public_url, $arDisabledText);
        $this->assertStringNotContainsString('❤️', $arDisabledText);
        $this->assertStringNotContainsString('confirm attendance', $enDisabledText);
        $this->assertStringContainsString('details & location', $enDisabledText);
    }

    public function test_all_14_templates_produce_valid_theme_tokens_and_render_share_studio(): void
    {
        $owner = User::factory()->create();
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $this->assertCount(14, $templates);

        $service = app(InvitationShareCardService::class);
        $this->actingAs($owner);

        foreach ($templates as $template) {
            $invitation = Invitation::create([
                'user_id' => $owner->id,
                'template_id' => $template->id,
                'title' => 'دعوة '.$template->name_ar,
                'slug' => 'share-theme-'.$template->slug,
                'groom_name' => 'عمر الفهد',
                'bride_name' => 'ريم السديري',
                'wedding_date' => '2027-02-10',
                'wedding_time' => '20:30:00',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الاحتفالات',
                'cover_photo' => 'https://untrusted.example.com/evil.jpg',
                'status' => 'published',
                'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
                'is_rsvp_enabled' => false,
                'custom_styles' => $template->default_styles ?? [],
            ]);

            $payload = $service->buildPayload($invitation);
            $this->assertSame(1080, $payload['dimensions']['width']);
            $this->assertSame(1350, $payload['dimensions']['height']);
            $this->assertSame('4:5', $payload['dimensions']['aspect_ratio']);
            $this->assertNull($payload['invitation']['cover_photo'], 'Untrusted cross-origin cover photo should be omitted to avoid canvas tainting.');
            $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $payload['theme']['primary_color']);
            $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $payload['theme']['card_bg']);

            $expectedDark = in_array($template->slug, ['emerald-night', 'desert-dusk'], true);
            $this->assertSame($expectedDark, $payload['theme']['is_dark_theme'], "Unexpected dark-theme flag for {$template->slug}");

            $this->get(route('dashboard.invitations.share', $invitation->id))
                ->assertOk()
                ->assertSee($template->name_ar);
        }
    }

    public function test_dashboard_invitations_and_editor_expose_share_card_links(): void
    {
        $owner = User::where('email', 'couple@zaffe.app')->firstOrFail();
        $invitation = Invitation::where('user_id', $owner->id)->firstOrFail();
        $shareUrl = route('dashboard.invitations.share', $invitation->id);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($shareUrl, false)
            ->assertSee('بطاقة واتساب');

        $this->actingAs($owner)
            ->get(route('dashboard.invitations.index'))
            ->assertOk()
            ->assertSee($shareUrl, false)
            ->assertSee('بطاقة واتساب');

        $this->actingAs($owner);
        Livewire::test(InvitationEditor::class, ['id' => $invitation->id])
            ->assertSee($shareUrl, false)
            ->assertSee('بطاقة واتساب');
    }
}
