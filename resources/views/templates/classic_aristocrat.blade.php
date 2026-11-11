@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#8B263E'; // Royal Burgundy
    $goldColor = '#D4AF37';
    $bgColor = '#F8F6F0';
    $cardColor = '#FFFFFF';
@endphp

<div class="min-h-screen py-10 px-4 sm:px-6 relative overflow-hidden font-tajawal text-right text-[#2B2B2B]"
     style="background-color: {{ $bgColor }};">
    
    <div class="max-w-2xl mx-auto relative z-10 space-y-10">
        <!-- Aristocrat Parchment Card -->
        <div class="rounded-3xl p-8 sm:p-12 shadow-2xl relative border-2"
             style="background-color: {{ $cardColor }}; border-color: {{ $goldColor }};">
            
            <!-- Wax Seal Ornament on top -->
            <div class="flex justify-center -mt-16 mb-4">
                <div class="w-16 h-16 rounded-full bg-[#8B263E] border-4 border-[#D4AF37] shadow-xl flex items-center justify-center text-[#D4AF37] font-amiri font-bold text-2xl">
                    ز
                </div>
            </div>

            <div class="text-center space-y-6">
                @if($invitation->welcome_message)
                    <div class="font-amiri text-base sm:text-lg font-bold" style="color: {{ $primaryColor }};">
                        {!! nl2br(e($invitation->welcome_message)) !!}
                    </div>
                @endif

                <p class="text-xs sm:text-sm font-semibold tracking-wider text-stone-500">
                    تتشرف العائلتان الكريمتان بدعوتكم لحفل زفاف
                </p>

                <!-- Names -->
                <div class="py-4 space-y-3">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black" style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    <div class="font-amiri text-2xl font-bold" style="color: {{ $goldColor }};">و</div>
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black" style="color: {{ $primaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 shadow-xl overflow-hidden border-2"
                             style="border-color: {{ $goldColor }};">
                            <img src="{{ $invitation->cover_photo }}" alt="Couple" class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-relaxed text-stone-700 px-4">
                        {!! nl2br(e($invitation->invitation_message)) !!}
                    </p>
                @endif

                <div class="p-6 rounded-2xl border space-y-3"
                     style="background-color: #FAF8F5; border-color: {{ $goldColor }}50;">
                    <div class="text-base sm:text-lg font-bold" style="color: {{ $primaryColor }};">
                        🗓 {{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}
                    </div>
                    @if($invitation->wedding_time)
                        <div class="text-sm font-semibold text-stone-600">
                            الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}
                        </div>
                    @endif
                    <div class="text-sm font-bold text-stone-800 pt-1">
                        📍 {{ $invitation->venue_name }}
                    </div>
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-2 rounded-full text-xs font-bold text-white shadow-md hover:opacity-90 transition"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4" />
                                <span>موقع القاعة على الخريطة</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Countdown Section -->
        @include('templates.partials.countdown', [
            'invitation' => $invitation,
            'primaryColor' => $primaryColor,
            'cardColor' => $cardColor,
            'bgColor' => $bgColor,
        ])

        <!-- Timeline Section -->
        @include('templates.partials.timeline', [
            'invitation' => $invitation,
            'primaryColor' => $primaryColor,
            'cardColor' => $cardColor,
            'bgColor' => $bgColor,
        ])

        <!-- Gallery Section -->
        @include('templates.partials.gallery', [
            'invitation' => $invitation,
            'primaryColor' => $primaryColor,
            'cardColor' => $cardColor,
            'bgColor' => $bgColor,
        ])

        <!-- Dress Code Section -->
        @include('templates.partials.dress_code', [
            'invitation' => $invitation,
            'primaryColor' => $primaryColor,
            'cardColor' => $cardColor,
            'bgColor' => $bgColor,
        ])

        <!-- RSVP Form Section -->
        @if($invitation->is_rsvp_enabled && !$isPreview)
            <div id="rsvp-section" class="scroll-mt-10">
                <livewire:rsvp-form :invitation-id="$invitation->id" :max-guests="$invitation->max_guests_per_rsvp" />
            </div>
        @endif

        <!-- WhatsApp & Copy Link Box -->
        @if(!$isPreview)
            @include('templates.partials.share_box', [
                'invitation' => $invitation,
                'primaryColor' => $primaryColor,
                'cardColor' => $cardColor,
            ])
        @endif

        <!-- Floating audio / music player -->
        @include('templates.partials.music_player', [
            'invitation' => $invitation,
            'primaryColor' => $primaryColor,
        ])

        <!-- Platform Watermark -->
        <div class="text-center text-xs opacity-50 pt-6 pb-10">
            <span>صُممت بكل حب عبر </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">منصة زفّــة</a>
        </div>
    </div>
</div>
