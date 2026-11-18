@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#C86D51';
    $secondaryColor = $styles['secondary_color'] ?? '#2D1E18';
    $bgColor = $styles['background_color'] ?? '#231712';
    $cardColor = $styles['card_color'] ?? '#FBF3EB';
    $accentColor = $styles['accent_color'] ?? '#D99B82';
    $copperDeep = '#9B442A';
    $darkPartialCard = '#2E1F19';
    $darkPartialBg = '#211611';
    $darkPartialPrimary = '#E7B08E';
    $darkPartialText = '#FBF3EB';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(circle at 50% 0%, {{ $primaryColor }}2E 0%, transparent 55%); color: {{ $darkPartialText }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Desert Dusk Layered Horizon & Warm Sand Poster Card -->
        <div class="overflow-hidden shadow-[0_28px_64px_-18px_rgba(0,0,0,0.65)] border"
             style="background-color: {{ $cardColor }}; border-color: {{ $accentColor }}66; color: {{ $secondaryColor }};">

            <!-- Abstract Desert Dunes & Sunset Horizon Artwork -->
            <div class="relative w-full overflow-hidden">
                <svg viewBox="0 0 600 185" class="w-full h-36 sm:h-44 block" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="dusk-sky-{{ $invitation->id ?? 'preview' }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2A1B16" />
                            <stop offset="58%" stop-color="#6F3728" />
                            <stop offset="100%" stop-color="{{ $accentColor }}" />
                        </linearGradient>
                    </defs>
                    <rect width="600" height="185" fill="url(#dusk-sky-{{ $invitation->id ?? 'preview' }})" />
                    <!-- Geometric Desert Sun -->
                    <circle cx="300" cy="88" r="40" fill="#FBF3EB" fill-opacity="0.22" />
                    <circle cx="300" cy="88" r="26" fill="#F3C7A6" />
                    <!-- Distant Dune Ridge -->
                    <path d="M0 118 Q130 82 260 112 T520 100 L600 108 L600 185 L0 185 Z" fill="{{ $copperDeep }}" />
                    <!-- Mid Terracotta Dune -->
                    <path d="M0 140 Q180 102 350 138 T600 124 L600 185 L0 185 Z" fill="{{ $primaryColor }}" />
                    <!-- Foreground Warm Sand Crest Merging Into Card -->
                    <path d="M0 162 Q160 132 330 160 T600 152 L600 185 L0 185 Z" fill="{{ $cardColor }}" />
                </svg>
            </div>

            <!-- Sunlit Warm Sand Body -->
            <div class="px-6 pb-8 pt-2 sm:px-12 sm:pb-12 text-center space-y-6">
                @if($invitation->welcome_message)
                    <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2 max-w-lg mx-auto"
                         style="color: {{ $copperDeep }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </div>
                @endif

                <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide" style="color: {{ $secondaryColor }}; opacity: 0.78;">
                    {{ $isEnInv ? 'As twilight meets golden sand, join us for the wedding of' : 'على ضوء الغسق الذهبي، يسعدنا حضوركم زفاف' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-2.5">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                        style="color: {{ $secondaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>

                    @if($hasBrideName)
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $primaryColor }};"></span>
                            <span class="font-amiri text-2xl font-bold" style="color: {{ $copperDeep }};">{{ $isEnInv ? '&' : 'و' }}</span>
                            <span class="h-px w-12" style="background-color: {{ $primaryColor }};"></span>
                        </div>

                        <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->bride_name }}
                        </h1>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-40 h-40 mx-auto rounded-full p-1.5 border-2 shadow-md overflow-hidden"
                             style="border-color: {{ $primaryColor }}; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-lg mx-auto opacity-85">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Contrast Dark Cocoa Plaque Inside Warm Sand Card -->
                <div class="p-5 sm:p-6 border space-y-3 font-tajawal"
                     style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}; color: {{ $cardColor }};">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $darkPartialPrimary }};">
                        <x-app-icon name="calendar" class="w-5 h-5 shrink-0" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>

                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-bold text-[#F3E2D2]">
                            @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                        </div>
                    @endif

                    @if($dateInfo['time'])
                        <div class="flex items-center justify-center gap-2 text-sm font-semibold text-[#EAD5C2]">
                            <x-app-icon name="clock" class="w-4 h-4 shrink-0" style="color: {{ $darkPartialPrimary }};" />
                            @if($dateInfo['locale'] === 'en')
                                <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                            @else
                                <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-3 border-t border-white/15 space-y-1.5">
                        <div class="flex items-center justify-center gap-2 text-base font-extrabold text-white">
                            <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $darkPartialPrimary }};" />
                            <span class="break-words">{{ $invitation->venue_name }}</span>
                        </div>

                        @if($invitation->venue_address)
                            <p class="text-xs sm:text-sm text-[#EAD5C2] max-w-md mx-auto leading-relaxed break-words">
                                {{ $invitation->venue_address }}
                            </p>
                        @endif

                        @if($invitation->venue_map_url)
                            <div class="pt-2">
                                <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-extrabold shadow-sm hover:opacity-95 transition"
                                   style="background-color: {{ $darkPartialPrimary }}; color: {{ $secondaryColor }};">
                                    <x-app-icon name="map-pin" class="w-4 h-4" />
                                    <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'secondaryColor' => $darkPartialText, 'cardColor' => $darkPartialCard, 'bgColor' => $darkPartialBg, 'isDarkTheme' => true])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'secondaryColor' => $darkPartialText, 'cardColor' => $darkPartialCard, 'bgColor' => $darkPartialBg, 'isDarkTheme' => true])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'secondaryColor' => $darkPartialText, 'cardColor' => $darkPartialCard, 'bgColor' => $darkPartialBg, 'isDarkTheme' => true])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'secondaryColor' => $darkPartialText, 'cardColor' => $darkPartialCard, 'bgColor' => $darkPartialBg, 'isDarkTheme' => true])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'secondaryColor' => $darkPartialText, 'cardColor' => $darkPartialCard, 'isDarkTheme' => true])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $darkPartialPrimary, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs text-[#E7C2A8]/80 pt-3 pb-8 font-tajawal">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:text-white">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
