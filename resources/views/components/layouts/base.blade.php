<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة' }}</title>
    <meta name="description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة' }}">
    <meta property="og:description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-zaffe.jpg') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة' }}">
    <meta name="twitter:description" content="{{ $description ?? 'صمم دعوة زفافك الرقمية الفاخرة وشاركها مع ضيوفك برابط مميز مع تأكيد الحضور الفوري والمباشر.' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-zaffe.jpg') }}">

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
