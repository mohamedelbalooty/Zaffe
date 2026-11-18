@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#0F4C3A';
    $goldColor = $styles['secondary_color'] ?? '#C6A76B';
    $bgColor = $styles['background_color'] ?? '#F4F7F5';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Amiri');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
@endphp

<div class="min-h-screen py-10 sm:py-14 px-4 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: #1E293B; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Islamic Heritage Mihrab Card -->
        <div class="rounded-t-[3.5rem] rounded-b-2xl p-6 sm:p-10 shadow-xl relative border-2"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">

            <div class="border p-6 sm:p-8 rounded-t-[2.5rem] rounded-b-xl space-y-6 text-center"
                 style="border-color: {{ $goldColor }}70;">

                <!-- Arabesque Medallion Header -->
                <div class="space-y-2.5">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full border-2 mx-auto"
                         style="border-color: {{ $goldColor }}; color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                        <x-app-icon name="moon-star" class="w-7 h-7" />
                    </div>

                    <div class="font-amiri font-bold text-lg sm:text-xl leading-relaxed" style="color: {{ $primaryColor }};">
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                    </div>
                    <div class="font-amiri text-xs sm:text-sm opacity-90 max-w-md mx-auto leading-relaxed" style="color: {{ $primaryColor }};">
                        «وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»
                    </div>
                    <div class="w-24 h-0.5 mx-auto opacity-60" style="background: linear-gradient(90deg, transparent, {{ $goldColor }}, transparent);"></div>
                </div>

                <p class="font-tajawal text-xs sm:text-sm font-bold tracking-wide text-slate-600">
                    {{ $isEnInv ? 'May Allah bless their union in goodness and harmony' : 'بارك الله لهما وبارك عليهما وجمع بينهما في خير' }}
                </p>

                <!-- Couple Names -->
                <div class="py-2 space-y-2">
                    <h1 class="text-4xl sm:text-5xl font-bold leading-tight" style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    <div class="text-xl font-bold" style="color: {{ $goldColor }};">{{ $isEnInv ? '&' : 'وَ' }}</div>
                    <h1 class="text-4xl sm:text-5xl font-bold leading-tight" style="color: {{ $primaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 shadow-lg overflow-hidden border-2"
                             style="border-color: {{ $goldColor }};">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }} & {{ $invitation->bride_name }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-sm sm:text-base leading-[1.85] text-slate-700 px-2 max-w-lg mx-auto">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Date & Venue -->
                <div class="p-5 sm:p-6 rounded-xl border space-y-2.5 font-tajawal"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}30;">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $primaryColor }};">
                        <x-app-icon name="calendar" class="w-5 h-5 shrink-0" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>
                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-bold" style="color: {{ $primaryColor }};">
                            @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                        </div>
                    @endif
                    @if($dateInfo['time'])
                        <div class="flex items-center justify-center gap-2 text-sm font-semibold text-slate-600">
                            <x-app-icon name="clock" class="w-4 h-4 shrink-0" />
                            @if($dateInfo['locale'] === 'en')
                                <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                            @else
                                <span dir="rtl">الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                            @endif
                        </div>
                    @endif
                    <div class="flex items-center justify-center gap-2 text-sm font-extrabold pt-1 text-slate-800">
                        <x-app-icon name="map-pin" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                        <span>{{ $invitation->venue_name }}</span>
                    </div>
                    @if($invitation->venue_address)
                        <div class="text-xs text-slate-600">{{ $invitation->venue_address }}</div>
                    @endif
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 min-h-[42px] px-6 py-2 rounded-full text-xs font-bold text-white shadow-sm hover:opacity-90 transition"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4" />
                                <span>{{ $isEnInv ? 'Venue Location on Google Maps' : 'موقع القاعة على Google Maps' }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#1E293B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#1E293B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#1E293B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#1E293B', 'cardColor' => $cardColor, 'bgColor' => $bgColor])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => '#1E293B', 'cardColor' => $cardColor])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs opacity-60 pt-4 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
