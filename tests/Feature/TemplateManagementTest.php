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
}
