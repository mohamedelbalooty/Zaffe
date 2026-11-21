@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#1B4332';
    $secondaryColor = $styles['secondary_color'] ?? '#262A24';
    $bgColor = $styles['background_color'] ?? '#F4F1EA';
    $cardColor = $styles['card_color'] ?? '#FCFBF7';
    $accentColor = $styles['accent_color'] ?? '#C5A871';
    $oliveSage = '#6B7B5C';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));

    $groomParts = \App\Services\InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
    $brideParts = $hasBrideName
        ? \App\Services\InvitationDateFormatter::splitCoupleName($invitation->bride_name)
        : ['first' => '', 'lineage' => ''];
    $ceremonyStages = \App\Services\InvitationDateFormatter::ceremonyStages($invitation, $dateInfo);
    $linenPatternId = 'palm-linen-' . ($invitation->id ?? 'preview');
@endphp

<div class="min-h-screen py-7 sm:py-14 px-3 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 68% 48% at 50% 0%, {{ $primaryColor }}14 0%, transparent 65%), radial-gradient(ellipse 62% 46% at 50% 100%, {{ $accentColor }}1F 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #E7E2D6 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Al-Ahsa Oasis Brushed Linen & Panoramic Eucalyptus Couture Stationery -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-18px_rgba(27,67,50,0.2)] min-h-[780px] sm:min-h-[880px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 35%, #FFFFFF 0%, {{ $cardColor }} 64%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Brushed Vertical Linen Grain & Soft Atmospheric Wash -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <pattern id="{{ $linenPatternId }}" width="12" height="24" patternUnits="userSpaceOnUse">
                        <line x1="3" y1="0" x2="3" y2="24" stroke="{{ $oliveSage }}" stroke-opacity="0.045" stroke-width="0.7" />
                        <line x1="9" y1="4" x2="9" y2="20" stroke="{{ $accentColor }}" stroke-opacity="0.04" stroke-width="0.6" />
                    </pattern>
                </defs>
                <rect width="600" height="900" fill="url(#{{ $linenPatternId }})" />
                <ellipse cx="300" cy="240" rx="220" ry="180" fill="#FFFFFF" fill-opacity="0.45" />
            </svg>

            <!-- Fine Champagne-Gold Perimeter Frame -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $accentColor }}66;">
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
            </div>

            <!-- Layer 2A: Top-End Delicate Golden-Olive Oasis Spray (Ref Image 10) -->
            <svg viewBox="0 0 220 200"
                 class="w-36 h-32 sm:w-48 sm:h-44 absolute top-0 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="palmGoldenBlossom" cx="45%" cy="40%" r="60%">
                        <stop offset="0%" stop-color="#FFFDF5" stop-opacity="0.95" />
                        <stop offset="55%" stop-color="{{ $accentColor }}" stop-opacity="0.6" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.95" />
                    </radialGradient>
                </defs>
                <path d="M225 -6 C 178 24, 140 64, 102 108" fill="none" stroke="{{ $oliveSage }}" stroke-width="1.35" stroke-opacity="0.72" />
                <path d="M196 10 C 174 44, 174 86, 186 132" fill="none" stroke="{{ $oliveSage }}" stroke-width="1.2" stroke-opacity="0.62" />
                <!-- Olive Leaves -->
                <path d="M164 46 Q 138 28, 122 44 Q 146 62, 164 46 Z" fill="{{ $oliveSage }}" fill-opacity="0.34" stroke="{{ $primaryColor }}" stroke-width="0.7" stroke-opacity="0.45" />
                <path d="M138 74 Q 108 62, 96 82 Q 124 94, 138 74 Z" fill="{{ $oliveSage }}" fill-opacity="0.28" stroke="{{ $primaryColor }}" stroke-width="0.7" stroke-opacity="0.4" />
                <path d="M180 68 Q 204 84, 198 112 Q 174 96, 180 68 Z" fill="{{ $primaryColor }}" fill-opacity="0.25" stroke="{{ $primaryColor }}" stroke-width="0.7" stroke-opacity="0.45" />
                <!-- Champagne-Gold Oasis Blossoms -->
                <g transform="translate(178, 36)">
                    <circle cx="0" cy="-9" r="7" fill="url(#palmGoldenBlossom)" />
                    <circle cx="8" cy="2" r="7" fill="url(#palmGoldenBlossom)" />
                    <circle cx="-8" cy="2" r="7" fill="url(#palmGoldenBlossom)" />
                    <circle cx="0" cy="8" r="6" fill="url(#palmGoldenBlossom)" />
                    <circle cx="0" cy="0" r="3.5" fill="{{ $primaryColor }}" fill-opacity="0.75" />
                </g>
                <g transform="translate(132, 34) scale(0.75)">
                    <circle cx="0" cy="-8" r="6" fill="url(#palmGoldenBlossom)" />
                    <circle cx="7" cy="2" r="6" fill="url(#palmGoldenBlossom)" />
                    <circle cx="-7" cy="2" r="6" fill="url(#palmGoldenBlossom)" />
                    <circle cx="0" cy="0" r="3" fill="{{ $primaryColor }}" fill-opacity="0.7" />
                </g>
            </svg>

            <!-- Layer 2B: Bottom Panoramic Watercolor Eucalyptus & Oasis Frond Meadow (Ref Image 11) -->
            <svg viewBox="0 0 600 170"
                 preserveAspectRatio="xMidYMax slice"
                 class="w-full h-32 sm:h-44 absolute bottom-0 inset-x-0 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <!-- Soft Atmospheric Mist Wash -->
                <rect y="30" width="600" height="140" fill="url(#palmMeadowWash)" />
                <defs>
                    <linearGradient id="palmMeadowWash" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="{{ $oliveSage }}" stop-opacity="0" />
                        <stop offset="100%" stop-color="{{ $oliveSage }}" stop-opacity="0.16" />
                    </linearGradient>
                </defs>

                <!-- Back Ghost Eucalyptus Stems -->
                <g fill="{{ $oliveSage }}" fill-opacity="0.16" stroke="{{ $oliveSage }}" stroke-opacity="0.24" stroke-width="1">
                    <path d="M55 170 Q 75 95, 92 28" fill="none" />
                    <ellipse cx="74" cy="94" rx="11" ry="6" transform="rotate(-24 74 94)" />
                    <ellipse cx="90" cy="76" rx="11" ry="6" transform="rotate(24 90 76)" />
                    <ellipse cx="82" cy="56" rx="10" ry="5.5" transform="rotate(-20 82 56)" />
                    <path d="M210 170 Q 195 105, 185 45" fill="none" />
                    <ellipse cx="192" cy="112" rx="11" ry="6" transform="rotate(-26 192 112)" />
                    <ellipse cx="206" cy="92" rx="11" ry="6" transform="rotate(26 206 92)" />
                    <path d="M395 170 Q 412 102, 424 42" fill="none" />
                    <ellipse cx="402" cy="108" rx="11" ry="6" transform="rotate(-22 402 108)" />
                    <ellipse cx="420" cy="88" rx="11" ry="6" transform="rotate(25 420 88)" />
                    <path d="M538 170 Q 518 98, 502 32" fill="none" />
                    <ellipse cx="524" cy="98" rx="11" ry="6" transform="rotate(-24 524 98)" />
                    <ellipse cx="506" cy="78" rx="11" ry="6" transform="rotate(22 506 78)" />
                </g>

                <!-- Foreground Watercolor Eucalyptus & Oasis Leaves -->
                <g stroke="{{ $primaryColor }}" stroke-opacity="0.5" stroke-width="1.15">
                    <!-- Cluster 1 (Left Edge) -->
                    <path d="M26 170 Q 34 112, 44 52" fill="none" />
                    <g fill="{{ $primaryColor }}" fill-opacity="0.42">
                        <ellipse cx="24" cy="134" rx="10" ry="5.5" transform="rotate(-28 24 134)" />
                        <ellipse cx="42" cy="120" rx="10" ry="5.5" transform="rotate(24 42 120)" />
                        <ellipse cx="28" cy="102" rx="9.5" ry="5" transform="rotate(-24 28 102)" />
                        <ellipse cx="45" cy="86" rx="9" ry="5" transform="rotate(26 45 86)" />
                        <ellipse cx="34" cy="70" rx="8" ry="4.5" transform="rotate(-18 34 70)" />
                        <ellipse cx="44" cy="54" rx="6.5" ry="4" transform="rotate(14 44 54)" />
                    </g>

                    <!-- Cluster 2 -->
                    <path d="M128 170 Q 140 116, 152 62" fill="none" />
                    <g fill="{{ $oliveSage }}" fill-opacity="0.52">
                        <ellipse cx="122" cy="138" rx="10" ry="5.5" transform="rotate(-24 122 138)" />
                        <ellipse cx="144" cy="124" rx="10" ry="5.5" transform="rotate(28 144 124)" />
                        <ellipse cx="132" cy="106" rx="9" ry="5" transform="rotate(-20 132 106)" />
                        <ellipse cx="152" cy="90" rx="8.5" ry="4.5" transform="rotate(24 152 90)" />
                        <ellipse cx="144" cy="72" rx="7.5" ry="4" transform="rotate(-16 144 72)" />
                    </g>

                    <!-- Cluster 3 (Center-Low Arch) -->
                    <path d="M276 170 Q 282 130, 290 92" fill="none" />
                    <path d="M326 170 Q 320 132, 314 96" fill="none" />
                    <g fill="{{ $primaryColor }}" fill-opacity="0.34">
                        <ellipse cx="268" cy="142" rx="9" ry="5" transform="rotate(-26 268 142)" />
                        <ellipse cx="290" cy="130" rx="9" ry="5" transform="rotate(22 290 130)" />
                        <ellipse cx="278" cy="112" rx="8" ry="4.5" transform="rotate(-20 278 112)" />
                        <ellipse cx="332" cy="142" rx="9" ry="5" transform="rotate(26 332 142)" />
                        <ellipse cx="312" cy="128" rx="9" ry="5" transform="rotate(-22 312 128)" />
                        <ellipse cx="322" cy="110" rx="8" ry="4.5" transform="rotate(20 322 110)" />
                    </g>

                    <!-- Cluster 4 -->
                    <path d="M468 170 Q 456 116, 444 62" fill="none" />
                    <g fill="{{ $oliveSage }}" fill-opacity="0.52">
                        <ellipse cx="474" cy="138" rx="10" ry="5.5" transform="rotate(24 474 138)" />
                        <ellipse cx="452" cy="124" rx="10" ry="5.5" transform="rotate(-28 452 124)" />
                        <ellipse cx="464" cy="106" rx="9" ry="5" transform="rotate(20 464 106)" />
                        <ellipse cx="444" cy="90" rx="8.5" ry="4.5" transform="rotate(-24 444 90)" />
                        <ellipse cx="452" cy="72" rx="7.5" ry="4" transform="rotate(16 452 72)" />
                    </g>

                    <!-- Cluster 5 (Right Edge) -->
                    <path d="M574 170 Q 566 112, 556 52" fill="none" />
                    <g fill="{{ $primaryColor }}" fill-opacity="0.42">
                        <ellipse cx="576" cy="134" rx="10" ry="5.5" transform="rotate(28 576 134)" />
                        <ellipse cx="558" cy="120" rx="10" ry="5.5" transform="rotate(-24 558 120)" />
                        <ellipse cx="572" cy="102" rx="9.5" ry="5" transform="rotate(24 572 102)" />
                        <ellipse cx="555" cy="86" rx="9" ry="5" transform="rotate(-26 555 86)" />
                        <ellipse cx="566" cy="70" rx="8" ry="4.5" transform="rotate(18 566 70)" />
                        <ellipse cx="556" cy="54" rx="6.5" ry="4" transform="rotate(-14 556 54)" />
                    </g>
                </g>
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-16 sm:pt-20 pb-32 sm:pb-40 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 mx-auto" aria-hidden="true">
                                <line x1="12" y1="10" x2="82" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.8" />
                                <line x1="138" y1="10" x2="208" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.8" />
                                <path d="M96 10 Q 110 2, 124 10 Q 110 18, 96 10 Z" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                                <circle cx="110" cy="10" r="2.2" fill="{{ $accentColor }}" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7]"
                               style="color: {{ $primaryColor }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                            <span class="w-2 h-2 rotate-45" style="background-color: {{ $primaryColor }};"></span>
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                        </div>
                    @endif

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-sm sm:text-base leading-[1.95] max-w-md mx-auto pt-1 opacity-85"
                             style="color: {{ $secondaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif
                </header>

                <!-- Host & Opening Phrase -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-85" style="color: {{ $oliveSage }};">
                        {{ $isEnInv ? 'Under the Canopy of Blessing' : 'بكل السكينة والسرور' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'We invite you to share in the joy of the wedding of' : 'تتشرف العائلتان بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Side-by-Side Couple Triad + Translucent First-Name Echo -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.035]"
                         aria-hidden="true">
                        @if($hasBrideName)
                            <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap"
                                  style="color: {{ $primaryColor }};">{{ $brideParts['first'] }}</span>
                        @endif
                        <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap {{ $hasBrideName ? '-mt-2' : '' }}"
                              style="color: {{ $primaryColor }};">{{ $groomParts['first'] }}</span>
                    </div>

                    @if($hasBrideName)
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $oliveSage }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'ابنتنا' }}
                                </span>
                                <span class="font-cairo text-[1.65rem] sm:text-[2.6rem] font-black leading-[1.2] break-words w-full"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $brideParts['first'] }}
                                </span>
                                @if($brideParts['lineage'] !== '')
                                    <span class="font-tajawal text-[11px] sm:text-sm font-medium opacity-80 break-words w-full leading-snug"
                                          style="color: {{ $secondaryColor }};"
                                          aria-hidden="true">
                                        {{ $brideParts['lineage'] }}
                                    </span>
                                @endif
                            </span>

                            <!-- Center Classic Oasis Waw Ligature -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 46 84" class="w-7 h-14 sm:w-10 sm:h-18 mx-auto overflow-visible">
                                        <path d="M27 18 C 34 18, 37 26, 33 34 C 29 41, 18 41, 15 34 C 12 27, 19 18, 27 18 Z M29 24 C 25 23, 21 27, 22 31 C 23 35, 29 35, 31 30 C 32 27, 31 25, 29 24 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M34 31 C 36 47, 29 63, 12 75 C 10 77, 8 74, 10 71 C 22 60, 28 48, 28 35 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M22 10 L 25 7 L 28 10 L 25 13 Z" fill="{{ $accentColor }}" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $primaryColor }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $oliveSage }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'ابننا' }}
                                </span>
                                <span class="font-cairo text-[1.65rem] sm:text-[2.6rem] font-black leading-[1.2] break-words w-full"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $groomParts['first'] }}
                                </span>
                                @if($groomParts['lineage'] !== '')
                                    <span class="font-tajawal text-[11px] sm:text-sm font-medium opacity-80 break-words w-full leading-snug"
                                          style="color: {{ $secondaryColor }};"
                                          aria-hidden="true">
                                        {{ $groomParts['lineage'] }}
                                    </span>
                                @endif
                            </span>
                        </h1>
                    @else
                        <h1 class="relative z-10 flex flex-col items-center space-y-1.5 text-center">
                            <span class="sr-only">{{ $invitation->groom_name }}</span>
                            <span class="font-tajawal text-xs sm:text-sm font-bold tracking-wide opacity-75"
                                  style="color: {{ $oliveSage }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'ابننا' }}
                            </span>
                            <span class="font-cairo text-3xl sm:text-5xl font-black leading-[1.18] break-words max-w-full"
                                  style="color: {{ $primaryColor }};"
                                  aria-hidden="true">
                                {{ $groomParts['first'] }}
                            </span>
                            @if($groomParts['lineage'] !== '')
                                <span class="font-tajawal text-xs sm:text-base font-medium opacity-80 break-words max-w-md"
                                      style="color: {{ $secondaryColor }};"
                                      aria-hidden="true">
                                    {{ $groomParts['lineage'] }}
                                </span>
                            @endif
                            <svg viewBox="0 0 200 14" class="w-40 sm:w-52 h-3 pt-1" aria-hidden="true">
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.6" stroke-opacity="0.7" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto rounded-t-[4.5rem] rounded-b-lg p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-t-[4rem] rounded-b-sm">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-xs sm:text-sm leading-[1.95] px-2 max-w-md mx-auto opacity-85"
                       style="color: {{ $secondaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Layer 5: Winged Hairline Date Bar & Venue Block -->
                <section class="space-y-4 font-tajawal">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'By the Grace of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="py-3 px-2 border-y text-xs sm:text-sm font-bold space-y-1.5"
                         style="border-color: {{ $accentColor }}66; color: {{ $secondaryColor }};">
                        <div class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1">
                            <span class="inline-flex items-center gap-1.5" style="color: {{ $primaryColor }};">
                                <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $oliveSage }};" />
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </span>

                            @if($dateInfo['hijri_date'])
                                <span class="h-3.5 w-px inline-block" style="background-color: {{ $accentColor }};" aria-hidden="true"></span>
                                <span class="inline-flex items-center gap-1 font-semibold">
                                    @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                                </span>
                            @endif
                        </div>

                        @if($dateInfo['time'])
                            <div class="flex items-center justify-center gap-1.5 font-semibold opacity-90">
                                <x-app-icon name="clock" class="w-3.5 h-3.5 shrink-0" style="color: {{ $primaryColor }};" />
                                @if($dateInfo['locale'] === 'en')
                                    <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                @else
                                    <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="pt-1 space-y-1.5">
                        <div class="inline-flex items-center justify-center gap-1.5 text-sm sm:text-base font-extrabold"
                             style="color: {{ $secondaryColor }};">
                            <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                            <span class="break-words">{{ $invitation->venue_name }}</span>
                        </div>

                        @if($invitation->venue_address)
                            <p class="text-[11px] sm:text-xs opacity-75 max-w-md mx-auto leading-relaxed break-words">
                                {{ $invitation->venue_address }}
                            </p>
                        @endif

                        @if($invitation->venue_map_url)
                            <div class="pt-2">
                                <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-extrabold tracking-wide shadow-xs hover:opacity-90 transition border"
                                   style="background-color: {{ $primaryColor }}; border-color: {{ $accentColor }}; color: #FCFBF7;">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: {{ $accentColor }};" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: Continuous Horizontal Thread Ceremony Schedule (Ref Image 11) -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $accentColor,
                    'cardColor' => $cardColor,
                    'variant' => 'thread',
                ])

                <footer class="pt-2 space-y-1.5">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Grateful for your presence · With you, our joy is complete' : 'شاكرين لكم تلبية الدعوة · وبحضوركم تكتمل فرحتنا' }}
                    </p>
                    <p class="font-tajawal text-[10px] sm:text-[11px] opacity-60 tracking-wide">
                        {{ $isEnInv ? '• Personal Invitation · Honored by Your Presence •' : '• الدعوة شخصية · يسعدنا تشريفكم •' }}
                    </p>
                </footer>
            </div>
        </article>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs opacity-70 pt-3 pb-8 font-tajawal">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ ($isPreview ?? false) ? route('templates.index') : route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
