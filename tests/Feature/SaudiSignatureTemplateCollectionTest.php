<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\InvitationTemplates\Pages\CreateInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\EditInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\ListInvitationTemplates;
use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use App\Services\InvitationDateFormatter;
use App\Support\TemplateStyleCatalog;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class SaudiSignatureTemplateCollectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_saudi_signature_eight_templates_exist_with_valid_catalog_metadata(): void
    {
        $allTemplates = InvitationTemplate::orderBy('sort_order')->get();
        $this->assertCount(14, $allTemplates);

        $expectedBaseline = [
            'royal-gold',
            'floral-romance',
            'minimal-chic',
            'islamic-elegance',
            'emerald-night',
            'classic-aristocrat',
        ];

        foreach ($expectedBaseline as $slug) {
            $this->assertDatabaseHas('invitation_templates', [
                'slug' => $slug,
                'is_active' => true,
                'is_premium' => false,
            ]);
        }

        $expectedNew = [
            'diriyah-heritage' => ['view' => 'templates.diriyah_heritage', 'category' => 'classic', 'name_ar' => 'تراث الدرعية'],
            'rawashin-elegance' => ['view' => 'templates.rawashin_elegance', 'category' => 'islamic', 'name_ar' => 'أناقة الرواشين'],
            'sadu-signature' => ['view' => 'templates.sadu_signature', 'category' => 'classic', 'name_ar' => 'نقوش السدو'],
            'taif-rose' => ['view' => 'templates.taif_rose', 'category' => 'floral', 'name_ar' => 'ورد الطائف'],
            'palm-oasis' => ['view' => 'templates.palm_oasis', 'category' => 'luxury', 'name_ar' => 'واحة النخيل'],
            'red-sea-pearl' => ['view' => 'templates.red_sea_pearl', 'category' => 'minimal', 'name_ar' => 'لؤلؤ البحر الأحمر'],
            'desert-dusk' => ['view' => 'templates.desert_dusk', 'category' => 'romantic', 'name_ar' => 'غروب الصحراء'],
            'modern-monogram' => ['view' => 'templates.modern_monogram', 'category' => 'minimal', 'name_ar' => 'المونوغرام العصري'],
        ];

        $this->assertSame(8, InvitationTemplate::whereIn('slug', array_keys($expectedNew))->count());

        foreach ($expectedNew as $slug => $meta) {
            $template = InvitationTemplate::with('category')->where('slug', $slug)->first();
            $this->assertNotNull($template, "Missing seeded template [{$slug}]");
            $this->assertSame($meta['name_ar'], $template->name_ar);
            $this->assertNotEmpty($template->name_en);
            $this->assertDoesNotMatchRegularExpression('/[A-Za-z()]/', $template->name_ar);
            $this->assertSame($meta['view'], $template->view_name);
            $this->assertContains($template->view_name, TemplateStyleCatalog::bladeViews());
            $this->assertSame($meta['category'], $template->category?->slug);
            $this->assertTrue($template->is_active);
            $this->assertFalse($template->is_premium);
            $this->assertSame("/images/templates/{$slug}.webp", $template->thumbnail);

            $absThumb = public_path("images/templates/{$slug}.webp");
            $this->assertFileExists($absThumb);
            $this->assertLessThanOrEqual(120 * 1024, (int) filesize($absThumb));
        }
    }

    public function test_gallery_collection_filter_and_search_showcase_saudi_signature_templates(): void
    {
        $collectionResponse = $this->get('/templates?collection=saudi-signature');
        $collectionResponse->assertOk();

        foreach (TemplateStyleCatalog::saudiSignatureSlugs() as $slug) {
            $tmpl = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $collectionResponse->assertSee($tmpl->name_ar);
            $collectionResponse->assertSee($tmpl->name_en);
        }

        $collectionResponse->assertDontSee('Royal Gold & Ivory');
        $collectionResponse->assertDontSee('/templates/royal-gold/preview');

        $searchAr = $this->get('/templates?search='.urlencode('الدرعية'));
        $searchAr->assertOk()->assertSee('تراث الدرعية');

        $searchEn = $this->get('/templates?search=Rawashin');
        $searchEn->assertOk()->assertSee('Rawashin Elegance');
    }

    public function test_each_new_template_preview_renders_without_mutating_invitations_and_invalid_returns_404(): void
    {
        $initialInvitationCount = Invitation::count();

        foreach (TemplateStyleCatalog::saudiSignatureSlugs() as $slug) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();

            $response = $this->get("/templates/{$slug}/preview");
            $response->assertOk();
            $response->assertSee($template->name_ar);
            $response->assertSee('فيصل بن عبد العزيز آل مقرن');
            $response->assertSee('الجوهرة بنت خالد السديري');
            $response->assertSee('قصر طويق للاحتفالات');
        }

        $this->assertSame($initialInvitationCount, Invitation::count());

        $this->get('/templates/non-existent-saudi-template/preview')->assertNotFound();

        $diriyah = InvitationTemplate::where('slug', 'diriyah-heritage')->firstOrFail();
        $diriyah->update(['is_active' => false]);
        $this->get('/templates/diriyah-heritage/preview')->assertNotFound();
    }

    public function test_full_invitation_lifecycle_works_for_every_new_saudi_signature_template(): void
    {
        $couple = User::where('email', 'couple@zaffe.app')->firstOrFail();

        foreach (TemplateStyleCatalog::saudiSignatureSlugs() as $index => $slug) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $groomName = 'طلال آل سعود '.($index + 1);
            $brideName = 'لولوة السديري '.($index + 1);

            $createResponse = $this->actingAs($couple)->post(route('dashboard.invitations.store'), [
                'template_id' => $template->id,
                'groom_name' => $groomName,
                'bride_name' => $brideName,
                'wedding_date' => '2027-04-20',
                'venue_name' => 'قصر طويق الرياض',
                'timezone' => 'Asia/Riyadh',
            ]);

            $invitation = Invitation::where('groom_name', $groomName)->firstOrFail();
            $createResponse->assertRedirect(route('dashboard.invitations.editor', $invitation->id));
            $this->assertSame('draft', $invitation->status);

            // Draft privacy enforced for public guests
            auth()->logout();
            $this->get(route('invitation.show', $invitation->slug))->assertForbidden();

            // Owner opens editor, edits venue, and publishes
            $editor = Livewire::actingAs($couple)
                ->test(InvitationEditor::class, ['id' => $invitation->id])
                ->assertOk()
                ->set('venue_name', 'قاعة نيارة للاحتفالات')
                ->call('saveChanges')
                ->call('togglePublish');

            $this->assertSame('published', $invitation->fresh()->status);

            // Public rendering uses exact template Blade view and displays dynamic data
            auth()->logout();
            $publicResponse = $this->get(route('invitation.show', $invitation->slug));
            $publicResponse->assertOk();
            $publicResponse->assertViewIs('pages.invitation-show');
            $publicResponse->assertViewHas('viewName', $template->view_name);
            $publicResponse->assertSee($groomName);
            $publicResponse->assertSee($brideName);
            $publicResponse->assertSee('قاعة نيارة للاحتفالات');

            // Unpublishing restores draft privacy
            Livewire::actingAs($couple)
                ->test(InvitationEditor::class, ['id' => $invitation->id])
                ->call('togglePublish');

            $this->assertSame('draft', $invitation->fresh()->status);
            auth()->logout();
            $this->get(route('invitation.show', $invitation->slug))->assertForbidden();
        }
    }

    public function test_new_templates_support_hijri_timezones_locales_optional_bride_photos_and_f16_modes(): void
    {
        $couple = User::where('email', 'couple@zaffe.app')->firstOrFail();
        $slugs = TemplateStyleCatalog::saudiSignatureSlugs();

        // 1. Hijri + Gregorian + Arabic-Indic digits + Asia/Riyadh + photo-free + optional blank bride_name
        $diriyah = InvitationTemplate::where('slug', 'diriyah-heritage')->firstOrFail();
        $kunyaInv = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $diriyah->id,
            'title' => 'حفل زفاف فهد آل مقرن',
            'slug' => 'fahd-saudi-test',
            'groom_name' => 'فهد بن عبد الله آل مقرن',
            'bride_name' => '',
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:30:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الثقافة بالرياض',
            'cover_photo' => null,
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_RSVP_QR,
            'is_rsvp_enabled' => true,
            'custom_styles' => array_merge($diriyah->default_styles, [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
                'show_hijri' => true,
            ]),
        ]);

        InvitationDateFormatter::withHijriResolver(
            fn () => '4 رجب 1448 هـ',
            function () use ($kunyaInv): void {
                $response = $this->get(route('invitation.show', $kunyaInv->slug));
                $response->assertOk();
                $response->assertSee('فهد بن عبد الله آل مقرن');
                $response->assertSee('الاثنين، ١٤ ديسمبر ٢٠٢٦');
                $response->assertSee('٤ رجب ١٤٤٨ هـ');
                $response->assertSee('٨:٣٠ مساءً');
                $response->assertSee('+03:00');
                $response->assertDontSee('<img', false);
            }
        );

        // 2. English LTR + Africa/Cairo + latin digits on Red Sea Pearl
        $pearl = InvitationTemplate::where('slug', 'red-sea-pearl')->firstOrFail();
        $enInv = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $pearl->id,
            'title' => 'Tariq & Salma Wedding',
            'slug' => 'tariq-salma-en-test',
            'groom_name' => 'Tariq El-Masry',
            'bride_name' => 'Salma Nabil',
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00:00',
            'timezone' => 'Africa/Cairo',
            'venue_name' => 'Nile Ritz-Carlton Cairo',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            'is_rsvp_enabled' => true,
            'custom_styles' => array_merge($pearl->default_styles, [
                'locale' => 'en',
                'digits' => 'latin',
            ]),
        ]);

        $enResponse = $this->get(route('invitation.show', $enInv->slug));
        $enResponse->assertOk();
        $enResponse->assertSee('dir="ltr"', false);
        $enResponse->assertSee('<bdi dir="ltr">Monday, 14 December 2026</bdi>', false);
        $enResponse->assertSee('<bdi dir="ltr">At 8:00 PM</bdi>', false);

        // 3. Verify all 4 F16 Part A invitation modes & personalized ?g= links + /pass/{qrToken}
        $modes = [
            ['slug' => $slugs[0], 'mode' => Invitation::MODE_RSVP_QR, 'hasRsvp' => true, 'hasQr' => true],
            ['slug' => $slugs[3], 'mode' => Invitation::MODE_RSVP_ONLY, 'hasRsvp' => true, 'hasQr' => false],
            ['slug' => $slugs[4], 'mode' => Invitation::MODE_QR_ONLY, 'hasRsvp' => false, 'hasQr' => true],
            ['slug' => $slugs[7], 'mode' => Invitation::MODE_ANNOUNCEMENT, 'hasRsvp' => false, 'hasQr' => false],
        ];

        foreach ($modes as $cfg) {
            $tmpl = InvitationTemplate::where('slug', $cfg['slug'])->firstOrFail();
            $inv = Invitation::create([
                'user_id' => $couple->id,
                'template_id' => $tmpl->id,
                'title' => 'وضع '.$cfg['mode'],
                'slug' => 'mode-check-'.$cfg['slug'],
                'groom_name' => 'سلمان الدوسري',
                'bride_name' => 'نوف القحطاني',
                'wedding_date' => now()->addDays(25)->toDateString(),
                'wedding_time' => '21:00:00',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الفيصلية',
                'status' => 'published',
                'is_rsvp_enabled' => $cfg['hasRsvp'],
                'invitation_mode' => $cfg['mode'],
            ]);

            $guest = InvitationGuest::create([
                'invitation_id' => $inv->id,
                'name' => 'الشيخ ناصر المطيري',
                'phone' => '+966551234567',
                'category' => InvitationGuest::CATEGORY_VIP,
                'max_adults' => 2,
                'max_children' => 1,
                'rsvp_status' => $cfg['mode'] === Invitation::MODE_RSVP_QR
                    ? InvitationGuest::STATUS_ATTENDING
                    : InvitationGuest::STATUS_PENDING,
                'confirmed_adults' => $cfg['mode'] === Invitation::MODE_RSVP_QR ? 2 : 0,
            ]);

            $guestPage = $this->get(route('invitation.show', $inv->slug).'?g='.$guest->invite_token);
            $guestPage->assertOk();
            $guestPage->assertSee('الشيخ ناصر المطيري');

            if ($cfg['hasRsvp']) {
                $guestPage->assertSeeLivewire(RsvpForm::class);
            } else {
                $guestPage->assertDontSeeLivewire(RsvpForm::class);
            }

            $passResponse = $this->get(route('guest.pass', $guest->qr_token));
            if ($cfg['hasQr']) {
                $passResponse->assertOk();
                $passResponse->assertSee($guest->pass_code);
                $passResponse->assertSee('<svg', false);
            } else {
                $passResponse->assertNotFound();
            }
        }
    }

    public function test_admin_can_manage_saudi_signature_templates_and_invalid_view_names_are_rejected(): void
    {
        $admin = User::where('email', 'admin@zaffe.app')->firstOrFail();
        $couple = User::where('email', 'couple@zaffe.app')->firstOrFail();
        $category = TemplateCategory::firstOrFail();

        $this->actingAs($couple)->get('/admin/invitation-templates')->assertForbidden();

        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        $diriyah = InvitationTemplate::where('slug', 'diriyah-heritage')->firstOrFail();

        // Reject untrusted / non-allowlisted Blade view path
        Livewire::test(CreateInvitationTemplate::class)
            ->fillForm([
                'category_id' => $category->id,
                'name_ar' => 'قالب غير صالح',
                'name_en' => 'Invalid View Template',
                'slug' => 'invalid-view-template',
                'view_name' => 'pages.home',
                'sort_order' => 99,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['view_name']);

        $this->assertDatabaseMissing('invitation_templates', ['slug' => 'invalid-view-template']);

        // Safe fallback if untrusted view_name somehow exists on model
        $this->assertSame('templates.royal_gold', TemplateStyleCatalog::resolveBladeView('pages.home'));
        $this->assertSame('templates.diriyah_heritage', TemplateStyleCatalog::resolveBladeView('templates.diriyah_heritage'));

        // Apply new Saudi Signature preset in Filament editor & verify Release 1 free enforcement
        Livewire::test(EditInvitationTemplate::class, ['record' => $diriyah->id])
            ->callAction(TestAction::make('apply_preset_sadu_signature')->schemaComponent('default_styles_section'))
            ->assertSchemaStateSet([
                'default_styles.primary_color' => '#7A1F2B',
                'default_styles.border_style' => 'sadu-woven-band',
                'default_styles.ornament_style' => 'sadu_lozenge',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $diriyah->refresh();
        $this->assertFalse($diriyah->is_premium);
        $this->assertSame('#7A1F2B', $diriyah->default_styles['primary_color']);

        // Deactivate via Filament Admin -> immediately reflected on customer catalog & preview
        Livewire::test(ListInvitationTemplates::class)
            ->callTableAction('toggleActive', $diriyah);

        $this->assertFalse($diriyah->fresh()->is_active);
        auth()->logout();
        $this->get('/templates')->assertOk()->assertDontSee('تراث الدرعية');
        $this->get('/templates/diriyah-heritage/preview')->assertNotFound();
    }

    public function test_taif_rose_couture_template_renders_side_by_side_couple_triad_calligraphy_and_ceremony_timeline(): void
    {
        $taifRose = InvitationTemplate::where('slug', 'taif-rose')->firstOrFail();
        $this->assertSame(1, $taifRose->sort_order);
        $this->assertSame('#8E4A64', $taifRose->default_styles['primary_color']);

        // 1. Preview renders side-by-side split wings, contiguous full names, calligraphic blessing & 3-stage ceremony bar
        $preview = $this->get('/templates/taif-rose/preview');
        $preview->assertOk();
        $preview->assertSee('بارك الله لهما وبارك عليهما وجمع بينهما في خير');
        $preview->assertSee('الجوهرة');
        $preview->assertSee('بنت خالد السديري');
        $preview->assertSee('فيصل');
        $preview->assertSee('بن عبد العزيز آل مقرن');
        $preview->assertSee('فيصل بن عبد العزيز آل مقرن');
        $preview->assertSee('الجوهرة بنت خالد السديري');
        $preview->assertSee('الاستقبال');
        $preview->assertSee('الزفّــة');
        $preview->assertSee('العشــاء');
        $preview->assertSee('<svg', false);
        $preview->assertDontSee('<img', false);

        // 2. Single-name / blank bride_name kunya mode omits orphan bride wing & renders centered kashida layout
        $couple = User::where('email', 'couple@zaffe.app')->firstOrFail();
        $kunyaInv = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $taifRose->id,
            'title' => 'حفل زفاف سلمان آل سعود',
            'slug' => 'salman-taif-kunya-test',
            'groom_name' => 'سلمان بن فهد آل سعود',
            'bride_name' => '',
            'wedding_date' => '2027-02-18',
            'wedding_time' => '21:00:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الفيصلية بالرياض',
            'cover_photo' => null,
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            'is_rsvp_enabled' => true,
            'custom_styles' => array_merge($taifRose->default_styles, [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
                'show_hijri' => true,
            ]),
        ]);

        $response = $this->get(route('invitation.show', $kunyaInv->slug));
        $response->assertOk();
        $response->assertSee('سلمان');
        $response->assertSee('بن فهد آل سعود');
        $response->assertSee('سلمان بن فهد آل سعود');
        $response->assertSee('العريس');
        $response->assertDontSee('العروس');
        $response->assertSee('٩:٠٠ مساءً');
        $response->assertSee('١٠:٣٠ مساءً');
        $response->assertDontSee('<img', false);
    }

    public function test_all_eight_saudi_signature_templates_render_couture_triad_calligraphy_and_ceremony_schedule(): void
    {
        $expectedOrder = [
            'taif-rose' => 1,
            'diriyah-heritage' => 2,
            'rawashin-elegance' => 3,
            'sadu-signature' => 4,
            'palm-oasis' => 5,
            'red-sea-pearl' => 6,
            'desert-dusk' => 7,
            'modern-monogram' => 8,
        ];

        foreach ($expectedOrder as $slug => $sortOrder) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $this->assertSame($sortOrder, $template->sort_order, "Unexpected sort_order for [{$slug}]");

            $preview = $this->get("/templates/{$slug}/preview");
            $preview->assertOk();
            $preview->assertSee('بارك الله لهما وبارك عليهما وجمع بينهما في خير');
            $preview->assertSee('الجوهرة');
            $preview->assertSee('بنت خالد السديري');
            $preview->assertSee('فيصل');
            $preview->assertSee('بن عبد العزيز آل مقرن');
            $preview->assertSee('فيصل بن عبد العزيز آل مقرن');
            $preview->assertSee('الجوهرة بنت خالد السديري');
            $preview->assertSee('الاستقبال');
            $preview->assertSee('الزفّــة');
            $preview->assertSee('العشــاء');
            $preview->assertSee('<svg', false);
            $preview->assertDontSee('<img', false);
        }
    }

    public function test_all_template_views_are_free_of_external_image_urls_and_unicode_emojis(): void
    {
        $emojiRegex = '/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u';

        foreach (File::allFiles(resource_path('views/templates')) as $file) {
            $content = (string) file_get_contents($file->getPathname());
            $this->assertDoesNotMatchRegularExpression($emojiRegex, $content, "Emoji detected in {$file->getRelativePathname()}");
            $this->assertDoesNotMatchRegularExpression('#https?://(?!maps\.google)#i', $content, "External URL detected in {$file->getRelativePathname()}");
        }
    }

    public function test_saudi_signature_sync_migration_upgrades_legacy_six_template_database_idempotently(): void
    {
        $baselineOrder = [
            'royal-gold' => 1,
            'floral-romance' => 2,
            'minimal-chic' => 3,
            'islamic-elegance' => 4,
            'emerald-night' => 5,
            'classic-aristocrat' => 6,
        ];

        InvitationTemplate::whereIn('slug', TemplateStyleCatalog::saudiSignatureSlugs())->delete();
        foreach ($baselineOrder as $slug => $order) {
            InvitationTemplate::where('slug', $slug)->update(['sort_order' => $order]);
        }

        $this->assertSame(6, InvitationTemplate::count());
        $this->get('/templates/taif-rose/preview')->assertNotFound();
        $this->assertDatabaseHas('invitations', ['slug' => 'ahmed-and-sarah']);

        $migration = require database_path('migrations/2026_11_19_000001_sync_saudi_signature_vol1_templates.php');
        $migration->up();

        $this->assertSame(14, InvitationTemplate::count());

        $expectedSaudiOrder = [
            'taif-rose' => 1,
            'diriyah-heritage' => 2,
            'rawashin-elegance' => 3,
            'sadu-signature' => 4,
            'palm-oasis' => 5,
            'red-sea-pearl' => 6,
            'desert-dusk' => 7,
            'modern-monogram' => 8,
        ];

        foreach ($expectedSaudiOrder as $slug => $sortOrder) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $this->assertSame($sortOrder, $template->sort_order);
            $this->assertTrue($template->is_active);
            $this->assertFalse($template->is_premium);
        }

        $taifRose = InvitationTemplate::where('slug', 'taif-rose')->firstOrFail();
        $this->assertSame('#8E4A64', $taifRose->default_styles['primary_color']);

        $expectedBaselineAfter = [
            'royal-gold' => 9,
            'floral-romance' => 10,
            'minimal-chic' => 11,
            'islamic-elegance' => 12,
            'emerald-night' => 13,
            'classic-aristocrat' => 14,
        ];

        foreach ($expectedBaselineAfter as $slug => $sortOrder) {
            $this->assertSame($sortOrder, InvitationTemplate::where('slug', $slug)->firstOrFail()->sort_order);
        }

        $this->assertDatabaseHas('invitations', ['slug' => 'ahmed-and-sarah']);

        $homeResponse = $this->get('/');
        $homeResponse->assertOk();
        $homeResponse->assertSee('ورد الطائف');
        $homeResponse->assertSee('تراث الدرعية');

        $collectionResponse = $this->get('/templates?collection=saudi-signature');
        $collectionResponse->assertOk();
        foreach (array_keys($expectedSaudiOrder) as $slug) {
            $collectionResponse->assertSee(InvitationTemplate::where('slug', $slug)->firstOrFail()->name_ar);
        }

        $this->get('/templates/taif-rose/preview')->assertOk();

        // Idempotency + admin active toggle preservation
        $diriyah = InvitationTemplate::where('slug', 'diriyah-heritage')->firstOrFail();
        $diriyah->update(['is_active' => false]);

        $migration->up();

        $this->assertSame(14, InvitationTemplate::count());
        $this->assertFalse($diriyah->fresh()->is_active);

        // Fresh/empty DB guard: does not seed rows when baseline catalog is absent
        Invitation::query()->delete();
        InvitationTemplate::query()->delete();

        $migration->up();

        $this->assertSame(0, InvitationTemplate::count());
    }
}
