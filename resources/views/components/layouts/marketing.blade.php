<x-layouts.base :title="$title ?? null" :description="$description ?? null">
    <!-- Top Announcement Bar -->
    <div class="bg-[#292521] text-[#E8DED0] text-xs py-2 px-4 text-center border-b border-[#C6A76B]/30 flex items-center justify-center gap-2">
        <span class="inline-flex items-center gap-1.5 font-medium">
            <x-app-icon name="sparkles" class="w-3.5 h-3.5 text-[#C6A76B]" />
            <span>عرض الإطلاق الحصري: صمم بطاقة زفافك الرقمية الفاخرة وشاركها فوراً عبر واتساب مجاناً</span>
        </span>
        <a href="{{ route('templates.index') }}" class="text-[#C6A76B] hover:text-[#E8DED0] underline font-semibold transition">
            استكشف التصاميم
        </a>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-50 bg-[#FAF7F2]/90 backdrop-blur-md border-b border-[#E8DED0]/60 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#C6A76B] via-[#E6D5B8] to-[#B59556] p-0.5 shadow-md group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-[#FAF7F2] rounded-full flex items-center justify-center">
                            <span class="font-amiri text-2xl font-bold text-[#C6A76B]">ز</span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-cairo text-2xl font-extrabold tracking-tight text-[#292521]">
                            زفّــة <span class="text-[#C6A76B] text-lg font-normal">| Zaffe</span>
                        </span>
                        <span class="text-[11px] text-[#292521]/60 font-medium -mt-1">دعوات زفاف رقمية فاخرة</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-semibold transition hover:text-[#C6A76B] {{ request()->routeIs('home') ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                        الرئيسية
                    </a>
                    <a href="{{ route('templates.index') }}" class="text-sm font-semibold transition hover:text-[#C6A76B] {{ request()->routeIs('templates.*') ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                        تصاميم الدعوات
                    </a>
                    <a href="{{ route('pricing') }}" class="text-sm font-semibold transition hover:text-[#C6A76B] {{ request()->routeIs('pricing') ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                        الأسعار
                    </a>
                    <a href="{{ route('home') }}#how-it-works" class="text-sm font-semibold text-[#292521] hover:text-[#C6A76B] transition">
                        كيف تعمل
                    </a>
                    <a href="{{ route('about') }}" class="text-sm font-semibold transition hover:text-[#C6A76B] {{ request()->routeIs('about') ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                        من نحن
                    </a>
                    <a href="{{ route('faq') }}" class="text-sm font-semibold transition hover:text-[#C6A76B] {{ request()->routeIs('faq') ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                        الأسئلة الشائعة
                    </a>
                </nav>

                <!-- Auth & Action CTA Buttons -->
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#292521] text-white text-sm font-bold shadow-md hover:bg-[#3D3731] transition">
                            <x-app-icon name="crown" class="w-4 h-4 text-[#C6A76B]" />
                            <span>لوحة التحكم</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-[#292521] hover:text-[#C6A76B] transition">
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-sm font-bold shadow-md hover:opacity-95 hover:shadow-lg transition">
                            <span>صمم دعوتك الآن</span>
                            <x-app-icon name="arrow-left" class="w-4 h-4" />
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button (Alpine.js) -->
                <div class="md:hidden flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-[#292521] hover:text-[#C6A76B] focus:outline-none">
                        <x-app-icon name="menu" class="w-6 h-6" x-show="!open" />
                        <x-app-icon name="x" class="w-6 h-6" x-show="open" style="display: none;" />
                    </button>

                    <!-- Mobile Drawer -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="absolute top-20 left-0 right-0 bg-[#FAF7F2] border-b border-[#E8DED0] shadow-xl p-6 flex flex-col gap-4 text-center z-50">
                        <a href="{{ route('home') }}" class="py-2 text-base font-bold text-[#292521] hover:text-[#C6A76B]">الرئيسية</a>
                        <a href="{{ route('templates.index') }}" class="py-2 text-base font-bold text-[#292521] hover:text-[#C6A76B]">تصاميم الدعوات</a>
                        <a href="{{ route('pricing') }}" class="py-2 text-base font-bold text-[#292521] hover:text-[#C6A76B]">الأسعار</a>
                        <a href="{{ route('about') }}" class="py-2 text-base font-bold text-[#292521] hover:text-[#C6A76B]">من نحن</a>
                        <a href="{{ route('faq') }}" class="py-2 text-base font-bold text-[#292521] hover:text-[#C6A76B]">الأسئلة الشائعة</a>
                        <div class="pt-4 border-t border-[#E8DED0] flex flex-col gap-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="w-full py-3 rounded-full bg-[#292521] text-white font-bold">لوحة التحكم</a>
                            @else
                                <a href="{{ route('login') }}" class="w-full py-2.5 text-[#292521] font-bold">تسجيل الدخول</a>
                                <a href="{{ route('register') }}" class="w-full py-3 rounded-full bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold shadow-md">صمم دعوتك الآن</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[#292521] text-[#E8DED0] pt-16 pb-12 border-t border-[#C6A76B]/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-[#E8DED0]/10">
                <!-- Col 1: About Zaffe -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#292521] font-amiri text-xl font-bold">
                            ز
                        </div>
                        <span class="font-cairo text-2xl font-bold text-white tracking-tight">زفّــة</span>
                    </div>
                    <p class="text-sm text-[#E8DED0]/70 leading-relaxed">
                        المنصة الأولى في مصر والشرق الأوسط لابتكار دعوات الزفاف الرقمية التفاعلية بكل أناقة وبساطة، لمشاركة أحلى اللحظات مع أحبائكم بلمسة عصرية لا تُنسى.
                    </p>
                    <div class="flex items-center gap-3 pt-2 text-[#C6A76B]">
                        <span class="text-xs bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30 font-semibold">
                            🇪🇬 صُنع بفخر في مصر
                        </span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 font-cairo">روابط سريعة</h3>
                    <ul class="space-y-2.5 text-sm text-[#E8DED0]/80">
                        <li><a href="{{ route('templates.index') }}" class="hover:text-[#C6A76B] transition">معرض التصاميم الملكية</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-[#C6A76B] transition">باقات الأسعار</a></li>
                        <li><a href="{{ route('home') }}#features" class="hover:text-[#C6A76B] transition">مميزات المنصة</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-[#C6A76B] transition">الأسئلة الشائعة</a></li>
                        <li><a href="{{ url('/invitation/ahmed-and-sarah') }}" target="_blank" class="text-[#C6A76B] hover:underline flex items-center gap-1 font-semibold">
                            <span>نموذج دعوة حية</span>
                            <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                        </a></li>
                    </ul>
                </div>

                <!-- Col 3: Templates Styles -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 font-cairo">أنماط التصاميم</h3>
                    <ul class="space-y-2.5 text-sm text-[#E8DED0]/80">
                        <li><a href="{{ route('templates.index', ['category' => 'luxury']) }}" class="hover:text-[#C6A76B] transition">الفخامة الذهبية والملكية</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'floral']) }}" class="hover:text-[#C6A76B] transition">الزهور والباستيل الرومانسي</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'islamic']) }}" class="hover:text-[#C6A76B] transition">الأصالة الإسلامية والأرابيسك</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'minimal']) }}" class="hover:text-[#C6A76B] transition">المودرن البسيط (Minimalist)</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'classic']) }}" class="hover:text-[#C6A76B] transition">الكلاسيكي مع ختم الشمع</a></li>
                    </ul>
                </div>

                <!-- Col 4: Support & Contact -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 font-cairo">تواصل ودعم الزفاف</h3>
                    <p class="text-sm text-[#E8DED0]/70 mb-4">
                        فريق زفّة جاهز لمساعدتكم في تنسيق أرقى دعوة ليومكم الكبير:
                    </p>
                    <ul class="space-y-2.5 text-sm text-[#E8DED0]/80">
                        <li class="flex items-center gap-2">
                            <x-app-icon name="whatsapp" class="w-4 h-4 text-emerald-400" />
                            <a href="https://wa.me/201000000000" target="_blank" class="hover:text-emerald-400 transition" dir="ltr">+20 100 000 0000</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-app-icon name="mail" class="w-4 h-4 text-[#C6A76B]" />
                            <a href="mailto:support@zaffe.app" class="hover:text-[#C6A76B] transition">support@zaffe.app</a>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-[#E8DED0]/60">
                            <x-app-icon name="clock" class="w-4 h-4 text-[#C6A76B]" />
                            <span>دعم متواصل 7 أيام في الأسبوع</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#E8DED0]/60 gap-4">
                <p>© {{ date('Y') }} زفّــة (Zaffe). جميع الحقوق محفوظة لشركاء العمر في مصر والوطن العربي.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('about') }}" class="hover:text-[#C6A76B] transition">الشروط والأحكام</a>
                    <a href="{{ route('about') }}" class="hover:text-[#C6A76B] transition">سياسة الخصوصية</a>
                    <a href="{{ route('contact') }}" class="hover:text-[#C6A76B] transition">خدمة العملاء</a>
                </div>
            </div>
        </div>
    </footer>
</x-layouts.base>
