<x-layouts.dashboard :title="'لوحة التحكم — زفّة'">
    <div class="space-y-8">
        <!-- Welcome Greeting Banner -->
        <div class="bg-gradient-to-r from-[#292521] via-[#3A342E] to-[#292521] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl border border-[#C6A76B]/30">
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="text-xs font-bold text-[#C6A76B] bg-[#C6A76B]/20 px-3 py-1 rounded-full border border-[#C6A76B]/30 inline-block">
                        أهلاً بك في استوديو زفّــة ✨
                    </span>
                    <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-white">
                        مرحباً، {{ auth()->user()->name }}
                    </h1>
                    <p class="text-sm text-[#E8DED0]/80 max-w-xl font-normal">
                        تابع تفاصيل دعوات زفافك، تأكيدات الحضور الفورية، وتخصيص مظهر بطاقتك الرقمية بكل سهولة.
                    </p>
                </div>

                <a href="{{ route('dashboard.invitations.create') }}" 
                   class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:shadow-lg hover:opacity-95 transition flex items-center gap-2 shrink-0">
                    <x-app-icon name="plus" class="w-4 h-4" />
                    <span>إنشاء دعوة جديدة</span>
                </a>
            </div>
        </div>

        <!-- Metrics & Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Stat 1 -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DED0] shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-2xl bg-[#C6A76B]/15 text-[#C6A76B] flex items-center justify-center">
                    <x-app-icon name="heart" class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-bold text-[#292521]/60">إجمالي الدعوات</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-[#292521] block">{{ $totalInvitations }}</span>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DED0] shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <x-app-icon name="sparkles" class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-bold text-[#292521]/60">الدعوات المنشورة</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-emerald-600 block">{{ $publishedInvitations }}</span>
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DED0] shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <x-app-icon name="users" class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-bold text-[#292521]/60">ضيوف أكدوا الحضور</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-blue-600 block">{{ $confirmedAttendees }}</span>
                </div>
            </div>

            <!-- Stat 4 -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DED0] shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-500 flex items-center justify-center">
                    <x-app-icon name="x" class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-bold text-[#292521]/60">اعتذارات الحضور</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-rose-500 block">{{ $declinedCount }}</span>
                </div>
            </div>
        </div>

        <!-- Invitations List Section -->
        <div class="bg-white rounded-3xl border border-[#E8DED0] shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#E8DED0]">
                <div>
                    <h2 class="font-cairo text-xl font-bold text-[#292521]">دعوات الزفاف الخاصة بك</h2>
                    <p class="text-xs text-[#292521]/60 mt-0.5">تحكم في محتوى ومظهر ومشاركة بطاقاتك الرقمية</p>
                </div>
                <a href="{{ route('dashboard.invitations.index') }}" class="text-xs font-bold text-[#C6A76B] hover:underline flex items-center gap-1">
                    <span>عرض الكل</span>
                    <x-app-icon name="arrow-left" class="w-3.5 h-3.5" />
                </a>
            </div>

            @if($invitations->isEmpty())
                <div class="text-center py-12 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-[#FAF7F2] text-[#C6A76B] flex items-center justify-center mx-auto">
                        <x-app-icon name="heart" class="w-8 h-8" />
                    </div>
                    <h3 class="font-cairo text-lg font-bold text-[#292521]">لم تنشئ أي دعوة بعد</h3>
                    <p class="text-xs text-[#292521]/60 max-w-sm mx-auto">
                        ابدأ الآن باختيار قالبك المفضل وأدخل تفاصيل حفل الزفاف في دقائق معدودة.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('dashboard.invitations.create') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-xs font-bold shadow-md">
                            <x-app-icon name="plus" class="w-4 h-4" />
                            <span>صمم دعوتك الأولى الآن</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($invitations as $invitation)
                        <div class="p-5 rounded-2xl border border-[#E8DED0] bg-[#FAF7F2]/50 hover:bg-[#FAF7F2] transition flex flex-col justify-between space-y-4"
                             x-data="{ copied: false }">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-cairo text-lg font-bold text-[#292521]">
                                            {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
                                        </h3>
                                        @if($invitation->status === 'published')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">منشورة</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">مسودة</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#292521]/60">
                                        {{ $invitation->template?->name_ar }} • موعد الفرح: {{ $invitation->wedding_date->format('Y-m-d') }}
                                    </p>
                                </div>

                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-white shadow-sm">
                                    <img src="{{ $invitation->cover_photo ?: $invitation->template?->thumbnail }}" alt="Cover" class="w-full h-full object-cover">
                                </div>
                            </div>

                            <!-- Mini RSVP stats bar -->
                            <div class="p-3 rounded-xl bg-white border border-[#E8DED0] flex items-center justify-between text-xs">
                                <span class="text-[#292521]/70">تأكيدات الحضور:</span>
                                <div class="flex items-center gap-3 font-bold">
                                    <span class="text-emerald-600 font-cairo">✓ {{ $invitation->rsvps->where('attendance_status', 'attending')->sum('guest_count') }} فرد</span>
                                    <span class="text-rose-500 font-cairo">✕ {{ $invitation->rsvps->where('attendance_status', 'declined')->count() }}</span>
                                    <a href="{{ route('dashboard.invitations.guests', $invitation->id) }}" class="text-[#C6A76B] hover:underline font-semibold text-[11px]">
                                        إدارة الضيوف
                                    </a>
                                </div>
                            </div>

                            <!-- Actions Row -->
                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-[#E8DED0]/60">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}" 
                                       class="px-3.5 py-2 rounded-xl bg-[#292521] text-white text-xs font-bold flex items-center gap-1.5 hover:bg-[#3D3731] transition">
                                        <x-app-icon name="edit" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                        <span>تعديل بالمحرر</span>
                                    </a>

                                    @if($invitation->status === 'published')
                                        <a href="{{ $invitation->public_url }}" target="_blank"
                                           class="px-3 py-2 rounded-xl bg-white border border-[#E8DED0] text-[#292521] text-xs font-semibold hover:bg-white/80 transition flex items-center gap-1">
                                            <x-app-icon name="eye" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                            <span>معاينة الرابط</span>
                                        </a>
                                    @endif
                                </div>

                                @if($invitation->status === 'published')
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank"
                                           class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="مشاركة واتساب">
                                            <x-app-icon name="whatsapp" class="w-4 h-4" />
                                        </a>

                                        <button @click="navigator.clipboard.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                                class="p-2 rounded-xl bg-white border border-[#E8DED0] text-[#292521] hover:bg-white/80 transition" title="نسخ الرابط">
                                            <x-app-icon name="copy" class="w-4 h-4 text-[#C6A76B]" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
