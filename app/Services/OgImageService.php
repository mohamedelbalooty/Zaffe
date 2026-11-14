<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Facades\Storage;

class OgImageService
{
    public const TARGET_WIDTH = 1200;

    public const TARGET_HEIGHT = 630;

    public const MAX_BYTES = 300 * 1024;

    public const CACHE_DIR = 'invitations/og';

    /**
     * Resolve and cache a 1200x630 (<= 300KB) Open Graph image URL for the invitation.
     */
    public function forInvitation(Invitation $invitation): string
    {
        $source = $this->resolveLocalSource($invitation->cover_photo);
        if ($source === null) {
            return $this->fallbackUrl($invitation);
        }

        [$sourceBytes, $hash] = $source;
        $disk = Storage::disk('public');

        foreach (['webp', 'jpg'] as $ext) {
            $cachedPath = $this->cachedFilePath($invitation->id, $hash, $ext);
            if ($disk->exists($cachedPath)) {
                return $this->toPublicUrl($disk->url($cachedPath));
            }
        }

        $srcImage = @imagecreatefromstring($sourceBytes);
        if ($srcImage === false) {
            return $this->fallbackUrl($invitation);
        }

        try {
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
            if ($srcWidth <= 0 || $srcHeight <= 0) {
                return $this->fallbackUrl($invitation);
            }

            $canvas = $this->resizeAndCrop($srcImage, $srcWidth, $srcHeight);

            try {
                [$encodedBytes, $extension] = $this->encodeCanvas($canvas);
            } finally {
                unset($canvas);
            }
        } finally {
            unset($srcImage);
        }

        if ($encodedBytes === null || strlen($encodedBytes) > self::MAX_BYTES) {
            return $this->fallbackUrl($invitation);
        }

        $this->cleanupForInvitation($invitation->id);

        $targetPath = $this->cachedFilePath($invitation->id, $hash, $extension);
        $disk->put($targetPath, $encodedBytes);

        return $this->toPublicUrl($disk->url($targetPath));
    }

    /**
     * Return the template thumbnail fallback URL when no local cover photo can be used.
     */
    public function fallbackUrl(Invitation $invitation): string
    {
        $thumbnail = $invitation->template?->thumbnail;

        if (! empty($thumbnail)) {
            return preg_match('#^https?://#i', $thumbnail)
                ? $thumbnail
                : url($thumbnail);
        }

        return asset('images/templates/royal-gold.webp');
    }

    /**
     * Remove any cached OG preview files belonging to the given invitation.
     */
    public function cleanupForInvitation(int $invitationId): void
    {
        $disk = Storage::disk('public');
        if (! $disk->exists(self::CACHE_DIR)) {
            return;
        }

        $prefix = self::CACHE_DIR.'/inv_'.$invitationId.'_';
        foreach ($disk->files(self::CACHE_DIR) as $file) {
            if (str_starts_with($file, $prefix)) {
                $disk->delete($file);
            }
        }
    }

    private function cachedFilePath(int $invitationId, string $hash, string $extension): string
    {
        return self::CACHE_DIR."/inv_{$invitationId}_{$hash}.{$extension}";
    }

    private function toPublicUrl(string $pathOrUrl): string
    {
        return preg_match('#^https?://#i', $pathOrUrl) ? $pathOrUrl : url($pathOrUrl);
    }

    /**
     * Resolve local cover photo bytes and a deterministic content hash without remote HTTP calls.
     *
     * @return array{0: string, 1: string}|null
     */
    private function resolveLocalSource(?string $coverPhoto): ?array
    {
        if (empty($coverPhoto)) {
            return null;
        }

        $disk = Storage::disk('public');
        $relativePath = $this->extractPublicDiskPath($coverPhoto);

        if ($relativePath !== null && $disk->exists($relativePath)) {
            $bytes = $disk->get($relativePath);
            if ($bytes === '') {
                return null;
            }

            $hash = substr(sha1($relativePath.'|'.strlen($bytes).'|'.$disk->lastModified($relativePath)), 0, 16);

            return [$bytes, $hash];
        }

        if (! preg_match('#^https?://#i', $coverPhoto)) {
            $publicRelative = ltrim($coverPhoto, '/');
            $publicFullPath = public_path($publicRelative);
            if (is_file($publicFullPath)) {
                $bytes = file_get_contents($publicFullPath);
                if ($bytes === false || $bytes === '') {
                    return null;
                }

                $hash = substr(sha1($publicRelative.'|'.strlen($bytes).'|'.(string) filemtime($publicFullPath)), 0, 16);

                return [$bytes, $hash];
            }
        }

        return null;
    }

