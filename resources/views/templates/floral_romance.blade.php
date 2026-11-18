@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#D9828A';
    $secondaryColor = $styles['secondary_color'] ?? '#4A3E3D';
    $accentColor = $styles['accent_color'] ?? '#738C70';
    $bgColor = $styles['background_color'] ?? '#FDF8F7';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
@endphp

<div class="min-h-screen py-10 sm:py-14 px-4 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Botanical Garden Arched Card -->
        <div class="rounded-t-[4rem] rounded-b-2xl p-7 sm:p-12 shadow-wedding relative border"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}45;">

            <!-- Botanical Wreath Crest -->
            <div class="text-center pb-5">
                <div class="inline-flex items-center justify-center gap-3 px-4 py-1.5 rounded-full border"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}35; color: {{ $primaryColor }};">
                    <x-app-icon name="flower-2" class="w-4 h-4" />
                    <x-app-icon name="leaf" class="w-4 h-4" style="color: {{ $accentColor }};" />
                    <x-app-icon name="flower-2" class="w-4 h-4" />
                </div>
            </div>

            <div class="text-center space-y-6">
                @if($invitation->welcome_message)
                    <p class="font-amiri text-base sm:text-lg leading-relaxed font-bold" style="color: {{ $secondaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </p>
                @endif

                <p class="text-xs sm:text-sm font-bold tracking-wide opacity-75">
                    {{ $isEnInv ? 'Together with their families, invite you to celebrate' : 'دعوة زفاف مفعمة بالمحبة والبهجة' }}
                </p>

                <!-- Couple Names -->
                <div class="py-3 space-y-2">
                    <h1 class="text-3xl sm:text-5xl font-black leading-tight" style="color: {{ $secondaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    <div class="flex items-center justify-center gap-3">
                        <span class="h-px w-10 opacity-40" style="background-color: {{ $primaryColor }};"></span>
                        <span class="text-xl font-amiri font-bold" style="color: {{ $primaryColor }};">{{ $isEnInv ? '&' : 'و' }}</span>
                        <span class="h-px w-10 opacity-40" style="background-color: {{ $primaryColor }};"></span>
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-black leading-tight" style="color: {{ $secondaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-40 h-40 mx-auto rounded-t-full rounded-b-2xl p-1.5 shadow-md overflow-hidden border"
                             style="border-color: {{ $primaryColor }}60;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }} & {{ $invitation->bride_name }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-t-full rounded-b-xl">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-[1.85] opacity-85 px-2 max-w-lg mx-auto font-tajawal">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Date & Venue -->
                <div class="p-6 rounded-2xl border space-y-3"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}35;">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $secondaryColor }};">
                        <x-app-icon name="calendar" class="w-5 h-5 shrink-0" style="color: {{ $primaryColor }};" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>
                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-bold opacity-85">
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
                    <div class="flex items-center justify-center gap-2 text-sm font-extrabold pt-2">
                        <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                        <span>{{ $invitation->venue_name }}</span>
                    </div>
                    @if($invitation->venue_address)
                        <div class="text-xs opacity-75 max-w-sm mx-auto">{{ $invitation->venue_address }}</div>
                    @endif
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[42px] px-6 py-2 rounded-full text-xs font-bold text-white shadow-sm hover:opacity-90 transition"
                               style="background-color: {{ $secondaryColor }};">
                                <x-app-icon name="map-pin" class="w-3.5 h-3.5" style="color: {{ $primaryColor }};" />
                                <span>{{ $isEnInv ? 'Venue Location Map' : 'موقع الحفل على الخريطة' }}</span>
                            </a>
                        </div>
                    @endif
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

        <div class="text-center text-xs opacity-60 pt-4 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
