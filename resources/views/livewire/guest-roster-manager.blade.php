<div class="space-y-6" x-data="{ copiedUrl: null, copyLink(url) { navigator.clipboard?.writeText(url); this.copiedUrl = url; setTimeout(() => { if (this.copiedUrl === url) this.copiedUrl = null; }, 2000); } }">
    <!-- Mode & Check-In Summary KPIs -->
    <div class="bg-white rounded-3xl border border-[#E8DED0] p-5 sm:p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-cairo text-lg font-black text-[#292521] flex items-center gap-2">
                    <x-app-icon name="qr-code" class="w-5 h-5 text-[#C6A76B]" />
                    <span>قائمة المدعوين وبطاقات الدخول الذكية (QR)</span>
                </h2>
                <p class="text-xs text-[#292521]/65 mt-0.5">
                    الوضع الحالي للدعوة: <strong class="text-[#8A6D3B]">{{ \App\Models\Invitation::modeLabel($invitation->effectiveMode(), 'ar') }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="$toggle('showImportModal')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-[#C6A76B]/40 text-[#8A6D3B] bg-[#FAF7F2] text-xs font-bold hover:bg-[#C6A76B]/15 transition cursor-pointer">
                    <x-app-icon name="plus" class="w-4 h-4" />
                    <span>استيراد جماعي / CSV</span>
                </button>
                <a href="{{ route('dashboard.invitations.guests.roster-export', $invitation->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#292521] text-white text-xs font-bold hover:opacity-90 transition">
                    <x-app-icon name="download" class="w-4 h-4" />
                    <span>تصدير القائمة ونتائج الدخول</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="p-3.5 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                <span class="block text-[11px] text-[#292521]/60 font-bold">المدعوون المسجلون</span>
                <span class="block text-xl font-black text-[#292521] mt-1">{{ $stats['total_invited'] }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/70">
                <span class="block text-[11px] text-emerald-800/75 font-bold">مؤكدون (دعوات / أفراد)</span>
                <span class="block text-xl font-black text-emerald-700 mt-1">{{ $stats['attending_parties'] }} <span class="text-xs font-bold">({{ $stats['attending_heads'] }})</span></span>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/70">
                <span class="block text-[11px] text-amber-800/75 font-bold">بانتظار الرد</span>
                <span class="block text-xl font-black text-amber-700 mt-1">{{ $stats['pending_parties'] }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/70">
                <span class="block text-[11px] text-rose-800/75 font-bold">معتذرون</span>
                <span class="block text-xl font-black text-rose-700 mt-1">{{ $stats['declined_parties'] }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-200/70">
                <span class="block text-[11px] text-sky-900/75 font-bold">حضروا القاعة</span>
                <span class="block text-xl font-black text-sky-700 mt-1">{{ $stats['checked_in_parties'] }} <span class="text-xs font-bold">({{ $stats['checked_in_heads'] }})</span></span>
            </div>
            <div class="p-3.5 rounded-2xl bg-[#292521] text-white">
                <span class="block text-[11px] text-white/70 font-bold">المتوقع وصولهم</span>
                <span class="block text-xl font-black text-[#E5C98B] mt-1">{{ $stats['remaining_heads'] }}</span>
            </div>
        </div>
    </div>

    <!-- Bulk Import Collapsible Panel -->
    @if($showImportModal)
        <div class="bg-white rounded-3xl border border-[#C6A76B]/40 p-5 sm:p-6 shadow-md space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-cairo text-base font-bold text-[#292521]">استيراد قائمة مدعوين دفعة واحدة</h3>
                <button type="button" wire:click="$set('showImportModal', false)" class="text-[#292521]/50 hover:text-[#292521] cursor-pointer">
                    <x-app-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <p class="text-xs text-[#292521]/70">
                ألصق القائمة سطراً بسطر بصيغة: <code dir="ltr" class="bg-[#FAF7F2] px-2 py-0.5 rounded border border-[#E8DED0]">الاسم, رقم الهاتف, عدد البالغين, عدد الأطفال, الفئة (vip/general)</code> أو ارفع ملف CSV.
            </p>

            <form wire:submit="importBulkGuests" class="space-y-3">
                <div>
                    <textarea wire:model="bulkText" rows="5"
                              class="w-full px-4 py-3 rounded-2xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none"
                              placeholder="الأستاذ خالد العتيبي, +966501234567, 2, 0, vip&#10;عائلة الأستاذ محمود سالم, +201001234567, 4, 2, family_groom"></textarea>
                    @error('bulkText') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <input type="file" wire:model="csvFile" accept=".csv,.txt"
                           class="text-xs text-[#292521]/75 file:mr-2 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#FAF7F2] file:font-bold file:text-[#292521]">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#C6A76B] text-[#1A1612] text-xs font-bold hover:opacity-90 transition cursor-pointer">
                        تنفيذ الاستيراد وإصدار الأكواد
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($importSummary)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
            <span>تم استيراد {{ $importSummary['added'] }} مدعو بنجاح ({{ $importSummary['skipped'] }} سطور متجاهلة لعدم اكتمال البيانات).</span>
            <button type="button" wire:click="$set('importSummary', null)" class="text-emerald-700 hover:underline cursor-pointer">إغلاق</button>
        </div>
    @endif

    @if(session('roster_notice'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold">
            {{ session('roster_notice') }}
        </div>
    @endif

    <!-- Add Single Guest Form & Door Gates Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Single Pre-Invited Guest -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-[#E8DED0] p-5 sm:p-6 shadow-sm space-y-4">
            <h3 class="font-cairo text-base font-bold text-[#292521]">إضافة مدعو إلى القائمة المخصصة</h3>
            <form wire:submit="addGuest" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">اسم الضيف / العائلة</label>
                    <input type="text" wire:model="guestName" placeholder="مثال: د. طارق المنصور وعائلته"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                    @error('guestName') <span class="text-[11px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">الجوال / الواتساب (اختياري)</label>
                    <div class="flex gap-1.5">
                        <select wire:model="guestPhoneCountry" class="px-2 py-2.5 rounded-xl border border-[#E8DED0] text-xs font-bold bg-[#FAF7F2]">
                            <option value="SA">+966</option>
                            <option value="EG">+20</option>
                        </select>
                        <input type="tel" wire:model="guestPhone" dir="ltr" placeholder="501234567"
                               class="w-full px-3 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                    </div>
                    @error('guestPhone') <span class="text-[11px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">الفئة</label>
                    <select wire:model="guestCategory" class="w-full px-3 py-2.5 rounded-xl border border-[#E8DED0] text-xs font-bold bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                        @foreach(\App\Models\InvitationGuest::categories() as $cat)
                            <option value="{{ $cat }}">{{ \App\Models\InvitationGuest::categoryLabel($cat, 'ar') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">بالغون</label>
                    <input type="number" min="1" max="20" wire:model="maxAdults"
                           class="w-full px-3 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none text-center font-bold">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">أطفال</label>
                    <input type="number" min="0" max="20" wire:model="maxChildren"
                           class="w-full px-3 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none text-center font-bold">
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold text-[#292521]/70 mb-1">ملاحظات داخلية (اختياري)</label>
                    <input type="text" wire:model="guestNotes" placeholder="مثال: طاولة VIP 1"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                </div>

                <div class="sm:col-span-3 flex items-end">
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-[#292521] text-white text-xs font-bold hover:opacity-90 transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                        <x-app-icon name="plus" class="w-4 h-4" />
                        <span>إضافة المدعو</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Zero-App Venue Door Gates -->
        <div class="bg-white rounded-3xl border border-[#E8DED0] p-5 sm:p-6 shadow-sm space-y-4">
            <div>
                <h3 class="font-cairo text-base font-bold text-[#292521] flex items-center gap-1.5">
                    <x-app-icon name="shield-check" class="w-4 h-4 text-[#C6A76B]" />
                    <span>بوابات الاستقبال وفحص QR</span>
                </h3>
                <p class="text-[11px] text-[#292521]/65 mt-0.5">أنشئ رابطاً لمستقبل الضيوف يعمل مباشرة من متصفح الجوال دون تحميل تطبيق أو تسجيل دخول.</p>
            </div>

            <form wire:submit="createGate" class="flex gap-2">
                <input type="text" wire:model="gateName" placeholder="مثال: قاعة النساء / البوابة 1"
                       class="flex-1 px-3 py-2 rounded-xl border border-[#E8DED0] text-xs bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#C6A76B] text-[#1A1612] text-xs font-bold hover:opacity-90 transition shrink-0 cursor-pointer">
                    إنشاء بوابة
                </button>
            </form>
            @error('gateName') <span class="text-[11px] text-rose-600 block">{{ $message }}</span> @enderror
            @if(session('gate_notice'))
                <p class="text-[11px] text-emerald-700 font-bold">{{ session('gate_notice') }}</p>
            @endif

            <div class="space-y-2 max-h-60 overflow-y-auto">
                @forelse($gates as $gate)
                    <div class="p-3 rounded-2xl border {{ $gate->is_active ? 'border-[#E8DED0] bg-[#FAF7F2]/70' : 'border-rose-200 bg-rose-50/40' }} space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-[#292521]">{{ $gate->name }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $gate->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                {{ $gate->is_active ? 'نشطة' : 'موقوفة' }} · {{ $gate->scan_count }} دخول
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <button type="button" @click="copyLink('{{ $gate->getPortalUrl() }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-[#E8DED0] text-[#292521] font-bold hover:bg-[#FAF7F2] cursor-pointer">
                                <x-app-icon name="copy" class="w-3 h-3" />
                                <span x-text="copiedUrl === '{{ $gate->getPortalUrl() }}' ? 'تم النسخ' : 'نسخ رابط الماسح'"></span>
                            </button>
                            <a href="{{ $gate->getPortalUrl() }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#292521] text-white font-bold hover:opacity-90">
                                <x-app-icon name="external-link" class="w-3 h-3" />
                                <span>فتح</span>
                            </a>
                            <button type="button" wire:click="toggleGate({{ $gate->id }})"
                                    class="px-2 py-1 rounded-lg border border-[#E8DED0] text-[#292521]/75 hover:bg-white cursor-pointer">
                                {{ $gate->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                            <button type="button" wire:click="deleteGate({{ $gate->id }})"
                                    class="p-1 text-rose-500 hover:text-rose-700 cursor-pointer" title="حذف البوابة">
                                <x-app-icon name="trash" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#292521]/50 text-center py-3">لم يتم إنشاء بوابات استقبال بعد.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Roster Filter & Table -->
    <div class="bg-white rounded-3xl border border-[#E8DED0] shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E8DED0] flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                @php
                    $statusTabs = [
                        'all' => 'الكل',
                        'pending' => 'بانتظار الرد',
                        'attending' => 'مؤكد',
                        'declined' => 'معتذر',
                        'checked_in' => 'دخل القاعة',
                        'not_checked_in' => 'لم يدخل بعد',
                    ];
                @endphp
                @foreach($statusTabs as $tabKey => $tabLabel)
                    <button type="button" wire:click="$set('filterStatus', '{{ $tabKey }}')"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $filterStatus === $tabKey ? 'bg-[#292521] text-white' : 'bg-[#FAF7F2] text-[#292521]/75 hover:bg-[#E8DED0]/60' }}">
                        {{ $tabLabel }}
                    </button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select wire:model.live="filterCategory" class="px-3 py-2 rounded-xl border border-[#E8DED0] text-xs font-bold bg-[#FAF7F2]">
                    <option value="all">كل الفئات</option>
                    @foreach(\App\Models\InvitationGuest::categories() as $cat)
                        <option value="{{ $cat }}">{{ \App\Models\InvitationGuest::categoryLabel($cat, 'ar') }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live.debounce.250ms="search" placeholder="بحث بالاسم، الهاتف، الكود..."
                       class="px-3.5 py-2 rounded-xl border border-[#E8DED0] text-xs bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#FAF7F2] border-b border-[#E8DED0] text-[#292521]/70 font-bold">
                    <tr>
                        <th class="px-4 py-3">الضيف</th>
                        <th class="px-4 py-3">الفئة / المقاعد</th>
                        <th class="px-4 py-3">حالة الرد</th>
                        <th class="px-4 py-3">كود الدخول والدخول الفعلي</th>
                        <th class="px-4 py-3">إرسال ومتابعة</th>
                        <th class="px-4 py-3 text-left">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8DED0]/60">
                    @forelse($guests as $guest)
                        <tr class="hover:bg-[#FAF7F2]/40 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-sm text-[#292521] block">{{ $guest->name }}</span>
                                @if($guest->phone)
                                    <span dir="ltr" class="text-[11px] text-[#292521]/60">{{ \App\Services\PhoneService::formatForDisplay($guest->phone) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-y-1">
                                <span class="inline-block px-2 py-0.5 rounded-full bg-[#FAF7F2] border border-[#E8DED0] text-[10px] font-bold">
                                    {{ \App\Models\InvitationGuest::categoryLabel($guest->category, 'ar') }}
                                </span>
                                <span class="block text-[11px] text-[#292521]/75">
                                    مخصص: {{ $guest->max_adults }} بالغين{{ $guest->max_children > 0 ? ' + ' . $guest->max_children . ' أطفال' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_ATTENDING)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                        مؤكد ({{ $guest->confirmed_adults ?: $guest->max_adults }}{{ $guest->confirmed_children > 0 ? '+' . $guest->confirmed_children : '' }})
                                    </span>
                                @elseif($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_DECLINED)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-[11px] font-bold">معتذر</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">بانتظار الرد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-y-1">
                                <span dir="ltr" class="inline-block font-mono font-black text-xs px-2 py-0.5 rounded bg-[#FAF7F2] border border-[#E8DED0]">
                                    {{ $guest->pass_code }}
                                </span>
                                @if($guest->isCheckedIn())
                                    <span class="block text-[11px] font-bold text-emerald-700">
                                        تم الدخول ({{ $guest->checked_in_at->timezone($invitation->timezone ?: 'Africa/Cairo')->format('H:i') }})
                                        @if($guest->checkedInGate)
                                            · {{ $guest->checkedInGate->name }}
                                        @endif
                                    </span>
                                @else
                                    <span class="block text-[11px] text-[#292521]/50">لم يسجل دخول بعد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="{{ $invitation->getGuestWhatsAppInviteUrl($guest, 'ar', $guest->invited_at !== null) }}"
                                       target="_blank" rel="noopener noreferrer"
                                       wire:click="markInvited({{ $guest->id }})"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-bold hover:bg-emerald-700 transition">
                                        <x-app-icon name="whatsapp" class="w-3.5 h-3.5" />
                                        <span>{{ $guest->invited_at ? 'مشاركة تذكير (واتساب)' : 'مشاركة عبر واتساب' }}</span>
                                    </a>
                                    <button type="button" @click="copyLink('{{ $guest->getPersonalInviteUrl() }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border border-[#E8DED0] bg-white text-[#292521] text-[11px] font-bold hover:bg-[#FAF7F2] cursor-pointer">
                                        <x-app-icon name="copy" class="w-3 h-3" />
                                        <span x-text="copiedUrl === '{{ $guest->getPersonalInviteUrl() }}' ? 'تم النسخ' : 'الرابط'"></span>
                                    </button>
                                </div>
                                @if($guest->invited_at)
                                    <span class="block text-[10px] text-emerald-700 mt-1">تم فتح واتساب: {{ $guest->invited_at->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-left">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" wire:click="openPassPreview({{ $guest->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-[#C6A76B]/15 text-[#8A6D3B] font-bold hover:bg-[#C6A76B]/25 cursor-pointer">
                                        بطاقة QR
                                    </button>
                                    <button type="button" wire:click="toggleCheckIn({{ $guest->id }})"
                                            class="px-2.5 py-1 rounded-lg border border-[#E8DED0] text-[#292521] font-bold hover:bg-[#FAF7F2] cursor-pointer">
                                        {{ $guest->isCheckedIn() ? 'إلغاء الدخول' : 'تسجيل دخول' }}
                                    </button>
                                    <button type="button" wire:click="deleteGuest({{ $guest->id }})"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 cursor-pointer" title="حذف الضيف">
                                        <x-app-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-xs text-[#292521]/55">
                                لا يوجد مدعوون مسجلون في هذه الفئة بعد. أضف أول مدعو أعلاه لإصدار رابط دعوة شخصي وكود دخول QR فوراً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- QR Pass Preview Modal -->
    @if($previewGuest && $previewQrSvg)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl border border-[#C6A76B]/40 max-w-sm w-full p-6 space-y-4 text-center shadow-2xl">
                <div class="flex items-center justify-between">
                    <h4 class="font-cairo text-base font-black text-[#292521]">معاينة بطاقة {{ $previewGuest->name }}</h4>
                    <button type="button" wire:click="closePassPreview" class="text-[#292521]/50 hover:text-[#292521] cursor-pointer">
                        <x-app-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-[#FAF7F2] border border-[#E8DED0]">
                    <div class="bg-white p-2 rounded-xl border border-[#E8DED0]">
                        {!! $previewQrSvg !!}
                    </div>
                    <span dir="ltr" class="mt-2 font-mono text-sm font-black tracking-widest text-[#292521]">
                        {{ $previewGuest->pass_code }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                    <a href="{{ $previewGuest->getPassUrl() }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#292521] text-white text-xs font-bold">
                        <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                        <span>فتح رابط البطاقة</span>
                    </a>
                    <button type="button" wire:click="closePassPreview"
                            class="px-4 py-2 rounded-xl border border-[#E8DED0] text-xs font-bold text-[#292521] cursor-pointer">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
