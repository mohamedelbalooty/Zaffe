@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'isDarkTheme' => false,
])

@php
    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $copyBtnClasses = $isDarkTheme
        ? 'bg-[#FAF7F2] text-[#151210] hover:bg-white'
        : 'bg-[#221E1A] text-white hover:bg-[#38322C]';
    $isOwner = auth()->check()
        && isset($invitation->id, $invitation->user_id)
        && (int) $invitation->id > 0
        && (int) auth()->id() === (int) $invitation->user_id;
    $invLocale = \App\Services\InvitationDateFormatter::resolveLocale($invitation);
    $isEnInv = $invLocale === 'en';
@endphp

<div class="rounded-3xl p-6 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center space-y-4 border transition-all"
     style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};"
     x-data="{
        copied: false,
        copyFailed: false,
        copyLink() {
            const url = @js($invitation->public_url);
            this.copyFailed = false;
            const markCopied = () => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            };
            const markFailed = () => {
                this.copyFailed = true;
                setTimeout(() => this.copyFailed = false, 3500);
            };
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                navigator.clipboard.writeText(url).then(markCopied).catch(() => {
                    if (!this.fallbackCopy(url, markCopied)) {
                        markFailed();
                    }
                });
                return;
            }
            if (!this.fallbackCopy(url, markCopied)) {
                markFailed();
            }
        },
        fallbackCopy(url, onDone) {
            try {
                const ta = document.createElement('textarea');
                ta.value = url;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                const ok = document.execCommand('copy');
                document.body.removeChild(ta);
                if (ok) {
                    onDone();
                    return true;
                }
            } catch (e) {}
            return false;
        }
     }">
    <h4 class="text-sm font-bold" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.92;" : 'opacity: 0.85;' }}">
        {{ $isEnInv ? 'Share this invitation with family and friends' : 'شارك هذه الدعوة مع الأهل والأصدقاء' }}
    </h4>
    <div class="flex flex-wrap items-center justify-center gap-3">
        @if($isOwner)
            <a href="{{ route('dashboard.invitations.share', $invitation->id) }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0F4C3A] text-white text-xs font-bold shadow-xs hover:bg-[#0B3A2C] transition">
                <x-app-icon name="whatsapp" class="w-4 h-4 shrink-0" />
                <span>{{ $isEnInv ? 'Share Invitation Image & Message on WhatsApp' : 'مشاركة صورة الدعوة والرسالة عبر واتساب' }}</span>
            </a>
        @else
            <a href="{{ $invitation->getWhatsAppShareUrl($invLocale) }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0F4C3A] text-white text-xs font-bold shadow-xs hover:bg-[#0B3A2C] transition">
                <x-app-icon name="whatsapp" class="w-4 h-4 shrink-0" />
                <span>{{ $isEnInv ? 'Share Invitation Link via WhatsApp' : 'مشاركة رابط الدعوة عبر واتساب' }}</span>
            </a>
        @endif

        <button type="button"
                @click="copyLink()"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-bold shadow-xs transition cursor-pointer {{ $copyBtnClasses }}">
            <x-app-icon name="copy" x-show="!copied" class="w-4 h-4 text-[#C6A76B] shrink-0" />
            <x-app-icon name="check" x-show="copied" x-cloak class="w-4 h-4 text-emerald-500 shrink-0" />
            <span x-text="copied ? @js($isEnInv ? 'Invitation link copied' : 'تم نسخ رابط الدعوة') : @js($isEnInv ? 'Copy Invitation Link' : 'نسخ رابط الدعوة')"></span>
        </button>
    </div>

    <p x-show="copyFailed" x-cloak class="text-xs font-semibold text-rose-600">
        {{ $isEnInv ? 'Could not copy automatically. Please copy the URL from your browser address bar.' : 'تعذر النسخ التلقائي؛ يرجى نسخ الرابط من شريط العنوان في المتصفح.' }}
    </p>
</div>
