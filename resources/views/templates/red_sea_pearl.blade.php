@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#4A7C8C';
    $secondaryColor = $styles['secondary_color'] ?? '#2C3E47';
    $bgColor = $styles['background_color'] ?? '#F6F8F9';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#9EB3BC';
    $pearlGold = '#CBB994';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Tajawal');
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
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 74% 50% at 14% 8%, #FFFFFF 0%, transparent 65%), radial-gradient(ellipse 68% 48% at 86% 90%, {{ $accentColor }}24 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #E7EEF2 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Red Sea Mother-of-Pearl Nacre & White Magnolia Couture Stationery -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-18px_rgba(44,62,71,0.18)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background: linear-gradient(160deg, {{ $cardColor }} 0%, #F9FBFC 56%, #EEF4F7 100%);">

            <!-- Layer 1: Luminous Nacre Silk Wave & Translucent Pearl Ripple -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <linearGradient id="pearlSilkSheen" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.85" />
                        <stop offset="50%" stop-color="{{ $primaryColor }}" stop-opacity="0.04" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.55" />
                    </linearGradient>
                </defs>
                <path d="M-30 140 C 170 280, 410 60, 630 220 L 630 560 C 420 400, 170 620, -30 470 Z"
                      fill="url(#pearlSilkSheen)" />
                <circle cx="300" cy="440" r="170" fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.05" stroke-width="1" />
                <circle cx="300" cy="440" r="125" fill="none" stroke="{{ $accentColor }}" stroke-opacity="0.06" stroke-width="0.9" />
            </svg>

            <!-- Fine Silver-Slate Inlay Perimeter & Corner Pearl Nodes -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $accentColor }}66;">
                <span class="w-2.5 h-2.5 rounded-full border absolute -top-1.5 -start-1.5 shadow-2xs" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -top-1.5 -end-1.5 shadow-2xs" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -bottom-1.5 -start-1.5 shadow-2xs" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -bottom-1.5 -end-1.5 shadow-2xs" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
            </div>

            <!-- Layer 2A: Top-Start Sculpted Bas-Relief Shell Fan & White Magnolia Bloom (Ref Images 9 & 12) -->
            <svg viewBox="0 0 260 220"
                 class="w-36 h-32 sm:w-52 sm:h-44 absolute top-0 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <linearGradient id="pearlShellGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" />
                        <stop offset="65%" stop-color="#E7EFF3" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.65" />
                    </linearGradient>
                    <radialGradient id="pearlMagnoliaGrad" cx="45%" cy="40%" r="68%">
                        <stop offset="0%" stop-color="#FFFFFF" />
                        <stop offset="62%" stop-color="#EDF3F6" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.72" />
                    </radialGradient>
                </defs>

                <!-- Sculpted Nacre Fluted Shell Fan Relief -->
                <g transform="translate(8, 8)">
                    <path d="M0 0 L 122 18 C 118 68, 84 108, 36 122 L 0 0 Z"
                          fill="url(#pearlShellGrad)" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.7" />
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.28" stroke-width="0.9">
                        <line x1="0" y1="0" x2="112" y2="34" />
                        <line x1="0" y1="0" x2="98" y2="58" />
                        <line x1="0" y1="0" x2="80" y2="82" />
                        <line x1="0" y1="0" x2="58" y2="104" />
                    </g>
                </g>

                <!-- White Gardenia / Magnolia Blossom Overlay -->
                <path d="M56 76 C 88 94, 128 96, 158 84" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.2" stroke-opacity="0.5" />
                <g transform="translate(64, 56) rotate(-12)">
                    <path d="M0 -34 C 16 -32, 22 -14, 4 -4 C 22 -18, 36 -4, 28 14 C 20 26, 4 16, 2 4 C 16 18, 8 36, -8 34 C -24 32, -20 12, -3 4 C -20 18, -36 8, -30 -10 C -24 -24, -8 -16, -2 -4 C -14 -18, -10 -34, 0 -34 Z"
                          fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="0.9" />
                    <circle cx="0" cy="0" r="5.5" fill="{{ $pearlGold }}" />
                    <circle cx="0" cy="0" r="2.2" fill="#FFFFFF" />
                </g>
            </svg>

            <!-- Layer 2B: Bottom-End Sculpted Nacre Shell & Magnolia Garden + Drifting Petals (Ref Images 9 & 12) -->
            <svg viewBox="0 0 280 220"
                 class="w-40 h-32 sm:w-56 sm:h-46 absolute bottom-0 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <g transform="translate(272, 212) rotate(180)">
                    <path d="M0 0 L 128 20 C 122 72, 86 112, 38 128 L 0 0 Z"
                          fill="url(#pearlShellGrad)" stroke="{{ $accentColor }}" stroke-width="0.9" stroke-opacity="0.7" />
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.28" stroke-width="0.9">
                        <line x1="0" y1="0" x2="116" y2="38" />
                        <line x1="0" y1="0" x2="100" y2="64" />
                        <line x1="0" y1="0" x2="82" y2="88" />
                        <line x1="0" y1="0" x2="60" y2="110" />
                    </g>
                </g>

                <g transform="translate(210, 158) rotate(14)">
                    <path d="M0 -34 C 16 -32, 22 -14, 4 -4 C 22 -18, 36 -4, 28 14 C 20 26, 4 16, 2 4 C 16 18, 8 36, -8 34 C -24 32, -20 12, -3 4 C -20 18, -36 8, -30 -10 C -24 -24, -8 -16, -2 -4 C -14 -18, -10 -34, 0 -34 Z"
                          fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="0.9" />
                    <circle cx="0" cy="0" r="5.5" fill="{{ $pearlGold }}" />
                    <circle cx="0" cy="0" r="2.2" fill="#FFFFFF" />
                </g>

                <!-- Floating Delicate White Blossom Petals (Ref Image 12) -->
                <g transform="translate(116, 178) scale(0.45)">
                    <circle cx="0" cy="-12" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="11" cy="4" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="-11" cy="4" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="0" cy="0" r="4" fill="{{ $pearlGold }}" />
                </g>
                <g transform="translate(64, 188) scale(0.36) rotate(22)">
                    <circle cx="0" cy="-12" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="11" cy="4" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="-11" cy="4" r="10" fill="url(#pearlMagnoliaGrad)" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="0" cy="0" r="4" fill="{{ $pearlGold }}" />
                </g>
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-16 sm:pt-20 pb-24 sm:pb-28 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-2.5 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <svg viewBox="0 0 180 20" class="w-36 sm:w-44 h-4 mx-auto" aria-hidden="true">
                                <line x1="12" y1="10" x2="68" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" />
                                <line x1="112" y1="10" x2="168" y2="10" stroke="{{ $accentColor }}" stroke-width="0.9" />
                                <circle cx="90" cy="10" r="4.5" fill="#FFFFFF" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                                <circle cx="76" cy="10" r="1.5" fill="{{ $accentColor }}" />
                                <circle cx="104" cy="10" r="1.5" fill="{{ $accentColor }}" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7]"
                               style="color: {{ $primaryColor }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                            <span class="w-2.5 h-2.5 rounded-full border" style="border-color: {{ $primaryColor }}; background-color: #FFFFFF;"></span>
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
                        {{ $isEnInv ? 'Serenity by the Red Sea' : 'تتشرف العائلتان الكريمتان' }}
                    </p>
                    <p class="text-xs sm:text-sm font-semibold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Inviting you with joy to the wedding of' : 'بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Side-by-Side Couple Triad + Translucent First-Name Echo -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <div class="pointer-events-none select-none absolute inset-0 flex flex-col items-center justify-center overflow-hidden opacity-[0.04]"
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
                                <span class="text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $secondaryColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'ابنتنا' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $brideParts['first'] }}
                                </span>
                                @if($brideParts['lineage'] !== '')
                                    <span class="text-[11px] sm:text-sm font-medium opacity-80 break-words w-full leading-snug"
                                          style="color: {{ $secondaryColor }};"
                                          aria-hidden="true">
                                        {{ $brideParts['lineage'] }}
                                    </span>
                                @endif
                            </span>

                            <!-- Looped Coastal Calligraphic Waw (Ref Image 12) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 46 86" class="w-7 h-14 sm:w-10 sm:h-20 mx-auto overflow-visible">
                                        <path d="M26 18 C 33 18, 36 26, 32 34 C 28 41, 18 41, 15 34 C 12 27, 19 18, 26 18 Z M28 24 C 24 23, 21 27, 22 31 C 23 35, 29 35, 30 30 C 31 27, 30 25, 28 24 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M33 31 C 35 47, 29 63, 13 75 C 11 77, 9 74, 11 71 C 22 60, 27 48, 27 35 Z"
                                              fill="{{ $primaryColor }}" />
                                        <circle cx="26" cy="11" r="2" fill="{{ $pearlGold }}" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $primaryColor }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $secondaryColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'الابن' }}
                                </span>
                                <span class="font-amiri text-[1.85rem] sm:text-[3rem] font-bold leading-[1.18] break-words w-full"
                                      style="color: {{ $primaryColor }};"
                                      aria-hidden="true">
                                    {{ $groomParts['first'] }}
                                </span>
                                @if($groomParts['lineage'] !== '')
                                    <span class="text-[11px] sm:text-sm font-medium opacity-80 break-words w-full leading-snug"
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
                            <span class="text-xs sm:text-sm font-bold tracking-wide opacity-75"
                                  style="color: {{ $secondaryColor }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'الابن' }}
                            </span>
                            <span class="font-amiri text-4xl sm:text-[3.4rem] font-bold leading-[1.18] break-words max-w-full"
                                  style="color: {{ $primaryColor }};"
                                  aria-hidden="true">
                                {{ $groomParts['first'] }}
                            </span>
                            @if($groomParts['lineage'] !== '')
                                <span class="text-xs sm:text-base font-medium opacity-80 break-words max-w-md"
                                      style="color: {{ $secondaryColor }};"
                                      aria-hidden="true">
                                    {{ $groomParts['lineage'] }}
                                </span>
                            @endif
                            <svg viewBox="0 0 200 14" class="w-40 sm:w-52 h-3 pt-1" aria-hidden="true">
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-opacity="0.65" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-xs sm:text-sm leading-[1.95] px-2 max-w-md mx-auto opacity-85"
                       style="color: {{ $secondaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Layer 5: Symmetrical Pearl Hairline Date & Venue Block -->
                <section class="space-y-4">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'With the Blessings of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="py-3 px-2 border-y text-xs sm:text-sm font-bold space-y-1.5"
                         style="border-color: {{ $accentColor }}80; color: {{ $secondaryColor }};">
                        <div class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1">
                            <span class="inline-flex items-center gap-1.5" style="color: {{ $primaryColor }};">
                                <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
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
                                   class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-bold tracking-wide shadow-xs hover:opacity-90 transition border"
                                   style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}; color: {{ $secondaryColor }};">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: {{ $primaryColor }};" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: Continuous Thread Ceremony Schedule (Ref Image 12) -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $accentColor,
                    'cardColor' => $cardColor,
                    'variant' => 'thread',
                ])

                <footer class="pt-2 space-y-2">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Grateful for your presence · With you, our joy is complete' : 'بحضوركم تكتمل فرحتنا' }}
                    </p>
                    <p class="text-[10px] sm:text-[11px] opacity-60 tracking-wide">
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

        <div class="text-center text-xs opacity-70 pt-3 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
