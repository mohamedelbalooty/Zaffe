<x-layouts.marketing :title="'زفّــة — اصنع دعوتك الخاصة واجعل يومك المميز لا يُنسى'">
    <!-- Hero Section -->
    <section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden bg-gradient-to-b from-[#FAF7F2] via-[#F4EBD9]/30 to-[#FAF7F2]">
        <!-- Decorative blurred circles -->
        <div class="absolute top-1/4 -right-20 w-96 h-96 rounded-full bg-[#C6A76B]/15 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 -left-20 w-96 h-96 rounded-full bg-[#D9B7B0]/20 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text / Call to Action -->
                <div class="lg:col-span-7 text-center lg:text-right space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C6A76B]/15 border border-[#C6A76B]/30 text-[#C6A76B] text-xs font-bold shadow-sm">
                        <x-app-icon name="crown" class="w-4 h-4 text-[#C6A76B]" />
                        <span>المنصة الأولى لدعوات الزفاف الرقمية الملكية في مصر والشرق الأوسط</span>
                    </div>

                    <h1 class="font-cairo text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#292521] leading-[1.25] tracking-tight">
                        اصنع دعوتك الخاصة <br class="hidden sm:inline">
                        <span class="text-gold-gradient font-bold">واجعل يومك المميز لا يُنسى</span>
                    </h1>

                    <p class="text-base sm:text-lg text-[#292521]/75 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        صمم بطاقة زفاف رقمية فاخرة تعكس فخامة مناسبتك الخاصة دون الحاجة لأي خبرة برمجية أو تصميمية. شارك بطاقتك فوراً برابط فريد عبر واتساب واستقبل تأكيدات حضور ضيوفك (RSVP) بدقة واحترافية.
                    </p>

                    <!-- CTAs -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('templates.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-full bg-gradient-to-r from-[#C6A76B] via-[#DFCA9B] to-[#B59556] text-white font-bold text-base shadow-wedding hover:shadow-wedding-hover hover:opacity-95 transition-all transform hover:-translate-y-0.5">
                            <x-app-icon name="sparkles" class="w-5 h-5" />
                            <span>صمم دعوتك الآن</span>
                        </a>

                        <a href="{{ route('templates.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-white text-[#292521] border border-[#C6A76B]/40 font-bold text-base shadow-sm hover:bg-[#FAF7F2] transition">
                            <x-app-icon name="eye" class="w-5 h-5 text-[#C6A76B]" />
                            <span>استكشف التصميمات</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="pt-8 border-t border-[#E8DED0]/60 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-[#292521]/70">
                        <div class="flex items-center gap-2 font-medium">
                            <x-app-icon name="check-circle" class="w-4 h-4 text-[#C6A76B]" />
                            <span>مشاركة فورية عبر واتساب</span>
                        </div>
                        <div class="flex items-center gap-2 font-medium">
                            <x-app-icon name="check-circle" class="w-4 h-4 text-[#C6A76B]" />
                            <span>تأكيد حضور تلقائي (RSVP)</span>
                        </div>
                        <div class="flex items-center gap-2 font-medium">
                            <x-app-icon name="check-circle" class="w-4 h-4 text-[#C6A76B]" />
                            <span>متجاوبة 100% مع الهواتف الذكية</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Phone Mockup / Live Preview Card -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <!-- Golden Glow Aura -->
                        <div class="absolute -inset-1.5 bg-gradient-to-tr from-[#C6A76B] to-[#D9B7B0] rounded-[2.5rem] blur-xl opacity-40"></div>

                        <!-- Phone Frame Container -->
                        <div class="relative bg-white border-4 border-[#C6A76B]/30 rounded-[2.5rem] shadow-2xl p-5 overflow-hidden">
                            <!-- Live Ribbon Badge -->
                            <div class="flex items-center justify-between pb-4 border-b border-[#E8DED0]">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-[#C6A76B]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>معاينة حية للدعوة الملكية</span>
                                </div>
                                <span class="text-[11px] font-semibold text-[#292521]/50">Four Seasons Nile Plaza</span>
                            </div>

                            <!-- Invitation Preview Card -->
                            <div class="mt-4 p-5 rounded-2xl bg-[#FAF7F2] border border-[#C6A76B]/30 text-center space-y-4">
                                <div class="font-amiri text-xs text-[#C6A76B] font-bold">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                                <div class="w-8 h-px bg-[#C6A76B]/40 mx-auto"></div>
                                
                                <div class="font-cairo text-2xl font-black text-[#292521] tracking-wide">
                                    أحمد & سارة
                                </div>

                                <div class="w-20 h-20 mx-auto rounded-full p-1 bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] shadow-sm">
                                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=300&q=80" 
                                         alt="العروسان" 
                                         class="w-full h-full object-cover rounded-full">
                                </div>

                                <p class="text-xs text-[#292521]/75 leading-relaxed font-tajawal">
                                    نتشرف بدعوتكم لمشاركتنا فرحة العمر وتتويج قصة حبنا في ليلة لا تكتمل إلا بوجودكم.
                                </p>

                                <!-- Mini Countdown -->
                                <div class="grid grid-cols-4 gap-1.5 text-center bg-white p-2.5 rounded-xl border border-[#E8DED0]">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-[#C6A76B]">45</span>
                                        <span class="text-[10px] text-[#292521]/60">يوم</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-[#C6A76B]">14</span>
                                        <span class="text-[10px] text-[#292521]/60">ساعة</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-[#C6A76B]">32</span>
                                        <span class="text-[10px] text-[#292521]/60">دقيقة</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-[#C6A76B]">18</span>
                                        <span class="text-[10px] text-[#292521]/60">ثانية</span>
                                    </div>
                                </div>

                                <!-- RSVP Interactive Button Mock -->
                                <a href="{{ url('/invitation/ahmed-and-sarah') }}" 
                                   target="_blank"
                                   class="w-full py-2.5 px-4 rounded-xl bg-[#292521] text-white text-xs font-bold flex items-center justify-center gap-2 hover:bg-[#3D3731] transition">
                                    <x-app-icon name="heart" class="w-4 h-4 text-[#C6A76B]" />
                                    <span>تأكيد الحضور (RSVP)</span>
                                </a>
                            </div>

                            <!-- Mobile frame bottom indicator -->
                            <div class="mt-4 flex items-center justify-center">
                                <div class="w-24 h-1 bg-[#292521]/20 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white border-y border-[#E8DED0]/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/10 px-3 py-1 rounded-full border border-[#C6A76B]/25">
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
                <div class="relative bg-[#FAF7F2] p-8 rounded-3xl border border-[#C6A76B]/25 hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-white font-extrabold text-xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        ١
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">اختر التصميم المناسب</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        استعرض تشكيلتنا الملكية من القوالب المصممة بعناية فائقة (ملكي ذهبي، زهور ناعمة، أصالة إسلامية، وبساطة معاصرة) وعاينها مباشرة.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-[#FAF7F2] p-8 rounded-3xl border border-[#C6A76B]/25 hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-white font-extrabold text-xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        ٢
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">خصص الدعوة بالتفاصيل والصور</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        أضف أسماء العروسين، تاريخ وموعد الحفل، خريطة موقع القاعة، ألبوم صور الخطوبة، والموسيقى الخلفية مع معاينة حية فورية للتغييرات.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-[#FAF7F2] p-8 rounded-3xl border border-[#C6A76B]/25 hover:shadow-wedding transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-white font-extrabold text-xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        ٣
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-3">شارك دعوتك مع ضيوفك</h3>
                    <p class="text-sm text-[#292521]/75 leading-relaxed">
                        بنقرة واحدة، أرسل رابط دعوتك الفريد عبر واتساب أو وسائل التواصل، وتابع تأكيدات الحضور وأعداد المرافقين لحظة بلحظة من لوحتك الخاصة.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Templates Section -->
    <section class="py-20 bg-[#FAF7F2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                        معرض القوالب
                    </span>
                    <h2 class="font-cairo text-3xl sm:text-4xl font-extrabold text-[#292521] mt-2">
                        أشهر تصاميم بطاقات الزفاف
                    </h2>
                    <p class="text-sm text-[#292521]/70 mt-1">اختر من بين تشكيلة متنوعة تناسب مختلف الأذواق والطقوس</p>
                </div>
                <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white border border-[#C6A76B]/40 text-[#292521] text-sm font-bold shadow-sm hover:bg-[#FAF7F2] transition">
                    <span>عرض كل التصاميم</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4 text-[#C6A76B]" />
                </a>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredTemplates as $template)
                    <div class="bg-white rounded-3xl overflow-hidden border border-[#C6A76B]/25 shadow-wedding hover:shadow-wedding-hover transition-all flex flex-col group">
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
                            
                            <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                            <!-- Category Badge -->
                            <div class="absolute top-4 right-4 z-20 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-[#292521] shadow-sm">
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
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="flex items-center justify-between text-xs text-[#292521]/60">
                                <span class="flex items-center gap-1">
                                    <x-app-icon name="palette" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                    <span>ألوان وخطوط مخصصة</span>
                                </span>
                                <span class="flex items-center gap-1">
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
    <section id="features" class="py-20 bg-white border-t border-[#E8DED0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
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
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="crown" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">قوالب ملكية جاهزة</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        تصاميم احترافية مسبقة الصنع مستوحاة من الثقافة العربية والمصرية والمودرن بأعلى معايير الأناقة والذوق الرفيع.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="edit" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">تخصيص كامل وتفاعلي</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        تحكم كامل بالنصوص، ألوان الثيم، الخطوط العربية، صور العروسين، برامج الحفل، وموقع الحفل مع معاينة فورية بدون انتظار.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="whatsapp" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">مشاركة بنقرة واحدة عبر واتساب</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        توليد رسالة تهنئة ودعوة مخصصة وجذابة مع رابط دعوتك المباشر لمشاركتها مع العائلة والأصدقاء فوراً.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="users" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">إدارة تأكيدات الحضور (RSVP)</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        سجل حضور دقيق يوضح من سيحضر، أعداد المرافقين، والرسائل الرقيقة، مع إمكانية تصدير القائمة الكاملة لملف Excel/CSV.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="map-pin" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">خرائط جوجل وملاحة سهلة</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        توجيه الضيوف بضغطة زر واحدة مباشرة إلى موقع القاعة عبر خرائط Google Maps وتجنب أي ارتباك في الوصول.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="p-8 rounded-3xl bg-[#FAF7F2] border border-[#E8DED0] space-y-4 hover:border-[#C6A76B]/40 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#C6A76B]/20 text-[#C6A76B] flex items-center justify-center">
                        <x-app-icon name="sparkles" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521]">موسيقى وعد تنازلي تفاعلي</h3>
                    <p class="text-sm text-[#292521]/70 leading-relaxed">
                        عداد زمني بالثواني يحسب المتبقي حتى يوم الزفاف، مع مشغل موسيقى هادئ يمنح الضيوف تجربة شعورية حالمة.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-20 bg-gradient-to-tr from-[#292521] via-[#38322C] to-[#292521] text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#C6A76B_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C6A76B]/20 border border-[#C6A76B]/40 text-[#C6A76B] text-xs font-bold">
                <x-app-icon name="heart" class="w-4 h-4" />
                <span>ليلة العمر تستحق الأفضل</span>
            </span>

            <h2 class="font-cairo text-3xl sm:text-5xl font-black text-white leading-tight">
                ابدأ الآن بتصميم دعوة زفافك <br>
                <span class="text-gold-gradient font-bold">وشارك الفرحة مع من تحب</span>
            </h2>

            <p class="text-base sm:text-lg text-[#E8DED0]/80 max-w-2xl mx-auto font-normal">
                انضم إلى مئات العرسان في مصر والوطن العربي الذين وثقوا بـ زفّة لصنع بطاقة رقمية مبهرة ليومهم الكبير.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('templates.index') }}" 
                   class="w-full sm:w-auto px-8 py-4 rounded-full bg-gradient-to-r from-[#C6A76B] via-[#DFC99E] to-[#B59556] text-white font-bold text-base shadow-lg hover:opacity-95 transition transform hover:-translate-y-0.5">
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
