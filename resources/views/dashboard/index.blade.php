@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.dashboard :title="$isEn ? 'Studio Overview — Zaffe' : 'لوحة التحكم — زفّة'">
    <div class="space-y-7">
        <!-- Studio Greeting Header -->
        <div class="rounded-2xl p-6 sm:p-8 bg-[#1E1A16] text-white border border-[#C6A76B]/30 shadow-wedding flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <h1 class="font-cairo text-2xl sm:text-3xl font-black text-white">
                    {{ $isEn ? 'Welcome, ' . auth()->user()->name : 'مرحباً، ' . auth()->user()->name }}
                </h1>
                <p class="text-sm text-[#E4D9C8]/80 max-w-xl">
                    {{ $isEn
                        ? 'Track your wedding invitations, instant RSVP confirmations, guest rosters, and live studio customizations.'
                        : 'تابع تفاصيل دعوات زفافك، تأكيدات الحضور الفورية، وتخصيص مظهر بطاقتك الرقمية بكل سهولة.' }}
                </p>
            </div>

            <a href="{{ route('dashboard.invitations.create') }}" class="btn-royal shrink-0">
                <x-app-icon name="plus" class="w-4 h-4" />
                <span>{{ $isEn ? 'Create New Invitation' : 'إنشاء دعوة جديدة' }}</span>
            </a>
        </div>

        <!-- Metrics & KPI Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="card-atelier p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#52493F]">{{ $isEn ? 'Total Invitations' : 'إجمالي الدعوات' }}</span>
                    <div class="w-9 h-9 rounded-xl bg-[#F4EBD9] text-[#856326] flex items-center justify-center">
                        <x-app-icon name="heart" class="w-4.5 h-4.5" />
                    </div>
                </div>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A] block tabular-nums">{{ $totalInvitations }}</span>
            </div>

            <div class="card-atelier p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#52493F]">{{ $isEn ? 'Published Live' : 'الدعوات المنشورة' }}</span>
                    <div class="w-9 h-9 rounded-xl bg-[#ECF5F0] text-[#0F4C3A] flex items-center justify-center">
                        <x-app-icon name="sparkles" class="w-4.5 h-4.5" />
                    </div>
                </div>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-[#0F4C3A] block tabular-nums">{{ $publishedInvitations }}</span>
            </div>

            <div class="card-atelier p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#52493F]">{{ $isEn ? 'Confirmed Guests' : 'ضيوف أكدوا الحضور' }}</span>
                    <div class="w-9 h-9 rounded-xl bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center">
                        <x-app-icon name="users" class="w-4.5 h-4.5" />
                    </div>
                </div>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-[#7A5A20] block tabular-nums">{{ $confirmedAttendees }}</span>
            </div>

            <div class="card-atelier p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#52493F]">{{ $isEn ? 'Declined RSVPs' : 'اعتذارات الحضور' }}</span>
                    <div class="w-9 h-9 rounded-xl bg-[#FAF0EC] text-[#BE123C] flex items-center justify-center">
                        <x-app-icon name="x" class="w-4.5 h-4.5" />
                    </div>
                </div>
                <span class="font-cairo text-2xl sm:text-3xl font-black text-[#BE123C] block tabular-nums">{{ $declinedCount }}</span>
            </div>
        </div>

        <!-- Invitations Overview Panel -->
        <div class="card-atelier p-6 sm:p-7 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#E4D9C8]">
                <div>
                    <h2 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Your Wedding Invitations' : 'دعوات الزفاف الخاصة بك' }}
                    </h2>
                    <p class="text-xs text-[#52493F] mt-0.5">
                        {{ $isEn ? 'Edit card designs, manage guests, and share links' : 'تحكم في محتوى ومظهر ومشاركة بطاقاتك الرقمية' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#221E1A] bg-[#FAF7F2] px-3 py-1.5 rounded-full border border-[#E4D9C8] tabular-nums">
                        <x-app-icon name="eye" class="w-3.5 h-3.5 text-[#856326]" />
                        <span>{{ $totalViews ?? 0 }} مشاهدة</span>
                    </span>
                    <a href="{{ route('dashboard.invitations.index') }}" class="text-xs font-extrabold text-[#7A5A20] hover:underline inline-flex items-center gap-1">
                        <span>{{ $isEn ? 'View All' : 'عرض الكل' }}</span>
                        <x-app-icon name="arrow-left" class="w-3.5 h-3.5 dir-arrow" />
                    </a>
                </div>
            </div>

            @if($invitations->isEmpty())
                <div class="text-center py-12 space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#F4EBD9] text-[#856326] flex items-center justify-center mx-auto">
                        <x-app-icon name="heart" class="w-7 h-7" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'No invitations created yet' : 'لم تنشئ أي دعوة بعد' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-[#52493F] max-w-sm mx-auto">
                        {{ $isEn
                            ? 'Pick a theme and enter your wedding details to build your interactive invitation in minutes.'
                            : 'ابدأ الآن باختيار قالبك المفضل وأدخل تفاصيل حفل الزفاف في دقائق معدودة.' }}
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('dashboard.invitations.create') }}" class="btn-royal">
                            <x-app-icon name="plus" class="w-4 h-4" />
                            <span>{{ $isEn ? 'Design Your First Card' : 'صمم دعوتك الأولى الآن' }}</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($invitations as $invitation)
                        <div class="p-5 rounded-xl border border-[#E4D9C8] bg-[#FAF7F2]/60 hover:border-[#C6A76B] transition flex flex-col justify-between space-y-4"
                             x-data="{ copied: false }">
                            <div class="flex items-start justify-between gap-3.5">
                                <div class="space-y-1.5 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A] truncate">
                                            {{ $invitation->formattedCoupleNames($isEn ? 'en' : 'ar', ' & ') }}
                                        </h3>
                                        @if($invitation->status === 'published')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20">
                                                {{ $isEn ? 'Published' : 'منشورة' }}
                                            </span>
                                        @elseif($invitation->status === 'archived')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                                {{ $isEn ? 'Archived' : 'مؤرشفة' }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                {{ $isEn ? 'Draft' : 'مسودة' }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#52493F]">
                                        {{ $isEn && $invitation->template?->name_en ? $invitation->template->name_en : $invitation->template?->name_ar }}
                                        •
                                        <span class="tabular-nums">{{ $invitation->wedding_date->format('Y-m-d') }}</span>
                                    </p>
                                </div>

                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-[#E4D9C8] bg-[#221E1A] flex items-center justify-center text-[#DFC99E]">
                                    @if($invitation->cover_photo || $invitation->template?->thumbnail)
                                        <img src="{{ $invitation->cover_photo ?: $invitation->template?->thumbnail }}"
                                             alt=""
                                             onerror="this.style.display='none'"
                                             class="w-full h-full object-cover">
                                    @else
                                        <x-app-icon name="crown" class="w-5 h-5" />
                                    @endif
                                </div>
                            </div>

                            <!-- RSVP & Views Summary Row -->
                            <div class="px-3.5 py-2.5 rounded-lg bg-white border border-[#E4D9C8] flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-3 font-bold tabular-nums">
                                    <span class="inline-flex items-center gap-1 text-[#52493F]" title="{{ $isEn ? 'Views' : 'المشاهدات' }}">
                                        <x-app-icon name="eye" class="w-3.5 h-3.5 text-[#856326]" />
                                        <span>{{ $invitation->view_count ?? 0 }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[#0F4C3A]">
                                        <x-app-icon name="check" class="w-3.5 h-3.5" />
                                        <span>{{ (int) $invitation->rsvps->where('attendance_status', 'attending')->sum('guest_count') + (int) $invitation->rsvps->where('attendance_status', 'attending')->sum('children_count') }} {{ $isEn ? 'attending' : 'فرد' }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[#BE123C]">
                                        <x-app-icon name="x" class="w-3.5 h-3.5" />
                                        <span>{{ $invitation->rsvps->where('attendance_status', 'declined')->count() }}</span>
                                    </span>
                                </div>
                                <a href="{{ route('dashboard.invitations.guests', $invitation->id) }}"
                                   class="text-[#7A5A20] hover:underline font-extrabold">
                                    {{ $isEn ? 'Manage Guests' : 'إدارة الضيوف' }}
                                </a>
                            </div>

                            <!-- Actions Row -->
                            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}"
                                       class="btn-obsidian !min-h-[38px] !py-1.5 !px-3.5 !text-xs">
                                        <x-app-icon name="edit" class="w-3.5 h-3.5 text-[#DFC99E]" />
                                        <span>{{ $isEn ? 'Open Studio' : 'تعديل بالمحرر' }}</span>
                                    </a>

                                    <a href="{{ route('dashboard.invitations.share', $invitation->id) }}"
                                       class="btn-ghost-ivory !min-h-[38px] !py-1.5 !px-3 !text-xs">
                                        <x-app-icon name="image" class="w-3.5 h-3.5 text-[#856326]" />
                                        <span>{{ $isEn ? 'Share Card' : 'بطاقة واتساب' }}</span>
                                    </a>

                                    @if($invitation->status === 'published')
                                        <a href="{{ $invitation->public_url }}" target="_blank" rel="noopener noreferrer"
                                           class="btn-ghost-ivory !min-h-[38px] !py-1.5 !px-3 !text-xs">
                                            <x-app-icon name="eye" class="w-3.5 h-3.5 text-[#856326]" />
                                            <span>{{ $isEn ? 'Preview' : 'معاينة الرابط' }}</span>
                                        </a>
                                    @endif
                                </div>

                                @if($invitation->status === 'published')
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank" rel="noopener noreferrer"
                                           class="w-9 h-9 rounded-lg bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20 hover:bg-[#0F4C3A] hover:text-white transition flex items-center justify-center"
                                           title="{{ $isEn ? 'Share via WhatsApp' : 'مشاركة واتساب' }}"
                                           aria-label="{{ $isEn ? 'Share via WhatsApp' : 'مشاركة واتساب' }}">
                                            <x-app-icon name="whatsapp" class="w-4 h-4" />
                                        </a>

                                        <button type="button"
                                                @click="navigator.clipboard?.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                                class="h-9 px-2.5 rounded-lg bg-white border border-[#E4D9C8] text-[#221E1A] hover:border-[#C6A76B] transition inline-flex items-center gap-1 text-[11px] font-bold cursor-pointer"
                                                title="{{ $isEn ? 'Copy link' : 'نسخ الرابط' }}">
                                            <x-app-icon name="copy" class="w-3.5 h-3.5 text-[#856326]" x-show="!copied" />
                                            <x-app-icon name="check" class="w-3.5 h-3.5 text-[#0F4C3A]" x-show="copied" x-cloak />
                                            <span x-show="copied" x-cloak class="text-[#0F4C3A]">{{ $isEn ? 'Copied' : 'تم' }}</span>
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
