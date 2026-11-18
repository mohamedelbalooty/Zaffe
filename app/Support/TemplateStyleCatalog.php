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
            'diriyah_heritage' => [
                'primary_color' => '#B36A3E',
                'secondary_color' => '#26211D',
                'background_color' => '#F4ECE1',
                'card_color' => '#FBF7F0',
                'accent_color' => '#C5A067',
                'font_family' => 'Amiri',
                'border_style' => 'najdi-crenellation',
                'ornament_style' => 'najdi_triangles',
            ],
            'rawashin_elegance' => [
                'primary_color' => '#14535E',
                'secondary_color' => '#2A211B',
                'background_color' => '#F8F4EC',
                'card_color' => '#FFFFFF',
                'accent_color' => '#C87D65',
                'font_family' => 'Amiri',
                'border_style' => 'rawashin-lattice',
                'ornament_style' => 'hijazi_roshan',
            ],
            'sadu_signature' => [
                'primary_color' => '#7A1F2B',
                'secondary_color' => '#231F20',
                'background_color' => '#F5EFE6',
                'card_color' => '#FFFDF9',
                'accent_color' => '#C85A32',
                'font_family' => 'Cairo',
                'border_style' => 'sadu-woven-band',
                'ornament_style' => 'sadu_lozenge',
            ],
            'taif_rose' => [
                'primary_color' => '#B85D73',
                'secondary_color' => '#3A2D32',
                'background_color' => '#FCF6F5',
                'card_color' => '#FFFFFF',
                'accent_color' => '#7D937A',
                'font_family' => 'Amiri',
                'border_style' => 'botanical-asymmetric',
                'ornament_style' => 'taif_rose_bloom',
            ],
            'palm_oasis' => [
                'primary_color' => '#1B4332',
                'secondary_color' => '#262A24',
                'background_color' => '#F4F1EA',
                'card_color' => '#FCFBF7',
                'accent_color' => '#C5A871',
                'font_family' => 'Cairo',
                'border_style' => 'palm-arch-frame',
                'ornament_style' => 'date_palm_fronds',
            ],
            'red_sea_pearl' => [
                'primary_color' => '#4A7C8C',
                'secondary_color' => '#2C3E47',
                'background_color' => '#F6F8F9',
                'card_color' => '#FFFFFF',
                'accent_color' => '#9EB3BC',
                'font_family' => 'Tajawal',
                'border_style' => 'nacre-inlay',
                'ornament_style' => 'pearl_shell',
            ],
            'desert_dusk' => [
                'primary_color' => '#C86D51',
                'secondary_color' => '#2D1E18',
                'background_color' => '#231712',
                'card_color' => '#FBF3EB',
                'accent_color' => '#D99B82',
                'font_family' => 'Cairo',
                'border_style' => 'dune-horizon',
                'ornament_style' => 'desert_sun_dunes',
            ],
            'modern_monogram' => [
                'primary_color' => '#141413',
                'secondary_color' => '#2B2927',
                'background_color' => '#F5F2EB',
                'card_color' => '#FFFFFF',
                'accent_color' => '#C5A869',
                'font_family' => 'Cairo',
                'border_style' => 'editorial-monogram-grid',
                'ornament_style' => 'couture_monogram',
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
            'najdi-crenellation' => __('admin.template_editor.borders.najdi-crenellation'),
            'rawashin-lattice' => __('admin.template_editor.borders.rawashin-lattice'),
            'sadu-woven-band' => __('admin.template_editor.borders.sadu-woven-band'),
            'botanical-asymmetric' => __('admin.template_editor.borders.botanical-asymmetric'),
            'palm-arch-frame' => __('admin.template_editor.borders.palm-arch-frame'),
            'nacre-inlay' => __('admin.template_editor.borders.nacre-inlay'),
            'dune-horizon' => __('admin.template_editor.borders.dune-horizon'),
            'editorial-monogram-grid' => __('admin.template_editor.borders.editorial-monogram-grid'),
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
            'najdi_triangles' => __('admin.template_editor.ornaments.najdi_triangles'),
            'hijazi_roshan' => __('admin.template_editor.ornaments.hijazi_roshan'),
            'sadu_lozenge' => __('admin.template_editor.ornaments.sadu_lozenge'),
            'taif_rose_bloom' => __('admin.template_editor.ornaments.taif_rose_bloom'),
            'date_palm_fronds' => __('admin.template_editor.ornaments.date_palm_fronds'),
            'pearl_shell' => __('admin.template_editor.ornaments.pearl_shell'),
            'desert_sun_dunes' => __('admin.template_editor.ornaments.desert_sun_dunes'),
            'couture_monogram' => __('admin.template_editor.ornaments.couture_monogram'),
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
            'templates.diriyah_heritage',
            'templates.rawashin_elegance',
            'templates.sadu_signature',
            'templates.taif_rose',
            'templates.palm_oasis',
            'templates.red_sea_pearl',
            'templates.desert_dusk',
            'templates.modern_monogram',
        ];
    }

    public static function resolveBladeView(?string $viewName): string
    {
        if (is_string($viewName) && in_array($viewName, self::bladeViews(), true)) {
            return $viewName;
        }

        return 'templates.royal_gold';
    }

    /**
     * @return array<int, string>
     */
    public static function saudiSignatureSlugs(): array
    {
        return [
            'diriyah-heritage',
            'rawashin-elegance',
            'sadu-signature',
            'taif-rose',
            'palm-oasis',
            'red-sea-pearl',
            'desert-dusk',
            'modern-monogram',
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
