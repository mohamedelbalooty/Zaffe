@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#1B4332';
    $secondaryColor = $styles['secondary_color'] ?? '#262A24';
    $bgColor = $styles['background_color'] ?? '#F4F1EA';
    $cardColor = $styles['card_color'] ?? '#FCFBF7';
    $accentColor = $styles['accent_color'] ?? '#C5A871';
    $oliveColor = '#6B7B5C';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(circle at 50% 0%, {{ $primaryColor }}12 0%, transparent 52%), radial-gradient(circle at 50% 100%, {{ $accentColor }}1A 0%, transparent 50%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Al-Ahsa & AlUla Oasis Colonnade Sanctuary Card -->
        <div class="relative p-2.5 sm:p-4 border shadow-[0_24px_56px_-18px_rgba(27,67,50,0.18)]"
             style="background-color: {{ $cardColor }}; border-color: {{ $accentColor }};">

            <div class="border p-5 sm:p-10 text-center space-y-6 relative"
                 style="border-color: {{ $primaryColor }}45;">

                <!-- Engraved Symmetrical Date Palm Fronds & Oasis Arch -->
                <div class="pt-1 pb-2 flex flex-col items-center">
                    <svg viewBox="0 0 360 92" class="w-72 sm:w-80 h-20 mx-auto" aria-hidden="true">
                        <!-- Fine Architectural Canopy Arch -->
                        <path d="M24 84 Q180 6 336 84" fill="none" stroke="{{ $accentColor }}" stroke-width="1.3" stroke-dasharray="4 3" />
                        <path d="M40 84 Q180 18 320 84" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.2" stroke-opacity="0.6" />
                        <!-- Left Palm Frond -->
                        <g fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-linecap="round">
                            <path d="M174 70 Q132 32 76 42" />
                            <path d="M150 48 Q138 28 122 26" stroke="{{ $oliveColor }}" />
                            <path d="M134 42 Q118 24 100 24" stroke="{{ $oliveColor }}" />
                            <path d="M118 39 Q100 24 84 26" stroke="{{ $oliveColor }}" />
                            <path d="M142 48 Q128 58 112 62" stroke="{{ $oliveColor }}" />
                            <path d="M124 44 Q110 54 94 58" stroke="{{ $oliveColor }}" />
                        </g>
                        <!-- Right Palm Frond -->
                        <g fill="none" stroke="{{ $primaryColor }}" stroke-width="1.5" stroke-linecap="round">
                            <path d="M186 70 Q228 32 284 42" />
                            <path d="M210 48 Q222 28 238 26" stroke="{{ $oliveColor }}" />
                            <path d="M226 42 Q242 24 260 24" stroke="{{ $oliveColor }}" />
                            <path d="M242 39 Q260 24 276 26" stroke="{{ $oliveColor }}" />
                            <path d="M218 48 Q232 58 248 62" stroke="{{ $oliveColor }}" />
                            <path d="M236 44 Q250 54 266 58" stroke="{{ $oliveColor }}" />
                        </g>
                        <!-- Central Oasis Medallion & Date Clusters -->
                        <circle cx="180" cy="40" r="10" fill="{{ $bgColor }}" stroke="{{ $accentColor }}" stroke-width="1.5" />
                        <polygon points="180,33 185,40 180,47 175,40" fill="{{ $primaryColor }}" />
                        <circle cx="171" cy="64" r="2.2" fill="{{ $accentColor }}" />
                        <circle cx="180" cy="68" r="2.2" fill="{{ $accentColor }}" />
                        <circle cx="189" cy="64" r="2.2" fill="{{ $accentColor }}" />
                    </svg>
                </div>

                @if($invitation->welcome_message)
                    <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2 max-w-lg mx-auto"
                         style="color: {{ $primaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </div>
                @endif

                <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide" style="color: {{ $oliveColor }};">
                    {{ $isEnInv ? 'Under the canopy of grace and blessing, we invite you to celebrate' : 'في أمسية تفيض بالسكينة والسرور، نتشرف بدعوتكم لحضور زفاف' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-3">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                        style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>

                    @if($hasBrideName)
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                            <span class="font-amiri text-2xl font-bold" style="color: {{ $accentColor }};">{{ $isEnInv ? '&' : 'و' }}</span>
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                        </div>

                        <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                            style="color: {{ $primaryColor }};">
                            {{ $invitation->bride_name }}
                        </h1>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-40 h-48 mx-auto rounded-t-[4.5rem] rounded-b-lg p-1.5 border-2 shadow-md overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: {{ $bgColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-t-[4rem] rounded-b-sm">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-lg mx-auto opacity-85">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Symmetrical Two-Column Oasis Hospitality Ledger -->
                <div class="grid grid-cols-1 sm:grid-cols-2 border text-start font-tajawal"
                     style="background-color: {{ $bgColor }}; border-color: {{ $accentColor }}90;">
                    <div class="p-5 border-b sm:border-b-0 sm:border-e space-y-2" style="border-color: {{ $accentColor }}60;">
                        <div class="flex items-center gap-2 text-xs font-extrabold" style="color: {{ $oliveColor }};">
                            <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                            <span>{{ $isEnInv ? 'Date & Time' : 'الموعد والتوقيت' }}</span>
                        </div>
                        <div class="text-sm sm:text-base font-extrabold" style="color: {{ $primaryColor }};">
                            <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                        </div>
                        @if($dateInfo['hijri_date'])
                            <div class="text-xs font-bold" style="color: {{ $secondaryColor }};">
                                @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                            </div>
                        @endif
                        @if($dateInfo['time'])
                            <div class="text-xs font-semibold opacity-80 flex items-center gap-1.5 pt-0.5">
                                <x-app-icon name="clock" class="w-3.5 h-3.5 shrink-0" style="color: {{ $primaryColor }};" />
                                @if($dateInfo['locale'] === 'en')
                                    <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                @else
                                    <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="p-5 space-y-2 flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-xs font-extrabold" style="color: {{ $oliveColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                                <span>{{ $isEnInv ? 'Celebration Venue' : 'مكان الحفل' }}</span>
                            </div>
                            <div class="text-sm sm:text-base font-extrabold break-words" style="color: {{ $secondaryColor }};">
                                {{ $invitation->venue_name }}
                            </div>
                            @if($invitation->venue_address)
                                <p class="text-xs opacity-80 leading-relaxed break-words">
                                    {{ $invitation->venue_address }}
                                </p>
                            @endif
                        </div>

                        @if($invitation->venue_map_url)
                            <div class="pt-2">
                                <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center gap-2 min-h-[42px] px-5 py-2 text-xs font-extrabold text-white shadow-xs hover:opacity-90 transition w-full sm:w-auto"
                                   style="background-color: {{ $primaryColor }};">
                                    <x-app-icon name="map-pin" class="w-3.5 h-3.5" style="color: {{ $accentColor }};" />
                                    <span>{{ $isEnInv ? 'Open Venue Map' : 'عرض الموقع على الخريطة' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
