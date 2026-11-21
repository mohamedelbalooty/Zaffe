<?php

namespace Tests\Unit;

use App\Services\PhoneService;
use PHPUnit\Framework\TestCase;

class PhoneServiceTest extends TestCase
{
    public function test_saudi_is_default_and_handles_local_and_arabic_indic_digits(): void
    {
        $this->assertSame('+966501234567', PhoneService::normalize('0501234567'));
        $this->assertSame('+966501234567', PhoneService::normalize('٠٥٠١٢٣٤٥٦٧', 'SA'));
        $this->assertTrue(PhoneService::isValid('0501234567'));
        $this->assertFalse(PhoneService::isValid('01012345678', 'SA'));
    }

    public function test_egyptian_mobile_normalization_and_validation_remain_intact(): void
    {
        $this->assertSame('+201012345678', PhoneService::normalize('01012345678', 'EG'));
        $this->assertSame('+201198765432', PhoneService::normalize('+20 11 9876 5432', 'EG'));
        $this->assertTrue(PhoneService::isValid('01012345678', 'EG'));
        $this->assertFalse(PhoneService::isValid('01312345678', 'EG'));
    }

    public function test_other_country_accepts_international_e164_without_falling_back_to_egypt(): void
    {
        $this->assertSame('+971501234567', PhoneService::normalize('+971501234567', 'OTHER'));
        $this->assertSame('+447911123456', PhoneService::normalize('00447911123456', 'OTHER'));
        $this->assertSame('+14155552671', PhoneService::normalize('+1 (415) 555-2671', 'OTHER'));
        $this->assertSame('+971501234567', PhoneService::normalize('+٩٧١٥٠١٢٣٤٥٦٧', 'OTHER'));

        $this->assertTrue(PhoneService::isValid('+971501234567', 'OTHER'));
        $this->assertTrue(PhoneService::isValid('00447911123456', 'OTHER'));

        // Missing international prefix must NOT silently coerce into +20 or +966
        $this->assertNull(PhoneService::normalize('501234567', 'OTHER'));
        $this->assertFalse(PhoneService::isValid('501234567', 'OTHER'));
        $this->assertFalse(PhoneService::isValid('+123', 'OTHER'));
    }

    public function test_format_for_display_supports_saudi_egypt_and_other_e164(): void
    {
        $this->assertSame('+966 50 123 4567', PhoneService::formatForDisplay('+966501234567'));
        $this->assertSame('+20 100 123 4567', PhoneService::formatForDisplay('+201001234567'));
        $this->assertSame('+971501234567', PhoneService::formatForDisplay('+971501234567'));
    }
}
