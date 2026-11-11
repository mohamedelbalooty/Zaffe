@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
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
@endphp

@if($isEnabled && $allPhotos->isNotEmpty())
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
        <h3 class="font-cairo text-xl font-bold text-center mb-6" style="color: {{ $primaryColor }};">
            {{ $gallerySection?->title_ar ?: 'ألبوم الذكريات السعيدة' }}
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($allPhotos as $photoUrl)
                <div class="h-36 sm:h-44 rounded-2xl overflow-hidden shadow-sm border border-black/10 group">
                    <img src="{{ $photoUrl }}" alt="صورة الزفاف" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @endforeach
        </div>
    </div>
@endif
