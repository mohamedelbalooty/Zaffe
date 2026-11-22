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
            nativeShareMode: 'unsupported',
            pngBlob: null,
            pngFile: null,
            shareState: 'idle',
            clipboardState: 'idle',
            downloadState: 'idle',
            get canNativeFileShare() {
                return this.nativeShareMode === 'files_and_text' || this.nativeShareMode === 'files_only';
            },
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
            detectNativeShareSupport() {
                if (typeof navigator === 'undefined' || typeof navigator.share !== 'function' || typeof navigator.canShare !== 'function' || !this.pngFile) {
                    this.nativeShareMode = 'unsupported';
                    return;
                }
                try {
                    if (navigator.canShare({
                        files: [this.pngFile],
                        title: this.payload.invitation.title,
                        text: this.currentMessage,
                    })) {
                        this.nativeShareMode = 'files_and_text';
                        return;
                    }
                } catch (e) {}
                try {
                    if (navigator.canShare({ files: [this.pngFile] })) {
                        this.nativeShareMode = 'files_only';
                        return;
                    }
                } catch (e) {}
                this.nativeShareMode = 'unsupported';
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
                    this.pngFile = (typeof File !== 'undefined' && this.pngBlob)
                        ? new File([this.pngBlob], this.payload.filename, { type: 'image/png' })
                        : null;
                    this.detectNativeShareSupport();
                } catch (e) {
                    this.renderError = true;
                } finally {
                    this.rendering = false;
                }
            },
            async copyToClipboard(value, successState) {
                this.clipboardState = 'idle';
                let copied = false;
                if (typeof navigator !== 'undefined' && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                    try {
                        await navigator.clipboard.writeText(value);
                        copied = true;
                    } catch (e) {
                        copied = false;
                    }
                }
                if (!copied) {
                    copied = this.fallbackCopy(value);
                }
                this.clipboardState = copied ? successState : 'failed';
                setTimeout(() => {
                    if (this.clipboardState === successState) {
                        this.clipboardState = 'idle';
                    }
                }, 3500);
                return copied;
            },
            fallbackCopy(value) {
                try {
                    const ta = document.createElement('textarea');
                    ta.value = value;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    const ok = Boolean(document.execCommand('copy'));
                    document.body.removeChild(ta);
                    return ok;
                } catch (e) {
                    return false;
                }
            },
            async downloadImage() {
                this.downloadState = 'idle';
                try {
                    if (!this.pngBlob && this.$refs.shareCanvas && window.ZaffeShareCard) {
                        this.pngBlob = await window.ZaffeShareCard.toBlob(this.$refs.shareCanvas);
                    }
                    if (!this.pngBlob) {
                        this.downloadState = 'failed';
                        return;
                    }
                    const url = URL.createObjectURL(this.pngBlob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = this.payload.filename || 'zaffe-share-card.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                    this.downloadState = 'downloaded';
                    setTimeout(() => {
                        if (this.downloadState === 'downloaded') {
                            this.downloadState = 'idle';
                        }
                    }, 3500);
                } catch (e) {
                    this.downloadState = 'failed';
                }
            },
            async shareNative() {
                this.detectNativeShareSupport();
                if (!this.canNativeFileShare || !this.pngFile) {
                    this.shareState = 'unsupported';
                    return;
                }
                const shareData = this.nativeShareMode === 'files_and_text'
                    ? {
                        files: [this.pngFile],
                        title: this.payload.invitation.title,
                        text: this.currentMessage,
                    }
                    : {
                        files: [this.pngFile],
                    };
                this.shareState = 'sharing';
                try {
                    await navigator.share(shareData);
                    this.shareState = 'handoff_completed';
                } catch (err) {
                    if (err && err.name === 'AbortError') {
                        this.shareState = 'canceled';
                    } else if (err && (err.name === 'NotAllowedError' || err.name === 'TypeError')) {
                        this.shareState = 'unsupported';
                    } else {
                        this.shareState = 'error';
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

                <!-- Primary Native Mobile Share Card (Only shown when browser supports navigator.canShare with files) -->
                <div x-show="canNativeFileShare" x-cloak
                     class="card-atelier p-5 sm:p-6 border-[#0F4C3A]/35 bg-[#ECF5F0]/55 space-y-3.5">
                    <div class="space-y-1">
                        <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#0F4C3A] flex items-center gap-2">
                            <x-app-icon name="whatsapp" class="w-5 h-5 shrink-0" />
                            <span>{{ $isEn ? 'Share Invitation Image & Message' : 'مشاركة صورة الدعوة والرسالة عبر واتساب' }}</span>
                        </h3>
                        <p class="text-xs text-[#221E1A]/85 leading-relaxed">
                            <span x-show="nativeShareMode === 'files_and_text'">
                                {{ $isEn
                                    ? 'Opens your device share sheet with the 1080×1350 image, invitation message, and live link in one action. Some mobile WhatsApp versions omit text alongside an image—if empty, copy & paste the caption below.'
                                    : 'يفتح نافذة المشاركة على جهازك بصورة الدعوة والرسالة والرابط معاً في خطوة واحدة. قد لا تحتفظ بعض إصدارات واتساب بالنص تلقائياً مع الصورة؛ وفي هذه الحالة يمكنك نسخه ولصقه فوراً من الخيارات أدناه.' }}
                            </span>
                            <span x-show="nativeShareMode === 'files_only'" x-cloak>
                                {{ $isEn
                                    ? 'This browser supports sharing the image file to WhatsApp, while caption text must be copied below and pasted under the image.'
                                    : 'يدعم هذا المتصفح مشاركة ملف الصورة مباشرة، بينما يلزم نسخ الرسالة والرابط أدناه ولصقهما في خانة التعليق أسفل الصورة في واتساب.' }}
                            </span>
                        </p>
                    </div>

                    <button type="button"
                            @click="shareNative()"
                            :disabled="rendering || renderError || shareState === 'sharing'"
                            class="w-full min-h-[46px] py-2.5 px-4 rounded-xl bg-[#0F4C3A] hover:bg-[#0B3A2C] text-white font-extrabold text-xs sm:text-sm transition inline-flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <x-app-icon name="share" class="w-4.5 h-4.5" />
                        <span x-text="shareState === 'sharing' ? @js($isEn ? 'Opening share sheet…' : 'جاري فتح نافذة المشاركة…') : @js($isEn ? 'Share Image + Message + Link' : 'مشاركة صورة الدعوة مع الرسالة والرابط')"></span>
                    </button>

                    <!-- Honest Share State Banners -->
                    <div x-show="shareState === 'handoff_completed'" x-cloak
                         class="p-3 rounded-xl bg-white border border-[#0F4C3A]/30 text-xs text-[#0F4C3A] font-semibold leading-relaxed">
                        {{ $isEn
                            ? 'Share sheet handoff completed with the invitation image. Because WhatsApp on some devices strips text when attaching an image, please verify the caption appears in WhatsApp—or use "Copy Message + Link" below to paste it.'
                            : 'تم فتح نافذة المشاركة بالصورة. لأن بعض إصدارات واتساب لا تنقل النص تلقائياً عند إرفاق صورة، يرجى التأكد من ظهور التعليق داخل واتساب أو استخدام زر «نسخ الرسالة والرابط» أدناه ولصقه في خانة التعليق.' }}
                    </div>

                    <div x-show="shareState === 'canceled'" x-cloak
                         class="p-3 rounded-xl bg-amber-50 border border-amber-300/70 text-xs text-amber-900 font-semibold leading-relaxed">
                        {{ $isEn
                            ? 'Share dialog was canceled. You can try again or use Download Image + Copy Message below.'
                            : 'تم إلغاء نافذة المشاركة. يمكنك المحاولة مرة أخرى أو استخدام التحميل والنسخ أدناه.' }}
                    </div>

                    <div x-show="shareState === 'error' || shareState === 'unsupported'" x-cloak
                         class="p-3 rounded-xl bg-amber-50 border border-amber-300/70 text-xs text-amber-900 font-semibold leading-relaxed">
                        {{ $isEn
                            ? 'This browser cannot complete native file + caption sharing directly. Please use Download Invitation Image + Copy Message & Link below.'
                            : 'تعذر إتمام المشاركة المباشرة بالصورة والنص في هذا المتصفح. يرجى استخدام خطوات التحميل والنسخ الموثوقة أدناه.' }}
                    </div>
                </div>

                <!-- Browser Notice when Native File Share is Unsupported (e.g., Desktop Chrome / WhatsApp Web) -->
                <div x-show="!canNativeFileShare && !rendering" x-cloak
                     class="p-4 rounded-2xl bg-[#F4EBD9]/70 border border-[#C6A76B]/40 flex items-start gap-3 text-xs text-[#221E1A] leading-relaxed">
                    <x-app-icon name="info" class="w-4.5 h-4.5 text-[#856326] shrink-0 mt-0.5" />
                    <p>
                        {{ $isEn
                            ? 'Desktop & unsupported web browsers cannot send an image file + caption directly into WhatsApp in one click. Use the two controls below to attach the PNG card and paste your message + live invitation link as its WhatsApp caption.'
                            : 'لا تدعم متصفحات سطح المكتب (أو هذا المتصفح) إرسال ملف الصورة مع التعليق داخل واتساب بضغطة واحدة؛ استخدم الخطوات الموثوقة أدناه لإرفاق الصورة ولصق الرسالة والرابط في خانة التعليق.' }}
                    </p>
                </div>

                <!-- Reliable Fallback: Download Image + Copy Message & Link as Caption -->
                <div class="card-atelier p-5 sm:p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A]">
                                {{ $isEn ? 'Share Image + Caption reliably in WhatsApp' : 'مشاركة صورة الدعوة مع الرسالة والرابط (الطريقة الموثوقة)' }}
                            </h3>
                            <p class="text-xs text-[#52493F]">
                                {{ $isEn
                                    ? 'Keeps the 1080×1350 card image, full event text, and live URL together in one WhatsApp media message.'
                                    : 'تضمن وصول صورة الدعوة بمقاس (4:5) مع الرسالة الرسمية ورابط الدعوة التفاعلية في رسالة واتساب واحدة.' }}
                            </p>
                        </div>

                        <!-- Message Locale Toggle -->
                        <div class="inline-flex items-center bg-[#FAF7F2] p-1 rounded-xl border border-[#E4D9C8] shrink-0 self-start sm:self-center">
                            <button type="button"
                                    @click="msgLang = 'ar'; detectNativeShareSupport()"
                                    :class="msgLang === 'ar' ? 'bg-[#151210] text-[#DFC99E]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                                العربية
                            </button>
                            <button type="button"
                                    @click="msgLang = 'en'; detectNativeShareSupport()"
                                    :class="msgLang === 'en' ? 'bg-[#151210] text-[#DFC99E]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                                English
                            </button>
                        </div>
                    </div>

                    <!-- Concise 3-Step Caption Workflow Strip -->
                    <ol class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                        <li class="p-3 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-[#151210] text-[#DFC99E] font-cairo font-black text-[11px] flex items-center justify-center shrink-0">1</span>
                            <span class="font-bold text-[#221E1A]">{{ $isEn ? 'Download card image (PNG)' : 'حمّل صورة الدعوة (PNG)' }}</span>
                        </li>
                        <li class="p-3 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-[#151210] text-[#DFC99E] font-cairo font-black text-[11px] flex items-center justify-center shrink-0">2</span>
                            <span class="font-bold text-[#221E1A]">{{ $isEn ? 'Copy message + link' : 'انسخ الرسالة والرابط' }}</span>
                        </li>
                        <li class="p-3 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-[#0F4C3A] text-white font-cairo font-black text-[11px] flex items-center justify-center shrink-0">3</span>
                            <span class="font-bold text-[#221E1A]">{{ $isEn ? 'Attach image in WhatsApp & paste caption' : 'أرفق الصورة بواتساب والصق النص كتعليق' }}</span>
                        </li>
                    </ol>

                    <!-- Primary Fallback Actions: Download PNG & Copy Message + Link -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button type="button"
                                @click="downloadImage()"
                                :disabled="rendering || renderError"
                                class="btn-royal w-full cursor-pointer disabled:opacity-50">
                            <x-app-icon name="download" class="w-4.5 h-4.5" />
                            <span>{{ $isEn ? 'Download Invitation Image' : 'تحميل صورة الدعوة (PNG)' }}</span>
                        </button>

                        <button type="button"
                                @click="copyToClipboard(currentMessage, 'copied_message')"
                                class="btn-obsidian w-full cursor-pointer">
                            <x-app-icon name="copy" class="w-4 h-4 text-[#DFC99E]" x-show="clipboardState !== 'copied_message'" />
                            <x-app-icon name="check" class="w-4 h-4 text-emerald-300" x-show="clipboardState === 'copied_message'" x-cloak />
                            <span x-text="clipboardState === 'copied_message' ? @js($isEn ? 'Caption Copied to Clipboard' : 'تم نسخ الرسالة والرابط للحافظة') : @js($isEn ? 'Copy Message + Link' : 'نسخ الرسالة والرابط')"></span>
                        </button>
                    </div>

                    <!-- Explicit Download / Clipboard Feedback -->
                    <div x-show="downloadState === 'downloaded' || downloadState === 'failed' || clipboardState === 'copied_message' || clipboardState === 'copied_link' || clipboardState === 'failed'"
                         x-cloak
                         class="space-y-1.5 text-xs font-bold">
                        <p x-show="downloadState === 'downloaded'" class="text-[#0F4C3A] flex items-center gap-1.5">
                            <x-app-icon name="check-circle" class="w-4 h-4 shrink-0" />
                            <span>{{ $isEn ? 'PNG image downloaded to your device. Attach it in WhatsApp next.' : 'تم تحميل ملف الصورة (PNG) على جهازك. أرفقها الآن في محادثة واتساب.' }}</span>
                        </p>
                        <p x-show="downloadState === 'failed'" class="text-rose-700">
                            {{ $isEn ? 'Could not export the PNG image. Please retry rendering the card.' : 'تعذر تصدير ملف الصورة؛ يرجى إعادة المحاولة.' }}
                        </p>
                        <p x-show="clipboardState === 'copied_message'" class="text-[#0F4C3A] flex items-center gap-1.5">
                            <x-app-icon name="check-circle" class="w-4 h-4 shrink-0" />
                            <span>{{ $isEn ? 'Message + live URL copied to clipboard. Paste it into the WhatsApp image caption field.' : 'تم نسخ الرسالة ورابط الدعوة للحافظة؛ الصقهما الآن في خانة التعليق أسفل الصورة في واتساب.' }}</span>
                        </p>
                        <p x-show="clipboardState === 'copied_link'" class="text-[#0F4C3A] flex items-center gap-1.5">
                            <x-app-icon name="check-circle" class="w-4 h-4 shrink-0" />
                            <span>{{ $isEn ? 'Invitation link copied to clipboard.' : 'تم نسخ رابط الدعوة إلى الحافظة.' }}</span>
                        </p>
                        <p x-show="clipboardState === 'failed'" class="text-rose-700">
                            {{ $isEn ? 'Clipboard permission denied in this browser. Select and copy the text manually from the box below.' : 'تعذر النسخ التلقائي لعدم توفر صلاحية الحافظة؛ حدد النص من المربع أدناه وانسخه يدوياً.' }}
                        </p>
                    </div>

                    <!-- Selectable Caption Preview -->
                    <div>
                        <label for="wa-share-message-preview" class="block text-xs font-bold text-[#52493F] mb-1.5">
                            {{ $isEn ? 'Prepared Image Caption (Message + Live Link):' : 'النص الجاهز للتعليق أسفل الصورة (الرسالة + الرابط):' }}
                        </label>
                        <textarea id="wa-share-message-preview"
                                  readonly
                                  rows="8"
                                  onclick="this.select()"
                                  :dir="msgLang === 'en' ? 'ltr' : 'rtl'"
                                  x-model="currentMessage"
                                  class="w-full rounded-xl border border-[#E4D9C8] bg-[#FCFBF8] p-4 text-xs sm:text-sm text-[#221E1A] leading-relaxed focus:outline-none focus:border-[#C6A76B] resize-none"></textarea>
                    </div>

                    <!-- Secondary Utilities (Explicitly Labeled) -->
                    <div class="pt-3 border-t border-[#E4D9C8] flex flex-wrap items-center justify-between gap-2.5">
                        <button type="button"
                                @click="copyToClipboard(payload.invitation.public_url, 'copied_link')"
                                class="btn-ghost-ivory !min-h-[38px] !py-1.5 !px-3.5 !text-xs cursor-pointer">
                            <x-app-icon name="copy" class="w-3.5 h-3.5 text-[#856326]" />
                            <span>{{ $isEn ? 'Copy Link Only' : 'نسخ الرابط فقط' }}</span>
                        </button>

                        <a href="{{ $shareCard['messages'][$defaultMsgLang]['whatsapp_url'] }}"
                           :href="currentWhatsAppUrl"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-[#52493F] hover:text-[#0F4C3A] underline underline-offset-4 transition">
                            <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                            <span>{{ $isEn ? 'Open WhatsApp (Text & Link Only — No Image)' : 'فتح واتساب بنص ورابط فقط (بدون صورة)' }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
