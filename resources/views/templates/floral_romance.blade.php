@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#D9828A';
    $secondaryColor = $styles['secondary_color'] ?? '#4A3E3D';
    $bgColor = $styles['background_color'] ?? '#FDF8F7';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
@endphp

<div class="min-h-screen py-10 px-4 sm:px-6 relative overflow-hidden font-cairo text-right"
     style="background-color: {{ $bgColor }}; color: {{ $secondaryColor }};">
    
    <div class="max-w-2xl mx-auto relative z-10 space-y-10">
        <!-- Floral Card -->
        <div class="rounded-3xl p-8 sm:p-12 shadow-xl relative border"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
            
            <!-- Floral Garland Header Accent -->
            <div class="text-center pb-6">
                <div class="inline-flex items-center justify-center gap-2 text-xl" style="color: {{ $primaryColor }};">
                    <span>🌸</span>
                    <span class="w-16 h-px bg-current opacity-40"></span>
                    <span>🌿</span>
                    <span class="w-16 h-px bg-current opacity-40"></span>
                    <span>🌸</span>
                </div>
            </div>

            <div class="text-center space-y-6">
                @if($invitation->welcome_message)
                    <p class="font-amiri text-base sm:text-lg leading-relaxed font-bold" style="color: {{ $primaryColor }};">
                        {!! nl2br(e($invitation->welcome_message)) !!}
                    </p>
                @endif

                <p class="text-xs sm:text-sm tracking-wider uppercase opacity-70">
                    دعوة زفاف مفعمة بالمحبة والبهجة
                </p>

                <!-- Names with Floral Script -->
                <div class="py-4 space-y-2">
                    <h1 class="text-3xl sm:text-5xl font-extrabold" style="color: {{ $primaryColor }};">
                        {{ $invitation->groom_name }}
                    </h1>
                    <span class="text-xl font-amiri font-bold opacity-60">و</span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold" style="color: {{ $primaryColor }};">
                        {{ $invitation->bride_name }}
                    </h1>
                </div>

                @if($invitation->cover_photo)
                    <div class="py-2">
                        <div class="w-40 h-40 mx-auto rounded-3xl p-2 shadow-lg overflow-hidden border-2"
                             style="border-color: {{ $primaryColor }}50;">
                            <img src="{{ $invitation->cover_photo }}" alt="Couple" class="w-full h-full object-cover rounded-2xl">
                        </div>
                    </div>
                @endif

                @if($invitation->invitation_message)
                    <p class="text-sm sm:text-base leading-relaxed opacity-85 px-4 font-tajawal">
                        {!! nl2br(e($invitation->invitation_message)) !!}
                    </p>
                @endif

                <!-- Date & Venue -->
                <div class="p-6 rounded-2xl border space-y-3"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}30;">
                    <div class="text-base sm:text-lg font-bold" style="color: {{ $primaryColor }};">
                        🗓 {{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}
                    </div>
                    @if($invitation->wedding_time)
                        <div class="text-sm font-semibold opacity-80">
                            ⏰ في تمام الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}
                        </div>
                    @endif
                    <div class="text-sm font-bold pt-2 opacity-90">
                        📍 {{ $invitation->venue_name }}
                    </div>
                    @if($invitation->venue_address)
                        <div class="text-xs opacity-70 max-w-sm mx-auto">{{ $invitation->venue_address }}</div>
                    @endif
                    @if($invitation->venue_map_url)
                        <div class="pt-2">
                            <a href="{{ $invitation->venue_map_url }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold text-white shadow-sm"
                               style="background-color: {{ $primaryColor }};">
                                <x-app-icon name="map-pin" class="w-3.5 h-3.5" />
                                <span>موقع الحفل على الخريطة</span>
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

        <!-- WhatsApp Box -->
        @if(!$isPreview)
            <div class="rounded-3xl p-6 shadow-md text-center space-y-3 bg-white border border-[#E8DED0]">
                <h4 class="text-xs font-bold opacity-75">مشاركة الدعوة مع الأهل والأصدقاء</h4>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank"
                       class="px-5 py-2.5 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-sm hover:bg-emerald-700 transition flex items-center gap-1.5">
                        <x-app-icon name="whatsapp" class="w-4 h-4" />
                        <span>مشاركة عبر واتساب</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
