@php
    use App\Support\PlatformSettingCatalog;

    $siteNameAr = filled($settings['site_name_ar'] ?? null)
        ? (string) $settings['site_name_ar']
        : (PlatformSettingCatalog::defaultFor('site_name_ar') ?? 'زفّة');
    $siteNameEn = filled($settings['site_name_en'] ?? null)
        ? (string) $settings['site_name_en']
        : (PlatformSettingCatalog::defaultFor('site_name_en') ?? 'Zaffe');
    $contactEmail = filled($settings['contact_email'] ?? null)
        ? (string) $settings['contact_email']
        : (PlatformSettingCatalog::defaultFor('contact_email') ?? 'support@zaffe.app');
    $contactPhone = filled($settings['contact_phone'] ?? null)
        ? (string) $settings['contact_phone']
        : (PlatformSettingCatalog::defaultFor('contact_phone') ?? '+20 100 000 0000');
    $whatsappSupport = filled($settings['whatsapp_support'] ?? null)
        ? (string) $settings['whatsapp_support']
        : (PlatformSettingCatalog::defaultFor('whatsapp_support') ?? '+201000000000');

    $whatsappUrl = PlatformSettingCatalog::whatsappPreviewUrl($whatsappSupport);
@endphp

<div style="display: flex; flex-direction: column; gap: 12px;">
    {{-- Brand Header Preview --}}
    <div style="border-radius: 16px; border: 1px solid rgba(198, 167, 107, 0.38); background: linear-gradient(135deg, #151210 0%, #221E1A 100%); color: #FAF7F2; padding: 16px;">
        <div style="font-size: 11px; font-weight: 700; color: #DFC99E; margin-bottom: 10px;">
            {{ __('admin.settings.preview.navbar_label') }}
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 12px; border: 1.5px solid rgba(198, 167, 107, 0.65); background-color: #1E1A16; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 900; color: #DFC99E; font-size: 16px;">
                ز
            </div>
            <div style="min-width: 0;">
                <div style="font-size: 14px; font-weight: 800; color: #FFFFFF; line-height: 1.4;">
                    {{ $siteNameAr }}
                </div>
                <div style="font-size: 11px; font-weight: 600; color: #DFC99E; margin-top: 2px;" dir="ltr">
                    {{ $siteNameEn }}
                </div>
            </div>
        </div>
    </div>

    {{-- Contact Channels Preview --}}
    <div style="border-radius: 16px; border: 1px solid rgba(198, 167, 107, 0.25); padding: 14px 16px; display: flex; flex-direction: column; gap: 10px;">
        <div style="font-size: 11px; font-weight: 700; opacity: 0.8;">
            {{ __('admin.settings.preview.contact_label') }}
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 12px;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.22);">
                <span style="opacity: 0.78;">{{ __('admin.settings.items.contact_email.label') }}</span>
                <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 600;" dir="ltr">{{ $contactEmail }}</span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.22);">
                <span style="opacity: 0.78;">{{ __('admin.settings.items.contact_phone.label') }}</span>
                <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 600;" dir="ltr">{{ $contactPhone }}</span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(15, 76, 58, 0.30); background-color: rgba(15, 76, 58, 0.08);">
                <span style="color: #0F4C3A; font-weight: 700;">{{ __('admin.settings.preview.whatsapp_link_label') }}</span>
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" dir="ltr" style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; color: #0F4C3A; text-decoration: underline;">
                    {{ $whatsappUrl }}
                </a>
            </div>
        </div>
    </div>

    <p style="font-size: 11px; opacity: 0.72; margin: 0;">
        {{ __('admin.settings.preview.live_hint') }}
    </p>
</div>
