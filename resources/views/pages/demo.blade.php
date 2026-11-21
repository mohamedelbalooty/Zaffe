@php
    $isEn = app()->getLocale() === 'en';
    $description = 'نموذج حي لدعوة زفاف سعودية عبر منصة زفّة — تصميم تراث الدرعية بتقويم أم القرى الهجري وتوقيت الرياض.';
@endphp

<x-layouts.base
    :title="'نموذج دعوة زفاف سعودية حية — زفّة'"
    :description="$description"
    :og-image="asset('images/og-zaffe.jpg')">

    <!-- Subtle Demo Ribbon -->
    <div class="bg-[#161311] text-[#FAF7F2] border-b border-[#C6A76B]/35 py-2.5 px-4 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <x-app-icon name="crown" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                <span class="font-bold">
                    {{ $isEn ? 'Live Saudi Wedding Card Demo (Diriyah Heritage)' : 'نموذج حي لدعوة زفاف سعودية (تصميم تراث الدرعية)' }}
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#C6A76B] text-[#151210] font-extrabold hover:bg-[#DFC99E] transition">
                    <x-app-icon name="sparkles" class="w-3.5 h-3.5" />
                    <span>{{ $isEn ? 'Design Your Own Free' : 'صمّم دعوتك مجاناً' }}</span>
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-[#E4D9C8] font-bold transition">
                    <span>{{ $isEn ? 'Home' : 'الرئيسية' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stateless Saudi Signature Render -->
    @include('templates.diriyah_heritage', [
        'invitation' => $demoInvitation,
        'isPreview' => true,
    ])
</x-layouts.base>
