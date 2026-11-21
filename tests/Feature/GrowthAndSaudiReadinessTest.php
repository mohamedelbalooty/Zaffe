<?php

namespace Tests\Feature;

use App\Filament\Pages\OperationsMonitor;
use App\Livewire\InvitationEditor;
use App\Livewire\RsvpForm;
use App\Models\FunnelDailyMetric;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use App\Support\TemplateStyleCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GrowthAndSaudiReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_stateless_saudi_demo_works_on_unseeded_database_with_absolute_og_image_and_no_db_writes(): void
    {
        $this->assertSame(0, InvitationTemplate::count());
        $this->assertSame(0, Invitation::count());

        $response = $this->get(route('demo.saudi'));

        $response->assertOk();
        $response->assertSee('فيصل');
        $response->assertSee('الجوهرة');
        $response->assertSee('property="og:image" content="'.asset('images/og-zaffe.jpg').'"', false);
        $response->assertSee('name="twitter:image" content="'.asset('images/og-zaffe.jpg').'"', false);
        $response->assertDontSee('إرسال تأكيد الحضور');

        $this->assertSame(0, Invitation::count());
        $this->assertFileExists(public_path('images/og-zaffe.jpg'));

        $ogInfo = @getimagesize(public_path('images/og-zaffe.jpg'));
        $this->assertIsArray($ogInfo);
        $this->assertSame(1200, $ogInfo[0]);
        $this->assertSame(630, $ogInfo[1]);
        $this->assertSame(IMAGETYPE_JPEG, $ogInfo[2]);
    }

    public function test_public_invitation_always_emits_absolute_og_image_url_with_fallback(): void
    {
        $couple = User::factory()->create(['role' => 'user']);
        $template = InvitationTemplate::create([
            'name_ar' => 'تراث الدرعية',
            'name_en' => 'Diriyah Heritage',
            'slug' => 'diriyah-heritage',
            'view_name' => 'templates.diriyah_heritage',
            'thumbnail' => '/images/templates/diriyah-heritage.webp',
            'is_active' => true,
            'is_premium' => false,
        ]);

        $invitation = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف بندر',
            'slug' => 'bandar-og-test',
            'groom_name' => 'بندر',
            'bride_name' => null,
            'wedding_date' => now()->addDays(25)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة طويق',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'cover_photo' => null,
        ]);

        $response = $this->get(route('invitation.show', $invitation->slug));
        $response->assertOk();
        $response->assertSee('property="og:image" content="'.url('/images/templates/diriyah-heritage.webp').'"', false);
    }

    public function test_c5_studio_editor_converts_uploads_to_webp_and_preserves_old_cover_on_failure(): void
    {
        Storage::fake('public');

        $couple = User::factory()->create(['role' => 'user']);
        $template = InvitationTemplate::create([
            'name_ar' => 'ورد الطائف',
            'name_en' => 'Taif Rose',
            'slug' => 'taif-rose',
            'view_name' => 'templates.taif_rose',
            'is_active' => true,
        ]);

        Storage::disk('public')->put('invitations/covers/legacy-cover.jpg', 'legacy-jpeg-content');

        $invitation = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف فهد',
            'slug' => 'fahad-webp-test',
            'groom_name' => 'فهد',
            'bride_name' => null,
            'wedding_date' => now()->addDays(30)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة الريتز',
            'status' => 'draft',
            'cover_photo' => '/storage/invitations/covers/legacy-cover.jpg',
        ]);

        // Bad/corrupt upload must reject and leave legacy cover intact on disk
        $badFile = UploadedFile::fake()->createWithContent('corrupt.jpg', 'not-an-image');
        Livewire::actingAs($couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('new_cover_photo', $badFile)
            ->assertHasErrors(['new_cover_photo']);

        Storage::disk('public')->assertExists('invitations/covers/legacy-cover.jpg');
        $this->assertSame('/storage/invitations/covers/legacy-cover.jpg', $invitation->fresh()->cover_photo);

        // Valid upload encodes to .webp, persists, and purges replaced cover after save
        $validCover = UploadedFile::fake()->image('valid-cover.jpg', 1800, 900);
        Livewire::actingAs($couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('new_cover_photo', $validCover)
            ->assertHasNoErrors();

        $invitation->refresh();
        $this->assertStringEndsWith('.webp', (string) $invitation->cover_photo);
        Storage::disk('public')->assertMissing('invitations/covers/legacy-cover.jpg');

        $validGallery = UploadedFile::fake()->image('valid-gallery.png', 1000, 800);
        Livewire::actingAs($couple)
            ->test(InvitationEditor::class, ['id' => $invitation->id])
            ->set('new_gallery_photo', $validGallery)
            ->assertHasNoErrors();

        $photo = $invitation->photos()->firstOrFail();
        $this->assertStringEndsWith('.webp', $photo->image_path);
    }

    public function test_c7_international_phone_supported_across_register_profile_and_rsvp(): void
    {
        // 1. Register with Other Country (+971...)
        $registerResponse = $this->post('/register', [
            'name' => 'سلطان القاسمي',
            'email' => 'sultan@example.com',
            'phone_country' => 'OTHER',
            'phone' => '+971501234567',
            'password' => 'SecurePass!2026',
            'password_confirmation' => 'SecurePass!2026',
        ]);

        $registerResponse->assertRedirect(route('dashboard.invitations.create'));
        $user = User::where('email', 'sultan@example.com')->firstOrFail();
        $this->assertSame('+971501234567', $user->phone);

        // 2. Profile edit with Other Country (+44...)
        $profileResponse = $this->actingAs($user)->put(route('dashboard.profile.update'), [
            'name' => 'سلطان القاسمي',
            'email' => 'sultan@example.com',
            'phone_country' => 'OTHER',
            'phone' => '00447911123456',
        ]);
        $profileResponse->assertSessionHas('success');
        $this->assertSame('+447911123456', $user->fresh()->phone);

        // 3. Profile rejects OTHER number without international prefix
        $badProfile = $this->actingAs($user)->put(route('dashboard.profile.update'), [
            'name' => 'سلطان القاسمي',
            'email' => 'sultan@example.com',
            'phone_country' => 'OTHER',
            'phone' => '501234567',
        ]);
        $badProfile->assertSessionHasErrors('phone');

        // 4. RSVP supports both OTHER international number and blank optional phone
        $template = InvitationTemplate::create([
            'name_ar' => 'سدو',
            'name_en' => 'Sadu Signature',
            'slug' => 'sadu-signature',
            'view_name' => 'templates.sadu_signature',
            'is_active' => true,
        ]);

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف سلطان',
            'slug' => 'sultan-rsvp-test',
            'groom_name' => 'سلطان',
            'bride_name' => null,
            'wedding_date' => now()->addDays(15)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة دبي',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
        ]);

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->assertSet('phone_country', 'SA')
            ->set('guest_name', 'خليفة المري')
            ->set('phone_country', 'OTHER')
            ->set('phone', '+971551234567')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'خليفة المري',
            'phone' => '+971551234567',
        ]);

        Livewire::test(RsvpForm::class, ['invitationId' => $invitation->id])
            ->set('guest_name', 'راشد آل مكتوم')
            ->set('phone', '')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name' => 'راشد آل مكتوم',
            'phone' => null,
        ]);
    }

    public function test_marketing_copy_contact_pricing_about_faq_and_dashboard_metrics_match_dec_04_and_07(): void
    {
        // /contact removes simulated form
        $contact = $this->get(route('contact'));
        $contact->assertOk();
        $contact->assertDontSee('أرسل لنا رسالة سريعة');
        $contact->assertDontSee('Send a Quick Note');

        // /pricing removes fake claims, reflects 14 themes & honest R1 copy
        $pricing = $this->get(route('pricing'));
        $pricing->assertOk();
        $pricing->assertSee('١٤');
        $pricing->assertDontSee('القوالب الستة');
        $pricing->assertDontSee('بطاقات QR غير محدودة');

        // /home links to stateless /demo
        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee(route('demo.saudi'));

        // /about and /faq load cleanly with updated messaging
        $this->get(route('about'))->assertOk()->assertSee('١٤ قالباً');
        $this->get(route('faq'))->assertOk()->assertSee('ببطاقة عرض');

        // Dashboard metrics sum guest_count + children_count and render archived badge
        $couple = User::factory()->create(['role' => 'user']);
        $template = InvitationTemplate::create([
            'name_ar' => 'ورد الطائف',
            'name_en' => 'Taif Rose',
            'slug' => 'taif-rose',
            'view_name' => 'templates.taif_rose',
            'is_active' => true,
        ]);

        $invitation = Invitation::create([
            'user_id' => $couple->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف معاذ',
            'slug' => 'muath-dash-test',
            'groom_name' => 'معاذ',
            'bride_name' => null,
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة النخيل',
            'status' => 'archived',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
        ]);

        RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'guest_name' => 'عائلة المطيري',
            'attendance_status' => 'attending',
            'guest_count' => 2,
            'children_count' => 3,
        ]);

        $dashboard = $this->actingAs($couple)->get(route('dashboard'));
        $dashboard->assertOk();
        $dashboard->assertViewHas('confirmedAttendees', 5);
        $dashboard->assertSee('مؤرشفة');
    }

    public function test_kpi_01_02_03b_and_05_attribution_work_and_render_in_operations_monitor(): void
    {
        // All 14 templates link footer to ?ref=invite
        foreach (TemplateStyleCatalog::bladeViews() as $view) {
            $slug = str_replace(['templates.', '_'], ['', '-'], $view);
            InvitationTemplate::firstOrCreate(
                ['slug' => $slug],
                ['name_ar' => $slug, 'name_en' => $slug, 'view_name' => $view, 'is_active' => true]
            );
        }

        // Visitor arrives from card footer (?ref=invite)
        $this->get(route('templates.index', ['ref' => 'invite']))
            ->assertOk()
            ->assertSessionHas('signup_source', 'invite_footer');

        // Repeat visit in same session/day is deduplicated
        $this->get(route('templates.index', ['ref' => 'invite']))->assertOk();
        $this->assertSame(1, FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_INVITE_FOOTER_VISIT));

        // Referred user registers -> stores invite_footer & clears session tag
        $this->post('/register', [
            'name' => 'نواف العتيبي',
            'email' => 'nawaf.ref@example.com',
            'phone_country' => 'SA',
            'phone' => '0509988776',
            'password' => 'StrongPass!123',
            'password_confirmation' => 'StrongPass!123',
        ])->assertRedirect(route('dashboard.invitations.create'));

        $referredUser = User::where('email', 'nawaf.ref@example.com')->firstOrFail();
        $this->assertSame('invite_footer', $referredUser->signup_source);

        // Visiting /create increments creation_start once per session/day
        $this->actingAs($referredUser)->get(route('dashboard.invitations.create'))->assertOk();
        $this->actingAs($referredUser)->get(route('dashboard.invitations.create'))->assertOk();
        $this->assertSame(1, FunnelDailyMetric::sumForKey(FunnelDailyMetric::KEY_CREATION_START));

        $template = InvitationTemplate::firstOrFail();

        // Referred user has 1 published invitation with RSVP enabled + 2 RSVPs, and 1 view-only published card
        $rsvpEnabledCard = Invitation::create([
            'user_id' => $referredUser->id,
            'template_id' => $template->id,
            'title' => 'حفل نواف 1',
            'slug' => 'nawaf-rsvp-enabled',
            'groom_name' => 'نواف',
            'bride_name' => null,
            'wedding_date' => now()->addDays(18)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة المملكة',
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_ONLY,
            'view_count' => 12,
        ]);

        Invitation::create([
            'user_id' => $referredUser->id,
            'template_id' => $template->id,
            'title' => 'حفل نواف 2',
            'slug' => 'nawaf-view-only',
            'groom_name' => 'نواف',
            'bride_name' => null,
            'wedding_date' => now()->addDays(20)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة الفيصلية',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'view_count' => 3,
        ]);

        // Invitation whose RSVP was turned OFF preserves DB responses but drops out of KPI-03b numerator & denominator
        $toggledOffCard = Invitation::create([
            'user_id' => $referredUser->id,
            'template_id' => $template->id,
            'title' => 'حفل نواف 3',
            'slug' => 'nawaf-rsvp-off',
            'groom_name' => 'نواف',
            'bride_name' => null,
            'wedding_date' => now()->addDays(22)->toDateString(),
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة نجد',
            'status' => 'published',
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'view_count' => 0,
        ]);

        RsvpResponse::create([
            'invitation_id' => $rsvpEnabledCard->id,
            'guest_name' => 'ضيف 1',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);
        RsvpResponse::create([
            'invitation_id' => $rsvpEnabledCard->id,
            'guest_name' => 'ضيف 2',
            'attendance_status' => 'declined',
            'guest_count' => 0,
        ]);
        RsvpResponse::create([
            'invitation_id' => $toggledOffCard->id,
            'guest_name' => 'ضيف محفوظ مسبقاً',
            'attendance_status' => 'attending',
            'guest_count' => 1,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $monitor = Livewire::actingAs($admin)->test(OperationsMonitor::class);
        $monitor->assertOk();

        $viewData = (new OperationsMonitor)->getViewData();
        $this->assertSame(1.0, $viewData['funnel']['avg_rsvps_per_published']); // 3 RSVPs across 3 published cards
        $this->assertSame(1, $viewData['kpi03b']['rsvp_enabled_published']);
        $this->assertSame(2, $viewData['kpi03b']['rsvps_on_enabled_published']);
        $this->assertSame(2.0, $viewData['kpi03b']['avg_rsvps_per_enabled_published']);
        $this->assertSame(33.3, $viewData['kpi03b']['rsvp_adoption_rate']);
        $this->assertSame(2, $viewData['kpi02']['shared_published_count']); // rsvpEnabledCard (12 views + RSVPs) & toggledOffCard (>=1 RSVP)

        $this->assertSame(1, $viewData['kpi05']['invite_footer_visits']);
        $this->assertSame(1, $viewData['kpi05']['referred_signups']);
        $this->assertSame(3, $viewData['kpi05']['referred_published_invitations']);
        $this->assertSame(1, $viewData['kpi05']['referred_activated_couples']);
    }
}
