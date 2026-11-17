<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class InvitationImageService
{
    public function processCoverFromTemp(Invitation $invitation, string $tempDisk, string $tempPath): void
    {
        try {
            $sourceImage = $this->loadOrientedGdImage($tempDisk, $tempPath);
            $maxWidth = (int) config('invitations.images.cover_max_width', 1600);
            $quality = (int) config('invitations.images.webp_quality', 82);

            [$resized, $width, $height] = $this->resizeToMaxWidth($sourceImage, $maxWidth);
            unset($sourceImage);

            try {
                $webpBytes = $this->encodeWebp($resized, $quality);
            } finally {
                unset($resized);
            }

            $publicDisk = Storage::disk((string) config('invitations.images.public_disk', 'public'));
            $targetPath = 'invitations/covers/' . Str::uuid() . '.webp';
            $publicDisk->put($targetPath, $webpBytes);

            // Clean up any previous cover image and cached OG preview after new variant is safely written
            $this->deleteStorageFile($invitation->cover_photo);
            app(OgImageService::class)->cleanupForInvitation($invitation->id);

            $invitation->update([
                'cover_photo' => $publicDisk->url($targetPath),
                'cover_photo_width' => $width,
                'cover_photo_height' => $height,
            ]);
        } finally {
            $this->deleteTempFile($tempDisk, $tempPath);
        }
    }

    public function processGalleryFromTemp(
        Invitation $invitation,
        string $tempDisk,
        string $tempPath,
        ?int $sortOrder = null
    ): InvitationPhoto {
        try {
            $sourceImage = $this->loadOrientedGdImage($tempDisk, $tempPath);
            $galleryMaxWidth = (int) config('invitations.images.gallery_max_width', 1200);
            $thumbMaxWidth = (int) config('invitations.images.thumbnail_max_width', 400);
            $quality = (int) config('invitations.images.webp_quality', 82);

            [$galleryImg, $width, $height] = $this->resizeToMaxWidth($sourceImage, $galleryMaxWidth);
            [$thumbImg, $thumbWidth, $thumbHeight] = $this->resizeToMaxWidth($sourceImage, $thumbMaxWidth);
            unset($sourceImage);

            try {
                $galleryBytes = $this->encodeWebp($galleryImg, $quality);
                $thumbBytes = $this->encodeWebp($thumbImg, $quality);
            } finally {
                unset($galleryImg, $thumbImg);
            }

            $publicDisk = Storage::disk((string) config('invitations.images.public_disk', 'public'));
            $uuid = (string) Str::uuid();
            $mainPath = "invitations/gallery/{$uuid}.webp";
            $thumbPath = "invitations/gallery/thumbs/{$uuid}.webp";

            $publicDisk->put($mainPath, $galleryBytes);
            $publicDisk->put($thumbPath, $thumbBytes);

            $resolvedOrder = $sortOrder
                ?? ((int) InvitationPhoto::where('invitation_id', $invitation->id)->max('sort_order') + 1);

            return InvitationPhoto::create([
                'invitation_id' => $invitation->id,
                'image_path' => $publicDisk->url($mainPath),
                'thumbnail_path' => $publicDisk->url($thumbPath),
                'width' => $width,
                'height' => $height,
                'thumbnail_width' => $thumbWidth,
                'thumbnail_height' => $thumbHeight,
                'caption' => 'صورة من ألبوم الفرح',
                'sort_order' => $resolvedOrder,
            ]);
        } finally {
            $this->deleteTempFile($tempDisk, $tempPath);
        }
    }

    public function deleteStorageFile(?string $pathOrUrl, ?string $thumbnailPathOrUrl = null): void
    {
        $publicDiskName = (string) config('invitations.images.public_disk', 'public');
        $disk = Storage::disk($publicDiskName);

        $pathsToDelete = [];

        $primaryRelative = $this->extractPublicDiskPath($pathOrUrl);
        if ($primaryRelative !== null) {
            $pathsToDelete[] = $primaryRelative;

            if (str_starts_with($primaryRelative, 'invitations/gallery/') && ! str_starts_with($primaryRelative, 'invitations/gallery/thumbs/')) {
                $filename = basename($primaryRelative);
                $pathsToDelete[] = 'invitations/gallery/thumbs/' . $filename;
                $stem = pathinfo($filename, PATHINFO_FILENAME);
                if ($stem !== '') {
                    $pathsToDelete[] = "invitations/gallery/thumbs/{$stem}.webp";
                }
            }
        }

        $thumbRelative = $this->extractPublicDiskPath($thumbnailPathOrUrl);
        if ($thumbRelative !== null) {
            $pathsToDelete[] = $thumbRelative;
        }

        foreach (array_unique($pathsToDelete) as $relativePath) {
            if ($relativePath !== '' && $disk->exists($relativePath)) {
                $disk->delete($relativePath);
            }
        }
    }

    public function deleteTempFile(string $tempDisk, string $tempPath): void
    {
        if ($tempPath === '') {
            return;
        }

        $disk = Storage::disk($tempDisk);
        if ($disk->exists($tempPath)) {
            $disk->delete($tempPath);
        }
    }

    public function extractPublicDiskPath(?string $pathOrUrl): ?string
    {
        if (empty($pathOrUrl)) {
            return null;
        }

        $disk = Storage::disk((string) config('invitations.images.public_disk', 'public'));
        $diskUrl = rtrim($disk->url(''), '/') . '/';
        $appStorageUrl = rtrim(url('/storage'), '/') . '/';

        $relativePath = $pathOrUrl;
        if ($diskUrl !== '/' && str_starts_with($relativePath, $diskUrl)) {
            $relativePath = substr($relativePath, strlen($diskUrl));
        } elseif (str_starts_with($relativePath, $appStorageUrl)) {
            $relativePath = substr($relativePath, strlen($appStorageUrl));
        } elseif (str_starts_with($relativePath, '/storage/')) {
            $relativePath = substr($relativePath, strlen('/storage/'));
        } elseif (preg_match('#^https?://#i', $relativePath)) {
            $parsed = parse_url($relativePath);
            $path = $parsed['path'] ?? '';
            $host = strtolower((string) ($parsed['host'] ?? ''));
            $appHost = strtolower((string) (parse_url(url('/'), PHP_URL_HOST) ?? ''));
            $diskHost = strtolower((string) (parse_url($diskUrl, PHP_URL_HOST) ?? ''));

            if (str_starts_with($path, '/storage/') && ($host === $appHost || $host === $diskHost)) {
                $relativePath = substr($path, strlen('/storage/'));
            } else {
                return null;
            }
        }

        $relativePath = ltrim($relativePath, '/');

        return $relativePath !== '' ? $relativePath : null;
    }

    private function loadOrientedGdImage(string $tempDisk, string $tempPath): \GdImage
    {
        $disk = Storage::disk($tempDisk);
        if (! $disk->exists($tempPath)) {
            throw new RuntimeException("Temporary image [{$tempPath}] not found on disk [{$tempDisk}].");
        }

        $bytes = $disk->get($tempPath);
        if ($bytes === '') {
            throw new RuntimeException('Uploaded image file is empty.');
        }

        $info = @getimagesizefromstring($bytes);
        $allowedMimes = config('invitations.images.allowed_mime_types', ['image/jpeg', 'image/png', 'image/webp']);
        if ($info === false || ! in_array($info['mime'] ?? '', $allowedMimes, true)) {
            throw new RuntimeException('Unsupported or invalid image MIME type.');
        }

        $orientation = $this->readExifOrientation($bytes);

        $image = @imagecreatefromstring($bytes);
        if ($image === false) {
            throw new RuntimeException('Failed to decode uploaded image with GD.');
        }

        return $this->applyOrientation($image, $orientation);
    }

    private function readExifOrientation(string $bytes): int
    {
        if (! function_exists('exif_read_data')) {
            return 1;
        }

        $stream = @fopen('php://temp', 'r+');
        if ($stream === false) {
            return 1;
        }

        try {
            fwrite($stream, $bytes);
            rewind($stream);
            $exif = @exif_read_data($stream);

            if (is_array($exif) && isset($exif['Orientation'])) {
                return (int) $exif['Orientation'];
            }
        } finally {
            fclose($stream);
        }

        return 1;
    }

    private function applyOrientation(\GdImage $image, int $orientation): \GdImage
    {
        $rotated = match ($orientation) {
            2 => $this->flipImage($image, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($image, 180, 0) ?: $image,
            4 => $this->flipImage($image, IMG_FLIP_VERTICAL),
            5 => $this->rotateAndFlip($image, -90, IMG_FLIP_HORIZONTAL),
            6 => imagerotate($image, -90, 0) ?: $image,
            7 => $this->rotateAndFlip($image, 90, IMG_FLIP_HORIZONTAL),
            8 => imagerotate($image, 90, 0) ?: $image,
            default => $image,
        };

        if ($rotated !== $image) {
            unset($image);
        }

        return $rotated;
    }

    private function flipImage(\GdImage $image, int $mode): \GdImage
    {
        imageflip($image, $mode);

        return $image;
    }

    private function rotateAndFlip(\GdImage $image, int $angle, int $flipMode): \GdImage
    {
        $rotated = imagerotate($image, $angle, 0);
        if ($rotated === false) {
            return $image;
        }

        imageflip($rotated, $flipMode);

        return $rotated;
    }

    /**
     * Resize the image proportionally so its width never exceeds $maxWidth.
     * Always copies into a fresh truecolor canvas so metadata is completely stripped.
     *
     * @return array{0: \GdImage, 1: int, 2: int}
     */
    private function resizeToMaxWidth(\GdImage $src, int $maxWidth): array
    {
        $srcWidth = max(1, imagesx($src));
        $srcHeight = max(1, imagesy($src));

        if ($srcWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = max(1, (int) round($srcHeight * ($maxWidth / $srcWidth)));
        } else {
            $targetWidth = $srcWidth;
            $targetHeight = $srcHeight;
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        imagecopyresampled(
            $canvas,
            $src,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );

        return [$canvas, $targetWidth, $targetHeight];
    }

    private function encodeWebp(\GdImage $image, int $quality): string
    {
        if (function_exists('imagewebp')) {
            if (! imageistruecolor($image)) {
                imagepalettetotruecolor($image);
            }

            ob_start();
            $ok = imagewebp($image, null, $quality);
            $bytes = ob_get_clean();

            if ($ok && is_string($bytes) && $bytes !== '') {
                return $bytes;
            }
        }

        return $this->encodeLosslessVp8lWebp($image);
    }

    /**
     * Pure-PHP lossless VP8L WebP fallback used only if GD was compiled without libwebp.
     */
    private function encodeLosslessVp8lWebp(\GdImage $image): string
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $payload = '';
        $bitBuffer = 0;
        $bitCount = 0;

        $pushBits = static function (int $val, int $len) use (&$payload, &$bitBuffer, &$bitCount): void {
            $bitBuffer |= ($val << $bitCount);
            $bitCount += $len;
            while ($bitCount >= 8) {
                $payload .= chr($bitBuffer & 0xFF);
                $bitBuffer >>= 8;
                $bitCount -= 8;
            }
        };

        $pushBits(0x2F, 8);
        $pushBits($width - 1, 14);
        $pushBits($height - 1, 14);
        $pushBits(1, 1); // alpha_is_used
        $pushBits(0, 3); // version

        $pushBits(0, 1); // no transforms
        $pushBits(0, 1); // no color cache
        $pushBits(0, 1); // no meta huffman

        $write8BitPrefix = static function (int $alphabetSize) use ($pushBits): void {
            $pushBits(0, 1); // normal prefix code
            $pushBits(8, 4); // 12 code-length codes (index 0=17..index 11=8)
            foreach ([0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1] as $len) {
                $pushBits($len, 3);
            }
            $pushBits(0, 1); // use_max_symbol = 0
            for ($s = 0; $s < 256; $s++) {
                $pushBits(1, 1);
            }
            for ($s = 256; $s < $alphabetSize; $s++) {
                $pushBits(0, 1);
            }
        };

        $write8BitPrefix(280); // Green + length
        $write8BitPrefix(256); // Red
        $write8BitPrefix(256); // Blue
        $write8BitPrefix(256); // Alpha

        // Distance code: simple 1-symbol (0)
        $pushBits(1, 1);
        $pushBits(0, 1);
        $pushBits(0, 1);

        // Precompute 8-bit bit-reversal table for fast canonical byte writing
        $rev = [];
        for ($i = 0; $i < 256; $i++) {
            $r = 0;
            for ($b = 0; $b < 8; $b++) {
                if (($i >> $b) & 1) {
                    $r |= (1 << (7 - $b));
                }
            }
            $rev[$i] = $r;
        }

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $a7 = ($rgba >> 24) & 0x7F;
                $a = (int) round((127 - $a7) * 255 / 127);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $pushBits($rev[$g], 8);
                $pushBits($rev[$r], 8);
                $pushBits($rev[$b], 8);
                $pushBits($rev[$a], 8);
            }
        }

        if ($bitCount > 0) {
            $payload .= chr($bitBuffer & 0xFF);
        }

        $chunk = 'VP8L' . pack('V', strlen($payload)) . $payload;
        if (strlen($payload) % 2 === 1) {
            $chunk .= "\x00";
        }

        return 'RIFF' . pack('V', 4 + strlen($chunk)) . 'WEBP' . $chunk;
    }
}
