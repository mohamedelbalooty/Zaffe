<x-layouts.marketing :title="'الدعوة غير منشورة بعد — زفّة'">
    <div class="min-h-[70vh] flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center bg-white rounded-3xl p-8 sm:p-10 shadow-wedding border border-[#E8DED0] space-y-5">
            <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
                <x-app-icon name="lock" class="w-8 h-8" />
            </div>

            <h1 class="font-cairo text-2xl font-bold text-[#292521]">هذه الدعوة ما زالت قيد الإعداد</h1>
            
            <p class="text-sm text-[#292521]/70 leading-relaxed">
                يقوم العروسان حالياً بوضع اللمسات الأخيرة على بطاقة زفافهما. يرجى مراجعة الرابط لاحقاً بعد النشر الرسمي!
            </p>

            <div class="pt-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#292521] text-white text-xs font-bold hover:bg-[#3D3731] transition">
                    <span>العودة إلى الصفحة الرئيسية</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.marketing>
