@php
    $isEn = app()->getLocale() === 'en';
    $defaultMsgLang = ($shareCard['invitation']['locale'] ?? 'ar') === 'en' ? 'en' : 'ar';
    $templateTitle = $isEn
        ? ($shareCard['theme']['template_name_en'] ?? $shareCard['theme']['template_name_ar'])
        : $shareCard['theme']['template_name_ar'];
@endphp

<x-layouts.dashboard :title="$isEn ? 'WhatsApp Share Card — ' . $invitation->title : 'بطاقة مشاركة واتساب — ' . $invitation->title">
    <div class="space-y-6"
         x-data="{
            payload: @js($shareCard),
            msgLang: @js($defaultMsgLang),
            rendering: true,
            renderError: false,
            canNativeFileShare: false,
            pngBlob: null,
            downloaded: false,
            copiedMsg: false,
            copiedLink: false,
            nativeFeedback: '',
            get currentMessage() {
                return (this.payload.messages[this.msgLang] || this.payload.messages.ar).text;
            },
            get currentWhatsAppUrl() {
                return (this.payload.messages[this.msgLang] || this.payload.messages.ar).whatsapp_url;
            },
            async init() {
                await this.$nextTick();
                await this.generateCard();
            },
            async generateCard() {
                this.rendering = true;
                this.renderError = false;
                try {
                    let attempts = 0;
                    while (!window.ZaffeShareCard && attempts < 20) {
                        await new Promise((r) => setTimeout(r, 100));
                        attempts++;
                    }
                    if (!window.ZaffeShareCard) {
                        throw new Error('Share card renderer is not loaded.');
                    }
                    const canvas = this.$refs.shareCanvas;
                    await window.ZaffeShareCard.render(canvas, this.payload);
                    this.pngBlob = await window.ZaffeShareCard.toBlob(canvas);

                    if (navigator.canShare && typeof File !== 'undefined' && this.pngBlob) {
                        const testFile = new File([this.pngBlob], this.payload.filename, { type: 'image/png' });
                        this.canNativeFileShare = Boolean(navigator.canShare({ files: [testFile] }));
                    }
                } catch (e) {
                    this.renderError = true;
                } finally {
                    this.rendering = false;
                }
            },
            copyText(value, flagKey) {
                const done = () => {
                    this[flagKey] = true;
                    setTimeout(() => { this[flagKey] = false; }, 2500);
                };
                if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                    navigator.clipboard.writeText(value).then(done).catch(() => this.fallbackCopy(value, done));
                    return;
                }
                this.fallbackCopy(value, done);
            },
            fallbackCopy(value, onDone) {
                try {
                    const ta = document.createElement('textarea');
                    ta.value = value;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    if (onDone) onDone();
                } catch (e) {}
            },
            async downloadImage() {
                if (!this.pngBlob && this.$refs.shareCanvas && window.ZaffeShareCard) {
                    this.pngBlob = await window.ZaffeShareCard.toBlob(this.$refs.shareCanvas);
                }
                if (!this.pngBlob) return;
                const url = URL.createObjectURL(this.pngBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = this.payload.filename || 'zaffe-share-card.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(url), 1000);
                this.downloaded = true;
                setTimeout(() => { this.downloaded = false; }, 3500);
            },
            async shareNative() {
                if (!this.pngBlob || !navigator.share || typeof File === 'undefined') return;
                this.copyText(this.currentMessage, 'copiedMsg');
                const file = new File([this.pngBlob], this.payload.filename, { type: 'image/png' });
                try {
                    await navigator.share({
                        files: [file],
                        title: this.payload.invitation.title,
                        text: this.currentMessage,
                    });
                    this.nativeFeedback = @js($isEn ? 'Shared! Message & link were also copied to clipboard if needed.' : 'تمت المشاركة! تم نسخ الرسالة والرابط أيضاً احتياطاً للصق.');
                } catch (err) {
                    if (err && err.name !== 'AbortError') {
                        this.nativeFeedback = @js($isEn ? 'Direct app sharing was cancelled or unsupported; use Download PNG + Open WhatsApp below.' : 'تعذرت المشاركة المباشرة في هذا المتصفح؛ استخدم تحميل الصورة ثم فتح واتساب أدناه.');
                    }
                }
            }
         }">

        <!-- Header & Navigation Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-[#7A5A20]">
                    <a href="{{ route('dashboard.invitations.index') }}" class="hover:underline inline-flex items-center gap-1">
                        <x-app-icon name="arrow-right" class="w-3.5 h-3.5 dir-arrow" />
                        <span>{{ $isEn ? 'My Invitations' : 'إدارة دعواتي' }}</span>
                    </a>
                    <span class="text-[#C6A76B]">/</span>
                    <span>{{ $isEn ? 'WhatsApp Share Card' : 'بطاقة مشاركة واتساب' }}</span>
                </div>
                <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                    {{ $isEn ? 'WhatsApp Share Card — ' . $invitation->formattedCoupleNames('en', ' & ') : 'بطاقة مشاركة واتساب — ' . $invitation->formattedCoupleNames('ar') }}
                </h1>
                <p class="text-xs sm:text-sm text-[#52493F]">
                    {{ $isEn
                        ? 'Download a crisp 4:5 theme-matched card image alongside a ready WhatsApp message and your live invitation link.'
                        : 'حمّل صورة دعوة أنيقة بمقاس واتساب (4:5) متطابقة مع تصميمك، وشاركها مع الرسالة الجاهزة ورابط الدعوة التفاعلية.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}"
                   class="btn-ghost-ivory !min-h-[40px] !py-2 !px-3.5 !text-xs">
                    <x-app-icon name="edit" class="w-4 h-4 text-[#856326]" />
                    <span>{{ $isEn ? 'Edit Card in Studio' : 'تعديل التصميم' }}</span>
                </a>

                @if($invitation->isPublished())
                    <a href="{{ $invitation->public_url }}" target="_blank" rel="noopener noreferrer"
                       class="btn-obsidian !min-h-[40px] !py-2 !px-3.5 !text-xs">
                        <x-app-icon name="external-link" class="w-4 h-4 text-[#DFC99E]" />
                        <span>{{ $isEn ? 'View Live Invitation' : 'عرض الدعوة المباشرة' }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Draft Reminder Banner -->
        @if(!$invitation->isPublished())
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-300/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <x-app-icon name="alert-triangle" class="w-5 h-5 text-amber-700 shrink-0 mt-0.5" />
                    <div class="space-y-0.5 text-xs sm:text-sm text-amber-950">
                        <p class="font-extrabold">
                            {{ $isEn ? 'This invitation is currently a private draft.' : 'هذه الدعوة ما زالت في وضع (مسودة خاصة).' }}
                        </p>
                        <p class="text-amber-800">
                            {{ $isEn ? 'You can preview and download the share image now, but guests must wait until you publish before opening the live link.' : 'يمكنك معاينة وتحميل صورة المشاركة الآن، ولكن لن يتمكن الضيوف من فتح رابط الدعوة التفاعلية حتى تقوم بنشرها.' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('dashboard.invitations.toggle-publish', $invitation->id) }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="btn-royal !min-h-[40px] !py-2 !px-4 !text-xs">
                        <x-app-icon name="sparkles" class="w-4 h-4" />
                        <span>{{ $isEn ? 'Publish Invitation Now' : 'نشر الدعوة الآن' }}</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Main Two-Column Share Studio Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- Preview Column (4:5 Generated Card) -->
            <div class="lg:col-span-5 card-atelier p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A] flex items-center gap-2">
                        <x-app-icon name="image" class="w-4.5 h-4.5 text-[#856326]" />
                        <span>{{ $isEn ? 'Generated Share Image' : 'معاينة صورة المشاركة' }}</span>
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F4EBD9] text-[#7A5A20] border border-[#C6A76B]/30 tabular-nums">
                        {{ $shareCard['dimensions']['width'] }} × {{ $shareCard['dimensions']['height'] }} · 4:5
                    </span>
                </div>

                <div class="relative mx-auto w-full max-w-[380px] aspect-[4/5] rounded-2xl overflow-hidden border border-[#E4D9C8] bg-[#FAF7F2] shadow-royal-card">
                    <canvas x-ref="shareCanvas"
                            width="{{ $shareCard['dimensions']['width'] }}"
                            height="{{ $shareCard['dimensions']['height'] }}"
                            class="w-full h-full block object-contain"
                            aria-label="{{ $isEn ? 'Generated WhatsApp wedding invitation share card preview' : 'معاينة صورة بطاقة مشاركة دعوة الزفاف عبر واتساب' }}"></canvas>

                    <div x-show="rendering"
                         class="absolute inset-0 bg-[#FAF7F2]/85 backdrop-blur-xs flex flex-col items-center justify-center gap-2.5 text-xs font-bold text-[#52493F]">
                        <x-app-icon name="sparkles" class="w-6 h-6 text-[#C6A76B] animate-pulse" />
                        <span>{{ $isEn ? 'Crafting your invitation card…' : 'جاري تجهيز صورة الدعوة…' }}</span>
                    </div>

                    <div x-show="renderError" x-cloak
                         class="absolute inset-0 bg-white/95 p-6 flex flex-col items-center justify-center text-center gap-3">
                        <x-app-icon name="alert-triangle" class="w-7 h-7 text-amber-600" />
                        <p class="text-xs font-bold text-[#221E1A]">
                            {{ $isEn ? 'Could not render the share image in this browser.' : 'تعذر إنشاء صورة البطاقة في هذا المتصفح.' }}
                        </p>
                        <button type="button" @click="generateCard()" class="btn-ghost-ivory !min-h-[36px] !py-1.5 !px-3 !text-xs">
                            {{ $isEn ? 'Retry' : 'إعادة المحاولة' }}
                        </button>
                    </div>
                </div>

                <!-- Theme & Invitation Spec Summary -->
                <div class="flex flex-wrap items-center justify-center gap-2 text-[11px] font-bold">
                    <span class="px-2.5 py-1 rounded-full bg-[#FAF7F2] border border-[#E4D9C8] text-[#52493F]">
                        {{ $isEn ? 'Theme:' : 'القالب:' }} {{ $templateTitle }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full {{ $invitation->allowsRsvp() ? 'bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20' : 'bg-stone-100 text-stone-700 border border-stone-200' }}">
                        {{ $invitation->allowsRsvp()
                            ? ($isEn ? 'RSVP Enabled' : 'تأكيد الحضور مُفعّل')
                            : ($isEn ? 'Announcement Only (No RSVP)' : 'بطاقة عرض فقط (بدون RSVP)') }}
                    </span>
                    @if(!$invitation->hasBrideName())
                        <span class="px-2.5 py-1 rounded-full bg-[#F4EBD9] text-[#7A5A20] border border-[#C6A76B]/30">
                            {{ $isEn ? 'Groom-Only Privacy' : 'خصوصية: اسم العريس فقط' }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Workflow Column -->
            <div class="lg:col-span-7 space-y-5">

                <!-- Mobile Native Share Sheet (when supported by device browser) -->
                <div x-show="canNativeFileShare" x-cloak
                     class="card-atelier p-5 border-[#0F4C3A]/30 bg-[#ECF5F0]/55 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <h3 class="font-cairo text-base font-extrabold text-[#0F4C3A] flex items-center gap-2">
                                <x-app-icon name="share" class="w-4.5 h-4.5" />
                                <span>{{ $isEn ? 'Quick Mobile Share (Image + Copy)' : 'مشاركة سريعة من الجوال (صورة + نص)' }}</span>
                            </h3>
                            <p class="text-xs text-[#221E1A]/80 leading-relaxed">
                                {{ $isEn
                                    ? 'Opens your phone share sheet with the PNG card ready. We also copy the message & link to your clipboard so you can paste them easily.'
                                    : 'يفتح قائمة المشاركة في جوالك بالصورة جاهزة، مع نسخ الرسالة والرابط تلقائياً للحافظة لتسهيل اللصق داخل واتساب.' }}
                            </p>
                        </div>
                    </div>
                    <button type="button"
                            @click="shareNative()"
                            :disabled="rendering || renderError"
                            class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-[#0F4C3A] hover:bg-[#0B3A2C] text-white font-extrabold text-xs sm:text-sm transition inline-flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <x-app-icon name="whatsapp" class="w-4.5 h-4.5" />
                        <span>{{ $isEn ? 'Share Image & Copy Message' : 'مشاركة الصورة ونسخ الرسالة' }}</span>
                    </button>
                    <p x-show="nativeFeedback" x-cloak x-text="nativeFeedback" class="text-xs font-bold text-[#0F4C3A]"></p>
                </div>

                <!-- Step 1: Download Share Image -->
                <div class="card-atelier p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#151210] text-[#DFC99E] font-cairo font-black text-sm flex items-center justify-center shrink-0 tabular-nums">1</span>
                        <div>
                            <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A]">
                                {{ $isEn ? 'Download the WhatsApp Share Image' : 'الخطوة ١: تحميل صورة الدعوة لواتساب' }}
                            </h3>
                            <p class="text-xs text-[#52493F]">
                                {{ $isEn ? 'Crisp 1080×1350 PNG matched to your invitation theme, ready to attach in any chat.' : 'صورة عالية الدقة (1080×1350 PNG) مطابقة لهوية دعوتك ومناسبة لعرض الجوال بدون قصّ مزعج.' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button"
                                @click="downloadImage()"
                                :disabled="rendering || renderError"
                                class="btn-royal flex-1 sm:flex-none cursor-pointer disabled:opacity-50">
                            <x-app-icon name="download" class="w-4.5 h-4.5" />
                            <span>{{ $isEn ? 'Download Share Image (PNG)' : 'تحميل صورة الدعوة (PNG)' }}</span>
                        </button>

                        <span x-show="downloaded" x-cloak class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0F4C3A]">
                            <x-app-icon name="check-circle" class="w-4 h-4" />
                            <span>{{ $isEn ? 'Image downloaded!' : 'تم تحميل الصورة بنجاح!' }}</span>
                        </span>
                    </div>
                </div>

                <!-- Step 2: Dynamic WhatsApp Message + Live Link -->
                <div class="card-atelier p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#0F4C3A] text-white font-cairo font-black text-sm flex items-center justify-center shrink-0 tabular-nums">2</span>
                            <div>
                                <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A]">
                                    {{ $isEn ? 'Send WhatsApp Message & Live Link' : 'الخطوة ٢: إرسال رسالة واتساب ورابط الدعوة' }}
                                </h3>
                                <p class="text-xs text-[#52493F]">
                                    {{ $isEn ? 'Includes event date, Hijri date, venue, and your live interactive invitation link.' : 'تتضمن بيانات الزفاف الرسمية ورابط الدعوة التفاعلية للخرائط وتفاصيل الحفل.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Message Locale Toggle -->
                        <div class="inline-flex items-center bg-[#FAF7F2] p-1 rounded-xl border border-[#E4D9C8] shrink-0 self-start sm:self-center">
                            <button type="button"
                                    @click="msgLang = 'ar'"
                                    :class="msgLang === 'ar' ? 'bg-[#151210] text-[#DFC99E]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                                العربية
                            </button>
                            <button type="button"
                                    @click="msgLang = 'en'"
                                    :class="msgLang === 'en' ? 'bg-[#151210] text-[#DFC99E]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                                English
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="wa-share-message-preview" class="sr-only">
                            {{ $isEn ? 'WhatsApp Share Message' : 'نص رسالة واتساب' }}
                        </label>
                        <textarea id="wa-share-message-preview"
                                  readonly
                                  rows="8"
                                  :dir="msgLang === 'en' ? 'ltr' : 'rtl'"
                                  x-model="currentMessage"
                                  class="w-full rounded-xl border border-[#E4D9C8] bg-[#FCFBF8] p-4 text-xs sm:text-sm text-[#221E1A] leading-relaxed focus:outline-none focus:border-[#C6A76B] resize-none"></textarea>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="{{ $shareCard['messages'][$defaultMsgLang]['whatsapp_url'] }}"
                           :href="currentWhatsAppUrl"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 py-2.5 rounded-xl bg-[#0F4C3A] hover:bg-[#0B3A2C] text-white font-extrabold text-xs sm:text-sm transition">
                            <x-app-icon name="whatsapp" class="w-4.5 h-4.5" />
                            <span>{{ $isEn ? 'Open WhatsApp with Message & Link' : 'فتح واتساب بالرسالة والرابط' }}</span>
                        </a>

                        <button type="button"
                                @click="copyText(currentMessage, 'copiedMsg')"
                                class="btn-obsidian !min-h-[44px] !py-2.5 !px-4 !text-xs cursor-pointer">
                            <x-app-icon name="copy" class="w-4 h-4 text-[#DFC99E]" x-show="!copiedMsg" />
                            <x-app-icon name="check" class="w-4 h-4 text-emerald-300" x-show="copiedMsg" x-cloak />
                            <span x-text="copiedMsg ? '{{ $isEn ? 'Message Copied!' : 'تم نسخ الرسالة والرابط!' }}' : '{{ $isEn ? 'Copy Message & Link' : 'نسخ الرسالة والرابط' }}'"></span>
                        </button>

                        <button type="button"
                                @click="copyText(payload.invitation.public_url, 'copiedLink')"
                                class="btn-ghost-ivory !min-h-[44px] !py-2.5 !px-4 !text-xs cursor-pointer">
                            <x-app-icon name="external-link" class="w-4 h-4 text-[#856326]" x-show="!copiedLink" />
                            <x-app-icon name="check" class="w-4 h-4 text-[#0F4C3A]" x-show="copiedLink" x-cloak />
                            <span x-text="copiedLink ? '{{ $isEn ? 'Link Copied!' : 'تم نسخ الرابط!' }}' : '{{ $isEn ? 'Copy Link Only' : 'نسخ الرابط فقط' }}'"></span>
                        </button>
                    </div>

                    <!-- Honest Web UX Note -->
                    <div class="p-3.5 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] flex items-start gap-2.5 text-xs text-[#52493F] leading-relaxed">
                        <x-app-icon name="info" class="w-4 h-4 text-[#856326] shrink-0 mt-0.5" />
                        <p>
                            {{ $isEn
                                ? 'Note: Web browsers cannot attach an image file automatically inside a WhatsApp URL link. Simply download the share image in Step 1, attach it in WhatsApp, and paste or send the message + live link together.'
                                : 'تنبيه عملي: لا تسمح المتصفحات بإرفاق ملف صورة تلقائياً داخل رابط واتساب المباشر؛ لذلك حمّل الصورة من الخطوة ١ وأرفقها في محادثة واتساب مع الرسالة والرابط التفاعلي الجاهز.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
