@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'isPreview' => false,
])

@if(!empty($invitation->music_url))
    @php
        $gateEnabled = !($isPreview ?? false);
        $coupleTitle = collect([$invitation->groom_name ?? null, $invitation->bride_name ?? null])->filter()->implode(' & ');
        $styles = $invitation->effective_styles ?? [];
        $accentColor = $styles['primary_color'] ?? $primaryColor ?? '#C6A76B';

        $hex = ltrim((string) $accentColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $isBrightAccent = false;
        if (strlen($hex) === 6 && ctype_xdigit($hex)) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $isBrightAccent = (($r * 299 + $g * 587 + $b * 114) / 1000) > 155;
        }
        $floatingIconColor = $isBrightAccent ? '#151210' : '#FFFFFF';
    @endphp

    <div x-data="{
            gateOpen: {{ $gateEnabled ? 'false' : 'true' }},
            playing: false,
            audio: null,
            initPlayer() {
                this.audio = new Audio(@js($invitation->music_url));
                this.audio.loop = true;
            },
            openWithMusic() {
                this.gateOpen = true;
                if (this.audio) {
                    const p = this.audio.play();
                    if (p && typeof p.then === 'function') {
                        p.then(() => { this.playing = true; }).catch(() => { this.playing = false; });
                    } else {
                        this.playing = true;
                    }
                }
            },
            openSilently() {
                this.gateOpen = true;
                this.playing = false;
            },
            toggleAudio() {
                if (!this.audio) return;
                if (this.playing) {
                    this.audio.pause();
                    this.playing = false;
                } else {
                    const p = this.audio.play();
                    if (p && typeof p.then === 'function') {
                        p.then(() => { this.playing = true; }).catch(() => { this.playing = false; });
                    } else {
                        this.playing = true;
                    }
                }
            }
         }"
         x-init="initPlayer()">

        @if($gateEnabled)
            <!-- Interactive Royal Seal Open-Gate Screen (شاشة فتح الدعوة) -->
            <div x-show="!gateOpen"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 role="dialog"
                 aria-modal="true"
                 aria-label="شاشة فتح الدعوة"
                 class="fixed inset-0 z-50 bg-[#151210]/95 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 text-center text-[#FAF7F2]">

                <div class="max-w-md w-full rounded-3xl p-6 sm:p-8 bg-[#1E1A16] border shadow-2xl relative overflow-hidden"
                     style="border-color: {{ $accentColor }}88;">
                    <div class="border border-dashed rounded-2xl p-6 sm:p-8 space-y-6"
                         style="border-color: {{ $accentColor }}55;">

                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/5 border text-[11px] font-bold tracking-wide"
                             style="border-color: {{ $accentColor }}55; color: {{ $accentColor }};">
                            <x-app-icon name="crown" class="w-3.5 h-3.5" />
                            <span>شاشة فتح الدعوة</span>
                        </div>

                        <div class="space-y-2.5">
                            <p class="font-amiri text-base" style="color: {{ $accentColor }};">
                                دعوة زفاف خاصة
                            </p>
                            <h2 class="font-cairo text-2xl sm:text-3xl font-black text-white leading-snug">
                                {{ $coupleTitle ?: ($invitation->title ?? 'دعوة زفاف') }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[#DED5C8] leading-relaxed">
                                انقر لفتح بطاقة الدعوة والاستماع إلى المقطوعة الصوتية المصاحبة
                            </p>
                        </div>

                        <div class="space-y-3 pt-1">
                            <button type="button"
                                    data-open-invitation-gate
                                    @click="openWithMusic()"
                                    class="w-full py-3.5 px-6 rounded-2xl text-sm font-extrabold text-[#151210] shadow-lg hover:opacity-95 transition flex items-center justify-center gap-2.5 cursor-pointer focus-visible:outline-2 focus-visible:outline-white"
                                    style="background: linear-gradient(135deg, {{ $accentColor }} 0%, #DFC99E 50%, {{ $accentColor }} 100%);">
                                <x-app-icon name="mail-open" class="w-5 h-5" />
                                <span>اضغط لفتح الدعوة وتشغيل الزفّة</span>
                            </button>

                            <button type="button"
                                    @click="openSilently()"
                                    class="w-full py-2.5 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/15 text-xs font-semibold text-[#DED5C8] transition flex items-center justify-center gap-2 cursor-pointer focus-visible:outline-2 focus-visible:outline-white">
                                <x-app-icon name="volume-x" class="w-4 h-4" />
                                <span>فتح الدعوة بدون موسيقى</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Persistent Floating Play / Mute Control -->
        <div x-show="gateOpen" x-cloak class="fixed bottom-6 end-6 z-40">
            <button type="button"
                    @click="toggleAudio()"
                    class="w-12 h-12 rounded-full shadow-2xl flex items-center justify-center border-2 border-white/90 transition hover:scale-105 cursor-pointer focus-visible:outline-2 focus-visible:outline-[#C6A76B]"
                    style="background-color: {{ $accentColor }}; color: {{ $floatingIconColor }};"
                    aria-label="موسيقى الخلفية"
                    title="موسيقى الخلفية">
                <x-app-icon name="volume-2" class="w-5 h-5" x-show="playing" />
                <x-app-icon name="volume-x" class="w-5 h-5" x-show="!playing" />
            </button>
        </div>
    </div>
@endif
