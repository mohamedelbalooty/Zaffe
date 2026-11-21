@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.marketing :title="$isEn ? 'Pricing & Launch Offer — Zaffe' : 'باقات الأسعار — زفّة'">
    <div class="bg-[#FAF7F2] py-14 sm:py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <!-- Page Heading -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <h1 class="font-cairo text-3xl sm:text-5xl font-black text-[#221E1A] leading-tight">
                    {{ $isEn
                        ? 'Celebrate Your Wedding with Complete Zaffe Studio — 100% Free'
                        : 'باقات الأسعار: تجربة زفّة الكاملة مجاناً في الإصدار الأول' }}
                </h1>
                <p class="text-base text-[#52493F] leading-relaxed">
                    {{ $isEn
                        ? 'During Release 1 across Saudi Arabia and Egypt, every couple enjoys unlimited access to all 14 themes, view-first sharing, and optional RSVP tracking with zero fees or credit card.'
                        : 'بمناسبة الإطلاق الأول لمنصة زفّة في المملكة العربية السعودية ومصر، نهدي جميع العرسان وصولاً كاملاً لكافة القوالب الـ ١٤ والمميزات الأساسية مجاناً دون أي رسوم أو بطاقة ائتمان.' }}
                </p>
            </div>

            <!-- 100% Free Launch Banner -->
            <div class="rounded-2xl p-6 sm:p-8 bg-[#0F4C3A] text-white shadow-wedding border border-[#C6A76B]/35 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-start">
                    <h2 class="font-cairo text-xl sm:text-2xl font-extrabold text-white">
                        {{ $isEn ? '100% Free Launch Gift in Egypt & Saudi Arabia' : 'هدية الإطلاق المجاني ١٠٠٪ في مصر والسعودية' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-emerald-50/90 leading-relaxed max-w-2xl">
                        {{ $isEn
                            ? 'All 14 curated themes, custom colors and fonts, photo-free privacy, optional RSVP confirmations, and Excel/CSV exports are completely free in Release 1.'
                            : 'جميع القوالب الـ ١٤ الفاخرة، تخصيص الألوان والخطوط، الخصوصية بدون صور أو مع ألبوم صور محسّن، تأكيدات الحضور الاختيارية (RSVP)، وتصدير الضيوف إلى Excel/CSV متاحة مجاناً في الإصدار الأول.' }}
                    </p>
                </div>
                <a href="{{ auth()->check() ? route('dashboard.invitations.create') : route('register') }}"
                   class="btn-royal whitespace-nowrap shrink-0">
                    <span>{{ $isEn ? 'Start Designing Free' : 'ابدأ تصميم دعوتك مجاناً الآن' }}</span>
                    <x-app-icon name="arrow-left" class="w-4 h-4 dir-arrow" />
                </a>
            </div>

            <!-- Release 1 All-Access Card -->
            <div class="max-w-3xl mx-auto">
                <div class="rounded-2xl p-7 sm:p-10 bg-[#1B1815] text-white shadow-royal-card border border-[#C6A76B]/50 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-white/10">
                        <div class="space-y-1.5">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#DFC99E]">
                                <x-app-icon name="crown" class="w-4 h-4" />
                                <span>{{ $isEn ? 'Release 1 Open Access' : 'جميع المميزات مفتوحة مجاناً' }}</span>
                            </span>
                            <h3 class="font-cairo text-2xl font-extrabold text-white">
                                {{ $isEn ? 'All-Inclusive Royal Package' : 'الباقة الملكية الشاملة (الإطلاق المجاني)' }}
                            </h3>
                            <p class="text-xs text-[#E4D9C8]/75">
                                {{ $isEn ? 'No subscriptions, no hidden tiers, no locked templates.' : 'تجربة زفّة المتكاملة في السعودية ومصر بدون أي اشتراكات أو قيود' }}
                            </p>
                        </div>

                        <div class="flex items-baseline gap-2.5 shrink-0">
                            <span class="font-cairo text-4xl sm:text-5xl font-black text-[#DFC99E]">
                                {{ $isEn ? 'Free' : 'مجاناً' }}
                            </span>
                            <span class="text-xs font-bold text-emerald-200 bg-emerald-500/20 px-3 py-1 rounded-full border border-emerald-400/30 tabular-nums">
                                {{ $isEn ? '0 SAR / EGP' : '0 ج.م / ر.س' }}
                            </span>
                        </div>
                    </div>

                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-[#E4D9C8]">
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'All 14 luxury wedding themes unlocked' : 'جميع القوالب الـ ١٤ الفاخرة مفتوحة مجاناً' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'Full color palette & Arabic/Latin font studio' : 'تخصيص كامل للألوان والخطوط العربية' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'Optional bride name & photo-free privacy' : 'اسم العروس اختياري وخصوصية تامة بدون صور' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'View-first card with optional RSVP toggle' : 'بطاقة عرض متكاملة مع تفعيل اختياري للـ RSVP' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'Formula-safe Excel / CSV guest exports' : 'تصدير قائمة الضيوف الآمن إلى Excel / CSV' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#C6A76B]/20 text-[#DFC99E] flex items-center justify-center shrink-0">
                                <x-app-icon name="check" class="w-3.5 h-3.5" />
                            </span>
                            <span>{{ $isEn ? 'Riyadh/Cairo timezones & Umm al-Qura Hijri' : 'توقيت الرياض والقاهرة وتقويم أم القرى الهجري' }}</span>
                        </li>
                    </ul>

                    <div class="pt-2 space-y-3">
                        <a href="{{ auth()->check() ? route('dashboard.invitations.create') : route('register') }}"
                           class="btn-royal w-full !py-4 !text-base">
                            <span>{{ $isEn ? 'Start Designing Free Now' : 'ابدأ تصميم دعوتك مجاناً الآن' }}</span>
                            <x-app-icon name="arrow-left" class="w-4 h-4 dir-arrow" />
                        </a>
                        <p class="text-xs text-center text-[#E4D9C8]/75 leading-relaxed">
                            {{ $isEn
                                ? 'No credit card required • Release 1 is completely free (any future paid plans after Release 1 are not yet finalized and will never charge existing cards retroactively).'
                                : 'لا يتطلب بطاقة ائتمان • الإصدار الأول مجاني بالكامل بدون اشتراكات (لم تُحدد أي باقات مدفوعة مستقبلية بعد، ولن تُفرض رسوم بأثر رجعي على أي دعوة حالية).' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Concierge Support Note -->
            <div class="max-w-2xl mx-auto card-atelier p-5 flex items-center justify-center gap-3 text-xs sm:text-sm text-[#52493F] text-center">
                <x-app-icon name="shield-check" class="w-5 h-5 text-[#856326] shrink-0" />
                <span>
                    {{ $isEn
                        ? 'Full free concierge support: our team is ready on WhatsApp to assist you across Egypt and Saudi Arabia.'
                        : 'دعم مجاني كامل: إذا احتجت لأي مساعدة في تنسيق دعوتك أو مشاركتها، فريق زفّة متاح عبر واتساب لخدمتك في مصر والسعودية.' }}
                </span>
            </div>
        </div>
    </div>
</x-layouts.marketing>
