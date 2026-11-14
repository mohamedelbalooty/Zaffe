<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvitationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private InvitationTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->template = InvitationTemplate::create([
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'view_name' => 'templates.royal_gold',
            'category' => 'classic',
            'is_premium' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_user_can_create_invitation_with_default_timezone(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الفورسيزونز',
        ]);

        $invitation = Invitation::first();
        $this->assertNotNull($invitation);
        $this->assertSame('Africa/Cairo', $invitation->timezone);
        $this->assertSame('draft', $invitation->status);
        $response->assertRedirect(route('dashboard.invitations.editor', $invitation->id));
    }

    public function test_user_can_create_invitation_with_saudi_timezone(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'فهد',
            'bride_name' => 'نورة',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'venue_name' => 'قاعة قصر الرياض',
            'timezone' => 'Asia/Riyadh',
        ]);

        $invitation = Invitation::where('groom_name', 'فهد')->first();
        $this->assertNotNull($invitation);
        $this->assertSame('Asia/Riyadh', $invitation->timezone);
    }

    public function test_user_cannot_create_invitation_with_invalid_timezone(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'أحمد',
            'bride_name' => 'سارة',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'فندق الفورسيزونز',
            'timezone' => 'America/New_York',
        ]);

        $response->assertSessionHasErrors('timezone');
        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_owner_can_toggle_publish_status(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح كريم ومريم',
            'slug' => 'karim-and-mariam',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'draft',
        ]);

        // Toggle to published
        $response = $this->actingAs($this->user)
            ->post(route('dashboard.invitations.toggle-publish', $invitation->id));

        $response->assertSessionHas('success');
        $this->assertSame('published', $invitation->fresh()->status);

        // Toggle back to draft
        $response = $this->actingAs($this->user)
            ->post(route('dashboard.invitations.toggle-publish', $invitation->id));

        $response->assertSessionHas('success');
        $this->assertSame('draft', $invitation->fresh()->status);
    }

    public function test_non_owner_cannot_toggle_publish_status(): void
    {
        $otherUser = User::factory()->create();
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح خاص',
            'slug' => 'private-wedding',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($otherUser)
            ->post(route('dashboard.invitations.toggle-publish', $invitation->id));

        $response->assertNotFound();
        $this->assertSame('draft', $invitation->fresh()->status);
    }

    public function test_owner_can_archive_invitation(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح للأرشفة',
            'slug' => 'archive-wedding',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.invitations.archive', $invitation->id));

        $response->assertSessionHas('success');
        $this->assertSame('archived', $invitation->fresh()->status);
    }

    public function test_draft_invitation_returns_403_for_public_guests(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح مسودة',
            'slug' => 'draft-wedding',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->get(route('invitation.show', $invitation->slug));
        $response->assertStatus(403);
    }

    public function test_draft_invitation_accessible_to_owner(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح مسودة المالك',
            'slug' => 'owner-draft-wedding',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get(route('invitation.show', $invitation->slug));
        $response->assertOk();
    }

    public function test_published_invitation_accessible_publicly_and_increments_views(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح منشور للعامة',
            'slug' => 'public-wedding-f03',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'published',
            'view_count' => 0,
        ]);

        $response = $this->get(route('invitation.show', $invitation->slug));
        $response->assertOk();
        $this->assertSame(1, $invitation->fresh()->view_count);
    }

    public function test_destroy_cleans_up_uploaded_storage_files(): void
    {
        Storage::fake('public');

        $coverFile = UploadedFile::fake()->image('cover.png');
        $coverPath = $coverFile->store('invitations/covers', 'public');

        $galleryFile = UploadedFile::fake()->image('gallery1.png');
        $galleryPath = $galleryFile->store('invitations/gallery', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح مع صور',
            'slug' => 'wedding-with-photos',
            'groom_name' => 'كريم',
            'bride_name' => 'مريم',
            'venue_name' => 'فندق الفورسيزونز',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'draft',
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => Storage::disk('public')->url($galleryPath),
            'caption' => 'صورة ألبوم',
            'sort_order' => 1,
        ]);

        Storage::disk('public')->assertExists($coverPath);
        Storage::disk('public')->assertExists($galleryPath);

        $response = $this->actingAs($this->user)
            ->delete(route('dashboard.invitations.destroy', $invitation->id));

        $response->assertRedirect(route('dashboard.invitations.index'));
        $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
        $this->assertDatabaseMissing('invitation_photos', ['invitation_id' => $invitation->id]);

        Storage::disk('public')->assertMissing($coverPath);
        Storage::disk('public')->assertMissing($galleryPath);
    }

    public function test_new_invitation_receives_short_url_safe_unique_code_and_canonical_i_url(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'عبدالله السالم',
            'bride_name' => 'سارة العتيبي',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'venue_name' => 'قاعة نيارة الرياض',
            'timezone' => 'Asia/Riyadh',
        ]);

        $invitation = Invitation::where('groom_name', 'عبدالله السالم')->firstOrFail();
        $response->assertRedirect(route('dashboard.invitations.editor', $invitation->id));

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{7,8}$/', $invitation->slug);
        $this->assertSame($invitation->slug, $invitation->code);
        $this->assertSame(url('/i/' . $invitation->slug), $invitation->public_url);
        $this->assertStringNotContainsString('-and-', $invitation->slug);

        $whatsAppUrl = $invitation->getWhatsAppShareUrl();
        $this->assertStringContainsString(rawurlencode($invitation->public_url), $whatsAppUrl);
        $this->assertStringNotContainsString(rawurlencode('/invitation/'), $whatsAppUrl);
    }

    public function test_destroy_cleans_up_cached_og_image(): void
    {
        Storage::fake('public');

        $coverFile = UploadedFile::fake()->image('cover.png', 1600, 1000);
        $coverPath = $coverFile->store('invitations/covers', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'فرح مع OG كاش',
            'groom_name' => 'فيصل',
            'bride_name' => 'مها',
            'venue_name' => 'فندق الريتز كارلتون',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        $ogUrl = $invitation->og_image_url;
        $this->assertStringContainsString('/storage/invitations/og/', $ogUrl);
        $this->assertNotEmpty(Storage::disk('public')->files('invitations/og'));

        $this->actingAs($this->user)
            ->delete(route('dashboard.invitations.destroy', $invitation->id))
            ->assertRedirect(route('dashboard.invitations.index'));

        $this->assertEmpty(Storage::disk('public')->files('invitations/og'));
    }
}
