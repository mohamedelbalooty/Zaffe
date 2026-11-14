<x-layouts.marketing :title="'معرض تصاميم وقوالب دعوات الزفاف — زفّة'">
    <div class="bg-[#FAF7F2] py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    تشكيلة زفّــة الحصرية
                </span>
                <h1 class="font-cairo text-3xl sm:text-5xl font-extrabold text-[#292521] mt-3">
                    معرض قوالب وتصاميم الزفاف
                </h1>
                <p class="text-base text-[#292521]/70 mt-3">
                    استكشف قوالب فاخرة ومصممة بأدق التفاصيل لتناسب هوية يومك المميز، وعاينها قبل الاختيار
                </p>
            </div>

            <!-- Search & Category Filters Bar -->
            <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-[#E8DED0] mb-12 space-y-5">
                <!-- Search input -->
                <form method="GET" action="{{ route('templates.index') }}" class="flex flex-col sm:flex-row gap-3">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="ابحث عن اسم القالب أو الطراز..."
                               class="w-full px-5 py-3.5 rounded-2xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none text-sm bg-[#FAF7F2]/50">
                    </div>
                    <button type="submit" class="px-7 py-3.5 rounded-2xl bg-[#292521] text-white text-sm font-bold hover:bg-[#3D3731] transition shrink-0">
                        بحث في القوالب
                    </button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('templates.index') }}" class="px-5 py-3.5 rounded-2xl bg-[#FAF7F2] text-[#292521] text-sm font-semibold hover:bg-[#E8DED0] transition text-center shrink-0">
                            إعادة التعيين
                        </a>
                    @endif
                </form>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-sm">
                    <a href="{{ route('templates.index', ['search' => request('search')]) }}" 
                       class="px-5 py-2 rounded-full font-bold whitespace-nowrap transition {{ !request('category') || request('category') === 'all' ? 'bg-[#C6A76B] text-white shadow-sm' : 'bg-[#FAF7F2] text-[#292521]/80 hover:bg-[#E8DED0]' }}">
                        جميع التصاميم ({{ $templates->count() }})
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('templates.index', ['category' => $cat->slug, 'search' => request('search')]) }}" 
                           class="px-5 py-2 rounded-full font-bold whitespace-nowrap transition {{ request('category') === $cat->slug ? 'bg-[#C6A76B] text-white shadow-sm' : 'bg-[#FAF7F2] text-[#292521]/80 hover:bg-[#E8DED0]' }}">
                            {{ $cat->name_ar }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Templates List -->
            @if($templates->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-[#E8DED0] p-8 max-w-lg mx-auto">
                    <div class="w-16 h-16 rounded-full bg-[#FAF7F2] text-[#C6A76B] flex items-center justify-center mx-auto mb-4">
                        <x-app-icon name="sparkles" class="w-8 h-8" />
                    </div>
                    <h3 class="font-cairo text-xl font-bold text-[#292521] mb-2">لم نعثر على قوالب مطابقة</h3>
                    <p class="text-sm text-[#292521]/60 mb-6">جرب تغيير كلمات البحث أو اختر تصنيفاً آخر لاستعراض التصاميم المتوفرة.</p>
                    <a href="{{ route('templates.index') }}" class="px-6 py-2.5 rounded-full bg-[#C6A76B] text-white text-sm font-bold shadow-sm">
                        عرض جميع القوالب
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($templates as $template)
                        <div class="bg-white rounded-3xl overflow-hidden border border-[#C6A76B]/25 shadow-wedding hover:shadow-wedding-hover transition-all flex flex-col group">
                            <!-- Card Preview Image -->
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
                                
                                <div class="absolute inset-0 z-20 pointer-events-none bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>

                                <div class="absolute top-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-[#292521] shadow-sm">
                                    {{ $template->category?->name_ar }}
                                </div>

                                @if($template->is_premium)
                                    <div class="absolute top-4 left-4 z-20 bg-gradient-to-r from-amber-500 to-amber-600 text-white px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow-md">
                                        <x-app-icon name="crown" class="w-3 h-3" />
                                        <span>ملكي</span>
                                    </div>
                                @endif

                                <div class="absolute bottom-4 right-4 left-4 z-20 text-white">
                                    <h3 class="font-cairo text-xl font-bold leading-snug">{{ $template->name_ar }}</h3>
                                    @if($template->name_en)
                                        <span class="block text-[11px] font-medium text-white/75 tracking-wide" dir="ltr">{{ $template->name_en }}</span>
                                    @endif
                                    <p class="text-xs text-white/80 line-clamp-1 mt-1">{{ $template->description_ar }}</p>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <p class="text-xs text-[#292521]/75 leading-relaxed">
                                        {{ $template->description_ar }}
                                    </p>
                                    <div class="flex items-center gap-3 pt-2 text-xs text-[#292521]/60">
                                        <span class="flex items-center gap-1">
                                            <span class="w-3 h-3 rounded-full border border-gray-300" style="background-color: {{ $template->default_styles['primary_color'] ?? '#C6A76B' }};"></span>
                                            <span>اللون الأساسي</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <x-app-icon name="sparkles" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                            <span>خط {{ $template->default_styles['font_family'] ?? 'Tajawal' }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-[#E8DED0]">
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
            @endif
        </div>
    </div>
</x-layouts.marketing>
