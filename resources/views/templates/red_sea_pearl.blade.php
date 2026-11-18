@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#4A7C8C';
    $secondaryColor = $styles['secondary_color'] ?? '#2C3E47';
    $bgColor = $styles['background_color'] ?? '#F6F8F9';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $accentColor = $styles['accent_color'] ?? '#9EB3BC';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Tajawal');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
    $hasBrideName = filled(trim((string) ($invitation->bride_name ?? '')));
@endphp

<div class="min-h-screen py-10 sm:py-16 px-4 sm:px-6 relative overflow-hidden"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; background-image: radial-gradient(ellipse 80% 45% at 50% 0%, #FFFFFF 0%, {{ $bgColor }} 70%); color: {{ $secondaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-2xl mx-auto relative z-10 space-y-8">
        <!-- Luminous Nacre / Mother-of-Pearl Coastal Stationery -->
        <div class="relative p-3 sm:p-5 border shadow-[0_20px_50px_-18px_rgba(44,62,71,0.14)]"
             style="background: linear-gradient(160deg, {{ $cardColor }} 0%, #FAFBFD 55%, #F2F6F7 100%); border-color: {{ $accentColor }};">

            <!-- Delicate Silver-Slate Inlay Border & Corner Pearl Nodes -->
            <div class="relative border p-6 sm:p-12 text-center space-y-7"
                 style="border-color: {{ $primaryColor }}38;">
                <span class="w-2.5 h-2.5 rounded-full border absolute -top-1.5 -start-1.5" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -top-1.5 -end-1.5" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -bottom-1.5 -start-1.5" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>
                <span class="w-2.5 h-2.5 rounded-full border absolute -bottom-1.5 -end-1.5" style="background-color: #FFFFFF; border-color: {{ $primaryColor }};"></span>

                <!-- Scalloped Mother-of-Pearl Crest -->
                <div class="flex flex-col items-center gap-3">
                    <svg viewBox="0 0 120 44" class="w-24 h-9" aria-hidden="true">
                        <path d="M16 32 Q60 6 104 32" fill="none" stroke="{{ $accentColor }}" stroke-width="1" />
                        <path d="M40 30 Q60 12 80 30 Q60 36 40 30 Z" fill="{{ $bgColor }}" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                        <line x1="60" y1="16" x2="60" y2="33" stroke="{{ $accentColor }}" stroke-width="0.9" />
                        <line x1="50" y1="20" x2="56" y2="32" stroke="{{ $accentColor }}" stroke-width="0.9" />
                        <line x1="70" y1="20" x2="64" y2="32" stroke="{{ $accentColor }}" stroke-width="0.9" />
                        <circle cx="60" cy="25" r="4.5" fill="#FFFFFF" stroke="{{ $primaryColor }}" stroke-width="1.2" />
                    </svg>

                    @if($invitation->welcome_message)
                        <div class="font-amiri text-base sm:text-lg leading-[1.95] font-bold px-2 max-w-lg mx-auto"
                             style="color: {{ $secondaryColor }};">
                            {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                        </div>
                    @endif
                </div>

                <p class="text-xs sm:text-sm font-semibold tracking-wide" style="color: {{ $primaryColor }};">
                    {{ $isEnInv ? 'Serenity by the sea, joy in every detail' : 'أمسية ساحلية صافية تفيض بالبهجة والسكينة' }}
                </p>

                <!-- Spacious Editorial Arabic Couple Typography -->
                <div class="py-3 space-y-3">
                    <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                        style="color: {{ $secondaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>

                    @if($hasBrideName)
                        <div class="flex items-center justify-center gap-3">
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $primaryColor }};"></span>
                            <span class="font-amiri text-xl font-bold" style="color: {{ $primaryColor }};">{{ $isEnInv ? '&' : 'و' }}</span>
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $primaryColor }};"></span>
                            <span class="h-px w-12" style="background-color: {{ $accentColor }};"></span>
                        </div>

                        <h1 class="font-amiri text-4xl sm:text-5xl font-bold leading-[1.25] break-words"
                            style="color: {{ $secondaryColor }};">
                            {{ $invitation->bride_name }}
                        </h1>
                    @endif
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 border shadow-sm overflow-hidden"
                             style="border-color: {{ $accentColor }}; background-color: #FFFFFF;">
                            <img src="{{ $invitation->cover_photo }}"
                                 alt="{{ $invitation->groom_name }}{{ $hasBrideName ? ' & ' . $invitation->bride_name : '' }}"
                                 onerror="this.parentElement.parentElement.style.display='none'"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-[1.95] px-2 max-w-md mx-auto opacity-85">
                        {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                    </p>
                @endif

                <!-- Serene Pearl & Sea-Glass Logistics Section -->
                <div class="pt-6 border-t space-y-3 max-w-md mx-auto" style="border-color: {{ $accentColor }}60;">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-extrabold" style="color: {{ $secondaryColor }};">
                        <x-app-icon name="calendar" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                    </div>

                    @if($dateInfo['hijri_date'])
                        <div class="text-xs sm:text-sm font-semibold" style="color: {{ $primaryColor }};">
                            @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                        </div>
                    @endif

                    @if($dateInfo['time'])
                        <div class="flex items-center justify-center gap-2 text-sm font-medium opacity-80">
                            <x-app-icon name="clock" class="w-4 h-4 shrink-0" style="color: {{ $primaryColor }};" />
                            @if($dateInfo['locale'] === 'en')
                                <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                            @else
                                <span dir="rtl">في تمام الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-3 space-y-1.5">
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
                                   class="inline-flex items-center gap-2 min-h-[44px] px-6 py-2 rounded-full text-xs font-bold border shadow-xs hover:opacity-90 transition"
                                   style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}; color: {{ $secondaryColor }};">
                                    <x-app-icon name="map-pin" class="w-4 h-4" style="color: {{ $primaryColor }};" />
                                    <span>{{ $isEnInv ? 'Open Venue on Google Maps' : 'عرض الموقع على خرائط Google Maps' }}</span>
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

        <div class="text-center text-xs opacity-70 pt-3 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
