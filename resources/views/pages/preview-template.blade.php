<x-layouts.base :title="'معاينة: ' . $template->name_ar . ' — زفّة'">
    <div class="min-h-screen bg-[#292521] flex flex-col" x-data="{ device: 'mobile' }">
        <!-- Top Toolbar -->
        <header class="h-16 bg-[#1F1C19] border-b border-[#C6A76B]/30 px-4 sm:px-8 flex items-center justify-between text-white z-50 shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('templates.index') }}" class="flex items-center gap-2 text-xs font-bold text-[#E8DED0]/80 hover:text-white transition">
                    <x-app-icon name="arrow-right" class="w-4 h-4 text-[#C6A76B]" />
                    <span class="hidden sm:inline">العودة لمعرض القوالب</span>
                </a>
                <div class="h-4 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold font-cairo text-white">{{ $template->name_ar }}</span>
                    <span class="text-xs bg-[#C6A76B]/20 text-[#C6A76B] px-2.5 py-0.5 rounded-full border border-[#C6A76B]/30 hidden sm:inline">
                        {{ $template->category?->name_ar }}
                    </span>
                </div>
            </div>

            <!-- Viewport Switcher -->
            <div class="flex items-center bg-white/10 p-1 rounded-xl border border-white/10">
                <button @click="device = 'mobile'" 
                        :class="device === 'mobile' ? 'bg-[#C6A76B] text-white shadow-sm' : 'text-[#E8DED0]/70 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                    <span>📱 هاتف</span>
                </button>
                <button @click="device = 'desktop'" 
                        :class="device === 'desktop' ? 'bg-[#C6A76B] text-white shadow-sm' : 'text-[#E8DED0]/70 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                    <span>💻 شاشة كاملة</span>
                </button>
            </div>

            <!-- Action CTA -->
            <div>
                <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-xs sm:text-sm font-bold shadow-md hover:opacity-95 transition">
                    <x-app-icon name="sparkles" class="w-4 h-4" />
                    <span>استخدم هذا التصميم</span>
                </a>
            </div>
        </header>

        <!-- Preview Canvas -->
        <main class="flex-1 overflow-auto flex items-center justify-center p-4 sm:p-6 bg-[#161413]">
            <!-- Container with dynamic width based on device toggle -->
            <div :class="device === 'mobile' ? 'max-w-md w-full my-6 rounded-[2.5rem] shadow-2xl border-4 border-[#3D3731] overflow-hidden' : 'w-full max-w-5xl rounded-3xl shadow-2xl border border-[#3D3731] overflow-hidden'"
                 class="transition-all duration-300 bg-[#FAF7F2]">
                
                @include($template->view_name, ['invitation' => $previewData, 'isPreview' => true])

            </div>
        </main>
    </div>
</x-layouts.base>
