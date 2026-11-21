<?php

declare(strict_types=1);

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ImageOptimizationService
{
    public const MAX_PIXEL_COUNT = 25_000_000;

    private const SAFETY_HEADROOM_BYTES = 16 * 1024 * 1024;

    /**
     * Validate, safely decode, proportionally resize, and store an uploaded image as optimized WebP.
     *
     * @throws ValidationException
     */
    public function storeAsWebp(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxDimension = 1600,
        int $quality = 82,
        ?int $memoryLimitBytesOverride = null,
    ): string {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw ValidationException::withMessages([
                'image' => 'خدمة معالجة الصور غير متوفرة حالياً على الخادم.',
            ]);
        }

        $realPath = $file->getRealPath() ?: $file->getPathname();
        if (! is_string($realPath) || $realPath === '' || ! is_file($realPath) || ! is_readable($realPath)) {
            throw ValidationException::withMessages([
                'image' => 'تعذر قراءة ملف الصورة المرفوع. يرجى المحاولة مرة أخرى.',
            ]);
        }

        $info = @getimagesize($realPath);
        if (! is_array($info)) {
            throw ValidationException::withMessages([
                'image' => 'صيغة الصورة غير مدعومة أو تالفة. يرجى رفع صورة صالحة.',
            ]);
        }

        [$width, $height, $imageType] = [$info[0] ?? 0, $info[1] ?? 0, $info[2] ?? 0];
        if (! in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
            throw ValidationException::withMessages([
                'image' => 'صيغة الصورة غير مدعومة. يرجى رفع صورة بصيغة JPG أو PNG أو WebP أو GIF.',
            ]);
        }

        $this->assertSafeDimensionsAndMemory((int) $width, (int) $height, $memoryLimitBytesOverride);

        $source = $this->createSourceImage($realPath, (int) $imageType);
        if (! $source instanceof GdImage) {
            throw ValidationException::withMessages([
                'image' => 'تعذر معالجة ملف الصورة المرفوع. يرجى اختيار صورة أخرى.',
            ]);
        }

        if ((int) $imageType === IMAGETYPE_JPEG) {
            $source = $this->normalizeJpegOrientation($source, $realPath);
        }

        $canvas = $this->resizeToTrueColorWebpCanvas($source, max(1, $maxDimension));
        unset($source);

        ob_start();
        $encoded = @imagewebp($canvas, null, max(1, min(100, $quality)));
        $webpBytes = ob_get_clean();
        unset($canvas);

        if (! $encoded || ! is_string($webpBytes) || $webpBytes === '') {
            throw ValidationException::withMessages([
                'image' => 'تعذر تحويل الصورة إلى صيغة WebP. يرجى المحاولة مرة أخرى.',
            ]);
        }

        $relativePath = trim($directory, '/').'/'.Str::ulid().'.webp';
        $stored = Storage::disk($disk)->put($relativePath, $webpBytes);

        if (! $stored) {
            throw ValidationException::withMessages([
                'image' => 'تعذر حفظ الصورة المحسّنة على الخادم. يرجى المحاولة مرة أخرى.',
            ]);
        }

        return $relativePath;
    }

    /**
     * Reject decompression bombs and images exceeding safe server decode memory headroom.
     *
     * @throws ValidationException
     */
    public function assertSafeDimensionsAndMemory(int $width, int $height, ?int $memoryLimitBytesOverride = null): void
    {
        if ($width <= 0 || $height <= 0 || ($width * $height) > self::MAX_PIXEL_COUNT) {
            throw ValidationException::withMessages([
                'image' => 'أبعاد الصورة كبيرة جداً. الحد الأقصى المسموح به هو 25 ميجابكسل.',
            ]);
        }

        $limitBytes = $memoryLimitBytesOverride ?? $this->parseIniMemoryLimit((string) ini_get('memory_limit'));
        if ($limitBytes <= 0) {
            return;
        }

        $estimatedDecodeBytes = (int) ceil($width * $height * 4 * 1.8);
        $availableBytes = $limitBytes - memory_get_usage(true) - self::SAFETY_HEADROOM_BYTES;

        if ($memoryLimitBytesOverride !== null) {
            $availableBytes = $memoryLimitBytesOverride - self::SAFETY_HEADROOM_BYTES;
        }

        if ($availableBytes <= 0 || $estimatedDecodeBytes > $availableBytes) {
            throw ValidationException::withMessages([
                'image' => 'أبعاد الصورة تتجاوز الحد الآمن لذاكرة الخادم. يرجى تقليص أبعاد الصورة وإعادة المحاولة.',
            ]);
        }
    }

    private function createSourceImage(string $path, int $imageType): ?GdImage
    {
        $image = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            default => false,
        };

        return $image instanceof GdImage ? $image : null;
    }

    private function normalizeJpegOrientation(GdImage $image, string $path): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $exif = @exif_read_data($path);
            $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

            $angle = match ($orientation) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };

            if ($angle === 0) {
                return $image;
            }

            $rotated = @imagerotate($image, $angle, 0);

            return $rotated instanceof GdImage ? $rotated : $image;
        } catch (Throwable) {
            return $image;
        }
    }

    private function resizeToTrueColorWebpCanvas(GdImage $source, int $maxDimension): GdImage
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        if (! imageistruecolor($source) && function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($source);
        }

        $longest = max($srcW, $srcH);
        if ($longest > $maxDimension) {
            $scale = $maxDimension / $longest;
            $targetW = max(1, (int) round($srcW * $scale));
            $targetH = max(1, (int) round($srcH * $scale));
        } else {
            $targetW = $srcW;
            $targetH = $srcH;
        }

        $canvas = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetW, $targetH, $transparent);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
        imagesavealpha($canvas, true);

        return $canvas;
    }

    private function parseIniMemoryLimit(string $value): int
    {
        $trimmed = strtoupper(trim($value));
        if ($trimmed === '' || $trimmed === '-1') {
            return -1;
        }

        if (! preg_match('/^(\d+)\s*([KMG])?$/', $trimmed, $matches)) {
            return -1;
        }

        $bytes = (int) $matches[1];
        $unit = $matches[2] ?? '';

        return match ($unit) {
            'G' => $bytes * 1024 * 1024 * 1024,
            'M' => $bytes * 1024 * 1024,
            'K' => $bytes * 1024,
            default => $bytes,
        };
    }
}
