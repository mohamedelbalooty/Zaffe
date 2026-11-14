@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
])

@php
    $shareUrl = $invitation->public_url ?? url('/i/' . ($invitation->code ?? $invitation->slug ?? ''));
    $whatsappUrl = method_exists($invitation, 'getWhatsAppShareUrl')
        ? $invitation->getWhatsAppShareUrl()
        : ('https://api.whatsapp.com/send?text=' . rawurlencode($shareUrl));
@endphp

<div class="rounded-3xl p-6 shadow-xl text-center space-y-4 border transition-all"
     style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;"
     x-data="{ copied: false }">
    <h4 class="text-sm font-bold opacity-80">شارك هذه الدعوة مع الأهل والأصدقاء</h4>
    <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="{{ $whatsappUrl }}" target="_blank"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-md hover:bg-emerald-700 transition">
            <x-app-icon name="whatsapp" class="w-4 h-4" />
            <span>مشاركة عبر واتساب</span>
        </a>

        <button @click="navigator.clipboard.writeText('{{ $shareUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#292521] text-white text-xs font-bold shadow-md hover:bg-[#3D3731] transition">
            <x-app-icon name="copy" class="w-4 h-4 text-[#C6A76B]" />
            <span x-text="copied ? 'تم نسخ الرابط! ✓' : 'نسخ رابط الدعوة'"></span>
        </button>
    </div>
</div>
