<?php

namespace App\Support;

class TemplateStyleCatalog
{
    public const DEFAULTS = [
        'primary_color' => '#C6A76B',
        'secondary_color' => '#292521',
        'background_color' => '#FAF7F2',
        'card_color' => '#FFFFFF',
        'accent_color' => '#D9B7B0',
        'font_family' => 'Tajawal',
        'border_style' => 'double-gold',
        'ornament_style' => 'royal_crest',
    ];

    /**
     * @var array<int, string>
     */
    public const COLOR_KEYS = [
        'primary_color',
        'secondary_color',
        'background_color',
        'card_color',
        'accent_color',
    ];

    /**
     * @var array<int, string>
     */
    public const KNOWN_KEYS = [
        'primary_color',
        'secondary_color',
        'background_color',
        'card_color',
        'accent_color',
        'font_family',
        'border_style',
        'ornament_style',
    ];

    /**
     * @return array<string, array<string, string>>
     */
    public static function presets(): array
    {
        return [
            'royal_gold' => [
                'primary_color' => '#C6A76B',
                'secondary_color' => '#292521',
                'background_color' => '#FAF7F2',
                'card_color' => '#FFFFFF',
                'accent_color' => '#D9B7B0',
                'font_family' => 'Tajawal',
                'border_style' => 'double-gold',
                'ornament_style' => 'royal_crest',
            ],
            'floral_romance' => [
                'primary_color' => '#D9828A',
                'secondary_color' => '#4A3E3D',
                'background_color' => '#FDF8F7',
                'card_color' => '#FFFFFF',
                'accent_color' => '#8BA888',
                'font_family' => 'Cairo',
                'border_style' => 'floral-wreath',
                'ornament_style' => 'blooming_flowers',
            ],
            'minimal_chic' => [
                'primary_color' => '#1F2937',
                'secondary_color' => '#6B7280',
                'background_color' => '#FAFAFA',
                'card_color' => '#FFFFFF',
                'accent_color' => '#C6A76B',
                'font_family' => 'Tajawal',
                'border_style' => 'clean-line',
                'ornament_style' => 'minimal_dots',
            ],
            'islamic_elegance' => [
                'primary_color' => '#0F4C3A',
                'secondary_color' => '#C6A76B',
                'background_color' => '#F4F7F5',
                'card_color' => '#FFFFFF',
                'accent_color' => '#2D6A4F',
                'font_family' => 'Amiri',
                'border_style' => 'arabesque-lattice',
                'ornament_style' => 'islamic_star',
            ],
            'emerald_night' => [
                'primary_color' => '#E6C687',
                'secondary_color' => '#E2E8F0',
                'background_color' => '#0F172A',
                'card_color' => '#1E293B',
                'accent_color' => '#38BDF8',
                'font_family' => 'Cairo',
                'border_style' => 'stardust-glow',
                'ornament_style' => 'constellation',
            ],
            'classic_aristocrat' => [
                'primary_color' => '#8B263E',
                'secondary_color' => '#2B2B2B',
                'background_color' => '#F8F6F0',
                'card_color' => '#FFFFFF',
                'accent_color' => '#D4AF37',
                'font_family' => 'Tajawal',
                'border_style' => 'wax-seal-border',
                'ornament_style' => 'wax_seal',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fonts(?string $current = null): array
    {
        return self::withFallback([
            'Tajawal' => __('admin.template_editor.fonts.Tajawal'),
            'Cairo' => __('admin.template_editor.fonts.Cairo'),
            'Amiri' => __('admin.template_editor.fonts.Amiri'),
            'Playfair Display' => __('admin.template_editor.fonts.Playfair Display'),
        ], $current);
    }

    /**
     * @return array<string, string>
     */
    public static function borderStyles(?string $current = null): array
    {
        return self::withFallback([
            'double-gold' => __('admin.template_editor.borders.double-gold'),
            'floral-wreath' => __('admin.template_editor.borders.floral-wreath'),
            'clean-line' => __('admin.template_editor.borders.clean-line'),
            'arabesque-lattice' => __('admin.template_editor.borders.arabesque-lattice'),
            'stardust-glow' => __('admin.template_editor.borders.stardust-glow'),
            'wax-seal-border' => __('admin.template_editor.borders.wax-seal-border'),
        ], $current);
    }

    /**
     * @return array<string, string>
     */
    public static function ornamentStyles(?string $current = null): array
    {
        return self::withFallback([
            'royal_crest' => __('admin.template_editor.ornaments.royal_crest'),
            'blooming_flowers' => __('admin.template_editor.ornaments.blooming_flowers'),
            'minimal_dots' => __('admin.template_editor.ornaments.minimal_dots'),
            'islamic_star' => __('admin.template_editor.ornaments.islamic_star'),
            'constellation' => __('admin.template_editor.ornaments.constellation'),
            'wax_seal' => __('admin.template_editor.ornaments.wax_seal'),
        ], $current);
    }

    /**
     * @return array<int, string>
     */
    public static function bladeViews(): array
    {
        return [
            'templates.royal_gold',
            'templates.floral_romance',
            'templates.minimal_chic',
            'templates.islamic_elegance',
            'templates.emerald_night',
            'templates.classic_aristocrat',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function sanitizeStyles(mixed $styles, array $existingStyles = []): array
    {
        if (! is_array($styles)) {
            return [];
        }

        // Preserve any non-standard custom style tokens saved on legacy records.
        $extraKeys = array_diff_key($existingStyles, array_flip(self::KNOWN_KEYS));
        $merged = array_merge($extraKeys, $styles);

        $clean = [];
        foreach ($merged as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_string($value) && in_array($key, self::COLOR_KEYS, true)) {
                $trimmed = trim($value);
                $clean[$key] = preg_match('/^#[0-9A-Fa-f]{6}$/', $trimmed) === 1
                    ? strtoupper($trimmed)
                    : $trimmed;

                continue;
            }

            $clean[$key] = is_string($value) ? trim($value) : $value;
        }

        return $clean;
    }

    public static function normalizeHex(?string $hex, string $fallback): string
    {
        $candidate = trim((string) $hex);

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $candidate) === 1
            ? strtoupper($candidate)
            : $fallback;
    }

    public static function contrastRatio(string $hexA, string $hexB): float
    {
        $lumA = self::relativeLuminance($hexA);
        $lumB = self::relativeLuminance($hexB);

        $bright = max($lumA, $lumB);
        $dark = min($lumA, $lumB);

        return round(($bright + 0.05) / ($dark + 0.05), 1);
    }

    public static function fontCssStack(?string $fontFamily): string
    {
        return match ($fontFamily) {
            'Cairo' => "'Cairo', sans-serif",
            'Amiri' => "'Amiri', serif",
            'Playfair Display' => "'Playfair Display', Georgia, serif",
            default => "'Tajawal', sans-serif",
        };
    }

    /**
     * @param  array<string, string>  $options
     * @return array<string, string>
     */
    private static function withFallback(array $options, ?string $current): array
    {
        if (filled($current) && ! array_key_exists($current, $options)) {
            $options[$current] = $current;
        }

        return $options;
    }

    private static function relativeLuminance(string $hex): float
    {
        $hex = ltrim(self::normalizeHex($hex, '#000000'), '#');
        $channels = array_map(function (string $pair): float {
            $srgb = hexdec($pair) / 255;

            return $srgb <= 0.03928
                ? $srgb / 12.92
                : (($srgb + 0.055) / 1.055) ** 2.4;
        }, str_split($hex, 2));

        return (0.2126 * $channels[0]) + (0.7152 * $channels[1]) + (0.0722 * $channels[2]);
    }
}
