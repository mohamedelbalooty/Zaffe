<x-layouts.marketing :title="'باقات الأسعار — زفّة'">
    <div class="bg-[#FAF7F2] py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30 inline-block">
                    باقات الأسعار — إطلاق مجاني ١٠٠٪
                </span>
                <h1 class="font-cairo text-3xl sm:text-5xl font-extrabold text-[#292521] mt-3">
                    احتفل بليلة العمر مع تجربة زفّة الكاملة مجاناً
                </h1>
                <p class="text-base text-[#292521]/70 mt-3 leading-relaxed">
                    بمناسبة الإطلاق الرسمي لمنصة زفّة في مصر والمملكة العربية السعودية، نهدي جميع العرسان وصولاً كاملاً وغير محدود لكافة المميزات والقوالب الملكية مجاناً دون أي رسوم أو بطاقة ائتمان.
                </p>
            </div>

            <!-- 100% Free Launch Banner -->
            <div class="max-w-4xl mx-auto mb-10 rounded-3xl p-6 sm:p-8 bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white shadow-xl border border-emerald-500/40 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center sm:text-right">
                    <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-bold text-emerald-100">
                        <x-app-icon name="sparkles" class="w-4 h-4 text-[#C6A76B]" />
                        <span>عرض خاص للإصدار الأول (Release 1)</span>
                    </div>
                    <h2 class="font-cairo text-xl sm:text-2xl font-extrabold">
                        هدية الإطلاق المجاني ١٠٠٪ في مصر والسعودية
                    </h2>
                    <p class="text-xs sm:text-sm text-emerald-50/90 leading-relaxed max-w-2xl">
                        جميع القوالب الستة الفاخرة، تخصيص الألوان والخطوط، موسيقى الخلفية، تأكيدات الحضور غير المحدودة (RSVP)، وتصدير قوائم الضيوف إلى Excel/CSV متاحة الآن مجاناً بالكامل دون الحاجة لبطاقة بنكية.
                    </p>
                </div>
                <a href="{{ auth()->check() ? route('dashboard.invitations.create') : route('register') }}"
                   class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-lg hover:opacity-95 transition whitespace-nowrap flex items-center gap-2 shrink-0">
                    <span>ابدأ تصميم دعوتك مجاناً الآن</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4" />
                </a>
            </div>

            <!-- All-Access Release 1 Free Card -->
            <div class="max-w-2xl mx-auto">
                <div class="rounded-3xl p-8 sm:p-10 transition-all flex flex-col justify-between relative bg-[#292521] text-white shadow-2xl border-2 border-[#C6A76B]">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white px-4 py-1 rounded-full text-xs font-black tracking-wider shadow-md flex items-center gap-1.5 whitespace-nowrap">
                        <x-app-icon name="crown" class="w-3.5 h-3.5" />
                        <span>جميع المميزات مفتوحة مجاناً في الإصدار الأول</span>
                    </div>

                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-cairo text-2xl font-bold text-white">
                                    الباقة الملكية الشاملة (الإطلاق المجاني)
                                </h3>
                                <p class="text-xs text-[#E8DED0]/75 mt-1">
                                    تجربة زفّة المتكاملة لعرسان مصر والسعودية بدون أي قيود أو اشتراكات
                                </p>
                            </div>

                            <div class="flex items-baseline gap-2 shrink-0">
                                <span class="font-cairo text-4xl sm:text-5xl font-extrabold text-[#C6A76B]">
                                    مجاناً
                                </span>
                                <span class="text-xs font-bold text-emerald-300 bg-emerald-500/20 px-2.5 py-1 rounded-full border border-emerald-400/30">
                                    0 ج.م / ر.س
                                </span>
                            </div>
                        </div>

                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-sm pt-6 border-t border-white/10 text-[#E8DED0]/90">
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>جميع القوالب الستة الفاخرة مفتوحة مجاناً</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>تخصيص كامل للألوان والخطوط العربية</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>إضافة موسيقى خلفية وألبوم صور تفاعلي</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>تأكيدات حضور RSVP غير محدودة</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>تصدير قائمة الضيوف الآمن إلى Excel / CSV</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center shrink-0">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                </div>
                                <span>دعم توقيت القاهرة والرياض ومشاركة واتساب</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-8">
                        <a href="{{ auth()->check() ? route('dashboard.invitations.create') : route('register') }}"
                           class="w-full py-4 px-6 rounded-2xl font-bold text-center text-sm shadow-md transition flex items-center justify-center gap-2 bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white hover:opacity-95">
                            <span>ابدأ تصميم دعوتك مجاناً الآن</span>
                            <x-app-icon name="arrow-left" class="w-4 h-4" />
                        </a>
                        <p class="text-[11px] text-center text-[#E8DED0]/60 mt-3">
                            لا يتطلب بطاقة ائتمان • حساب مجاني خلال ٣٠ ثانية
                        </p>
                    </div>
                </div>
            </div>

            <!-- Guarantee Box -->
            <div class="max-w-xl mx-auto mt-12 p-6 rounded-2xl bg-white border border-[#C6A76B]/30 text-center text-xs text-[#292521]/70 flex items-center justify-center gap-3">
                <x-app-icon name="shield-check" class="w-6 h-6 text-[#C6A76B] shrink-0" />
                <span>دعم مجاني كامل: إذا احتجت لأي مساعدة في تنسيق دعوتك أو مشاركتها، فريق زفّة متاح عبر واتساب لخدمتك في مصر والسعودية.</span>
            </div>
        </div>
    </div>
</x-layouts.marketing>
