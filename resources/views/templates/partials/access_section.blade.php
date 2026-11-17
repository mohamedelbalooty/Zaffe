@if(!($isPreview ?? false))
    @php
        $mode = $invitation instanceof \App\Models\Invitation
            ? $invitation->effectiveMode()
            : ($invitation->invitation_mode ?? \App\Models\Invitation::MODE_RSVP_QR);
        $allowsRsvp = $invitation instanceof \App\Models\Invitation
            ? $invitation->allowsRsvp()
            : (bool) ($invitation->is_rsvp_enabled ?? false);
        $supportsQr = $invitation instanceof \App\Models\Invitation
            ? $invitation->supportsQrPass()
            : in_array($mode, [\App\Models\Invitation::MODE_RSVP_QR, \App\Models\Invitation::MODE_QR_ONLY], true);
        $personalGuest ??= null;
    @endphp

    @if($allowsRsvp)
        <div id="rsvp-section" class="scroll-mt-10">
            <livewire:rsvp-form
                :invitation-id="$invitation->id"
                :max-guests="$invitation->max_guests_per_rsvp"
                :guest-id="$personalGuest?->id" />
        </div>
    @elseif($supportsQr && $mode === \App\Models\Invitation::MODE_QR_ONLY)
        <div id="rsvp-section" class="scroll-mt-10">
            @if($personalGuest && $personalGuest->rsvp_status !== \App\Models\InvitationGuest::STATUS_DECLINED)
                @php
                    $inlineQrSvg = app(\App\Services\QrCodeService::class)->renderSvg(
                        $personalGuest->getPassUrl(),
                        210,
                        '#111827',
                        '#FFFFFF',
                        'بطاقة الدخول الإلكترونية '.$personalGuest->name
                    );
                @endphp
                <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-[#C6A76B]/35 shadow-xl text-center space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A76B]/15 text-[#8A6D3B] text-xs font-bold">
                        <x-app-icon name="qr-code" class="w-4 h-4" />
                        <span>بطاقة الدخول المباشرة (QR Pass)</span>
                    </span>
                    <h3 class="font-cairo text-xl sm:text-2xl font-bold text-[#292521]">
                        أهلاً بكم، {{ $personalGuest->name }}
                    </h3>
                    <p class="text-xs text-[#292521]/70">
                        المقاعد المخصصة لكم: {{ $personalGuest->max_adults }} بالغين
                        @if($personalGuest->max_children > 0)
                            و {{ $personalGuest->max_children }} أطفال
                        @endif
                    </p>

                    @if($personalGuest->isCheckedIn())
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold">
                            تم تسجيل دخولكم بنجاح عند البوابة بتاريخ {{ $personalGuest->checked_in_at->timezone($invitation->timezone ?: 'Africa/Cairo')->format('Y-m-d H:i') }}
                        </div>
                    @else
                        <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-white border border-[#E8DED0] shadow-sm">
                            {!! $inlineQrSvg !!}
                            <div class="mt-3 flex items-center gap-2 text-xs">
                                <span class="text-[#292521]/60">كود الدخول البديل:</span>
                                <span dir="ltr" class="font-mono font-black tracking-widest text-[#292521] bg-[#FAF7F2] px-2.5 py-1 rounded-lg border border-[#E8DED0]">
                                    {{ $personalGuest->pass_code }}
                                </span>
                            </div>
                        </div>
                        <div class="pt-1">
                            <a href="{{ $personalGuest->getPassUrl() }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#292521] text-white text-xs font-bold hover:opacity-90 transition">
                                <x-app-icon name="download" class="w-4 h-4" />
                                <span>فتح وحفظ بطاقة الدخول</span>
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 border border-[#C6A76B]/30 shadow-lg text-center space-y-2">
                    <div class="w-11 h-11 rounded-full bg-[#C6A76B]/15 text-[#8A6D3B] flex items-center justify-center mx-auto">
                        <x-app-icon name="shield-check" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-bold text-[#292521]">الدخول ببطاقة QR شخصية</h3>
                    <p class="text-xs text-[#292521]/70 max-w-md mx-auto leading-relaxed">
                        هذه الدعوة مخصصة بكود دخول إلكتروني فردي. يرجى استخدام الرابط الخاص المرسل إليكم من منظم الحفل لإظهار بطاقة الدخول الخاصة بكم.
                    </p>
                </div>
            @endif
        </div>
    @endif
@endif
