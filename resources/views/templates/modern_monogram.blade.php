@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#141413';
    $secondaryColor = $styles['secondary_color'] ?? '#2B2927';
    $bgColor = $styles['background_color'] ?? '#F5F2EB';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#C5A869';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Cairo');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
    $groomInitial = mb_substr(trim((string) ($invitation->groom_name ?? 'ف')), 0, 1);
    $brideInitial = $hasBrideName ? mb_substr(trim((string) $invitation->bride_name), 0, 1) : '';
@endphp

<div class="min-h-screen py-8 sm:py-16 px-3.5 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: {{ $primaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Haute-Couture Editorial Monogram Stationery -->
        <div class="border shadow-[0_24px_60px_-20px_rgba(20,20,19,0.14)] overflow-hidden"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">

            <!-- Asymmetric Couture Header Rule -->
            <div class="px-6 py-4 border-b flex items-center justify-between gap-4 text-xs font-bold font-tajawal"
                 style="border-color: {{ $primaryColor }}22; background-color: {{ $bgColor }}66;">
                <span style="color: {{ $secondaryColor }};">
                    {{ $isEnInv ? 'Wedding Invitation' : 'دعوة زفاف خاصة' }}
                </span>
                <span class="font-playfair tracking-[0.2em] uppercase text-[10px]" style="color: #7A5A20;" dir="ltr">
                    ATELIER EDITION
                </span>
            </div>

            <div class="p-6 sm:p-12 space-y-8">
                <!-- Oversized Monogram Crest + Welcome Text -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 pb-6 border-b"
                     style="border-color: {{ $primaryColor }}22;">
                    <!-- Interlocking Octagonal Monogram -->
                    <div class="relative w-24 h-24 shrink-0 flex items-center justify-center border-2"
                         style="border-color: {{ $primaryColor }}; background-color: {{ $bgColor }};">
                        <div class="w-20 h-20 border flex items-center justify-center"
                             style="border-color: {{ $accentColor }};">
                            <span class="inline-flex items-center justify-center gap-2 font-amiri text-2xl font-bold leading-none" style="color: {{ $primaryColor }};">
                                <span class="inline-block">{{ $groomInitial }}</span>@if($brideInitial !== '')<span class="w-px h-6 inline-block shrink-0" style="background-color: #8A6829;"></span><span class="inline-block">{{ $brideInitial }}</span>@endif
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-start space-y-2">
                        @if($invitation->welcome_message)
                            <div class="font-amiri text-base sm:text-lg leading-[1.85] font-bold"
                                 style="color: {{ $secondaryColor }};">
                                {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                            </div>
                        @else
                            <p class="font-amiri text-base font-bold" style="color: {{ $secondaryColor }};">
                                {{ $isEnInv ? 'Together with their families' : 'تتشرف العائلتان بدعوتكم لحضور حفل الزفاف' }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Asymmetric Editorial Couple Typography -->
                <div class="py-2 space-y-4">
                    <div class="text-start">
                        <h1 class="font-cairo text-4xl sm:text-5xl font-black tracking-tight leading-[1.18] break-words"
                            style="color: {{ $primaryColor }};">
                            {{ $invitation->groom_name }}
                        </h1>
                    </div>

                    @if($hasBrideName)
                        <div class="flex items-center gap-4">
                            <span class="h-px flex-1" style="background-color: {{ $accentColor }};"></span>
                            <span class="font-playfair italic text-2xl font-bold" style="color: #7A5A20;">
                                {{ $isEnInv ? '&' : 'و' }}
                            </span>
                            <span class="h-px flex-1" style="background-color: {{ $accentColor }};"></span>
                        </div>

                        <div class="text-end">
                            <h1 class="font-cairo text-4xl sm:text-5xl font-black tracking-tight leading-[1.18] break-words"
                                style="color: {{ $primaryColor }};">
                                {{ $invitation->bride_name }}
                            </h1>
                        </div>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-full h-64 sm:h-72 overflow-hidden border"
                             style="border-color: {{ $primaryColor }}30;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="font-tajawal text-sm sm:text-base leading-[1.9] text-start border-s-2 ps-4 max-w-xl"
                       style="border-color: {{ $accentColor }}; color: {{ $secondaryColor }};">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Architectural Editorial 2x2 Grid Ledger -->
                <div class="grid grid-cols-1 sm:grid-cols-2 border text-start font-tajawal"
                     style="border-color: {{ $primaryColor }};">
                    <div class="p-4 sm:p-5 border-b sm:border-e space-y-1" style="border-color: {{ $primaryColor }}25;">
                        <span class="text-[11px] font-bold opacity-65 block">{{ $isEnInv ? 'Date' : 'التاريخ الميلادي' }}</span>
                        <div class="text-sm sm:text-base font-extrabold" style="color: {{ $primaryColor }};">
                            <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 border-b space-y-1" style="border-color: {{ $primaryColor }}25;">
                        <span class="text-[11px] font-bold opacity-65 block">{{ $isEnInv ? 'Hijri Calendar' : 'التاريخ الهجري' }}</span>
                        @if($dateInfo['hijri_date'])
                            <div class="text-sm font-bold" style="color: {{ $secondaryColor }};">
                                @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                            </div>
                        @else
                            <div class="text-sm font-bold opacity-50">—</div>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5 border-b sm:border-b-0 sm:border-e space-y-1" style="border-color: {{ $primaryColor }}25;">
                        <span class="text-[11px] font-bold opacity-65 block">{{ $isEnInv ? 'Ceremony Time' : 'موعد الحضور' }}</span>
                        @if($dateInfo['time'])
                            <div class="text-sm sm:text-base font-extrabold" style="color: {{ $primaryColor }};">
                                @if($dateInfo['locale'] === 'en')
                                    <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                                @else
                                    <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                                @endif
                            </div>
                        @else
                            <div class="text-sm font-bold opacity-50">—</div>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5 space-y-2" style="background-color: {{ $bgColor }}88;">
                        <span class="text-[11px] font-bold opacity-65 block">{{ $isEnInv ? 'Venue' : 'قاعة الاحتفال' }}</span>
                        <div class="text-sm sm:text-base font-extrabold break-words" style="color: {{ $primaryColor }};">
                            {{ $invitation->venue_name }}
                        </div>
                        @if($invitation->venue_address)
                            <p class="text-xs opacity-80 leading-relaxed break-words">{{ $invitation->venue_address }}</p>
                        @endif
                    </div>
                </div>

                @if($invitation->venue_map_url)
                    <div class="pt-1 text-center">
                        <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 min-h-[44px] px-7 py-2.5 text-xs font-extrabold tracking-wide shadow-sm hover:opacity-90 transition"
                           style="background-color: {{ $primaryColor }}; color: #FFFFFF;">
                            <x-app-icon name="map-pin" class="w-4 h-4" style="color: {{ $accentColor }};" />
                            <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
                        </a>
                    </div>
                @endif
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
