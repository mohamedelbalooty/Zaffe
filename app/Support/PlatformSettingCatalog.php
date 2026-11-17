<?php

namespace App\Support;

use Illuminate\Support\Str;

class PlatformSettingCatalog
{
    /**
     * Canonical definitions for Zaffe's configurable platform settings.
     *
     * @return array<string, array{group: string, type: string, dir: string, default: string}>
     */
    public static function definitions(): array
    {
        return [
            'site_name_ar' => [
                'group' => 'general',
                'type' => 'text',
                'dir' => 'rtl',
                'default' => 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة',
            ],
            'site_name_en' => [
                'group' => 'general',
                'type' => 'text',
                'dir' => 'ltr',
                'default' => 'Zaffe — Luxury Digital Wedding Invitations',
            ],
            'contact_email' => [
                'group' => 'contact',
                'type' => 'email',
                'dir' => 'ltr',
                'default' => 'support@zaffe.app',
            ],
            'contact_phone' => [
                'group' => 'contact',
                'type' => 'phone',
                'dir' => 'ltr',
                'default' => '+20 100 000 0000',
            ],
            'whatsapp_support' => [
                'group' => 'contact',
                'type' => 'whatsapp',
                'dir' => 'ltr',
                'default' => '+201000000000',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::definitions());
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::definitions());
    }

    public static function labelFor(string $key): string
    {
        $translationKey = "admin.settings.items.{$key}.label";
        $translated = __($translationKey);

        if ($translated !== $translationKey) {
            return $translated;
        }

        return Str::headline($key);
    }

    public static function descriptionFor(string $key): ?string
    {
        $translationKey = "admin.settings.items.{$key}.description";
        $translated = __($translationKey);

        return $translated !== $translationKey ? $translated : null;
    }

    public static function placeholderFor(string $key): ?string
    {
        $translationKey = "admin.settings.items.{$key}.placeholder";
        $translated = __($translationKey);

        if ($translated !== $translationKey) {
            return $translated;
        }

        return self::definitions()[$key]['default'] ?? null;
    }

    public static function groupFor(string $key, ?string $fallback = 'general'): string
    {
        return self::definitions()[$key]['group'] ?? ($fallback ?: 'general');
    }

    public static function typeFor(string $key): string
    {
        return self::definitions()[$key]['type'] ?? 'textarea';
    }

    public static function directionFor(string $key): string
    {
        return self::definitions()[$key]['dir'] ?? 'rtl';
    }

    public static function defaultFor(string $key): ?string
    {
        return self::definitions()[$key]['default'] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        $defaults = [];

        foreach (self::definitions() as $key => $definition) {
            $defaults[$key] = $definition['default'];
        }

        return $defaults;
    }

    public static function whatsappPreviewUrl(?string $raw, string $fallback = '201000000000'): string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw) ?: $fallback;

        return 'https://wa.me/'.$digits;
    }
}
