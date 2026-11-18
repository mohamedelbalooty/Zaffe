@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.marketing :title="$isEn ? 'Zaffe — Luxury Digital Wedding Invitations' : 'زفّــة — اصنع دعوتك الخاصة واجعل يومك المميز لا يُنسى'">
    <!-- Hero Section: Arabian Editorial Atelier -->
    <section class="relative pt-10 pb-16 sm:pt-14 sm:pb-24 overflow-hidden bg-luxury-grain border-b border-[#E4D9C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Editorial Copy & Primary CTAs -->
                <div class="lg:col-span-7 text-center lg:text-start space-y-6 animate-fade-up">
                    <h1 class="font-cairo text-3xl sm:text-5xl lg:text-[3.35rem] font-black text-[#221E1A] leading-[1.2] tracking-[-0.02em]">
                        @if($isEn)
                            Craft Your Royal Wedding Card <br class="hidden sm:inline">
                            <span class="text-[#7A5A20] font-extrabold">And Make Your Night Unforgettable</span>
                        @else
                            اصنع دعوتك الخاصة <br class="hidden sm:inline">
                            <span class="text-[#7A5A20]">واجعل يومك المميز لا يُنسى</span>
                        @endif
                    </h1>

                    <p class="text-base sm:text-lg text-[#52493F] max-w-[62ch] mx-auto lg:mx-0 leading-[1.75]">
                        @if($isEn)
                            Design an interactive luxury wedding invitation tailored for Saudi Arabia and Egypt — complete with an audio open-gate seal, dual Umm al-Qura Hijri &amp; Gregorian calendar, one-tap WhatsApp sharing, and structured RSVP tracking.
                        @else
                            صمّم بطاقة زفاف رقمية فاخرة تليق بخصوصية مناسبتك دون الحاجة لأي خبرة تصميمية؛ مع بوابة فتح تفاعلية، التاريخ الهجري والميلادي، ومشاركة فورية عبر واتساب مع تأكيد حضور ضيوفك (RSVP) بدقة واحترافية.
                        @endif
                    </p>

                    <!-- Primary & Secondary Actions -->
                    <div class="pt-1 flex flex-col sm:flex-row items-stretch sm:items-center justify-center lg:justify-start gap-3.5">
                        <a href="{{ route('templates.index') }}" class="btn-royal !px-7 !py-3.5 !text-base">
                            <x-app-icon name="sparkles" class="w-5 h-5" />
                            <span>{{ $isEn ? 'Design Your Card Free' : 'صمم دعوتك الآن مجاناً' }}</span>
                        </a>

                        <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-ghost-ivory !px-7 !py-3.5 !text-base">
                            <x-app-icon name="eye" class="w-5 h-5 text-[#856326]" />
                            <span>{{ $isEn ? 'View Live Invitation' : 'عاين دعوة حية مباشرة' }}</span>
                        </a>
                    </div>

                    <!-- Trust & Cultural Fit Strip -->
                    <div class="pt-6 border-t border-[#E4D9C8] flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-3 text-xs font-bold text-[#52493F]">
                        <div class="flex items-center gap-2">
                            <x-app-icon name="check-badge" class="w-4 h-4 text-[#856326] shrink-0" />
                            <span>{{ $isEn ? 'WhatsApp In-App Browser Ready' : 'توافق تام مع متصفح واتساب' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-app-icon name="calendar" class="w-4 h-4 text-[#856326] shrink-0" />
                            <span>{{ $isEn ? 'Umm al-Qura Hijri & Gregorian' : 'التقويم الهجري (أم القرى) والميلادي' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-app-icon name="shield-check" class="w-4 h-4 text-[#856326] shrink-0" />
                            <span>{{ $isEn ? 'Family Privacy & Optional Photos' : 'خصوصية عائلية وصور اختيارية' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Royal Invitation Folio Mockup -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-[23.5rem]" x-data="{ previewMode: 'card' }">
                        <div class="relative bg-white border-2 border-[#C6A76B]/55 rounded-[2rem] shadow-royal-card p-4 overflow-hidden">
                            <!-- Interactive Mode Switcher -->
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-[#E4D9C8]">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-[#221E1A]">
                                    <span class="w-2 h-2 rounded-full bg-[#0F4C3A]"></span>
                                    <span>{{ $isEn ? 'Live Folio' : 'معاينة حية' }}</span>
                                </div>

                                <div class="inline-flex items-center p-0.5 rounded-full bg-[#FAF7F2] border border-[#E4D9C8] text-[11px] font-bold">
                                    <button type="button"
                                            @click="previewMode = 'card'"
                                            :class="previewMode === 'card' ? 'bg-[#221E1A] text-[#FAF7F2]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                            class="px-3 py-1 rounded-full transition cursor-pointer">
                                        {{ $isEn ? 'Card' : 'بطاقة الدعوة' }}
                                    </button>
                                    <button type="button"
                                            @click="previewMode = 'gate'"
                                            :class="previewMode === 'gate' ? 'bg-[#C6A76B] text-[#151210]' : 'text-[#52493F] hover:text-[#221E1A]'"
                                            class="px-3 py-1 rounded-full transition cursor-pointer">
                                        {{ $isEn ? 'Open Gate' : 'بوابة الفتح' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Mode 1: Calligraphic Royal Card (Zero External Photos) -->
                            <div x-show="previewMode === 'card'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="mt-3.5 p-5 rounded-xl bg-[#FAF7F2] border-royal-double text-center space-y-3.5">
                                <div class="font-amiri text-xs text-[#7A5A20] font-bold tracking-wide">
                                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                                </div>

                                <div class="w-18 h-18 mx-auto rounded-full p-1 bg-gradient-to-tr from-[#C6A76B] via-[#DFC99E] to-[#B59556] shadow-sm">
                                    <div class="w-full h-full rounded-full bg-[#1B1815] border border-[#C6A76B]/50 flex flex-col items-center justify-center">
                                        <span class="font-amiri text-lg font-bold text-[#DFC99E] leading-none">ف &amp; ج</span>
                                        <span class="text-[8px] text-[#C6A76B] tracking-widest mt-1 font-semibold">ZAFFE</span>
                                    </div>
                                </div>

                                <div class="space-y-0.5">
                                    <p class="text-[11px] font-semibold text-[#52493F]">
                                        {{ $isEn ? 'We cordially invite you to the wedding of' : 'نتشرف بدعوتكم لحضور حفل زفاف' }}
                                    </p>
                                    <div class="font-cairo text-xl font-black text-[#221E1A] tracking-tight">
                                        {{ $isEn ? 'Faisal & Al-Jawhara' : 'فيصل & الجوهرة' }}
                                    </div>
                                </div>

                                <div class="px-3 py-2.5 rounded-lg bg-white border border-[#E4D9C8] space-y-1">
                                    <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-[#7A5A20]">
                                        <x-app-icon name="calendar" class="w-3.5 h-3.5 text-[#856326]" />
                                        <bdi dir="rtl">١٤ ذو القعدة ١٤٤٨ هـ</bdi>
                                    </div>
                                    <div class="text-[11px] text-[#52493F] font-medium">
                                        <bdi dir="rtl">الموافق ٢٠ أبريل ٢٠٢٧ م • ٨:٠٠ مساءً</bdi>
                                    </div>
                                    <div class="text-[11px] font-bold text-[#221E1A] pt-0.5 flex items-center justify-center gap-1">
                                        <x-app-icon name="map-pin" class="w-3 h-3 text-[#856326] shrink-0" />
                                        <span>{{ $isEn ? 'The Ritz-Carlton Riyadh' : 'الريتز كارلتون الرياض • الفورسيزونز القاهرة' }}</span>
                                    </div>
                                </div>

                                <!-- Mini Countdown -->
                                <div class="grid grid-cols-4 gap-1.5 text-center bg-white p-2 rounded-lg border border-[#E4D9C8] tabular-nums">
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#7A5A20]">٤٥</span>
                                        <span class="text-[10px] text-[#52493F]">{{ $isEn ? 'Days' : 'يوم' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#7A5A20]">١٤</span>
                                        <span class="text-[10px] text-[#52493F]">{{ $isEn ? 'Hrs' : 'ساعة' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#7A5A20]">٣٢</span>
                                        <span class="text-[10px] text-[#52493F]">{{ $isEn ? 'Min' : 'دقيقة' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-cairo font-extrabold text-sm text-[#7A5A20]">١٨</span>
                                        <span class="text-[10px] text-[#52493F]">{{ $isEn ? 'Sec' : 'ثانية' }}</span>
                                    </div>
                                </div>

                                <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn-obsidian w-full !min-h-[40px] !py-2 !text-xs !rounded-lg">
                                    <x-app-icon name="heart" class="w-3.5 h-3.5 text-[#DFC99E]" />
                                    <span>{{ $isEn ? 'Confirm Attendance (RSVP)' : 'تأكيد الحضور (RSVP)' }}</span>
                                </a>
                            </div>

                            <!-- Preview Mode 2: Tactile Royal Seal Open Gate -->
                            <div x-show="previewMode === 'gate'"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="mt-3.5 p-6 rounded-xl bg-obsidian-royal border border-[#C6A76B]/50 text-center text-[#FAF7F2] space-y-5">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-[#C6A76B]/40 text-[#DFC99E] text-[10px] font-bold">
                                    <x-app-icon name="volume-2" class="w-3.5 h-3.5" />
                                    <span>{{ $isEn ? 'Interactive Audio Seal' : 'شاشة فتح الدعوة بالزفّة الصوتية' }}</span>
                                </div>

                                <div class="space-y-1.5">
                                    <p class="font-amiri text-sm text-[#DFC99E]">{{ $isEn ? 'Private Wedding Invitation' : 'دعوة زفاف خاصة' }}</p>
                                    <h3 class="font-cairo text-2xl font-black text-white">
                                        {{ $isEn ? 'Faisal & Al-Jawhara' : 'فيصل & الجوهرة' }}
                                    </h3>
                                    <p class="text-xs text-[#E4D9C8]/75">
                                        {{ $isEn ? 'Tap the golden seal to open the card' : 'انقر على الختم الملكي لفتح البطاقة وسماع الزفّة' }}
                                    </p>
                                </div>

                                <button type="button"
                                        @click="previewMode = 'card'"
                                        class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-[#B59556] via-[#DFC99E] to-[#C6A76B] text-[#151210] flex flex-col items-center justify-center gap-1 glow-gold-seal animate-seal-pulse hover:scale-105 transition cursor-pointer">
                                    <x-app-icon name="mail-open" class="w-6 h-6" />
                                    <span class="font-cairo text-[11px] font-black">{{ $isEn ? 'Open Card' : 'افتح الدعوة' }}</span>
                                </button>

                                <div class="text-[11px] text-[#E4D9C8]/70 flex items-center justify-center gap-2">
                                    <x-app-icon name="shield-check" class="w-3.5 h-3.5 text-[#DFC99E]" />
                                    <span>{{ $isEn ? 'Works seamlessly inside WhatsApp' : 'يعمل بسلاسة داخل متصفح واتساب' }}</span>
                                </div>
                            </div>

                            <div class="mt-3.5 flex items-center justify-center">
                                <div class="w-20 h-1 bg-[#221E1A]/15 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Culturally Tailored Capabilities (Architectural 2-Column Split) -->
    <section class="py-16 sm:py-20 bg-white border-b border-[#E4D9C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                <div class="lg:col-span-5 space-y-5">
                    <h2 class="font-cairo text-3xl sm:text-4xl font-black text-[#221E1A] leading-tight">
                        {{ $isEn ? 'Flexible Invitation Modes & Exceptional Guest Experience' : 'أنماط دعوات مرنة وتجربة ضيوف استثنائية' }}
                    </h2>
                    <p class="text-base text-[#52493F] leading-[1.75]">
                        {{ $isEn
                            ? 'Zaffe gives couples complete control over hospitality — from tactile audio gates and photo-free family privacy to personal QR entry passes and accurate Umm al-Qura Hijri conversion.'
                            : 'تمنحك زفّــة تحكماً كاملاً في طريقة استقبال ضيوفك — من بوابة الختم الملكي والخصوصية التامة بدون صور، إلى تأكيدات الحضور الفورية والتقويم الهجري المزدوج.' }}
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('templates.index') }}" class="btn-obsidian !px-6">
                            <span>{{ $isEn ? 'Start Designing Free' : 'ابدأ تجربتك المجانية' }}</span>
                            <x-app-icon name="arrow-left" class="w-4 h-4 text-[#DFC99E] dir-arrow" />
                        </a>
                    </div>
                </div>

                <!-- 2x2 Architectural Hairline Grid -->
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 border border-[#E4D9C8] rounded-2xl overflow-hidden bg-[#FAF7F2]">
                    <div class="p-6 sm:p-7 border-b sm:border-e border-[#E4D9C8] space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-[#221E1A] text-[#DFC99E] flex items-center justify-center">
                                <x-app-icon name="mail-open" class="w-5 h-5" />
                            </div>
                            <span class="font-cairo text-xs font-black text-[#856326]">٠١</span>
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Interactive Open-Gate & Zaffe Audio' : 'بوابة فتح الدعوة والزفّة' }}
                        </h3>
                        <p class="text-sm text-[#52493F] leading-relaxed">
                            {{ $isEn
                                ? 'Guests tap the royal seal to open the card and unlock ceremonial audio smoothly inside mobile and WhatsApp browsers.'
                                : 'عند تفعيل الصوت، يبدأ الضيف بنقرة أنيقة على الختم الملكي لفتح الدعوة وتشغيل الزفّة بسلاسة داخل متصفح واتساب.' }}
                        </p>
                    </div>

                    <div class="p-6 sm:p-7 border-b border-[#E4D9C8] space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-[#0F4C3A] text-white flex items-center justify-center">
                                <x-app-icon name="check-badge" class="w-5 h-5" />
                            </div>
                            <span class="font-cairo text-xs font-black text-[#0F4C3A]">٠٢</span>
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Smart RSVP & Guest Quotas' : 'تأكيد حضور واعتذار ذكي' }}
                        </h3>
                        <p class="text-sm text-[#52493F] leading-relaxed">
                            {{ $isEn
                                ? 'Enforce adult/child companion quotas per family, prevent duplicate phone submissions, and export clean Excel/CSV sheets.'
                                : 'استقبل ردود الضيوف مع تحديد الحد الأقصى للمرافقين، منع تكرار رقم الجوال، وتصدير السجل إلى Excel بضغطة زر.' }}
                        </p>
                    </div>

                    <div class="p-6 sm:p-7 border-b sm:border-b-0 sm:border-e border-[#E4D9C8] space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-[#F4EBD9] text-[#7A5A20] border border-[#C6A76B]/40 flex items-center justify-center">
                                <x-app-icon name="shield-check" class="w-5 h-5" />
                            </div>
                            <span class="font-cairo text-xs font-black text-[#856326]">٠٣</span>
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Family Privacy by Default' : 'خصوصية العائلة أولاً' }}
                        </h3>
                        <p class="text-sm text-[#52493F] leading-relaxed">
                            {{ $isEn
                                ? 'Every theme looks complete without couple portraits, supports family honorifics, and blocks public access to drafts.'
                                : 'قوالب فاخرة تكتمل أناقتها دون الحاجة لرفع صور شخصية، مع دعم الأسماء العائلية وحماية المسودات الخاصة.' }}
                        </p>
                    </div>

                    <div class="p-6 sm:p-7 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-[#221E1A] text-[#DFC99E] flex items-center justify-center">
                                <x-app-icon name="calendar" class="w-5 h-5" />
                            </div>
                            <span class="font-cairo text-xs font-black text-[#856326]">٠٤</span>
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Umm al-Qura Hijri & Venue Maps' : 'هجري أم القرى وخرائط القاعة' }}
                        </h3>
                        <p class="text-sm text-[#52493F] leading-relaxed">
                            {{ $isEn
                                ? 'Accurate Riyadh and Cairo timezone support with Umm al-Qura Hijri conversion, Arabic/Latin numerals, and Google Maps pins.'
                                : 'تحويل تلقائي دقيق للتاريخ الهجري (أم القرى) مع حرية اختيار الأرقام العربية أو اللاتينية وتوجيه مباشر لخرائط جوجل.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: How It Works (Connected 3-Step Sequence) -->
    <section id="how-it-works" class="py-16 sm:py-20 bg-[#FAF7F2] border-b border-[#E4D9C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12 space-y-3">
                <h2 class="font-cairo text-3xl sm:text-4xl font-black text-[#221E1A]">
                    {{ $isEn ? 'How Zaffe Works' : 'كيف تعمل منصة زفّــة؟' }}
                </h2>
                <p class="text-base text-[#52493F]">
                    {{ $isEn
                        ? 'Three streamlined steps from choosing a theme to collecting guest confirmations.'
                        : 'ثلاث خطوات واضحة تفصلك عن بطاقة زفاف رقمية متكاملة تليق بأجمل ليالي العمر.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                <div class="card-atelier p-7 space-y-4">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] border border-[#C6A76B]/45 flex items-center justify-center text-[#7A5A20] font-cairo font-black text-lg">
                        ١
                    </div>
                    <h3 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Choose Your Theme' : 'اختر التصميم المناسب' }}
                    </h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn
                            ? 'Browse six curated royal, floral, Islamic heritage, celestial, minimalist, and classic themes with live device previews.'
                            : 'استعرض تشكيلتنا الملكية من القوالب المصممة بعناية فائقة وعاينها مباشرة على شاشة الجوال وسطح المكتب.' }}
                    </p>
                </div>

                <div class="card-atelier p-7 space-y-4">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] border border-[#C6A76B]/45 flex items-center justify-center text-[#7A5A20] font-cairo font-black text-lg">
                        ٢
                    </div>
                    <h3 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Personalize in Real Time' : 'خصّص الدعوة بالتفاصيل والخصوصية' }}
                    </h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn
                            ? 'Set names, Hijri/Gregorian dates, venue directions, optional music gate, and RSVP/QR pass rules with instant autosave.'
                            : 'أضف أسماء العروسين أو العائلتين، الموعد الهجري والميلادي، خريطة القاعة، وإعدادات الحضور مع معاينة فورية.' }}
                    </p>
                </div>

                <div class="card-atelier p-7 space-y-4">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] border border-[#C6A76B]/45 flex items-center justify-center text-[#7A5A20] font-cairo font-black text-lg">
                        ٣
                    </div>
                    <h3 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Share via WhatsApp' : 'شارك دعوتك عبر واتساب' }}
                    </h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn
                            ? 'Send public or personalized guest invite links in one click and track confirmed headcounts and dietary notes live.'
                            : 'بنقرة واحدة، أرسل رابط دعوتك الفريد عبر واتساب، وتابع تأكيدات الحضور وأعداد المرافقين لحظة بلحظة من لوحة التحكم.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Featured Templates Showcase (Clean Matte Framing, No Overlapping Text) -->
    <section class="py-16 sm:py-20 bg-white border-b border-[#E4D9C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div class="space-y-2">
                    <h2 class="font-cairo text-3xl sm:text-4xl font-black text-[#221E1A]">
                        {{ $isEn ? 'Featured Wedding Themes' : 'أشهر تصاميم بطاقات الزفاف' }}
                    </h2>
                    <p class="text-sm sm:text-base text-[#52493F]">
                        {{ $isEn ? 'Six distinct visual worlds ready to customize in minutes.' : 'اختر من بين تشكيلة متنوعة تناسب مختلف الأذواق والمناسبات' }}
                    </p>
                </div>
                <a href="{{ route('templates.index') }}" class="btn-ghost-ivory shrink-0 self-start sm:self-auto">
                    <span>{{ $isEn ? 'All Templates' : 'عرض كل التصاميم' }}</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4 text-[#856326] dir-arrow" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($featuredTemplates as $template)
                    @php
                        $primaryColor = $template->default_styles['primary_color'] ?? '#C6A76B';
                    @endphp
                    <article class="card-atelier overflow-hidden hover:border-[#C6A76B] transition-colors flex flex-col group">
                        <!-- Clean Preview Frame (No cluttered overlapping titles) -->
                        <div class="relative h-56 overflow-hidden bg-[#221E1A] border-b border-[#E4D9C8]">
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center"
                                 style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                <div class="w-10 h-10 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white mb-2">
                                    <x-app-icon name="sparkles" class="w-5 h-5" />
                                </div>
                                <span class="font-cairo text-base font-bold text-white">{{ $template->name_ar }}</span>
                                @if($template->name_en)
                                    <span class="text-xs font-medium text-white/80 mt-0.5" dir="ltr">{{ $template->name_en }}</span>
                                @endif
                            </div>

                            @if($template->thumbnail)
                                <img src="{{ $template->thumbnail }}"
                                     alt=""
                                     loading="lazy"
                                     onerror="this.style.display='none'"
                                     class="relative z-10 w-full h-full object-cover object-top group-hover:scale-[1.03] transition-transform duration-500">
                            @endif

                            <div class="absolute top-3 start-3 z-20 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-[#221E1A] border border-[#E4D9C8] shadow-xs">
                                {{ $isEn ? ($template->category?->name_en ?? 'Luxury') : ($template->category?->name_ar ?? 'تصميم فاخر') }}
                            </div>
                        </div>

                        <!-- Clean Matte Card Details -->
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                            <div class="space-y-2">
                                <div class="flex items-baseline justify-between gap-2">
                                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                                        {{ $isEn && $template->name_en ? $template->name_en : $template->name_ar }}
                                    </h3>
                                    @if(!$isEn && $template->name_en)
                                        <span class="text-[11px] font-medium text-[#6E6458] shrink-0" dir="ltr">{{ $template->name_en }}</span>
                                    @endif
                                </div>
                                <p class="text-xs sm:text-sm text-[#52493F] line-clamp-2 leading-relaxed">
                                    {{ $isEn && $template->description_en ? $template->description_en : $template->description_ar }}
                                </p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-xs text-[#52493F] pt-3 border-t border-[#E4D9C8]">
                                    <span class="inline-flex items-center gap-1.5 font-semibold">
                                        <span class="w-3.5 h-3.5 rounded-full border border-black/15" style="background-color: {{ $primaryColor }};"></span>
                                        <span>{{ $isEn ? 'Custom Palette' : 'ألوان قابلة للتخصيص' }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 font-semibold">
                                        <x-app-icon name="type" class="w-3.5 h-3.5 text-[#856326]" />
                                        <span dir="ltr">{{ $template->default_styles['font_family'] ?? 'Tajawal' }}</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    <a href="{{ route('templates.preview', $template->slug) }}"
                                       class="btn-ghost-ivory !min-h-[42px] !py-2 !px-3 !text-xs">
                                        <x-app-icon name="eye" class="w-4 h-4 text-[#856326]" />
                                        <span>{{ $isEn ? 'Live Preview' : 'معاينة حية' }}</span>
                                    </a>

                                    <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}"
                                       class="btn-royal !min-h-[42px] !py-2 !px-3 !text-xs">
                                        <x-app-icon name="sparkles" class="w-4 h-4" />
                                        <span>{{ $isEn ? 'Use Theme' : 'استخدم التصميم' }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 5: Complete Platform Features -->
    <section id="features" class="py-16 sm:py-20 bg-[#FAF7F2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12 space-y-2">
                <h2 class="font-cairo text-3xl sm:text-4xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Everything Required for a Seamless Wedding Invitation' : 'كل ما تحتاجه لإدارة دعوة زفافك بلمسة عصرية' }}
                </h2>
                <p class="text-base text-[#52493F]">
                    {{ $isEn ? 'A thoughtful alternative to printed cards with live updates and complete host control.' : 'بديل أنيق ومستدام للبطاقات الورقية المطبوعة، مع تحكم فوري وتفاعلية مطلقة' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center border border-[#C6A76B]/30">
                        <x-app-icon name="crown" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'Curated Cultural Themes' : 'قوالب ملكية جاهزة' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Six thoughtfully crafted designs inspired by Saudi, Egyptian, and contemporary editorial aesthetics.' : 'تصاميم احترافية مستوحاة من الثقافة السعودية والمصرية والمودرن بأعلى معايير الأناقة والذوق الرفيع.' }}
                    </p>
                </div>

                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center border border-[#C6A76B]/30">
                        <x-app-icon name="edit" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'Live Studio Editor' : 'تخصيص كامل وتفاعلي' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Control text, colors, Arabic fonts, optional photo gallery, event schedule, and venue map with real-time autosave.' : 'تحكم كامل بالنصوص، ألوان الثيم، الخطوط العربية، صور المناسبة الاختيارية، برامج الحفل، وموقع القاعة مع معاينة فورية.' }}
                    </p>
                </div>

                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#ECF5F0] text-[#0F4C3A] flex items-center justify-center border border-[#0F4C3A]/20">
                        <x-app-icon name="whatsapp" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'One-Click WhatsApp Sharing' : 'مشاركة بنقرة واحدة عبر واتساب' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Generate polished invitation messages with unique personal guest links ready to send directly to family and friends.' : 'توليد رسالة تهنئة ودعوة مخصصة مع رابط دعوتك المباشر أو رابط كل مدعو لمشاركتها فوراً.' }}
                    </p>
                </div>

                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center border border-[#C6A76B]/30">
                        <x-app-icon name="users" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'Smart Guest Roster & QR Passes' : 'إدارة الضيوف وبطاقات QR' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Pre-register guests via CSV, enforce adult/child quotas, issue personal SVG QR passes, and export formula-safe spreadsheets.' : 'سجل حضور دقيق يوضح المؤكدين والمرافقين والوجبات، مع قائمة مدعوين مسبقة وبطاقات QR وتصدير Excel محمي.' }}
                    </p>
                </div>

                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center border border-[#C6A76B]/30">
                        <x-app-icon name="map-pin" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'Google Maps Venue Navigation' : 'خرائط جوجل وملاحة سهلة' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Direct guests straight to your ballroom or venue location on Google Maps with one tap on mobile.' : 'توجيه الضيوف بضغطة زر واحدة مباشرة إلى موقع القاعة عبر خرائط Google Maps لتيسير الوصول يوم الحفل.' }}
                    </p>
                </div>

                <div class="card-atelier p-6 sm:p-7 space-y-3">
                    <div class="w-11 h-11 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center border border-[#C6A76B]/30">
                        <x-app-icon name="music" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">{{ $isEn ? 'Audio Gate & Live Countdown' : 'بوابة صوتية وعدّاد تنازلي' }}</h3>
                    <p class="text-sm text-[#52493F] leading-relaxed">
                        {{ $isEn ? 'Timezone-accurate countdown timer paired with an interactive open-gate welcome experience for mobile browsers.' : 'عداد زمني دقيق بتوقيت حفل الزفاف، مع بوابة ترحيبية ومشغل صوتي يمنح الضيوف تجربة شعورية متكاملة.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 6: Final Call to Action -->
    <section class="py-16 sm:py-20 bg-obsidian-royal text-white border-t border-[#C6A76B]/30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="font-cairo text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                {{ $isEn ? 'Begin Crafting Your Wedding Invitation Today' : 'ابدأ الآن بتصميم دعوة زفافك وشارك الفرحة مع من تحب' }}
            </h2>

            <p class="text-base sm:text-lg text-[#E4D9C8]/85 max-w-2xl mx-auto">
                {{ $isEn
                    ? 'Join couples and families across Saudi Arabia and Egypt creating unforgettable digital invitations on Zaffe — 100% free during Release 1.'
                    : 'انضم إلى العرسان والعائلات في المملكة العربية السعودية ومصر الذين يثقون بمنصة زفّــة لصنع بطاقة رقمية تليق بمناسبتهم.' }}
            </p>

            <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3.5">
                <a href="{{ route('templates.index') }}" class="btn-royal !px-8 !py-4 !text-base">
                    <x-app-icon name="sparkles" class="w-5 h-5" />
                    <span>{{ $isEn ? 'Create Invitation Free' : 'صمم دعوتك مجاناً الآن' }}</span>
                </a>
                <a href="{{ url('/invitation/ahmed-and-sarah') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 min-h-[48px] px-8 py-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/25 text-white font-bold text-base transition">
                    <x-app-icon name="eye" class="w-5 h-5 text-[#DFC99E]" />
                    <span>{{ $isEn ? 'Inspect Live Sample' : 'معاينة نموذج زفاف حقيقي' }}</span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.marketing>
