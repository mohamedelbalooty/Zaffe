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
    $latticeId = 'rawashin-lattice-' . ($invitation->id ?? 'preview');
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(circle at 85% 12%, {{ $accentColor }}1A 0%, transparent 45%), radial-gradient(circle at 15% 88%, {{ $primaryColor }}14 0%, transparent 45%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Hijazi Rawashin Crafted Window Bay Card -->
        <div class="shadow-[0_24px_52px_-16px_rgba(20,83,94,0.18)] border-2 relative overflow-hidden"
             style="background-color: {{ $cardColor }}; border-color: {{ $secondaryColor }};">

            <!-- Carved Teakwood & Coral Rawashin Arch Header -->
            <div class="relative pt-5 pb-4 px-4 border-b-2"
                 style="background: linear-gradient(180deg, {{ $primaryColor }} 0%, #0F3F48 100%); border-color: {{ $accentColor }};">
                <svg viewBox="0 0 560 96" class="w-full h-20 sm:h-24 mx-auto" aria-hidden="true">
                    <defs>
                        <pattern id="{{ $latticeId }}" width="18" height="18" patternUnits="userSpaceOnUse">
                            <path d="M9 0 L18 9 L9 18 L0 9 Z" fill="none" stroke="#FAF4EA" stroke-opacity="0.28" stroke-width="1" />
                            <circle cx="9" cy="9" r="1.5" fill="{{ $accentColor }}" fill-opacity="0.7" />
                        </pattern>
                    </defs>
                    <!-- Left Roshan Shutter Lattice -->
                    <rect x="8" y="8" width="122" height="80" fill="url(#{{ $latticeId }})" stroke="{{ $accentColor }}" stroke-width="1.5" />
                    <line x1="69" y1="8" x2="69" y2="88" stroke="{{ $accentColor }}" stroke-width="1.2" stroke-opacity="0.75" />
                    <!-- Right Roshan Shutter Lattice -->
                    <rect x="430" y="8" width="122" height="80" fill="url(#{{ $latticeId }})" stroke="{{ $accentColor }}" stroke-width="1.5" />
                    <line x1="491" y1="8" x2="491" y2="88" stroke="{{ $accentColor }}" stroke-width="1.2" stroke-opacity="0.75" />
                    <!-- Central Hijazi Pointed Arch Window -->
                    <path d="M148 88 V48 Q148 16 280 6 Q412 16 412 48 V88 Z"
                          fill="#123F47" stroke="{{ $accentColor }}" stroke-width="2" />
                    <path d="M164 88 V50 Q164 24 280 15 Q396 24 396 50 V88 Z"
                          fill="url(#{{ $latticeId }})" stroke="#FAF4EA" stroke-opacity="0.45" stroke-width="1" />
                    <!-- Carved Wooden Star Rosette Inside Arch -->
                    <g transform="translate(280,52)">
                        <polygon points="0,-18 5,-5 18,0 5,5 0,18 -5,5 -18,0 -5,-5" fill="{{ $accentColor }}" />
                        <polygon points="0,-13 13,0 0,13 -13,0" fill="none" stroke="#FAF4EA" stroke-width="1.3" />
                        <circle cx="0" cy="0" r="4" fill="#FAF4EA" />
                    </g>
                </svg>
            </div>

            <!-- Layered Coral-Stone & Teak Inner Frame -->
            <div class="p-3.5 sm:p-6">
                <div class="border p-5 sm:p-10 text-center space-y-6 relative"
                     style="border-color: {{ $accentColor }}; box-shadow: inset 0 0 0 4px {{ $bgColor }}, inset 0 0 0 5px {{ $primaryColor }}45;">

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2"
                             style="color: {{ $primaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif

                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full border font-tajawal text-xs font-bold"
                         style="background-color: {{ $bgColor }}; border-color: {{ $accentColor }}75; color: {{ $secondaryColor }};">
                        <span>{{ $isEnInv ? 'An Invitation to Joy & Harmony' : 'ليلة فرح عامرة بالمودة والأنس' }}</span>
                    </div>

                    <!-- Couple Names Framed by Vertical Lattice Bars -->
                    <div class="py-3 px-4 border-y relative space-y-3"
                         style="border-color: {{ $primaryColor }}35; background-color: {{ $bgColor }}66;">
                        <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->groom_name }}
                        </h1>

                        @if($hasBrideName)
                            <div class="flex items-center justify-center gap-3">
                                <span class="h-px w-10" style="background-color: {{ $accentColor }};"></span>
                                <span class="font-amiri text-2xl font-bold" style="color: {{ $accentColor }};">{{ $isEnInv ? '&' : 'و' }}</span>
                                <span class="h-px w-10" style="background-color: {{ $accentColor }};"></span>
                            </div>

                            <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                                style="color: {{ $secondaryColor }};">
                                {{ $invitation->bride_name }}
                            </h1>
                        @endif
                    </div>

                    @if($invitation->cover_photo)
                        <div class="py-2">
                            <div class="w-40 h-48 mx-auto rounded-t-[4rem] rounded-b-lg p-1.5 border-2 shadow-md overflow-hidden"
                                 style="border-color: {{ $accentColor }}; background-color: {{ $bgColor }};">
                                <img src="{{ $invitation->cover_photo }}"
                                     alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                     onerror="this.parentElement.parentElement.style.display='none'"
                                     class="w-full h-full object-cover rounded-t-[3.5rem] rounded-b-sm">
                            </div>
                        </div>
                    @endif

                    @if($invitation->invitation_message)
                        <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-lg mx-auto opacity-90">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                        </p>
                    @endif

                    <!-- Hijazi Shuttered Event Schedule & Venue -->
                    <div class="p-5 sm:p-6 border space-y-3 font-tajawal relative"
                         style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}45;">
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
                            <div class="flex items-center justify-center gap-2 text-sm font-semibold opacity-85">
                                <x-app-icon name="clock" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                                @if($dateInfo['locale'] === 'en')
                                    <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                @else
                                    <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                @endif
                            </div>
                        @endif

                        <div class="pt-3 border-t space-y-1.5" style="border-color: {{ $accentColor }}45;">
                            <div class="flex items-center justify-center gap-2 text-base font-extrabold" style="color: {{ $secondaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $accentColor }};" />
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
                                       class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2.5 rounded-full text-xs font-extrabold text-white shadow-sm hover:opacity-90 transition"
                                       style="background-color: {{ $primaryColor }};">
                                        <x-app-icon name="map-pin" class="w-4 h-4" style="color: #FAF4EA;" />
                                        <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carved Rawashin Valance Footer Strip -->
            <div class="px-4 py-2.5 border-t flex items-center justify-center gap-3"
                 style="background-color: {{ $secondaryColor }}; border-color: {{ $accentColor }}; color: #FAF4EA;">
                <span class="h-px flex-1 opacity-30" style="background-color: {{ $accentColor }};"></span>
                <span class="font-amiri text-xs tracking-wide" style="color: {{ $accentColor }};">
                    {{ $isEnInv ? 'Hijazi Craftsmanship' : 'أصالة الرواشين الحجازية' }}
                </span>
                <span class="h-px flex-1 opacity-30" style="background-color: {{ $accentColor }};"></span>
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
