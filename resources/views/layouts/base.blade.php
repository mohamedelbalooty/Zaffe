<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $resolvedTitle = $title ?? 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة';
        $resolvedOgTitle = $ogTitle ?? $resolvedTitle;
        $resolvedDescription = $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.';
        $resolvedOgDescription = $ogDescription ?? $resolvedDescription;
        $resolvedOgUrl = $ogUrl ?? url()->current();
        $resolvedOgImage = $ogImage ?? asset('images/templates/royal-gold.webp');
    @endphp
    <title>{{ $resolvedTitle }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $resolvedOgUrl }}">
    <meta property="og:title" content="{{ $resolvedOgTitle }}">
    <meta property="og:description" content="{{ $resolvedOgDescription }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $resolvedOgTitle }}">
    <meta name="twitter:description" content="{{ $resolvedOgDescription }}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">

    <!-- Google Fonts: Tajawal, Cairo, Amiri, Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-[#FAF7F2] text-[#292521] antialiased selection:bg-[#C6A76B] selection:text-white min-h-screen flex flex-col font-tajawal">
    
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
