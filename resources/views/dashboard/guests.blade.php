@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.dashboard :title="($isEn ? 'Guests & RSVPs: ' : 'إدارة ضيوف وتأكيدات حضور: ') . $invitation->title . ' — زفّة'">
    <div class="space-y-8">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-5 border-b border-[#E4D9C8]">
            <div class="space-y-1">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-semibold text-[#52493F]">
                    <a href="{{ route('dashboard.invitations.index') }}" class="hover:text-[#7A5A20] hover:underline">
                        {{ $isEn ? 'My Invitations' : 'دعواتي' }}
                    </a>
                    <span>/</span>
                    <span>{{ $isEn ? 'Guests & RSVPs' : 'سجل الضيوف وتأكيدات الحضور' }}</span>
                </nav>
                <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Guest List: ' : 'سجل ضيوف: ' }}{{ $invitation->groom_name }} &amp; {{ $invitation->bride_name }}
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}"
                   class="btn-ghost-ivory !min-h-[40px] !py-2 !px-4 !text-xs">
                    <x-app-icon name="edit" class="w-4 h-4 text-[#856326]" />
                    <span>{{ $isEn ? 'Edit Card' : 'تعديل الدعوة' }}</span>
                </a>
                <a href="{{ route('dashboard.invitations.guests.export', $invitation->id) }}"
                   class="inline-flex items-center gap-2 min-h-[40px] px-4 py-2 rounded-xl bg-[#0F4C3A] text-white font-bold text-xs shadow-sm hover:bg-[#0B3A2C] transition">
                    <x-app-icon name="download" class="w-4 h-4" />
                    <span>{{ $isEn ? 'Export RSVP Sheet (CSV)' : 'تصدير قائمة الضيوف (Excel/CSV)' }}</span>
                </a>
            </div>
        </div>

        <!-- Section 1: Smart Pre-Invited Guest Roster & Personal QR Passes -->
        <livewire:guest-roster-manager :invitation-id="$invitation->id" />

        <!-- Section 2: Submitted RSVP Responses Log -->
        <section class="space-y-5 pt-4 border-t border-[#E4D9C8]">
            <div>
                <h2 class="font-cairo text-xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Submitted RSVP Responses' : 'سجل ردود تأكيد الحضور المستلمة (RSVP)' }}
                </h2>
                <p class="text-xs text-[#52493F] mt-0.5">
                    {{ $isEn ? 'Confirmed guests, accompanying companions, dietary preferences, and couple wishes.' : 'جميع الردود المرسلة عبر نموذج تأكيد الحضور مع أعداد المرافقين والتهاني والملاحظات الغذائية.' }}
                </p>
            </div>

            <!-- RSVP Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card-atelier p-4 sm:p-5">
                    <span class="text-xs font-bold text-[#52493F] block mb-1">{{ $isEn ? 'Total Submissions' : 'إجمالي الردود المستلمة' }}</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A] tabular-nums">{{ $stats['total'] }}</span>
                </div>

                <div class="card-atelier p-4 sm:p-5">
                    <span class="text-xs font-bold text-[#52493F] block mb-1">{{ $isEn ? 'Attending Parties' : 'عدد العائلات الحاضرة' }}</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-[#0F4C3A] tabular-nums">{{ $stats['attending'] }}</span>
                </div>

                <div class="card-atelier p-4 sm:p-5">
                    <span class="text-xs font-bold text-[#52493F] block mb-1">{{ $isEn ? 'Total Attending Heads' : 'إجمالي الأفراد والمرافقين' }}</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-[#7A5A20] tabular-nums">{{ $stats['total_guests'] }} {{ $isEn ? 'guests' : 'فرد' }}</span>
                </div>

                <div class="card-atelier p-4 sm:p-5">
                    <span class="text-xs font-bold text-[#52493F] block mb-1">{{ $isEn ? 'Declined' : 'عدد الاعتذارات' }}</span>
                    <span class="font-cairo text-2xl sm:text-3xl font-black text-[#BE123C] tabular-nums">{{ $stats['declined'] }}</span>
                </div>
            </div>

            <!-- Search & Status Filter Bar -->
            <div class="card-atelier p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('dashboard.invitations.guests', $invitation->id) }}" class="flex-1 flex items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-[#6E6458]">
                            <x-app-icon name="search" class="w-4 h-4" />
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               aria-label="{{ $isEn ? 'Search RSVPs' : 'بحث في ردود الضيوف' }}"
                               placeholder="{{ $isEn ? 'Search guest name, phone, or greeting...' : 'ابحث باسم الضيف، رقم الهاتف، أو نص التهنئة...' }}"
                               class="input-atelier !min-h-[42px] !ps-9 !text-xs sm:!text-sm">
                    </div>
                    <button type="submit" class="btn-obsidian !min-h-[42px] !py-2 !px-4 !text-xs shrink-0">
                        {{ $isEn ? 'Search' : 'بحث' }}
                    </button>
                </form>

                <!-- Status Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold shrink-0">
                    <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'search' => request('search')]) }}"
                       class="px-3.5 py-2 rounded-lg border transition {{ !request('status') ? 'bg-[#221E1A] text-white border-[#221E1A]' : 'bg-[#FAF7F2] text-[#221E1A] border-[#E4D9C8] hover:border-[#C6A76B]' }}">
                        {{ $isEn ? 'All' : 'الكل' }} ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'status' => 'attending', 'search' => request('search')]) }}"
                       class="px-3.5 py-2 rounded-lg border transition {{ request('status') === 'attending' ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]' : 'bg-[#ECF5F0] text-[#0F4C3A] border-[#0F4C3A]/20 hover:border-[#0F4C3A]/50' }}">
                        {{ $isEn ? 'Attending' : 'حضور' }} ({{ $stats['attending'] }})
                    </a>
                    <a href="{{ route('dashboard.invitations.guests', ['id' => $invitation->id, 'status' => 'declined', 'search' => request('search')]) }}"
                       class="px-3.5 py-2 rounded-lg border transition {{ request('status') === 'declined' ? 'bg-[#BE123C] text-white border-[#BE123C]' : 'bg-[#FAF0EC] text-[#BE123C] border-[#BE123C]/20 hover:border-[#BE123C]/50' }}">
                        {{ $isEn ? 'Declined' : 'اعتذار' }} ({{ $stats['declined'] }})
                    </a>
                </div>
            </div>

            <!-- Guests Table -->
            <div class="card-atelier overflow-hidden">
                @if($rsvps->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#F4EBD9] text-[#856326] flex items-center justify-center mx-auto">
                            <x-app-icon name="users" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo text-base font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'No RSVP responses recorded yet' : 'لا توجد ردود حضور مسجلة حتى الآن' }}
                        </h3>
                        <p class="text-xs text-[#52493F] max-w-sm mx-auto">
                            {{ $isEn ? 'Share your invitation via WhatsApp to start collecting live responses from guests.' : 'شارك رابط دعوتك عبر واتساب لتصلك تأكيدات حضور ضيوفك فوراً.' }}
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-start text-xs">
                            <thead class="bg-[#FAF7F2] border-b border-[#E4D9C8] text-[#52493F] font-bold">
                                <tr>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Guest / Family' : 'اسم الضيف / العائلة' }}</th>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Phone' : 'الهاتف / التواصل' }}</th>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Status' : 'حالة الحضور' }}</th>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Party Size' : 'عدد الأفراد' }}</th>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Dietary & Wishes' : 'التهنئة أو الملاحظات' }}</th>
                                    <th class="px-5 py-3.5">{{ $isEn ? 'Submitted' : 'تاريخ الإرسال' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E4D9C8]/70">
                                @foreach($rsvps as $rsvp)
                                    <tr class="hover:bg-[#FAF7F2]/60 transition">
                                        <td class="px-5 py-4 font-bold text-sm text-[#221E1A]">
                                            {{ $rsvp->guest_name }}
                                        </td>
                                        <td class="px-5 py-4 text-[#52493F] tabular-nums whitespace-nowrap" dir="ltr">
                                            {{ $rsvp->phone ? \App\Services\PhoneService::formatForDisplay($rsvp->phone) : '—' }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if($rsvp->attendance_status === 'attending')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20">
                                                    <x-app-icon name="check" class="w-3 h-3" />
                                                    <span>{{ $isEn ? 'Attending' : 'حضور مؤكد' }}</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FAF0EC] text-[#BE123C] border border-[#BE123C]/20">
                                                    <x-app-icon name="x" class="w-3 h-3" />
                                                    <span>{{ $isEn ? 'Declined' : 'اعتذر عن الحضور' }}</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 font-bold text-sm text-[#221E1A] tabular-nums whitespace-nowrap">
                                            {{ $rsvp->guest_count + ($rsvp->children_count ?? 0) }}
                                            @if(($rsvp->children_count ?? 0) > 0)
                                                <span class="block text-[11px] font-medium text-[#52493F]">
                                                    {{ $rsvp->guest_count }} {{ $isEn ? 'adults' : 'بالغين' }} · {{ $rsvp->children_count }} {{ $isEn ? 'children' : 'أطفال' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-[#221E1A]/85 max-w-xs space-y-1">
                                            @if($rsvp->dietary_notes)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-900 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                                    <x-app-icon name="utensils" class="w-3 h-3 text-amber-700 shrink-0" />
                                                    <span>{{ $rsvp->dietary_notes }}</span>
                                                </span>
                                            @endif
                                            <div class="truncate" title="{{ $rsvp->message }}">{{ $rsvp->message ?: '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-[#52493F] whitespace-nowrap tabular-nums">
                                            {{ $rsvp->created_at->translatedFormat('d M Y, h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-[#E4D9C8]">
                        {{ $rsvps->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layouts.dashboard>
