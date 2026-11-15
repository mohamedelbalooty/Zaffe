<?php

namespace App\Support;

final class CsvSanitizer
{
    /**
     * Neutralize leading spreadsheet formula characters in exported CSV cells.
     */
    public static function cell(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        $trimmed = ltrim($value);

        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
