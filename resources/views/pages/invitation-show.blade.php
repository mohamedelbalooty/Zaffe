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

    <!-- Dynamic Template Renderer -->
    @include($viewName, [
        'invitation' => $invitation,
        'isPreview' => false,
    ])

</x-layouts.base>

