@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#E6C687';
    $secondaryColor = $styles['secondary_color'] ?? '#F1F5F9';
    $bgColor = $styles['background_color'] ?? '#0B1220';
    $cardColor = $styles['card_color'] ?? '#151F32';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
@endphp

<div class="min-h-screen py-10 sm:py-14 px-4 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(rgba(230, 198, 135, 0.14) 1px, transparent 1px); background-size: 28px 28px; color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Celestial Midnight Gala Card -->
        <div class="rounded-2xl p-6 sm:p-11 shadow-2xl relative border text-center space-y-6"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}55;">

            <div class="text-xs sm:text-sm font-bold tracking-widest flex items-center justify-center gap-2.5"
                 style="color: {{ $primaryColor }};">
                <x-app-icon name="stars" class="w-4 h-4" />
                <span>{{ $isEnInv ? 'A Starlit Celebration' : 'ليلة من ألف ليلة وليلة' }}</span>
                <x-app-icon name="stars" class="w-4 h-4" />
            </div>

            @if($invitation->welcome_message)
                <p class="font-amiri text-base sm:text-lg leading-relaxed font-bold" style="color: {{ $primaryColor }};">
                    {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                </p>
            @endif

            <div class="py-3 space-y-2.5">
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight" style="color: {{ $primaryColor }};">
                    {{ $invitation->groom_name }}
                </h1>
                @if($hasBrideName)
                    <div class="text-lg text-slate-400 font-bold">{{ $isEnInv ? '&' : 'و' }}</div>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight" style="color: {{ $primaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                @endif
            </div>

            @if($invitation->cover_photo)
                <div class="py-2">
                    <div class="w-40 h-40 mx-auto rounded-full p-1.5 shadow-2xl overflow-hidden border-2"
                         style="border-color: {{ $primaryColor }};">
                        <img src="{{ $invitation->cover_photo }}"
                             alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                             onerror="this.parentElement.parentElement.style.display='none'"
                             class="w-full h-full object-cover rounded-full">
                    </div>
                </div>
            @endif

            @if($invitation->invitation_message)
                <p class="text-sm sm:text-base leading-[1.85] opacity-90 px-2 max-w-lg mx-auto font-tajawal" style="color: {{ $secondaryColor }};">
                    {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                </p>
            @endif

            <div class="p-5 sm:p-6 rounded-xl border border-slate-700/80 space-y-2.5 font-tajawal"
                 style="background-color: {{ $bgColor }};">
                <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $primaryColor }};">
                    <x-app-icon name="calendar" class="w-5 h-5 shrink-0" />
                    <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                </div>
                @if($dateInfo['hijri_date'])
                    <div class="text-xs sm:text-sm font-semibold opacity-90" style="color: {{ $secondaryColor }};">
                        @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                    </div>
                @endif
                @if($dateInfo['time'])
                    <div class="flex items-center justify-center gap-2 text-sm opacity-85" style="color: {{ $secondaryColor }};">
                        <x-app-icon name="clock" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                        @if($dateInfo['locale'] === 'en')
                            <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                        @else
                            <span dir="rtl">الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                        @endif
                    </div>
                @endif
                <div class="flex items-center justify-center gap-2 text-sm font-bold pt-1" style="color: {{ $secondaryColor }};">
                    <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                    <span>{{ $invitation->venue_name }}</span>
                </div>
                @if($invitation->venue_address)
                    <div class="text-xs opacity-75" style="color: {{ $secondaryColor }};">{{ $invitation->venue_address }}</div>
                @endif
                @if($invitation->venue_map_url)
                    <div class="pt-2">
                        <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 min-h-[42px] px-6 py-2 rounded-full text-xs font-extrabold text-[#0B1220] shadow-md hover:opacity-95 transition"
                           style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, #DFC99E 100%);">
                            <x-app-icon name="map-pin" class="w-4 h-4" />
                            <span>{{ $isEnInv ? 'Venue Map' : 'موقع الحفل على الخريطة' }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor, 'isDarkTheme' => true])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor, 'isDarkTheme' => true])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor, 'isDarkTheme' => true])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor, 'isDarkTheme' => true])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $secondaryColor, 'cardColor' => $cardColor, 'isDarkTheme' => true])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs text-slate-400 pt-4 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ ($isPreview ?? false) ? route('templates.index') : route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:text-white">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
