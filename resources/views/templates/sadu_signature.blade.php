@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#7A1F2B';
    $secondaryColor = $styles['secondary_color'] ?? '#231F20';
    $bgColor = $styles['background_color'] ?? '#F5EFE6';
    $cardColor = $styles['card_color'] ?? '#FFFDF9';
    $accentColor = $styles['accent_color'] ?? '#C85A32';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
    $saduBandId = 'sadu-band-' . ($invitation->id ?? 'preview');
@endphp

<div class="min-h-screen py-8 sm:py-14 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Al Sadu Woven Textile Stationery Card -->
        <div class="shadow-[0_22px_52px_-16px_rgba(35,31,32,0.18)] border overflow-hidden"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">

            <!-- Top Authentic Al Sadu Woven Geometric Band (Selective Heritage Accent) -->
            <div class="w-full border-b-2" style="border-color: {{ $secondaryColor }};">
                <svg viewBox="0 0 600 44" class="w-full h-10 sm:h-11 block" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <pattern id="{{ $saduBandId }}" width="40" height="44" patternUnits="userSpaceOnUse">
                            <rect width="40" height="44" fill="{{ $primaryColor }}" />
                            <rect y="0" width="40" height="5" fill="{{ $secondaryColor }}" />
                            <rect y="39" width="40" height="5" fill="{{ $secondaryColor }}" />
                            <polygon points="20,7 36,22 20,37 4,22" fill="#FFFDF9" />
                            <polygon points="20,12 30,22 20,32 10,22" fill="{{ $accentColor }}" />
                            <polygon points="20,17 25,22 20,27 15,22" fill="{{ $secondaryColor }}" />
                            <polygon points="0,15 6,22 0,29" fill="{{ $accentColor }}" />
                            <polygon points="40,15 34,22 40,29" fill="{{ $accentColor }}" />
                        </pattern>
                    </defs>
                    <rect width="600" height="44" fill="url(#{{ $saduBandId }})" />
                </svg>
            </div>

            <!-- Quiet Unbleached Wool Ivory Field so Arabic Text Dominates -->
            <div class="p-6 sm:p-12 text-center space-y-6 relative">
                <!-- Selective Side Woven Stitch Accents -->
                <div class="hidden sm:block absolute top-8 bottom-8 start-4 w-1 border-x" style="border-color: {{ $primaryColor }}45;"></div>
                <div class="hidden sm:block absolute top-8 bottom-8 end-4 w-1 border-x" style="border-color: {{ $primaryColor }}45;"></div>

                @if($invitation->welcome_message)
                    <div class="font-amiri text-base sm:text-lg leading-[1.9] font-bold px-2 max-w-lg mx-auto"
                         style="color: {{ $primaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </div>
                @endif

                <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide" style="color: {{ $secondaryColor }}; opacity: 0.78;">
                    {{ $isEnInv ? 'You are warmly invited to join the wedding celebration of' : 'يسرّنا دعوتكم لحضور حفل زفاف' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-3">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                        style="color: {{ $secondaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>

                    @if($hasBrideName)
                        <!-- Sadu Diamond Lozenge Connector -->
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-0.5 w-10" style="background-color: {{ $accentColor }};"></span>
                            <span class="inline-flex items-center justify-center w-8 h-8 rotate-45 border-2 text-sm font-amiri font-bold"
                                  style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }}; color: {{ $primaryColor }};">
                                <span class="-rotate-45">{{ $isEnInv ? '&' : 'و' }}</span>
                            </span>
                            <span class="h-0.5 w-10" style="background-color: {{ $accentColor }};"></span>
                        </div>

                        <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-[1.2] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->bride_name }}
                        </h1>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto p-1.5 border-2 shadow-md overflow-hidden"
                             style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-sm sm:text-base leading-[1.9] px-2 max-w-lg mx-auto" style="color: {{ $secondaryColor }}; opacity: 0.9;">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Woven Sadu Event Plaque -->
                <div class="p-5 sm:p-7 border-y-2 space-y-3 font-tajawal"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }};">
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

                    <div class="pt-3 border-t space-y-2" style="border-color: {{ $primaryColor }}25;">
                        <div class="flex items-center justify-center gap-2 text-base font-extrabold" style="color: {{ $secondaryColor }};">
                            <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
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
                                   class="inline-flex items-center gap-2 min-h-[44px] px-7 py-2.5 text-xs font-extrabold text-white shadow-sm hover:opacity-90 transition"
                                   style="background-color: {{ $primaryColor }};">
                                    <x-app-icon name="map-pin" class="w-4 h-4" />
                                    <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Al Sadu Matching Band -->
            <div class="w-full border-t-2" style="border-color: {{ $secondaryColor }};">
                <svg viewBox="0 0 600 28" class="w-full h-6 sm:h-7 block" preserveAspectRatio="none" aria-hidden="true">
                    <rect width="600" height="28" fill="url(#{{ $saduBandId }})" />
                </svg>
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
