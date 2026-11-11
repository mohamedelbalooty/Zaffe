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

        <!-- Live Countdown Section (Alpine.js) -->
        <div class="rounded-3xl p-6 sm:p-8 shadow-xl text-center border"
             style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;"
             x-data="{
                target: new Date('{{ \Carbon\Carbon::parse($invitation->wedding_date)->toIso8601String() }}').getTime(),
                now: new Date().getTime(),
                days: 0, hours: 0, minutes: 0, seconds: 0,
                update() {
                    const diff = Math.max(0, this.target - new Date().getTime());
                    this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                }
             }"
             x-init="update(); setInterval(() => update(), 1000);">
            
            <h3 class="font-cairo text-base sm:text-lg font-bold mb-4" style="color: {{ $primaryColor }};">
                ⏳ المتبقي حتى ليلة العمر
            </h3>

            <div class="grid grid-cols-4 gap-2 sm:gap-4 max-w-md mx-auto">
                <div class="p-3 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                    <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="days">0</span>
                    <span class="text-xs opacity-70">يوم</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                    <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="hours">0</span>
                    <span class="text-xs opacity-70">ساعة</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                    <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="minutes">0</span>
                    <span class="text-xs opacity-70">دقيقة</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                    <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="seconds">0</span>
                    <span class="text-xs opacity-70">ثانية</span>
                </div>
            </div>
        </div>

        <!-- Sections: Timeline, Photo Gallery, Dress Code -->
        @foreach($invitation->sections as $section)
            @if($section->is_enabled && $section->section_type === 'timeline' && !empty($section->content['events']))
                <div class="rounded-3xl p-6 sm:p-8 shadow-xl border"
                     style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
                    <h3 class="font-cairo text-xl font-bold text-center mb-6" style="color: {{ $primaryColor }};">
                        {{ $section->title_ar ?: 'برنامج الحفل' }}
                    </h3>
                    <div class="space-y-4 max-w-md mx-auto">
                        @foreach($section->content['events'] as $event)
                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                                <span class="px-3 py-1 rounded-xl text-xs font-bold text-white shrink-0"
                                      style="background-color: {{ $primaryColor }};">
                                    {{ $event['time'] }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-[#292521]">{{ $event['title'] }}</h4>
                                    @if(!empty($event['desc']))
                                        <p class="text-xs text-[#292521]/70 mt-0.5">{{ $event['desc'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($section->is_enabled && $section->section_type === 'gallery' && !empty($section->content['photos']))
                <div class="rounded-3xl p-6 sm:p-8 shadow-xl border"
                     style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
                    <h3 class="font-cairo text-xl font-bold text-center mb-6" style="color: {{ $primaryColor }};">
                        {{ $section->title_ar ?: 'ألبوم الذكريات' }}
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($section->content['photos'] as $photoUrl)
                            <div class="h-36 sm:h-44 rounded-2xl overflow-hidden shadow-sm border border-[#E8DED0]">
                                <img src="{{ $photoUrl }}" alt="صورة الزفاف" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <!-- RSVP Form Section -->
        @if($invitation->is_rsvp_enabled && !$isPreview)
            <div id="rsvp-section" class="scroll-mt-10">
                <livewire:rsvp-form :invitation-id="$invitation->id" :max-guests="$invitation->max_guests_per_rsvp" />
            </div>
        @endif

        <!-- WhatsApp Sharing & Copy Link Box -->
        @if(!$isPreview)
            <div class="rounded-3xl p-6 shadow-xl text-center space-y-4 border"
                 style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;"
                 x-data="{ copied: false }">
                <h4 class="text-sm font-bold opacity-80">شارك هذه الدعوة مع الأهل والأصدقاء</h4>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-md hover:bg-emerald-700 transition">
                        <x-app-icon name="whatsapp" class="w-4 h-4" />
                        <span>مشاركة عبر واتساب</span>
                    </a>

                    <button @click="navigator.clipboard.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#292521] text-white text-xs font-bold shadow-md hover:bg-[#3D3731] transition">
                        <x-app-icon name="copy" class="w-4 h-4 text-[#C6A76B]" />
                        <span x-text="copied ? 'تم نسخ الرابط! ✓' : 'نسخ رابط الدعوة'"></span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Floating audio / music player if configured -->
        @if($invitation->music_url)
            <div x-data="{ playing: false, audio: null }" 
                 x-init="audio = new Audio('{{ $invitation->music_url }}')"
                 class="fixed bottom-6 right-6 z-40">
                <button @click="if(playing) { audio.pause(); playing = false; } else { audio.play(); playing = true; }"
                        class="w-12 h-12 rounded-full shadow-2xl flex items-center justify-center text-white border-2 border-white transition transform hover:scale-110"
                        style="background-color: {{ $primaryColor }};">
                    <x-app-icon name="music" class="w-5 h-5 animate-spin" x-show="playing" />
                    <x-app-icon name="music" class="w-5 h-5" x-show="!playing" />
                </button>
            </div>
        @endif

        <!-- Platform Watermark -->
        <div class="text-center text-xs opacity-50 pt-6 pb-10">
            <span>صُممت بكل حب عبر </span>
            <a href="{{ route('home') }}" class="font-bold underline hover:opacity-100">منصة زفّــة</a>
        </div>
    </div>
</div>
