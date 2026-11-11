<x-layouts.marketing :title="'تواصل معنا — زفّة'">
    <div class="bg-[#FAF7F2] py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    نحن هنا للمساعدة
                </span>
                <h1 class="font-cairo text-3xl sm:text-5xl font-extrabold text-[#292521]">
                    فريق زفّــة يسعد بالاستماع إليك
                </h1>
                <p class="text-base text-[#292521]/70 max-w-xl mx-auto">
                    سواء كان لديك استفسار حول تخصيص دعوتك أو أردت تصميماً خاصاً لحفلك، تواصل معنا مباشرة
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Direct channels -->
                <div class="bg-white rounded-3xl p-8 shadow-wedding border border-[#C6A76B]/25 space-y-6">
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">قنوات التواصل الفوري</h3>
                    
                    <a href="https://wa.me/201000000000" target="_blank" 
                       class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 hover:bg-emerald-100 transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <x-app-icon name="whatsapp" class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-emerald-700 block">واتساب مخصص للمساعدة</span>
                            <span class="text-base font-bold" dir="ltr">+20 100 000 0000</span>
                        </div>
                    </a>

                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                        <div class="w-12 h-12 rounded-xl bg-[#C6A76B] text-white flex items-center justify-center shrink-0">
                            <x-app-icon name="mail" class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-[#292521]/60 block">البريد الإلكتروني</span>
                            <span class="text-base font-bold text-[#292521]">support@zaffe.app</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                        <div class="w-12 h-12 rounded-xl bg-[#292521] text-white flex items-center justify-center shrink-0">
                            <x-app-icon name="map-pin" class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-[#292521]/60 block">المقر الرئيسي</span>
                            <span class="text-sm font-bold text-[#292521]">القاهرة، جمهورية مصر العربية</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white rounded-3xl p-8 shadow-wedding border border-[#E8DED0] space-y-5" x-data="{ submitted: false }">
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">أرسل لنا رسالة سريعة</h3>
                    
                    <div x-show="submitted" class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center space-y-3" style="display: none;">
                        <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto text-xl">
                            ✓
                        </div>
                        <h4 class="font-bold text-base">تم إرسال رسالتك بنجاح!</h4>
                        <p class="text-xs leading-relaxed">
                            شكراً لتواصلك مع زفّــة. يقوم فريق الدعم بمراجعة استفسارك وسنعاود التواصل معك عبر الواتساب أو البريد الإلكتروني في أسرع وقت.
                        </p>
                    </div>

                    <form x-show="!submitted" @submit.prevent="submitted = true" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[#292521] mb-1">الاسم الكامل</label>
                            <input type="text" required placeholder="أحمد محمود" class="w-full px-4 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 outline-none focus:border-[#C6A76B]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#292521] mb-1">رقم الهاتف / الواتساب</label>
                            <input type="text" required placeholder="+20 100 000 0000" dir="ltr" class="w-full px-4 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 outline-none focus:border-[#C6A76B] text-right">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#292521] mb-1">الرسالة أو الاستفسار</label>
                            <textarea rows="4" required placeholder="كيف يمكننا مساعدتك في تجهيز دعوة زفافك؟" class="w-full px-4 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 outline-none focus:border-[#C6A76B]"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:opacity-95 transition">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
