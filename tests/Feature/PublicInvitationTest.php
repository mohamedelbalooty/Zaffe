<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PublicInvitationTest extends TestCase
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
            'default_styles' => [
                'primary_color' => '#C6A76B',
                'secondary_color' => '#292521',
                'background_color' => '#FAF7F2',
                'font_family' => 'Tajawal',
            ],
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->owner->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف كريم ومريم',
            'slug' => 'karim-mariam-wedding',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'venue_name' => 'فندق الفورسيزونز نايل بلازا',
            'venue_address' => 'كورنيش النيل، جاردن سيتي',
            'status' => 'published',
            'view_count' => 0,
        ]);

        InvitationSection::create([
            'invitation_id' => $this->invitation->id,
            'section_type' => 'countdown',
            'title_ar' => 'العد التنازلي',
            'content' => ['target_date' => $this->invitation->wedding_date->format('Y-m-d')],
            'sort_order' => 1,
            'is_enabled' => true,
        ]);
    }

    public function test_public_invitation_renders_successfully_for_guest(): void
    {
        $response = $this->get(route('invitation.show', $this->invitation->slug));

        $response->assertOk();
        $response->assertSee('كريم');
        $response->assertSee('مريم');
        $response->assertSee('فندق الفورسيزونز نايل بلازا');
    }

    public function test_view_count_increments_only_once_per_session(): void
    {
        $this->assertSame(0, $this->invitation->fresh()->view_count);

        // First visit
        $this->get(route('invitation.show', $this->invitation->slug));
        $this->assertSame(1, $this->invitation->fresh()->view_count);

        // Second visit in the same session
        $this->get(route('invitation.show', $this->invitation->slug));
        $this->assertSame(1, $this->invitation->fresh()->view_count);

        // Third visit in the same session
        $this->get(route('invitation.show', $this->invitation->slug));
        $this->assertSame(1, $this->invitation->fresh()->view_count);
    }

    public function test_opengraph_tags_are_rendered_in_html(): void
    {
        $response = $this->get(route('invitation.show', $this->invitation->slug));

        $response->assertOk();
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    public function test_countdown_uses_event_timezone_in_iso_string(): void
    {
        $this->invitation->update([
            'timezone' => 'Asia/Riyadh',
            'wedding_time' => '21:00',
        ]);

        $response = $this->get(route('invitation.show', $this->invitation->slug));
        $response->assertOk();
        // Asia/Riyadh is UTC+3 (+03:00)
        $response->assertSee('+03:00');
    }

    public function test_draft_invitation_denies_guest_and_permits_owner(): void
    {
        $this->invitation->update(['status' => 'draft']);

        // Guest gets 403
        $guestResponse = $this->get(route('invitation.show', $this->invitation->slug));
        $guestResponse->assertStatus(403);

        // Owner gets 200 with draft alert banner
        $ownerResponse = $this->actingAs($this->owner)->get(route('invitation.show', $this->invitation->slug));
        $ownerResponse->assertOk();
        $ownerResponse->assertSee('مسودة خاصة');
    }

    public function test_all_six_templates_render_cleanly_on_public_page(): void
    {
        $templates = [
            ['slug' => 'floral-romance', 'view_name' => 'templates.floral_romance', 'name_ar' => 'الرومانسية الزهرية'],
            ['slug' => 'minimal-chic', 'view_name' => 'templates.minimal_chic', 'name_ar' => 'الأناقة البسيطة'],
            ['slug' => 'islamic-elegance', 'view_name' => 'templates.islamic_elegance', 'name_ar' => 'الأصالة الإسلامية'],
            ['slug' => 'emerald-night', 'view_name' => 'templates.emerald_night', 'name_ar' => 'الزمرد الليلي'],
            ['slug' => 'classic-aristocrat', 'view_name' => 'templates.classic_aristocrat', 'name_ar' => 'الأرستقراطي الكلاسيكي'],
        ];

        foreach ($templates as $t) {
            $tmpl = InvitationTemplate::create([
                'name_ar' => $t['name_ar'],
                'name_en' => $t['slug'],
                'slug' => $t['slug'],
                'view_name' => $t['view_name'],
                'category' => 'classic',
                'is_premium' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]);

            $this->invitation->update([
                'template_id' => $tmpl->id,
                'wedding_date' => '2026-12-14',
                'wedding_time' => '20:00',
            ]);

            $response = $this->get(route('invitation.show', $this->invitation->slug));
            $response->assertOk();
            $response->assertSee('الاثنين، 14 ديسمبر 2026');
            $response->assertSee('8:00 مساءً');
            $response->assertSee('<bdi', false);
        }
    }

    public function test_public_invitation_renders_arabic_date_and_time_with_bidi_regardless_of_session_locale(): void
    {
        $this->invitation->update([
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => ['locale' => 'ar'],
        ]);

        $response = $this->withSession(['locale' => 'en'])
            ->get(route('invitation.show', $this->invitation->slug));

        $response->assertOk();
        $response->assertSee('<bdi dir="rtl">الاثنين، 14 ديسمبر 2026</bdi>', false);
        $response->assertSee('<bdi dir="rtl">8:00 مساءً</bdi>', false);
        $response->assertDontSee('Monday, 14 December 2026');
        $response->assertDontSee('08:00 PM');
    }

    public function test_public_invitation_respects_arabic_indic_digits_setting(): void
    {
        $this->invitation->update([
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ],
        ]);

        $response = $this->get(route('invitation.show', $this->invitation->slug));

        $response->assertOk();
        $response->assertSee('الاثنين، ١٤ ديسمبر ٢٠٢٦');
        $response->assertSee('٨:٠٠ مساءً');
    }

    public function test_public_invitation_respects_invitation_english_locale_setting(): void
    {
        $this->invitation->update([
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'custom_styles' => [
                'locale' => 'en',
                'digits' => 'latin',
            ],
        ]);

        $response = $this->withSession(['locale' => 'ar'])
            ->get(route('invitation.show', $this->invitation->slug));

        $response->assertOk();
        $response->assertSee('<bdi dir="ltr">Monday, 14 December 2026</bdi>', false);
        $response->assertSee('<bdi dir="ltr">At 8:00 PM</bdi>', false);
    }

    public function test_editor_live_preview_formats_date_and_time_by_invitation_settings(): void
    {
        $this->invitation->update([
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
        ]);

        Livewire::actingAs($this->owner)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('invitation_locale', 'ar')
            ->set('digits', 'arabic_indic')
            ->assertSee('الاثنين، ١٤ ديسمبر ٢٠٢٦')
            ->assertSee('٨:٠٠ مساءً')
            ->set('invitation_locale', 'en')
            ->set('digits', 'latin')
            ->assertSee('Monday, 14 December 2026')
            ->assertSee('At 8:00 PM');
    }

    public function test_new_code_route_and_legacy_slug_route_both_render_successfully(): void
    {
        $shortInvitation = Invitation::create([
            'user_id' => $this->owner->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف طارق ولينا',
            'groom_name' => 'طارق',
            'bride_name' => 'لينا',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة',
            'status' => 'published',
        ]);

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{7,8}$/', $shortInvitation->slug);

        // New canonical /i/{code} route
        $codeResponse = $this->get('/i/' . $shortInvitation->slug);
        $codeResponse->assertOk();
        $codeResponse->assertSee('طارق');
        $codeResponse->assertSee('لينا');
        $codeResponse->assertSee($shortInvitation->public_url, false);
        $codeResponse->assertSee($shortInvitation->getWhatsAppShareUrl(), false);

        // Existing legacy /invitation/{slug} route remains backward-compatible
        $legacyResponse = $this->get('/invitation/' . $this->invitation->slug);
        $legacyResponse->assertOk();
        $legacyResponse->assertSee('كريم');
        $legacyResponse->assertSee('مريم');
        $legacyResponse->assertSee($this->invitation->public_url, false);
    }

    public function test_opengraph_and_twitter_tags_include_couple_names_and_fallback_to_template_thumbnail(): void
    {
        $this->template->update(['thumbnail' => '/images/templates/royal-gold.webp']);
        $this->invitation->update([
            'title' => 'ليلة العمر',
            'cover_photo' => null,
            'invitation_message' => 'يسرنا دعوتكم لحفلنا السعيد في القاهرة',
        ]);

        $response = $this->get('/i/' . $this->invitation->slug);
        $response->assertOk();

        $expectedTitle = 'حفل زفاف كريم و مريم (ليلة العمر) — زفّة';
        $expectedImage = url('/images/templates/royal-gold.webp');

        $response->assertSee('<meta property="og:title" content="' . $expectedTitle . '">', false);
        $response->assertSee('<meta name="twitter:title" content="' . $expectedTitle . '">', false);
        $response->assertSee('<meta property="og:description" content="يسرنا دعوتكم لحفلنا السعيد في القاهرة">', false);
        $response->assertSee('<meta name="twitter:description" content="يسرنا دعوتكم لحفلنا السعيد في القاهرة">', false);
        $response->assertSee('<meta property="og:url" content="' . $this->invitation->public_url . '">', false);
        $response->assertSee('<meta property="og:image" content="' . $expectedImage . '">', false);
        $response->assertSee('<meta name="twitter:image" content="' . $expectedImage . '">', false);
    }

    public function test_opengraph_image_resizes_cover_photo_to_1200x630_under_300kb_and_caches_on_disk(): void
    {
        Storage::fake('public');

        $coverPath = UploadedFile::fake()->image('cover.png', 1600, 1200)->store('invitations/covers', 'public');
        $this->invitation->update([
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        $firstResponse = $this->get('/i/' . $this->invitation->slug);
        $firstResponse->assertOk();

        $cachedFiles = Storage::disk('public')->files('invitations/og');
        $this->assertCount(1, $cachedFiles);
        $cachedFile = $cachedFiles[0];
        $this->assertStringStartsWith("invitations/og/inv_{$this->invitation->id}_", $cachedFile);

        $bytes = Storage::disk('public')->get($cachedFile);
        $this->assertGreaterThan(0, strlen($bytes));
        $this->assertLessThanOrEqual(300 * 1024, strlen($bytes));

        $info = getimagesizefromstring($bytes);
        $this->assertIsArray($info);
        $this->assertSame(1200, $info[0]);
        $this->assertSame(630, $info[1]);
        $this->assertContains($info['mime'], ['image/webp', 'image/jpeg']);

        $expectedOgImageUrl = url(Storage::disk('public')->url($cachedFile));
        $firstResponse->assertSee('<meta property="og:image" content="' . $expectedOgImageUrl . '">', false);
        $firstResponse->assertSee('<meta name="twitter:image" content="' . $expectedOgImageUrl . '">', false);
        $firstResponse->assertSee('<meta property="og:image:width" content="1200">', false);
        $firstResponse->assertSee('<meta property="og:image:height" content="630">', false);

        // Second request reuses cached file on disk without duplicating files
        $secondResponse = $this->get('/i/' . $this->invitation->slug);
        $secondResponse->assertOk();
        $this->assertSame([$cachedFile], Storage::disk('public')->files('invitations/og'));
    }

    public function test_draft_invitation_never_leaks_og_data_on_code_or_legacy_routes(): void
    {
        $this->invitation->update([
            'status' => 'draft',
            'title' => 'زفاف خاص سري جداً',
            'groom_name' => 'سلطان القحطاني',
            'bride_name' => 'العنود الدوسري',
            'invitation_message' => 'رسالة خاصة جداً لا يجب تسريبها للعامة',
            'cover_photo' => '/storage/invitations/covers/secret-cover-xyz.png',
        ]);

        foreach (['/i/' . $this->invitation->slug, '/invitation/' . $this->invitation->slug] as $url) {
            $response = $this->get($url);
            $response->assertStatus(403);
            $response->assertDontSee('سلطان القحطاني');
            $response->assertDontSee('العنود الدوسري');
            $response->assertDontSee('زفاف خاص سري جداً');
            $response->assertDontSee('رسالة خاصة جداً لا يجب تسريبها للعامة');
            $response->assertDontSee('secret-cover-xyz.png');
        }
    }
}
