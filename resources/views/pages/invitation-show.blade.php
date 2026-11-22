<x-layouts.base :title="$invitation->title . ' — زفّة'"
                 :description="$invitation->invitation_message ?: ('نتشرف بدعوتكم لحضور حفل زفاف ' . $invitation->formattedCoupleNames('ar'))"
                 :og-image="$invitation->cover_photo ?: ($invitation->template?->thumbnail ?? asset('images/og-zaffe.jpg'))">

    <!-- Draft Alert Banner for Owner -->
    @if(!$invitation->isPublished() && auth()->check() && (auth()->id() === $invitation->user_id || auth()->user()->isAdmin()))
        <div class="bg-[#9A5D12] text-white text-xs py-2.5 px-4 text-center font-bold sticky top-0 z-50 shadow-md flex flex-wrap items-center justify-center gap-2">
            <x-app-icon name="alert-triangle" class="w-4 h-4 shrink-0 text-[#FDE68A]" />
            <span>هذه الدعوة ما زالت في وضع (مسودة خاصة) وغير مرئية للجمهور.</span>
            <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}" class="underline bg-white/15 px-2.5 py-0.5 rounded-lg hover:bg-white/25 transition">
                فتح المحرر لنشرها
            </a>
            @if(auth()->id() === $invitation->user_id)
                <a href="{{ route('dashboard.invitations.share', $invitation->id) }}" class="underline bg-white/15 px-2.5 py-0.5 rounded-lg hover:bg-white/25 transition">
                    تجهيز بطاقة واتساب
                </a>
            @endif
        </div>
    @endif

    <!-- Personalized Guest Welcome Strip -->
    @if(!empty($personalGuest))
        <div class="bg-[#151210] text-white border-b border-[#C6A76B]/30 py-2.5 px-4 text-xs">
            <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2">
                    <x-app-icon name="sparkles" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                    <span>
                        دعوة خاصة إلى: <strong class="text-[#DFC99E]">{{ $personalGuest->name }}</strong>
                        <span class="text-[#DED5C8]/80 ms-1 tabular-nums">
                            ({{ $personalGuest->max_adults }} بالغين{{ $personalGuest->max_children > 0 ? ' · ' . $personalGuest->max_children . ' أطفال' : '' }})
                        </span>
                    </span>
                </div>
                @if($personalGuest->canReceiveQrPass())
                    <a href="{{ $personalGuest->getPassUrl() }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#C6A76B] text-[#151210] font-bold hover:bg-[#DFC99E] transition">
                        <x-app-icon name="qr-code" class="w-3.5 h-3.5" />
                        <span>بطاقة الدعوة الشخصية (QR)</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Dynamic Template Renderer -->
    @include($viewName, [
        'invitation' => $invitation,
        'personalGuest' => $personalGuest ?? null,
        'isPreview' => false,
    ])

</x-layouts.base>
