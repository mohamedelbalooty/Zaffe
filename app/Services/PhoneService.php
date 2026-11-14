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
    public static function normalize(?string $phone, string $countryCode = 'EG'): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        // Convert Eastern Arabic numerals to standard digits
        $normalized = strtr($phone, self::ARABIC_INDIC_DIGITS);

        // Check if starts with a plus
        $hasLeadingPlus = str_starts_with(trim($normalized), '+');

        // Remove all non-numeric characters
        $digits = preg_replace('/[^\d]/', '', $normalized) ?? '';

        if ($digits === '') {
            return null;
        }

        $country = strtoupper(trim($countryCode));

        // Auto-detect country if international prefix is present
        if (str_starts_with($digits, '20') && (strlen($digits) === 12 || $hasLeadingPlus)) {
            $country = 'EG';
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '966') && (strlen($digits) === 12 || $hasLeadingPlus)) {
            $country = 'SA';
            $digits = substr($digits, 3);
        }

        // Strip leading zeros
        $digits = ltrim($digits, '0');

        if ($country === 'SA') {
            // Saudi Arabia: local mobile starts with 5 and has 9 digits
            if (strlen($digits) === 9 && str_starts_with($digits, '5')) {
                return '+966' . $digits;
            }
            // If already prefixed with 966
            if (str_starts_with($digits, '9665') && strlen($digits) === 12) {
                return '+' . $digits;
            }
            return '+966' . $digits;
        }

        // Default: Egypt (EG)
        // Egyptian mobile starts with 10, 11, 12, or 15 and has 10 digits
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '+20' . $digits;
        }
        if (str_starts_with($digits, '201') && strlen($digits) === 12) {
            return '+' . $digits;
        }

        // Fallback for general E.164
        if ($hasLeadingPlus) {
            return '+' . $digits;
        }

        return '+20' . $digits;
    }

    /**
     * Validate whether a phone number is a valid Egyptian or Saudi mobile number.
     */
    public static function isValid(?string $phone, string $countryCode = 'EG'): bool
    {
        if ($phone === null || trim($phone) === '') {
            return false;
        }

        $normalized = self::normalize($phone, $countryCode);
        if ($normalized === null) {
            return false;
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

        $normalized = self::normalize($phone);
        if ($normalized === null) {
            return $phone;
        }

        if (str_starts_with($normalized, '+20') && strlen($normalized) === 13) {
            // +20 100 123 4567
            return '+20 ' . substr($normalized, 3, 3) . ' ' . substr($normalized, 6, 3) . ' ' . substr($normalized, 9);
        }

        if (str_starts_with($normalized, '+966') && strlen($normalized) === 13) {
            // +966 50 123 4567
            return '+966 ' . substr($normalized, 4, 2) . ' ' . substr($normalized, 6, 3) . ' ' . substr($normalized, 9);
        }

        return $normalized;
    }
}

