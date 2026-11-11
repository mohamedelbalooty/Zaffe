@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#0F4C3A';
    $goldColor = '#C6A76B';
    $bgColor = $styles['background_color'] ?? '#F4F7F5';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
@endphp

<div class="min-h-screen py-10 px-4 sm:px-6 relative overflow-hidden font-amiri text-right"
     style="background-color: {{ $bgColor }}; color: #1E293B;">
    
    <div class="max-w-2xl mx-auto relative z-10 space-y-10">
        <!-- Islamic Heritage Card -->
        <div class="rounded-3xl p-8 sm:p-12 shadow-2xl relative border-4"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }};">
            
            <!-- Traditional Arabesque Arch Header -->
            <div class="text-center pb-6">
                <div class="inline-block p-2 rounded-full border-2 mb-3" style="border-color: {{ $goldColor }}; color: {{ $primaryColor }};">
                    <x-app-icon name="moon-star" class="w-8 h-8" />
                </div>
                
                <div class="font-bold text-lg sm:text-xl text-[#0F4C3A] leading-relaxed">
                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                </div>
                <div class="text-xs sm:text-sm text-[#0F4C3A]/80 mt-1 max-w-md mx-auto">
                    «وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»
                </div>
                <div class="w-24 h-0.5 mx-auto mt-4 bg-gradient-to-r from-transparent via-[#C6A76B] to-transparent"></div>
            </div>

            <div class="text-center space-y-6">
                <p class="font-tajawal text-xs sm:text-sm font-semibold tracking-wider text-slate-600">
                    بارك الله لهما وبارك عليهما وجمع بينهما في خير
                </p>

                <!-- Names with Calligraphic Flourish -->
                <div class="py-4 space-y-3">
                    <h1 class="text-4xl sm:text-6xl font-bold" style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    <div class="text-2xl font-bold" style="color: {{ $goldColor }};">وَ</div>
                    <h1 class="text-4xl sm:text-6xl font-bold" style="color: {{ $primaryColor }};">
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
                    <p class="font-tajawal text-sm sm:text-base leading-relaxed text-slate-700 px-4">
                        {!! nl2br(e($invitation->invitation_message)) !!}
                    </p>
                @endif

                <!-- Date & Venue -->
                <div class="p-6 rounded-2xl border space-y-3 font-tajawal"
                     style="background-color: #F4F7F5; border-color: {{ $primaryColor }}30;">
                    <div class="text-base sm:text-lg font-bold" style="color: {{ $primaryColor }};">
                        🗓 {{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}
                    </div>
                    @if($invitation->wedding_time)
                        <div class="text-sm font-semibold text-slate-600">
                            ⏰ الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}
                        </div>
                    @endif
                    <div class="text-sm font-bold pt-1 text-slate-800">
                        📍 {{ $invitation->venue_name }}
                    </div>
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold text-white shadow-md hover:opacity-90 transition"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-4 h-4" />
                                <span>موقع القاعة على Google Maps</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RSVP Form Section -->
        @if($invitation->is_rsvp_enabled && !$isPreview)
            <div id="rsvp-section" class="scroll-mt-10">
                <livewire:rsvp-form :invitation-id="$invitation->id" :max-guests="$invitation->max_guests_per_rsvp" />
            </div>
        @endif
    </div>
</div>
