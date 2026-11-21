@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#8B263E';
    $goldColor = $styles['accent_color'] ?? '#D4AF37';
    $bgColor = $styles['background_color'] ?? '#F8F6F0';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Tajawal');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
@endphp

<div class="min-h-screen py-14 px-4 sm:px-6 relative overflow-hidden text-[#2B2B2B]"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Aristocrat Wax-Seal Parchment Card -->
        <div class="rounded-2xl p-6 sm:p-11 shadow-royal-card relative border-2 mt-6"
             style="background-color: {{ $cardColor }}; border-color: {{ $goldColor }};">

            <!-- Embossed Wax Seal Medallion -->
            <div class="flex justify-center -mt-14 mb-5">
                <div class="w-16 h-16 rounded-full border-4 shadow-lg flex items-center justify-center font-amiri font-bold text-2xl"
                     style="background-color: {{ $primaryColor }}; border-color: {{ $goldColor }}; color: #FAF7F2;">
                    ز
                </div>
            </div>

            <div class="text-center space-y-6">
                @if($invitation->welcome_message)
                    <div class="font-amiri text-base sm:text-lg font-bold leading-relaxed" style="color: {{ $primaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </div>
                @endif

                <p class="text-xs sm:text-sm font-bold tracking-wide text-stone-600">
                    {{ $isEnInv ? 'Together with our families, we joyfully invite you' : 'تتشرف العائلتان الكريمتان بدعوتكم لحفل زفاف' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-2.5">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-tight" style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    @if($hasBrideName)
                        <div class="font-amiri text-2xl font-bold" style="color: {{ $goldColor }};">{{ $isEnInv ? '&' : 'و' }}</div>
                        <h1 class="font-cairo text-3xl sm:text-5xl font-black leading-tight" style="color: {{ $primaryColor }};">
                            {{ $invitation->bride_name }}
                        </h1>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 shadow-lg overflow-hidden border-2"
                             style="border-color: {{ $goldColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-[1.85] text-stone-700 px-2 max-w-lg mx-auto">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <div class="p-5 sm:p-6 rounded-xl border space-y-2.5"
                     style="background-color: {{ $bgColor }}; border-color: {{ $goldColor }}65;">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $primaryColor }};">
                        <x-app-icon name="calendar" class="w-5 h-5 shrink-0" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>
                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-bold opacity-85" style="color: {{ $primaryColor }};">
                            @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                        </div>
                    @endif
                    @if($dateInfo['time'])
                        <div class="flex items-center justify-center gap-2 text-sm font-semibold text-stone-600">
                            <x-app-icon name="clock" class="w-4 h-4 shrink-0" />
                            @if($dateInfo['locale'] === 'en')
                                <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                            @else
                                <span dir="rtl">الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                            @endif
                        </div>
                    @endif
                    <div class="flex items-center justify-center gap-2 text-sm font-extrabold text-stone-800 pt-1">
                        <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                        <span>{{ $invitation->venue_name }}</span>
                    </div>
                    @if($invitation->venue_address)
                        <div class="text-xs text-stone-600">{{ $invitation->venue_address }}</div>
                    @endif
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[42px] px-6 py-2 rounded-full text-xs font-bold text-white shadow-sm hover:opacity-90 transition"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4" />
                                <span>{{ $isEnInv ? 'Venue Location Map' : 'موقع القاعة على الخريطة' }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#2B2B2B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#2B2B2B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#2B2B2B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#2B2B2B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#2B2B2B', 'cardColor' => $cardColor])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs opacity-60 pt-4 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ ($isPreview ?? false) ? route('templates.index') : route('templates.index', ['ref' => 'invite']) }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
