@php
    $isEn = app()->getLocale() === 'en';
    $saudiSlugs = $saudiSignatureSlugs ?? \App\Support\TemplateStyleCatalog::saudiSignatureSlugs();
    $activeCollection = $collection ?? request('collection');
@endphp

<x-layouts.marketing :title="$isEn ? 'Wedding Invitation Templates — Zaffe' : 'معرض تصاميم وقوالب دعوات الزفاف — زفّة'">
    <div class="bg-[#FAF7F2] py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <!-- Header -->
            <div class="max-w-3xl space-y-3">
                <h1 class="font-cairo text-3xl sm:text-5xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Wedding Invitation Templates' : 'معرض قوالب وتصاميم الزفاف' }}
                </h1>
                <p class="text-base text-[#52493F] max-w-2xl leading-relaxed">
                    {{ $isEn
                        ? 'Explore fourteen refined designs crafted for Saudi and MENA celebrations — including our Saudi Signature Collection (Vol. 1). Preview every theme on mobile and desktop before customizing.'
                        : 'استكشف ١٤ تصميماً فاخراً صُممت بعناية للأعراس السعودية والعربية، بما فيها مجموعة التوقيع السعودي — الإصدار الأول. عاين كل قالب على الجوال وسطح المكتب مجاناً قبل الاختيار.' }}
                </p>
            </div>

            <!-- Saudi Signature Collection Vol. 1 Curated Banner -->
            <div class="rounded-2xl p-6 sm:p-8 bg-obsidian-royal text-[#FAF7F2] border border-[#C6A76B]/40 shadow-royal-card flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2.5 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C6A76B]/15 border border-[#C6A76B]/40 text-[#DFC99E] text-xs font-bold">
                        <x-app-icon name="crown" class="w-3.5 h-3.5" />
                        <span>{{ $isEn ? 'Saudi Signature Collection — Vol. 1' : 'مجموعة التوقيع السعودي — الإصدار الأول' }}</span>
                    </div>
                    <h2 class="font-cairo text-2xl sm:text-3xl font-black text-white leading-snug">
                        {{ $isEn ? 'Eight Original Heritage & Editorial Designs' : 'ثمانية تصاميم أصلية مستوحاة من التراث والذوق السعودي المعاصر' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[#E4D9C8]/85 leading-relaxed">
                        {{ $isEn
                            ? 'Inspired by Diriyah Najdi geometry, Historic Jeddah Rawashin, Al Sadu weaving, Taif roses, oasis palms, Red Sea pearl, desert dusk, and couture monograms — 100% free in Release 1.'
                            : 'تصاميم مستوحاة من الدرعية والرواشين الحجازية ونقوش السدو وورد الطائف وواحات النخيل ولؤلؤ البحر الأحمر وغروب الصحراء والمونوغرام العصري — مجانية بالكامل في الإطلاق الأول.' }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    @if($activeCollection === 'saudi-signature')
                        <a href="{{ route('templates.index') }}" class="btn-ghost-ivory">
                            <span>{{ $isEn ? 'View All 14 Templates' : 'عرض جميع التصاميم (١٤)' }}</span>
                        </a>
                    @else
                        <a href="{{ route('templates.index', ['collection' => 'saudi-signature']) }}" class="btn-royal">
                            <x-app-icon name="sparkles" class="w-4 h-4" />
                            <span>{{ $isEn ? 'Explore Saudi Signature (8)' : 'استعراض التوقيع السعودي (٨)' }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Search & Category Filters Bar -->
            <div class="card-atelier p-4 sm:p-6 space-y-4">
                <form method="GET" action="{{ route('templates.index') }}" class="flex flex-col sm:flex-row gap-3">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if($activeCollection)
                        <input type="hidden" name="collection" value="{{ $activeCollection }}">
                    @endif
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 start-3.5 flex items-center text-[#6E6458]">
                            <x-app-icon name="search" class="w-4 h-4" />
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               aria-label="{{ $isEn ? 'Search templates' : 'ابحث في القوالب' }}"
                               placeholder="{{ $isEn ? 'Search template name or style...' : 'ابحث عن اسم القالب أو الطراز...' }}"
                               class="input-atelier !ps-10">
                    </div>
                    <button type="submit" class="btn-obsidian shrink-0">
                        {{ $isEn ? 'Search' : 'بحث في القوالب' }}
                    </button>
                    @if(request('search') || request('category') || $activeCollection)
                        <a href="{{ route('templates.index') }}" class="btn-ghost-ivory shrink-0">
                            {{ $isEn ? 'Reset' : 'إعادة التعيين' }}
                        </a>
                    @endif
                </form>

                <!-- Collection & Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs sm:text-sm">
                    <a href="{{ route('templates.index', ['search' => request('search')]) }}"
                       class="px-4 py-2 rounded-full font-bold whitespace-nowrap transition border {{ (!request('category') || request('category') === 'all') && !$activeCollection ? 'bg-[#221E1A] text-white border-[#221E1A]' : 'bg-[#FAF7F2] text-[#221E1A] border-[#E4D9C8] hover:border-[#C6A76B]' }}">
                        {{ $isEn ? 'All Designs' : 'جميع التصاميم' }} ({{ $templates->count() }})
                    </a>

                    <a href="{{ route('templates.index', ['collection' => 'saudi-signature', 'search' => request('search')]) }}"
                       class="px-4 py-2 rounded-full font-bold whitespace-nowrap transition border inline-flex items-center gap-1.5 {{ $activeCollection === 'saudi-signature' ? 'bg-[#7A5A20] text-white border-[#7A5A20]' : 'bg-[#F4EBD9] text-[#7A5A20] border-[#C6A76B]/50 hover:border-[#7A5A20]' }}">
                        <x-app-icon name="crown" class="w-3.5 h-3.5" />
                        <span>{{ $isEn ? 'Saudi Signature Vol. 1' : 'التوقيع السعودي · Vol. 1' }}</span>
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('templates.index', ['category' => $cat->slug, 'collection' => $activeCollection, 'search' => request('search')]) }}"
                           class="px-4 py-2 rounded-full font-bold whitespace-nowrap transition border {{ request('category') === $cat->slug ? 'bg-[#221E1A] text-white border-[#221E1A]' : 'bg-[#FAF7F2] text-[#221E1A] border-[#E4D9C8] hover:border-[#C6A76B]' }}">
                            {{ $isEn && $cat->name_en ? $cat->name_en : $cat->name_ar }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Templates Grid or Empty State -->
            @if($templates->isEmpty())
                <div class="card-atelier text-center py-14 px-6 max-w-lg mx-auto space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#F4EBD9] text-[#856326] flex items-center justify-center mx-auto">
                        <x-app-icon name="search" class="w-6 h-6" />
                    </div>
                    <h2 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'No matching templates found' : 'لم نعثر على قوالب مطابقة' }}
                    </h2>
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'Try clearing your search term or selecting another category.' : 'جرب تغيير كلمات البحث أو اختر تصنيفاً آخر لاستعراض التصاميم المتوفرة.' }}
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('templates.index') }}" class="btn-royal">
                            {{ $isEn ? 'Show All Templates' : 'عرض جميع القوالب' }}
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-7 lg:gap-8">
                    @foreach($templates as $template)
                        @php
                            $primaryColor = $template->default_styles['primary_color'] ?? '#C6A76B';
                            $isSaudiSignatureCard = in_array($template->slug, $saudiSlugs, true);
                            $cardTitle = $isEn && $template->name_en ? $template->name_en : $template->name_ar;
                            $cardBadge = $isSaudiSignatureCard
                                ? ($isEn ? 'Saudi Signature' : 'التوقيع السعودي')
                                : ($isEn ? ($template->category?->name_en ?? 'Collection') : ($template->category?->name_ar ?? 'تصميم'));
                        @endphp
                        <article class="template-gallery-card overflow-hidden flex flex-col group">
                            <!-- Framed Stationery Preview Mat -->
                            <a href="{{ route('templates.preview', $template->slug) }}"
                               aria-label="{{ $isEn ? 'Preview ' . $cardTitle : 'معاينة تصميم ' . $cardTitle }}"
                               class="template-gallery-stage relative py-5 px-4 sm:py-6 sm:px-5 flex items-center justify-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#B59556]">
                                <div class="template-gallery-sheet relative w-[74%] aspect-[5/7] overflow-hidden bg-[#221E1A] ring-1 ring-[#221E1A]/10">
                                    <!-- Graceful Fallback (Gradient + Template Name) -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center p-5 text-center"
                                         style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                        <div class="w-10 h-10 rounded-full bg-white/10 border border-white/25 flex items-center justify-center text-white mb-2">
                                            <x-app-icon name="sparkles" class="w-5 h-5" />
                                        </div>
                                        <span class="font-cairo text-sm font-bold text-white">{{ $template->name_ar }}</span>
                                        @if($template->name_en)
                                            <span class="text-[11px] font-medium text-white/80 mt-0.5" dir="ltr">{{ $template->name_en }}</span>
                                        @endif
                                    </div>

                                    @if($template->thumbnail)
                                        <img src="{{ $template->thumbnail }}"
                                             alt=""
                                             width="540"
                                             height="756"
                                             loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}"
                                             decoding="async"
                                             onerror="this.style.display='none'"
                                             class="relative z-10 w-full h-full object-cover object-top">
                                    @endif
                                </div>
                            </a>

                            <!-- Minimal Luxury Card Footer -->
                            <div class="px-5 pt-4 pb-5 flex-1 flex flex-col justify-between gap-3.5">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2.5">
                                        <h2 class="font-cairo text-[17px] font-extrabold text-[#221E1A] leading-snug truncate">
                                            {{ $cardTitle }}
                                        </h2>
                                        @if($isSaudiSignatureCard)
                                            <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#F4EBD9]/85 text-[#7A5A20] border border-[#C6A76B]/35">
                                                <x-app-icon name="crown" class="w-3 h-3" />
                                                <span>{{ $cardBadge }}</span>
                                            </span>
                                        @else
                                            <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF7F2] text-[#6E6458] border border-[#E4D9C8]">
                                                {{ $cardBadge }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($isEn)
                                        <p class="text-xs font-medium text-[#6E6458] leading-tight truncate" dir="rtl">{{ $template->name_ar }}</p>
                                    @elseif($template->name_en)
                                        <p class="text-xs font-medium text-[#6E6458] leading-tight truncate" dir="ltr">{{ $template->name_en }}</p>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2.5 pt-0.5">
                                    <a href="{{ route('templates.preview', $template->slug) }}"
                                       class="btn-ghost-ivory !min-h-[44px] !py-2 !px-3 !text-xs whitespace-nowrap">
                                        <x-app-icon name="eye" class="w-4 h-4 text-[#7A5A20] shrink-0" />
                                        <span>{{ $isEn ? 'Preview' : 'معاينة التصميم' }}</span>
                                    </a>

                                    <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}"
                                       class="btn-obsidian !min-h-[44px] !py-2 !px-3 !text-xs whitespace-nowrap">
                                        <x-app-icon name="sparkles" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                                        <span>{{ $isEn ? 'Use Template' : 'استخدم التصميم' }}</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.marketing>
