@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#7A1F2B';
    $secondaryColor = $styles['secondary_color'] ?? '#231F20';
    $bgColor = $styles['background_color'] ?? '#F5EFE6';
    $cardColor = $styles['card_color'] ?? '#FFFDF9';
    $accentColor = $styles['accent_color'] ?? '#C85A32';
    $goldSpeck = '#C5A067';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));

    $groomParts = \App\Services\InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
    $brideParts = $hasBrideName
        ? \App\Services\InvitationDateFormatter::splitCoupleName($invitation->bride_name)
        : ['first' => '', 'lineage' => ''];
    $ceremonyStages = \App\Services\InvitationDateFormatter::ceremonyStages($invitation, $dateInfo);
    $saduWaterId = 'sadu-water-' . ($invitation->id ?? 'preview');
    $saduTrimId = 'sadu-trim-' . ($invitation->id ?? 'preview');
@endphp

<div class="min-h-screen py-7 sm:py-14 px-3 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 70% 48% at 12% 10%, {{ $primaryColor }}14 0%, transparent 65%), radial-gradient(ellipse 64% 46% at 88% 90%, {{ $accentColor }}18 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #EAE0D2 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Al Sadu Crimson Poppy & Woven Alabaster Couture Stationery -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-18px_rgba(35,31,32,0.22)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 40%, #FFFFFF 0%, {{ $cardColor }} 62%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Full-Canvas Embossed Sadu Diamond Lozenge Watermark -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <pattern id="{{ $saduWaterId }}" width="64" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="32,6 58,32 32,58 6,32" fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.042" stroke-width="1" />
                        <polygon points="32,18 46,32 32,46 18,32" fill="none" stroke="{{ $accentColor }}" stroke-opacity="0.04" stroke-width="0.9" />
                        <circle cx="32" cy="32" r="2.2" fill="{{ $primaryColor }}" fill-opacity="0.045" />
                    </pattern>
                </defs>
                <rect width="600" height="900" fill="url(#{{ $saduWaterId }})" />
            </svg>

            <!-- Refined Al Sadu Loom Header & Footer Trim Bands -->
            <div class="w-full h-3.5 relative z-20" aria-hidden="true">
                <svg viewBox="0 0 600 14" class="w-full h-full block" preserveAspectRatio="none">
                    <defs>
                        <pattern id="{{ $saduTrimId }}" width="28" height="14" patternUnits="userSpaceOnUse">
                            <rect width="28" height="14" fill="{{ $primaryColor }}" />
                            <rect y="0" width="28" height="2" fill="{{ $secondaryColor }}" />
                            <rect y="12" width="28" height="2" fill="{{ $secondaryColor }}" />
                            <polygon points="14,2 25,7 14,12 3,7" fill="{{ $cardColor }}" />
                            <polygon points="14,4.2 20,7 14,9.8 8,7" fill="{{ $accentColor }}" />
                        </pattern>
                    </defs>
                    <rect width="600" height="14" fill="url(#{{ $saduTrimId }})" />
                </svg>
            </div>

            <!-- Layer 2A: Top-Start Cascading Watercolor Crimson & Terracotta Poppies (Ref Image 7) -->
            <svg viewBox="0 0 240 240"
                 class="w-36 h-36 sm:w-52 sm:h-52 absolute top-3 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="saduPoppyGrad" cx="45%" cy="40%" r="65%">
                        <stop offset="0%" stop-color="#FFFDF9" stop-opacity="0.88" />
                        <stop offset="48%" stop-color="{{ $accentColor }}" stop-opacity="0.65" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0.92" />
                    </radialGradient>
                </defs>

                <circle cx="52" cy="68" r="56" fill="{{ $accentColor }}" fill-opacity="0.1" />
                <!-- Slender Hanging Poppy Stems -->
                <path d="M24 -6 C 32 42, 46 86, 56 126" fill="none" stroke="{{ $secondaryColor }}" stroke-width="1.2" stroke-opacity="0.6" />
                <path d="M58 -8 C 68 28, 92 64, 114 96" fill="none" stroke="{{ $secondaryColor }}" stroke-width="1.1" stroke-opacity="0.52" />

                <!-- Ghost Translucent Poppy -->
                <g transform="translate(88, 128) scale(0.82)" opacity="0.22" fill="{{ $accentColor }}">
                    <path d="M0 -30 C 18 -30, 32 -14, 26 2 C 30 18, 14 28, 0 24 C -16 28, -30 16, -26 0 C -30 -16, -16 -30, 0 -30 Z" />
                </g>

                <!-- Hero Upper Poppy Bloom -->
                <g transform="translate(52, 86) rotate(15)">
                    <path d="M0 -34 C 20 -34, 34 -18, 28 2 C 34 20, 16 32, -2 28 C -20 32, -34 18, -28 -2 C -32 -20, -18 -34, 0 -34 Z"
                          fill="url(#saduPoppyGrad)" stroke="{{ $primaryColor }}" stroke-width="0.9" stroke-opacity="0.55" />
                    <path d="M0 -18 C 12 -18, 18 -8, 14 2 C 16 11, 6 18, -2 15 C -12 17, -18 8, -14 -2 C -16 -10, -10 -18, 0 -18 Z"
                          fill="{{ $primaryColor }}" fill-opacity="0.45" />
                    <circle cx="0" cy="0" r="4.5" fill="{{ $secondaryColor }}" fill-opacity="0.82" />
                    <circle cx="-3" cy="-3" r="1.1" fill="{{ $goldSpeck }}" />
                    <circle cx="3" cy="-2" r="1.1" fill="{{ $goldSpeck }}" />
                    <circle cx="1" cy="3.5" r="1.1" fill="{{ $goldSpeck }}" />
                </g>

                <!-- Hanging Poppy Bud -->
                <g transform="translate(114, 102) rotate(24)">
                    <path d="M0 -16 C 10 -12, 12 2, 0 12 C -12 2, -10 -12, 0 -16 Z"
                          fill="url(#saduPoppyGrad)" stroke="{{ $primaryColor }}" stroke-width="0.85" />
                </g>
            </svg>

            <!-- Layer 2B: Bottom-End Rising Crimson Poppy Cluster (Ref Image 7) -->
            <svg viewBox="0 0 240 240"
                 class="w-36 h-36 sm:w-52 sm:h-52 absolute bottom-3 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <circle cx="184" cy="172" r="56" fill="{{ $primaryColor }}" fill-opacity="0.1" />
                <path d="M214 246 C 206 198, 192 154, 182 114" fill="none" stroke="{{ $secondaryColor }}" stroke-width="1.2" stroke-opacity="0.6" />
                <path d="M180 246 C 170 210, 146 174, 124 142" fill="none" stroke="{{ $secondaryColor }}" stroke-width="1.1" stroke-opacity="0.52" />

                <g transform="translate(150, 112) scale(0.82)" opacity="0.22" fill="{{ $accentColor }}">
                    <path d="M0 -30 C 18 -30, 32 -14, 26 2 C 30 18, 14 28, 0 24 C -16 28, -30 16, -26 0 C -30 -16, -16 -30, 0 -30 Z" />
                </g>

                <g transform="translate(184, 152) rotate(-15)">
                    <path d="M0 -34 C 20 -34, 34 -18, 28 2 C 34 20, 16 32, -2 28 C -20 32, -34 18, -28 -2 C -32 -20, -18 -34, 0 -34 Z"
                          fill="url(#saduPoppyGrad)" stroke="{{ $primaryColor }}" stroke-width="0.9" stroke-opacity="0.55" />
                    <path d="M0 -18 C 12 -18, 18 -8, 14 2 C 16 11, 6 18, -2 15 C -12 17, -18 8, -14 -2 C -16 -10, -10 -18, 0 -18 Z"
                          fill="{{ $primaryColor }}" fill-opacity="0.45" />
                    <circle cx="0" cy="0" r="4.5" fill="{{ $secondaryColor }}" fill-opacity="0.82" />
                    <circle cx="-3" cy="-3" r="1.1" fill="{{ $goldSpeck }}" />
                    <circle cx="3" cy="-2" r="1.1" fill="{{ $goldSpeck }}" />
                    <circle cx="1" cy="3.5" r="1.1" fill="{{ $goldSpeck }}" />
                </g>

                <g transform="translate(124, 138) rotate(-24)">
                    <path d="M0 -16 C 10 -12, 12 2, 0 12 C -12 2, -10 -12, 0 -16 Z"
                          fill="url(#saduPoppyGrad)" stroke="{{ $primaryColor }}" stroke-width="0.85" />
                </g>
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-14 sm:pt-18 pb-20 sm:pb-24 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 220 18" class="w-40 sm:w-48 h-3.5 mx-auto" aria-hidden="true">
                                <line x1="12" y1="9" x2="84" y2="9" stroke="{{ $accentColor }}" stroke-width="1" stroke-opacity="0.7" />
                                <line x1="136" y1="9" x2="208" y2="9" stroke="{{ $accentColor }}" stroke-width="1" stroke-opacity="0.7" />
                                <polygon points="110,2 117,9 110,16 103,9" fill="{{ $primaryColor }}" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7]"
                               style="color: {{ $primaryColor }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                            <span class="w-2.5 h-2.5 rotate-45" style="background-color: {{ $primaryColor }};"></span>
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

                <!-- Host & Opening Phrase (Ref Image 7) -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-85" style="color: {{ $accentColor }};">
                        {{ $isEnInv ? 'With Love & Warmth' : 'بكل الحب والود' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Our families joyfully invite you to celebrate the wedding of' : 'تتشرف العائلتان بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Side-by-Side Couple Triad + Offset Translucent Echo (Ref Image 7) -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.045]"
                         aria-hidden="true">
                        @if($hasBrideName)
                            <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap translate-x-6"
                                  style="color: {{ $accentColor }};">{{ $brideParts['first'] }}</span>
                        @endif
                        <span class="font-cairo text-4xl sm:text-6xl font-black leading-tight whitespace-nowrap -translate-x-6 {{ $hasBrideName ? '-mt-3' : '' }}"
                              style="color: {{ $primaryColor }};">{{ $groomParts['first'] }}</span>
                    </div>

                    @if($hasBrideName)
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $accentColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'ابنتنا' }}
                                </span>
                                <span class="font-cairo text-[1.65rem] sm:text-[2.6rem] font-black leading-[1.2] break-words w-full"
                                      style="color: {{ $secondaryColor }};"
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

                            <!-- Sweeping Terracotta-Crimson Diwani Waw Ribbon (Ref Image 7) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 46 88" class="w-8 h-14 sm:w-11 sm:h-20 mx-auto overflow-visible">
                                        <path d="M27 16 C 35 16, 38 25, 33 34 C 28 42, 17 42, 14 34 C 11 26, 19 16, 27 16 Z M29 22 C 25 22, 21 26, 22 31 C 23 35, 29 35, 31 30 C 32 27, 31 23, 29 22 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M35 30 C 37 49, 28 65, 9 78 C 7 80, 5 77, 8 74 C 21 62, 27 49, 28 34 Z"
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
                                    {{ $isEnInv ? 'The Groom' : 'ابننا' }}
                                </span>
                                <span class="font-cairo text-[1.65rem] sm:text-[2.6rem] font-black leading-[1.2] break-words w-full"
                                      style="color: {{ $secondaryColor }};"
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
                                {{ $isEnInv ? 'Our Son' : 'ابننا' }}
                            </span>
                            <span class="font-cairo text-3xl sm:text-5xl font-black leading-[1.18] break-words max-w-full"
                                  style="color: {{ $secondaryColor }};"
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
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto p-1.5 border-2 shadow-sm overflow-hidden"
                             style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover">
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
                                   style="background-color: {{ $primaryColor }}; border-color: {{ $goldSpeck }}80; color: #FFFDF9;">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: #FFFDF9;" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: 3-Stage Column Ceremony Schedule (Ref Image 7) -->
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
                        {{ $isEnInv ? 'With your presence, our joy is complete' : 'وبحضوركم تكتمل فرحتنا' }}
                    </p>
                    <p class="font-tajawal text-[10px] sm:text-[11px] opacity-60 tracking-wide">
                        {{ $isEnInv ? '• Personal Invitation · Honored by Your Presence •' : '• الدعوة شخصية · يسعدنا تشريفكم •' }}
                    </p>
                </footer>
            </div>

            <!-- Matching Al Sadu Woven Footer Band -->
            <div class="w-full h-3.5 relative z-20" aria-hidden="true">
                <svg viewBox="0 0 600 14" class="w-full h-full block" preserveAspectRatio="none">
                    <rect width="600" height="14" fill="url(#{{ $saduTrimId }})" />
                </svg>
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
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
