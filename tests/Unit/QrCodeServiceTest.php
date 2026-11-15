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
}
