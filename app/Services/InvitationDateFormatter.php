<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use Throwable;

class InvitationDateFormatter
{
    public const DEFAULT_LOCALE = 'ar';
    public const DEFAULT_TIMEZONE = 'Africa/Cairo';
    public const DEFAULT_DIGITS = 'latin';

    private const LATIN_TO_ARABIC_INDIC = [
        '0' => '٠',
        '1' => '١',
        '2' => '٢',
        '3' => '٣',
        '4' => '٤',
        '5' => '٥',
        '6' => '٦',
        '7' => '٧',
        '8' => '٨',
        '9' => '٩',
    ];

    private const ARABIC_INDIC_TO_LATIN = [
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
    ];

    private static bool $forceDisableIntl = false;

    /**
     * Optional test resolver for Hijri formatting in environments without ext-intl.
     *
     * @var (callable(Carbon, string, string): ?string)|null
     */
    private static $customHijriResolver = null;

    /**
     * Return all formatted date/time metadata for a wedding invitation.
     *
     * @return array{
     *     locale: string,
     *     dir: string,
     *     timezone: string,
     *     digits: string,
     *     show_hijri: bool,
     *     gregorian_date: string,
     *     hijri_date: ?string,
     *     time: ?string
     * }
     */
    public static function forInvitation(object $invitation): array
    {
        $locale = self::resolveLocale($invitation);
        $timezone = self::resolveTimezone($invitation);
        $digits = self::resolveDigits($invitation);
        $showHijri = self::shouldShowHijri($invitation);

        return [
            'locale' => $locale,
            'dir' => $locale === 'en' ? 'ltr' : 'rtl',
            'timezone' => $timezone,
            'digits' => $digits,
            'show_hijri' => $showHijri,
            'gregorian_date' => self::formatGregorianDate($invitation, $locale, $timezone, $digits),
            'hijri_date' => self::formatHijriDate($invitation, $timezone, $digits, $showHijri, $locale),
            'time' => self::formatTime($invitation, $locale, $timezone, $digits),
        ];
    }

    /**
     * Alias for forInvitation().
     *
     * @return array{
     *     locale: string,
     *     dir: string,
     *     timezone: string,
     *     digits: string,
     *     show_hijri: bool,
     *     gregorian_date: string,
     *     hijri_date: ?string,
     *     time: ?string
     * }
     */
    public static function for(object $invitation): array
    {
        return self::forInvitation($invitation);
    }

    public static function formatGregorianDate(
        object|string|null $invitationOrDate,
        ?string $locale = null,
        ?string $timezone = null,
        ?string $digits = null,
    ): string {
        if ($invitationOrDate === null) {
            return '';
        }

        $isInvitationObject = is_object($invitationOrDate) && !($invitationOrDate instanceof DateTimeInterface);

        if ($isInvitationObject) {
            $locale ??= self::resolveLocale($invitationOrDate);
            $timezone ??= self::resolveTimezone($invitationOrDate);
            $digits ??= self::resolveDigits($invitationOrDate);
        }

        $resolvedLocale = self::normalizeLocale($locale);
        $resolvedTimezone = self::normalizeTimezone($timezone);
        $resolvedDigits = self::normalizeDigits($digits);

        $carbon = self::resolveDateTime($invitationOrDate, null, $resolvedTimezone);
        if ($carbon === null) {
            return '';
        }

        $pattern = $resolvedLocale === 'en' ? 'l, j F Y' : 'l، j F Y';
        $formatted = $carbon->copy()->locale($resolvedLocale)->translatedFormat($pattern);

        return self::applyDigits($formatted, $resolvedDigits);
    }

    public static function formatDate(
        object|string|null $invitationOrDate,
        ?string $locale = null,
        ?string $timezone = null,
        ?string $digits = null,
    ): string {
        return self::formatGregorianDate($invitationOrDate, $locale, $timezone, $digits);
    }

