@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.marketing :title="$isEn ? 'Invitation Under Preparation — Zaffe' : 'الدعوة غير منشورة بعد — زفّة'">
    <div class="min-h-[65vh] flex items-center justify-center p-4 bg-[#FAF7F2]">
        <div class="max-w-md w-full text-center card-atelier p-8 sm:p-10 space-y-5">
            <div class="w-14 h-14 rounded-2xl bg-[#F4EBD9] text-[#856326] border border-[#C6A76B]/30 flex items-center justify-center mx-auto">
                <x-app-icon name="lock" class="w-6 h-6" />
            </div>

            <h1 class="font-cairo text-2xl font-extrabold text-[#221E1A]">
                {{ $isEn ? 'This Invitation Is Still Being Prepared' : 'هذه الدعوة ما زالت قيد الإعداد' }}
            </h1>

            <p class="text-sm text-[#52493F] leading-relaxed">
                {{ $isEn
                    ? 'The couple is still putting final touches on their wedding card. Please check back once the invitation is published.'
                    : 'يقوم العروسان حالياً بوضع اللمسات الأخيرة على بطاقة زفافهما. يرجى مراجعة الرابط لاحقاً بعد النشر الرسمي.' }}
            </p>

            <div class="pt-2">
                <a href="{{ route('home') }}" class="btn-obsidian">
                    {{ $isEn ? 'Back to Homepage' : 'العودة إلى الصفحة الرئيسية' }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.marketing>
