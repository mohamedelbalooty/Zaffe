<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoupleNamePositioningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_14_templates_render_groom_on_right_and_bride_on_left_in_preview(): void
    {
        $templates = InvitationTemplate::where('is_active', true)->get();
        $this->assertCount(14, $templates);

        foreach ($templates as $template) {
            $response = $this->get(route('templates.preview', ['slug' => $template->slug]));
            $response->assertOk();

            // Arabic labels
            $response->assertSee('العريس');
            $response->assertSee('العروس');

            // Must not contain legacy family labels
            $response->assertDontSee('الابن');
            $response->assertDontSee('الابنة');
            $response->assertDontSee('ابننا');
            $response->assertDontSee('ابنتنا');

            // Order of appearance in HTML:
            // In Arabic RTL, the first column in the DOM is the right-hand column (العريس / Groom),
            // followed by separator, followed by the left-hand column (العروس / Bride).
            $content = $response->getContent();
            $groomLabelPos = mb_strpos($content, 'العريس');
            $brideLabelPos = mb_strpos($content, 'العروس');

            $this->assertNotFalse($groomLabelPos, "Template [{$template->slug}] missing 'العريس'");
            $this->assertNotFalse($brideLabelPos, "Template [{$template->slug}] missing 'العروس'");
            $this->assertLessThan($brideLabelPos, $groomLabelPos, "In template [{$template->slug}], groom label should appear before bride label in RTL layout (right side first).");
        }
    }

    public function test_all_14_templates_render_centered_groom_without_orphan_bride_or_separators_when_bride_name_is_empty(): void
    {
        $templates = InvitationTemplate::where('is_active', true)->get();
        $this->assertCount(14, $templates);

        $user = User::factory()->create();

        foreach ($templates as $template) {
            $invitation = Invitation::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'title' => 'حفل زفاف '.$template->name_ar,
                'slug' => 'test-single-'.$template->slug,
                'groom_name' => 'محمد بن سلمان العتيبي',
                'bride_name' => '', // Empty bride name
                'wedding_date' => '2026-12-01',
                'wedding_time' => '20:00:00',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'قصر الرياض',
                'venue_address' => 'الرياض، المملكة العربية السعودية',
                'status' => 'published',
                'invitation_mode' => Invitation::MODE_RSVP_ONLY,
                'is_rsvp_enabled' => true,
                'custom_styles' => array_merge($template->default_styles ?? [], [
                    'locale' => 'ar',
                ]),
            ]);

            $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
            $response->assertOk();

            // Shows Groom name and label
            $response->assertSee('العريس');
            $response->assertSee('محمد بن سلمان العتيبي');

            // Does not show Bride label or legacy labels
            $response->assertDontSee('العروس');
            $response->assertDontSee('الابن');
            $response->assertDontSee('الابنة');
            $response->assertDontSee('ابننا');
            $response->assertDontSee('ابنتنا');
        }
    }

    public function test_english_locale_renders_the_groom_and_the_bride(): void
    {
        $templates = InvitationTemplate::where('is_active', true)->get();
        $user = User::factory()->create();

        foreach ($templates as $template) {
            $invitation = Invitation::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'title' => 'Wedding of Faisal & Reem - '.$template->slug,
                'slug' => 'test-en-'.$template->slug,
                'groom_name' => 'Faisal Al-Otaibi',
                'bride_name' => 'Reem Mansour',
                'wedding_date' => '2026-12-01',
                'wedding_time' => '20:00:00',
                'timezone' => 'Asia/Riyadh',
                'venue_name' => 'Riyadh Palace',
                'venue_address' => 'Riyadh, Saudi Arabia',
                'status' => 'published',
                'invitation_mode' => Invitation::MODE_RSVP_ONLY,
                'is_rsvp_enabled' => true,
                'custom_styles' => array_merge($template->default_styles ?? [], [
                    'locale' => 'en',
                ]),
            ]);

            $response = $this->get(route('invitation.show', ['slug' => $invitation->slug]));
            $response->assertOk();

            $response->assertSee('The Groom');
            $response->assertSee('The Bride');
            $response->assertSee('Faisal Al-Otaibi');
            $response->assertSee('Reem Mansour');
        }
    }
}
