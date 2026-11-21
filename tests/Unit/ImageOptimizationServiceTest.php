<?php

namespace Tests\Unit;

use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ImageOptimizationServiceTest extends TestCase
{
    public function test_converts_uploaded_jpeg_and_transparent_png_to_optimized_webp(): void
    {
        Storage::fake('public');
        $service = app(ImageOptimizationService::class);

        $jpegUpload = UploadedFile::fake()->image('cover.jpg', 2400, 1200);
        $coverPath = $service->storeAsWebp($jpegUpload, 'invitations/covers');

        $this->assertStringStartsWith('invitations/covers/', $coverPath);
        $this->assertStringEndsWith('.webp', $coverPath);
        Storage::disk('public')->assertExists($coverPath);

        $coverBytes = Storage::disk('public')->get($coverPath);
        $info = @getimagesizefromstring($coverBytes);
        $this->assertIsArray($info);
        $this->assertSame(IMAGETYPE_WEBP, $info[2]);
        $this->assertSame(1600, $info[0]);
        $this->assertSame(800, $info[1]);

        $pngUpload = UploadedFile::fake()->image('gallery.png', 900, 600);
        $galleryPath = $service->storeAsWebp($pngUpload, 'invitations/gallery');
        Storage::disk('public')->assertExists($galleryPath);

        $galleryInfo = @getimagesizefromstring(Storage::disk('public')->get($galleryPath));
        $this->assertIsArray($galleryInfo);
        $this->assertSame(IMAGETYPE_WEBP, $galleryInfo[2]);
        $this->assertSame(900, $galleryInfo[0]);
        $this->assertSame(600, $galleryInfo[1]);
    }

    public function test_rejects_corrupt_or_non_image_payload_safely(): void
    {
        Storage::fake('public');
        $service = app(ImageOptimizationService::class);

        $fakeImage = UploadedFile::fake()->createWithContent('bad.jpg', 'not-really-an-image');

        $this->expectException(ValidationException::class);
        $service->storeAsWebp($fakeImage, 'invitations/covers');
    }

    public function test_rejects_oversized_pixel_dimensions_and_memory_budget_without_decoding(): void
    {
        Storage::fake('public');
        $service = app(ImageOptimizationService::class);

        // Exceeds hard 25MP limit (5001 x 5001 = 25,010,001 pixels)
        try {
            $service->assertSafeDimensionsAndMemory(5001, 5001);
            $this->fail('Expected ValidationException for oversized pixel dimensions.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('image', $e->errors());
        }

        // Valid 2000x2000 image (~28.8MB RGBA decode estimate) rejected cleanly under tight 24MB limit
        $smallEnoughUpload = UploadedFile::fake()->image('tight.png', 2000, 2000);
        $this->expectException(ValidationException::class);
        $service->storeAsWebp($smallEnoughUpload, 'invitations/covers', 'public', 1600, 82, 24 * 1024 * 1024);
    }
}
