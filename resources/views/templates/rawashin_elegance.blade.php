@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#14535E';
    $secondaryColor = $styles['secondary_color'] ?? '#2A211B';
    $bgColor = $styles['background_color'] ?? '#F8F4EC';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#C87D65';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Amiri');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));

    $groomParts = \App\Services\InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
    $brideParts = $hasBrideName
        ? \App\Services\InvitationDateFormatter::splitCoupleName($invitation->bride_name)
        : ['first' => '', 'lineage' => ''];
    $ceremonyStages = \App\Services\InvitationDateFormatter::ceremonyStages($invitation, $dateInfo);
    $roshanPatternId = 'roshan-emboss-' . ($invitation->id ?? 'preview');
@endphp

<div class="min-h-screen py-7 sm:py-14 px-3 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 68% 48% at 88% 10%, {{ $primaryColor }}18 0%, transparent 65%), radial-gradient(ellipse 62% 46% at 10% 90%, {{ $accentColor }}1C 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #EDE7DD 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Hijazi Rawashin & Porcelain Rose Couture Stationery -->
        <article class="relative overflow-hidden shadow-[0_28px_70px_-18px_rgba(20,83,94,0.2)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 38%, #FFFFFF 0%, #FAF8F4 62%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Diagonal Silk Fold & Embossed Hijazi Mashrabiya Lattice Watermark -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <pattern id="{{ $roshanPatternId }}" width="48" height="48" patternUnits="userSpaceOnUse">
                        <path d="M24 2 L46 24 L24 46 L2 24 Z" fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.045" stroke-width="0.9" />
                        <circle cx="24" cy="24" r="6" fill="none" stroke="{{ $accentColor }}" stroke-opacity="0.04" stroke-width="0.8" />
                    </pattern>
                    <linearGradient id="rawashinFold" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.6" />
                        <stop offset="50%" stop-color="{{ $primaryColor }}" stop-opacity="0.03" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.4" />
                    </linearGradient>
                </defs>

                <rect width="600" height="900" fill="url(#{{ $roshanPatternId }})" />
                <path d="M-30 560 C 160 440, 390 660, 630 480 L 630 900 L -30 900 Z" fill="url(#rawashinFold)" />
                <!-- Subtle Pointed Hijazi Arch Silhouette -->
                <path d="M90 880 V 210 Q 90 70, 300 36 Q 510 70, 510 210 V 880"
                      fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.065" stroke-width="1.35" />
            </svg>

            <!-- Fine Coral-Gold Perimeter Line -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $accentColor }}52;">
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
            </div>

            <!-- Layer 2A: Top-End Watercolor Porcelain-Teal & Coral Garden Roses (Ref Image 4) -->
            <svg viewBox="0 0 260 220"
                 class="w-40 h-32 sm:w-56 sm:h-48 absolute top-0 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="roshanTopRose" cx="55%" cy="35%" r="68%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.95" />
                        <stop offset="52%" stop-color="{{ $primaryColor }}" stop-opacity="0.45" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0.9" />
                    </radialGradient>
                    <radialGradient id="roshanTopCoral" cx="50%" cy="40%" r="65%">
                        <stop offset="0%" stop-color="#FFFDF9" stop-opacity="0.94" />
                        <stop offset="55%" stop-color="{{ $accentColor }}" stop-opacity="0.42" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.86" />
                    </radialGradient>
                </defs>

                <circle cx="196" cy="48" r="64" fill="{{ $primaryColor }}" fill-opacity="0.1" />
                <!-- Ghost Foliage -->
                <path d="M266 12 C 216 42, 166 78, 108 92" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-opacity="0.55" />
                <path d="M168 64 Q 142 36, 118 48 Q 138 78, 168 64 Z" fill="{{ $primaryColor }}" fill-opacity="0.22" stroke="{{ $primaryColor }}" stroke-width="0.8" stroke-opacity="0.5" />
                <path d="M142 82 Q 112 88, 100 108 Q 128 114, 142 82 Z" fill="{{ $primaryColor }}" fill-opacity="0.18" stroke="{{ $primaryColor }}" stroke-width="0.8" stroke-opacity="0.45" />

                <!-- Layered Porcelain-Teal Watercolor Rose (Ref Image 4) -->
                <g transform="translate(206, 48) rotate(14)">
                    <path d="M0 -42 C 14 -40, 20 -20, 4 -5 C 20 -26, 40 -14, 34 6 C 28 22, 10 16, 3 4 C 24 14, 24 38, 4 40 C -14 42, -16 18, -2 5 C -16 24, -38 22, -38 2 C -38 -16, -16 -14, -4 -2 C -24 -12, -26 -36, -8 -42 Z"
                          fill="url(#roshanTopRose)" stroke="{{ $primaryColor }}" stroke-width="0.9" stroke-opacity="0.52" />
                    <path d="M0 -22 C 9 -20, 12 -8, 2 -2 C 14 -10, 24 0, 16 12 C 10 20, 2 10, 0 2 C 6 14, -6 24, -14 14 C -22 6, -10 -4, -2 -2 C -14 -8, -12 -22, 0 -22 Z"
                          fill="{{ $primaryColor }}" fill-opacity="0.32" stroke="#FFFFFF" stroke-width="0.7" stroke-opacity="0.65" />
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.32" stroke-width="0.6">
                        <line x1="0" y1="-8" x2="0" y2="-32" />
                        <line x1="7" y1="-5" x2="24" y2="-12" />
                        <line x1="6" y1="6" x2="20" y2="24" />
                        <line x1="-6" y1="6" x2="-24" y2="12" />
                        <line x1="-7" y1="-5" x2="-22" y2="-22" />
                    </g>
                    <circle cx="0" cy="0" r="5" fill="{{ $accentColor }}" />
                    <circle cx="0" cy="0" r="2" fill="#FFFDF9" />
                </g>

                <!-- Coral Companion Blossom -->
                <g transform="translate(150, 28) scale(0.68) rotate(-18)">
                    <path d="M0 -36 C 12 -34, 16 -16, 3 -4 C 18 -20, 34 -8, 28 8 C 22 20, 6 12, 2 3 C 20 12, 20 32, 2 34 C -12 36, -14 14, -2 4 C -14 20, -32 16, -32 0 C -32 -14, -14 -12, -3 -2 C -20 -10, -20 -30, -6 -36 Z"
                          fill="url(#roshanTopCoral)" stroke="{{ $accentColor }}" stroke-width="0.85" stroke-opacity="0.58" />
                    <circle cx="0" cy="0" r="4.5" fill="{{ $primaryColor }}" fill-opacity="0.75" />
                </g>
            </svg>

            <!-- Layer 2B: Bottom-Start Matching Porcelain-Teal & Coral Rose Cluster -->
            <svg viewBox="0 0 260 220"
                 class="w-40 h-32 sm:w-56 sm:h-48 absolute bottom-0 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <circle cx="64" cy="170" r="64" fill="{{ $primaryColor }}" fill-opacity="0.1" />
                <path d="M-6 208 C 44 178, 94 142, 152 128" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-opacity="0.55" />
                <path d="M92 156 Q 118 184, 142 172 Q 122 142, 92 156 Z" fill="{{ $primaryColor }}" fill-opacity="0.22" stroke="{{ $primaryColor }}" stroke-width="0.8" stroke-opacity="0.5" />
                <path d="M118 138 Q 148 132, 160 112 Q 132 106, 118 138 Z" fill="{{ $primaryColor }}" fill-opacity="0.18" stroke="{{ $primaryColor }}" stroke-width="0.8" stroke-opacity="0.45" />

                <g transform="translate(54, 172) rotate(-14)">
                    <path d="M0 -42 C 14 -40, 20 -20, 4 -5 C 20 -26, 40 -14, 34 6 C 28 22, 10 16, 3 4 C 24 14, 24 38, 4 40 C -14 42, -16 18, -2 5 C -16 24, -38 22, -38 2 C -38 -16, -16 -14, -4 -2 C -24 -12, -26 -36, -8 -42 Z"
                          fill="url(#roshanTopRose)" stroke="{{ $primaryColor }}" stroke-width="0.9" stroke-opacity="0.52" />
                    <path d="M0 -22 C 9 -20, 12 -8, 2 -2 C 14 -10, 24 0, 16 12 C 10 20, 2 10, 0 2 C 6 14, -6 24, -14 14 C -22 6, -10 -4, -2 -2 C -14 -8, -12 -22, 0 -22 Z"
                          fill="{{ $primaryColor }}" fill-opacity="0.32" stroke="#FFFFFF" stroke-width="0.7" stroke-opacity="0.65" />
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.32" stroke-width="0.6">
                        <line x1="0" y1="-8" x2="0" y2="-32" />
                        <line x1="7" y1="-5" x2="24" y2="-12" />
                        <line x1="6" y1="6" x2="20" y2="24" />
                        <line x1="-6" y1="6" x2="-24" y2="12" />
                        <line x1="-7" y1="-5" x2="-22" y2="-22" />
                    </g>
                    <circle cx="0" cy="0" r="5" fill="{{ $accentColor }}" />
                    <circle cx="0" cy="0" r="2" fill="#FFFDF9" />
                </g>

                <g transform="translate(110, 192) scale(0.68) rotate(18)">
                    <path d="M0 -36 C 12 -34, 16 -16, 3 -4 C 18 -20, 34 -8, 28 8 C 22 20, 6 12, 2 3 C 20 12, 20 32, 2 34 C -12 36, -14 14, -2 4 C -14 20, -32 16, -32 0 C -32 -14, -14 -12, -3 -2 C -20 -10, -20 -30, -6 -36 Z"
                          fill="url(#roshanTopCoral)" stroke="{{ $accentColor }}" stroke-width="0.85" stroke-opacity="0.58" />
                    <circle cx="0" cy="0" r="4.5" fill="{{ $primaryColor }}" fill-opacity="0.75" />
                </g>
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-18 sm:pt-22 pb-24 sm:pb-28 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 mx-auto" aria-hidden="true">
                                <line x1="10" y1="10" x2="82" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.75" />
                                <line x1="138" y1="10" x2="210" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.75" />
                                <polygon points="110,3 117,10 110,17 103,10" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.2" />
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
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-85" style="color: {{ $accentColor }};">
                        {{ $isEnInv ? 'With Warmest Regards' : 'تتشرف العائلتان الكريمتان' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Requesting the honor of your presence at the wedding of' : 'بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Side-by-Side Couple Triad + Translucent First-Name Echo -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.035]"
                         aria-hidden="true">
                        @if($hasBrideName)
                            <span class="font-amiri text-4xl sm:text-6xl font-bold leading-tight whitespace-nowrap"
                                  style="color: {{ $primaryColor }};">{{ $brideParts['first'] }}</span>
                        @endif
                        <span class="font-amiri text-4xl sm:text-6xl font-bold leading-tight whitespace-nowrap {{ $hasBrideName ? '-mt-2' : '' }}"
                              style="color: {{ $primaryColor }};">{{ $groomParts['first'] }}</span>
                    </div>

                    @if($hasBrideName)
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $accentColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'الابنة' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
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

                            <!-- Tall Tapered Vertical Calligraphic Stroke (Ref Image 4) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 40 94" class="w-7 h-16 sm:w-9 sm:h-22 mx-auto overflow-visible">
                                        <path d="M22 10 L25 8 L23 64 C 23 72, 16 79, 12 84 C 18 82, 27 76, 27 65 L26 14 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M13 70 C 13 64, 21 63, 22 69 C 23 74, 15 76, 13 70 Z"
                                              fill="{{ $accentColor }}" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $primaryColor }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $accentColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'الابن' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
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
                                  style="color: {{ $accentColor }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'الابن' }}
                            </span>
                            <span class="font-amiri text-4xl sm:text-[3.4rem] font-bold leading-[1.18] break-words max-w-full"
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
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-opacity="0.7" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto rounded-t-[4rem] rounded-b-lg p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-t-[3.5rem] rounded-b-sm">
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
                        {{ $isEnInv ? 'By the Will of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="py-3 px-2 border-y text-xs sm:text-sm font-bold space-y-1.5"
                         style="border-color: {{ $primaryColor }}42; color: {{ $secondaryColor }};">
                        <div class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1">
                            <span class="inline-flex items-center gap-1.5" style="color: {{ $primaryColor }};">
                                <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $accentColor }};" />
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </span>

                            @if($dateInfo['hijri_date'])
                                <span class="h-3.5 w-px inline-block" style="background-color: {{ $primaryColor }}45;" aria-hidden="true"></span>
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
                                   style="background-color: {{ $primaryColor }}; border-color: {{ $accentColor }}80; color: #FFFFFF;">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: #FAF4EA;" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: 3-Stage Column Ceremony Schedule (Ref Image 4) -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $accentColor,
                    'cardColor' => $cardColor,
                    'variant' => 'columns',
                ])

                <footer class="pt-2 space-y-2">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'With your presence, our celebration is complete' : 'وبحضوركم تكتمل فرحتنا' }}
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
            <a href="{{ route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
