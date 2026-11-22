<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invitation;
use App\Support\TemplateStyleCatalog;

class InvitationShareCardService
{
    public const WIDTH = 1080;

    public const HEIGHT = 1350;

    public const ASPECT_RATIO = '4:5';

    public const MIME_TYPE = 'image/png';

    /**
     * Build an ownership-safe, theme-accurate payload for rendering the 4:5 WhatsApp share card.
     *
     * @return array<string, mixed>
     */
    public function buildPayload(Invitation $invitation): array
    {
        $invitation->loadMissing('template');

        $styles = $invitation->effective_styles;
        $dateInfo = InvitationDateFormatter::forInvitation($invitation);
        $isEn = $dateInfo['locale'] === 'en';
        $hasBrideName = $invitation->hasBrideName();

        $groomParts = InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
        $brideParts = $hasBrideName
            ? InvitationDateFormatter::splitCoupleName($invitation->bride_name ?? '')
            : ['first' => '', 'lineage' => ''];

        $allowsRsvp = $invitation->allowsRsvp();

        return [
            'dimensions' => [
                'width' => self::WIDTH,
                'height' => self::HEIGHT,
                'aspect_ratio' => self::ASPECT_RATIO,
                'mime_type' => self::MIME_TYPE,
            ],
            'filename' => 'zaffe-invitation-'.$invitation->slug.'.png',
            'invitation' => [
                'id' => $invitation->id,
                'slug' => $invitation->slug,
                'title' => $invitation->title,
                'status' => $invitation->status,
                'is_published' => $invitation->isPublished(),
                'allows_rsvp' => $allowsRsvp,
                'public_url' => $invitation->public_url,
                'locale' => $dateInfo['locale'],
                'dir' => $dateInfo['dir'],
                'digits' => $dateInfo['digits'],
                'groom_name' => trim((string) ($invitation->groom_name ?? '')),
                'bride_name' => $hasBrideName ? trim((string) $invitation->bride_name) : null,
                'has_bride_name' => $hasBrideName,
                'groom_parts' => $groomParts,
                'bride_parts' => $brideParts,
                'gregorian_date' => $dateInfo['gregorian_date'],
                'hijri_date' => $dateInfo['hijri_date'],
                'time' => $dateInfo['time'],
                'venue_name' => trim((string) ($invitation->venue_name ?? '')),
                'venue_address' => trim((string) ($invitation->venue_address ?? '')),
                'cover_photo' => $this->resolveSameOriginCoverUrl($invitation->cover_photo),
            ],
            'theme' => $this->resolveThemeTokens($invitation, $styles),
            'copy' => [
                'blessing' => $isEn
                    ? 'With joy and gratitude'
                    : 'بارك الله لهما وبارك عليهما وجمع بينهما في خير',
                'eyebrow' => $this->resolveEyebrow($isEn, $hasBrideName),
                'groom_label' => $isEn ? 'THE GROOM' : 'العريس',
                'bride_label' => $isEn ? 'THE BRIDE' : 'العروس',
                'separator' => $isEn ? '&' : 'و',
                'cta_badge' => $allowsRsvp
                    ? ($isEn ? 'Interactive Invitation · Open link for details & RSVP' : 'دعوة رقمية تفاعلية · افتح الرابط للتفاصيل وتأكيد الحضور')
                    : ($isEn ? 'Interactive Invitation · Open link for details & map' : 'دعوة رقمية تفاعلية · افتح الرابط للتفاصيل والموقع'),
                'brand_footer' => $isEn ? 'Crafted with Zaffe' : 'دعوة رقمية عبر منصة زفّــة',
            ],
            'messages' => [
                'ar' => [
                    'text' => $invitation->getWhatsAppShareText('ar'),
                    'whatsapp_url' => $invitation->getWhatsAppShareUrl('ar'),
                ],
                'en' => [
                    'text' => $invitation->getWhatsAppShareText('en'),
                    'whatsapp_url' => $invitation->getWhatsAppShareUrl('en'),
                ],
            ],
        ];
    }

