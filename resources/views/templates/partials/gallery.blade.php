@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
])

@php
    $gallerySection = collect($invitation->sections ?? [])->where('section_type', 'gallery')->first();
    $isEnabled = $gallerySection ? (bool) $gallerySection->is_enabled : true;

    // Gather photos from both InvitationPhoto relationship (preferring WebP thumbnails) and section content
    $modelItems = collect($invitation->photos ?? [])->map(function ($p) {
        if (is_string($p)) {
            return ['full' => $p, 'src' => $p, 'width' => null, 'height' => null, 'alt' => 'صورة الزفاف'];
        }

        $full = $p->url ?? $p->image_path;
        $thumb = $p->thumbnail_url ?? $full;

        return [
            'full' => $full,
            'src' => $thumb,
            'width' => $p->thumbnail_width ?? $p->width ?? null,
            'height' => $p->thumbnail_height ?? $p->height ?? null,
            'alt' => $p->caption ?: 'صورة الزفاف',
        ];
    })->filter(fn (array $item) => ! empty($item['src']));

    $existingFullUrls = $modelItems->pluck('full')->all();
    $contentItems = collect($gallerySection->content['photos'] ?? [])
        ->filter(fn ($url) => is_string($url) && $url !== '' && ! in_array($url, $existingFullUrls, true))
        ->map(fn (string $url) => ['full' => $url, 'src' => $url, 'width' => null, 'height' => null, 'alt' => 'صورة الزفاف']);

    $allPhotos = $modelItems->merge($contentItems)->values();
@endphp

@if($isEnabled && $allPhotos->isNotEmpty())
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
        <h3 class="font-cairo text-xl font-bold text-center mb-6" style="color: {{ $primaryColor }};">
            {{ $gallerySection?->title_ar ?: 'ألبوم الذكريات السعيدة' }}
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($allPhotos as $photo)
                <div class="h-36 sm:h-44 rounded-2xl overflow-hidden shadow-sm border border-black/10 group">
                    <img src="{{ $photo['src'] }}"
                         alt="{{ $photo['alt'] }}"
                         @if(!empty($photo['width']) && !empty($photo['height']))
                             width="{{ $photo['width'] }}"
                             height="{{ $photo['height'] }}"
                         @endif
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @endforeach
        </div>
    </div>
@endif
