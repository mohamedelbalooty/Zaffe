@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#C6A76B';
    $secondaryColor = $styles['secondary_color'] ?? '#292521';
    $bgColor = $styles['background_color'] ?? '#FAF7F2';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Tajawal');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $groomInitial = mb_substr(trim((string) ($invitation->groom_name ?? 'أ')), 0, 1);
    $brideInitial = mb_substr(trim((string) ($invitation->bride_name ?? 'س')), 0, 1);
@endphp

<div class="min-h-screen py-10 sm:py-14 px-4 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Main Royal Diwan Invitation Card -->
        <div class="rounded-2xl p-5 sm:p-8 shadow-royal-card relative border-2"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">

            <!-- Inner Architectural Gold Hairline Frame -->
            <div class="border p-6 sm:p-10 rounded-xl text-center space-y-6 relative"
                 style="border-color: {{ $primaryColor }}55;">

                <!-- Top Crown Crest -->
                <div class="flex items-center justify-center gap-3" style="color: {{ $primaryColor }};">
                    <span class="h-px w-12 opacity-40" style="background-color: {{ $primaryColor }};"></span>
                    <x-app-icon name="crown" class="w-5 h-5" />
                    <span class="h-px w-12 opacity-40" style="background-color: {{ $primaryColor }};"></span>
                </div>

                @if($invitation->welcome_message)
                    <div class="font-amiri text-sm sm:text-base leading-relaxed font-bold px-2"
                         style="color: {{ $secondaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                    </div>
                @endif

                <p class="text-xs sm:text-sm font-bold tracking-wide opacity-75">
                    {{ $isEnInv ? 'We cordially invite you to celebrate the wedding of' : 'نتشرف بدعوتكم لحضور حفل زفاف' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-2.5">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black tracking-tight leading-tight"
                        style="color: {{ $secondaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>

                    <div class="flex items-center justify-center gap-3">
                        <span class="h-px w-12 opacity-45" style="background-color: {{ $primaryColor }};"></span>
                        <span class="font-amiri text-2xl font-bold" style="color: {{ $primaryColor }};">{{ $isEnInv ? '&' : 'و' }}</span>
                        <span class="h-px w-12 opacity-45" style="background-color: {{ $primaryColor }};"></span>
                    </div>

                    <h1 class="font-cairo text-3xl sm:text-5xl font-black tracking-tight leading-tight"
                        style="color: {{ $secondaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                <!-- Cover Photo OR Photo-Free Royal Monogram Seal -->
                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 shadow-lg overflow-hidden border-2"
                             style="border-color: {{ $primaryColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }} & {{ $invitation->bride_name }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @else
                    <div class="py-1">
                        <div class="w-20 h-20 mx-auto rounded-full p-1 border"
                             style="border-color: {{ $primaryColor }}80;">
                            <div class="w-full h-full rounded-full flex items-center justify-center font-amiri text-lg font-bold"
                                 style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }};">
                                {{ $groomInitial }} &amp; {{ $brideInitial }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-[1.85] px-2 opacity-85 max-w-lg mx-auto">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Date, Umm al-Qura Hijri & Time Plaque -->
                <div class="p-5 rounded-xl border space-y-2.5"
                     style="border-color: {{ $primaryColor }}45; background-color: {{ $bgColor }};">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold"
                         style="color: {{ $secondaryColor }};">
                        <x-app-icon name="calendar" class="w-5 h-5 shrink-0" style="color: {{ $primaryColor }};" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>

                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-bold opacity-85" style="color: {{ $secondaryColor }};">
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
                </div>

                <!-- Venue & Map -->
                <div class="space-y-2.5 pt-1">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold">
                        <x-app-icon name="map-pin" class="w-5 h-5 shrink-0" style="color: {{ $primaryColor }};" />
                        <span>{{ $invitation->venue_name }}</span>
                    </div>

                    @if($invitation->venue_address)
                        <p class="text-xs sm:text-sm opacity-75 max-w-md mx-auto leading-relaxed">
                            {{ $invitation->venue_address }}
                        </p>
                    @endif

                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[42px] px-6 py-2.5 rounded-full text-xs font-bold shadow-sm hover:opacity-90 transition"
                               style="background-color: {{ $secondaryColor }}; color: #FAF7F2;">
                                <x-app-icon name="map-pin" class="w-4 h-4" style="color: {{ $primaryColor }};" />
                                <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Bottom Ornamental Corners -->
                <div class="pt-2 flex items-center justify-center gap-3 opacity-60" style="color: {{ $primaryColor }};">
                    <span class="h-px w-10" style="background-color: {{ $primaryColor }};"></span>
                    <x-app-icon name="gem" class="w-3.5 h-3.5" />
                    <span class="h-px w-10" style="background-color: {{ $primaryColor }};"></span>
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
