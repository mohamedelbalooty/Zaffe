<?php

namespace Tests\Feature;

use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private InvitationTemplate $template1;
    private InvitationTemplate $template2;
    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->template1 = InvitationTemplate::create([
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

        $this->template2 = InvitationTemplate::create([
            'name_ar' => 'الرومانسية الزهرية',
            'name_en' => 'Floral Romance',
            'slug' => 'floral-romance',
            'view_name' => 'templates.floral_romance',
            'category' => 'floral',
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 2,
            'default_styles' => [
                'primary_color' => '#D98282',
                'secondary_color' => '#2B2B2B',
                'background_color' => '#FFF9F9',
                'font_family' => 'Amiri',
            ],
        ]);

        $this->invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template1->id,
            'title' => 'حفل زفاف أحمد وسارة',
            'slug' => 'ahmed-and-sara',
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'venue_name' => 'فندق الفورسيزونز',
            'status' => 'draft',
            'custom_styles' => $this->template1->default_styles,
        ]);
    }

    public function test_guest_cannot_access_editor(): void
    {
        $response = $this->get(route('dashboard.invitations.editor', $this->invitation->id));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_other_users_invitation(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('dashboard.invitations.editor', $this->invitation->id));

        $response->assertStatus(403);
    }

    public function test_owner_can_access_editor(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.invitations.editor', $this->invitation->id));

        $response->assertOk();
        $response->assertSee('أحمد');
        $response->assertSee('سارة');
    }

    public function test_owner_can_update_basic_details_and_timezone(): void
    {
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('groom_name', 'مروان')
            ->set('bride_name', 'ياسمين')
            ->set('timezone', 'Asia/Riyadh')
            ->call('saveChanges');

        $this->invitation->refresh();
        $this->assertSame('مروان', $this->invitation->groom_name);
        $this->assertSame('ياسمين', $this->invitation->bride_name);
        $this->assertSame('Asia/Riyadh', $this->invitation->timezone);
    }

    public function test_owner_can_switch_template_without_restriction(): void
    {
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->call('changeTemplate', $this->template2->id);

        $this->invitation->refresh();
        $this->assertSame($this->template2->id, $this->invitation->template_id);
        $this->assertSame('#D98282', $this->invitation->custom_styles['primary_color']);
        $this->assertSame('Amiri', $this->invitation->custom_styles['font_family']);
    }

    public function test_owner_can_customize_colors_and_typography(): void
    {
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('primary_color', '#10B981')
            ->set('font_family', 'Cairo')
            ->call('saveChanges');

        $this->invitation->refresh();
        $this->assertSame('#10B981', $this->invitation->custom_styles['primary_color']);
        $this->assertSame('Cairo', $this->invitation->custom_styles['font_family']);
    }

    public function test_owner_can_toggle_sections(): void
    {
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('section_countdown', false)
            ->set('section_timeline', true)
            ->call('saveChanges');

        $countdownSection = InvitationSection::where('invitation_id', $this->invitation->id)
            ->where('section_type', 'countdown')
            ->first();

        $this->assertNotNull($countdownSection);
        $this->assertFalse($countdownSection->is_enabled);
    }

    public function test_owner_can_toggle_publish_status_in_editor(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id]);

        $component->call('togglePublish');
        $this->assertSame('published', $this->invitation->fresh()->status);

        $component->call('togglePublish');
        $this->assertSame('draft', $this->invitation->fresh()->status);
    }

    public function test_owner_can_upload_cover_photo(): void
    {
        Storage::fake('public');

        $fakeCover = UploadedFile::fake()->image('cover.png', 800, 600);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $fakeCover);

        $this->invitation->refresh();
        $this->assertNotNull($this->invitation->cover_photo);
    }

    public function test_owner_can_upload_and_delete_gallery_photo(): void
    {
        Storage::fake('public');

        $fakePhoto = UploadedFile::fake()->image('gallery_sample.png', 800, 600);

        $component = Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_gallery_photo', $fakePhoto);

        $photo = InvitationPhoto::where('invitation_id', $this->invitation->id)->first();
        $this->assertNotNull($photo);

        $component->call('deleteGalleryPhoto', $photo->id);

        $this->assertDatabaseMissing('invitation_photos', ['id' => $photo->id]);
    }
}
