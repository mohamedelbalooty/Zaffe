@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#B36A3E';
    $secondaryColor = $styles['secondary_color'] ?? '#26211D';
    $bgColor = $styles['background_color'] ?? '#F4ECE1';
    $cardColor = $styles['card_color'] ?? '#FBF7F0';
    $accentColor = $styles['accent_color'] ?? '#C5A067';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Amiri');
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
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 70% 50% at 15% 10%, {{ $accentColor }}22 0%, transparent 65%), radial-gradient(ellipse 65% 45% at 88% 92%, {{ $primaryColor }}18 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #E7DCCB 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Diriyah Najdi Hurufiyya & Parchment Couture Stationery -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-18px_rgba(38,33,29,0.24)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 42%, #FFFDF9 0%, {{ $cardColor }} 62%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Parchment Light Fold & Embossed Najdi Geometric Rosette Watermark -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <linearGradient id="diriyahSheen" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.55" />
                        <stop offset="50%" stop-color="{{ $accentColor }}" stop-opacity="0.04" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.35" />
                    </linearGradient>
                </defs>
                <path d="M-30 220 C 180 120, 390 360, 630 210 L 630 560 C 410 690, 170 470, -30 580 Z"
                      fill="url(#diriyahSheen)" />

                <!-- Embossed Najdi Door Rosette & Stepped Geometry -->
                <g fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.055" stroke-width="1.15">
                    <circle cx="300" cy="445" r="145" />
                    <polygon points="300,285 338,407 460,445 338,483 300,605 262,483 140,445 262,407" />
                    <polygon points="300,328 417,445 300,562 183,445" />
                    <polygon points="300,362 383,445 300,528 217,445" />
                </g>
            </svg>

            <!-- Fine Bronze-Gold Perimeter Frame -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $accentColor }}59;">
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
            </div>

            <!-- Layer 2A: Top-Start Sweeping Hurufiyya Calligraphic Corner Arc -->
            <svg viewBox="0 0 320 220"
                 class="w-44 h-30 sm:w-64 sm:h-44 absolute top-0 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <clipPath id="diriyahTopArcClip">
                        <path d="M0 0 L315 0 C 225 44, 128 84, 52 178 C 28 206, 12 215, 0 218 Z" />
                    </clipPath>
                </defs>
                <path d="M0 0 L315 0 C 225 44, 128 84, 52 178 C 28 206, 12 215, 0 218 Z"
                      fill="{{ $bgColor }}" fill-opacity="0.72" />
                <g clip-path="url(#diriyahTopArcClip)" fill="none" stroke-linecap="round">
                    <!-- Hurufiyya Reed-Pen Calligraphic Strokes -->
                    <path d="M24 22 Q 52 46, 86 16" stroke="{{ $secondaryColor }}" stroke-width="2.2" stroke-opacity="0.75" />
                    <path d="M18 54 C 46 48, 72 74, 48 94 C 30 108, 12 88, 28 72" stroke="{{ $primaryColor }}" stroke-width="2.1" stroke-opacity="0.8" />
                    <path d="M88 38 C 118 22, 134 52, 108 66 C 88 76, 78 52, 98 44" stroke="{{ $accentColor }}" stroke-width="2" stroke-opacity="0.9" />
                    <path d="M138 14 Q 168 38, 198 12" stroke="{{ $secondaryColor }}" stroke-width="1.8" stroke-opacity="0.65" />
                    <path d="M168 48 C 194 34, 212 52, 184 72" stroke="{{ $primaryColor }}" stroke-width="1.9" stroke-opacity="0.75" />
                    <path d="M42 118 Q 74 108, 92 132" stroke="{{ $secondaryColor }}" stroke-width="1.7" stroke-opacity="0.7" />
                    <path d="M12 138 C 36 128, 46 156, 22 178" stroke="{{ $accentColor }}" stroke-width="2" stroke-opacity="0.85" />
                    <path d="M214 14 Q 244 34, 272 10" stroke="{{ $primaryColor }}" stroke-width="1.7" stroke-opacity="0.7" />
                    <path d="M126 76 L 146 66" stroke="{{ $secondaryColor }}" stroke-width="2.2" stroke-opacity="0.65" />
                    <path d="M72 82 L 82 96" stroke="{{ $primaryColor }}" stroke-width="2" stroke-opacity="0.7" />
                    <!-- Calligraphic Diamond Nuqta Marks -->
                    <polygon points="62,22 67,27 62,32 57,27" fill="{{ $primaryColor }}" fill-opacity="0.8" />
                    <polygon points="154,28 159,33 154,38 149,33" fill="{{ $accentColor }}" fill-opacity="0.9" />
                    <polygon points="34,110 38,114 34,118 30,114" fill="{{ $secondaryColor }}" fill-opacity="0.7" />
                    <polygon points="104,58 108,62 104,66 100,62" fill="{{ $primaryColor }}" fill-opacity="0.75" />
                </g>
                <!-- Double Curved Bronze & Charcoal Divider Arcs -->
                <path d="M312 0 C 224 44, 128 84, 52 178 C 28 206, 12 215, 0 218"
                      fill="none" stroke="{{ $secondaryColor }}" stroke-width="2" stroke-opacity="0.8" />
                <path d="M294 0 C 210 40, 118 78, 44 168 C 22 195, 8 203, 0 206"
                      fill="none" stroke="{{ $accentColor }}" stroke-width="1.25" stroke-opacity="0.9" />
            </svg>

            <!-- Layer 2B: Bottom-End Balancing Hurufiyya Calligraphic Corner Arc -->
            <svg viewBox="0 0 320 220"
                 class="w-44 h-30 sm:w-64 sm:h-44 absolute bottom-0 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <clipPath id="diriyahBotArcClip">
                        <path d="M320 220 L5 220 C 95 176, 192 136, 268 42 C 292 14, 308 5, 320 2 Z" />
                    </clipPath>
                </defs>
                <path d="M320 220 L5 220 C 95 176, 192 136, 268 42 C 292 14, 308 5, 320 2 Z"
                      fill="{{ $bgColor }}" fill-opacity="0.72" />
                <g clip-path="url(#diriyahBotArcClip)" fill="none" stroke-linecap="round">
                    <path d="M296 196 Q 268 172, 234 202" stroke="{{ $secondaryColor }}" stroke-width="2.2" stroke-opacity="0.75" />
                    <path d="M302 164 C 274 170, 248 144, 272 124 C 290 110, 308 130, 292 146" stroke="{{ $primaryColor }}" stroke-width="2.1" stroke-opacity="0.8" />
                    <path d="M232 182 C 202 198, 186 168, 212 154 C 232 144, 242 168, 222 176" stroke="{{ $accentColor }}" stroke-width="2" stroke-opacity="0.9" />
                    <path d="M182 204 Q 152 180, 122 206" stroke="{{ $secondaryColor }}" stroke-width="1.8" stroke-opacity="0.65" />
                    <path d="M152 170 C 126 184, 108 166, 136 146" stroke="{{ $primaryColor }}" stroke-width="1.9" stroke-opacity="0.75" />
                    <path d="M278 100 Q 246 110, 228 86" stroke="{{ $secondaryColor }}" stroke-width="1.7" stroke-opacity="0.7" />
                    <path d="M308 82 C 284 92, 274 64, 298 42" stroke="{{ $accentColor }}" stroke-width="2" stroke-opacity="0.85" />
                    <path d="M106 204 Q 76 184, 48 208" stroke="{{ $primaryColor }}" stroke-width="1.7" stroke-opacity="0.7" />
                    <polygon points="258,198 263,193 258,188 253,193" fill="{{ $primaryColor }}" fill-opacity="0.8" />
                    <polygon points="166,192 171,187 166,182 161,187" fill="{{ $accentColor }}" fill-opacity="0.9" />
                    <polygon points="286,110 290,106 286,102 282,106" fill="{{ $secondaryColor }}" fill-opacity="0.7" />
                </g>
                <path d="M8 220 C 96 176, 192 136, 268 42 C 292 14, 308 5, 320 2"
                      fill="none" stroke="{{ $secondaryColor }}" stroke-width="2" stroke-opacity="0.8" />
                <path d="M26 220 C 110 180, 202 142, 276 52 C 298 25, 312 17, 320 14"
                      fill="none" stroke="{{ $accentColor }}" stroke-width="1.25" stroke-opacity="0.9" />
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-20 sm:pt-24 pb-24 sm:pb-28 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 mx-auto" aria-hidden="true">
                                <line x1="12" y1="10" x2="80" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.8" />
                                <line x1="140" y1="10" x2="208" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.8" />
                                <polygon points="110,3 118,10 110,17 102,10" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" />
                                <polygon points="110,6.5 113.5,10 110,13.5 106.5,10" fill="{{ $primaryColor }}" />
                                <polygon points="90,10 94,6 98,10 94,14" fill="{{ $accentColor }}" />
                                <polygon points="122,10 126,6 130,10 126,14" fill="{{ $accentColor }}" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7]"
                               style="color: {{ $secondaryColor }};">
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

                <!-- Host & Opening Phrase -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-85" style="color: {{ $primaryColor }};">
                        {{ $isEnInv ? 'In the Spirit of Najdi Hospitality' : 'يتشرف الجميع بحضوركم الكريم' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Inviting you to celebrate the wedding ceremony of' : 'بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Poetic Side-by-Side Couple Triad + Translucent Name Watermark Echo -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.04]"
                         aria-hidden="true">
                        @if($hasBrideName)
                            <span class="font-amiri text-4xl sm:text-6xl font-bold leading-tight whitespace-nowrap"
                                  style="color: {{ $primaryColor }};">{{ $brideParts['first'] }}</span>
                        @endif
                        <span class="font-amiri text-4xl sm:text-6xl font-bold leading-tight whitespace-nowrap {{ $hasBrideName ? '-mt-2' : '' }}"
                              style="color: {{ $secondaryColor }};">{{ $groomParts['first'] }}</span>
                    </div>

                    @if($hasBrideName)
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'كريمة' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
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

                            <!-- Center Ornate Calligraphic Ligature (Ref Image 5 Inspired) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 48 92" class="w-8 h-16 sm:w-11 sm:h-20 mx-auto overflow-visible">
                                        <path d="M28 16 C 36 16, 39 25, 34 34 C 29 42, 17 42, 14 34 C 11 26, 19 16, 28 16 Z M30 22 C 25 22, 21 26, 22 31 C 23 36, 30 36, 32 30 C 33 27, 32 23, 30 22 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M35 30 C 37 49, 29 66, 10 80 C 8 82, 6 79, 9 76 C 22 63, 28 49, 28 34 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M18 46 Q 30 48, 39 40" fill="none" stroke="{{ $accentColor }}" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $primaryColor }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'ابننا' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
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
                                  style="color: {{ $primaryColor }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'ابننا' }}
                            </span>
                            <span class="font-amiri text-4xl sm:text-[3.4rem] font-bold leading-[1.18] break-words max-w-full"
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
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-opacity="0.7" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto p-1.5 border-2 shadow-sm overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: {{ $bgColor }};">
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

                <!-- Layer 5: Reference Image 5 Split Date/Venue Hairline Ledger -->
                <section class="space-y-4 font-tajawal">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'By the Grace of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x sm:rtl:divide-x-reverse border-y py-3.5 text-center"
                         style="border-color: {{ $accentColor }}66;">
                        <!-- Date, Hijri & Time Column -->
                        <div class="pb-3.5 sm:pb-0 sm:px-4 space-y-1.5 flex flex-col items-center justify-center"
                             style="border-color: {{ $accentColor }}55;">
                            <x-app-icon name="calendar" class="w-4 h-4 mb-0.5" style="color: {{ $primaryColor }};" />
                            <div class="text-xs sm:text-sm font-extrabold" style="color: {{ $secondaryColor }};">
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </div>
                            @if($dateInfo['hijri_date'])
                                <div class="text-xs font-semibold opacity-85" style="color: {{ $primaryColor }};">
                                    @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                                </div>
                            @endif
                            @if($dateInfo['time'])
                                <div class="inline-flex items-center gap-1.5 text-xs font-semibold opacity-85 pt-0.5">
                                    <x-app-icon name="clock" class="w-3.5 h-3.5 shrink-0" style="color: {{ $primaryColor }};" />
                                    @if($dateInfo['locale'] === 'en')
                                        <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                    @else
                                        <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Venue & Address Column -->
                        <div class="pt-3.5 sm:pt-0 sm:px-4 space-y-1.5 flex flex-col items-center justify-center"
                             style="border-color: {{ $accentColor }}55;">
                            <x-app-icon name="map-pin" class="w-4 h-4 mb-0.5" style="color: {{ $primaryColor }};" />
                            <div class="text-xs sm:text-sm font-extrabold break-words" style="color: {{ $secondaryColor }};">
                                {{ $invitation->venue_name }}
                            </div>
                            @if($invitation->venue_address)
                                <p class="text-[11px] opacity-75 leading-relaxed break-words max-w-xs">
                                    {{ $invitation->venue_address }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($invitation->venue_map_url)
                        <div class="pt-1">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-extrabold tracking-wide shadow-xs hover:opacity-90 transition border"
                               style="background-color: {{ $secondaryColor }}; border-color: {{ $accentColor }}; color: #FBF7F0;">
                                <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: {{ $accentColor }};" />
                                <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                            </a>
                        </div>
                    @endif
                </section>

                <!-- Layer 6: Bespoke 3-Stage Saudi Ceremony Thread -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $accentColor,
                    'cardColor' => $cardColor,
                    'variant' => 'thread',
                ])

                <!-- Closing Gratitude & Diwani Seal -->
                <footer class="pt-2 space-y-2">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Your presence is an honor · May joy unite us' : 'حضوركم تشريف لنا · وبحضوركم تكتمل فرحتنا' }}
                    </p>
                    <svg viewBox="0 0 220 28" class="w-36 sm:w-44 h-6 mx-auto" aria-hidden="true">
                        <path d="M18 16 C 56 24, 86 6, 110 14 C 134 22, 166 5, 202 14"
                              fill="none"
                              stroke="{{ $primaryColor }}"
                              stroke-width="1.45"
                              stroke-opacity="0.72"
                              stroke-linecap="round" />
                        <polygon points="110,9 116,14 110,19 104,14" fill="{{ $accentColor }}" fill-opacity="0.65" />
                    </svg>
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