    public static function formatHijriDate(
        object|string|null $invitationOrDate,
        ?string $timezone = null,
        ?string $digits = null,
        ?bool $showHijri = null,
        ?string $locale = null,
    ): ?string {
        if ($invitationOrDate === null) {
            return null;
        }

        $isInvitationObject = is_object($invitationOrDate) && !($invitationOrDate instanceof DateTimeInterface);

        if ($isInvitationObject) {
            $showHijri ??= self::shouldShowHijri($invitationOrDate);
            $timezone ??= self::resolveTimezone($invitationOrDate);
            $digits ??= self::resolveDigits($invitationOrDate);
            $locale ??= self::resolveLocale($invitationOrDate);
        }

        $resolvedShowHijri = $showHijri ?? true;
        if (!$resolvedShowHijri) {
            return null;
        }

        if (self::$forceDisableIntl) {
            return null;
        }

        $resolvedTimezone = self::normalizeTimezone($timezone);
        $resolvedDigits = self::normalizeDigits($digits);
        $resolvedLocale = self::normalizeLocale($locale);

        $carbon = self::resolveDateTime($invitationOrDate, null, $resolvedTimezone);
        if ($carbon === null) {
            return null;
        }

        if (self::$customHijriResolver !== null) {
            $formatted = (self::$customHijriResolver)($carbon, $resolvedTimezone, $resolvedLocale);
            if (!is_string($formatted) || trim($formatted) === '') {
                return null;
            }

            return self::applyDigits($formatted, $resolvedDigits);
        }

        if (!extension_loaded('intl') || !class_exists(IntlDateFormatter::class)) {
            return null;
        }

        try {
            $formatter = new IntlDateFormatter(
                'ar_SA@calendar=islamic-umalqura',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                $resolvedTimezone,
                IntlDateFormatter::TRADITIONAL,
                'd MMMM y هـ',
            );

            $formatted = $formatter->format($carbon->toDateTimeImmutable());
            if ($formatted === false || trim($formatted) === '') {
                return null;
            }

            return self::applyDigits($formatted, $resolvedDigits);
        } catch (Throwable) {
            return null;
        }
    }

    public static function hijriDate(
        object|string|null $invitationOrDate,
        ?string $timezone = null,
        ?string $digits = null,
        ?bool $showHijri = null,
        ?string $locale = null,
    ): ?string {
        return self::formatHijriDate($invitationOrDate, $timezone, $digits, $showHijri, $locale);
    }

