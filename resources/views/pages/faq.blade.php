<x-layouts.marketing :title="'الأسئلة الشائعة — زفّة'">
    <div class="bg-[#FAF7F2] py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    إجابات واضحة
                </span>
                <h1 class="font-cairo text-3xl sm:text-5xl font-extrabold text-[#292521]">
                    الأسئلة الأكثر شيوعاً
                </h1>
                <p class="text-base text-[#292521]/70 max-w-xl mx-auto">
                    إليك كل ما تود معرفته عن تصميم ومشاركة دعوات الزفاف الرقمية عبر منصة زفّة
                </p>
            </div>

            <div class="space-y-4" x-data="{ openFaq: 1 }">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-[#E8DED0] p-6 shadow-sm">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex items-center justify-between text-right font-cairo font-bold text-lg text-[#292521]">
                        <span>كيف يمكنني مشاركة دعوتي مع ضيوفي عبر واتساب؟</span>
                        <x-app-icon name="chevron-down" class="w-5 h-5 text-[#C6A76B] transition-transform" ::class="openFaq === 1 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="mt-4 text-sm text-[#292521]/75 leading-relaxed pt-3 border-t border-[#E8DED0]/60">
                        بمجرد نشر دعوتك، توفر لك المنصة زر "مشاركة عبر واتساب" يقوم تلقائياً بإنشاء نص دعوة رقيق مع رابط بطاقتك المميز، ويمكنك إرسالها لأي ضيف أو مجموعة بضغطة زر واحدة.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-[#E8DED0] p-6 shadow-sm">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex items-center justify-between text-right font-cairo font-bold text-lg text-[#292521]">
                        <span>هل يحتاج ضيوفي إلى تحميل أي تطبيق لفتح الدعوة؟</span>
                        <x-app-icon name="chevron-down" class="w-5 h-5 text-[#C6A76B] transition-transform" ::class="openFaq === 2 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="mt-4 text-sm text-[#292521]/75 leading-relaxed pt-3 border-t border-[#E8DED0]/60" style="display: none;">
                        إطلاقاً! تفتح الدعوة في متصفح أي هاتف ذكي (آيفون، أندرويد، أو كمبيوتر) بسرعة فائقة ودون الحاجة لأي تسجيل أو تحميل تطبيقات.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-[#E8DED0] p-6 shadow-sm">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex items-center justify-between text-right font-cairo font-bold text-lg text-[#292521]">
                        <span>كيف يعمل نظام تأكيد الحضور (RSVP)؟</span>
                        <x-app-icon name="chevron-down" class="w-5 h-5 text-[#C6A76B] transition-transform" ::class="openFaq === 3 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="mt-4 text-sm text-[#292521]/75 leading-relaxed pt-3 border-t border-[#E8DED0]/60" style="display: none;">
                        في أسفل بطاقتك، يجد الضيف نموذجاً سهلاً لتسجيل اسمه، وحالة حضوره (بحضور مؤكد أو اعتذار رقيق)، مع تحديد عدد المرافقين وكتابة تهنئة. تظهر جميع الردود فوراً في لوحة تحكمك ويمكنك تحميلها كملف Excel لتنسيق مقاعد القاعة وبوفيه الفرح.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-[#E8DED0] p-6 shadow-sm">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full flex items-center justify-between text-right font-cairo font-bold text-lg text-[#292521]">
                        <span>هل يمكنني تعديل بيانات الدعوة بعد نشرها ومشاركتها؟</span>
                        <x-app-icon name="chevron-down" class="w-5 h-5 text-[#C6A76B] transition-transform" ::class="openFaq === 4 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="mt-4 text-sm text-[#292521]/75 leading-relaxed pt-3 border-t border-[#E8DED0]/60" style="display: none;">
                        نعم بكل تأكيد! يمكنك في أي وقت الدخول إلى استوديو الدعوة في لوحة التحكم وتحديث المواعيد أو الموقع أو الصور، وستنعكس التعديلات فوراً على الرابط دون الحاجة لتغيير الرابط الذي أرسلته لضيوفك.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-[#E8DED0] p-6 shadow-sm">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full flex items-center justify-between text-right font-cairo font-bold text-lg text-[#292521]">
                        <span>هل تدعم الدعوات اللغة الإنجليزية بجانب العربية؟</span>
                        <x-app-icon name="chevron-down" class="w-5 h-5 text-[#C6A76B] transition-transform" ::class="openFaq === 5 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="mt-4 text-sm text-[#292521]/75 leading-relaxed pt-3 border-t border-[#E8DED0]/60" style="display: none;">
                        نعم، المنصة مصممة بأولوية عربية أصيلة مع دعم كامل للغة الإنجليزية (LTR) وخطوط لاتينية كلاسيكية مثل Playfair Display.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
