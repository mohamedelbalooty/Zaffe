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
    $groomInitial = mb_substr(trim((string) ($invitation->groom_name ?? 'أ')), 0, 1);
    $brideInitial = $hasBrideName ? mb_substr(trim((string) $invitation->bride_name), 0, 1) : '';
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(circle at 50% 0%, {{ $accentColor }}1F 0%, transparent 55%), linear-gradient(180deg, {{ $bgColor }} 0%, #EADFCB 100%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Diriyah Najdi Courtyard Architectural Card -->
        <div class="relative shadow-[0_24px_56px_-18px_rgba(38,33,29,0.22)]"
             style="background-color: {{ $cardColor }}; border: 2px solid {{ $primaryColor }};">

            <!-- Top Najdi Stepped Crenellation (Shurfat) Cornice -->
            <div class="w-full overflow-hidden border-b" style="background-color: {{ $secondaryColor }}; border-color: {{ $accentColor }};">
                <svg viewBox="0 0 600 44" class="w-full h-9 sm:h-11 block" preserveAspectRatio="none" aria-hidden="true">
                    <rect width="600" height="44" fill="{{ $secondaryColor }}" />
                    <!-- Stepped Najdi Merlons -->
                    <g fill="{{ $primaryColor }}">
                        <path d="M0 44 V26 H16 V14 H28 V6 H40 V14 H52 V26 H68 V44 Z" />
                        <path d="M68 44 V26 H84 V14 H96 V6 H108 V14 H120 V26 H136 V44 Z" />
                        <path d="M136 44 V26 H152 V14 H164 V6 H176 V14 H188 V26 H204 V44 Z" />
                        <path d="M204 44 V26 H220 V14 H232 V6 H244 V14 H256 V26 H272 V44 Z" />
                        <path d="M272 44 V26 H288 V14 H300 V6 H312 V14 H324 V26 H340 V44 Z" />
                        <path d="M340 44 V26 H356 V14 H368 V6 H380 V14 H392 V26 H408 V44 Z" />
                        <path d="M408 44 V26 H424 V14 H436 V6 H448 V14 H460 V26 H476 V44 Z" />
                        <path d="M476 44 V26 H492 V14 H504 V6 H516 V14 H528 V26 H544 V44 Z" />
                        <path d="M544 44 V26 H560 V14 H572 V6 H584 V14 H596 V26 H600 V44 Z" />
                    </g>
                    <!-- Recessed Triangular Furjat Openings -->
                    <g fill="{{ $accentColor }}">
                        <polygon points="34,17 40,27 28,27" />
                        <polygon points="102,17 108,27 96,27" />
                        <polygon points="170,17 176,27 164,27" />
                        <polygon points="238,17 244,27 232,27" />
                        <polygon points="306,17 312,27 300,27" />
                        <polygon points="374,17 380,27 368,27" />
                        <polygon points="442,17 448,27 436,27" />
                        <polygon points="510,17 516,27 504,27" />
                        <polygon points="578,17 584,27 572,27" />
                    </g>
                    <line x1="0" y1="41" x2="600" y2="41" stroke="{{ $accentColor }}" stroke-width="2" />
                </svg>
            </div>

            <!-- Inner Recessed Mud-Brick Courtyard Frame -->
            <div class="p-4 sm:p-7">
                <div class="relative border p-5 sm:p-10 text-center space-y-6"
                     style="border-color: {{ $accentColor }}90; background-image: radial-gradient(circle at 50% 25%, rgba(255,255,255,0.55) 0%, transparent 72%);">

                    <!-- Four Najdi Wooden Door Studs at Corners -->
                    <span class="absolute top-2.5 start-2.5 w-3 h-3 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};"></span>
                    <span class="absolute top-2.5 end-2.5 w-3 h-3 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};"></span>
                    <span class="absolute bottom-2.5 start-2.5 w-3 h-3 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};"></span>
                    <span class="absolute bottom-2.5 end-2.5 w-3 h-3 rotate-45 border" style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};"></span>

                    <!-- Najdi Triangular Medallion Emblem -->
                    <div class="flex flex-col items-center gap-3 pt-1">
                        <svg viewBox="0 0 180 32" class="w-40 h-7" aria-hidden="true">
                            <line x1="6" y1="16" x2="62" y2="16" stroke="{{ $accentColor }}" stroke-width="1.2" />
                            <line x1="118" y1="16" x2="174" y2="16" stroke="{{ $accentColor }}" stroke-width="1.2" />
                            <polygon points="90,3 103,16 90,29 77,16" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.8" />
                            <polygon points="90,8 98,16 90,24 82,16" fill="{{ $accentColor }}" />
                            <polygon points="69,16 74,11 79,16 74,21" fill="{{ $primaryColor }}" />
                            <polygon points="101,16 106,11 111,16 106,21" fill="{{ $primaryColor }}" />
                        </svg>

                        @if($invitation->welcome_message)
                            <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2 max-w-lg mx-auto"
                                 style="color: {{ $secondaryColor }};">
                                {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                            </div>
                        @endif
                    </div>

                    <div class="font-tajawal text-xs sm:text-sm font-bold tracking-wide" style="color: {{ $primaryColor }};">
                        {{ $isEnInv ? 'With the blessings of our families, we honorably invite you to the wedding of' : 'بحضوركم تكتمل مسرّاتنا في ليلة زفاف' }}
                    </div>

                    <!-- Couple Names (Architectural Monumental Hierarchy) -->
                    <div class="py-2 px-2 space-y-3">
                        <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->groom_name }}
                        </h1>

                        @if($hasBrideName)
                            <div class="flex items-center justify-center gap-3">
                                <span class="h-px w-14" style="background-color: {{ $accentColor }};"></span>
                                <span class="w-8 h-8 rotate-45 flex items-center justify-center border text-sm font-amiri font-bold"
                                      style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }}; color: {{ $primaryColor }};">
                                    <span class="-rotate-45">{{ $isEnInv ? '&' : 'و' }}</span>
                                </span>
                                <span class="h-px w-14" style="background-color: {{ $accentColor }};"></span>
                            </div>

                            <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                                style="color: {{ $secondaryColor }};">
                                {{ $invitation->bride_name }}
                            </h1>
                        @endif
                    </div>

                    <!-- Cover Photo OR Najdi Diamond Monogram Seal (Photo-Free Ready) -->
                    @if($invitation->cover_photo)
                        <div class="py-2">
                            <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto p-1.5 border-2 shadow-md overflow-hidden"
                                 style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                                <img src="{{ $invitation->cover_photo }}"
                                     alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                     onerror="this.parentElement.parentElement.style.display='none'"
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    @else
                        <div class="py-2 flex justify-center">
                            <div class="w-16 h-16 rotate-45 border-2 flex items-center justify-center shadow-xs"
                                 style="border-color: {{ $accentColor }}; background-color: {{ $bgColor }};">
                                <span class="-rotate-45 inline-flex items-center justify-center gap-1.5 font-amiri text-base font-bold leading-none" style="color: {{ $secondaryColor }};">
                                    <span class="inline-block">{{ $groomInitial }}</span>@if($brideInitial !== '')<span class="w-1.5 h-1.5 rotate-45 inline-block shrink-0" style="background-color: {{ $primaryColor }};"></span><span class="inline-block">{{ $brideInitial }}</span>@endif
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($invitation->invitation_message)
                        <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-lg mx-auto opacity-90">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                        </p>
                    @endif

                    <!-- 3-Bay Architectural Lintel for Gregorian, Umm al-Qura Hijri & Time -->
                    <div class="border divide-y sm:divide-y-0 sm:divide-x sm:rtl:divide-x-reverse grid grid-cols-1 sm:grid-cols-3 text-center font-tajawal"
                         style="border-color: {{ $primaryColor }}55; background-color: {{ $bgColor }};">
                        <div class="p-3.5 sm:p-4 space-y-1 flex flex-col items-center justify-center">
                            <x-app-icon name="calendar" class="w-4 h-4 mb-0.5" style="color: {{ $primaryColor }};" />
                            <span class="text-[11px] font-bold opacity-75">{{ $isEnInv ? 'Date' : 'التاريخ الميلادي' }}</span>
                            <span class="text-xs sm:text-sm font-extrabold" style="color: {{ $secondaryColor }};">
                                <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                            </span>
                        </div>

                        <div class="p-3.5 sm:p-4 space-y-1 flex flex-col items-center justify-center"
                             style="border-color: {{ $primaryColor }}35;">
                            <x-app-icon name="moon-star" class="w-4 h-4 mb-0.5" style="color: {{ $primaryColor }};" />
                            <span class="text-[11px] font-bold opacity-75">{{ $isEnInv ? 'Hijri Date' : 'التاريخ الهجري' }}</span>
                            @if($dateInfo['hijri_date'])
                                <span class="text-xs sm:text-sm font-extrabold" style="color: {{ $secondaryColor }};">
                                    @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                                </span>
                            @else
                                <span class="text-xs font-bold opacity-60">—</span>
                            @endif
                        </div>

                        <div class="p-3.5 sm:p-4 space-y-1 flex flex-col items-center justify-center"
                             style="border-color: {{ $primaryColor }}35;">
                            <x-app-icon name="clock" class="w-4 h-4 mb-0.5" style="color: {{ $primaryColor }};" />
                            <span class="text-[11px] font-bold opacity-75">{{ $isEnInv ? 'Time' : 'وقت الحفل' }}</span>
                            @if($dateInfo['time'])
                                <span class="text-xs sm:text-sm font-extrabold" style="color: {{ $secondaryColor }};">
                                    @if($dateInfo['locale'] === 'en')
                                        <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                    @else
                                        <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                    @endif
                                </span>
                            @else
                                <span class="text-xs font-bold opacity-60">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- Recessed Courtyard Venue Plaque -->
                    <div class="pt-2 space-y-2.5 font-tajawal">
                        <div class="inline-flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $secondaryColor }};">
                            <x-app-icon name="map-pin" class="w-5 h-5 shrink-0" style="color: {{ $primaryColor }};" />
                            <span class="break-words">{{ $invitation->venue_name }}</span>
                        </div>

                        @if($invitation->venue_address)
                            <p class="text-xs sm:text-sm opacity-80 max-w-md mx-auto leading-relaxed break-words">
                                {{ $invitation->venue_address }}
                            </p>
                        @endif

                        @if($invitation->venue_map_url)
                            <div class="pt-2">
                                <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 min-h-[44px] px-7 py-2.5 text-xs font-extrabold tracking-wide shadow-sm hover:opacity-90 transition border"
                                   style="background-color: {{ $secondaryColor }}; border-color: {{ $accentColor }}; color: #FBF7F0;">
                                    <x-app-icon name="map-pin" class="w-4 h-4" style="color: {{ $accentColor }};" />
                                    <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Najdi Geometric Band -->
            <div class="h-3 w-full" style="background: repeating-linear-gradient(135deg, {{ $primaryColor }} 0 10px, {{ $secondaryColor }} 10px 20px, {{ $accentColor }} 20px 24px);"></div>
        </div>

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
