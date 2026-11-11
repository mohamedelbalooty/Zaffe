@props(['invitation', 'isPreview' => false])

@php
    $styles = $invitation->effective_styles;
    $primaryColor = $styles['primary_color'] ?? '#1F2937';
    $secondaryColor = $styles['secondary_color'] ?? '#6B7280';
    $bgColor = $styles['background_color'] ?? '#FAFAFA';
    $cardColor = $styles['card_color'] ?? '#FFFFFF';
@endphp

<div class="min-h-screen py-12 px-4 sm:px-6 relative font-tajawal text-right"
     style="background-color: {{ $bgColor }}; color: {{ $primaryColor }};">
    
    <div class="max-w-xl mx-auto space-y-12">
        <!-- Minimal Card -->
        <div class="rounded-2xl p-8 sm:p-14 shadow-sm border border-zinc-200 text-center space-y-8"
             style="background-color: {{ $cardColor }};">
            
            <div class="space-y-2">
                <span class="text-[11px] font-black uppercase tracking-[0.25em] text-zinc-400">Wedding Invitation</span>
                <div class="w-6 h-0.5 bg-zinc-300 mx-auto"></div>
            </div>

            @if($invitation->welcome_message)
                <p class="text-xs sm:text-sm leading-relaxed text-zinc-500 font-medium px-4">
                    {!! nl2br(e($invitation->welcome_message)) !!}
                </p>
            @endif

            <!-- Clean Modern Typography -->
            <div class="py-6 space-y-4">
                <h1 class="font-cairo text-4xl sm:text-6xl font-black tracking-tight text-zinc-900">
                    {{ $invitation->groom_name }}
                </h1>
                <div class="text-sm font-semibold tracking-widest text-zinc-400">&</div>
                <h1 class="font-cairo text-4xl sm:text-6xl font-black tracking-tight text-zinc-900">
                    {{ $invitation->bride_name }}
                </h1>
            </div>

            @if($invitation->cover_photo)
                <div class="py-2">
                    <div class="w-full h-64 sm:h-80 mx-auto rounded-xl overflow-hidden shadow-sm">
                        <img src="{{ $invitation->cover_photo }}" alt="Couple" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            @if($invitation->invitation_message)
                <p class="text-sm text-zinc-600 leading-relaxed max-w-md mx-auto">
                    {!! nl2br(e($invitation->invitation_message)) !!}
                </p>
            @endif

            <!-- Date & Venue -->
            <div class="pt-8 border-t border-zinc-100 space-y-3">
                <div class="text-lg font-bold text-zinc-900">
                    {{ \Carbon\Carbon::parse($invitation->wedding_date)->translatedFormat('l, d F Y') }}
                </div>
                @if($invitation->wedding_time)
                    <div class="text-xs font-semibold text-zinc-500">
                        الساعة {{ date('h:i A', strtotime($invitation->wedding_time)) }}
                    </div>
                @endif
                <div class="text-sm font-semibold text-zinc-700 pt-1">
                    {{ $invitation->venue_name }}
                </div>
                @if($invitation->venue_map_url)
                    <div class="pt-2">
                        <a href="{{ $invitation->venue_map_url }}" target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-bold text-zinc-900 underline hover:text-zinc-600 transition">
                            <span>عرض الموقع على Google Maps</span>
                            <x-app-icon name="external-link" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- RSVP Form Section -->
        @if($invitation->is_rsvp_enabled && !$isPreview)
            <div id="rsvp-section" class="scroll-mt-10">
                <livewire:rsvp-form :invitation-id="$invitation->id" :max-guests="$invitation->max_guests_per_rsvp" />
            </div>
        @endif
    </div>
</div>
