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
@endphp

<div class="rounded-3xl p-6 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center space-y-4 border transition-all"
     style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};"
     x-data="{ copied: false }">
    <h4 class="text-sm font-bold" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.92;" : 'opacity: 0.85;' }}">
        شارك هذه الدعوة مع الأهل والأصدقاء
    </h4>
    <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0F4C3A] text-white text-xs font-bold shadow-xs hover:bg-[#0B3A2C] transition">
            <x-app-icon name="whatsapp" class="w-4 h-4 shrink-0" />
            <span>مشاركة عبر واتساب</span>
        </a>

        <button type="button"
                @click="navigator.clipboard.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-bold shadow-xs transition cursor-pointer {{ $copyBtnClasses }}">
            <x-app-icon name="copy" x-show="!copied" class="w-4 h-4 text-[#C6A76B] shrink-0" />
            <x-app-icon name="check" x-show="copied" x-cloak class="w-4 h-4 text-emerald-500 shrink-0" />
            <span x-text="copied ? 'تم نسخ الرابط بنجاح' : 'نسخ رابط الدعوة'"></span>
        </button>
    </div>
</div>
