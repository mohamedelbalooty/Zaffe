@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.base :title="($isEn ? 'Preview: ' . ($template->name_en ?: $template->name_ar) : 'معاينة: ' . $template->name_ar) . ' — زفّة'">
    <div class="min-h-screen bg-[#1B1815] flex flex-col" x-data="{ device: 'mobile' }">
        <!-- Top Preview Studio Toolbar -->
        <header class="min-h-16 bg-[#24201C] border-b border-[#C6A76B]/25 px-3 sm:px-6 py-2 flex items-center justify-between gap-3 text-white z-50 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center gap-1.5 min-h-[40px] px-3 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/15 border border-white/10 text-xs font-bold text-[#E4D9C8] hover:text-white transition shrink-0">
                    <x-app-icon name="arrow-right" class="w-4 h-4 text-[#DFC99E] dir-arrow" />
                    <span class="hidden sm:inline">{{ $isEn ? 'Back to Templates' : 'العودة لمعرض القوالب' }}</span>
                </a>

                <div class="h-5 w-px bg-white/15 hidden sm:block shrink-0"></div>

                <div class="flex items-center gap-2 min-w-0">
                    <h1 class="text-sm sm:text-base font-extrabold font-cairo text-white truncate">
                        {{ $isEn && $template->name_en ? $template->name_en : $template->name_ar }}
                    </h1>
                    @if($template->category)
                        <span class="text-[11px] bg-[#C6A76B]/15 text-[#DFC99E] px-2.5 py-0.5 rounded-full border border-[#C6A76B]/30 hidden md:inline whitespace-nowrap">
                            {{ $isEn && $template->category->name_en ? $template->category->name_en : $template->category->name_ar }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- SVG Device Switcher -->
            <div class="flex items-center bg-black/35 p-1 rounded-xl border border-white/10 shrink-0">
                <button type="button"
                        @click="device = 'mobile'"
                        :class="device === 'mobile' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                    <x-app-icon name="smartphone" class="w-4 h-4" />
                    <span class="hidden sm:inline">{{ $isEn ? 'Mobile' : 'هاتف' }}</span>
                </button>
                <button type="button"
                        @click="device = 'tablet'"
                        :class="device === 'tablet' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold hidden sm:flex items-center gap-1.5 transition cursor-pointer">
                    <x-app-icon name="tablet" class="w-4 h-4" />
                    <span>{{ $isEn ? 'Tablet' : 'تابلت' }}</span>
                </button>
                <button type="button"
                        @click="device = 'desktop'"
                        :class="device === 'desktop' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                    <x-app-icon name="monitor" class="w-4 h-4" />
                    <span class="hidden sm:inline">{{ $isEn ? 'Full Screen' : 'شاشة كاملة' }}</span>
                </button>
            </div>

            <!-- Action CTA -->
            <div class="shrink-0">
                <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}"
                   class="btn-royal !min-h-[40px] !py-2 !px-4 !text-xs sm:!text-sm">
                    <x-app-icon name="sparkles" class="w-4 h-4" />
                    <span>{{ $isEn ? 'Use This Design' : 'استخدم هذا التصميم' }}</span>
                </a>
            </div>
        </header>

        <!-- Preview Stage -->
        <main id="main-content" class="flex-1 overflow-auto flex items-start justify-center p-4 sm:p-8 bg-studio-grid">
            <div :class="device === 'mobile' ? 'max-w-[26rem] w-full my-2 rounded-[2.25rem] shadow-2xl border-[6px] border-[#2E2924] overflow-hidden' : (device === 'tablet' ? 'max-w-2xl w-full my-2 rounded-3xl shadow-2xl border-[6px] border-[#2E2924] overflow-hidden' : 'w-full max-w-5xl rounded-2xl shadow-2xl border border-white/10 overflow-hidden')"
                 class="transition-all duration-300 bg-[#FAF7F2]">
                @include($template->view_name, ['invitation' => $previewData, 'isPreview' => true])
            </div>
        </main>
    </div>
</x-layouts.base>
