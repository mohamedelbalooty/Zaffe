@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#8E4A64';
    $secondaryColor = $styles['secondary_color'] ?? '#3B2E35';
    $bgColor = $styles['background_color'] ?? '#F3EDF1';
    $cardColor = $styles['card_color'] ?? '#FAF7F9';
    $accentColor = $styles['accent_color'] ?? '#7A8F7B';
    $champagneGold = '#C5A47E';
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
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 68% 48% at 10% 8%, {{ $primaryColor }}1A 0%, transparent 65%), radial-gradient(ellipse 62% 46% at 92% 90%, {{ $accentColor }}1C 0%, transparent 65%), linear-gradient(180deg, {{ $bgColor }} 0%, #EAE1E7 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-[38rem] mx-auto relative z-10 space-y-7">
        <!-- Couture Silk-Linen Saudi Wedding Stationery Canvas -->
        <article class="relative overflow-hidden shadow-[0_28px_70px_-18px_rgba(59,46,53,0.22)] min-h-[760px] sm:min-h-[860px] flex flex-col justify-between"
                 style="background-color: {{ $cardColor }}; background-image: radial-gradient(circle at 50% 38%, #FFFFFF 0%, {{ $cardColor }} 62%, {{ $bgColor }} 100%);">

            <!-- Layer 1: Luminous Silk Drape Sheen & Embossed Botanical Line-Art Watermark -->
            <svg viewBox="0 0 600 900"
                 preserveAspectRatio="xMidYMid slice"
                 class="absolute inset-0 w-full h-full pointer-events-none select-none"
                 aria-hidden="true">
                <defs>
                    <linearGradient id="taifSilkWave" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.65" />
                        <stop offset="50%" stop-color="{{ $primaryColor }}" stop-opacity="0.03" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.45" />
                    </linearGradient>
                </defs>

                <!-- Flowing Silk Drape Fold -->
                <path d="M-40 150 C 170 290, 410 40, 640 210 L 640 540 C 410 380, 170 610, -40 460 Z"
                      fill="url(#taifSilkWave)" />
                <path d="M-20 620 C 190 500, 390 730, 630 580 L 630 900 L -20 900 Z"
                      fill="#FFFFFF"
                      fill-opacity="0.28" />

                <!-- Embossed Upper-End Botanical Peony Contour -->
                <g fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.065" stroke-width="1.15">
                    <circle cx="515" cy="145" r="95" />
                    <path d="M515 40 C 565 75, 590 120, 565 170 C 595 215, 540 265, 485 240 C 435 270, 395 215, 420 165 C 390 115, 445 65, 515 40 Z" />
                    <path d="M515 72 C 548 95, 562 128, 542 158 C 562 188, 526 222, 492 204 C 460 222, 434 186, 452 154 C 432 122, 468 88, 515 72 Z" />
                    <path d="M405 220 Q 345 275, 315 345" />
                    <path d="M362 268 Q 318 250, 310 292 Q 342 302, 362 268 Z" />
                </g>

                <!-- Embossed Lower-Start Botanical Lotus/Rose Contour -->
                <g fill="none" stroke="{{ $primaryColor }}" stroke-opacity="0.06" stroke-width="1.1">
                    <path d="M85 695 C 145 710, 185 755, 165 815 C 195 855, 140 905, 85 885 C 30 905, -20 850, 12 800 C -18 745, 35 695, 85 695 Z" />
                    <path d="M85 732 C 122 742, 146 768, 134 804 C 152 828, 118 858, 85 846 C 52 858, 22 824, 42 794 C 24 760, 56 732, 85 732 Z" />
                    <path d="M168 725 Q 230 665, 255 595" />
                    <path d="M214 676 Q 254 686, 250 644 Q 218 640, 214 676 Z" />
                </g>
            </svg>

            <!-- Fine Champagne-Gold Stationery Hairline Perimeter -->
            <div class="pointer-events-none absolute inset-3 sm:inset-4 border z-10"
                 style="border-color: {{ $champagneGold }}4D;">
                <span class="absolute -top-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $champagneGold }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -top-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $champagneGold }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -start-1 w-2 h-2 rotate-45 border" style="border-color: {{ $champagneGold }}; background-color: {{ $cardColor }};"></span>
                <span class="absolute -bottom-1 -end-1 w-2 h-2 rotate-45 border" style="border-color: {{ $champagneGold }}; background-color: {{ $cardColor }};"></span>
            </div>

            <!-- Layer 2A: Top-Start Watercolor Taif Rose & Eucalyptus Cascade -->
            <svg viewBox="0 0 260 230"
                 class="w-36 h-32 sm:w-52 sm:h-46 absolute top-0 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="taifTopHalo" cx="38%" cy="34%" r="55%">
                        <stop offset="0%" stop-color="{{ $primaryColor }}" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0" />
                    </radialGradient>
                    <radialGradient id="taifTopPetal" cx="45%" cy="35%" r="68%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.95" />
                        <stop offset="52%" stop-color="{{ $primaryColor }}" stop-opacity="0.42" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0.88" />
                    </radialGradient>
                    <linearGradient id="taifTopLeaf" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="{{ $accentColor }}" stop-opacity="0.68" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.25" />
                    </linearGradient>
                </defs>

                <!-- Soft Watercolor Blush Diffusion -->
                <circle cx="72" cy="58" r="64" fill="url(#taifTopHalo)" />

                <!-- Ghost Translucent Cosmos Silhouette -->
                <g transform="translate(116, 44) rotate(18)" opacity="0.24" fill="{{ $primaryColor }}">
                    <path d="M0 -34 C 12 -32, 16 -12, 4 -4 C 18 -14, 34 -4, 30 10 C 26 22, 8 18, 2 6 C 14 20, 4 36, -10 32 C -22 28, -16 10, -4 4 C -18 14, -34 2, -28 -12 C -22 -24, -6 -16, -2 -4 C -12 -18, -8 -34, 0 -34 Z" />
                </g>

                <!-- Mountain Sage & Eucalyptus Stems -->
                <path d="M-8 10 C 42 46, 98 92, 168 122" fill="none" stroke="{{ $accentColor }}" stroke-width="1.35" stroke-opacity="0.8" />
                <path d="M22 -4 C 52 54, 46 118, 28 176" fill="none" stroke="{{ $accentColor }}" stroke-width="1.2" stroke-opacity="0.72" />

                <!-- Layered Veined Sage Leaves -->
                <path d="M98 72 Q 124 44, 152 54 Q 134 86, 98 72 Z" fill="url(#taifTopLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.6" />
                <path d="M126 98 Q 162 94, 180 116 Q 150 128, 126 98 Z" fill="url(#taifTopLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.55" />
                <path d="M36 96 Q 66 112, 56 146 Q 28 130, 36 96 Z" fill="url(#taifTopLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.6" />
                <path d="M26 128 Q 4 148, 18 178 Q 42 158, 26 128 Z" fill="url(#taifTopLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.5" />

                <!-- Primary 6-Petal Taif Damask Cosmos -->
                <g transform="translate(56, 52) rotate(-14)">
                    <path d="M0 -42 C 14 -40, 20 -20, 4 -5 C 20 -26, 40 -14, 34 6 C 28 22, 10 16, 3 4 C 24 14, 24 38, 4 40 C -14 42, -16 18, -2 5 C -16 24, -38 22, -38 2 C -38 -16, -16 -14, -4 -2 C -24 -12, -26 -36, -8 -42 Z"
                          fill="url(#taifTopPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.9"
                          stroke-opacity="0.55" />
                    <!-- Inner Ruffled Petals -->
                    <path d="M0 -22 C 9 -20, 12 -8, 2 -2 C 14 -10, 24 0, 16 12 C 10 20, 2 10, 0 2 C 6 14, -6 24, -14 14 C -22 6, -10 -4, -2 -2 C -14 -8, -12 -22, 0 -22 Z"
                          fill="{{ $primaryColor }}"
                          fill-opacity="0.34"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.7"
                          stroke-opacity="0.5" />
                    <!-- Delicate Petal Veins -->
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.32" stroke-width="0.6">
                        <line x1="0" y1="-8" x2="0" y2="-32" />
                        <line x1="7" y1="-5" x2="24" y2="-12" />
                        <line x1="6" y1="6" x2="20" y2="24" />
                        <line x1="-6" y1="6" x2="-24" y2="12" />
                        <line x1="-7" y1="-5" x2="-22" y2="-22" />
                    </g>
                    <!-- Champagne Gold Pollen Center -->
                    <circle cx="0" cy="0" r="6.5" fill="{{ $champagneGold }}" fill-opacity="0.9" />
                    <circle cx="0" cy="0" r="3" fill="{{ $secondaryColor }}" fill-opacity="0.45" />
                </g>

                <!-- Half-Open Rosebud -->
                <g transform="translate(138, 66) rotate(36)">
                    <path d="M0 -18 C 10 -14, 12 2, 0 12 C -12 2, -10 -14, 0 -18 Z"
                          fill="url(#taifTopPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.85"
                          stroke-opacity="0.65" />
                    <path d="M-6 8 Q 0 16, 6 8" fill="none" stroke="{{ $accentColor }}" stroke-width="1.3" />
                </g>
            </svg>

            <!-- Layer 2B: Bottom-Start Rising Watercolor Cosmos Garden Bed -->
            <svg viewBox="0 0 260 250"
                 class="w-44 h-40 sm:w-64 sm:h-56 absolute bottom-0 start-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="taifBotHalo" cx="42%" cy="62%" r="54%">
                        <stop offset="0%" stop-color="{{ $primaryColor }}" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0" />
                    </radialGradient>
                    <radialGradient id="taifBotPetal" cx="48%" cy="44%" r="65%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.96" />
                        <stop offset="56%" stop-color="{{ $primaryColor }}" stop-opacity="0.45" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0.92" />
                    </radialGradient>
                    <linearGradient id="taifBotLeaf" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="{{ $accentColor }}" stop-opacity="0.72" />
                        <stop offset="100%" stop-color="{{ $accentColor }}" stop-opacity="0.22" />
                    </linearGradient>
                </defs>

                <!-- Atmospheric Watercolor Halo -->
                <circle cx="88" cy="170" r="78" fill="url(#taifBotHalo)" />

                <!-- Ghost Background Cosmos (Depth Layer) -->
                <g transform="translate(124, 146) scale(1.15)" opacity="0.22" fill="{{ $primaryColor }}">
                    <path d="M0 -38 C 12 -36, 18 -14, 3 -4 C 18 -20, 38 -8, 32 10 C 26 24, 8 18, 2 5 C 18 22, 6 40, -10 34 C -24 28, -16 10, -3 4 C -22 14, -38 0, -30 -16 C -22 -28, -6 -16, -2 -4 C -14 -20, -10 -38, 0 -38 Z" />
                </g>

                <!-- Rising Botanical Stems -->
                <path d="M44 252 C 48 202, 62 152, 74 114" fill="none" stroke="{{ $accentColor }}" stroke-width="1.5" stroke-opacity="0.82" />
                <path d="M98 252 C 102 206, 128 166, 168 124" fill="none" stroke="{{ $accentColor }}" stroke-width="1.2" stroke-opacity="0.7" />
                <path d="M22 252 C 18 214, 24 184, 32 152" fill="none" stroke="{{ $accentColor }}" stroke-width="1.1" stroke-opacity="0.65" />

                <!-- Rising Sage Leaves -->
                <path d="M62 186 Q 94 176, 112 198 Q 84 214, 62 186 Z" fill="url(#taifBotLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.55" />
                <path d="M48 166 Q 16 154, 14 182 Q 40 192, 48 166 Z" fill="url(#taifBotLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.55" />
                <path d="M126 176 Q 158 184, 152 212 Q 124 202, 126 176 Z" fill="url(#taifBotLeaf)" stroke="{{ $accentColor }}" stroke-width="0.8" stroke-opacity="0.5" />

                <!-- Foreground Hero Cosmos Bloom -->
                <g transform="translate(76, 122) rotate(12)">
                    <path d="M0 -46 C 14 -44, 20 -22, 4 -6 C 22 -28, 46 -14, 38 8 C 30 24, 10 16, 4 5 C 28 16, 28 42, 4 44 C -14 46, -18 18, -3 6 C -18 28, -42 24, -42 2 C -42 -18, -18 -16, -4 -3 C -26 -14, -28 -40, -8 -46 Z"
                          fill="url(#taifBotPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.95"
                          stroke-opacity="0.58" />
                    <path d="M0 -24 C 10 -22, 14 -8, 2 -2 C 16 -12, 26 2, 18 14 C 10 22, 2 10, 0 2 C 6 16, -8 26, -16 14 C -24 4, -10 -4, -2 -2 C -16 -8, -12 -24, 0 -24 Z"
                          fill="{{ $primaryColor }}"
                          fill-opacity="0.34" />
                    <g stroke="{{ $primaryColor }}" stroke-opacity="0.35" stroke-width="0.65">
                        <line x1="0" y1="-8" x2="0" y2="-36" />
                        <line x1="8" y1="-5" x2="28" y2="-14" />
                        <line x1="6" y1="7" x2="22" y2="28" />
                        <line x1="-6" y1="7" x2="-28" y2="12" />
                        <line x1="-8" y1="-5" x2="-24" y2="-26" />
                    </g>
                    <!-- Golden Stamen Cluster -->
                    <circle cx="0" cy="0" r="8" fill="{{ $champagneGold }}" />
                    <circle cx="-2.2" cy="-2" r="1.25" fill="#FFFDF9" />
                    <circle cx="2.5" cy="-1.2" r="1.15" fill="#FFFDF9" />
                    <circle cx="0.6" cy="2.6" r="1.25" fill="#FFFDF9" />
                    <circle cx="-2.6" cy="1.6" r="0.95" fill="#FFFDF9" />
                </g>

                <!-- Secondary Midground Cosmos Bloom -->
                <g transform="translate(154, 152) scale(0.68) rotate(-20)">
                    <path d="M0 -42 C 14 -40, 18 -18, 3 -5 C 20 -24, 42 -10, 34 8 C 28 22, 8 14, 3 4 C 24 14, 24 38, 3 40 C -14 42, -16 16, -2 5 C -16 24, -38 20, -38 2 C -38 -16, -16 -14, -3 -2 C -22 -12, -24 -36, -6 -42 Z"
                          fill="url(#taifBotPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.9"
                          stroke-opacity="0.52" />
                    <circle cx="0" cy="0" r="6.5" fill="{{ $champagneGold }}" />
                </g>

                <!-- Secondary Rising Rosebud & Petals -->
                <g transform="translate(172, 118) rotate(-26)">
                    <path d="M0 -18 C 11 -14, 12 2, 0 12 C -12 2, -11 -14, 0 -18 Z"
                          fill="url(#taifBotPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.85"
                          stroke-opacity="0.62" />
                    <path d="M-6 8 Q 0 15, 6 8" fill="none" stroke="{{ $accentColor }}" stroke-width="1.25" />
                </g>
                <g transform="translate(32, 146) rotate(22)">
                    <path d="M0 -13 C 8 -10, 9 1, 0 9 C -9 1, -8 -10, 0 -13 Z"
                          fill="{{ $primaryColor }}"
                          fill-opacity="0.45"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.8" />
                </g>
            </svg>

            <!-- Layer 2C: Bottom-End Balancing Botanical Spray & Drifting Petals -->
            <svg viewBox="0 0 210 190"
                 class="w-32 h-28 sm:w-44 sm:h-40 absolute bottom-0 end-0 rtl:-scale-x-100 pointer-events-none select-none z-10"
                 aria-hidden="true">
                <defs>
                    <radialGradient id="taifEndPetal" cx="50%" cy="45%" r="65%">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.92" />
                        <stop offset="60%" stop-color="{{ $primaryColor }}" stop-opacity="0.38" />
                        <stop offset="100%" stop-color="{{ $primaryColor }}" stop-opacity="0.82" />
                    </radialGradient>
                </defs>

                <circle cx="150" cy="136" r="46" fill="{{ $primaryColor }}" fill-opacity="0.09" />
                <path d="M172 192 C 166 154, 146 116, 116 88" fill="none" stroke="{{ $accentColor }}" stroke-width="1.3" stroke-opacity="0.75" />
                <path d="M148 148 Q 116 142, 108 164 Q 134 172, 148 148 Z" fill="{{ $accentColor }}" fill-opacity="0.32" stroke="{{ $accentColor }}" stroke-width="0.8" />
                <path d="M142 122 Q 168 106, 178 128 Q 154 140, 142 122 Z" fill="{{ $accentColor }}" fill-opacity="0.28" stroke="{{ $accentColor }}" stroke-width="0.8" />

                <!-- Smaller Tilted Cosmos Bloom -->
                <g transform="translate(152, 134) scale(0.76) rotate(-18)">
                    <path d="M0 -38 C 12 -36, 16 -16, 3 -5 C 18 -22, 36 -10, 30 8 C 24 22, 8 14, 3 4 C 22 14, 22 36, 2 38 C -12 40, -14 16, -2 4 C -16 22, -36 18, -34 0 C -34 -16, -14 -14, -3 -2 C -22 -12, -22 -34, -6 -38 Z"
                          fill="url(#taifEndPetal)"
                          stroke="{{ $primaryColor }}"
                          stroke-width="0.9"
                          stroke-opacity="0.55" />
                    <circle cx="0" cy="0" r="6" fill="{{ $champagneGold }}" />
                </g>

                <!-- Drifting Individual Petals -->
                <path d="M84 162 Q 96 148, 102 162 Q 92 174, 84 162 Z" fill="{{ $primaryColor }}" fill-opacity="0.26" />
                <path d="M52 172 Q 62 162, 68 172 Q 58 182, 52 172 Z" fill="{{ $primaryColor }}" fill-opacity="0.2" />
            </svg>

            <!-- Layer 3: Editorial Content Flow -->
            <div class="relative z-20 px-5 sm:px-14 pt-16 sm:pt-20 pb-24 sm:pb-28 text-center flex-1 flex flex-col justify-between space-y-6 sm:space-y-7">

                <!-- Top Calligraphic Blessing Crown -->
                <header class="space-y-3 pt-1">
                    @if(!$isEnInv)
                        <div class="flex flex-col items-center gap-1.5">
                            <!-- Ornamental Calligraphic Swash Crown -->
                            <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 mx-auto" aria-hidden="true">
                                <line x1="10" y1="10" x2="82" y2="10" stroke="{{ $champagneGold }}" stroke-width="0.85" stroke-opacity="0.7" />
                                <line x1="138" y1="10" x2="210" y2="10" stroke="{{ $champagneGold }}" stroke-width="0.85" stroke-opacity="0.7" />
                                <path d="M92 10 Q 110 2, 128 10 Q 110 18, 92 10 Z" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.1" stroke-opacity="0.75" />
                                <circle cx="110" cy="10" r="2.4" fill="{{ $champagneGold }}" />
                                <circle cx="86" cy="10" r="1.2" fill="{{ $primaryColor }}" fill-opacity="0.6" />
                                <circle cx="134" cy="10" r="1.2" fill="{{ $primaryColor }}" fill-opacity="0.6" />
                            </svg>

                            <p class="font-amiri text-xl sm:text-2xl font-bold leading-[1.7] tracking-normal"
                               style="color: {{ $primaryColor }};">
                                بارك الله لهما وبارك عليهما وجمع بينهما في خير
                            </p>

                            <svg viewBox="0 0 180 12" class="w-32 sm:w-40 h-2.5 mx-auto" aria-hidden="true">
                                <path d="M12 6 C 48 11, 76 1, 90 6 C 104 11, 132 1, 168 6" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.1" stroke-opacity="0.45" />
                            </svg>
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $champagneGold }}80;"></span>
                            <span class="w-2 h-2 rotate-45" style="background-color: {{ $primaryColor }};"></span>
                            <span class="h-px w-12" style="background-color: {{ $champagneGold }}80;"></span>
                        </div>
                    @endif

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-sm sm:text-base leading-[1.95] max-w-md mx-auto pt-1 opacity-85"
                             style="color: {{ $secondaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif
                </header>

                <!-- Host & Opening Invitation Phrase -->
                <div class="space-y-1">
                    <p class="font-amiri text-xs sm:text-sm font-bold opacity-75"
                       style="color: {{ $primaryColor }};">
                        {{ $isEnInv ? 'With Love & Joy' : 'بكل الحب والود' }}
                    </p>
                    <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wide opacity-85"
                       style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'We honorably invite you to the wedding celebration of' : 'نتشرف بدعوتكم لحضور حفل زفاف' }}
                    </p>
                </div>

                <!-- Layer 4: Poetic Side-by-Side Couple Triad + Translucent Name Watermark Echo -->
                <section class="relative py-3 sm:py-5 overflow-hidden">
                    <!-- Oversized Translucent First-Name Watermark Echo -->
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
                        <!-- Side-by-Side Couple Triad (Bride Wing · Calligraphic Waw · Groom Wing) -->
                        <h1 class="relative z-10 grid grid-cols-11 items-center gap-1 sm:gap-3 text-center">
                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->bride_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
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
                                    <span class="font-tajawal text-[11px] sm:text-sm font-medium opacity-80 break-words w-full leading-snug"
                                          style="color: {{ $secondaryColor }};"
                                          aria-hidden="true">
                                        {{ $brideParts['lineage'] }}
                                    </span>
                                @endif
                            </span>

                            <!-- Center Ornate Diwani Calligraphic Ligature -->
                            <span class="col-span-1 flex items-center justify-center" aria-hidden="true">
                                @if(!$isEnInv)
                                    <svg viewBox="0 0 46 88" class="w-7 h-14 sm:w-10 sm:h-20 mx-auto overflow-visible">
                                        <path d="M27 18 C 34 18, 37 26, 33 34 C 29 41, 18 41, 15 34 C 12 27, 19 18, 27 18 Z M29 24 C 25 23, 21 27, 22 31 C 23 35, 29 35, 31 30 C 32 27, 31 25, 29 24 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M34 31 C 36 47, 29 63, 12 76 C 10 78, 8 76, 10 73 C 22 61, 28 48, 28 35 Z"
                                              fill="{{ $primaryColor }}" />
                                        <path d="M23 11 Q 26 7, 30 11" fill="none" stroke="{{ $champagneGold }}" stroke-width="1.3" stroke-linecap="round" />
                                    </svg>
                                @else
                                    <span class="font-playfair italic text-2xl sm:text-4xl font-semibold" style="color: {{ $primaryColor }};">&amp;</span>
                                @endif
                            </span>

                            <span class="col-span-5 flex flex-col items-center space-y-1 min-w-0">
                                <span class="sr-only">{{ $invitation->groom_name }}</span>
                                <span class="font-tajawal text-[11px] sm:text-xs font-bold tracking-wide opacity-75"
                                      style="color: {{ $secondaryColor }};"
                                      aria-hidden="true">
                                    {{ $isEnInv ? 'The Groom' : 'ابننا' }}
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
                        <!-- Single-Name / Kunya Privacy Layout (No Orphan Connector) -->
                        <h1 class="relative z-10 flex flex-col items-center space-y-1.5 text-center">
                            <span class="sr-only">{{ $invitation->groom_name }}</span>
                            <span class="font-tajawal text-xs sm:text-sm font-bold tracking-wide opacity-75"
                                  style="color: {{ $secondaryColor }};"
                                  aria-hidden="true">
                                {{ $isEnInv ? 'Our Son' : 'ابننا' }}
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
                                <path d="M8 7 C 58 13, 142 13, 192 5" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-opacity="0.65" stroke-linecap="round" />
                            </svg>
                        </h1>
                    @endif
                </section>

                <!-- Optional Portrait Frame (Photo-Free Safe) -->
                @if($invitation->cover_photo)
                    <div class="py-1">
                        <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto rounded-full p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $champagneGold }}99; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
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
                        {{ $isEnInv ? 'With the Blessings of Allah' : 'وذلك بمشيئة الله تعالى' }}
                    </p>

                    <div class="py-3 px-2 border-y text-xs sm:text-sm font-bold space-y-1.5"
                         style="border-color: {{ $primaryColor }}38; color: {{ $secondaryColor }};">
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

                    <!-- Venue Name, Address & Subtle Stationery Map CTA -->
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
                                   style="background-color: {{ $secondaryColor }}; border-color: {{ $champagneGold }}80; color: #FAF7F9;">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" style="color: {{ $champagneGold }};" />
                                    <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue on Google Maps' : 'الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Layer 6: Bespoke 3-Stage Saudi Ceremony Horizontal Thread -->
                @include('templates.partials.ceremony_stages', [
                    'stages' => $ceremonyStages,
                    'dateInfo' => $dateInfo,
                    'primaryColor' => $primaryColor,
                    'secondaryColor' => $secondaryColor,
                    'accentColor' => $champagneGold,
                    'cardColor' => $cardColor,
                    'variant' => 'thread',
                ])

                <!-- Closing Gratitude & Sweeping Calligraphic Seal -->
                <footer class="pt-2 space-y-2">
                    <p class="font-amiri text-sm sm:text-base font-bold opacity-85" style="color: {{ $secondaryColor }};">
                        {{ $isEnInv ? 'Grateful for your presence · With you, our joy is complete' : 'شاكرين لكم تلبية الدعوة · وبحضوركم تكتمل فرحتنا' }}
                    </p>

                    <!-- Sweeping Diwani Flourish Signature Seal -->
                    <svg viewBox="0 0 220 30" class="w-36 sm:w-44 h-6 mx-auto" aria-hidden="true">
                        <path d="M16 18 C 52 25, 86 6, 110 15 C 134 24, 168 5, 204 14"
                              fill="none"
                              stroke="{{ $primaryColor }}"
                              stroke-width="1.45"
                              stroke-opacity="0.72"
                              stroke-linecap="round" />
                        <path d="M88 15 Q 110 8, 132 15 Q 110 22, 88 15 Z"
                              fill="{{ $champagneGold }}"
                              fill-opacity="0.48" />
                        <circle cx="110" cy="15" r="2.5" fill="{{ $primaryColor }}" />
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
            <a href="{{ ($isPreview ?? false) ? route('templates.index') : route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
