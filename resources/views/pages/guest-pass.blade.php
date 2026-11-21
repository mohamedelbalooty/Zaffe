<x-layouts.base :title="'بطاقة الدعوة الشخصية — ' . $guest->name . ' | زفّة'">
    <div class="min-h-screen bg-[#FAF7F2] bg-luxury-grain py-8 sm:py-12 px-4 flex flex-col items-center justify-center print:bg-white print:py-2">
        <div class="w-full max-w-md bg-[#FCFBF8] rounded-3xl border border-[#C6A76B]/40 shadow-[0_24px_60px_rgba(21,18,16,0.12)] overflow-hidden print:shadow-none print:border-[#DED5C8]">
            <!-- Ticket Top Header -->
            <div class="bg-[#151210] text-white px-6 py-6 text-center space-y-2 border-b border-[#C6A76B]/30">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-[#C6A76B]/35 text-[#DFC99E] text-[11px] font-bold">
                    <x-app-icon name="qr-code" class="w-3.5 h-3.5" />
                    <span>بطاقة دعوة شخصية (QR Pass)</span>
                </span>
                <h1 class="font-cairo text-xl sm:text-2xl font-black text-white pt-1">
                    {{ $invitation->formattedCoupleNames('ar') }}
                </h1>
                <p class="text-xs text-[#DED5C8]">
                    {{ \App\Services\InvitationDateFormatter::formatGregorianDate($invitation, 'ar') }}
                    @if($invitation->venue_name)
                        — {{ $invitation->venue_name }}
                    @endif
                </p>
            </div>

            <!-- Perforated Ticket Divider -->
            <div class="relative h-6 bg-[#FCFBF8] flex items-center justify-between">
                <span class="w-4 h-6 rounded-e-full bg-[#FAF7F2] border-e border-y border-[#C6A76B]/35 print:hidden"></span>
                <div class="flex-1 mx-2 border-b-2 border-dashed border-[#DED5C8]"></div>
                <span class="w-4 h-6 rounded-s-full bg-[#FAF7F2] border-s border-y border-[#C6A76B]/35 print:hidden"></span>
            </div>

            <div class="px-6 pb-7 pt-2 space-y-5 text-center">
                <!-- Guest Details -->
                <div class="space-y-1.5">
                    <p class="text-xs text-[#6E675F] font-bold">باسم الضيف الكريم</p>
                    <h2 class="font-cairo text-2xl font-black text-[#221E1A]">{{ $guest->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-1 text-xs">
                        <span class="px-3 py-1 rounded-full bg-[#FAF7F2] border border-[#E5DCD0] text-[#221E1A] font-bold">
                            {{ \App\Models\InvitationGuest::categoryLabel($guest->category, 'ar') }}
                        </span>
                        @php
                            $hasConfirmed = ($guest->confirmed_adults + $guest->confirmed_children) > 0;
                            $displayAdults = $guest->rsvp_status === 'attending' && $hasConfirmed ? $guest->confirmed_adults : $guest->max_adults;
                            $displayChildren = $guest->rsvp_status === 'attending' && $hasConfirmed ? $guest->confirmed_children : $guest->max_children;
                        @endphp
                        <span class="px-3 py-1 rounded-full bg-[#C6A76B]/14 border border-[#C6A76B]/25 text-[#7A5A20] font-bold tabular-nums">
                            البالغون: {{ $displayAdults }}
                            @if($displayChildren > 0)
                                · الأطفال: {{ $displayChildren }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Entry Status Badge & QR -->
                @if($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_DECLINED)
                    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 space-y-2">
                        <x-app-icon name="x" class="w-8 h-8 mx-auto text-rose-600" />
                        <p class="text-sm font-bold">تم تسجيل الاعتذار عن الحضور</p>
                        <p class="text-xs text-rose-800/80">تم إلغاء تفعيل بطاقة الدعوة تلقائياً بناءً على ردكم.</p>
                    </div>
                @elseif($qrSvg)
                    <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-white border border-[#DED5C8] shadow-xs">
                        <div class="rounded-xl overflow-hidden bg-white p-1">
                            {!! $qrSvg !!}
                        </div>
                        <div class="mt-3 pt-3 border-t border-[#E5DCD0] w-full flex items-center justify-between gap-4 text-xs">
                            <span class="text-[#6E675F] font-bold">كود البطاقة الشخصي:</span>
                            <span dir="ltr" class="font-mono text-base font-black tracking-widest text-[#221E1A] bg-[#FAF7F2] px-3 py-1 rounded-lg border border-[#E5DCD0]">
                                {{ $guest->pass_code }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-[#6E675F] leading-relaxed">
                        بطاقة دعوة إلكترونية شخصية برمز QR لحفظ تفاصيل الحفل والمقاعد المخصصة وموقع القاعة.
                    </p>
                @else
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2">
                        <p class="text-sm font-bold">يرجى تأكيد الحضور أولاً لإصدار بطاقة الدعوة الشخصية</p>
                        <a href="{{ $guest->getPersonalInviteUrl() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 underline">
                            الانتقال لصفحة الدعوة وتأكيد الحضور
                        </a>
                    </div>
                @endif

                <!-- Venue & Action Buttons -->
                <div class="pt-3 border-t border-[#E5DCD0] flex flex-wrap items-center justify-center gap-2 print:hidden">
                    @if($qrSvg)
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#221E1A] text-white text-xs font-bold hover:bg-[#38322C] transition cursor-pointer">
                            <x-app-icon name="printer" class="w-4 h-4 text-[#DFC99E]" />
                            <span>حفظ / طباعة البطاقة</span>
                        </button>
                    @endif
                    @if($invitation->venue_map_url)
                        <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#C6A76B]/15 text-[#7A5A20] border border-[#C6A76B]/25 text-xs font-bold hover:bg-[#C6A76B]/25 transition">
                            <x-app-icon name="map-pin" class="w-4 h-4" />
                            <span>موقع القاعة</span>
                        </a>
                    @endif
                    <a href="{{ $guest->getPersonalInviteUrl() }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-[#DED5C8] bg-white text-[#221E1A] text-xs font-bold hover:bg-[#FAF7F2] transition">
                        <x-app-icon name="external-link" class="w-4 h-4" />
                        <span>صفحة الدعوة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>
