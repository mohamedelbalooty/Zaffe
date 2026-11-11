<x-layouts.dashboard :title="'إدارة دعواتي — زفّة'">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">دعوات الزفاف الخاصة بك</h1>
                <p class="text-xs sm:text-sm text-[#292521]/60 mt-1">تعديل التصاميم، متابعة الحضور، ومشاركة الروابط مع ضيوفك</p>
            </div>
            <a href="{{ route('dashboard.invitations.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-xs sm:text-sm shadow-md hover:opacity-95 transition flex items-center gap-2">
                <x-app-icon name="plus" class="w-4 h-4" />
                <span>إنشاء دعوة جديدة</span>
            </a>
        </div>

        @if($invitations->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-[#E8DED0] space-y-4">
                <div class="w-16 h-16 rounded-full bg-[#FAF7F2] text-[#C6A76B] flex items-center justify-center mx-auto">
                    <x-app-icon name="heart" class="w-8 h-8" />
                </div>
                <h3 class="font-cairo text-lg font-bold text-[#292521]">ليس لديك دعوات حالياً</h3>
                <p class="text-xs text-[#292521]/60 max-w-sm mx-auto">
                    اختر قالباً ملكياً وابدأ بإدخال بيانات فرحك لتجهيز دعوتك الأولى في دقائق.
                </p>
                <div class="pt-2">
                    <a href="{{ route('dashboard.invitations.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-xs font-bold shadow-md">
                        <x-app-icon name="plus" class="w-4 h-4" />
                        <span>إنشاء أول دعوة</span>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($invitations as $invitation)
                    <div class="bg-white rounded-3xl p-6 border border-[#E8DED0] shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:border-[#C6A76B]/40 transition"
                         x-data="{ copied: false, showDelete: false }">
                        
                        <!-- Left Info -->
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0 border border-[#C6A76B]/30 shadow-sm">
                                <img src="{{ $invitation->cover_photo ?: $invitation->template?->thumbnail }}" alt="Cover" class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-cairo text-lg font-bold text-[#292521]">
                                        {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
                                    </h3>
                                    @if($invitation->status === 'published')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>منشورة</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">مسودة</span>
                                    @endif
                                </div>
                                <div class="text-xs text-[#292521]/60 flex flex-wrap items-center gap-3">
                                    <span>القالب: {{ $invitation->template?->name_ar }}</span>
                                    <span>•</span>
                                    <span>الموعد: {{ $invitation->wedding_date->format('Y-m-d') }}</span>
                                    <span>•</span>
                                    <span>المكان: {{ $invitation->venue_name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- RSVP Stats Badge -->
                        <div class="flex items-center gap-3 bg-[#FAF7F2] px-4 py-2 rounded-2xl border border-[#E8DED0] text-xs">
                            <span class="text-[#292521]/70 font-medium">الحضور:</span>
                            <span class="text-emerald-700 font-bold font-cairo">✓ {{ $invitation->rsvps->where('attendance_status', 'attending')->sum('guest_count') }} فرد</span>
                            <span class="text-rose-600 font-bold font-cairo">✕ {{ $invitation->rsvps->where('attendance_status', 'declined')->count() }}</span>
                            <a href="{{ route('dashboard.invitations.guests', $invitation->id) }}" class="text-[#C6A76B] font-bold hover:underline">
                                القائمة الكاملة
                            </a>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="flex items-center gap-2 self-end md:self-center">
                            <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}" 
                               class="px-3.5 py-2 rounded-xl bg-[#292521] text-white text-xs font-bold hover:bg-[#3D3731] transition flex items-center gap-1.5">
                                <x-app-icon name="edit" class="w-3.5 h-3.5 text-[#C6A76B]" />
                                <span>المحرر المباشر</span>
                            </a>

                            @if($invitation->status === 'published')
                                <a href="{{ $invitation->public_url }}" target="_blank"
                                   class="p-2 rounded-xl bg-[#FAF7F2] border border-[#E8DED0] text-[#292521] hover:bg-white transition" title="عرض الدعوة">
                                    <x-app-icon name="eye" class="w-4 h-4 text-[#C6A76B]" />
                                </a>

                                <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank"
                                   class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="مشاركة واتساب">
                                    <x-app-icon name="whatsapp" class="w-4 h-4" />
                                </a>

                                <button @click="navigator.clipboard.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                        class="p-2 rounded-xl bg-[#FAF7F2] border border-[#E8DED0] text-[#292521] hover:bg-white transition" title="نسخ الرابط">
                                    <x-app-icon name="copy" class="w-4 h-4 text-[#C6A76B]" />
                                </button>
                            @endif

                            <button @click="showDelete = true" 
                                    class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="حذف الدعوة">
                                <x-app-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal (Alpine.js) -->
                        <div x-show="showDelete" 
                             @click.away="showDelete = false"
                             class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
                             style="display: none;">
                            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center space-y-4 shadow-2xl">
                                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                                    <x-app-icon name="trash" class="w-6 h-6" />
                                </div>
                                <h3 class="font-cairo text-lg font-bold text-[#292521]">تأكيد حذف الدعوة</h3>
                                <p class="text-xs text-[#292521]/70 leading-relaxed">
                                    هل أنت متأكد من رغبتك في حذف دعوة «{{ $invitation->title }}»؟ سيتم حذف جميع بيانات وتأكيدات الحضور المرتبطة بها نهائياً.
                                </p>
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <button @click="showDelete = false" class="py-2.5 rounded-xl border border-[#E8DED0] text-xs font-bold text-[#292521]">
                                        إلغاء
                                    </button>
                                    <form method="POST" action="{{ route('dashboard.invitations.destroy', $invitation->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition">
                                            نعم، احذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $invitations->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.dashboard>