    public static function formatTime(
        object|string|null $invitationOrTime,
        ?string $locale = null,
        ?string $timezone = null,
        ?string $digits = null,
    ): ?string {
        if ($invitationOrTime === null) {
            return null;
        }

        $isInvitationObject = is_object($invitationOrTime) && !($invitationOrTime instanceof DateTimeInterface);

        if ($isInvitationObject) {
            $locale ??= self::resolveLocale($invitationOrTime);
            $timezone ??= self::resolveTimezone($invitationOrTime);
            $digits ??= self::resolveDigits($invitationOrTime);

            $rawTime = $invitationOrTime->wedding_time ?? null;
            if ($rawTime === null || trim((string) $rawTime) === '') {
                return null;
            }

            $resolvedTimezone = self::normalizeTimezone($timezone);
            $carbon = self::resolveDateTime($invitationOrTime, (string) $rawTime, $resolvedTimezone);
        } elseif ($invitationOrTime instanceof DateTimeInterface) {
            $resolvedTimezone = self::normalizeTimezone($timezone);
            $carbon = Carbon::instance($invitationOrTime)->setTimezone($resolvedTimezone);
        } else {
            $trimmed = trim($invitationOrTime);
            if ($trimmed === '') {
                return null;
            }

            $resolvedTimezone = self::normalizeTimezone($timezone);
            try {
                if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $trimmed) === 1) {
                    $carbon = Carbon::parse('2026-01-01 ' . $trimmed, $resolvedTimezone);
                } else {
                    $carbon = Carbon::parse($trimmed, $resolvedTimezone)->setTimezone($resolvedTimezone);
                }
            } catch (Throwable) {
                return null;
            }
        }

        if ($carbon === null) {
            return null;
        }

        $resolvedLocale = self::normalizeLocale($locale);
        $resolvedDigits = self::normalizeDigits($digits);

        $isAm = (int) $carbon->format('G') < 12;
        $clock = $carbon->format('g:i');
        $period = $resolvedLocale === 'en'
            ? ($isAm ? 'AM' : 'PM')
            : ($isAm ? 'صباحاً' : 'مساءً');

        return self::applyDigits("{$clock} {$period}", $resolvedDigits);
    }

    public static function formatTimeLabel(object $invitation, string $arPrefix = 'في تمام الساعة'): ?string
    {
        $time = self::formatTime($invitation);
        if ($time === null) {
            return null;
        }

        if (self::resolveLocale($invitation) === 'en') {
            return 'At ' . $time;
        }

        return trim($arPrefix) . ' ' . $time;
    }

    public static function applyDigits(string $value, string $digits = self::DEFAULT_DIGITS): string
    {
        if (self::normalizeDigits($digits) === 'arabic_indic') {
            return strtr($value, self::LATIN_TO_ARABIC_INDIC);
        }

        return strtr($value, self::ARABIC_INDIC_TO_LATIN);
    }

    public static function resolveStyles(object $invitation): array
    {
        $effective = $invitation->effective_styles ?? [];
        $custom = $invitation->custom_styles ?? [];

        if (!is_array($effective)) {
            $effective = [];
        }
        if (!is_array($custom)) {
            $custom = [];
        }

        return array_merge($effective, $custom);
    }

    public static function resolveLocale(object $invitation): string
    {
        $styles = self::resolveStyles($invitation);
        $raw = $invitation->locale
            ?? $invitation->language
            ?? $styles['locale']
            ?? $styles['language']
            ?? self::DEFAULT_LOCALE;

        return self::normalizeLocale(is_string($raw) ? $raw : null);
    }

    public static function resolveTimezone(object $invitation): string
    {
        $styles = self::resolveStyles($invitation);
        $raw = $invitation->timezone
            ?? $styles['timezone']
            ?? self::DEFAULT_TIMEZONE;

        return self::normalizeTimezone(is_string($raw) ? $raw : null);
    }

    public static function resolveDigits(object $invitation): string
    {
        $styles = self::resolveStyles($invitation);
        $raw = $invitation->digits
            ?? $styles['digits']
            ?? $styles['digit_style']
            ?? self::DEFAULT_DIGITS;

        return self::normalizeDigits(is_string($raw) ? $raw : null);
    }

    public static function shouldShowHijri(object $invitation): bool
    {
        $styles = self::resolveStyles($invitation);
        $raw = $invitation->show_hijri
            ?? $styles['show_hijri']
            ?? $styles['hijri_enabled']
            ?? true;

        if (is_bool($raw)) {
            return $raw;
        }

        if (is_string($raw)) {
            return !in_array(strtolower(trim($raw)), ['0', 'false', 'off', 'no', ''], true);
        }

        return (bool) $raw;
    }

    public static function resolveDateTime(
        object|string|null $invitationOrDate,
        ?string $time = null,
        ?string $timezone = null,
    ): ?Carbon {
        if ($invitationOrDate === null) {
            return null;
        }

        $isInvitationObject = is_object($invitationOrDate) && !($invitationOrDate instanceof DateTimeInterface);
        $resolvedTimezone = self::normalizeTimezone(
            $timezone ?? ($isInvitationObject ? self::resolveTimezone($invitationOrDate) : self::DEFAULT_TIMEZONE)
        );

        try {
            if ($isInvitationObject) {
                $rawDate = $invitationOrDate->wedding_date ?? null;
                if ($rawDate === null || $rawDate === '') {
                    return null;
                }

                $rawTime = $time ?? ($invitationOrDate->wedding_time ?? null);
                $dateYmd = $rawDate instanceof DateTimeInterface
                    ? $rawDate->format('Y-m-d')
                    : Carbon::parse((string) $rawDate, $resolvedTimezone)->format('Y-m-d');

                $timeString = ($rawTime !== null && trim((string) $rawTime) !== '')
                    ? trim((string) $rawTime)
                    : '12:00:00';

                return Carbon::parse("{$dateYmd} {$timeString}", $resolvedTimezone);
            }

            if ($invitationOrDate instanceof DateTimeInterface) {
                return Carbon::instance($invitationOrDate)->setTimezone($resolvedTimezone);
            }

            $trimmed = trim($invitationOrDate);
            if ($trimmed === '') {
                return null;
            }

            if ($time !== null && trim($time) !== '') {
                $dateYmd = Carbon::parse($trimmed, $resolvedTimezone)->format('Y-m-d');

                return Carbon::parse($dateYmd . ' ' . trim($time), $resolvedTimezone);
            }

            return Carbon::parse($trimmed, $resolvedTimezone)->setTimezone($resolvedTimezone);
        } catch (Throwable) {
            return null;
        }
    }

    public static function withoutIntl(callable $callback): mixed
    {
        $previous = self::$forceDisableIntl;
        self::$forceDisableIntl = true;

        try {
            return $callback();
        } finally {
            self::$forceDisableIntl = $previous;
        }
    }

    public static function withHijriResolver(?callable $resolver, ?callable $callback = null): mixed
    {
        if ($callback === null) {
            self::$customHijriResolver = $resolver;

            return null;
        }

        $previous = self::$customHijriResolver;
        self::$customHijriResolver = $resolver;

        try {
            return $callback();
        } finally {
            self::$customHijriResolver = $previous;
        }
    }

    private static function normalizeLocale(?string $locale): string
    {
        if ($locale !== null && strtolower(trim($locale)) === 'en') {
            return 'en';
        }

        return self::DEFAULT_LOCALE;
    }

    private static function normalizeTimezone(?string $timezone): string
    {
        if ($timezone === null || trim($timezone) === '') {
            return self::DEFAULT_TIMEZONE;
        }

        $candidate = trim($timezone);

        try {
            new DateTimeZone($candidate);

            return $candidate;
        } catch (Throwable) {
            return self::DEFAULT_TIMEZONE;
        }
    }

    private static function normalizeDigits(?string $digits): string
    {
        if ($digits === null) {
            return self::DEFAULT_DIGITS;
        }

        return match (strtolower(trim($digits))) {
            'arabic_indic', 'arabic-indic', 'arabic', 'ar', 'eastern' => 'arabic_indic',
            default => self::DEFAULT_DIGITS,
        };
    }
}
