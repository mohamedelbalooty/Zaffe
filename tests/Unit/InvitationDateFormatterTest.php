<?php

namespace Tests\Unit;

use App\Services\InvitationDateFormatter;
use Carbon\Carbon;
use Tests\TestCase;

class InvitationDateFormatterTest extends TestCase
{
    public function test_formats_arabic_gregorian_date_and_time_with_default_latin_digits(): void
    {
        $invitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [],
        ];

        $formatted = InvitationDateFormatter::forInvitation($invitation);

        $this->assertSame('ar', $formatted['locale']);
        $this->assertSame('rtl', $formatted['dir']);
        $this->assertSame('Asia/Riyadh', $formatted['timezone']);
        $this->assertSame('latin', $formatted['digits']);
        $this->assertTrue($formatted['show_hijri']);
        $this->assertSame('الاثنين، 14 ديسمبر 2026', $formatted['gregorian_date']);
        $this->assertSame('8:00 مساءً', $formatted['time']);

        $morningInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '09:30',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [],
        ];

        $this->assertSame('9:30 صباحاً', InvitationDateFormatter::formatTime($morningInvitation));
    }

    public function test_formats_english_gregorian_date_and_time_when_invitation_locale_is_en(): void
    {
        $invitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'custom_styles' => [
                'locale' => 'en',
            ],
        ];

        $formatted = InvitationDateFormatter::forInvitation($invitation);

        $this->assertSame('en', $formatted['locale']);
        $this->assertSame('ltr', $formatted['dir']);
        $this->assertSame('Monday, 14 December 2026', $formatted['gregorian_date']);
        $this->assertSame('8:00 PM', $formatted['time']);

        $this->assertSame(
            '9:30 AM',
            InvitationDateFormatter::formatTime('09:30', 'en', 'Africa/Cairo', 'latin')
        );
    }

    public function test_invitation_language_drives_formatting_regardless_of_viewer_app_locale(): void
    {
        app()->setLocale('en');

        $arabicInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => ['locale' => 'ar'],
        ];

        $this->assertSame('الاثنين، 14 ديسمبر 2026', InvitationDateFormatter::formatGregorianDate($arabicInvitation));
        $this->assertSame('8:00 مساءً', InvitationDateFormatter::formatTime($arabicInvitation));

        app()->setLocale('ar');

        $englishInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'custom_styles' => ['locale' => 'en'],
        ];

        $this->assertSame('Monday, 14 December 2026', InvitationDateFormatter::formatGregorianDate($englishInvitation));
        $this->assertSame('8:00 PM', InvitationDateFormatter::formatTime($englishInvitation));
    }

    public function test_applies_arabic_indic_digits_setting_from_custom_styles(): void
    {
        $invitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'locale' => 'ar',
                'digits' => 'arabic_indic',
            ],
        ];

        $formatted = InvitationDateFormatter::forInvitation($invitation);

        $this->assertSame('arabic_indic', $formatted['digits']);
        $this->assertSame('الاثنين، ١٤ ديسمبر ٢٠٢٦', $formatted['gregorian_date']);
        $this->assertSame('٨:٠٠ مساءً', $formatted['time']);
        $this->assertSame('14 ديسمبر 2026', InvitationDateFormatter::applyDigits('١٤ ديسمبر ٢٠٢٦', 'latin'));
    }

    public function test_respects_africa_cairo_and_asia_riyadh_timezones(): void
    {
        $utcMoment = Carbon::parse('2026-12-14T21:30:00Z');

        $this->assertSame(
            'الاثنين، 14 ديسمبر 2026',
            InvitationDateFormatter::formatGregorianDate($utcMoment, 'ar', 'Africa/Cairo', 'latin')
        );
        $this->assertSame(
            '11:30 مساءً',
            InvitationDateFormatter::formatTime($utcMoment, 'ar', 'Africa/Cairo', 'latin')
        );

        $this->assertSame(
            'الثلاثاء، 15 ديسمبر 2026',
            InvitationDateFormatter::formatGregorianDate($utcMoment, 'ar', 'Asia/Riyadh', 'latin')
        );
        $this->assertSame(
            '12:30 صباحاً',
            InvitationDateFormatter::formatTime($utcMoment, 'ar', 'Asia/Riyadh', 'latin')
        );

        $riyadhInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [],
        ];
        $cairoInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Africa/Cairo',
            'custom_styles' => [],
        ];

        $this->assertSame('Asia/Riyadh', InvitationDateFormatter::resolveDateTime($riyadhInvitation)?->timezoneName);
        $this->assertSame('Africa/Cairo', InvitationDateFormatter::resolveDateTime($cairoInvitation)?->timezoneName);
    }

    public function test_hijri_date_is_hidden_when_show_hijri_is_false(): void
    {
        $invitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'show_hijri' => false,
            ],
        ];

        $this->assertFalse(InvitationDateFormatter::shouldShowHijri($invitation));
        $this->assertNull(InvitationDateFormatter::formatHijriDate($invitation));
        $this->assertNull(InvitationDateFormatter::forInvitation($invitation)['hijri_date']);
    }

    public function test_hijri_date_returns_null_gracefully_when_intl_extension_is_missing(): void
    {
        $invitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'show_hijri' => true,
            ],
        ];

        $formatted = InvitationDateFormatter::withoutIntl(
            fn () => InvitationDateFormatter::forInvitation($invitation)
        );

        $this->assertNull($formatted['hijri_date']);
        $this->assertSame('الاثنين، 14 ديسمبر 2026', $formatted['gregorian_date']);
        $this->assertSame('8:00 مساءً', $formatted['time']);
    }

    public function test_hijri_date_applies_digits_setting_for_umalqura_output(): void
    {
        $latinInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'show_hijri' => true,
                'digits' => 'latin',
            ],
        ];

        $arabicIndicInvitation = (object) [
            'wedding_date' => '2026-12-14',
            'wedding_time' => '20:00',
            'timezone' => 'Asia/Riyadh',
            'custom_styles' => [
                'show_hijri' => true,
                'digits' => 'arabic_indic',
            ],
        ];

        if (extension_loaded('intl') && class_exists(\IntlDateFormatter::class)) {
            $latinHijri = InvitationDateFormatter::formatHijriDate($latinInvitation);
            $arabicHijri = InvitationDateFormatter::formatHijriDate($arabicIndicInvitation);

            $this->assertNotNull($latinHijri);
            $this->assertStringContainsString('هـ', $latinHijri);
            $this->assertMatchesRegularExpression('/\d+/', $latinHijri);
            $this->assertDoesNotMatchRegularExpression('/[٠-٩]/u', $latinHijri);

            $this->assertNotNull($arabicHijri);
            $this->assertStringContainsString('هـ', $arabicHijri);
            $this->assertMatchesRegularExpression('/[٠-٩]+/u', $arabicHijri);
            $this->assertDoesNotMatchRegularExpression('/[0-9]/', $arabicHijri);
        }

        // Verify Umm al-Qura digit transformation pipeline regardless of local CLI ext-intl availability
        InvitationDateFormatter::withHijriResolver(
            fn (Carbon $dt, string $tz) => '٥ رجب ١٤٤٨ هـ',
            function () use ($latinInvitation, $arabicIndicInvitation): void {
                $this->assertSame('5 رجب 1448 هـ', InvitationDateFormatter::formatHijriDate($latinInvitation));
                $this->assertSame('٥ رجب ١٤٤٨ هـ', InvitationDateFormatter::formatHijriDate($arabicIndicInvitation));
            }
        );
    }
}
