<x-layouts.base :title="$invitation->og_title"
                :description="$invitation->og_description"
                :og-title="$invitation->og_title"
                :og-description="$invitation->og_description"
                :og-url="$invitation->public_url"
                :og-image="$invitation->og_image_url">
    
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

