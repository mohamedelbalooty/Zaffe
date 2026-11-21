@props([
    'invitation',
    'personalGuest' => null,
    'isPreview' => false,
])

@php
    /** @var \App\Models\Invitation|object $invitation */
    /** @var \App\Models\InvitationGuest|null $personalGuest */
    /** @var bool $isPreview */
    $isPreview = isset($isPreview) ? (bool) $isPreview : false;
    $personalGuest = isset($personalGuest) ? $personalGuest : null;
@endphp

@if(!$isPreview)
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
                        'بطاقة الدعوة الشخصية '.$personalGuest->name
                    );
                @endphp
                <div class="bg-[#FCFBF8] text-[#221E1A] rounded-3xl p-6 sm:p-8 border border-[#C6A76B]/35 shadow-[0_18px_48px_rgba(21,18,16,0.09)] text-center space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A76B]/15 text-[#7A5A20] text-xs font-bold">
                        <x-app-icon name="qr-code" class="w-4 h-4" />
                        <span>بطاقة الدعوة الشخصية المباشرة (QR Pass)</span>
                    </span>
                    <h3 class="font-cairo text-xl sm:text-2xl font-black text-[#221E1A]">
                        أهلاً بكم، {{ $personalGuest->name }}
                    </h3>
                    <p class="text-xs text-[#6E675F]">
                        المقاعد المخصصة لكم: <span class="font-bold tabular-nums text-[#221E1A]">{{ $personalGuest->max_adults }}</span> بالغين
                        @if($personalGuest->max_children > 0)
                            و <span class="font-bold tabular-nums text-[#221E1A]">{{ $personalGuest->max_children }}</span> أطفال
                        @endif
                    </p>

                    <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-white border border-[#E5DCD0] shadow-xs">
                        {!! $inlineQrSvg !!}
                        <div class="mt-3 flex items-center gap-2 text-xs">
                            <span class="text-[#6E675F]">كود البطاقة الشخصي:</span>
                            <span dir="ltr" class="font-mono font-black tracking-widest text-[#221E1A] bg-[#FAF7F2] px-2.5 py-1 rounded-lg border border-[#E5DCD0]">
                                {{ $personalGuest->pass_code }}
                            </span>
                        </div>
                    </div>
                    <div class="pt-1">
                        <a href="{{ $personalGuest->getPassUrl() }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#221E1A] text-white text-xs font-bold hover:bg-[#38322C] transition">
                            <x-app-icon name="download" class="w-4 h-4 text-[#DFC99E]" />
                            <span>فتح وحفظ بطاقة الدعوة</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-[#FCFBF8] text-[#221E1A] rounded-3xl p-6 sm:p-7 border border-[#C6A76B]/30 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center space-y-2.5">
                    <div class="w-11 h-11 rounded-full bg-[#C6A76B]/15 text-[#7A5A20] flex items-center justify-center mx-auto">
                        <x-app-icon name="shield-check" class="w-5 h-5" />
                    </div>
                    <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">بطاقة الدعوة الشخصية (QR)</h3>
                    <p class="text-xs text-[#6E675F] max-w-md mx-auto leading-relaxed">
                        هذه الدعوة مخصصة ببطاقة دعوة شخصية فردية. يرجى استخدام الرابط الخاص المرسل إليكم من منظم الحفل لإظهار بطاقة الدعوة الخاصة بكم.
                    </p>
                </div>
            @endif
        </div>
    @endif
@endif
