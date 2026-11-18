@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
    'isDarkTheme' => false,
])

@php
    $timelineSection = collect($invitation->sections ?? [])->where('section_type', 'timeline')->first();
    $isEnabled = $timelineSection ? (bool) $timelineSection->is_enabled : false;
    $events = $timelineSection->content['events'] ?? [
        ['time' => '08:00 م', 'title' => 'استقبال الضيوف الكرام', 'desc' => 'الترحيب والمشروبات المنعشة'],
        ['time' => '09:00 م', 'title' => 'الزفّة ودخول العروسين', 'desc' => 'دخول أسطوري وزفة المحبة'],
        ['time' => '10:00 م', 'title' => 'مأدبة العشاء الفاخرة', 'desc' => 'بوفيه مفتوح مع أشهى الأطباق'],
    ];

    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $itemBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.20)' : $primaryColor . '22';
    $badgeText = $isDarkTheme ? '#151210' : '#FFFFFF';
@endphp

@if($isEnabled && !empty($events))
    <div class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};">
        <div class="text-center mb-6 space-y-2">
            <h3 class="font-cairo text-xl font-extrabold" style="color: {{ $primaryColor }};">
                {{ $timelineSection?->title_ar ?: 'برنامج الحفل ومواعيد الليلة' }}
            </h3>
        </div>

        <div class="relative max-w-md mx-auto space-y-3.5 before:content-[''] before:absolute before:top-4 before:bottom-4 before:start-5 before:w-px"
             style="--tw-before-bg: {{ $primaryColor }}40;">
            <div class="space-y-3.5">
                @foreach($events as $event)
                    <div class="relative flex items-start gap-4 p-4 rounded-2xl border transition-colors"
                         style="background-color: {{ $bgColor }}; border-color: {{ $itemBorder }};">
                        <span class="tabular-nums px-3 py-1.5 rounded-xl text-xs font-extrabold shrink-0 shadow-xs"
                              style="background-color: {{ $primaryColor }}; color: {{ $badgeText }};">
                            {{ $event['time'] ?? '—' }}
                        </span>
                        <div class="min-w-0 text-start">
                            <h4 class="font-bold text-sm" style="color: inherit;">{{ $event['title'] ?? '' }}</h4>
                            @if(!empty($event['desc']))
                                <p class="text-xs mt-1 leading-relaxed" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $event['desc'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
