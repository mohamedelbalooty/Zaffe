@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
])

@php
    $dressCodeSection = collect($invitation->sections ?? [])->where('section_type', 'dress_code')->first();
    $isEnabled = $dressCodeSection ? (bool) $dressCodeSection->is_enabled : false;
    $content = $dressCodeSection->content ?? [
        'title' => 'ملابس رسمية أنيقة (Black Tie / Formal Evening)',
        'notes' => 'نرجو من ضيوفنا الكرام الالتزام بالملابس الرسمية المسائية لتكتمل روعة وأناقة مناسبتنا.',
    ];
@endphp

@if($isEnabled && !empty($content))
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl border text-center space-y-3 transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
        <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center text-xl shadow-sm"
             style="background-color: {{ $bgColor }}; color: {{ $primaryColor }};">
            👔
        </div>
        <h3 class="font-cairo text-lg font-bold" style="color: {{ $primaryColor }};">
            {{ $dressCodeSection?->title_ar ?: 'قواعد الأناقة واللباس (Dress Code)' }}
        </h3>
        @if(!empty($content['title']))
            <p class="font-bold text-sm tracking-wide" style="color: inherit;">
                {{ $content['title'] }}
            </p>
        @endif
        @if(!empty($content['notes']))
            <p class="text-xs sm:text-sm opacity-80 max-w-md mx-auto leading-relaxed">
                {{ $content['notes'] }}
            </p>
        @endif
    </div>
@endif
