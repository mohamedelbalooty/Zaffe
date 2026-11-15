<x-layouts.base :title="'بطاقة الدخول — ' . $guest->name . ' | زفّة'">
    <div class="min-h-screen bg-[#FAF7F2] py-8 px-4 flex flex-col items-center justify-center print:bg-white print:py-2">
        <div class="w-full max-w-md bg-white rounded-3xl border border-[#C6A76B]/35 shadow-xl overflow-hidden print:shadow-none">
            <!-- Header -->
            <div class="bg-[#292521] text-white px-6 py-5 text-center space-y-1.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A76B]/20 text-[#E5C98B] text-[11px] font-bold">
                    <x-app-icon name="qr-code" class="w-3.5 h-3.5" />
                    <span>بطاقة دخول شخصية (QR Pass)</span>
                </span>
                <h1 class="font-cairo text-xl font-black text-white pt-1">
                    {{ $invitation->groom_name }} و {{ $invitation->bride_name }}
                </h1>
                <p class="text-xs text-[#E8DED0]/80">
                    {{ \App\Services\InvitationDateFormatter::formatGregorianDate($invitation, 'ar') }}
                    @if($invitation->venue_name)
                        — {{ $invitation->venue_name }}
                    @endif
                </p>
            </div>

            <div class="p-6 space-y-5 text-center">
                <!-- Guest Details -->
                <div class="space-y-1">
                    <p class="text-xs text-[#292521]/60 font-bold">باسم الضيف الكريم</p>
                    <h2 class="font-cairo text-2xl font-black text-[#292521]">{{ $guest->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-1 text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-[#FAF7F2] border border-[#E8DED0] text-[#292521] font-bold">
                            {{ \App\Models\InvitationGuest::categoryLabel($guest->category, 'ar') }}
                        </span>
                        @php
                            $hasConfirmed = ($guest->confirmed_adults + $guest->confirmed_children) > 0;
                            $displayAdults = $guest->rsvp_status === 'attending' && $hasConfirmed ? $guest->confirmed_adults : $guest->max_adults;
                            $displayChildren = $guest->rsvp_status === 'attending' && $hasConfirmed ? $guest->confirmed_children : $guest->max_children;
                        @endphp
                        <span class="px-2.5 py-1 rounded-full bg-[#C6A76B]/15 text-[#8A6D3B] font-bold">
                            البالغون: {{ $displayAdults }}
                            @if($displayChildren > 0)
                                · الأطفال: {{ $displayChildren }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Entry Status Badge & QR -->
                @if($guest->rsvp_status === \App\Models\InvitationGuest::STATUS_DECLINED)
                    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-2">
                        <x-app-icon name="x" class="w-8 h-8 mx-auto text-rose-500" />
                        <p class="text-sm font-bold">تم تسجيل الاعتذار عن الحضور</p>
                        <p class="text-xs text-rose-700/80">تم إلغاء تفعيل كود الدخول تلقائياً بناءً على ردكم.</p>
                    </div>
                @elseif($guest->isCheckedIn())
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-2">
                        <x-app-icon name="check-circle" class="w-9 h-9 mx-auto text-emerald-600" />
                        <p class="text-sm font-black">تم تسجيل الدخول بنجاح</p>
                        <p class="text-xs text-emerald-700">
                            الوقت: {{ $guest->checked_in_at->timezone($invitation->timezone ?: 'Africa/Cairo')->format('Y-m-d H:i') }}
                            @if($guest->checkedInGate)
                                — عبر {{ $guest->checkedInGate->name }}
                            @endif
                        </p>
                        <p class="text-[11px] text-emerald-700/80">بطاقة صالحة للاستخدام مرة واحدة فقط عند بوابة القاعة.</p>
                    </div>
                @elseif($qrSvg)
                    <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-white border-2 border-[#C6A76B]/30 shadow-inner">
                        <div class="rounded-xl overflow-hidden bg-white">
                            {!! $qrSvg !!}
                        </div>
                        <div class="mt-3 pt-3 border-t border-[#E8DED0] w-full flex items-center justify-between gap-4 text-xs">
                            <span class="text-[#292521]/60 font-bold">كود التحقق البديل:</span>
                            <span dir="ltr" class="font-mono text-base font-black tracking-widest text-[#292521] bg-[#FAF7F2] px-3 py-1 rounded-lg border border-[#E8DED0]">
                                {{ $guest->pass_code }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-[#292521]/70 leading-relaxed">
                        يُرجى إبراز رمز QR أعلاه لمستقبل الضيوف عند بوابة القاعة. يُستخدم هذا الكود للدخول مرة واحدة فقط.
                    </p>
                @else
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2">
                        <p class="text-sm font-bold">يرجى تأكيد الحضور أولاً لإصدار كود الدخول</p>
                        <a href="{{ $guest->getPersonalInviteUrl() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 underline">
                            الانتقال لصفحة الدعوة وتأكيد الحضور
                        </a>
                    </div>
                @endif

                <!-- Venue & Action Buttons -->
                <div class="pt-2 border-t border-[#E8DED0] flex flex-wrap items-center justify-center gap-2 print:hidden">
                    @if($qrSvg && ! $guest->isCheckedIn())
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#292521] text-white text-xs font-bold hover:opacity-90 transition cursor-pointer">
                            <x-app-icon name="download" class="w-4 h-4" />
                            <span>حفظ / طباعة البطاقة</span>
                        </button>
                    @endif
                    @if($invitation->venue_map_url)
                        <a href="{{ $invitation->venue_map_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#C6A76B]/15 text-[#8A6D3B] text-xs font-bold hover:bg-[#C6A76B]/25 transition">
                            <x-app-icon name="map-pin" class="w-4 h-4" />
                            <span>موقع القاعة</span>
                        </a>
                    @endif
                    <a href="{{ $guest->getPersonalInviteUrl() }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-[#E8DED0] text-[#292521] text-xs font-bold hover:bg-[#FAF7F2] transition">
                        <x-app-icon name="external-link" class="w-4 h-4" />
                        <span>صفحة الدعوة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>
