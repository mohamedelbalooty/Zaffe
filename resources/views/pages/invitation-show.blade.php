<x-layouts.base :title="$invitation->title . ' — زفّة'" 
                 :description="$invitation->invitation_message ?: ('نتشرف بدعوتكم لحضور حفل زفاف ' . $invitation->groom_name . ' و ' . $invitation->bride_name)"
                 :og-image="$invitation->cover_photo ?: ($invitation->template?->thumbnail ?? asset('images/og-zaffe.jpg'))">
    
    <!-- Draft Alert Banner for Owner -->
    @if(!$invitation->isPublished() && auth()->check() && (auth()->id() === $invitation->user_id || auth()->user()->isAdmin()))
        <div class="bg-amber-600 text-white text-xs py-2.5 px-4 text-center font-bold sticky top-0 z-50 shadow-md flex items-center justify-center gap-2">
            <span>⚠️ هذه الدعوة ما زالت في وضع (مسودة خاصة) وغير مرئية للجمهور.</span>
            <a href="{{ route('dashboard.invitations.editor', $invitation->id) }}" class="underline bg-white/20 px-2 py-0.5 rounded hover:bg-white/30 transition">
                فتح المحرر لنشرها
            </a>
        </div>
    @endif

    <!-- Personalized Guest Welcome Strip -->
    @if(!empty($personalGuest))
        <div class="bg-[#292521] text-white border-b border-[#C6A76B]/30 py-2.5 px-4 text-xs">
            <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <x-app-icon name="sparkles" class="w-4 h-4 text-[#C6A76B] shrink-0" />
                    <span>
                        دعوة خاصة إلى: <strong class="text-[#E5C98B]">{{ $personalGuest->name }}</strong>
                        <span class="text-white/70 mr-1">
                            ({{ $personalGuest->max_adults }} بالغين{{ $personalGuest->max_children > 0 ? ' · ' . $personalGuest->max_children . ' أطفال' : '' }})
                        </span>
                    </span>
                </div>
                @if($personalGuest->canReceiveQrPass())
                    <a href="{{ $personalGuest->getPassUrl() }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A76B] text-[#1A1612] font-bold hover:opacity-90 transition">
                        <x-app-icon name="qr-code" class="w-3.5 h-3.5" />
                        <span>بطاقة الدخول (QR)</span>
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

