@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.dashboard :title="$isEn ? 'My Invitations — Zaffe' : 'إدارة دعواتي — زفّة'">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Your Wedding Invitations' : 'دعوات الزفاف الخاصة بك' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#52493F] mt-1">
                    {{ $isEn ? 'Edit card designs, manage guest lists, and share links' : 'تعديل التصاميم، متابعة الحضور، ومشاركة الروابط مع ضيوفك' }}
                </p>
            </div>
            <a href="{{ route('dashboard.invitations.create') }}" class="btn-royal shrink-0">
                <x-app-icon name="plus" class="w-4 h-4" />
                <span>{{ $isEn ? 'Create New Invitation' : 'إنشاء دعوة جديدة' }}</span>
            </a>
        </div>

        @if($invitations->isEmpty())
            <div class="card-atelier p-12 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-[#F4EBD9] text-[#856326] flex items-center justify-center mx-auto">
                    <x-app-icon name="heart" class="w-7 h-7" />
                </div>
                <h2 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                    {{ $isEn ? 'You have no invitations yet' : 'ليس لديك دعوات حالياً' }}
                </h2>
                <p class="text-xs sm:text-sm text-[#52493F] max-w-sm mx-auto">
                    {{ $isEn ? 'Select a royal theme and enter your celebration details to get started.' : 'اختر قالباً ملكياً وابدأ بإدخال بيانات فرحك لتجهيز دعوتك الأولى في دقائق.' }}
                </p>
                <div class="pt-1">
                    <a href="{{ route('dashboard.invitations.create') }}" class="btn-royal">
                        <x-app-icon name="plus" class="w-4 h-4" />
                        <span>{{ $isEn ? 'Create First Invitation' : 'إنشاء أول دعوة' }}</span>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($invitations as $invitation)
                    <div class="card-atelier p-5 sm:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 hover:border-[#C6A76B] transition"
                         x-data="{ copied: false, showDelete: false }"
                         @keydown.escape.window="showDelete = false">

                        <!-- Left Info -->
                        <div class="flex items-start sm:items-center gap-4 min-w-0 flex-1">
                            <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 border border-[#E4D9C8] bg-[#221E1A] flex items-center justify-center text-[#DFC99E]">
                                @if($invitation->cover_photo || $invitation->template?->thumbnail)
                                    <img src="{{ $invitation->cover_photo ?: $invitation->template?->thumbnail }}"
                                         alt=""
                                         onerror="this.style.display='none'"
                                         class="w-full h-full object-cover">
                                @else
                                    <x-app-icon name="crown" class="w-6 h-6" />
                                @endif
                            </div>

                            <div class="space-y-1.5 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-cairo text-base sm:text-lg font-extrabold text-[#221E1A]">
                                        {{ $invitation->groom_name }} &amp; {{ $invitation->bride_name }}
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
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FAF7F2] text-[#7A5A20] border border-[#E4D9C8]">
                                        {{ \App\Models\Invitation::modeLabel($invitation->effectiveMode(), $isEn ? 'en' : 'ar') }}
                                    </span>
                                </div>
                                <div class="text-xs text-[#52493F] flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <span>{{ $isEn ? 'Theme:' : 'القالب:' }} {{ $isEn && $invitation->template?->name_en ? $invitation->template->name_en : $invitation->template?->name_ar }}</span>
                                    <span>•</span>
                                    <span class="tabular-nums">{{ $isEn ? 'Date:' : 'الموعد:' }} {{ $invitation->wedding_date->format('Y-m-d') }}</span>
                                    <span>•</span>
                                    <span>{{ $isEn ? 'Venue:' : 'المكان:' }} {{ $invitation->venue_name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- RSVP Stats Summary -->
                        <div class="flex flex-wrap items-center gap-3 bg-[#FAF7F2] px-4 py-2.5 rounded-xl border border-[#E4D9C8] text-xs w-full lg:w-auto justify-between lg:justify-start">
                            <div class="flex items-center gap-3 font-bold tabular-nums">
                                <span class="text-[#52493F]">{{ $isEn ? 'RSVPs:' : 'الحضور:' }}</span>
                                <span class="inline-flex items-center gap-1 text-[#0F4C3A]">
                                    <x-app-icon name="check" class="w-3.5 h-3.5" />
                                    <span>{{ $invitation->rsvps->where('attendance_status', 'attending')->sum('guest_count') }} {{ $isEn ? 'guests' : 'فرد' }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 text-[#BE123C]">
                                    <x-app-icon name="x" class="w-3.5 h-3.5" />
                                    <span>{{ $invitation->rsvps->where('attendance_status', 'declined')->count() }}</span>
                                </span>
                            </div>
                            <a href="{{ route('dashboard.invitations.guests', $invitation->id) }}" class="text-[#7A5A20] font-extrabold hover:underline">
                                {{ $isEn ? 'Guest List' : 'القائمة الكاملة' }}
                            </a>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-2 self-end lg:self-center shrink-0">
                            <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}"
                               class="btn-obsidian !min-h-[38px] !py-1.5 !px-3.5 !text-xs">
                                <x-app-icon name="edit" class="w-3.5 h-3.5 text-[#DFC99E]" />
                                <span>{{ $isEn ? 'Live Studio' : 'المحرر المباشر' }}</span>
                            </a>

                            @if($invitation->status === 'published')
                                <a href="{{ $invitation->public_url }}" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 rounded-lg bg-[#FAF7F2] border border-[#E4D9C8] text-[#221E1A] hover:border-[#C6A76B] transition flex items-center justify-center"
                                   title="{{ $isEn ? 'View Public Card' : 'عرض الدعوة' }}"
                                   aria-label="{{ $isEn ? 'View Public Card' : 'عرض الدعوة' }}">
                                    <x-app-icon name="eye" class="w-4 h-4 text-[#856326]" />
                                </a>

                                <a href="{{ $invitation->getWhatsAppShareUrl() }}" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 rounded-lg bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20 hover:bg-[#0F4C3A] hover:text-white transition flex items-center justify-center"
                                   title="{{ $isEn ? 'Share via WhatsApp' : 'مشاركة واتساب' }}"
                                   aria-label="{{ $isEn ? 'Share via WhatsApp' : 'مشاركة واتساب' }}">
                                    <x-app-icon name="whatsapp" class="w-4 h-4" />
                                </a>

                                <button type="button"
                                        @click="navigator.clipboard?.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                        class="h-9 px-2.5 rounded-lg bg-[#FAF7F2] border border-[#E4D9C8] text-[#221E1A] hover:border-[#C6A76B] transition inline-flex items-center gap-1 text-[11px] font-bold cursor-pointer"
                                        title="{{ $isEn ? 'Copy URL' : 'نسخ الرابط' }}">
                                    <x-app-icon name="copy" class="w-3.5 h-3.5 text-[#856326]" x-show="!copied" />
                                    <x-app-icon name="check" class="w-3.5 h-3.5 text-[#0F4C3A]" x-show="copied" x-cloak />
                                    <span x-show="copied" x-cloak class="text-[#0F4C3A]">{{ $isEn ? 'Copied' : 'تم' }}</span>
                                </button>
                            @endif

                            <button type="button"
                                    @click="showDelete = true"
                                    class="w-9 h-9 rounded-lg bg-[#FAF0EC] text-[#BE123C] border border-[#BE123C]/20 hover:bg-[#BE123C] hover:text-white transition flex items-center justify-center cursor-pointer"
                                    title="{{ $isEn ? 'Delete Invitation' : 'حذف الدعوة' }}"
                                    aria-label="{{ $isEn ? 'Delete Invitation' : 'حذف الدعوة' }}">
                                <x-app-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Delete Confirmation Dialog -->
                        <div x-show="showDelete"
                             x-cloak
                             role="dialog"
                             aria-modal="true"
                             aria-labelledby="delete-title-{{ $invitation->id }}"
                             class="fixed inset-0 bg-black/65 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                            <div class="absolute inset-0" @click="showDelete = false"></div>
                            <div class="relative z-10 bg-white rounded-2xl p-6 sm:p-7 max-w-sm w-full text-center space-y-4 shadow-2xl border border-[#E4D9C8]">
                                <div class="w-12 h-12 rounded-2xl bg-[#FAF0EC] text-[#BE123C] flex items-center justify-center mx-auto">
                                    <x-app-icon name="trash" class="w-6 h-6" />
                                </div>
                                <h3 id="delete-title-{{ $invitation->id }}" class="font-cairo text-lg font-extrabold text-[#221E1A]">
                                    {{ $isEn ? 'Confirm Deletion' : 'تأكيد حذف الدعوة' }}
                                </h3>
                                <p class="text-xs sm:text-sm text-[#52493F] leading-relaxed">
                                    {{ $isEn
                                        ? 'Are you sure you want to permanently delete «' . $invitation->title . '» and all its RSVP & guest records?'
                                        : 'هل أنت متأكد من رغبتك في حذف دعوة «' . $invitation->title . '»؟ سيتم حذف جميع بيانات وتأكيدات الحضور المرتبطة بها نهائياً.' }}
                                </p>
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <button type="button" @click="showDelete = false" class="btn-ghost-ivory !min-h-[40px] !py-2 !text-xs">
                                        {{ $isEn ? 'Cancel' : 'إلغاء' }}
                                    </button>
                                    <form method="POST" action="{{ route('dashboard.invitations.destroy', $invitation->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full min-h-[40px] py-2 rounded-xl bg-[#BE123C] text-white text-xs font-bold hover:bg-rose-800 transition cursor-pointer">
                                            {{ $isEn ? 'Yes, Delete' : 'نعم، احذف' }}
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
