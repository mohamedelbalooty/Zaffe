@php
    $isEn = app()->getLocale() === 'en';
    $rawSiteNameAr = \App\Models\PlatformSetting::get('site_name_ar', 'زفّــة');
    $rawSiteNameEn = \App\Models\PlatformSetting::get('site_name_en', 'Zaffe');
    $brandAr = trim(\Illuminate\Support\Str::before($rawSiteNameAr, '—')) ?: $rawSiteNameAr;
    $brandEn = trim(\Illuminate\Support\Str::before($rawSiteNameEn, '—')) ?: $rawSiteNameEn;
    $supportEmail = \App\Models\PlatformSetting::get('contact_email', 'support@zaffe.app');
    $supportPhone = \App\Models\PlatformSetting::get('contact_phone', '+20 100 000 0000');
    $whatsappUrl = \App\Models\PlatformSetting::whatsappUrl();
@endphp

<x-layouts.base :title="$title ?? null" :description="$description ?? null">
    <!-- Top Release 1 Announcement Strip -->
    <div class="bg-[#161311] text-[#E4D9C8] text-xs py-2 px-4 border-b border-[#C6A76B]/25">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-center">
            <span class="inline-flex items-center gap-1.5 font-medium">
                <x-app-icon name="sparkles" class="w-3.5 h-3.5 text-[#DFC99E] shrink-0" />
                <span>
                    {{ $isEn
                        ? 'Release 1 Launch Gift: Design & share your luxury wedding card via WhatsApp completely free'
                        : 'عرض الإطلاق الحصري: صمم بطاقة زفافك الرقمية الفاخرة وشاركها فوراً عبر واتساب مجاناً' }}
                </span>
            </span>
            <a href="{{ route('templates.index') }}"
               class="text-[#DFC99E] hover:text-white underline underline-offset-4 font-bold transition whitespace-nowrap">
                {{ $isEn ? 'Explore Templates' : 'استكشف التصاميم' }}
            </a>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header x-data="{ open: false }"
            @keydown.escape.window="open = false"
            class="sticky top-0 z-50 bg-[#FAF7F2]/95 backdrop-blur-md border-b border-[#E4D9C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 h-18 sm:h-20">
                <!-- Brand Lockup -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0 min-w-0">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-[#C6A76B] via-[#DFC99E] to-[#B59556] p-[1.5px] shadow-sm group-hover:scale-[1.03] transition-transform shrink-0">
                        <div class="w-full h-full bg-[#FAF7F2] rounded-full flex items-center justify-center">
                            <span class="font-amiri text-xl sm:text-2xl font-bold text-[#856326] leading-none">ز</span>
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-baseline gap-1.5 whitespace-nowrap">
                            <span class="font-cairo text-xl sm:text-2xl font-extrabold tracking-tight text-[#221E1A]">
                                {{ $brandAr }}
                            </span>
                            <span class="text-[#C6A76B]/60 text-sm font-normal">|</span>
                            <span class="font-playfair text-base sm:text-lg font-semibold text-[#7A5A20]" dir="ltr">
                                {{ $brandEn }}
                            </span>
                        </div>
                        <span class="text-[11px] text-[#52493F] font-medium -mt-0.5 truncate hidden sm:block">
                            {{ $isEn ? 'Luxury Digital Wedding Invitations' : 'دعوات زفاف رقمية فاخرة' }}
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (5 core items) -->
                <nav aria-label="{{ $isEn ? 'Primary Navigation' : 'التنقل الرئيسي' }}"
                     class="hidden lg:flex items-center gap-7">
                    <a href="{{ route('home') }}"
                       class="relative py-2 text-sm font-bold whitespace-nowrap transition {{ request()->routeIs('home') ? 'text-[#7A5A20]' : 'text-[#221E1A] hover:text-[#7A5A20]' }}">
                        <span>{{ $isEn ? 'Home' : 'الرئيسية' }}</span>
                        @if(request()->routeIs('home'))
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-[#C6A76B] rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('templates.index') }}"
                       class="relative py-2 text-sm font-bold whitespace-nowrap transition {{ request()->routeIs('templates.*') ? 'text-[#7A5A20]' : 'text-[#221E1A] hover:text-[#7A5A20]' }}">
                        <span>{{ $isEn ? 'Templates' : 'تصاميم الدعوات' }}</span>
                        @if(request()->routeIs('templates.*'))
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-[#C6A76B] rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('pricing') }}"
                       class="relative py-2 text-sm font-bold whitespace-nowrap transition {{ request()->routeIs('pricing') ? 'text-[#7A5A20]' : 'text-[#221E1A] hover:text-[#7A5A20]' }}">
                        <span>{{ $isEn ? 'Pricing' : 'الأسعار' }}</span>
                        @if(request()->routeIs('pricing'))
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-[#C6A76B] rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('about') }}"
                       class="relative py-2 text-sm font-bold whitespace-nowrap transition {{ request()->routeIs('about') ? 'text-[#7A5A20]' : 'text-[#221E1A] hover:text-[#7A5A20]' }}">
                        <span>{{ $isEn ? 'About' : 'من نحن' }}</span>
                        @if(request()->routeIs('about'))
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-[#C6A76B] rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('faq') }}"
                       class="relative py-2 text-sm font-bold whitespace-nowrap transition {{ request()->routeIs('faq') ? 'text-[#7A5A20]' : 'text-[#221E1A] hover:text-[#7A5A20]' }}">
                        <span>{{ $isEn ? 'FAQ' : 'الأسئلة الشائعة' }}</span>
                        @if(request()->routeIs('faq'))
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-[#C6A76B] rounded-full"></span>
                        @endif
                    </a>
                </nav>

                <!-- Auth, Locale & Primary Action -->
                <div class="hidden lg:flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('lang.switch', $isEn ? 'ar' : 'en') }}"
                       class="inline-flex items-center justify-center gap-1.5 min-h-[40px] px-3 py-1.5 rounded-lg border border-[#E4D9C8] bg-white text-xs font-bold text-[#221E1A] hover:border-[#C6A76B] hover:text-[#7A5A20] transition whitespace-nowrap"
                       title="تغيير اللغة / Switch Language">
                        <x-app-icon name="globe" class="w-3.5 h-3.5 text-[#856326]" />
                        <span>{{ $isEn ? 'العربية' : 'EN' }}</span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-obsidian !rounded-xl !px-5 whitespace-nowrap">
                            <x-app-icon name="crown" class="w-4 h-4 text-[#DFC99E]" />
                            <span>{{ $isEn ? 'Studio Dashboard' : 'لوحة التحكم' }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center min-h-[44px] px-3.5 text-sm font-bold text-[#221E1A] hover:text-[#7A5A20] transition whitespace-nowrap">
                            {{ $isEn ? 'Sign In' : 'تسجيل الدخول' }}
                        </a>
                        <a href="{{ route('register') }}" class="btn-royal !rounded-xl !px-5 whitespace-nowrap">
                            <span>{{ $isEn ? 'Create Invitation' : 'صمم دعوتك الآن' }}</span>
                            <x-app-icon name="arrow-left" class="w-4 h-4 dir-arrow" />
                        </a>
                    @endauth
                </div>

                <!-- Mobile Controls -->
                <div class="lg:hidden flex items-center gap-2 shrink-0">
                    <a href="{{ route('lang.switch', $isEn ? 'ar' : 'en') }}"
                       class="inline-flex items-center justify-center min-h-[40px] min-w-[40px] px-2.5 rounded-lg border border-[#E4D9C8] bg-white text-xs font-bold text-[#221E1A]">
                        {{ $isEn ? 'ع' : 'EN' }}
                    </a>

                    <button type="button"
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            aria-label="{{ $isEn ? 'Toggle navigation menu' : 'فتح أو إغلاق القائمة' }}"
                            class="inline-flex items-center justify-center w-11 h-11 rounded-xl border border-[#E4D9C8] bg-white text-[#221E1A] hover:border-[#C6A76B] transition cursor-pointer">
                        <x-app-icon name="menu" class="w-5 h-5" x-show="!open" />
                        <x-app-icon name="x" class="w-5 h-5" x-show="open" x-cloak />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="open"
             x-cloak
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden absolute top-full inset-x-0 bg-[#FAF7F2] border-b border-[#E4D9C8] shadow-xl p-5 space-y-2 z-50">
            <nav class="flex flex-col space-y-1">
                <a href="{{ route('home') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('home') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'Home' : 'الرئيسية' }}
                </a>
                <a href="{{ route('templates.index') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('templates.*') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'Templates' : 'تصاميم الدعوات' }}
                </a>
                <a href="{{ route('pricing') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('pricing') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'Pricing' : 'الأسعار' }}
                </a>
                <a href="{{ route('about') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('about') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'About' : 'من نحن' }}
                </a>
                <a href="{{ route('faq') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('faq') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'FAQ' : 'الأسئلة الشائعة' }}
                </a>
                <a href="{{ route('contact') }}" class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('contact') ? 'bg-[#F4EBD9] text-[#7A5A20]' : 'text-[#221E1A] hover:bg-white' }}">
                    {{ $isEn ? 'Contact' : 'تواصل معنا' }}
                </a>
            </nav>

            <div class="pt-3 border-t border-[#E4D9C8] flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-obsidian w-full">
                        <x-app-icon name="crown" class="w-4 h-4 text-[#DFC99E]" />
                        <span>{{ $isEn ? 'Studio Dashboard' : 'لوحة التحكم' }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost-ivory w-full">
                        {{ $isEn ? 'Sign In' : 'تسجيل الدخول' }}
                    </a>
                    <a href="{{ route('register') }}" class="btn-royal w-full">
                        <span>{{ $isEn ? 'Create Invitation' : 'صمم دعوتك الآن' }}</span>
                        <x-app-icon name="arrow-left" class="w-4 h-4 dir-arrow" />
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main id="main-content" class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer Colophon -->
    <footer class="bg-[#161311] text-[#E4D9C8] pt-16 pb-10 border-t border-[#C6A76B]/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-white/10">
                <!-- Col 1: Brand -->
                <div class="space-y-4 md:col-span-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#161311] font-amiri text-xl font-bold shrink-0">
                            ز
                        </div>
                        <div>
                            <span class="font-cairo text-xl font-bold text-white block leading-none">{{ $brandAr }}</span>
                            <span class="text-[11px] text-[#DFC99E]/80 font-playfair block mt-1" dir="ltr">{{ $rawSiteNameEn }}</span>
                        </div>
                    </div>
                    <p class="text-sm text-[#E4D9C8]/75 leading-relaxed max-w-sm">
                        {{ $isEn
                            ? 'The leading digital wedding invitation studio across Saudi Arabia and Egypt — combining Arabian royal craft, family privacy, Umm al-Qura Hijri precision, and instant RSVP management.'
                            : 'المنصة الرائدة في المملكة العربية السعودية ومصر لابتكار دعوات الزفاف الرقمية التفاعلية بكل أناقة وخصوصية، لمشاركة أجمل اللحظات مع أحبائكم.' }}
                    </p>
                    <div class="inline-flex items-center gap-2 text-xs bg-white/[0.06] px-3.5 py-1.5 rounded-full border border-[#C6A76B]/30 font-semibold text-[#DFC99E]">
                        <x-app-icon name="crown" class="w-3.5 h-3.5 text-[#C6A76B]" />
                        <span>{{ $isEn ? 'Built for Saudi Arabia, Egypt & the Gulf' : 'مصممة بفخر للعرسان في السعودية ومصر والخليج' }}</span>
                    </div>
                    <span class="sr-only">{{ $rawSiteNameAr }} | زفّــة</span>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="md:col-span-2">
                    <h3 class="text-white font-bold text-sm mb-4 font-cairo">{{ $isEn ? 'Platform' : 'روابط سريعة' }}</h3>
                    <ul class="space-y-2.5 text-sm text-[#E4D9C8]/80">
                        <li><a href="{{ route('templates.index') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Templates Gallery' : 'معرض التصاميم' }}</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Pricing' : 'باقات الأسعار' }}</a></li>
                        <li><a href="{{ route('home') }}#features" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Features' : 'مميزات المنصة' }}</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'FAQ' : 'الأسئلة الشائعة' }}</a></li>
                        <li>
                            <a href="{{ route('demo.saudi') }}" target="_blank" rel="noopener noreferrer" class="text-[#DFC99E] hover:underline inline-flex items-center gap-1.5 font-semibold">
                                <span>{{ $isEn ? 'Live Sample Card' : 'نموذج دعوة حية' }}</span>
                                <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Template Styles -->
                <div class="md:col-span-3">
                    <h3 class="text-white font-bold text-sm mb-4 font-cairo">{{ $isEn ? 'Design Collections' : 'أنماط التصاميم' }}</h3>
                    <ul class="space-y-2.5 text-sm text-[#E4D9C8]/80">
                        <li><a href="{{ route('templates.index', ['category' => 'luxury']) }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Royal Gold & Luxury' : 'الفخامة الذهبية والملكية' }}</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'floral']) }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Floral Romance' : 'الزهور والباستيل الرومانسي' }}</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'islamic']) }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Islamic Arabesque' : 'الأصالة الإسلامية والأرابيسك' }}</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'minimal']) }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Minimalist Modern' : 'البساطة العصرية الراقية' }}</a></li>
                        <li><a href="{{ route('templates.index', ['category' => 'classic']) }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Classic Aristocrat' : 'الكلاسيكي مع ختم الشمع' }}</a></li>
                    </ul>
                </div>

                <!-- Col 4: Support -->
                <div class="md:col-span-3">
                    <h3 class="text-white font-bold text-sm mb-4 font-cairo">{{ $isEn ? 'Wedding Concierge' : 'تواصل ودعم الزفاف' }}</h3>
                    <p class="text-sm text-[#E4D9C8]/70 mb-4">
                        {{ $isEn ? 'Our support team is ready to assist with your invitation design:' : 'فريق زفّة جاهز لمساعدتكم في تنسيق أرقى دعوة ليومكم الكبير:' }}
                    </p>
                    <ul class="space-y-2.5 text-sm text-[#E4D9C8]/85">
                        <li class="flex items-center gap-2.5">
                            <x-app-icon name="whatsapp" class="w-4 h-4 text-emerald-400 shrink-0" />
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-300 transition tabular-nums" dir="ltr">{{ $supportPhone }}</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <x-app-icon name="mail" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                            <a href="mailto:{{ $supportEmail }}" class="hover:text-[#DFC99E] transition break-all">{{ $supportEmail }}</a>
                        </li>
                        <li class="flex items-center gap-2.5 text-xs text-[#E4D9C8]/65 pt-1">
                            <x-app-icon name="clock" class="w-4 h-4 text-[#C6A76B] shrink-0" />
                            <span>{{ $isEn ? 'Support available 7 days a week' : 'دعم متواصل 7 أيام في الأسبوع' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-7 flex flex-col sm:flex-row items-center justify-between text-xs text-[#E4D9C8]/60 gap-4">
                <p>© {{ date('Y') }} زفّــة (Zaffe). {{ $isEn ? 'All rights reserved.' : 'جميع الحقوق محفوظة لشركاء العمر في السعودية ومصر والعالم العربي.' }}</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('about') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'About Zaffe' : 'عن المنصة' }}</a>
                    <a href="{{ route('faq') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Help & FAQ' : 'الأسئلة الشائعة' }}</a>
                    <a href="{{ route('contact') }}" class="hover:text-[#DFC99E] transition">{{ $isEn ? 'Support' : 'خدمة العملاء' }}</a>
                </div>
            </div>
        </div>
    </footer>
</x-layouts.base>
