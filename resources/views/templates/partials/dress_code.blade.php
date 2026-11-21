@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
    'isDarkTheme' => false,
])

@php
    $dressCodeSection = collect($invitation->sections ?? [])->where('section_type', 'dress_code')->first();
    $isEnabled = $dressCodeSection ? (bool) $dressCodeSection->is_enabled : false;
    $content = is_array($dressCodeSection?->content ?? null) ? $dressCodeSection->content : [];
    $hasDressContent = !empty($content['title']) || !empty($content['notes']);

    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $iconBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.25)' : $primaryColor . '30';
@endphp

@if($isEnabled && $hasDressContent)
    <div class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] border text-center space-y-3 transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};">
        <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center border"
             style="background-color: {{ $bgColor }}; color: {{ $primaryColor }}; border-color: {{ $iconBorder }};">
            <x-app-icon name="shirt" class="w-6 h-6" />
        </div>
        <h3 class="font-cairo text-lg font-extrabold" style="color: {{ $primaryColor }};">
            {{ $dressCodeSection?->title_ar ?: 'قواعد الأناقة واللباس (Dress Code)' }}
        </h3>
        @if(!empty($content['title']))
            <p class="font-bold text-sm tracking-wide" style="color: inherit;">
                {{ $content['title'] }}
            </p>
        @endif
        @if(!empty($content['notes']))
            <p class="text-xs sm:text-sm max-w-md mx-auto leading-relaxed" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.82;' }}">
                {{ $content['notes'] }}
            </p>
        @endif
    </div>
@endif
