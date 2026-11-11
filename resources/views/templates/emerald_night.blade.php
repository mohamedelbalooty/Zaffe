@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#E6C687';
    $bgColor = '#0F172A';
    $cardColor = '#1E293B';
@endphp

<div class="min-h-screen py-10 px-4 sm:px-6 relative overflow-hidden font-cairo text-right text-slate-100"
     style="background-color: {{ $bgColor }};">
    
    <!-- Starry dots background -->
    <div class="max-w-2xl mx-auto relative z-10 space-y-10">
        <div class="rounded-3xl p-8 sm:p-12 shadow-2xl relative border border-[#E6C687]/40 text-center space-y-6"
             style="background-color: {{ $cardColor }};">
            
            <div class="text-[#E6C687] text-sm font-semibold tracking-widest flex items-center justify-center gap-2">
                <span>✦</span>
                <span>ليلة من ألف ليلة وليلة</span>
                <span>✦</span>
            </div>

            @if($invitation->welcome_message)
                <p class="font-amiri text-base sm:text-lg leading-relaxed text-[#E6C687] font-bold">
                    {!! nl2br(e($invitation->welcome_message)) !!}
                </p>
            @endif

            <div class="py-4 space-y-3">
                <h1 class="text-3xl sm:text-5xl font-black text-[#E6C687] tracking-tight">
                    {{ $invitation->groom_name }}
                </h1>
                <div class="text-xl text-slate-400 font-bold">&</div>
                <h1 class="text-3xl sm:text-5xl font-black text-[#E6C687] tracking-tight">
                    {{ $invitation->bride_name }}
                </h1>
            </div>

            @if($invitation->cover_photo)
                <div class="py-2">
                    <div class="w-40 h-40 mx-auto rounded-full p-1.5 shadow-2xl overflow-hidden border-2 border-[#E6C687]">
                        <img src="{{ $invitation->cover_photo }}" alt="Couple" class="w-full h-full object-cover rounded-full">
                    </div>
                </div>
            @endif

            @if($invitation->invitation_message)
                <p class="text-sm sm:text-base leading-relaxed text-slate-300 px-4 font-tajawal">
                    {!! nl2br(e($invitation->invitation_message)) !!}
                </p>
            @endif

            <div class="p-6 rounded-2xl border border-slate-700 bg-slate-900/60 space-y-3 font-tajawal">
                <div class="text-base sm:text-lg font-bold text-[#E6C687]">
                    🌙 {{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}
                </div>
                @if($invitation->wedding_time)
                    <div class="text-sm text-slate-400">
                        الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}
                    </div>
                @endif
                <div class="text-sm font-bold text-slate-200 pt-1">
                    📍 {{ $invitation->venue_name }}
                </div>
                @if($invitation->venue_map_url)
                    <div class="pt-2">
                        <a href="{{ $invitation->venue_map_url }}" target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold text-[#0F172A] bg-gradient-to-r from-[#E6C687] to-[#DFC99E] shadow-md">
                            <x-app-icon name="map-pin" class="w-4 h-4" />
                            <span>موقع الحفل على الخريطة</span>
                        </a>
                    </div>
                @endif
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
