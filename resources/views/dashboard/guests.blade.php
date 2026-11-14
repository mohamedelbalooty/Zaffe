<x-layouts.dashboard :title="'إدارة ضيوف وتأكيدات حضور: ' . $invitation->title . ' — زفّة'">
    <div class="space-y-6">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-[#E8DED0]">
            <div>
                <div class="flex items-center gap-2 text-xs text-[#292521]/60 mb-1">
                    <a href="{{ route('dashboard.invitations.index') }}" class="hover:underline">دعواتي</a>
                    <span>/</span>
                    <span>تأكيدات الحضور</span>
                </div>
                <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">
                    سجل ضيوف: {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
                </h1>
            </div>

            <!-- Export to CSV Button -->
            <a href="{{ route('dashboard.invitations.guests.export', $invitation->id) }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs sm:text-sm shadow-md hover:bg-emerald-700 transition flex items-center gap-2">
                <x-app-icon name="download" class="w-4 h-4" />
                <span>تصدير قائمة الضيوف (Excel/CSV)</span>
            </a>
        </div>

        <!-- RSVP Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-[#E8DED0] shadow-sm">
                <span class="text-xs font-bold text-[#292521]/60 block mb-1">إجمالي الردود المستلمة</span>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-[#292521]">{{ $stats['total'] }}</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-[#E8DED0] shadow-sm">
                <span class="text-xs font-bold text-[#292521]/60 block mb-1">عدد العائلات الحاضرة</span>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['attending'] }}</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-[#E8DED0] shadow-sm">
                <span class="text-xs font-bold text-[#292521]/60 block mb-1">إجمالي الأفراد والمرافقين</span>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-blue-600">{{ $stats['total_guests'] }} فرد</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-[#E8DED0] shadow-sm">
                <span class="text-xs font-bold text-[#292521]/60 block mb-1">عدد الاعتذارات</span>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-rose-500">{{ $stats['declined'] }}</span>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-3xl p-5 border border-[#E8DED0] shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('dashboard.invitations.guests', $invitation->id) }}" class="flex-1 w-full flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="ابحث باسم الضيف، رقم الهاتف، أو نص التهنئة..."
                       class="w-full px-4 py-2.5 rounded-xl border border-[#E8DED0] text-xs sm:text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#292521] text-white text-xs font-bold hover:bg-[#3D3731] transition shrink-0">
                    بحث
                </button>
            </form>

            <!-- Filter Status Pills -->
            <div class="flex items-center gap-1.5 self-start sm:self-center shrink-0 text-xs font-bold">
                <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ !request('status') ? 'bg-[#C6A76B] text-white' : 'bg-[#FAF7F2] text-[#292521]/70 hover:bg-[#E8DED0]' }}">
                    الكل ({{ $stats['total'] }})
                </a>
                <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'status' => 'attending', 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'attending' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    حضور ({{ $stats['attending'] }})
                </a>
                <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'status' => 'declined', 'search' => request('search')]) }}" 
                   class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'declined' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    اعتذار ({{ $stats['declined'] }})
                </a>
            </div>
        </div>

        <!-- Guests Table -->
        <div class="bg-white rounded-3xl border border-[#E8DED0] shadow-sm overflow-hidden">
            @if($rsvps->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-[#FAF7F2] text-[#C6A76B] flex items-center justify-center mx-auto">
                        <x-app-icon name="users" class="w-6 h-6" />
                    </div>
                    <h3 class="font-cairo text-base font-bold text-[#292521]">لا توجد ردود حضور مسجلة حتى الآن</h3>
                    <p class="text-xs text-[#292521]/60">شارك رابط دعوتك عبر واتساب لتصلك تأكيدات حضور ضيوفك فوراً.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#FAF7F2] border-b border-[#E8DED0] text-[#292521]/70 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">اسم الضيف / العائلة</th>
                                <th class="px-6 py-4">الهاتف / التواصل</th>
                                <th class="px-6 py-4">حالة الحضور</th>
                                <th class="px-6 py-4">عدد الأفراد</th>
                                <th class="px-6 py-4">التهنئة أو الملاحظات</th>
                                <th class="px-6 py-4">تاريخ الإرسال</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8DED0]/60">
                            @foreach($rsvps as $rsvp)
                                <tr class="hover:bg-[#FAF7F2]/50 transition">
                                    <td class="px-6 py-4 font-bold text-sm text-[#292521]">
                                        {{ $rsvp->guest_name }}
                                    </td>
                                    <td class="px-6 py-4 text-[#292521]/70 font-mono" dir="ltr">
                                        {{ $rsvp->phone ? \App\Services\PhoneService::formatForDisplay($rsvp->phone) : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($rsvp->attendance_status === 'attending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                <span>✓</span>
                                                <span>حضور مؤكد</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                <span>✕</span>
                                                <span>اعتذر عن الحضور</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-sm text-[#292521]">
                                        {{ $rsvp->guest_count }}
                                    </td>
                                    <td class="px-6 py-4 text-[#292521]/80 max-w-xs">
                                        @if($rsvp->dietary_notes)
                                            <span class="inline-block text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md mb-1">
                                                🍽️ {{ $rsvp->dietary_notes }}
                                            </span>
                                        @endif
                                        <div class="truncate" title="{{ $rsvp->message }}">{{ $rsvp->message ?: '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-[#292521]/60 whitespace-nowrap">
                                        {{ $rsvp->created_at->translatedFormat('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-[#E8DED0]">
                    {{ $rsvps->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
