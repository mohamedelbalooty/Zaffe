@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#141413';
    $secondaryColor = $styles['secondary_color'] ?? '#2B2927';
    $bgColor = $styles['background_color'] ?? '#F5F2EB';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#C5A869';
    $goldInk = '#8A6829';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));

    $groomParts = \App\Services\InvitationDateFormatter::splitCoupleName($invitation->groom_name ?? '');
    $brideParts = $hasBrideName
        ? \App\Services\InvitationDateFormatter::splitCoupleName($invitation->bride_name)
        : ['first' => '', 'lineage' => ''];
    $groomInitial = mb_substr($groomParts['first'] !== '' ? $groomParts['first'] : 'ف', 0, 1);
    $brideInitial = $hasBrideName ? mb_substr($brideParts['first'], 0, 1) : '';
    $ceremonyStages = \App\Services\InvitationDateFormatter::ceremonyStages($invitation, $dateInfo);
@endphp

<div class="min-h-screen py-7 sm:py-14 px-3 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 68% 48% at 15% 10%, {{ $accentColor }}1F 0%, transparent 65%), radial-gradient(ellipse 62% 46% at 88% 92%, {{ $primaryColor }}0D 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #EAE4D8 100%); color: {{ $primaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Haute-Couture Embossed Botanical Line-Art & Monogram Stationery (Ref Image 2) -->
        <article class="relative overflow-hidden shadow-[0_28px_72px_-18px_rgba(20,20,19,0.18)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 42%, #FFFFFF 0%, #FAF8F5 62%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Full-Canvas Oversized Embossed Botanical Line-Art Watermark (Ref Image 2) -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <!-- Oversized Upper Peony/Lotus Contour -->
                <g fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.065" stroke-width="0.95">
                    <path d="M360 -20 C 440 35, 495 110, 465 185 C 520 235, 460 320, 385 280 C 310 325, 240 245, 285 170 C 235 100, 295 10, 360 -20 Z" />
                    <path d="M360 25 C 415 65, 452 115, 430 165 C 468 202, 422 260, 368 232 C 316 262, 268 205, 300 155 C 266 105, 310 42, 360 25 Z" />
                    <path d="M360 72 C 392 98, 412 128, 396 158 C 418 182, 390 218, 360 200 C 328 218, 302 182, 322 154 C 306 126, 330 88, 360 72 Z" />
                    <path d="M275 215 Q 205 275, 165 360" />
                    <path d="M225 268 Q 175 245, 168 292 Q 204 306, 225 268 Z" />
                    <path d="M465 215 C 535 240, 585 295, 620 355" />
                </g>

                <!-- Oversized Lower Botanical Lotus Contour -->
                <g fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.06" stroke-width="0.95">
                    <path d="M125 650 C 205 668, 255 725, 232 802 C 272 855, 200 925, 130 895 C 58 925, -10 855, 28 790 C -12 720, 58 650, 125 650 Z" />
                    <path d="M125 692 C 178 706, 212 746, 194 796 C 222 832, 174 878, 125 858 C 76 878, 32 830, 58 788 C 32 742, 76 692, 125 692 Z" />
                    <path d="M225 685 Q 298 615, 330 535" />
                    <path d="M278 632 Q 326 644, 322 596 Q 282 590, 278 632 Z" />
                    <path d="M485 735 C 530 705, 585 720, 615 768" />
                </g>
            </svg>

            <!-- Double Hairline Couture Perimeter -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $primaryColor }}2E;">
                <div class="absolute inset-1.5 border" style="border-color: {{ $accentColor }}66;"></div>
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $cardColor }};"></span>
            </div>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-12 sm:pt-16 pb-16 sm:pb-20 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Couture Monogram Medallion + Calligraphic Blessing Crown -->
                <header class="space-y-3 pt-1">
                    <!-- Isolated HarfBuzz-Safe Couture Monogram Seal -->
                    <div class="flex flex-col items-center gap-2.5">
                        <div class="w-16 h-16 rotate-45 border flex items-center justify-center shadow-2xs"
                             style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                            <div class="w-13 h-13 border flex items-center justify-center"
                                 style="border-color: {{ $accentColor }};">
                                <span class="-rotate-45 inline-flex items-center justify-center gap-1.5 font-amiri text-lg font-bold leading-none" style="color: {{ $primaryColor }};">
                                    <span class="inline-block">{{ $groomInitial }}</span>@if($brideInitial !== '')<span class="w-px h-4 inline-block shrink-0" style="background-color: {{ $goldInk }};"></span><span class="inline-block">{{ $brideInitial }}</span>@endif
                                </span>
                            </div>
                        </div>

                        @if(!$isEnInv)
                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7] pt-1"
                               style="color: {{ $primaryColor }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>
                        @else
                            <span class="font-playfair tracking-[0.22em] uppercase text-[10px] pt-1" style="color: {{ $goldInk }};" dir="ltr">
                                ATELIER EDITION
                            </span>
                        @endif
                    </div>

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-sm sm:text-base leading-[1.95] max-w-md mx-auto pt-0.5 opacity-85"
                             style="color: {{ $secondaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif
                </header>

                <!-- Host & Opening Phrase -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-85" style="color: {{ $goldInk }};">
                        {{ $isEnInv ? 'Together With Their Families' : 'تتشرف العائلتان' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wide opacity-80" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Cordially invite you to the wedding celebration of' : 'بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Haute-Couture Side-by-Side Couple Triad (Ref Image 2) -->
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
                                      style="color: {{ $goldInk }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Bride' : 'ابنتنا' }}
                                </span>
                                <span class="font-cairo text-[1.7rem] sm:text-[2.7rem] font-black leading-[1.18] break-words w-full"
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

                            <!-- Sculptural Vertical Couture Waw (Ref Image 2) -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 42 88" class="w-7 h-14 sm:w-9 sm:h-20 mx-auto overflow-visible">
                                        <path d="M23 14 C 29 14, 32 22, 29 30 C 26 37, 17 37, 15 30 C 13 23, 18 14, 23 14 Z M24 20 C 21 20, 19 24, 19.5 28 C 20 32, 25 32, 26 27 C 27 24, 26 21, 24 20 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M29 28 C 31 46, 25 63, 11 76 C 9 78, 8 75, 10 72 C 20 60, 24 47, 24 32 Z"
                                              fill="{{ $primaryColor }}" />
                                        <line x1="21" y1="6" x2="21" y2="11" stroke="{{ $accentColor }}" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $goldInk }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $goldInk }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'ابننا' }}
                                </span>
                                <span class="font-cairo text-[1.7rem] sm:text-[2.7rem] font-black leading-[1.18] break-words w-full"
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
                                  style="color: {{ $goldInk }};"
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
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-opacity="0.65" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $primaryColor }}; background-color: #FFFFFF;">
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

                <!-- Layer 5: Winged Couture Hairline Date & Venue Block (Ref Image 2) -->
                <section class="space-y-4 font-tajawal">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'By the Grace of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="py-3 px-2 border-y text-xs sm:text-sm font-bold space-y-1.5"
                         style="border-color: {{ $primaryColor }}2E; color: {{ $secondaryColor }};">
                        <div class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1">
                            <span class="inline-flex items-center gap-1.5" style="color: {{ $primaryColor }};">
                                <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $goldInk }};" />
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </span>

                            @if($dateInfo['hijri_date'])
                                <span class="h-3.5 w-px inline-block" style="background-color: {{ $primaryColor }}38;" aria-hidden="true"></span>
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
                             style="color: {{ $primaryColor }};">
                            <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $goldInk }};" />
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
                                   style="background-color: {{ $primaryColor }}; border-color: {{ $accentColor }}; color: #FFFFFF;">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: {{ $accentColor }};" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: 3-Stage Couture Column Ceremony Schedule (Ref Image 2) -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $goldInk,
                    'cardColor' => $cardColor,
                    'variant' => 'columns',
                ])

                <footer class="pt-2 space-y-2">
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
            <a href="{{ route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
