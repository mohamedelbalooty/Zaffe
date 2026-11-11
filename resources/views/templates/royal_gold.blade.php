@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#C6A76B';
    $secondaryColor = $styles['secondary_color'] ?? '#292521';
    $bgColor = $styles['background_color'] ?? '#FAF7F2';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
@endphp

<div class="min-h-screen py-10 px-4 sm:px-6 relative overflow-hidden font-tajawal text-right"
     style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }};">
    
    <!-- Background subtle damask or geometric watermark -->
    <div class="max-w-2xl mx-auto relative z-10 space-y-10">
        
        <!-- Main Royal Invitation Card -->
        <div class="rounded-3xl p-8 sm:p-12 shadow-2xl relative border-2"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">
            
            <!-- Corner Decorative Ornaments -->
            <div class="absolute top-3 right-3 text-xs opacity-60" style="color: {{ $primaryColor }};">❖</div>
            <div class="absolute top-3 left-3 text-xs opacity-60" style="color: {{ $primaryColor }};">❖</div>
            <div class="absolute bottom-3 right-3 text-xs opacity-60" style="color: {{ $primaryColor }};">❖</div>
            <div class="absolute bottom-3 left-3 text-xs opacity-60" style="color: {{ $primaryColor }};">❖</div>

            <!-- Inner Gold Border -->
            <div class="border border-dashed p-6 sm:p-8 rounded-2xl text-center space-y-6"
                 style="border-color: {{ $primaryColor }}60;">
                
                <!-- Bismillah / Welcome verse -->
                @if($invitation->welcome_message)
                    <div class="font-amiri text-sm sm:text-base leading-relaxed font-bold px-4"
                         style="color: {{ $primaryColor }};">
                        {!! nl2br(e($invitation->welcome_message)) !!}
                    </div>
                    <div class="w-16 h-0.5 mx-auto opacity-40" style="background-color: {{ $primaryColor }};"></div>
                @endif

                <!-- Subtitle / Invitation heading -->
                <p class="text-xs sm:text-sm font-semibold tracking-wider opacity-75">
                    نتشرف بدعوتكم لحضور حفل زفاف
                </p>

                <!-- Bride & Groom Names -->
                <div class="py-4 space-y-3">
                    <h1 class="font-cairo text-3xl sm:text-5xl font-black tracking-tight"
                        style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    
                    <div class="flex items-center justify-center gap-3">
                        <span class="h-px w-12 opacity-30" style="background-color: {{ $primaryColor }};"></span>
                        <span class="font-amiri text-2xl font-bold" style="color: {{ $secondaryColor }};">و</span>
                        <span class="h-px w-12 opacity-30" style="background-color: {{ $primaryColor }};"></span>
                    </div>

                    <h1 class="font-cairo text-3xl sm:text-5xl font-black tracking-tight"
                        style="color: {{ $primaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                <!-- Cover / Couple Photo -->
                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full p-1.5 shadow-xl overflow-hidden border-2"
                             style="border-color: {{ $primaryColor }};">
                            <img src="{{ $invitation->cover_photo }}" 
                                 alt="{{ $invitation->groom_name }} & {{ $invitation->bride_name }}" 
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                @endif

                <!-- Invitation Message -->
                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-relaxed px-2 font-normal opacity-85">
                        {!! nl2br(e($invitation->invitation_message)) !!}
                    </p>
                @endif

                <div class="w-24 h-0.5 mx-auto opacity-30" style="background-color: {{ $primaryColor }};"></div>

                <!-- Wedding Date & Time Badge -->
                <div class="p-5 rounded-2xl border bg-opacity-30 space-y-3"
                     style="border-color: {{ $primaryColor }}40; background-color: {{ $bgColor }};">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-bold"
                         style="color: {{ $primaryColor }};">
                        <x-app-icon name="calendar" class="w-5 h-5" />
                        <span>{{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}</span>
                    </div>

                    @if($invitation->wedding_time)
                        <div class="flex items-center justify-center gap-2 text-sm font-semibold opacity-80">
                            <x-app-icon name="clock" class="w-4 h-4" />
                            <span>في تمام الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Venue & Address -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-center gap-2 text-base sm:text-lg font-bold">
                        <x-app-icon name="map-pin" class="w-5 h-5" style="color: {{ $primaryColor }};" />
                        <span>{{ $invitation->venue_name }}</span>
                    </div>

                    @if($invitation->venue_address)
                        <p class="text-xs sm:text-sm opacity-75 max-w-md mx-auto">
                            {{ $invitation->venue_address }}
                        </p>
                    @endif

                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-bold text-white shadow-md hover:opacity-90 transition"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4" />
                                <span>عرض الموقع على خرائط Google Maps</span>
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

        <!-- WhatsApp Sharing & Copy Link Box -->
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
