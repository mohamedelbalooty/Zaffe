<?php

namespace Tests\Feature;

use App\Jobs\ProcessInvitationImage;
use App\Livewire\InvitationEditor;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationImagePipelineTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private InvitationTemplate $template;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->template = InvitationTemplate::create([
            'name_ar' => 'الملكي الذهبي',
            'name_en' => 'Royal Gold',
            'slug' => 'royal-gold',
            'view_name' => 'templates.royal_gold',
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
            'user_id' => $this->user->id,
            'template_id' => $this->template->id,
            'title' => 'حفل زفاف تجريبي',
            'slug' => 'test-img-1',
            'groom_name' => 'سعد',
            'bride_name' => 'نورة',
            'wedding_date' => now()->addDays(30)->toDateString(),
            'venue_name' => 'قاعة نيارة',
            'status' => 'published',
        ]);
    }

    public function test_cover_upload_dispatches_job_produces_webp_variant_and_keeps_original_out_of_public_storage(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $coverFile = UploadedFile::fake()->image('hero-photo.png', 2400, 1600);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $coverFile)
            ->assertHasNoErrors();

        $this->invitation->refresh();

        $this->assertNotNull($this->invitation->cover_photo);
        $this->assertStringEndsWith('.webp', $this->invitation->cover_photo);
        $this->assertSame(1600, $this->invitation->cover_photo_width);
        $this->assertSame(1067, $this->invitation->cover_photo_height);

        $publicFiles = Storage::disk('public')->allFiles();
        $this->assertCount(1, $publicFiles);
        $this->assertStringStartsWith('invitations/covers/', $publicFiles[0]);
        $this->assertStringEndsWith('.webp', $publicFiles[0]);

        $webpBytes = Storage::disk('public')->get($publicFiles[0]);
        $info = getimagesizefromstring($webpBytes);
        $this->assertNotFalse($info);
        $this->assertSame('image/webp', $info['mime']);
        $this->assertSame(1600, $info[0]);
        $this->assertSame(1067, $info[1]);

        // Original temp upload must not linger on local or public disks
        $this->assertEmpty(Storage::disk('local')->allFiles('invitations/tmp'));
    }

    public function test_metadata_and_exif_chunks_are_stripped_from_generated_webp(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $gd = imagecreatetruecolor(800, 600);
        $gold = imagecolorallocate($gd, 198, 167, 107);
        imagefill($gd, 0, 0, $gold);
        ob_start();
        imagepng($gd);
        $pngBytes = ob_get_clean();
        unset($gd);

        // Inject a PNG tEXt metadata chunk containing GPS/camera strings before IEND
        $keyword = "GPSLocation\0Cairo-30.0444N-31.2357E-CameraMake";
        $chunkLen = pack('N', strlen($keyword));
        $chunkType = 'tEXt';
        $crc = pack('N', crc32($chunkType . $keyword));
        $metaChunk = $chunkLen . $chunkType . $keyword . $crc;
        $iendPos = strrpos($pngBytes, 'IEND') - 4;
        $dirtyPng = substr($pngBytes, 0, $iendPos) . $metaChunk . substr($pngBytes, $iendPos);

        $uploaded = UploadedFile::fake()->createWithContent('gps-photo.png', $dirtyPng);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $uploaded)
            ->assertHasNoErrors();

        $this->invitation->refresh();
        $coverRel = app(InvitationImageService::class)->extractPublicDiskPath($this->invitation->cover_photo);
        $webpBytes = Storage::disk('public')->get($coverRel);

        $this->assertStringNotContainsString('GPSLocation', $webpBytes);
        $this->assertStringNotContainsString('Cairo-30.0444N', $webpBytes);
        $this->assertStringNotContainsString('EXIF', $webpBytes);
    }

    public function test_upload_dispatches_queued_job_when_queue_is_faked(): void
    {
        Queue::fake();
        Storage::fake('public');
        Storage::fake('local');

        $coverFile = UploadedFile::fake()->image('queued-cover.png', 1800, 1200);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $coverFile)
            ->assertHasNoErrors();

        Queue::assertPushed(ProcessInvitationImage::class, function (ProcessInvitationImage $job) {
            return $job->invitationId === $this->invitation->id
                && $job->variant === 'cover'
                && Storage::disk($job->tempDisk)->exists($job->tempPath);
        });

        // Original stays off public disk while queued
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_multi_file_gallery_upload_produces_webp_and_thumbnail_variants_and_deletes_all_variants(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $files = [
            UploadedFile::fake()->image('gallery-1.png', 2000, 1000),
            UploadedFile::fake()->image('gallery-2.png', 1500, 1000),
        ];

        $component = Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_gallery_photos', $files)
            ->assertHasNoErrors();

        $photos = InvitationPhoto::where('invitation_id', $this->invitation->id)->orderBy('id')->get();
        $this->assertCount(2, $photos);

        foreach ($photos as $photo) {
            $this->assertStringEndsWith('.webp', $photo->image_path);
            $this->assertStringEndsWith('.webp', $photo->thumbnail_path);
            $this->assertSame(1200, $photo->width);
            $this->assertSame(400, $photo->thumbnail_width);

            $mainRel = app(InvitationImageService::class)->extractPublicDiskPath($photo->image_path);
            $thumbRel = app(InvitationImageService::class)->extractPublicDiskPath($photo->thumbnail_path);

            Storage::disk('public')->assertExists($mainRel);
            Storage::disk('public')->assertExists($thumbRel);

            $thumbInfo = getimagesizefromstring(Storage::disk('public')->get($thumbRel));
            $this->assertSame('image/webp', $thumbInfo['mime']);
            $this->assertSame(400, $thumbInfo[0]);
        }

        // Delete first photo via Livewire and assert both main + thumb variants are deleted
        $first = $photos->first();
        $firstMainRel = app(InvitationImageService::class)->extractPublicDiskPath($first->image_path);
        $firstThumbRel = app(InvitationImageService::class)->extractPublicDiskPath($first->thumbnail_path);

        $component->call('deleteGalleryPhoto', $first->id);

        Storage::disk('public')->assertMissing($firstMainRel);
        Storage::disk('public')->assertMissing($firstThumbRel);

        // Delete remaining invitation via DashboardController::destroy() and assert remaining variants are cleaned up
        $second = $photos->last();
        $secondMainRel = app(InvitationImageService::class)->extractPublicDiskPath($second->image_path);
        $secondThumbRel = app(InvitationImageService::class)->extractPublicDiskPath($second->thumbnail_path);

        $this->actingAs($this->user)
            ->delete(route('dashboard.invitations.destroy', $this->invitation->id))
            ->assertRedirect(route('dashboard.invitations.index'));

        Storage::disk('public')->assertMissing($secondMainRel);
        Storage::disk('public')->assertMissing($secondThumbRel);
    }

    public function test_remove_cover_photo_deletes_cover_and_cached_og_variants(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $coverFile = UploadedFile::fake()->image('cover.png', 1800, 1200);

        $component = Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $coverFile);

        $this->invitation->refresh();
        $coverRel = app(InvitationImageService::class)->extractPublicDiskPath($this->invitation->cover_photo);
        Storage::disk('public')->assertExists($coverRel);

        // Touch OG cache file
        $ogUrl = $this->invitation->og_image_url;
        $this->assertNotEmpty($ogUrl);

        $component->call('removeCoverPhoto');

        $this->invitation->refresh();
        $this->assertNull($this->invitation->cover_photo);
        $this->assertNull($this->invitation->cover_photo_width);
        Storage::disk('public')->assertMissing($coverRel);
        $this->assertEmpty(Storage::disk('public')->files('invitations/og'));
    }

    public function test_gallery_limit_and_mime_and_size_validation_are_enforced(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        config()->set('invitations.images.max_gallery_photos', 2);

        $valid1 = UploadedFile::fake()->image('one.png', 600, 600);
        $valid2 = UploadedFile::fake()->image('two.png', 600, 600);
        $valid3 = UploadedFile::fake()->image('three.png', 600, 600);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_gallery_photos', [$valid1, $valid2])
            ->assertHasNoErrors();

        $this->assertSame(2, InvitationPhoto::where('invitation_id', $this->invitation->id)->count());

        // Third photo exceeds configured limit of 2
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_gallery_photos', [$valid3])
            ->assertHasErrors(['new_gallery_photos']);

        $this->assertSame(2, InvitationPhoto::where('invitation_id', $this->invitation->id)->count());

        // Disallowed MIME (PDF disguised or real PDF)
        $invalidMime = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $invalidMime)
            ->assertHasErrors(['new_cover_photo']);

        // Oversized (> 5MB)
        $oversized = UploadedFile::fake()->create('huge.png', 6000, 'image/png');
        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $oversized)
            ->assertHasErrors(['new_cover_photo']);
    }

    public function test_public_invitation_renders_webp_dimensions_and_lazy_loading_for_gallery_but_not_hero(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $coverFile = UploadedFile::fake()->image('hero.png', 1800, 1200);
        $galleryFile = UploadedFile::fake()->image('album.png', 1600, 1200);

        Livewire::actingAs($this->user)
            ->test(InvitationEditor::class, ['id' => $this->invitation->id])
            ->set('new_cover_photo', $coverFile)
            ->set('new_gallery_photo', $galleryFile);

        InvitationSection::create([
            'invitation_id' => $this->invitation->id,
            'section_type' => 'gallery',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $this->invitation->refresh();
        $photo = InvitationPhoto::where('invitation_id', $this->invitation->id)->firstOrFail();

        $response = $this->get(route('invitation.show', $this->invitation->slug));
        $response->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString($this->invitation->cover_photo, $html);
        $this->assertStringContainsString('width="1600"', $html);
        $this->assertStringContainsString('height="1067"', $html);

        $this->assertStringContainsString($photo->thumbnail_url, $html);
        $this->assertStringContainsString('width="400"', $html);
        $this->assertStringContainsString('height="300"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);

        // Hero cover img tag must NOT have loading="lazy"
        $this->assertDoesNotMatchRegularExpression(
            '/<img[^>]*' . preg_quote($this->invitation->cover_photo, '/') . '[^>]*loading="lazy"/i',
            $html
        );
    }
}
