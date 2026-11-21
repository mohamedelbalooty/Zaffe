<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteNameAr = \App\Models\PlatformSetting::get('site_name_ar', 'زفّة');
        $defaultTitle = app()->getLocale() === 'en'
            ? 'Zaffe — Luxury Digital Wedding Invitations'
            : $siteNameAr . ' — منصة دعوات الزفاف الرقمية الفاخرة';
        $rawOgImage = is_string($ogImage ?? null) ? trim($ogImage) : '';
        if ($rawOgImage === '') {
            $resolvedOgImage = url(asset('images/og-zaffe.jpg'));
        } elseif (str_starts_with($rawOgImage, 'http://') || str_starts_with($rawOgImage, 'https://')) {
            $resolvedOgImage = $rawOgImage;
        } else {
            $resolvedOgImage = url('/' . ltrim($rawOgImage, '/'));
        }
    @endphp
    <title>{{ $title ?? $defaultTitle }}</title>
    <meta name="description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? $defaultTitle }}">
    <meta property="og:description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? $defaultTitle }}">
    <meta name="twitter:description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">

    <!-- Google Fonts: Tajawal, Cairo, Amiri, Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>[x-cloak] { display: none !important; }</style>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-[#FAF7F2] text-[#221E1A] antialiased min-h-screen flex flex-col font-tajawal">
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-[100] focus:px-4 focus:py-2.5 focus:rounded-lg focus:bg-[#151210] focus:text-[#FAF7F2] focus:text-xs focus:font-bold focus:shadow-lg">
        {{ app()->getLocale() === 'en' ? 'Skip to main content' : 'تخطي إلى المحتوى الرئيسي' }}
    </a>

    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
