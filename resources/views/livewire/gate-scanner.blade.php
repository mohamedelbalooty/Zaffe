<div class="min-h-screen bg-[#1A1612] text-white py-5 px-4" x-data="gateCameraScanner()">
    <div class="max-w-2xl mx-auto space-y-5">
        <!-- Event & Gate Header -->
        <div class="bg-[#292521] rounded-3xl border border-[#C6A76B]/30 p-5 flex flex-wrap items-center justify-between gap-3 shadow-lg">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#C6A76B]/20 text-[#E5C98B] text-[11px] font-bold">
                    <x-app-icon name="shield-check" class="w-3.5 h-3.5" />
                    <span>بوابة: {{ $gate->name }}</span>
                </div>
                <h1 class="font-cairo text-lg sm:text-xl font-black text-white mt-2">
                    {{ $invitation->groom_name }} و {{ $invitation->bride_name }}
                </h1>
                <p class="text-xs text-[#E8DED0]/70">{{ $invitation->venue_name }}</p>
            </div>
            <div class="text-left">
                <span class="block text-[11px] text-[#E8DED0]/60">عمليات المسح بهذه البوابة</span>
                <span class="font-mono text-2xl font-black text-[#C6A76B]">{{ $gate->scan_count }}</span>
            </div>
        </div>

        <!-- Live Attendance Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
            <div class="p-3 rounded-2xl bg-white/5 border border-white/10">
                <span class="block text-[11px] text-white/60">دعوات مؤكدة</span>
                <span class="block text-xl font-black text-white mt-0.5">{{ $stats['attending_parties'] }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-emerald-500/15 border border-emerald-400/30">
                <span class="block text-[11px] text-emerald-200">تم الدخول</span>
                <span class="block text-xl font-black text-emerald-300 mt-0.5">{{ $stats['checked_in_parties'] }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-amber-500/15 border border-amber-400/30">
                <span class="block text-[11px] text-amber-200">متبقي (دعوات)</span>
                <span class="block text-xl font-black text-amber-300 mt-0.5">{{ $stats['remaining_parties'] }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-[#C6A76B]/15 border border-[#C6A76B]/30">
                <span class="block text-[11px] text-[#E5C98B]">متبقي (أشخاص)</span>
                <span class="block text-xl font-black text-[#E5C98B] mt-0.5">{{ $stats['remaining_heads'] }}</span>
            </div>
        </div>

        <!-- Feedback Banner -->
        @if($scanState)
            @php
                $bannerClasses = match ($scanState) {
                    'success' => 'bg-emerald-950/90 border-emerald-400 text-emerald-100',
                    'duplicate' => 'bg-amber-950/90 border-amber-400 text-amber-100',
                    default => 'bg-rose-950/90 border-rose-400 text-rose-100',
                };
            @endphp
            <div class="p-4 rounded-3xl border-2 {{ $bannerClasses }} shadow-xl space-y-1 text-center animate-fade-in">
                @if($scanGuestName)
                    <p class="font-cairo text-xl font-black">{{ $scanGuestName }}</p>
                @endif
                <p class="text-sm font-bold">{{ $scanMessage }}</p>
                @if($scanGuestDetail)
                    <p class="text-xs opacity-85">{{ $scanGuestDetail }}</p>
                @endif
            </div>
        @endif

        <!-- Camera QR Scanner + Pass Code Form -->
        <div class="bg-[#292521] rounded-3xl border border-white/10 p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-cairo text-base font-bold text-white flex items-center gap-2">
                    <x-app-icon name="qr-code" class="w-5 h-5 text-[#C6A76B]" />
                    <span>مسح بطاقة QR أو إدخال الكود</span>
                </h2>
                <button type="button" @click="toggleCamera()"
                        class="min-h-[40px] px-4 py-2 rounded-xl bg-[#C6A76B] text-[#1A1612] text-xs font-bold hover:opacity-90 transition cursor-pointer"
                        x-text="cameraActive ? 'إيقاف الكاميرا' : 'تشغيل كاميرا المسح'"></button>
            </div>

            <div x-show="cameraActive" x-cloak class="rounded-2xl overflow-hidden bg-black border border-[#C6A76B]/40 relative">
                <video x-ref="video" autoplay playsinline muted class="w-full max-h-64 object-cover"></video>
                <div class="p-2 text-center text-[11px] text-[#E5C98B] bg-black/70">وجّه الكاميرا نحو رمز QR على هاتف الضيف</div>
            </div>

            <p x-show="cameraNotice" x-cloak x-text="cameraNotice" class="text-xs text-amber-300 bg-amber-950/50 border border-amber-400/30 rounded-xl p-3"></p>

            <form wire:submit="checkInByCode" class="flex flex-col sm:flex-row gap-2">
                <input type="text" wire:model="passCodeInput" dir="ltr" maxlength="48"
                       placeholder="أدخل كود البطاقة (مثال: K7P9MX)"
                       class="flex-1 min-h-[44px] px-4 py-2.5 rounded-xl bg-black/40 border border-white/15 text-white font-mono text-sm tracking-widest uppercase focus:border-[#C6A76B] outline-none">
                <button type="submit"
                        class="min-h-[44px] px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition cursor-pointer shrink-0">
                    تحقق وسجّل الدخول
                </button>
            </form>
        </div>

        <!-- Fast Guest Lookup -->
        <div class="bg-[#292521] rounded-3xl border border-white/10 p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-cairo text-base font-bold text-white flex items-center gap-2">
                    <x-app-icon name="users" class="w-5 h-5 text-[#C6A76B]" />
                    <span>البحث السريع عن مدعو</span>
                </h2>
                <input type="text" wire:model.live.debounce.200ms="search"
                       placeholder="ابحث بالاسم، آخر أرقام الجوال، أو الكود..."
                       class="w-full sm:w-64 px-3.5 py-2 rounded-xl bg-black/40 border border-white/15 text-white text-xs focus:border-[#C6A76B] outline-none">
            </div>

            <div class="divide-y divide-white/10">
                @forelse($guests as $guest)
                    <div class="py-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <p class="text-sm font-bold text-white">{{ $guest->name }}</p>
                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-white/65">
                                <span class="px-2 py-0.5 rounded bg-white/10 text-[#E5C98B] font-bold">
                                    {{ \App\Models\InvitationGuest::categoryLabel($guest->category, 'ar') }}
                                </span>
                                <span>الأفراد: {{ $guest->getExpectedPartySize() }}</span>
                                <span dir="ltr" class="font-mono text-white/80">{{ $guest->pass_code }}</span>
                            </div>
                        </div>

                        <div>
                            @if($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_DECLINED)
                                <span class="px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-bold">معتذر</span>
                            @elseif($guest->isCheckedIn())
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 text-xs font-bold">
                                    <x-app-icon name="check-circle" class="w-4 h-4" />
                                    <span>دخل ({{ $guest->checked_in_at->timezone($invitation->timezone ?: 'Africa/Cairo')->format('H:i') }})</span>
                                </span>
                            @else
                                <button type="button" wire:click="checkInGuest({{ $guest->id }})"
                                        class="min-h-[40px] px-4 py-2 rounded-xl bg-[#C6A76B] text-[#1A1612] text-xs font-bold hover:opacity-90 transition cursor-pointer">
                                    تأكيد الدخول
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-xs text-white/50">لا توجد نتائج مطابقة للبحث حالياً.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function gateCameraScanner() {
            return {
                cameraActive: false,
                cameraNotice: '',
                stream: null,
                timer: null,
                lastScanned: '',
                async toggleCamera() {
                    if (this.cameraActive) {
                        this.stopCamera();
                        return;
                    }
                    this.cameraNotice = '';
                    if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
                        this.cameraNotice = 'مسح الكاميرا التلقائي غير مدعوم في هذا المتصفح — يمكنك المسح بكاميرا الهاتف الأصلية لفتح رابط البطاقة أو استخدام البحث السريع وكود التحقق أدناه.';
                        return;
                    }
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                        this.$refs.video.srcObject = this.stream;
                        this.cameraActive = true;
                        const detector = new BarcodeDetector({ formats: ['qr_code'] });
                        this.timer = setInterval(async () => {
                            if (!this.cameraActive || this.$refs.video.readyState < 2) return;
                            try {
                                const codes = await detector.detect(this.$refs.video);
                                if (codes.length > 0 && codes[0].rawValue && codes[0].rawValue !== this.lastScanned) {
                                    this.lastScanned = codes[0].rawValue;
                                    this.$wire.scanPayload(codes[0].rawValue);
                                    setTimeout(() => { this.lastScanned = ''; }, 3000);
                                }
                            } catch (e) {}
                        }, 450);
                    } catch (err) {
                        this.cameraNotice = 'تعذر الوصول إلى الكاميرا. يرجى السماح بصلاحية الكاميرا أو استخدام كود التحقق اليدوي.';
                    }
                },
                stopCamera() {
                    this.cameraActive = false;
                    if (this.timer) clearInterval(this.timer);
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                    }
                }
            };
        }
    </script>
</div>
