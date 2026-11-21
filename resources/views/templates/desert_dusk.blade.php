@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#C86D51';
    $secondaryColor = $styles['secondary_color'] ?? '#2D1E18';
    $bgColor = $styles['background_color'] ?? '#231712';
    $cardColor = $styles['card_color'] ?? '#FBF3EB';
    $accentColor = $styles['accent_color'] ?? '#D99B82';
    $ivoryInk = '#FDF8F2';
    $champagneInk = '#E8C39E';
    $darkPartialCard = '#2E1F19';
    $darkPartialBg = $bgColor;
    $darkPartialPrimary = $primaryColor;
    $darkPartialText = '#FBF3EB';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));

    $groomParts = \App\Services\InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
    $brideParts = $hasBrideName
        ? \App\Services\InvitationDateFormatter::splitCoupleName($invitation->bride_name)
        : ['first' => '', 'lineage' => ''];
    $ceremonyStages = \App\Services\InvitationDateFormatter::ceremonyStages($invitation, $dateInfo);
@endphp

<div class="min-h-screen py-7 sm:py-14 px-3 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 72% 48% at 50% 0%, {{ $primaryColor }}2E 0%, transparent 65%), radial-gradient(ellipse 65% 45% at 50% 100%, #5E4A3F38 0%, transparent 65%); color: {{ $darkPartialText }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Desert Dusk Mocha-Taupe Silk & Sunlit Fold Couture Stationery (Ref Image 8) -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-16px_rgba(0,0,0,0.65)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background: linear-gradient(135deg, #7A6356 0%, #5E4A3F 48%, #705A4D 100%); color: {{ $ivoryInk }};">

            <!-- Layer 1: Diagonal Sunlit Silk Fold Beams & Subtle Desert Dune Contours -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <linearGradient id="duskSunbeam1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FDF8F2" stop-opacity="0.16" />
                        <stop offset="55%" stop-color="#E8C39E" stop-opacity="0.06" />
                        <stop offset="100%" stop-color="#FDF8F2" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="duskSunbeam2" x1="100%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#E8C39E" stop-opacity="0.12" />
                        <stop offset="60%" stop-color="#2D1E18" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="#2D1E18" stop-opacity="0.05" />
                    </linearGradient>
                </defs>

                <!-- Diagonal Sunlit Silk Ribbons (Ref Image 8) -->
                <polygon points="-40,0 260,0 640,560 640,900 320,900 -40,360" fill="url(#duskSunbeam1)" />
                <polygon points="360,0 640,0 640,420 140,900 -40,900 -40,680" fill="url(#duskSunbeam2)" />

                <!-- Embossed Desert Dune Horizons & Crescent Sun -->
                <g fill="none" stroke="{{ $ivoryInk }}" stroke-opacity="0.075" stroke-width="1.1">
                    <circle cx="300" cy="240" r="95" />
                    <path d="M-20 690 Q 170 620, 340 675 T 620 645" />
                    <path d="M-20 740 Q 190 670, 380 725 T 620 695" />
                    <path d="M-20 795 Q 210 735, 410 785 T 620 755" />
                </g>
            </svg>

            <!-- Fine Champagne-Sand Perimeter Frame -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $primaryColor }}80;">
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: #6B564A;"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: #6B564A;"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: #6B564A;"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: #6B564A;"></span>
            </div>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-14 sm:pt-18 pb-16 sm:pb-20 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Luminous Ivory Calligraphic Blessing Crown (Ref Image 8) -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 mx-auto" aria-hidden="true">
                                <line x1="12" y1="10" x2="82" y2="10" stroke="{{ $champagneInk }}" stroke-width="0.85" stroke-opacity="0.7" />
                                <line x1="138" y1="10" x2="208" y2="10" stroke="{{ $champagneInk }}" stroke-width="0.85" stroke-opacity="0.7" />
                                <path d="M94 10 Q 110 2, 126 10 Q 110 18, 94 10 Z" fill="none" stroke="{{ $ivoryInk }}" stroke-width="1.15" stroke-opacity="0.85" />
                                <circle cx="110" cy="10" r="2.2" fill="{{ $champagneInk }}" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7]"
                               style="color: {{ $ivoryInk }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $champagneInk }}80;"></span>
                            <span class="w-2.5 h-2.5 rotate-45" style="background-color: {{ $champagneInk }};"></span>
                            <span class="h-px w-12" style="background-color: {{ $champagneInk }}80;"></span>
                        </div>
                    @endif

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-sm sm:text-base leading-[1.95] max-w-md mx-auto pt-1 opacity-90"
                             style="color: {{ $champagneInk }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif
                </header>

                <!-- Host & Opening Phrase -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-90" style="color: {{ $champagneInk }};">
                        {{ $isEnInv ? 'Under the Golden Desert Twilight' : 'يتشرف الأهل والأحبة' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wide opacity-90" style="color: {{ $ivoryInk }};">
                        {{ $isEnInv ? 'Inviting you to celebrate the wedding evening of' : 'بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Luminous Ivory Side-by-Side Couple Triad -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.06]"
                         aria-hidden="true">
                        @if($hasBrideName)
                            <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap"
                                  style="color: {{ $ivoryInk }};">{{ $brideParts['first'] }}</span>
                        @endif
                        <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap {{ $hasBrideName ? '-mt-2' : '' }}"
                              style="color: {{ $champagneInk }};">{{ $groomParts['first'] }}</span>
                    </div>

                    @if($hasBrideName)
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide"
                                      style="color: {{ $champagneInk }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'كريمة' }}
                                </span>
                                <span class="font-cairo text-[1.7rem] sm:text-[2.7rem] font-black leading-[1.18] break-words w-full"
                                      style="color: {{ $ivoryInk }};"
                                      aria-hidden="true">
                                    {{ $brideParts['first'] }}
                                </span>
                                @if($brideParts['lineage'] !== '')
                                    <span class="font-tajawal text-[11px] sm:text-sm font-medium opacity-85 break-words w-full leading-snug"
                                          style="color: {{ $ivoryInk }};"
                                          aria-hidden="true">
                                        {{ $brideParts['lineage'] }}
                                    </span>
                                @endif
                            </span>

                            <!-- Central Calligraphic Ligature (Ref Image 8) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 48 92" class="w-8 h-16 sm:w-11 sm:h-20 mx-auto overflow-visible">
                                        <path d="M27 16 C 35 16, 38 25, 33 34 C 28 42, 17 42, 14 34 C 11 26, 19 16, 27 16 Z M29 22 C 25 22, 21 26, 22 31 C 23 35, 29 35, 31 30 C 32 27, 31 23, 29 22 Z"
                                              fill="{{ $ivoryInk }}" />
                                        <path d="M34 30 C 36 49, 28 65, 10 78 C 8 80, 6 77, 9 74 C 21 62, 27 49, 27 34 Z"
                                              fill="{{ $champagneInk }}" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $champagneInk }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide"
                                      style="color: {{ $champagneInk }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'الشاب' }}
                                </span>
                                <span class="font-cairo text-[1.7rem] sm:text-[2.7rem] font-black leading-[1.18] break-words w-full"
                                      style="color: {{ $ivoryInk }};"
                                      aria-hidden="true">
                                    {{ $groomParts['first'] }}
                                </span>
                                @if($groomParts['lineage'] !== '')
                                    <span class="font-tajawal text-[11px] sm:text-sm font-medium opacity-85 break-words w-full leading-snug"
                                          style="color: {{ $ivoryInk }};"
                                          aria-hidden="true">
                                        {{ $groomParts['lineage'] }}
                                    </span>
                                @endif
                            </span>
                        </h1>
                    @else
                        <h1 class="relative z-10 flex flex-col items-center space-y-1.5 text-center">
                            <span class="sr-only">{{ $invitation->groom_name }}</span>
                            <span class="font-tajawal text-xs sm:text-sm font-bold tracking-wide"
                                  style="color: {{ $champagneInk }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'ابننا' }}
                            </span>
                            <span class="font-cairo text-3xl sm:text-5xl font-black leading-[1.18] break-words max-w-full"
                                  style="color: {{ $ivoryInk }};"
                                  aria-hidden="true">
                                {{ $groomParts['first'] }}
                            </span>
                            @if($groomParts['lineage'] !== '')
                                <span class="font-tajawal text-xs sm:text-base font-medium opacity-85 break-words max-w-md"
                                      style="color: {{ $ivoryInk }};"
                                      aria-hidden="true">
                                    {{ $groomParts['lineage'] }}
                                </span>
                            @endif
                            <svg viewBox="0 0 200 14" class="w-40 sm:w-52 h-3 pt-1" aria-hidden="true">
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $champagneInk }}" stroke-width="1.6" stroke-opacity="0.8" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 border-2 shadow-md overflow-hidden"
                             style="border-color: {{ $champagneInk }}; background-color: #2D1E18;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-xs sm:text-sm leading-[1.95] px-2 max-w-md mx-auto opacity-90"
                       style="color: {{ $ivoryInk }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Layer 5: Reference Image 8 Split Date/Venue Hairline Ledger -->
                <section class="space-y-4 font-tajawal">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-90" style="color: {{ $champagneInk }};">
                        {{ $isEnInv ? 'By the Grace of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x sm:rtl:divide-x-reverse border-y py-3.5 text-center"
                         style="border-color: {{ $champagneInk }}4D;">
                        <div class="pb-3.5 sm:pb-0 sm:px-4 space-y-1.5 flex flex-col items-center justify-center"
                             style="border-color: {{ $champagneInk }}40;">
                            <x-app-icon name="calendar" class="w-4 h-4 mb-0.5" style="color: {{ $champagneInk }};" />
                            <div class="text-xs sm:text-sm font-extrabold" style="color: {{ $ivoryInk }};">
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </div>
                            @if($dateInfo['hijri_date'])
                                <div class="text-xs font-semibold" style="color: {{ $champagneInk }};">
                                    @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                                </div>
                            @endif
                            @if($dateInfo['time'])
                                <div class="inline-flex items-center gap-1.5 text-xs font-semibold opacity-90 pt-0.5" style="color: {{ $ivoryInk }};">
                                    <x-app-icon name="clock" class="w-3.5 h-3.5 shrink-0" style="color: {{ $champagneInk }};" />
                                    @if($dateInfo['locale'] === 'en')
                                        <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                    @else
                                        <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="pt-3.5 sm:pt-0 sm:px-4 space-y-1.5 flex flex-col items-center justify-center"
                             style="border-color: {{ $champagneInk }}40;">
                            <x-app-icon name="map-pin" class="w-4 h-4 mb-0.5" style="color: {{ $champagneInk }};" />
                            <div class="text-xs sm:text-sm font-extrabold break-words" style="color: {{ $ivoryInk }};">
                                {{ $invitation->venue_name }}
                            </div>
                            @if($invitation->venue_address)
                                <p class="text-[11px] opacity-85 leading-relaxed break-words max-w-xs" style="color: {{ $ivoryInk }};">
                                    {{ $invitation->venue_address }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($invitation->venue_map_url)
                        <div class="pt-1">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-extrabold tracking-wide shadow-sm hover:opacity-95 transition text-white"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0 text-white" />
                                <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                            </a>
                        </div>
                    @endif
                </section>

                <!-- Layer 6: Luminous Ivory 3-Stage Ceremony Columns -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $champagneInk,
                    'secondaryColor' => $ivoryInk,
                    'accentColor' => $accentColor,
                    'cardColor' => '#5E4A3F',
                    'variant' => 'columns',
                ])

                <footer class="pt-2 space-y-2">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-90" style="color: {{ $ivoryInk }};">
                        {{ $isEnInv ? 'By your presence, our joy and happiness are complete' : 'وبحضوركم يتم لنا الفرح والسرور' }}
                    </p>
                    <svg viewBox="0 0 220 26" class="w-36 sm:w-44 h-5 mx-auto" aria-hidden="true">
                        <path d="M18 14 C 58 22, 88 4, 110 12 C 132 20, 166 4, 202 12"
                              fill="none" stroke="{{ $champagneInk }}" stroke-width="1.35" stroke-opacity="0.75" stroke-linecap="round" />
                        <circle cx="110" cy="12" r="2.2" fill="{{ $ivoryInk }}" />
                    </svg>
                    <p class="font-tajawal text-[10px] sm:text-[11px] opacity-70 tracking-wide" style="color: {{ $ivoryInk }};">
                        {{ $isEnInv ? '• Personal Invitation · Honored by Your Presence •' : '• الدعوة شخصية · يسعدنا تشريفكم •' }}
                    </p>
                </footer>
            </div>
        </article>

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