    private function extractPublicDiskPath(string $coverPhoto): ?string
    {
        $disk = Storage::disk('public');
        $diskUrl = rtrim($disk->url(''), '/').'/';
        $appStorageUrl = rtrim(url('/storage'), '/').'/';

        if ($diskUrl !== '/' && str_starts_with($coverPhoto, $diskUrl)) {
            return ltrim(substr($coverPhoto, strlen($diskUrl)), '/');
        }

        if (str_starts_with($coverPhoto, $appStorageUrl)) {
            return ltrim(substr($coverPhoto, strlen($appStorageUrl)), '/');
        }

        if (str_starts_with($coverPhoto, '/storage/')) {
            return ltrim(substr($coverPhoto, strlen('/storage/')), '/');
        }

        if (preg_match('#^https?://#i', $coverPhoto)) {
            $parsed = parse_url($coverPhoto);
            $path = $parsed['path'] ?? '';
            $host = strtolower((string) ($parsed['host'] ?? ''));
            $appHost = strtolower((string) (parse_url(url('/'), PHP_URL_HOST) ?? ''));
            $diskHost = strtolower((string) (parse_url($diskUrl, PHP_URL_HOST) ?? ''));

            if (str_starts_with($path, '/storage/') && ($host === $appHost || $host === $diskHost)) {
                return ltrim(substr($path, strlen('/storage/')), '/');
            }

            return null;
        }

        $trimmed = ltrim($coverPhoto, '/');

        return $disk->exists($trimmed) ? $trimmed : null;
    }

