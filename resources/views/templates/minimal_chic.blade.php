@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#1F2937';
    $secondaryColor = $styles['secondary_color'] ?? '#4B5563';
    $bgColor = $styles['background_color'] ?? '#FAFAFA';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
    $fontStack = \App\Support\TemplateStyleCatalog::fontCssStack($styles['font_family'] ?? 'Tajawal');
    $dateInfo = \App\Services\InvitationDateFormatter::forInvitation($invitation);
    $isEnInv = $dateInfo['locale'] === 'en';
@endphp

<div class="min-h-screen py-12 sm:py-16 px-4 sm:px-6 relative"
     dir="{{ $dateInfo['dir'] }}"
     style="background-color: {{ $bgColor }}; color: {{ $primaryColor }}; font-family: {{ $fontStack }};">

    <div class="max-w-xl mx-auto space-y-10">
        <!-- Minimal Editorial Gallery Card -->
        <div class="rounded-xl p-8 sm:p-14 border border-zinc-200 text-center space-y-8 shadow-sm"
             style="background-color: {{ $cardColor }};">

            <div class="space-y-3">
                <span class="text-[11px] font-extrabold uppercase tracking-[0.25em] text-zinc-500">
                    {{ $isEnInv ? 'Wedding Invitation' : 'دعوة زفاف' }}
                </span>
                <div class="w-8 h-px bg-zinc-300 mx-auto"></div>
            </div>

            @if($invitation->welcome_message)
                <p class="text-xs sm:text-sm leading-relaxed text-zinc-600 font-medium px-2">
                    {!! nl2br(e(str_replace('\n', "\n", $invitation->welcome_message))) !!}
                </p>
            @endif

            <!-- Monumental Typography -->
            <div class="py-4 space-y-3">
                <h1 class="font-cairo text-4xl sm:text-5xl font-black tracking-tight text-zinc-900 leading-tight">
                    {{ $invitation->groom_name }}
                </h1>
                <div class="text-sm font-bold tracking-widest text-zinc-400">&amp;</div>
                <h1 class="font-cairo text-4xl sm:text-5xl font-black tracking-tight text-zinc-900 leading-tight">
                    {{ $invitation->bride_name }}
                </h1>
            </div>

            @if($invitation->cover_photo)
                <div class="py-2">
                    <div class="w-full h-64 sm:h-72 mx-auto rounded-lg overflow-hidden border border-zinc-200">
                        <img src="{{ $invitation->cover_photo }}"
                             alt="{{ $invitation->groom_name }} & {{ $invitation->bride_name }}"
                             onerror="this.parentElement.parentElement.style.display='none'"
                             class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            @if($invitation->invitation_message)
                <p class="text-sm text-zinc-600 leading-[1.85] max-w-md mx-auto">
                    {!! nl2br(e(str_replace('\n', "\n", $invitation->invitation_message))) !!}
                </p>
            @endif

            <!-- Architectural Date & Venue Block -->
            <div class="pt-7 border-t border-zinc-200 space-y-2.5">
                <div class="text-lg font-extrabold text-zinc-900">
                    <bdi dir="{{ $dateInfo['dir'] }}">{{ $dateInfo['gregorian_date'] }}</bdi>
                </div>
                @if($dateInfo['hijri_date'])
                    <div class="text-xs sm:text-sm font-medium text-zinc-600">
                        @if($dateInfo['locale'] === 'ar')<span>الموافق </span>@endif<bdi dir="rtl">{{ $dateInfo['hijri_date'] }}</bdi>
                    </div>
                @endif
                @if($dateInfo['time'])
                    <div class="text-xs font-bold text-zinc-500">
                        @if($dateInfo['locale'] === 'en')
                            <bdi dir="ltr">At {{ $dateInfo['time'] }}</bdi>
                        @else
                            <span dir="rtl">الساعة <bdi dir="rtl">{{ $dateInfo['time'] }}</bdi></span>
                        @endif
                    </div>
                @endif
                <div class="text-sm font-bold text-zinc-800 pt-2">
                    {{ $invitation->venue_name }}
                </div>
                @if($invitation->venue_address)
                    <div class="text-xs text-zinc-500">{{ $invitation->venue_address }}</div>
                @endif
                @if($invitation->venue_map_url)
                    <div class="pt-2">
                        <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-xs font-bold text-zinc-900 underline underline-offset-4 hover:text-zinc-600 transition">
                            <span>{{ $isEnInv ? 'Open Google Maps' : 'عرض الموقع على Google Maps' }}</span>
                            <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @include('templates.partials.countdown', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $primaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.timeline', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $primaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.gallery', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $primaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])
        @include('templates.partials.dress_code', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $primaryColor, 'cardColor' => $cardColor, 'bgColor' => $bgColor])

        @include('templates.partials.access_section', [
            'invitation' => $invitation,
            'personalGuest' => $personalGuest ?? null,
            'isPreview' => $isPreview ?? false,
        ])

        @if(!$isPreview)
            @include('templates.partials.share_box', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'secondaryColor' => $primaryColor, 'cardColor' => $cardColor])
        @endif

        @include('templates.partials.music_player', ['invitation' => $invitation, 'primaryColor' => $primaryColor, 'isPreview' => $isPreview ?? false])

        <div class="text-center text-xs opacity-60 pt-4 pb-8">
            <span>{{ $isEnInv ? 'Crafted with Zaffe' : 'صُممت بكل حب عبر' }} </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">{{ $isEnInv ? 'Zaffe Studio' : 'منصة زفّــة' }}</a>
        </div>
    </div>
</div>
