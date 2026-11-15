<?php

namespace Tests\Unit;

use App\Services\QrCodeService;
use InvalidArgumentException;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    public function test_renders_valid_svg_for_pass_urls_across_versions(): void
    {
        $service = app(QrCodeService::class);

        $shortSvg = $service->renderSvg('https://zaffe.test/pass/abc123');
        $this->assertStringStartsWith('<svg xmlns="http://www.w3.org/2000/svg"', $shortSvg);
        $this->assertStringContainsString('<rect width="', $shortSvg);
        $this->assertStringContainsString('<path d="', $shortSvg);

        $longPayload = 'https://zaffe.app/pass/'.str_repeat('AbCdEf123456', 10);
        $longSvg = $service->renderSvg($longPayload, 260, '#0F172A', '#FFFFFF', 'Pass');
        $this->assertStringContainsString('width="260" height="260"', $longSvg);
        $this->assertStringContainsString('aria-label="Pass"', $longSvg);
    }

    public function test_matrix_contains_valid_finder_timing_and_dark_module(): void
    {
        $service = app(QrCodeService::class);
        $matrix = $service->encodeMatrix('https://zaffe.app/pass/token_0123456789abcdef0123456789abcdef');
        $size = count($matrix);

        // Version 4 = 33x33, quiet check for square matrix dimensions (17 + 4*v)
        $this->assertSame($size, count($matrix[0]));
        $this->assertSame(0, ($size - 17) % 4);

        // Top-left finder corners and center dot
        $this->assertTrue($matrix[0][0]);
        $this->assertFalse($matrix[1][1]);
        $this->assertTrue($matrix[3][3]);
        $this->assertTrue($matrix[6][6]);
        $this->assertFalse($matrix[7][7]);

        // Top-right and bottom-left finder centers
        $this->assertTrue($matrix[3][$size - 4]);
        $this->assertTrue($matrix[$size - 4][3]);

        // Fixed dark module
        $this->assertTrue($matrix[$size - 8][8]);

        // Timing pattern alternating bits on row 6
        $this->assertTrue($matrix[6][8]);
        $this->assertFalse($matrix[6][9]);
        $this->assertTrue($matrix[6][10]);
    }

    public function test_qr_svg_contains_no_guest_pii_when_encoding_opaque_url(): void
    {
        $service = app(QrCodeService::class);
        $svg = $service->renderSvg('https://zaffe.app/pass/z9kL2pQr8vN4xW7mB1cT6yH3jD5fS0aE');

        $this->assertStringNotContainsString('VIP', $svg);
        $this->assertStringNotContainsString('+966', $svg);
        $this->assertStringNotContainsString('Ahmad', $svg);
    }

    public function test_rejects_empty_or_oversized_payloads(): void
    {
        $service = app(QrCodeService::class);

        $this->expectException(InvalidArgumentException::class);
        $service->renderSvg('');
    }

    public function test_rejects_payload_exceeding_version_10_capacity(): void
    {
        $service = app(QrCodeService::class);

        $this->expectException(InvalidArgumentException::class);
        $service->encodeMatrix(str_repeat('A', 214));
    }

    public function test_encodes_all_version_1_to_10_boundaries_with_4_module_quiet_zone(): void
    {
        $service = app(QrCodeService::class);

        $expectedVersions = [
            1 => 1, 14 => 1,
            15 => 2, 26 => 2,
            27 => 3, 42 => 3,
            43 => 4, 62 => 4,
            63 => 5, 84 => 5,
            85 => 6, 106 => 6,
            107 => 7, 122 => 7,
            123 => 8, 152 => 8,
            153 => 9, 180 => 9,
            181 => 10, 213 => 10,
        ];

        foreach ($expectedVersions as $byteLength => $version) {
            $payload = str_repeat('Z', $byteLength);
            $matrix = $service->encodeMatrix($payload);
            $dim = 17 + (4 * $version);

            $this->assertCount($dim, $matrix, "Failed matrix dimension for {$byteLength} bytes (V{$version})");
            $svg = $service->renderSvg($payload);
            $totalViewBox = $dim + 8;
            $this->assertStringContainsString("viewBox=\"0 0 {$totalViewBox} {$totalViewBox}\"", $svg);
        }
    }

    public function test_escapes_svg_attributes_safely(): void
    {
        $service = app(QrCodeService::class);
        $svg = $service->renderSvg(
            'https://zaffe.app/pass/abc123def456',
            200,
            '#111827',
            '#FFFFFF',
            'Guest "<script>alert(1)</script>"'
        );

        $this->assertStringNotContainsString('<script>', $svg);
        $this->assertStringContainsString('&lt;script&gt;', $svg);
    }
}