    private function resizeAndCrop(\GdImage $srcImage, int $srcWidth, int $srcHeight): \GdImage
    {
        $targetRatio = self::TARGET_WIDTH / self::TARGET_HEIGHT;
        $srcRatio = $srcWidth / $srcHeight;

        if ($srcRatio > $targetRatio) {
            $cropHeight = $srcHeight;
            $cropWidth = max(1, min($srcWidth, (int) round($srcHeight * $targetRatio)));
            $srcX = (int) floor(($srcWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = max(1, min($srcHeight, (int) round($srcWidth / $targetRatio)));
            $srcX = 0;
            $srcY = (int) floor(($srcHeight - $cropHeight) / 2);
        }

        $canvas = imagecreatetruecolor(self::TARGET_WIDTH, self::TARGET_HEIGHT);
        $background = imagecolorallocate($canvas, 250, 247, 242); // #FAF7F2
        imagefill($canvas, 0, 0, $background);
        imagealphablending($canvas, true);

        imagecopyresampled(
            $canvas,
            $srcImage,
            0,
            0,
            $srcX,
            $srcY,
            self::TARGET_WIDTH,
            self::TARGET_HEIGHT,
            $cropWidth,
            $cropHeight
        );

        return $canvas;
    }

    /**
     * Encode the 1200x630 canvas as WebP or JPEG under MAX_BYTES.
     *
     * @return array{0: string|null, 1: string}
     */
    private function encodeCanvas(\GdImage $canvas): array
    {
        $qualities = [82, 72, 62, 50, 40, 30, 20];

        if (function_exists('imagewebp')) {
            foreach ($qualities as $quality) {
                $bytes = $this->captureImage(static fn () => imagewebp($canvas, null, $quality));
                if ($bytes !== null && strlen($bytes) <= self::MAX_BYTES) {
                    return [$bytes, 'webp'];
                }
            }
        }

        if (function_exists('imagejpeg')) {
            foreach ($qualities as $quality) {
                $bytes = $this->captureImage(static fn () => imagejpeg($canvas, null, $quality));
                if ($bytes !== null && strlen($bytes) <= self::MAX_BYTES) {
                    return [$bytes, 'jpg'];
                }
            }
        }

        return [$this->encodeGdToBaselineJpeg($canvas, self::TARGET_WIDTH, self::TARGET_HEIGHT), 'jpg'];
    }

    private function captureImage(callable $writer): ?string
    {
        ob_start();
        $ok = $writer();
        $data = ob_get_clean();

        if ($ok === false || ! is_string($data) || $data === '') {
            return null;
        }

        return $data;
    }

    /**
     * Pure-PHP JFIF baseline JPEG encoder fallback when GD lacks WebP/JPEG writers.
     */
    private function encodeGdToBaselineJpeg(\GdImage $img, int $width, int $height): string
    {
        $qY = [
            16, 11, 10, 16, 24, 40, 51, 61, 12, 12, 14, 19, 26, 58, 60, 55,
            14, 13, 16, 24, 40, 57, 69, 56, 14, 17, 22, 29, 51, 87, 80, 62,
            18, 22, 37, 56, 68, 109, 103, 77, 24, 35, 55, 64, 81, 104, 113, 92,
            49, 64, 78, 87, 103, 121, 120, 101, 72, 92, 95, 98, 112, 100, 103, 99,
        ];
        $qC = [
            17, 18, 24, 47, 99, 99, 99, 99, 18, 21, 26, 66, 99, 99, 99, 99,
            24, 26, 56, 99, 99, 99, 99, 99, 47, 66, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99,
        ];

        $dcYBits = [0, 1, 5, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0];
        $dcYVals = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $dcYCode = [
            0 => [0, 2], 1 => [2, 3], 2 => [3, 3], 3 => [4, 3], 4 => [5, 3], 5 => [6, 3],
            6 => [14, 4], 7 => [30, 5], 8 => [62, 6], 9 => [126, 7], 10 => [254, 8], 11 => [510, 9],
        ];

        $dcCBits = [0, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0];
        $dcCVals = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $dcCCode = [
            0 => [0, 2], 1 => [1, 2], 2 => [2, 2], 3 => [6, 3], 4 => [14, 4], 5 => [30, 5],
            6 => [62, 6], 7 => [126, 7], 8 => [254, 8], 9 => [510, 9], 10 => [1022, 10], 11 => [2046, 11],
        ];

        $acBits = [1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $acVals = [0x00, 0x01];

        $out = "\xFF\xD8";
        $out .= "\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00";
        $out .= "\xFF\xDB\x00\x43\x00".pack('C*', ...$qY);
        $out .= "\xFF\xDB\x00\x43\x01".pack('C*', ...$qC);
        $out .= "\xFF\xC0\x00\x11\x08".pack('nn', $height, $width)."\x03\x01\x11\x00\x02\x11\x01\x03\x11\x01";
        $out .= "\xFF\xC4".pack('n', 3 + 16 + count($dcYVals))."\x00".pack('C*', ...$dcYBits).pack('C*', ...$dcYVals);
        $out .= "\xFF\xC4".pack('n', 3 + 16 + count($acVals))."\x10".pack('C*', ...$acBits).pack('C*', ...$acVals);
        $out .= "\xFF\xC4".pack('n', 3 + 16 + count($dcCVals))."\x01".pack('C*', ...$dcCBits).pack('C*', ...$dcCVals);
        $out .= "\xFF\xC4".pack('n', 3 + 16 + count($acVals))."\x11".pack('C*', ...$acBits).pack('C*', ...$acVals);
        $out .= "\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00";

        $bitBuf = 0;
        $bitCnt = 0;
        $scan = '';

        $putBits = static function (int $val, int $len) use (&$bitBuf, &$bitCnt, &$scan): void {
            $bitBuf = ($bitBuf << $len) | ($val & ((1 << $len) - 1));
            $bitCnt += $len;
            while ($bitCnt >= 8) {
                $bitCnt -= 8;
                $byte = ($bitBuf >> $bitCnt) & 0xFF;
                $scan .= ($byte === 0xFF) ? "\xFF\x00" : chr($byte);
            }
        };

        $encodeDc = static function (int $diff, array $table) use ($putBits): void {
            $abs = abs($diff);
            $category = 0;
            $tmp = $abs;
            while ($tmp > 0) {
                $category++;
                $tmp >>= 1;
            }
            if ($category > 11) {
                $category = 11;
            }
            [$code, $len] = $table[$category];
            $putBits($code, $len);
            if ($category > 0) {
                $bits = $diff > 0 ? $diff : (($diff - 1) & ((1 << $category) - 1));
                $putBits($bits, $category);
            }
            $putBits(0, 1);
        };

        $prevY = 0;
        $prevCb = 0;
        $prevCr = 0;
        $blocksX = intdiv($width + 7, 8);
        $blocksY = intdiv($height + 7, 8);

        for ($by = 0; $by < $blocksY; $by++) {
            $py = min($height - 1, $by * 8 + 3);
            for ($bx = 0; $bx < $blocksX; $bx++) {
                $px = min($width - 1, $bx * 8 + 3);
                $rgb = imagecolorat($img, $px, $py);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $y = (int) round(0.299 * $r + 0.587 * $g + 0.114 * $b) - 128;
                $cb = (int) round(-0.168736 * $r - 0.331264 * $g + 0.5 * $b);
                $cr = (int) round(0.5 * $r - 0.418688 * $g - 0.081312 * $b);

                $dcY = (int) round(($y * 8) / 16);
                $dcCb = (int) round(($cb * 8) / 17);
                $dcCr = (int) round(($cr * 8) / 17);

                $encodeDc($dcY - $prevY, $dcYCode);
                $prevY = $dcY;

                $encodeDc($dcCb - $prevCb, $dcCCode);
                $prevCb = $dcCb;

                $encodeDc($dcCr - $prevCr, $dcCCode);
                $prevCr = $dcCr;
            }
        }

        if ($bitCnt > 0) {
            $putBits((1 << (8 - $bitCnt)) - 1, 8 - $bitCnt);
        }

        return $out.$scan."\xFF\xD9";
    }
}
