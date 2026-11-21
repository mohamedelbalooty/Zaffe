<?php

declare(strict_types=1);

namespace App\Services;

class PhoneService
{
    /**
     * Map of Eastern Arabic numerals to Western ASCII digits.
     */
    private const ARABIC_INDIC_DIGITS = [
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ];

    /**
     * Normalize phone number to international E.164 format.
     */
    public static function normalize(?string $phone, string $countryCode = 'SA'): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        // Convert Eastern Arabic numerals to standard digits
        $normalized = trim(strtr($phone, self::ARABIC_INDIC_DIGITS));

        // Treat explicit '+' or international '00' dialing prefix identically
        $hasInternationalPrefix = str_starts_with($normalized, '+') || str_starts_with($normalized, '00');
        $rawForDigits = str_starts_with($normalized, '00')
            ? substr($normalized, 2)
            : $normalized;

        // Remove all non-numeric characters
        $digits = preg_replace('/[^\d]/', '', $rawForDigits) ?? '';

        if ($digits === '') {
            return null;
        }

        $country = strtoupper(trim($countryCode));

        // Respect explicit international prefix for SA/EG or third-country E.164 numbers
        if ($hasInternationalPrefix) {
            if (str_starts_with($digits, '20')) {
                $country = 'EG';
                $digits = substr($digits, 2);
            } elseif (str_starts_with($digits, '966')) {
                $country = 'SA';
                $digits = substr($digits, 3);
            } else {
                $internationalDigits = ltrim($digits, '0');

                return $internationalDigits !== '' ? '+'.$internationalDigits : null;
            }
        } elseif ($country !== 'OTHER') {
            if (str_starts_with($digits, '20') && strlen($digits) === 12) {
                $country = 'EG';
                $digits = substr($digits, 2);
            } elseif (str_starts_with($digits, '966') && strlen($digits) === 12) {
                $country = 'SA';
                $digits = substr($digits, 3);
            }
        }

        if ($country === 'OTHER') {
            return null;
        }

        // Strip leading local trunk zeros
        $digits = ltrim($digits, '0');
        if ($digits === '') {
            return null;
        }

        if ($country === 'SA') {
            // Saudi Arabia: local mobile starts with 5 and has 9 digits
            if (strlen($digits) === 9 && str_starts_with($digits, '5')) {
                return '+966'.$digits;
            }
            if (str_starts_with($digits, '9665') && strlen($digits) === 12) {
                return '+'.$digits;
            }

            return '+966'.$digits;
        }

        if ($country === 'EG') {
            // Egypt: mobile starts with 10, 11, 12, or 15 and has 10 digits
            if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
                return '+20'.$digits;
            }
            if (str_starts_with($digits, '201') && strlen($digits) === 12) {
                return '+'.$digits;
            }

            return '+20'.$digits;
        }

        return null;
    }

    /**
     * Validate whether a phone number is a valid Saudi, Egyptian, or international E.164 mobile number.
     */
    public static function isValid(?string $phone, string $countryCode = 'SA'): bool
    {
        if ($phone === null || trim($phone) === '') {
            return false;
        }

        $country = strtoupper(trim($countryCode));
        $normalized = self::normalize($phone, $country);
        if ($normalized === null) {
            return false;
        }

        if ($country === 'OTHER') {
            return (bool) preg_match('/^\+[1-9]\d{7,14}$/', $normalized);
        }

        // Egypt: +20 followed by 10, 11, 12, or 15 and 8 more digits (+201[0125][0-9]{8})
        if (str_starts_with($normalized, '+20')) {
            return (bool) preg_match('/^\+201[0125]\d{8}$/', $normalized);
        }

        // Saudi Arabia: +966 followed by 5 and 8 more digits (+9665[0-9]{8})
        if (str_starts_with($normalized, '+966')) {
            return (bool) preg_match('/^\+9665\d{8}$/', $normalized);
        }

        // Other international E.164 (min 8, max 15 digits)
        return (bool) preg_match('/^\+[1-9]\d{7,14}$/', $normalized);
    }

    /**
     * Format a normalized E.164 number for display.
     */
    public static function formatForDisplay(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        $trimmed = trim(strtr($phone, self::ARABIC_INDIC_DIGITS));
        $countryHint = str_starts_with($trimmed, '+20') ? 'EG' : (str_starts_with($trimmed, '+966') ? 'SA' : 'OTHER');
        $normalized = self::normalize($trimmed, $countryHint);
        if ($normalized === null) {
            return $phone;
        }

        if (str_starts_with($normalized, '+20') && strlen($normalized) === 13) {
            // +20 100 123 4567
            return '+20 '.substr($normalized, 3, 3).' '.substr($normalized, 6, 3).' '.substr($normalized, 9);
        }

        if (str_starts_with($normalized, '+966') && strlen($normalized) === 13) {
            // +966 50 123 4567
            return '+966 '.substr($normalized, 4, 2).' '.substr($normalized, 6, 3).' '.substr($normalized, 9);
        }

        return $normalized;
    }
}
