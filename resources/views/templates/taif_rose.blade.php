@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#B85D73';
    $secondaryColor = $styles['secondary_color'] ?? '#3A2D32';
    $bgColor = $styles['background_color'] ?? '#FCF6F5';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#7D937A';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Amiri');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
    $champagneColor = '#C8B08A';
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(circle at 12% 14%, {{ $primaryColor }}18 0%, transparent 45%), radial-gradient(circle at 88% 85%, {{ $accentColor }}1A 0%, transparent 45%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Asymmetric Botanical Taif Rose Editorial Stationery -->
        <div class="relative p-2 sm:p-3 border shadow-[0_24px_54px_-18px_rgba(184,93,115,0.2)]"
             style="background-color: {{ $cardColor }}; border-color: {{ $champagneColor }}88;">

            <div class="relative border p-6 sm:p-12 text-center overflow-hidden"
                 style="border-color: {{ $primaryColor }}45; background-image: radial-gradient(circle at 50% 45%, #FFFFFF 0%, {{ $bgColor }} 100%);">

                <!-- Top-Start Curated Botanical Taif Rose Illustration -->
                <svg viewBox="0 0 210 180" class="w-36 h-32 sm:w-44 sm:h-36 absolute -top-2 -start-2 pointer-events-none opacity-90" aria-hidden="true">
                    <!-- Sage Leaves & Stems -->
                    <path d="M18 148 Q54 96 122 44" fill="none" stroke="{{ $accentColor }}" stroke-width="1.6" />
                    <path d="M44 106 Q16 86 24 62 Q48 76 44 106 Z" fill="{{ $accentColor }}" fill-opacity="0.32" stroke="{{ $accentColor }}" stroke-width="1" />
                    <path d="M74 82 Q96 106 120 90 Q98 68 74 82 Z" fill="{{ $accentColor }}" fill-opacity="0.28" stroke="{{ $accentColor }}" stroke-width="1" />
                    <path d="M104 52 Q128 24 152 38 Q130 62 104 52 Z" fill="{{ $accentColor }}" fill-opacity="0.25" stroke="{{ $accentColor }}" stroke-width="1" />
                    <!-- Watercolor Blush Halo -->
                    <circle cx="64" cy="54" r="34" fill="{{ $primaryColor }}" fill-opacity="0.14" />
                    <!-- Primary Taif Damask Rose Bloom -->
                    <g transform="translate(64,54)">
                        <path d="M0 -24 C14 -24 24 -12 20 2 C24 14 10 24 -2 20 C-16 24 -24 12 -20 -2 C-24 -14 -12 -24 0 -24 Z" fill="{{ $primaryColor }}" fill-opacity="0.24" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                        <path d="M0 -17 C10 -17 17 -8 14 2 C16 11 7 17 -2 14 C-11 17 -17 8 -14 -2 C-16 -11 -8 -17 0 -17 Z" fill="{{ $primaryColor }}" fill-opacity="0.32" stroke="{{ $primaryColor }}" stroke-width="1.1" />
                        <path d="M0 -10 C6 -10 10 -4 8 2 C9 7 3 10 -2 8 C-7 9 -10 4 -8 -2 C-9 -7 -4 -10 0 -10 Z" fill="{{ $primaryColor }}" fill-opacity="0.55" />
                        <circle cx="0" cy="0" r="3.2" fill="{{ $champagneColor }}" />
                    </g>
                    <!-- Secondary Rose Bud -->
                    <g transform="translate(116,32) rotate(28)">
                        <path d="M0 -12 C8 -10 10 0 0 10 C-10 0 -8 -10 0 -12 Z" fill="{{ $primaryColor }}" fill-opacity="0.38" stroke="{{ $primaryColor }}" stroke-width="1" />
                        <path d="M-5 8 Q0 14 5 8" fill="none" stroke="{{ $accentColor }}" stroke-width="1.2" />
                    </g>
                </svg>

                <!-- Bottom-End Balancing Botanical Spray -->
                <svg viewBox="0 0 190 160" class="w-32 h-28 sm:w-40 sm:h-32 absolute -bottom-2 -end-2 pointer-events-none opacity-85" aria-hidden="true">
                    <path d="M174 20 Q138 78 68 132" fill="none" stroke="{{ $accentColor }}" stroke-width="1.5" />
                    <path d="M142 66 Q168 86 158 108 Q134 92 142 66 Z" fill="{{ $accentColor }}" fill-opacity="0.3" stroke="{{ $accentColor }}" stroke-width="1" />
                    <path d="M110 96 Q86 72 68 88 Q88 112 110 96 Z" fill="{{ $accentColor }}" fill-opacity="0.24" stroke="{{ $accentColor }}" stroke-width="1" />
                    <circle cx="128" cy="108" r="28" fill="{{ $primaryColor }}" fill-opacity="0.12" />
                    <g transform="translate(128,108) scale(0.82)">
                        <path d="M0 -22 C13 -22 22 -11 18 2 C21 13 9 22 -2 18 C-14 21 -22 11 -18 -2 C-21 -13 -11 -22 0 -22 Z" fill="{{ $primaryColor }}" fill-opacity="0.25" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                        <path d="M0 -13 C8 -13 13 -6 10 2 C12 8 5 13 -1 10 C-8 12 -13 6 -10 -2 C-12 -8 -5 -13 0 -13 Z" fill="{{ $primaryColor }}" fill-opacity="0.45" />
                        <circle cx="0" cy="0" r="3" fill="{{ $champagneColor }}" />
                    </g>
                </svg>

                <!-- Main Editorial Content -->
                <div class="relative z-10 space-y-6 pt-10 pb-8">
                    @if($invitation->welcome_message)
                        <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2 max-w-md mx-auto"
                             style="color: {{ $secondaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif

                    <div class="flex items-center justify-center gap-3">
                        <span class="h-px w-8" style="background-color: {{ $champagneColor }};"></span>
                        <span class="font-tajawal text-xs sm:text-sm font-bold" style="color: {{ $primaryColor }};">
                            {{ $isEnInv ? 'A Botanical Celebration of Love' : 'دعوة زفاف بنفحات ورد الطائف' }}
                        </span>
                        <span class="h-px w-8" style="background-color: {{ $champagneColor }};"></span>
                    </div>

                    <!-- Couple Names -->
                    <div class="py-2 space-y-3">
                        <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->groom_name }}
                        </h1>

                        @if($hasBrideName)
                            <div class="font-amiri text-2xl font-bold" style="color: {{ $primaryColor }};">
                                {{ $isEnInv ? '&' : 'و' }}
                            </div>

                            <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                                style="color: {{ $secondaryColor }};">
                                {{ $invitation->bride_name }}
                            </h1>
                        @endif
                    </div>

                    @if($invitation->cover_photo)
                        <div class="py-2">
                            <div class="w-36 h-44 sm:w-44 sm:h-52 mx-auto rounded-full p-1.5 border shadow-md overflow-hidden"
                                 style="border-color: {{ $primaryColor }}80; background-color: #FFFFFF;">
                                <img src="{{ $invitation->cover_photo }}"
                                     alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                     onerror="this.parentElement.parentElement.style.display='none'"
                                     class="w-full h-full object-cover rounded-full">
                            </div>
                        </div>
                    @endif

                    @if($invitation->invitation_message)
                        <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-md mx-auto opacity-85">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                        </p>
                    @endif

                    <!-- Soft Cotton Paper Logistics Box -->
                    <div class="max-w-md mx-auto p-5 sm:p-6 border space-y-3 font-tajawal"
                         style="background-color: #FFFFFF; border-color: {{ $accentColor }}55;">
                        <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $primaryColor }};">
                            <x-app-icon name="calendar" class="w-5 h-5 shrink-0" style="color: {{ $accentColor }};" />
                            <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                        </div>

                        @if($dateInfo['hijri_date'])
                            <div class="text-xs sm:text-sm font-bold" style="color: {{ $secondaryColor }};">
                                @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                            </div>
                        @endif

                        @if($dateInfo['time'])
                            <div class="flex items-center justify-center gap-2 text-sm font-semibold opacity-80">
                                <x-app-icon name="clock" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                                @if($dateInfo['locale'] === 'en')
                                    <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                @else
                                    <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                @endif
                            </div>
                        @endif

                        <div class="pt-3 border-t space-y-1.5" style="border-color: {{ $champagneColor }}55;">
                            <div class="flex items-center justify-center gap-2 text-base font-extrabold" style="color: {{ $secondaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                                <span class="break-words">{{ $invitation->venue_name }}</span>
                            </div>

                            @if($invitation->venue_address)
                                <p class="text-xs sm:text-sm opacity-75 leading-relaxed break-words">
                                    {{ $invitation->venue_address }}
                                </p>
                            @endif

                            @if($invitation->venue_map_url)
                                <div class="pt-2">
                                    <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-extrabold text-white shadow-sm hover:opacity-90 transition"
                                       style="background-color: {{ $secondaryColor }};">
                                        <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $bgColor }};" />
                                        <span class="whitespace-nowrap">{{ $isEnInv ? 'Open Venue Map' : 'عرض الموقع على الخريطة' }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
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
