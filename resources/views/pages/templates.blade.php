@php
    $isEn = app()->getLocale() === 'en';
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
                        ? 'Explore six refined designs crafted for Arabian celebrations. Preview every theme on mobile and desktop before customizing.'
                        : 'استكشف قوالب فاخرة ومصممة بأدق التفاصيل لتناسب هوية يومك المميز، وعاينها قبل الاختيار.' }}
                </p>
            </div>

            <!-- Search & Category Filters Bar -->
            <div class="card-atelier p-4 sm:p-6 space-y-4">
                <form method="GET" action="{{ route('templates.index') }}" class="flex flex-col sm:flex-row gap-3">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
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
                    @if(request('search') || request('category'))
                        <a href="{{ route('templates.index') }}" class="btn-ghost-ivory shrink-0">
                            {{ $isEn ? 'Reset' : 'إعادة التعيين' }}
                        </a>
                    @endif
                </form>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs sm:text-sm">
                    <a href="{{ route('templates.index', ['search' => request('search')]) }}"
                       class="px-4 py-2 rounded-full font-bold whitespace-nowrap transition border {{ !request('category') || request('category') === 'all' ? 'bg-[#221E1A] text-white border-[#221E1A]' : 'bg-[#FAF7F2] text-[#221E1A] border-[#E4D9C8] hover:border-[#C6A76B]' }}">
                        {{ $isEn ? 'All Designs' : 'جميع التصاميم' }} ({{ $templates->count() }})
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('templates.index', ['category' => $cat->slug, 'search' => request('search')]) }}"
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                    @foreach($templates as $template)
                        @php
                            $primaryColor = $template->default_styles['primary_color'] ?? '#C6A76B';
                        @endphp
                        <article class="card-atelier overflow-hidden hover:border-[#C6A76B] transition-colors flex flex-col group">
                            <!-- Clean Preview Stage -->
                            <div class="relative h-60 overflow-hidden bg-[#221E1A] border-b border-[#E4D9C8]">
                                <!-- Graceful Fallback (Gradient + Template Name) -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center"
                                     style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                    <div class="w-11 h-11 rounded-full bg-white/10 border border-white/25 flex items-center justify-center text-white mb-2.5">
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
                                    {{ $isEn ? ($template->category?->name_en ?? 'Collection') : ($template->category?->name_ar ?? 'تصميم') }}
                                </div>

                                @if($template->is_premium)
                                    <div class="absolute top-3 end-3 z-20 bg-[#221E1A] text-[#DFC99E] px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1 shadow-sm">
                                        <x-app-icon name="crown" class="w-3 h-3" />
                                        <span>{{ $isEn ? 'Royal' : 'ملكي' }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                                <div class="space-y-2">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <h2 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                                            {{ $template->name_ar }}
                                        </h2>
                                        @if($template->name_en)
                                            <span class="text-[11px] font-medium text-[#6E6458] shrink-0" dir="ltr">{{ $template->name_en }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm text-[#52493F] leading-relaxed">
                                        {{ $isEn && $template->description_en ? $template->description_en : $template->description_ar }}
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between text-xs text-[#52493F] pt-3 border-t border-[#E4D9C8]">
                                        <span class="flex items-center gap-1.5 font-semibold">
                                            <span class="w-3.5 h-3.5 rounded-full border border-black/15" style="background-color: {{ $primaryColor }};"></span>
                                            <span>{{ $isEn ? 'Primary Accent' : 'اللون الأساسي' }}</span>
                                        </span>
                                        <span class="flex items-center gap-1.5 font-semibold">
                                            <x-app-icon name="type" class="w-3.5 h-3.5 text-[#856326]" />
                                            <span>{{ $isEn ? 'Font:' : 'خط' }} {{ $template->default_styles['font_family'] ?? 'Tajawal' }}</span>
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
                                            <span>{{ $isEn ? 'Use Design' : 'استخدم هذا التصميم' }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.marketing>
