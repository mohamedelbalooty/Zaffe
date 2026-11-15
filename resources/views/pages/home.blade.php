<x-layouts.marketing :title="'زفّــة — اصنع دعوتك الخاصة واجعل يومك المميز لا يُنسى'">
    <!-- Hero Section -->
    <section class="relative pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden bg-luxury-grain border-b border-[#E8DED0]/70">
        <!-- Subtle Radial Gold & Rose Atmosphere -->
        <div class="absolute top-12 -right-24 w-[28rem] h-[28rem] rounded-full bg-[#C6A76B]/14 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-24 w-[26rem] h-[26rem] rounded-full bg-[#D9B7B0]/18 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">
                <!-- Editorial Copy & Primary CTAs -->
                <div class="lg:col-span-7 text-center lg:text-right space-y-6 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C6A76B]/12 border border-[#C6A76B]/35 text-[#8E6F36] text-xs font-bold shadow-sm">
                        <x-app-icon name="crown" class="w-4 h-4 text-[#C6A76B]" />
                        <span>المنصة الملكية لدعوات الزفاف الرقمية في المملكة العربية السعودية ومصر</span>
                    </div>

                    <h1 class="font-cairo text-4xl sm:text-5xl lg:text-[3.4rem] font-extrabold text-[#292521] leading-[1.22] tracking-tight">
                        اصنع دعوتك الخاصة <br class="hidden sm:inline">
                        <span class="text-gold-gradient font-bold">واجعل يومك المميز لا يُنسى</span>
                    </h1>

                    <p class="text-base sm:text-lg text-[#292521]/78 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        صمّم بطاقة زفاف رقمية فاخرة تليق بخصوصية مناسبتك دون الحاجة لأي خبرة تصميمية؛ مع بوابة فتح تفاعلية، التاريخ الهجري والميلادي، ومشاركة فورية عبر واتساب مع تأكيد حضور ضيوفك (RSVP) بدقة واحترافية.
                    </p>

                    <!-- CTAs -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('templates.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-full bg-gradient-to-r from-[#C6A76B] via-[#DFCA9B] to-[#B59556] text-[#1B1815] font-extrabold text-base shadow-wedding hover:shadow-wedding-hover transition-all transform hover:-translate-y-0.5">
                            <x-app-icon name="sparkles" class="w-5 h-5" />
                            <span>صمم دعوتك الآن مجاناً</span>
                        </a>

                        <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                           target="_blank"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-white text-[#292521] border border-[#C6A76B]/45 font-bold text-base shadow-sm hover:bg-[#FAF7F2] transition">
                            <x-app-icon name="eye" class="w-5 h-5 text-[#C6A76B]" />
                            <span>عاين دعوة حية مباشرة</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="pt-6 border-t border-[#E8DED0]/80 flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-3 text-xs text-[#292521]/75">
                        <div class="flex items-center gap-2 font-semibold">
                            <x-app-icon name="check-badge" class="w-4 h-4 text-[#C6A76B]" />
                            <span>توافق تام مع متصفح واتساب</span>
                        </div>
                        <div class="flex items-center gap-2 font-semibold">
                            <x-app-icon name="calendar" class="w-4 h-4 text-[#C6A76B]" />
                            <span>التقويم الهجري (أم القرى) والميلادي</span>
                        </div>
                        <div class="flex items-center gap-2 font-semibold">
                            <x-app-icon name="shield-check" class="w-4 h-4 text-[#C6A76B]" />
                            <span>خصوصية عائلية وإلزامية صور اختيارية</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Royal Invitation Phone Mockup (Zero External Images) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm" x-data="{ previewMode: 'card' }">
                        <!-- Golden Glow Aura -->
                        <div class="absolute -inset-2 bg-gradient-to-tr from-[#C6A76B]/45 via-[#DFC99E]/30 to-[#D9B7B0]/35 rounded-[2.75rem] blur-2xl opacity-75"></div>

                        <!-- Phone Frame Container -->
                        <div class="relative bg-white border-[3px] border-[#C6A76B]/45 rounded-[2.5rem] shadow-royal-card p-4 sm:p-5 overflow-hidden">
                            <!-- Interactive Mode Switcher Bar -->
                            <div class="flex items-center justify-between gap-2 pb-3.5 border-b border-[#E8DED0]">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-[#292521]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>معاينة حية</span>
                                </div>

                                <div class="inline-flex items-center p-0.5 rounded-full bg-[#FAF7F2] border border-[#E8DED0] text-[11px] font-bold">
                                    <button type="button"
                                            @click="previewMode = 'card'"
                                            :class="previewMode === 'card' ? 'bg-[#292521] text-[#FAF7F2] shadow-sm' : 'text-[#292521]/65 hover:text-[#292521]'"
                                            class="px-2.5 py-1 rounded-full transition">
                                        بطاقة الدعوة
                                    </button>
                                    <button type="button"
                                            @click="previewMode = 'gate'"
                                            :class="previewMode === 'gate' ? 'bg-[#C6A76B] text-[#1B1815] shadow-sm' : 'text-[#292521]/65 hover:text-[#292521]'"
                                            class="px-2.5 py-1 rounded-full transition">
                                        بوابة الفتح
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Mode 1: Royal Invitation Card (Calligraphic Monogram, Dual Hijri/Gregorian) -->
                            <div x-show="previewMode === 'card'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="mt-4 p-5 rounded-2xl bg-[#FAF7F2] border-royal-double text-center space-y-3.5">
                                <div class="font-amiri text-xs text-[#8E6F36] font-bold tracking-wide">
                                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                                </div>

                                <!-- Calligraphic Monogram Crest (No External Photos) -->
                                <div class="w-20 h-20 mx-auto rounded-full p-1 bg-gradient-to-tr from-[#C6A76B] via-[#E6D5B8] to-[#B59556] shadow-md">
                                    <div class="w-full h-full rounded-full bg-[#292521] border border-[#C6A76B]/50 flex flex-col items-center justify-center">
                                        <span class="font-amiri text-xl font-bold text-[#DFC99E] leading-none">ف &amp; ج</span>
                                        <span class="text-[9px] text-[#C6A76B]/80 tracking-widest mt-1">ZAFFE</span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold text-[#292521]/65">نتشرف بدعوتكم لحضور حفل زفاف</p>
                                    <div class="font-cairo text-xl font-black text-[#292521] tracking-tight">
                                        فيصل &amp; الجوهرة
                                    </div>
                                </div>

                                <!-- Dual Umm al-Qura Hijri & Gregorian Plaque -->
                                <div class="px-3 py-2.5 rounded-xl bg-white border border-[#C6A76B]/30 space-y-1">
                                    <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-[#8E6F36]">
                                        <x-app-icon name="calendar" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                        <bdi dir="rtl">١٤ ذو القعدة ١٤٤٨ هـ</bdi>
                                    </div>
                                    <div class="text-[11px] text-[#292521]/70 font-medium">
                                        <bdi dir="rtl">الموافق ٢٠ أبريل ٢٠٢٧ م • ٨:٠٠ مساءً</bdi>
                                    </div>
                                    <div class="text-[11px] font-bold text-[#292521] pt-0.5 flex items-center justify-center gap-1">
                                        <x-app-icon name="map-pin" class="w-3 h-3 text-[#C6A76B]" />
                                        <span>الريتز كارلتون الرياض • الفورسيزونز القاهرة</span>
                                    </div>
                                </div>

                                <!-- Mini Countdown -->
                                <div class="grid grid-cols-4 gap-1.5 text-center bg-white p-2 rounded-xl border border-[#E8DED0]">
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#8E6F36]">٤٥</span>
                                        <span class="text-[10px] text-[#292521]/60">يوم</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#8E6F36]">١٤</span>
                                        <span class="text-[10px] text-[#292521]/60">ساعة</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#8E6F36]">٣٢</span>
                                        <span class="text-[10px] text-[#292521]/60">دقيقة</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#8E6F36]">١٨</span>
                                        <span class="text-[10px] text-[#292521]/60">ثانية</span>
                                    </div>
                                </div>

                                <!-- Interactive RSVP Link -->
                                <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                                   target="_blank"
                                   class="w-full py-2.5 px-4 rounded-xl bg-[#292521] text-white text-xs font-bold flex items-center justify-center gap-2 hover:bg-[#3D3731] transition">
                                    <x-app-icon name="heart" class="w-4 h-4 text-[#C6A76B]" />
                                    <span>تأكيد الحضور (RSVP)</span>
                                </a>
                            </div>

                            <!-- Preview Mode 2: Royal Seal Open-Gate Screen (شاشة فتح الدعوة) -->
                            <div x-show="previewMode === 'gate'"
                                 style="display: none;"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="mt-4 p-6 rounded-2xl bg-obsidian-royal border border-[#C6A76B]/50 text-center text-[#FAF7F2] space-y-5">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A76B]/15 border border-[#C6A76B]/40 text-[#DFC99E] text-[10px] font-bold">
                                    <x-app-icon name="volume-2" class="w-3.5 h-3.5" />
                                    <span>شاشة فتح الدعوة بالزفّة الصوتية</span>
                                </div>

                                <div class="space-y-1.5 pt-1">
                                    <p class="font-amiri text-sm text-[#DFC99E]">دعوة زفاف خاصة</p>
                                    <h3 class="font-cairo text-2xl font-black text-white">فيصل &amp; الجوهرة</h3>
                                    <p class="text-[11px] text-[#E8DED0]/70">انقر على الختم الملكي لفتح البطاقة وسماع الزفّة</p>
                                </div>

                                <button type="button"
                                        @click="previewMode = 'card'"
                                        class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-[#B59556] via-[#DFC99E] to-[#C6A76B] text-[#1B1815] flex flex-col items-center justify-center gap-1 glow-gold-seal animate-seal-pulse hover:scale-105 transition cursor-pointer">
                                    <x-app-icon name="mail-open" class="w-7 h-7" />
                                    <span class="font-cairo text-[11px] font-black">افتح الدعوة</span>
                                </button>

                                <div class="pt-1 text-[11px] text-[#E8DED0]/65 flex items-center justify-center gap-2">
                                    <x-app-icon name="shield-check" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                    <span>يعمل بسلاسة داخل متصفح واتساب</span>
                                </div>
                            </div>

                            <!-- Mobile Frame Bottom Handle -->
                            <div class="mt-4 flex items-center justify-center">
                                <div class="w-24 h-1 bg-[#292521]/20 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flexible Invitation Modes & Competitor-Beating Guest Experience -->
    <section class="py-20 bg-white border-b border-[#E8DED0]/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-14">
                <div class="max-w-2xl space-y-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-extrabold tracking-wider text-[#8E6F36] bg-[#F4EBD9]/70 px-3.5 py-1 rounded-full border border-[#C6A76B]/30">
                        <x-app-icon name="gem" class="w-3.5 h-3.5 text-[#C6A76B]" />
                        <span>مصممة خصيصاً لذوق العائلة الخليجية والمصرية</span>
                    </span>
                    <h2 class="font-cairo text-3xl sm:text-4xl font-extrabold text-[#292521]">
                        أنماط دعوات مرنة وتجربة ضيوف استثنائية
                    </h2>
                    <p class="text-base text-[#292521]/70 leading-relaxed">
                        تمنحك زفّــة تحكماً كاملاً في طريقة استقبال ضيوفك — من بوابة الختم الملكي والخصوصية التامة بدون صور، إلى تأكيدات الحضور الفورية والتقويم الهجري المزدوج.
                    </p>
                </div>

                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#292521] text-white text-sm font-bold shadow-sm hover:bg-[#3D3731] transition shrink-0 self-start lg:self-auto">
                    <span>ابدأ تجربتك المجانية</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4 text-[#C6A76B]" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Mode 1: Interactive Open-Gate & Music -->
                <div class="p-7 rounded-3xl bg-[#FAF7F2] border border-[#C6A76B]/30 shadow-sm hover:shadow-wedding transition-all flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#292521] text-[#DFC99E] flex items-center justify-center shadow-sm">
                            <x-app-icon name="mail-open" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#292521]">بوابة فتح الدعوة والزفّة</h3>
                        <p class="text-sm text-[#292521]/75 leading-relaxed">
                            عند تفعيل الصوت، يبدأ الضيف بنقرة أنيقة على الختم الملكي لفتح الدعوة وتشغيل الزفّة بسلاسة تامة داخل متصفح واتساب.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#E8DED0] text-xs font-bold text-[#8E6F36] flex items-center gap-1.5">
                        <x-app-icon name="volume-2" class="w-4 h-4" />
                        <span>تحكم كامل بالتشغيل أو الكتم</span>
                    </div>
                </div>

                <!-- Mode 2: Smart RSVP & Companion Limits -->
                <div class="p-7 rounded-3xl bg-[#FAF7F2] border border-[#C6A76B]/30 shadow-sm hover:shadow-wedding transition-all flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#0F4C3A] text-white flex items-center justify-center shadow-sm">
                            <x-app-icon name="check-badge" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#292521]">تأكيد حضور واعتذار ذكي</h3>
                        <p class="text-sm text-[#292521]/75 leading-relaxed">
                            استقبل ردود الضيوف بالقبول أو الاعتذار مع تحديد الحد الأقصى للمرافقين، منع تكرار رقم الجوال، وتصدير السجل إلى Excel بضغطة زر.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#E8DED0] text-xs font-bold text-[#0F4C3A] flex items-center gap-1.5">
                        <x-app-icon name="users" class="w-4 h-4" />
                        <span>إحصاءات دقيقة للمؤكدين والمعتذرين</span>
                    </div>
                </div>

                <!-- Mode 3: Family Privacy by Default -->
                <div class="p-7 rounded-3xl bg-[#FAF7F2] border border-[#C6A76B]/30 shadow-sm hover:shadow-wedding transition-all flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/40">
                            <x-app-icon name="shield-check" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#292521]">خصوصية العائلة أولاً</h3>
                        <p class="text-sm text-[#292521]/75 leading-relaxed">
                            قوالب فاخرة تكتمل أناقتها دون الحاجة لرفع صور شخصية، مع دعم الأسماء العائلية (مثل: كريمة عائلة...) وحماية المسودات الخاصة.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#E8DED0] text-xs font-bold text-[#8E6F36] flex items-center gap-1.5">
                        <x-app-icon name="lock" class="w-4 h-4" />
                        <span>تصاميم راقية بلا صور إلزامية</span>
                    </div>
                </div>

                <!-- Mode 4: Umm al-Qura Hijri & Digit Choice -->
                <div class="p-7 rounded-3xl bg-[#FAF7F2] border border-[#C6A76B]/30 shadow-sm hover:shadow-wedding transition-all flex flex-col justify-between space-y-5">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#292521] text-[#C6A76B] flex items-center justify-center shadow-sm">
                            <x-app-icon name="calendar" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#292521]">هجري أم القرى وخرائط القاعة</h3>
                        <p class="text-sm text-[#292521]/75 leading-relaxed">
                            تحويل تلقائي دقيق للتاريخ الهجري (أم القرى) مع حرية اختيار الأرقام العربية (٠-٩) أو اللاتينية (0-9) وتوجيه مباشر لخرائط جوجل.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#E8DED0] text-xs font-bold text-[#8E6F36] flex items-center gap-1.5">
                        <x-app-icon name="map-pin" class="w-4 h-4" />
                        <span>توقيت الرياض والقاهرة بدقة تامة</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-[#FAF7F2] border-b border-[#E8DED0]/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#8E6F36] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    خطوات بسيطة وساحرة
                </span>
                <h2 class="font-cairo text-3xl sm:text-4xl font-extrabold text-[#292521] mt-3">
                    كيف تعمل منصة زفّــة؟
                </h2>
                <p class="text-base text-[#292521]/70 mt-3">
                    ثلاث خطوات سهلة تفصلك عن بطاقة زفاف رقمية متكاملة تليق بأجمل ليالي العمر
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Step 1 -->
                <div class="relative bg-white p-8 rounded-3xl border border-[#C6A76B]/25 shadow-sm hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#1B1815] font-cairo font-extrabold text-xl mb-6 shadow-md group-hover:scale-105 transition-transform">
                        ١
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">اختر التصميم المناسب</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        استعرض تشكيلتنا الملكية من القوالب المصممة بعناية فائقة (ملكي ذهبي، زهور ناعمة، أصالة إسلامية، وبساطة معاصرة) وعاينها مباشرة.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-white p-8 rounded-3xl border border-[#C6A76B]/25 shadow-sm hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#1B1815] font-cairo font-extrabold text-xl mb-6 shadow-md group-hover:scale-105 transition-transform">
                        ٢
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">خصّص الدعوة بالتفاصيل والخصوصية</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        أضف أسماء العروسين أو العائلتين، الموعد الهجري والميلادي، خريطة القاعة، برنامج الحفل، والموسيقى الخلفية مع معاينة حية فورية للتغييرات.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-white p-8 rounded-3xl border border-[#C6A76B]/25 shadow-sm hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#1B1815] font-cairo font-extrabold text-xl mb-6 shadow-md group-hover:scale-105 transition-transform">
                        ٣
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">شارك دعوتك عبر واتساب</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        بنقرة واحدة، أرسل رابط دعوتك الفريد عبر واتساب، وتابع تأكيدات الحضور وأعداد المرافقين لحظة بلحظة من لوحة التحكم الخاصة بك.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Templates Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#8E6F36] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                        معرض القوالب
                    </span>
                    <h2 class="font-cairo text-3xl sm:text-4xl font-extrabold text-[#292521] mt-2">
                        أشهر تصاميم بطاقات الزفاف
                    </h2>
                    <p class="text-sm text-[#292521]/70 mt-1">اختر من بين تشكيلة متنوعة تناسب مختلف الأذواق والمناسبات</p>
                </div>
                <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#FAF7F2] border border-[#C6A76B]/40 text-[#292521] text-sm font-bold shadow-sm hover:bg-[#F4EBD9]/50 transition">
                    <span>عرض كل التصاميم</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4 text-[#C6A76B]" />
                </a>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredTemplates as $template)
                    <div class="bg-[#FAF7F2]/60 rounded-3xl overflow-hidden border border-[#C6A76B]/25 shadow-wedding hover:shadow-wedding-hover transition-all flex flex-col group">
                        <!-- Thumbnail Preview -->
                        <div class="relative h-64 overflow-hidden bg-[#292521]">
                            <!-- Graceful Fallback (Gradient + Template Name) -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center"
                                 style="background: linear-gradient(135deg, #292521 0%, {{ $template->default_styles['primary_color'] ?? '#C6A76B' }} 100%);">
                                <div class="w-11 h-11 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white mb-2.5 shadow-sm">
                                    <x-app-icon name="sparkles" class="w-5 h-5" />
                                </div>
                                <span class="font-cairo text-base font-bold text-white">{{ $template->name_ar }}</span>
                                @if($template->name_en)
                                    <span class="text-xs font-medium text-white/75 mt-0.5" dir="ltr">{{ $template->name_en }}</span>
                                @endif
                            </div>

                            @if($template->thumbnail)
                                <img src="{{ $template->thumbnail }}"
                                     alt=""
                                     loading="lazy"
                                     onerror="this.style.display='none'"
                                     class="relative z-10 w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                            @endif

                            <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

                            <!-- Category Badge -->
                            <div class="absolute top-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-[#292521] shadow-sm">
                                {{ $template->category?->name_ar ?? 'تصميم فاخر' }}
                            </div>

                            @if($template->is_premium)
                                <div class="absolute top-4 left-4 z-20 bg-gradient-to-r from-amber-500 to-amber-600 text-white px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow-md">
                                    <x-app-icon name="crown" class="w-3 h-3" />
                                    <span>ملكي</span>
                                </div>
                            @endif

                            <div class="absolute bottom-4 right-4 left-4 z-20 text-white">
                                <h3 class="font-cairo text-lg font-bold leading-snug">{{ $template->name_ar }}</h3>
                                @if($template->name_en)
                                    <span class="block text-[11px] font-medium text-white/75 tracking-wide" dir="ltr">{{ $template->name_en }}</span>
                                @endif
                                <p class="text-xs text-white/80 line-clamp-1 mt-1">{{ $template->description_ar }}</p>
                            </div>
                        </div>

                        <!-- Card Action Body -->
                        <div class="p-6 bg-white flex-1 flex flex-col justify-between space-y-4">
                            <div class="flex items-center justify-between text-xs text-[#292521]/65">
                                <span class="flex items-center gap-1.5">
                                    <x-app-icon name="palette" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                    <span>ألوان وخطوط مخصصة</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <x-app-icon name="clock" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                    <span>تجهيز فوري</span>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <a href="{{ route('templates.preview', $template->slug) }}"
                                   class="py-2.5 px-3 rounded-xl bg-[#FAF7F2] border border-[#E8DED0] text-[#292521] text-xs font-bold text-center hover:bg-[#E8DED0]/50 transition flex items-center justify-center gap-1.5">
                                    <x-app-icon name="eye" class="w-4 h-4 text-[#C6A76B]" />
                                    <span>معاينة حية</span>
                                </a>

                                <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}"
                                   class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-xs font-bold text-center shadow-sm hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                    <x-app-icon name="sparkles" class="w-4 h-4" />
                                    <span>استخدم هذا التصميم</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-[#FAF7F2] border-t border-[#E8DED0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#8E6F36] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    مميزات متقدمة
                </span>
                <h2 class="font-cairo text-3xl sm:text-4xl font-extrabold text-[#292521] mt-3">
                    كل ما تحتاجه لإدارة دعوة زفافك بلمسة عصرية
                </h2>
                <p class="text-base text-[#292521]/70 mt-2">
                    بديل أنيق وعصري ومستدام للبطاقات الورقية المطبوعة، مع تحكم فوري وتفاعلية مطلقة
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="crown" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">قوالب ملكية جاهزة</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        تصاميم احترافية مسبقة الصنع مستوحاة من الثقافة السعودية والمصرية والمودرن بأعلى معايير الأناقة والذوق الرفيع.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="edit" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">تخصيص كامل وتفاعلي</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        تحكم كامل بالنصوص، ألوان الثيم، الخطوط العربية، صور المناسبة الاختيارية، برامج الحفل، وموقع القاعة مع معاينة فورية.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="whatsapp" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">مشاركة بنقرة واحدة عبر واتساب</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        توليد رسالة تهنئة ودعوة مخصصة وجذابة مع رابط دعوتك المباشر لمشاركتها مع العائلة والأصدقاء فوراً.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="users" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">إدارة تأكيدات الحضور (RSVP)</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        سجل حضور دقيق يوضح من سيحضر، أعداد المرافقين، والملاحظات الغذائية، مع تصدير القائمة الكاملة لملف Excel/CSV محمي.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="map-pin" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">خرائط جوجل وملاحة سهلة</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        توجيه الضيوف بضغطة زر واحدة مباشرة إلى موقع القاعة عبر خرائط Google Maps لتيسير الوصول يوم الحفل.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="p-8 rounded-3xl bg-white border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/50 hover:shadow-wedding transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/15 text-[#8E6F36] flex items-center justify-center border border-[#C6A76B]/25">
                        <x-app-icon name="music" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">بوابة صوتية وعدّاد تنازلي</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        عداد زمني بالثواني يحسب المتبقي حتى يوم الزفاف، مع بوابة ترحيبية ومشغل صوتي يمنح الضيوف تجربة شعورية حالمة.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-20 bg-obsidian-royal text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#C6A76B_1px,transparent_1px)] [background-size:16px_16px]"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C6A76B]/20 border border-[#C6A76B]/40 text-[#DFC99E] text-xs font-bold">
                <x-app-icon name="heart" class="w-4 h-4 text-[#C6A76B]" />
                <span>ليلة العمر تستحق الأفضل</span>
            </span>

            <h2 class="font-cairo text-3xl sm:text-5xl font-black text-white leading-tight">
                ابدأ الآن بتصميم دعوة زفافك <br>
                <span class="text-gold-gradient font-bold">وشارك الفرحة مع من تحب</span>
            </h2>

            <p class="text-base sm:text-lg text-[#E8DED0]/80 max-w-2xl mx-auto font-normal">
                انضم إلى العرسان والعائلات في المملكة العربية السعودية ومصر الذين وثقوا بمنصة زفّــة لصنع بطاقة رقمية تليق بمناسبتهم.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('templates.index') }}"
                   class="w-full sm:w-auto px-8 py-4 rounded-full bg-gradient-to-r from-[#C6A76B] via-[#DFC99E] to-[#B59556] text-[#1B1815] font-extrabold text-base shadow-lg hover:opacity-95 transition transform hover:-translate-y-0.5">
                    صمم دعوتك مجاناً الآن
                </a>
                <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                   target="_blank"
                   class="w-full sm:w-auto px-8 py-4 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-base transition">
                    معاينة نموذج زفاف حقيقي
                </a>
            </div>
        </div>
    </section>
</x-layouts.marketing>
