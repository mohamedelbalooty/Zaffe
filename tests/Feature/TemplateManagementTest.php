<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_all_six_templates_are_active_and_discoverable_in_catalog(): void
    {
        $response = $this->get('/templates');
        $response->assertStatus(200);

        $templates = InvitationTemplate::where('is_active', true)->get();
        $this->assertCount(6, $templates);

        foreach ($templates as $template) {
            $response->assertSee($template->name_ar);
        }
    }

    public function test_template_catalog_filters_by_category(): void
    {
        $response = $this->get('/templates?category=luxury');
        $response->assertStatus(200);
        $response->assertSee('الفخامة الذهبية');
    }

    public function test_template_catalog_searches_by_arabic_and_english(): void
    {
        $arResponse = $this->get('/templates?search=' . urlencode('الزهور'));
        $arResponse->assertStatus(200);
        $arResponse->assertSee('سحر الزهور');

        $enResponse = $this->get('/templates?search=Royal');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Royal Gold');
    }

    public function test_active_template_preview_returns_200(): void
    {
        $template = InvitationTemplate::where('slug', 'royal-gold')->first();
        $response = $this->get("/templates/{$template->slug}/preview");
        $response->assertStatus(200);
        $response->assertSee('معاينة');
    }

    public function test_inactive_template_preview_returns_404(): void
    {
        $template = InvitationTemplate::where('slug', 'royal-gold')->first();
        $template->update(['is_active' => false]);

        $response = $this->get("/templates/{$template->slug}/preview");
        $response->assertStatus(404);
    }

    public function test_invitation_creation_wizard_hides_inactive_templates(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $deactivatedTemplate = InvitationTemplate::where('slug', 'minimal-chic')->first();
        $deactivatedTemplate->update(['is_active' => false]);

        $response = $this->actingAs($user)->get('/dashboard/invitations/create');
        $response->assertStatus(200);
        $response->assertDontSee($deactivatedTemplate->name_ar);
    }

    public function test_invitation_creation_rejects_inactive_template_id(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();
        $inactive = InvitationTemplate::where('slug', 'classic-aristocrat')->first();
        $inactive->update(['is_active' => false]);

        $response = $this->actingAs($user)->post('/dashboard/invitations', [
            'template_id' => $inactive->id,
            'groom_name' => 'عمر الشريف',
            'bride_name' => 'فاتن حمامة',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'venue_name' => 'قصر النيل',
        ]);

        $response->assertSessionHasErrors(['template_id']);
        $this->assertDatabaseMissing('invitations', [
            'groom_name' => 'عمر الشريف',
            'template_id' => $inactive->id,
        ]);
    }

    public function test_editor_template_switcher_rejects_inactive_template(): void
    {
        $owner = User::where('email', 'couple@zaffe.app')->first();
        $invitation = Invitation::where('user_id', $owner->id)->first();
        $originalTemplateId = $invitation->template_id;

        $inactive = InvitationTemplate::where('id', '!=', $originalTemplateId)->first();
        $inactive->update(['is_active' => false]);

        $test = Livewire::actingAs($owner)
            ->test(InvitationEditor::class, ['id' => $invitation->id]);

        try {
            $test->call('changeTemplate', $inactive->id);
        } catch (\Throwable $e) {
            // caught
        }

        $invitation->refresh();
        $this->assertEquals($originalTemplateId, $invitation->template_id);
    }

    public function test_template_thumbnails_are_local_webp_files_with_no_external_urls(): void
    {
        $templates = InvitationTemplate::orderBy('sort_order')->get();
        $this->assertCount(6, $templates);

        foreach ($templates as $template) {
            $expectedPath = "/images/templates/{$template->slug}.webp";
            $this->assertSame($expectedPath, $template->thumbnail);
            $this->assertDoesNotMatchRegularExpression('#^https?://#i', (string) $template->thumbnail);

            $absolutePath = public_path(ltrim($expectedPath, '/'));
            $this->assertFileExists($absolutePath);

            $fileSize = filesize($absolutePath);
            $this->assertIsInt($fileSize);
            $this->assertGreaterThan(0, $fileSize);
            $this->assertLessThanOrEqual(120 * 1024, $fileSize);
        }
    }

    public function test_template_names_are_arabic_first_with_english_secondary_label_on_catalog_and_create_pages(): void
    {
        $templates = InvitationTemplate::where('is_active', true)->get();
        $user = User::where('email', 'couple@zaffe.app')->first();

        $catalogResponse = $this->get('/templates');
        $catalogResponse->assertOk();

        $createResponse = $this->actingAs($user)->get('/dashboard/invitations/create');
        $createResponse->assertOk();

        foreach ($templates as $template) {
            $this->assertDoesNotMatchRegularExpression('/[A-Za-z()]/', $template->name_ar);
            $this->assertNotEmpty($template->name_en);

            $catalogResponse->assertSee($template->name_ar);
            $catalogResponse->assertSee($template->name_en);

            $createResponse->assertSee($template->name_ar);
            $createResponse->assertSee($template->name_en);
        }
    }

    public function test_template_thumbnails_render_graceful_fallback_without_raw_alt_text(): void
    {
        $user = User::where('email', 'couple@zaffe.app')->first();

        $floral = InvitationTemplate::where('slug', 'floral-romance')->firstOrFail();
        $floral->update(['thumbnail' => '/images/templates/missing-preview.webp']);

        $minimal = InvitationTemplate::where('slug', 'minimal-chic')->firstOrFail();
        $minimal->update(['thumbnail' => null]);

        foreach ([$this->get('/templates'), $this->actingAs($user)->get('/dashboard/invitations/create')] as $response) {
            $response->assertOk();
            $response->assertSee('alt=""', false);
            $response->assertSee('onerror="this.style.display=\'none\'"', false);
            $response->assertSee('linear-gradient(135deg, #292521 0%,', false);
            $response->assertDontSee('alt="' . $floral->name_ar . '"', false);
            $response->assertDontSee('alt="' . $minimal->name_ar . '"', false);
            $response->assertSee($floral->name_ar);
            $response->assertSee($minimal->name_ar);
        }
    }

    public function test_data_migration_updates_existing_external_thumbnails_and_names(): void
    {
        $royal = InvitationTemplate::where('slug', 'royal-gold')->firstOrFail();
        $royal->update([
            'name_ar' => 'الفخامة الذهبية (Royal Gold)',
            'thumbnail' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=600&q=80',
        ]);

        $floral = InvitationTemplate::where('slug', 'floral-romance')->firstOrFail();
        $floral->update([
            'name_ar' => 'سحر الزهور والحديقة (Floral Romance)',
            'thumbnail' => 'https://images.unsplash.com/photo-1519225429402-957e8fa134d1?auto=format&fit=crop&w=600&q=80',
        ]);

        $migration = require database_path('migrations/2026_11_14_000002_update_template_thumbnails_and_names.php');
        $migration->up();

        $royal->refresh();
        $floral->refresh();

        $this->assertSame('الفخامة الذهبية', $royal->name_ar);
        $this->assertSame('/images/templates/royal-gold.webp', $royal->thumbnail);

        $this->assertSame('سحر الزهور والحديقة', $floral->name_ar);
        $this->assertSame('/images/templates/floral-romance.webp', $floral->thumbnail);
    }
}
