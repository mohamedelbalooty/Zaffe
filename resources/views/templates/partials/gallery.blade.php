@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
    'isDarkTheme' => false,
])

@php
    $gallerySection = collect($invitation->sections ?? [])->where('section_type', 'gallery')->first();
    $isEnabled = $gallerySection ? (bool) $gallerySection->is_enabled : true;

    // Gather photos from both InvitationPhoto relationship and section content
    $modelPhotos = collect($invitation->photos ?? [])->map(function ($p) {
        return is_string($p) ? $p : ($p->url ?? $p->image_path);
    });

    $contentPhotos = collect($gallerySection->content['photos'] ?? []);
    $allPhotos = $modelPhotos->merge($contentPhotos)->unique()->filter()->values();
    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $frameBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.22)' : 'rgba(21, 18, 16, 0.08)';
@endphp

@if($isEnabled && $allPhotos->isNotEmpty())
    <div x-data="{ loadedCount: 0 }"
         x-show="loadedCount > 0"
         class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};">
        <h3 class="font-cairo text-xl font-extrabold text-center mb-6" style="color: {{ $primaryColor }};">
            {{ $gallerySection?->title_ar ?: 'ألبوم الذكريات السعيدة' }}
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($allPhotos as $photoUrl)
                <div data-gallery-item class="hidden rounded-2xl overflow-hidden shadow-xs border group" style="border-color: {{ $frameBorder }}; background-color: {{ $bgColor }};">
                    <img src="{{ $photoUrl }}"
                         alt="صورة الزفاف"
                         decoding="async"
                         x-init="if ($el.complete && $el.naturalWidth > 0) { $el.closest('[data-gallery-item]').classList.remove('hidden'); loadedCount++; }"
                         @load="if ($el.naturalWidth > 0 && $el.closest('[data-gallery-item]').classList.contains('hidden')) { $el.closest('[data-gallery-item]').classList.remove('hidden'); loadedCount++; }"
                         onerror="this.closest('[data-gallery-item]').remove()"
                         class="w-full h-36 sm:h-44 object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @endforeach
        </div>
    </div>
@endif
