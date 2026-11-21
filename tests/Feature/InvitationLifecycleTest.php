<?php

namespace Tests\Feature;

use App\Jobs\PurgeInvitationMediaJob;
use App\Models\Invitation;
use App\Models\InvitationGuest;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\RsvpResponse;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;
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

    public function test_user_can_create_invitation_with_default_saudi_timezone_optional_bride_and_rsvp_disabled(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'أحمد',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'venue_name' => 'قاعة المملكة',
        ]);

        $invitation = Invitation::first();
        $this->assertNotNull($invitation);
        $this->assertSame('Asia/Riyadh', $invitation->timezone);
        $this->assertNull($invitation->bride_name);
        $this->assertSame('حفل زفاف أحمد', $invitation->title);
        $this->assertSame('draft', $invitation->status);
        $this->assertFalse($invitation->is_rsvp_enabled);
        $this->assertSame(Invitation::MODE_ANNOUNCEMENT, $invitation->invitation_mode);
        $response->assertRedirect(route('dashboard.invitations.editor', $invitation->id));
    }

    public function test_user_can_create_invitation_with_egyptian_timezone_and_bride_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.invitations.store'), [
            'template_id' => $this->template->id,
            'groom_name' => 'فهد',
            'bride_name' => 'نورة',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'venue_name' => 'قاعة قصر الرياض',
            'timezone' => 'Africa/Cairo',
        ]);

        $invitation = Invitation::where('groom_name', 'فهد')->first();
        $this->assertNotNull($invitation);
        $this->assertSame('Africa/Cairo', $invitation->timezone);
        $this->assertSame('نورة', $invitation->bride_name);
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

    public function test_my_invitations_page_renders_delete_confirmation_dialog_for_owner(): void
    {
        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل طارق ونور',
            'slug' => 'tariq-and-nour-delete-ui',
            'groom_name' => 'طارق',
            'bride_name' => 'نور',
            'venue_name' => 'قصر الرياض',
            'wedding_date' => now()->addDays(20)->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.invitations.index'));

        $response->assertOk();
        $response->assertSee(route('dashboard.invitations.destroy', $invitation->id), false);
        $response->assertSee('تأكيد حذف الدعوة');
        $response->assertSee('سيتم حذف الصور المرفوعة وسجلات الضيوف وتأكيدات الحضور المرتبطة بها وإيقاف جميع روابطها فوراً.');
        $response->assertSee('نعم، احذف');
    }

    public function test_owner_delete_cascades_guests_rsvps_sections_purges_media_and_returns_404_on_public_and_legacy_routes(): void
    {
        Storage::fake('public');

        $coverPath = UploadedFile::fake()->image('cover.png')->store('invitations/covers', 'public');
        $galleryUrlPath = UploadedFile::fake()->image('gallery-url.png')->store('invitations/gallery', 'public');
        $galleryRelativePath = UploadedFile::fake()->image('gallery-rel.png')->store('invitations/gallery', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل فيصل وسارة',
            'slug' => 'faisal-and-sara-cascade',
            'groom_name' => 'فيصل',
            'bride_name' => 'سارة',
            'venue_name' => 'قاعة المملكة',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'status' => 'published',
            'is_rsvp_enabled' => true,
            'invitation_mode' => Invitation::MODE_RSVP_QR,
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        $section = InvitationSection::create([
            'invitation_id' => $invitation->id,
            'section_type' => 'countdown',
            'title_ar' => 'العد التنازلي',
            'is_enabled' => true,
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => Storage::disk('public')->url($galleryUrlPath),
            'caption' => 'صورة رابط كامل',
            'sort_order' => 1,
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => $galleryRelativePath,
            'caption' => 'صورة مسار نسبي',
            'sort_order' => 2,
        ]);

        $guest = InvitationGuest::create([
            'invitation_id' => $invitation->id,
            'name' => 'ضيف مكرم',
            'phone' => '+966501112233',
            'max_adults' => 2,
            'max_children' => 1,
        ]);

        $rsvp = RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'invitation_guest_id' => $guest->id,
            'guest_name' => 'ضيف مكرم',
            'phone' => '+966501112233',
            'attendance_status' => 'attending',
            'guest_count' => 2,
        ]);

        // Verify routes live prior to deletion
        $this->get(route('invitation.show', $invitation->slug))->assertOk();
        $this->get(route('invitation.show', $invitation->slug).'?g='.$guest->invite_token)->assertOk();
        $this->get(route('guest.pass', $guest->qr_token))->assertOk();

        $response = $this->actingAs($this->user)
            ->delete(route('dashboard.invitations.destroy', $invitation->id));

        $response->assertRedirect(route('dashboard.invitations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
        $this->assertDatabaseMissing('invitation_sections', ['id' => $section->id]);
        $this->assertDatabaseMissing('invitation_photos', ['invitation_id' => $invitation->id]);
        $this->assertDatabaseMissing('invitation_guests', ['id' => $guest->id]);
        $this->assertDatabaseMissing('rsvp_responses', ['id' => $rsvp->id]);

        Storage::disk('public')->assertMissing($coverPath);
        Storage::disk('public')->assertMissing($galleryUrlPath);
        Storage::disk('public')->assertMissing($galleryRelativePath);

        $this->get(route('invitation.show', $invitation->slug))->assertNotFound();
        $this->get(route('invitation.show', $invitation->slug).'?g='.$guest->invite_token)->assertNotFound();
        $this->get(route('guest.pass', $guest->qr_token))->assertNotFound();
    }

    public function test_unauthenticated_and_non_owner_cannot_delete_invitation_or_media(): void
    {
        Storage::fake('public');

        $coverPath = UploadedFile::fake()->image('cover.png')->store('invitations/covers', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل محمي',
            'slug' => 'protected-delete-wedding',
            'groom_name' => 'سلمان',
            'bride_name' => 'مها',
            'venue_name' => 'فندق الريتز',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        $guest = InvitationGuest::create([
            'invitation_id' => $invitation->id,
            'name' => 'مدعو محمي',
        ]);

        // Unauthenticated visitor
        $this->delete(route('dashboard.invitations.destroy', $invitation->id))
            ->assertRedirect(route('login'));

        // Cross-tenant attacker
        $otherUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($otherUser)
            ->delete(route('dashboard.invitations.destroy', $invitation->id))
            ->assertNotFound();

        $this->assertDatabaseHas('invitations', ['id' => $invitation->id]);
        $this->assertDatabaseHas('invitation_guests', ['id' => $guest->id]);
        Storage::disk('public')->assertExists($coverPath);
    }

    public function test_partial_failure_preserves_media_when_db_delete_fails_and_handles_storage_error_after_commit(): void
    {
        Storage::fake('public');

        $coverPath = UploadedFile::fake()->image('cover-rollback.png')->store('invitations/covers', 'public');
        $galleryPath = UploadedFile::fake()->image('gallery-rollback.png')->store('invitations/gallery', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل اختبار الفشل الجزئي',
            'slug' => 'partial-failure-test',
            'groom_name' => 'بندر',
            'bride_name' => 'شهد',
            'venue_name' => 'قصر النخيل',
            'wedding_date' => now()->addDays(40)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($coverPath),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => Storage::disk('public')->url($galleryPath),
            'caption' => 'صورة ثابتة',
            'sort_order' => 1,
        ]);

        // Part A: Simulate DB failure during deletion; files on disk must NOT be deleted before commit.
        $shouldThrowOnDelete = true;
        Invitation::deleting(function () use (&$shouldThrowOnDelete): void {
            if ($shouldThrowOnDelete) {
                throw new \RuntimeException('Simulated DB delete failure');
            }
        });

        try {
            $this->withoutExceptionHandling();
            try {
                $this->actingAs($this->user)
                    ->delete(route('dashboard.invitations.destroy', $invitation->id));
                $this->fail('Expected RuntimeException during simulated DB failure.');
            } catch (\RuntimeException $e) {
                $this->assertSame('Simulated DB delete failure', $e->getMessage());
            }
        } finally {
            $shouldThrowOnDelete = false;
            $this->withExceptionHandling();
        }

        $this->assertDatabaseHas('invitations', ['id' => $invitation->id]);
        Storage::disk('public')->assertExists($coverPath);
        Storage::disk('public')->assertExists($galleryPath);

        // Part B: Simulate storage delete throwing after DB commit; DB row still deletes and warning is logged cleanly.
        Log::spy();
        $failingDisk = Mockery::mock(Filesystem::class);
        $failingDisk->shouldReceive('url')->with('')->andReturn(rtrim((string) config('filesystems.disks.public.url', 'http://localhost/storage'), '/').'/');
        $failingDisk->shouldReceive('exists')->andReturn(true);
        $failingDisk->shouldReceive('delete')->andThrow(new \RuntimeException('Simulated storage I/O error'));
        Storage::set('public', $failingDisk);

        try {
            $response = $this->actingAs($this->user)
                ->delete(route('dashboard.invitations.destroy', $invitation->id));

            $response->assertRedirect(route('dashboard.invitations.index'));
            $response->assertSessionHas('success');
            $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
            Log::shouldHaveReceived('warning')->atLeast()->once();
        } finally {
            Storage::forgetDisk('public');
        }
    }

    public function test_nested_transaction_rollback_preserves_media_across_dashboard_model_and_user_cascade_deletes(): void
    {
        Storage::fake('public');

        // 1. Direct Invitation::delete() inside a transaction that fails AFTER delete() fires.
        $directCover = UploadedFile::fake()->image('direct-cover.png')->store('invitations/covers', 'public');
        $directGallery = UploadedFile::fake()->image('direct-gallery.png')->store('invitations/gallery', 'public');

        $directInvitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل اختبار التراجع المباشر',
            'slug' => 'direct-rollback-test',
            'groom_name' => 'خالد',
            'bride_name' => 'ريم',
            'venue_name' => 'قصر الشرق',
            'wedding_date' => now()->addDays(25)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($directCover),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $directInvitation->id,
            'image_path' => $directGallery,
            'sort_order' => 1,
        ]);

        try {
            DB::transaction(function () use ($directInvitation): void {
                $directInvitation->delete();
                throw new \RuntimeException('Simulated failure after Invitation::deleted');
            });
            $this->fail('Expected transaction rollback exception.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated failure after Invitation::deleted', $e->getMessage());
        }

        $this->assertDatabaseHas('invitations', ['id' => $directInvitation->id]);
        Storage::disk('public')->assertExists($directCover);
        Storage::disk('public')->assertExists($directGallery);

        Invitation::findOrFail($directInvitation->id)->delete();
        $this->assertDatabaseMissing('invitations', ['id' => $directInvitation->id]);
        Storage::disk('public')->assertMissing($directCover);
        Storage::disk('public')->assertMissing($directGallery);

        // 2. Dashboard destroy nested inside an outer transaction that rolls back after controller finishes.
        $nestedCover = UploadedFile::fake()->image('nested-cover.png')->store('invitations/covers', 'public');
        $nestedGallery = UploadedFile::fake()->image('nested-gallery.png')->store('invitations/gallery', 'public');

        $nestedInvitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل المعاملة المتداخلة',
            'slug' => 'nested-tx-rollback',
            'groom_name' => 'سعد',
            'bride_name' => 'دانة',
            'venue_name' => 'قاعة النور',
            'wedding_date' => now()->addDays(35)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($nestedCover),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $nestedInvitation->id,
            'image_path' => Storage::disk('public')->url($nestedGallery),
            'sort_order' => 1,
        ]);

        try {
            DB::transaction(function () use ($nestedInvitation): void {
                $this->actingAs($this->user)
                    ->delete(route('dashboard.invitations.destroy', $nestedInvitation->id))
                    ->assertRedirect(route('dashboard.invitations.index'));

                throw new \RuntimeException('Outer nested transaction failed');
            });
            $this->fail('Expected outer transaction rollback.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Outer nested transaction failed', $e->getMessage());
        }

        $this->assertDatabaseHas('invitations', ['id' => $nestedInvitation->id]);
        Storage::disk('public')->assertExists($nestedCover);
        Storage::disk('public')->assertExists($nestedGallery);

        $this->actingAs($this->user)
            ->delete(route('dashboard.invitations.destroy', $nestedInvitation->id))
            ->assertRedirect(route('dashboard.invitations.index'));

        $this->assertDatabaseMissing('invitations', ['id' => $nestedInvitation->id]);
        Storage::disk('public')->assertMissing($nestedCover);
        Storage::disk('public')->assertMissing($nestedGallery);

        // 3. Cascaded User::delete() honors transaction rollback and purges cascaded invitation media on commit.
        $victimOwner = User::factory()->create(['role' => 'user']);
        $cascadeCover = UploadedFile::fake()->image('user-cascade-cover.png')->store('invitations/covers', 'public');
        $cascadeGallery = UploadedFile::fake()->image('user-cascade-gallery.png')->store('invitations/gallery', 'public');

        $cascadeInvitation = Invitation::create([
            'user_id' => $victimOwner->id,
            'template_id' => $this->template->id,
            'title' => 'دعوة محذوفة بحذف المستخدم',
            'slug' => 'user-cascade-delete-media',
            'groom_name' => 'ماجد',
            'bride_name' => 'لمى',
            'venue_name' => 'فندق الفيصلية',
            'wedding_date' => now()->addDays(45)->toDateString(),
            'status' => 'published',
            'cover_photo' => Storage::disk('public')->url($cascadeCover),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $cascadeInvitation->id,
            'image_path' => '/storage/'.$cascadeGallery,
            'sort_order' => 1,
        ]);

        try {
            DB::transaction(function () use ($victimOwner): void {
                $victimOwner->delete();
                throw new \RuntimeException('Rollback after user cascade');
            });
            $this->fail('Expected user cascade rollback.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Rollback after user cascade', $e->getMessage());
        }

        $this->assertDatabaseHas('users', ['id' => $victimOwner->id]);
        $this->assertDatabaseHas('invitations', ['id' => $cascadeInvitation->id]);
        Storage::disk('public')->assertExists($cascadeCover);
        Storage::disk('public')->assertExists($cascadeGallery);

        User::findOrFail($victimOwner->id)->delete();

        $this->assertDatabaseMissing('users', ['id' => $victimOwner->id]);
        $this->assertDatabaseMissing('invitations', ['id' => $cascadeInvitation->id]);
        Storage::disk('public')->assertMissing($cascadeCover);
        Storage::disk('public')->assertMissing($cascadeGallery);
    }

    public function test_post_commit_storage_failure_enqueues_retry_job_and_retry_job_purges_on_recovery(): void
    {
        Storage::fake('public');
        Queue::fake();
        Log::spy();

        $realFakeDisk = Storage::disk('public');
        $coverPath = UploadedFile::fake()->image('retry-cover.png')->store('invitations/covers', 'public');
        $galleryPath = UploadedFile::fake()->image('retry-gallery.png')->store('invitations/gallery', 'public');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل إعادة المحاولة',
            'slug' => 'retry-purge-test',
            'groom_name' => 'تركي',
            'bride_name' => 'هند',
            'venue_name' => 'قصر النخيل',
            'wedding_date' => now()->addDays(50)->toDateString(),
            'status' => 'published',
            'cover_photo' => $realFakeDisk->url($coverPath),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => $realFakeDisk->url($galleryPath),
            'sort_order' => 1,
        ]);

        $failingDisk = Mockery::mock(Filesystem::class);
        $failingDisk->shouldReceive('url')
            ->with('')
            ->andReturn(rtrim((string) config('filesystems.disks.public.url', 'http://localhost/storage'), '/').'/');
        $failingDisk->shouldReceive('exists')->andReturn(true);
        $failingDisk->shouldReceive('delete')->with($coverPath)->andThrow(new \RuntimeException('Transient I/O error'));
        $failingDisk->shouldReceive('delete')->with($galleryPath)->andReturn(false);

        Storage::set('public', $failingDisk);

        try {
            $response = $this->actingAs($this->user)
                ->delete(route('dashboard.invitations.destroy', $invitation->id));

            $response->assertRedirect(route('dashboard.invitations.index'));
            $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);
            Log::shouldHaveReceived('warning')->twice();

            Queue::assertPushed(PurgeInvitationMediaJob::class, function (PurgeInvitationMediaJob $job) use ($coverPath, $galleryPath): bool {
                return in_array($coverPath, $job->paths, true)
                    && in_array($galleryPath, $job->paths, true);
            });

            // Verify retry job throws so queue worker retries and tracks failed_jobs while storage stays broken.
            try {
                (new PurgeInvitationMediaJob([$coverPath, $galleryPath]))->handle();
                $this->fail('Expected PurgeInvitationMediaJob to fail while storage errors.');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString($coverPath, $e->getMessage());
                $this->assertStringContainsString($galleryPath, $e->getMessage());
            }
        } finally {
            Storage::set('public', $realFakeDisk);
        }

        // Once storage recovers, running the queued retry job removes the previously stranded files.
        $realFakeDisk->assertExists($coverPath);
        $realFakeDisk->assertExists($galleryPath);

        (new PurgeInvitationMediaJob([$coverPath, $galleryPath]))->handle();

        $realFakeDisk->assertMissing($coverPath);
        $realFakeDisk->assertMissing($galleryPath);
    }

    public function test_cleanup_orphaned_invitation_media_command_respects_dry_run_active_refs_grace_window_and_prefixes(): void
    {
        Storage::fake('public');
        $disk = Storage::disk('public');

        $activeCover = UploadedFile::fake()->image('active-cover.png')->store('invitations/covers', 'public');
        $activeGallery = UploadedFile::fake()->image('active-gallery.png')->store('invitations/gallery', 'public');
        $agedOrphanCover = UploadedFile::fake()->image('orphan-cover.png')->store('invitations/covers', 'public');
        $agedOrphanGallery = UploadedFile::fake()->image('orphan-gallery.png')->store('invitations/gallery', 'public');
        $recentUpload = UploadedFile::fake()->image('recent-upload.png')->store('invitations/covers', 'public');
        $disk->put('avatars/old-avatar.jpg', 'avatar-bytes');

        $invitation = Invitation::create([
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'دعوة ملفات نشطة',
            'slug' => 'active-media-refs',
            'groom_name' => 'سعود',
            'bride_name' => 'أمل',
            'venue_name' => 'قصر المملكة',
            'wedding_date' => now()->addDays(15)->toDateString(),
            'status' => 'published',
            'cover_photo' => $disk->url($activeCover),
        ]);

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'image_path' => '/storage/'.$activeGallery,
            'sort_order' => 1,
        ]);

        $expiredTime = now()->subHours(3)->getTimestamp();
        foreach ([$activeCover, $activeGallery, $agedOrphanCover, $agedOrphanGallery, 'avatars/old-avatar.jpg'] as $path) {
            touch($disk->path($path), $expiredTime);
        }
        clearstatcache();

        // Dry run must report candidates without deleting any files.
        $this->artisan('zaffe:cleanup-orphaned-media', ['--dry-run' => true])
            ->expectsOutputToContain('2 orphaned invitation media file(s)')
            ->assertSuccessful();

        $disk->assertExists($agedOrphanCover);
        $disk->assertExists($agedOrphanGallery);

        // Actual run removes aged orphans only while preserving active refs, grace-window uploads, and unrelated folders.
        $this->artisan('zaffe:cleanup-orphaned-media')
            ->expectsOutputToContain('deleted 2 orphaned invitation media file(s)')
            ->assertSuccessful();

        $disk->assertMissing($agedOrphanCover);
        $disk->assertMissing($agedOrphanGallery);
        $disk->assertExists($activeCover);
        $disk->assertExists($activeGallery);
        $disk->assertExists($recentUpload);
        $disk->assertExists('avatars/old-avatar.jpg');
    }
}
