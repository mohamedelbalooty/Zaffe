<x-layouts.dashboard :title="'إنشاء دعوة زفاف جديدة — زفّة'">
    <div class="max-w-4xl mx-auto space-y-8" x-data="{ selectedTemplate: '{{ $selectedTemplateId ?? ($templates->first()?->id ?? 1) }}' }">
        <div>
            <span class="text-xs font-bold text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30 inline-block mb-2">
                الخطوة الأولى
            </span>
            <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">صمم بطاقة زفافك الجديدة</h1>
            <p class="text-xs sm:text-sm text-[#292521]/60 mt-1">اختر القالب الأقرب لذوقك، ثم أدخل البيانات الأساسية وسننقلكم فوراً لمحرر التخصيص الكامل.</p>
        </div>

        <form method="POST" action="{{ route('dashboard.invitations.store') }}" class="space-y-8">
            @csrf

            <!-- 1. Choose Template -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E8DED0] shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8DED0]">
                    <h2 class="font-cairo text-lg font-bold text-[#292521]">١. اختر قالب التصميم</h2>
                    <span class="text-xs text-[#292521]/60">يمكنك تغيير القالب لاحقاً في أي وقت</span>
                </div>

                <input type="hidden" name="template_id" :value="selectedTemplate">

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($templates as $tmpl)
                        <div @click="selectedTemplate = '{{ $tmpl->id }}'"
                             :class="selectedTemplate == '{{ $tmpl->id }}' ? 'border-[#C6A76B] ring-2 ring-[#C6A76B]/30 bg-[#FAF7F2]' : 'border-[#E8DED0] hover:border-[#C6A76B]/40'"
                             class="rounded-2xl p-3 border-2 cursor-pointer transition flex flex-col gap-2 relative group">
                            
                            <div class="h-32 sm:h-40 rounded-xl overflow-hidden bg-zinc-100">
                                <img src="{{ $tmpl->thumbnail }}" alt="{{ $tmpl->name_ar }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="font-cairo text-xs font-bold text-[#292521] truncate">{{ $tmpl->name_ar }}</span>
                                <div :class="selectedTemplate == '{{ $tmpl->id }}' ? 'bg-[#C6A76B] text-white' : 'border border-gray-300 text-transparent'"
                                     class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold">
                                    ✓
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('template_id')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Basic Info -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E8DED0] shadow-sm space-y-5">
                <div class="pb-3 border-b border-[#E8DED0]">
                    <h2 class="font-cairo text-lg font-bold text-[#292521]">٢. بيانات العروسين وموعد الحفل</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="groom_name" class="block text-xs font-bold text-[#292521] mb-1.5">اسم العريس الكريم</label>
                        <input type="text" id="groom_name" name="groom_name" value="{{ old('groom_name') }}" required
                               placeholder="مثال: م. أحمد محمود"
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none">
                        @error('groom_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bride_name" class="block text-xs font-bold text-[#292521] mb-1.5">اسم العروس الكريمة</label>
                        <input type="text" id="bride_name" name="bride_name" value="{{ old('bride_name') }}" required
                               placeholder="مثال: د. سارة الشربيني"
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none">
                        @error('bride_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="wedding_date" class="block text-xs font-bold text-[#292521] mb-1.5">تاريخ حفل الزفاف</label>
                        <input type="date" id="wedding_date" name="wedding_date" value="{{ old('wedding_date', now()->addDays(30)->toDateString()) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none">
                        @error('wedding_date')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="venue_name" class="block text-xs font-bold text-[#292521] mb-1.5">اسم القاعة أو الفندق</label>
                        <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name') }}" required
                               placeholder="مثال: فندق فورسيزونز قاعة بلازا"
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/50 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none">
                        @error('venue_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end">
                    <button type="submit" 
                            class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:shadow-lg hover:opacity-95 transition flex items-center gap-2">
                        <span>متابعة إلى المحرر المباشر</span>
                        <x-app-icon name="arrow-left" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
