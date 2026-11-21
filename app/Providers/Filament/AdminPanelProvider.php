<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\PlatformStatsOverview;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(fn () => __('admin.brand_name'))
            ->font('Tajawal')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => [
                    50 => '#FAF6EE',
                    100 => '#F3EBD8',
                    200 => '#E5D3AE',
                    300 => '#D5BA84',
                    400 => '#C6A76B',
                    500 => '#9D7B3B',
                    600 => '#7A5A20',
                    700 => '#634818',
                    800 => '#4F3914',
                    900 => '#3C2B10',
                    950 => '#221809',
                ],
                'gray' => Color::Stone,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700;800;900&display=swap" rel="stylesheet">
<style>
    :root:not(.dark) .fi-body {
        background-color: #FAF7F2;
        color: #221E1A;
    }
    :root:not(.dark) .fi-simple-layout {
        background: radial-gradient(circle at top, #F2ECE1 0%, #FAF7F2 68%);
    }
    :root:not(.dark) .fi-simple-main {
        background-color: #FCFBF8;
        border: 1px solid #E5DCD0;
        border-radius: 1.5rem;
        box-shadow: 0 24px 60px rgba(21, 18, 16, 0.08);
    }
    :root:not(.dark) .fi-topbar {
        background-color: rgba(252, 251, 248, 0.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #E5DCD0;
    }
    :root:not(.dark) .fi-sidebar {
        background-color: #151210 !important;
        border-inline-end: 1px solid rgba(198, 167, 107, 0.22);
    }
    :root:not(.dark) .fi-sidebar-header,
    :root:not(.dark) .fi-sidebar-header-ctn {
        background-color: #151210 !important;
        border-bottom: 1px solid rgba(198, 167, 107, 0.16);
    }
    :root:not(.dark) .fi-sidebar .fi-logo {
        color: #DFC99E !important;
        font-family: 'Cairo', 'Tajawal', sans-serif;
        font-weight: 900;
    }
    :root:not(.dark) .fi-sidebar-group-label {
        color: #A89E92 !important;
        font-weight: 700;
    }
    :root:not(.dark) .fi-sidebar-item-button {
        border-radius: 0.75rem;
    }
    :root:not(.dark) .fi-sidebar-item-button:hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
    }
    :root:not(.dark) .fi-sidebar .fi-sidebar-item-label {
        color: #EAE2D6 !important;
    }
    :root:not(.dark) .fi-sidebar .fi-sidebar-item-icon {
        color: #B9AA95 !important;
    }
    :root:not(.dark) .fi-sidebar-item-active > .fi-sidebar-item-button,
    :root:not(.dark) .fi-sidebar-item-active > .fi-sidebar-item-btn,
    :root:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-button,
    :root:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: #FAF7F2 !important;
        box-shadow: inset 0 0 0 1px rgba(198, 167, 107, 0.45);
    }
    :root:not(.dark) .fi-sidebar-item-active .fi-sidebar-item-label,
    :root:not(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-label {
        color: #151210 !important;
        font-weight: 800;
    }
    :root:not(.dark) .fi-sidebar-item-active .fi-sidebar-item-icon,
    :root:not(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
        color: #9D7B3B !important;
    }
    :root:not(.dark) .fi-sidebar-collapse-btn,
    :root:not(.dark) .fi-sidebar-open-btn,
    :root:not(.dark) .fi-sidebar-header button {
        color: #DFC99E !important;
    }
    .fi-header-heading,
    .fi-section-header-heading,
    .fi-wi-stats-overview-stat-value {
        font-family: 'Cairo', 'Tajawal', sans-serif;
        font-variant-numeric: tabular-nums;
    }
    :root:not(.dark) .fi-section,
    :root:not(.dark) .fi-wi-stats-overview-stat,
    :root:not(.dark) .fi-ta-ctn {
        background-color: #FCFBF8;
        border-color: #E5DCD0;
        border-radius: 1.25rem;
        box-shadow: 0 8px 24px rgba(21, 18, 16, 0.04);
    }
    .fi-ta-cell,
    .fi-ta-header-cell {
        padding-inline: 0.75rem;
    }
    .fi-ta-actions {
        gap: 0.375rem;
    }

    /* Operations Monitor Dedicated Layout & Cards (Independent of App Vite CSS) */
    .zaffe-mon-stack {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .zaffe-mon-kpis {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .zaffe-mon-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1280px) {
        .zaffe-mon-kpis {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    .zaffe-mon-card {
        background-color: #FCFBF8;
        border: 1px solid #E5DCD0;
        border-radius: 1.25rem;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 8px 24px rgba(21, 18, 16, 0.04);
    }
    .dark .zaffe-mon-card {
        background-color: rgb(24 24 27);
        border-color: rgb(39 39 42);
    }
    .zaffe-mon-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6E675F;
        margin: 0;
    }
    .dark .zaffe-mon-label {
        color: rgb(161 161 170);
    }
    .zaffe-mon-value {
        margin-top: 0.5rem;
        font-family: 'Cairo', 'Tajawal', sans-serif;
        font-variant-numeric: tabular-nums;
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1.2;
        color: #221E1A;
    }
    .dark .zaffe-mon-value {
        color: #FFFFFF;
    }
    .zaffe-mon-value--emerald { color: #0F4C3A; }
    .zaffe-mon-value--gold { color: #7A5A20; }
    .dark .zaffe-mon-value--emerald { color: #34D399; }
    .dark .zaffe-mon-value--gold { color: #DFC99E; }
    .zaffe-mon-sub {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        color: #6E675F;
    }
    .zaffe-mon-sub--danger {
        color: #BE123C;
        font-weight: 700;
    }
    .zaffe-mon-section-title {
        font-family: 'Cairo', 'Tajawal', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        margin: 0 0 1rem 0;
        color: #221E1A;
    }
    .dark .zaffe-mon-section-title {
        color: #FFFFFF;
    }
    .zaffe-mon-configs {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.875rem;
    }
    @media (min-width: 640px) {
        .zaffe-mon-configs {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    .zaffe-mon-config-tile {
        background-color: #FAF7F2;
        border: 1px solid #E5DCD0;
        border-radius: 0.875rem;
        padding: 0.875rem 1rem;
    }
    .dark .zaffe-mon-config-tile {
        background-color: rgb(39 39 42);
        border-color: rgb(63 63 70);
    }
    .zaffe-mon-mono {
        display: inline-block;
        margin-top: 0.3rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #221E1A;
    }
    .dark .zaffe-mon-mono { color: #FAF7F2; }
    .zaffe-mon-badge-ok { color: #0F4C3A; font-weight: 700; font-size: 0.875rem; margin-top: 0.3rem; display: inline-block; }
    .zaffe-mon-badge-warn { color: #9A5D12; font-weight: 700; font-size: 0.875rem; margin-top: 0.3rem; display: inline-block; }
    .dark .zaffe-mon-badge-ok { color: #34D399; }
    .dark .zaffe-mon-badge-warn { color: #FBBF24; }
    .zaffe-mon-empty {
        border-radius: 0.875rem;
        border: 1px solid rgba(15, 76, 58, 0.2);
        background-color: rgba(15, 76, 58, 0.06);
        padding: 0.85rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #0F4C3A;
    }
    .zaffe-mon-table-wrap {
        overflow-x: auto;
        border: 1px solid #E5DCD0;
        border-radius: 0.875rem;
    }
    .dark .zaffe-mon-table-wrap {
        border-color: rgb(63 63 70);
    }
    .zaffe-mon-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
        text-align: start;
    }
    .zaffe-mon-table thead {
        background-color: #FAF7F2;
        border-bottom: 1px solid #E5DCD0;
    }
    .dark .zaffe-mon-table thead {
        background-color: rgb(39 39 42);
        border-color: rgb(63 63 70);
    }
    .zaffe-mon-table th {
        padding: 0.7rem 0.9rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #6E675F;
        text-align: start;
        white-space: nowrap;
    }
    .zaffe-mon-table td {
        padding: 0.7rem 0.9rem;
        border-top: 1px solid rgba(229, 220, 208, 0.65);
        vertical-align: top;
    }
    .dark .zaffe-mon-table td {
        border-color: rgb(39 39 42);
    }
    .zaffe-mon-error {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 0.75rem;
        color: #BE123C;
        word-break: break-word;
    }
</style>
HTML)
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                function (): HtmlString {
                    $isArabic = app()->getLocale() === 'ar';
                    $targetLocale = $isArabic ? 'en' : 'ar';
                    $label = $isArabic ? 'English' : 'العربية';
                    $url = route('lang.switch', ['locale' => $targetLocale]);

                    return new HtmlString(
                        '<a href="'.e($url).'" style="display:inline-flex;align-items:center;border-radius:9999px;border:1px solid #DED5C8;background-color:#FFFFFF;padding:0.35rem 0.85rem;font-size:0.75rem;font-weight:700;color:#221E1A;text-decoration:none;">'
                        .'<span>'.e($label).'</span>'
                        .'</a>'
                    );
                }
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                PlatformStatsOverview::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                SetLocale::class,
                SecurityHeaders::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
