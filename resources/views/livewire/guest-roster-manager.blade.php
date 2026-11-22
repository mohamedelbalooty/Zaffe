@php
    $isEn = app()->getLocale() === 'en';
@endphp

<div class="space-y-5"
     x-data="{ copiedUrl: null, copyLink(url) { navigator.clipboard?.writeText(url); this.copiedUrl = url; setTimeout(() => { if (this.copiedUrl === url) this.copiedUrl = null; }, 2000); } }"
     @keydown.escape.window="$wire.closePassPreview()">

    <!-- Mode & Roster KPIs -->
    <div class="card-atelier p-5 sm:p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-cairo text-lg sm:text-xl font-black text-[#221E1A] flex items-center gap-2">
                    <x-app-icon name="qr-code" class="w-5 h-5 text-[#856326]" />
                    <span>{{ $isEn ? 'Smart Guest Roster & QR Passes' : 'قائمة المدعوين وبطاقات الدخول الذكية (QR)' }}</span>
                </h2>
                <p class="text-xs text-[#52493F] mt-1">
                    {{ $isEn ? 'Active Mode:' : 'الوضع الحالي للدعوة:' }}
                    <strong class="text-[#7A5A20]">{{ \App\Models\Invitation::modeLabel($invitation->effectiveMode(), $isEn ? 'en' : 'ar') }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="$toggle('showImportModal')"
                        class="btn-ghost-ivory !min-h-[40px] !py-2 !px-3.5 !text-xs cursor-pointer">
                    <x-app-icon name="plus" class="w-4 h-4 text-[#856326]" />
                    <span>{{ $isEn ? 'Bulk Import / CSV' : 'استيراد جماعي / CSV' }}</span>
                </button>
                <a href="{{ route('dashboard.invitations.guests.roster-export', $invitation->id) }}"
                   class="btn-obsidian !min-h-[40px] !py-2 !px-3.5 !text-xs">
                    <x-app-icon name="download" class="w-4 h-4 text-[#DFC99E]" />
                    <span>{{ $isEn ? 'Export Roster (CSV)' : 'تصدير قائمة المدعوين (CSV)' }}</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 tabular-nums">
            <div class="p-3.5 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8]">
                <span class="block text-[11px] text-[#52493F] font-bold">{{ $isEn ? 'Pre-Invited' : 'المدعوون المسجلون' }}</span>
                <span class="font-cairo block text-xl font-black text-[#221E1A] mt-1">{{ $stats['total_invited'] }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/20">
                <span class="block text-[11px] text-[#0F4C3A] font-bold">{{ $isEn ? 'Confirmed (Parties / Heads)' : 'مؤكدون (دعوات / أفراد)' }}</span>
                <span class="font-cairo block text-xl font-black text-[#0F4C3A] mt-1">{{ $stats['attending_parties'] }} <span class="text-xs font-bold">({{ $stats['attending_heads'] }})</span></span>
            </div>
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200">
                <span class="block text-[11px] text-amber-900 font-bold">{{ $isEn ? 'Awaiting Reply' : 'بانتظار الرد' }}</span>
                <span class="font-cairo block text-xl font-black text-amber-800 mt-1">{{ $stats['pending_parties'] }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#FAF0EC] border border-[#BE123C]/20">
                <span class="block text-[11px] text-[#BE123C] font-bold">{{ $isEn ? 'Declined' : 'معتذرون' }}</span>
                <span class="font-cairo block text-xl font-black text-[#BE123C] mt-1">{{ $stats['declined_parties'] }}</span>
            </div>
        </div>
    </div>

    <!-- Bulk Import Collapsible Panel -->
    @if($showImportModal)
        <div class="card-atelier !border-[#C6A76B] p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-cairo text-base font-extrabold text-[#221E1A]">
                    {{ $isEn ? 'Bulk Import Guests' : 'استيراد قائمة مدعوين دفعة واحدة' }}
                </h3>
                <button type="button" wire:click="$set('showImportModal', false)" aria-label="{{ $isEn ? 'Close' : 'إغلاق' }}" class="p-1.5 rounded-lg text-[#52493F] hover:text-[#221E1A] hover:bg-[#FAF7F2] cursor-pointer">
                    <x-app-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <p class="text-xs text-[#52493F] leading-relaxed">
                {{ $isEn ? 'Paste one guest per line:' : 'ألصق القائمة سطراً بسطر بصيغة:' }}
                <code dir="ltr" class="bg-[#FAF7F2] px-2 py-0.5 rounded border border-[#E4D9C8] text-[#221E1A]">Name, Phone, Adults, Children, Category (vip/general)</code>
                {{ $isEn ? 'or upload a CSV file.' : 'أو ارفع ملف CSV.' }}
            </p>

            <form wire:submit="importBulkGuests" class="space-y-3">
                <div>
                    <textarea wire:model="bulkText" rows="5"
                              class="input-atelier !min-h-[120px]"
                              placeholder="الأستاذ خالد العتيبي, +966501234567, 2, 0, vip&#10;عائلة الأستاذ محمود سالم, +201001234567, 4, 2, family_groom"></textarea>
                    @error('bulkText') <span class="text-xs text-[#BE123C] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div x-data="{ fileName: '' }"
                         x-on:livewire-upload-finish="fileName = ''; if ($refs.csvInput) $refs.csvInput.value = ''"
                         x-on:livewire-upload-error="fileName = ''; if ($refs.csvInput) $refs.csvInput.value = ''"
                         class="flex items-center gap-2">
                        <label class="cursor-pointer inline-flex items-center justify-center px-3.5 py-2 rounded-lg border border-[#E4D9C8] bg-[#FAF7F2] hover:bg-[#F4EBD9] text-xs font-bold text-[#221E1A] transition select-none focus-within:ring-2 focus-within:ring-[#C6A76B]">
                            <span>{{ $isEn ? 'Choose File' : 'اختر ملف' }}</span>
                            <input type="file" x-ref="csvInput" wire:model="csvFile" accept=".csv,.txt" class="sr-only"
                                   x-on:change="fileName = $event.target.files.length > 0 ? $event.target.files[0].name : ''">
                        </label>
                        <span class="text-xs text-[#52493F] truncate max-w-[180px]"
                              x-text="fileName ? fileName : '{{ $isEn ? 'No file chosen' : 'لم يتم اختيار ملف' }}'">
                            {{ $isEn ? 'No file chosen' : 'لم يتم اختيار ملف' }}
                        </span>
                    </div>
                    <button type="submit" class="btn-royal !min-h-[40px] !py-2 !px-5 !text-xs cursor-pointer">
                        {{ $isEn ? 'Import & Generate Passes' : 'تنفيذ الاستيراد وإصدار الأكواد' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($importSummary)
        <div role="status" class="p-4 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/25 text-[#0F4C3A] text-xs font-bold flex items-center justify-between">
            <span>تم استيراد {{ $importSummary['added'] }} مدعو بنجاح ({{ $importSummary['skipped'] }} سطور متجاهلة لعدم اكتمال البيانات).</span>
            <button type="button" wire:click="$set('importSummary', null)" class="text-[#0F4C3A] underline cursor-pointer">إغلاق</button>
        </div>
    @endif

    @if(session('roster_notice'))
        <div role="status" class="p-4 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/25 text-[#0F4C3A] text-xs font-bold">
            {{ session('roster_notice') }}
        </div>
    @endif

    <!-- Add Single Pre-Invited Guest -->
    <div class="card-atelier p-5 sm:p-6 space-y-4">
        <h3 class="font-cairo text-base font-extrabold text-[#221E1A]">
            {{ $isEn ? 'Add Guest to Personal Roster' : 'إضافة مدعو إلى القائمة المخصصة' }}
        </h3>
        <form wire:submit="addGuest" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-5">
                <label for="roster_guest_name" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Guest or Family Name' : 'اسم الضيف / العائلة' }}
                </label>
                <input id="roster_guest_name" type="text" wire:model="guestName"
                       placeholder="{{ $isEn ? 'e.g. Dr. Tariq Al-Mansour & Family' : 'مثال: د. طارق المنصور وعائلته' }}"
                       class="input-atelier !min-h-[42px]">
                @error('guestName') <span class="text-[11px] text-[#BE123C] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-4">
                <label for="roster_guest_phone" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Phone / WhatsApp (Optional)' : 'الجوال / الواتساب (اختياري)' }}
                </label>
                <div class="flex gap-1.5" dir="ltr">
                    <select wire:model="guestPhoneCountry"
                            aria-label="{{ $isEn ? 'Country Code' : 'رمز الدولة' }}"
                            class="min-h-[42px] px-2.5 rounded-xl border border-[#E4D9C8] text-xs font-bold bg-[#FAF7F2] text-[#221E1A] shrink-0">
                        <option value="SA">+966</option>
                        <option value="EG">+20</option>
                    </select>
                    <input id="roster_guest_phone" type="tel" wire:model="guestPhone" dir="ltr" placeholder="501234567"
                           class="input-atelier !min-h-[42px] flex-1 text-left">
                </div>
                @error('guestPhone') <span class="text-[11px] text-[#BE123C] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-3">
                <label for="roster_guest_cat" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Category' : 'الفئة' }}
                </label>
                <select id="roster_guest_cat" wire:model="guestCategory" class="input-atelier !min-h-[42px] !text-xs font-bold">
                    @foreach(\App\Models\InvitationGuest::categories() as $cat)
                        <option value="{{ $cat }}">{{ \App\Models\InvitationGuest::categoryLabel($cat, $isEn ? 'en' : 'ar') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="roster_max_adults" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Adults' : 'بالغون' }}
                </label>
                <input id="roster_max_adults" type="number" min="1" max="20" wire:model="maxAdults"
                       class="input-atelier !min-h-[42px] text-center font-bold tabular-nums">
            </div>

            <div class="sm:col-span-2">
                <label for="roster_max_children" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Children' : 'أطفال' }}
                </label>
                <input id="roster_max_children" type="number" min="0" max="20" wire:model="maxChildren"
                       class="input-atelier !min-h-[42px] text-center font-bold tabular-nums">
            </div>

            <div class="sm:col-span-5">
                <label for="roster_guest_notes" class="block text-xs font-bold text-[#221E1A] mb-1">
                    {{ $isEn ? 'Internal Note (Optional)' : 'ملاحظات داخلية (اختياري)' }}
                </label>
                <input id="roster_guest_notes" type="text" wire:model="guestNotes"
                       placeholder="{{ $isEn ? 'e.g. VIP Table 1' : 'مثال: طاولة VIP 1' }}"
                       class="input-atelier !min-h-[42px]">
            </div>

            <div class="sm:col-span-3 flex items-end">
                <button type="submit" class="btn-obsidian w-full !min-h-[42px] !text-xs cursor-pointer">
                    <x-app-icon name="plus" class="w-4 h-4 text-[#DFC99E]" />
                    <span>{{ $isEn ? 'Add Guest' : 'إضافة المدعو' }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Roster Filter & Table -->
    <div class="card-atelier overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E4D9C8] flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                @php
                    $statusTabs = $isEn
                        ? ['all' => 'All', 'pending' => 'Pending', 'attending' => 'Confirmed', 'declined' => 'Declined']
                        : ['all' => 'الكل', 'pending' => 'بانتظار الرد', 'attending' => 'مؤكد', 'declined' => 'معتذر'];
                @endphp
                @foreach($statusTabs as $tabKey => $tabLabel)
                    <button type="button" wire:click="$set('filterStatus', '{{ $tabKey }}')"
                            class="px-3.5 py-2 rounded-lg text-xs font-bold transition border cursor-pointer {{ $filterStatus === $tabKey ? 'bg-[#221E1A] text-white border-[#221E1A]' : 'bg-[#FAF7F2] text-[#221E1A] border-[#E4D9C8] hover:border-[#C6A76B]' }}">
                        {{ $tabLabel }}
                    </button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <select wire:model.live="filterCategory"
                        aria-label="{{ $isEn ? 'Filter by category' : 'تصفية بالفئة' }}"
                        class="min-h-[40px] px-3 py-1.5 rounded-xl border border-[#E4D9C8] text-xs font-bold bg-[#FAF7F2] text-[#221E1A]">
                    <option value="all">{{ $isEn ? 'All Categories' : 'كل الفئات' }}</option>
                    @foreach(\App\Models\InvitationGuest::categories() as $cat)
                        <option value="{{ $cat }}">{{ \App\Models\InvitationGuest::categoryLabel($cat, $isEn ? 'en' : 'ar') }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live.debounce.250ms="search"
                       aria-label="{{ $isEn ? 'Search guest roster' : 'بحث في قائمة المدعوين' }}"
                       placeholder="{{ $isEn ? 'Search name, phone, code...' : 'بحث بالاسم، الهاتف، الكود...' }}"
                       class="min-h-[40px] flex-1 sm:flex-none sm:w-64 min-w-[200px] px-3.5 py-1.5 rounded-xl border border-[#E4D9C8] text-xs bg-[#FAF7F2]/70 text-[#221E1A] placeholder:text-[#6E6458] focus:border-[#B59556] outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-[#FAF7F2] border-b border-[#E4D9C8] text-[#52493F] font-bold">
                    <tr>
                        <th class="px-4 py-3.5">{{ $isEn ? 'Guest' : 'الضيف' }}</th>
                        <th class="px-4 py-3.5">{{ $isEn ? 'Category / Quota' : 'الفئة / المقاعد' }}</th>
                        <th class="px-4 py-3.5">{{ $isEn ? 'RSVP Status' : 'حالة الرد' }}</th>
                        <th class="px-4 py-3.5">{{ $isEn ? 'Pass Code' : 'كود البطاقة' }}</th>
                        <th class="px-4 py-3.5">{{ $isEn ? 'Share & Follow Up' : 'إرسال ومتابعة' }}</th>
                        <th class="px-4 py-3.5 text-end">{{ $isEn ? 'Actions' : 'إجراءات' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E4D9C8]/70">
                    @forelse($guests as $guest)
                        <tr class="hover:bg-[#FAF7F2]/60 transition">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-sm text-[#221E1A] block">{{ $guest->name }}</span>
                                @if($guest->phone)
                                    <span dir="ltr" class="text-[11px] text-[#52493F] tabular-nums">{{ \App\Services\PhoneService::formatForDisplay($guest->phone) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 space-y-1">
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-[#FAF7F2] border border-[#E4D9C8] text-[11px] font-bold text-[#221E1A]">
                                    {{ \App\Models\InvitationGuest::categoryLabel($guest->category, $isEn ? 'en' : 'ar') }}
                                </span>
                                <span class="block text-[11px] text-[#52493F] tabular-nums">
                                    {{ $isEn ? 'Quota:' : 'مخصص:' }} {{ $guest->max_adults }} {{ $isEn ? 'adults' : 'بالغين' }}{{ $guest->max_children > 0 ? ' + ' . $guest->max_children . ($isEn ? ' children' : ' أطفال') : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_ATTENDING)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#ECF5F0] text-[#0F4C3A] border border-[#0F4C3A]/20 text-[11px] font-bold tabular-nums">
                                        {{ $isEn ? 'Confirmed' : 'مؤكد' }} ({{ $guest->confirmed_adults ?: $guest->max_adults }}{{ $guest->confirmed_children > 0 ? '+' . $guest->confirmed_children : '' }})
                                    </span>
                                @elseif($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_DECLINED)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#FAF0EC] text-[#BE123C] border border-[#BE123C]/20 text-[11px] font-bold">
                                        {{ $isEn ? 'Declined' : 'معتذر' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                        {{ $isEn ? 'Pending' : 'بانتظار الرد' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span dir="ltr" class="inline-block font-mono font-black text-xs px-2.5 py-1 rounded-lg bg-[#FAF7F2] border border-[#E4D9C8] text-[#221E1A]">
                                    {{ $guest->pass_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="{{ $invitation->getGuestWhatsAppInviteUrl($guest, $isEn ? 'en' : 'ar', $guest->invited_at !== null) }}"
                                       target="_blank" rel="noopener noreferrer"
                                       wire:click="markInvited({{ $guest->id }})"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#0F4C3A] text-white text-[11px] font-bold hover:bg-[#0B3A2C] transition">
                                        <x-app-icon name="whatsapp" class="w-3.5 h-3.5" />
                                        <span>{{ $guest->invited_at ? ($isEn ? 'Send Reminder' : 'مشاركة تذكير (واتساب)') : ($isEn ? 'Send Invite' : 'مشاركة عبر واتساب') }}</span>
                                    </a>
                                    <button type="button" @click="copyLink('{{ $guest->getPersonalInviteUrl() }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-[#E4D9C8] bg-white text-[#221E1A] text-[11px] font-bold hover:border-[#C6A76B] cursor-pointer">
                                        <x-app-icon name="copy" class="w-3 h-3 text-[#856326]" />
                                        <span x-text="copiedUrl === '{{ $guest->getPersonalInviteUrl() }}' ? '{{ $isEn ? 'Copied' : 'تم النسخ' }}' : '{{ $isEn ? 'Copy Link' : 'الرابط' }}'"></span>
                                    </button>
                                </div>
                                @if($guest->invited_at)
                                    <span class="block text-[10px] text-[#0F4C3A] mt-1 font-semibold">
                                        {{ $isEn ? 'WhatsApp opened:' : 'تم فتح واتساب:' }} {{ $guest->invited_at->diffForHumans() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-end">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" wire:click="openPassPreview({{ $guest->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-[#F4EBD9] text-[#7A5A20] border border-[#C6A76B]/30 font-bold hover:bg-[#ECE0C8] cursor-pointer">
                                        {{ $isEn ? 'QR Pass' : 'بطاقة QR' }}
                                    </button>
                                    <button type="button" wire:click="deleteGuest({{ $guest->id }})"
                                            class="p-1.5 rounded-lg text-[#BE123C] hover:bg-[#FAF0EC] cursor-pointer"
                                            title="{{ $isEn ? 'Remove Guest' : 'حذف الضيف' }}"
                                            aria-label="{{ $isEn ? 'Remove Guest' : 'حذف الضيف' }}">
                                        <x-app-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-xs text-[#52493F]">
                                {{ $isEn
                                    ? 'No pre-invited guests in this filter yet. Add a guest above to issue their personal link and QR pass.'
                                    : 'لا يوجد مدعوون مسجلون في هذه الفئة بعد. أضف أول مدعو أعلاه لإصدار رابط دعوة شخصي وكود دخول QR فوراً.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- QR Pass Preview Dialog -->
    @if($previewGuest && $previewQrSvg)
        <div role="dialog"
             aria-modal="true"
             aria-labelledby="qr-modal-title"
             class="fixed inset-0 z-50 bg-black/65 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="absolute inset-0" wire:click="closePassPreview"></div>
            <div class="relative z-10 bg-white rounded-2xl border border-[#C6A76B]/50 max-w-sm w-full p-6 space-y-4 text-center shadow-2xl">
                <div class="flex items-center justify-between">
                    <h4 id="qr-modal-title" class="font-cairo text-base font-black text-[#221E1A]">
                        {{ $isEn ? 'Pass Preview: ' : 'معاينة بطاقة ' }}{{ $previewGuest->name }}
                    </h4>
                    <button type="button" wire:click="closePassPreview" aria-label="{{ $isEn ? 'Close' : 'إغلاق' }}" class="p-1.5 rounded-lg text-[#52493F] hover:text-[#221E1A] hover:bg-[#FAF7F2] cursor-pointer">
                        <x-app-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="inline-flex flex-col items-center p-4 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8]">
                    <div class="bg-white p-2.5 rounded-lg border border-[#E4D9C8]">
                        {!! $previewQrSvg !!}
                    </div>
                    <span dir="ltr" class="mt-2.5 font-mono text-sm font-black tracking-widest text-[#221E1A]">
                        {{ $previewGuest->pass_code }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                    <a href="{{ $previewGuest->getPassUrl() }}" target="_blank" rel="noopener noreferrer"
                       class="btn-obsidian !min-h-[40px] !py-2 !px-4 !text-xs">
                        <x-app-icon name="external-link" class="w-3.5 h-3.5 text-[#DFC99E]" />
                        <span>{{ $isEn ? 'Open Pass Page' : 'فتح رابط البطاقة' }}</span>
                    </a>
                    <button type="button" wire:click="closePassPreview"
                            class="btn-ghost-ivory !min-h-[40px] !py-2 !px-4 !text-xs cursor-pointer">
                        {{ $isEn ? 'Close' : 'إغلاق' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