    /**
     * Resolve theme colors, surfaces, typography, and ornamental tokens aligned with the 14 Blade views.
     *
     * @param  array<string, mixed>  $styles
     * @return array<string, mixed>
     */
    private function resolveThemeTokens(Invitation $invitation, array $styles): array
    {
        $template = $invitation->template;
        $viewName = TemplateStyleCatalog::resolveBladeView($template?->view_name);
        $viewKey = str_replace('templates.', '', $viewName);
        $preset = TemplateStyleCatalog::presets()[$viewKey] ?? TemplateStyleCatalog::DEFAULTS;
        $slug = $template?->slug ?? str_replace('_', '-', $viewKey);

        $ornamentStyle = is_string($styles['ornament_style'] ?? null) && $styles['ornament_style'] !== ''
            ? $styles['ornament_style']
            : ($preset['ornament_style'] ?? 'royal_crest');

        $borderStyle = is_string($styles['border_style'] ?? null) && $styles['border_style'] !== ''
            ? $styles['border_style']
            : ($preset['border_style'] ?? 'double-gold');

        $fontFamily = is_string($styles['font_family'] ?? null) && $styles['font_family'] !== ''
            ? $styles['font_family']
            : ($preset['font_family'] ?? 'Tajawal');

        $isSaudiSignature = in_array($slug, TemplateStyleCatalog::saudiSignatureSlugs(), true);

        if ($viewKey === 'desert_dusk') {
            return [
                'template_slug' => $slug,
                'template_key' => $viewKey,
                'template_name_ar' => $template?->name_ar ?? 'غسق الصحراء',
                'template_name_en' => $template?->name_en ?? 'Desert Dusk',
                'ornament_style' => $ornamentStyle,
                'border_style' => $borderStyle,
                'is_dark_theme' => true,
                'is_saudi_signature' => $isSaudiSignature,
                'canvas_bg' => TemplateStyleCatalog::normalizeHex($styles['background_color'] ?? null, '#231712'),
                'card_bg' => '#6B5549',
                'card_bg_alt' => '#5E4A3F',
                'primary_color' => TemplateStyleCatalog::normalizeHex($styles['primary_color'] ?? null, '#C86D51'),
                'secondary_color' => '#FDF8F2',
                'accent_color' => '#E8C39E',
                'ink_color' => '#FDF8F2',
                'muted_ink_color' => '#E8C39E',
                'font_family' => $fontFamily,
            ];
        }

        if ($viewKey === 'emerald_night') {
            return [
                'template_slug' => $slug,
                'template_key' => $viewKey,
                'template_name_ar' => $template?->name_ar ?? 'الزمرد الليلي',
                'template_name_en' => $template?->name_en ?? 'Emerald Night',
                'ornament_style' => $ornamentStyle,
                'border_style' => $borderStyle,
                'is_dark_theme' => true,
                'is_saudi_signature' => $isSaudiSignature,
                'canvas_bg' => TemplateStyleCatalog::normalizeHex($styles['background_color'] ?? null, '#0B1220'),
                'card_bg' => TemplateStyleCatalog::normalizeHex($styles['card_color'] ?? null, '#151F32'),
                'card_bg_alt' => '#0F172A',
                'primary_color' => TemplateStyleCatalog::normalizeHex($styles['primary_color'] ?? null, '#E6C687'),
                'secondary_color' => TemplateStyleCatalog::normalizeHex($styles['secondary_color'] ?? null, '#F1F5F9'),
                'accent_color' => TemplateStyleCatalog::normalizeHex($styles['accent_color'] ?? null, '#38BDF8'),
                'ink_color' => '#F1F5F9',
                'muted_ink_color' => '#CBD5E1',
                'font_family' => $fontFamily,
            ];
        }

        if ($viewKey === 'islamic_elegance') {
            $primaryColor = TemplateStyleCatalog::normalizeHex($styles['primary_color'] ?? null, '#0F4C3A');
            $accentColor = TemplateStyleCatalog::normalizeHex($styles['secondary_color'] ?? null, '#C6A76B');

            return [
                'template_slug' => $slug,
                'template_key' => $viewKey,
                'template_name_ar' => $template?->name_ar ?? 'الأصالة الإسلامية',
                'template_name_en' => $template?->name_en ?? 'Islamic Elegance',
                'ornament_style' => $ornamentStyle,
                'border_style' => $borderStyle,
                'is_dark_theme' => false,
                'is_saudi_signature' => $isSaudiSignature,
                'canvas_bg' => TemplateStyleCatalog::normalizeHex($styles['background_color'] ?? null, '#F4F7F5'),
                'card_bg' => TemplateStyleCatalog::normalizeHex($styles['card_color'] ?? null, '#FFFFFF'),
                'card_bg_alt' => '#F4F7F5',
                'primary_color' => $primaryColor,
                'secondary_color' => '#1E293B',
                'accent_color' => $accentColor,
                'ink_color' => '#1E293B',
                'muted_ink_color' => '#475569',
                'font_family' => $fontFamily,
            ];
        }

        $canvasBg = TemplateStyleCatalog::normalizeHex($styles['background_color'] ?? null, $preset['background_color'] ?? '#FAF7F2');
        $cardBg = TemplateStyleCatalog::normalizeHex($styles['card_color'] ?? null, $preset['card_color'] ?? '#FFFFFF');
        $primaryColor = TemplateStyleCatalog::normalizeHex($styles['primary_color'] ?? null, $preset['primary_color'] ?? '#C6A76B');
        $secondaryColor = TemplateStyleCatalog::normalizeHex($styles['secondary_color'] ?? null, $preset['secondary_color'] ?? '#292521');
        $accentColor = TemplateStyleCatalog::normalizeHex($styles['accent_color'] ?? null, $preset['accent_color'] ?? '#C5A067');

        return [
            'template_slug' => $slug,
            'template_key' => $viewKey,
            'template_name_ar' => $template?->name_ar ?? 'الفخامة الملكية',
            'template_name_en' => $template?->name_en ?? 'Royal Gold',
            'ornament_style' => $ornamentStyle,
            'border_style' => $borderStyle,
            'is_dark_theme' => false,
            'is_saudi_signature' => $isSaudiSignature,
            'canvas_bg' => $canvasBg,
            'card_bg' => $cardBg,
            'card_bg_alt' => $canvasBg,
            'primary_color' => $primaryColor,
            'secondary_color' => $secondaryColor,
            'accent_color' => $accentColor,
            'ink_color' => $secondaryColor,
            'muted_ink_color' => '#5C534A',
            'font_family' => $fontFamily,
        ];
    }

    private function resolveEyebrow(bool $isEn, bool $hasBrideName): string
    {
        if ($isEn) {
            return $hasBrideName
                ? 'Together with their families, joyfully invite you to the wedding of'
                : 'You are cordially invited to celebrate the wedding of';
        }

        return $hasBrideName
            ? 'تتشرف العائلتان الكريمتان بدعوتكم لحفل زفاف'
            : 'نتشرف بدعوتكم لحضور حفل زفاف';
    }

    private function resolveSameOriginCoverUrl(?string $coverPhoto): ?string
    {
        if (! is_string($coverPhoto) || trim($coverPhoto) === '') {
            return null;
        }

        $trimmed = trim($coverPhoto);
        if (str_starts_with($trimmed, '/images/') && ! str_contains($trimmed, '..')) {
            return $trimmed;
        }

        $resolvedDiskPath = Invitation::resolvePublicDiskPath($trimmed);
        if ($resolvedDiskPath !== null) {
            return '/storage/'.$resolvedDiskPath;
        }

        return null;
    }
}
