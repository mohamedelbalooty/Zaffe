@php
    use App\Support\TemplateStyleCatalog;

    $primary = TemplateStyleCatalog::normalizeHex($styles['primary_color'] ?? null, TemplateStyleCatalog::DEFAULTS['primary_color']);
    $secondary = TemplateStyleCatalog::normalizeHex($styles['secondary_color'] ?? null, TemplateStyleCatalog::DEFAULTS['secondary_color']);
    $background = TemplateStyleCatalog::normalizeHex($styles['background_color'] ?? null, TemplateStyleCatalog::DEFAULTS['background_color']);
    $card = TemplateStyleCatalog::normalizeHex($styles['card_color'] ?? null, TemplateStyleCatalog::DEFAULTS['card_color']);
    $accent = TemplateStyleCatalog::normalizeHex($styles['accent_color'] ?? null, TemplateStyleCatalog::DEFAULTS['accent_color']);
    $fontFamily = filled($styles['font_family'] ?? null) ? $styles['font_family'] : TemplateStyleCatalog::DEFAULTS['font_family'];
    $borderStyle = filled($styles['border_style'] ?? null) ? $styles['border_style'] : TemplateStyleCatalog::DEFAULTS['border_style'];
    $ornamentStyle = filled($styles['ornament_style'] ?? null) ? $styles['ornament_style'] : TemplateStyleCatalog::DEFAULTS['ornament_style'];

    $fontStack = TemplateStyleCatalog::fontCssStack($fontFamily);
    $contrast = TemplateStyleCatalog::contrastRatio($secondary, $card);

    $ornamentGlyph = match ($ornamentStyle) {
        'blooming_flowers' => '❀',
        'minimal_dots' => '• • •',
        'islamic_star' => '۞',
        'constellation' => '✦ ✧ ✦',
        'wax_seal' => '❦',
        default => '❖',
    };

    $outerBorderCss = match ($borderStyle) {
        'clean-line' => "1px solid {$primary}",
        'stardust-glow' => "1.5px solid {$primary}; box-shadow: 0 0 0 3px {$accent}26, 0 12px 24px -10px rgba(0,0,0,0.28)",
        'floral-wreath' => "2px solid {$accent}",
        default => "2px solid {$primary}",
    };

    $contrastBadgeColor = $contrast >= 4.5 ? '#15803D' : ($contrast >= 3.0 ? '#B45309' : '#B91C1C');
    $contrastBgColor = $contrast >= 4.5 ? '#DCFCE7' : ($contrast >= 3.0 ? '#FEF3C7' : '#FEE2E2');
    $contrastText = $contrast >= 4.5
        ? __('admin.template_editor.preview.contrast_good', ['ratio' => $contrast])
        : ($contrast >= 3.0
            ? __('admin.template_editor.preview.contrast_ok', ['ratio' => $contrast])
            : __('admin.template_editor.preview.contrast_low', ['ratio' => $contrast]));
@endphp

<div style="display: flex; flex-direction: column; gap: 12px;">
    <div style="font-size: 12px; font-weight: 600; color: inherit; opacity: 0.85;">
        {{ __('admin.template_editor.preview.title') }}
    </div>

    <div style="background-color: {{ $background }}; padding: 18px; border-radius: 16px; border: 1px solid rgba(148, 163, 184, 0.28); font-family: {{ $fontStack }}; transition: background-color 180ms ease;">
        <div style="background-color: {{ $card }}; color: {{ $secondary }}; border: {{ $outerBorderCss }}; border-radius: 14px; padding: 18px 16px; text-align: center; position: relative; transition: all 180ms ease;">
            <div style="border: 1px dashed {{ $primary }}66; border-radius: 10px; padding: 16px 12px;">
                <div style="color: {{ $primary }}; font-size: 15px; line-height: 1; margin-bottom: 8px;">
                    {{ $ornamentGlyph }}
                </div>

                <div style="font-size: 11px; color: {{ $accent }}; font-weight: 600; margin-bottom: 6px;">
                    {{ __('admin.template_editor.preview.bismillah') }}
                </div>

                <div style="font-size: 20px; font-weight: 700; color: {{ $primary }}; margin-bottom: 4px;">
                    {{ __('admin.template_editor.preview.sample_couple') }}
                </div>

                <div style="font-size: 12px; color: {{ $secondary }}; opacity: 0.9; margin-bottom: 10px;">
                    {{ __('admin.template_editor.preview.sample_subtitle') }}
                </div>

                <div style="height: 1px; background: linear-gradient(90deg, transparent, {{ $accent }}, transparent); margin: 10px auto; max-width: 140px;"></div>

                <div style="font-size: 11px; color: {{ $secondary }}; opacity: 0.78; margin-bottom: 14px;">
                    {{ __('admin.template_editor.preview.sample_date') }}
                </div>

                <span style="display: inline-block; background-color: {{ $primary }}; color: {{ $card }}; font-size: 11px; font-weight: 700; padding: 6px 16px; border-radius: 9999px;">
                    {{ __('admin.template_editor.preview.sample_cta') }}
                </span>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(148, 163, 184, 0.25);">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 11px; font-weight: 600; opacity: 0.8;">
                {{ __('admin.template_editor.preview.palette_bar') }}
            </span>
            <div style="display: flex; align-items: center; gap: 6px;" dir="ltr">
                @foreach ([$primary, $secondary, $background, $card, $accent] as $swatch)
                    <span title="{{ $swatch }}" style="width: 18px; height: 18px; border-radius: 9999px; background-color: {{ $swatch }}; border: 1px solid rgba(15, 23, 42, 0.25); display: inline-block;"></span>
                @endforeach
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; font-size: 11px;">
            <span style="opacity: 0.78;">{{ __('admin.template_editor.preview.contrast_label') }}</span>
            <span style="background-color: {{ $contrastBgColor }}; color: {{ $contrastBadgeColor }}; font-weight: 600; padding: 2px 8px; border-radius: 9999px;">
                {{ $contrastText }}
            </span>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 11px; opacity: 0.72;" dir="ltr">
            <span>{{ $fontFamily }}</span>
            <span>{{ $borderStyle }} · {{ $ornamentStyle }}</span>
        </div>
    </div>
</div>

