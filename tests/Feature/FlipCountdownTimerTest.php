<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationDateFormatter;
use App\Support\TemplateStyleCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FlipCountdownTimerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_14_templates_render_luxury_flip_countdown_with_four_arabic_units(): void
    {
        $templates = InvitationTemplate::where('is_active', true)->get();
        $this->assertCount(14, $templates);

        foreach ($templates as $template) {
            $response = $this->get(route('templates.preview', ['slug' => $template->slug]));
            $response->assertOk();

            $html = $response->getContent();
            $this->assertIsString($html);

            $this->assertStringContainsString('المتبقي حتى ليلة العمر', $html, "Missing heading in {$template->slug}");
            $this->assertStringContainsString('zaffe-flip-tile', $html, "Missing flip tile class in {$template->slug}");
            $this->assertStringContainsString('zaffe-flip-flap-top', $html, "Missing top flap class in {$template->slug}");
            $this->assertStringContainsString('zaffe-flip-flap-bottom', $html, "Missing bottom flap class in {$template->slug}");
            $this->assertStringContainsString('zaffe-flip-seam', $html, "Missing hinge seam class in {$template->slug}");

            $this->assertStringContainsString('يوم', $html);
            $this->assertStringContainsString('ساعة', $html);
            $this->assertStringContainsString('دقيقة', $html);
            $this->assertStringContainsString('ثانية', $html);
        }
    }

    public function test_flip_countdown_preserves_arabic_indic_numerals_prefers_reduced_motion_and_clean_stop_at_zero(): void
    {
        $user = User::factory()->create();
        $template = InvitationTemplate::where('is_active', true)->firstOrFail();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف عبد الله',
            'slug' => 'abdullah-flip-countdown',
            'groom_name' => 'عبد الله القحطاني',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'wedding_time' => '20:30:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر الرياض',
            'status' => 'published',
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'is_rsvp_enabled' => false,
            'custom_styles' => array_merge($template->default_styles ?? [], [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ]),
        ]);

        InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'countdown',
            'is_enabled' => true,
        ]);

        $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
        $response->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString('arabicIndic: true', $html);
        $this->assertStringContainsString('>٠<', $html);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $html);
        $this->assertStringContainsString('clearInterval(this.intervalId)', $html);
        $this->assertStringContainsString('Math.max(0, this.target - Date.now())', $html);
        $this->assertStringContainsString('visibilitychange.window', $html);
    }

    public function test_dark_theme_templates_apply_dark_flip_tile_modifier(): void
    {
        $user = User::factory()->create();

        foreach (['emerald-night', 'desert-dusk'] as $darkSlug) {
            $template = InvitationTemplate::where('slug', $darkSlug)->firstOrFail();
            $invitation = Invitation::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'title' => 'حفل زفاف ليلي',
                'slug' => 'dark-flip-'.$darkSlug,
                'groom_name' => 'نايف السبيعي',
                'wedding_date' => now()->addDays(20)->toDateString(),
                'wedding_time' => '21:00:00',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الصحراء',
                'status' => 'published',
                'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
                'is_rsvp_enabled' => false,
            ]);

            InvitationSection::create([
                'invitation_id' => $invitation->id,
                'section_type' => 'countdown',
                'is_enabled' => true,
            ]);

            $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
            $response->assertOk();
            $response->assertSee('zaffe-flip-tile--dark', false);
        }
    }

    public function test_studio_editor_preview_renders_flip_countdown_and_reacts_to_date_and_digit_changes(): void
    {
        $user = User::factory()->create();
        $template = InvitationTemplate::where('slug', 'taif-rose')->firstOrFail();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'حفل زفاف فهد',
            'slug' => 'fahad-editor-flip',
            'groom_name' => 'فهد الحربي',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'wedding_time' => '20:00:00',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قاعة نيارة',
            'status' => 'draft',
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'is_rsvp_enabled' => false,
            'custom_styles' => array_merge($template->default_styles ?? [], [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ]),
        ]);

        InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'countdown',
            'is_enabled' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(InvitationEditor::class, ['id' => $invitation->id])
            ->assertSee('المتبقي حتى ليلة العمر')
            ->assertSee('zaffe-flip-tile', false)
            ->assertSee('arabicIndic: true', false)
            ->set('digits', 'latin')
            ->assertSee('arabicIndic: false', false)
            ->assertSee('>0<', false)
            ->set('previewDevice', 'mobile')
            ->assertSee('max-w-[26rem]', false)
            ->assertSee('zaffe-flip-tile', false);
    }

    public function test_demo_route_renders_flip_countdown_with_arabic_units(): void
    {
        $response = $this->get(route('demo.saudi'));
        $response->assertOk();

        $html = $response->getContent();
        $this->assertIsString($html);
        $this->assertStringContainsString('المتبقي حتى ليلة العمر', $html);
        $this->assertStringContainsString('zaffe-flip-tile', $html);
        $this->assertStringContainsString('zaffe-flip-flap-top', $html);
        $this->assertStringContainsString('zaffe-flip-flap-bottom', $html);
        $this->assertStringContainsString('requestAnimationFrame', $html);
    }

    public function test_countdown_config_and_hex_alpha_helpers_handle_valid_and_fallback_inputs(): void
    {
        $this->assertSame('#B36A3E35', TemplateStyleCatalog::hexWithAlpha('#B36A3E', '35', 'rgba(0,0,0,0.2)'));
        $this->assertSame('rgba(0,0,0,0.2)', TemplateStyleCatalog::hexWithAlpha('#FFF', '35', 'rgba(0,0,0,0.2)'));
        $this->assertSame('rgba(0,0,0,0.2)', TemplateStyleCatalog::hexWithAlpha('rgb(1, 2, 3)', '35', 'rgba(0,0,0,0.2)'));

        $invitation = (object) [
            'wedding_date' => '2026-12-20',
            'wedding_time' => '21:00:00',
            'timezone' => 'Invalid/Timezone',
            'effective_styles' => [
                'locale' => 'en',
                'digits' => 'latin',
            ],
            'sections' => collect([]),
        ];

        $config = InvitationDateFormatter::countdownConfig($invitation);
        $this->assertTrue($config['enabled']);
        $this->assertTrue($config['is_english']);
        $this->assertFalse($config['is_arabic_indic']);
        $this->assertSame('0', $config['zero_char']);
        $this->assertCount(4, $config['units']);
        $this->assertSame('Days', $config['units'][0]['label']);
    }
}
