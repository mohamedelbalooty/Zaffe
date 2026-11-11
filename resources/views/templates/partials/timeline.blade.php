@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
])

@php
    $timelineSection = collect($invitation->sections ?? [])->where('section_type', 'timeline')->first();
    $isEnabled = $timelineSection ? (bool) $timelineSection->is_enabled : false;
    $events = $timelineSection->content['events'] ?? [
        ['time' => '08:00 م', 'title' => 'استقبال الضيوف الكرام', 'desc' => 'الترحيب والمشروبات المنعشة'],
        ['time' => '09:00 م', 'title' => 'الزفّة ودخول العروسين', 'desc' => 'دخول أسطوري وزفة المحبة'],
        ['time' => '10:00 م', 'title' => 'مأدبة العشاء الفاخرة', 'desc' => 'بوفيه مفتوح مع أشهى الأطباق'],
    ];
@endphp

@if($isEnabled && !empty($events))
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;">
        <h3 class="font-cairo text-xl font-bold text-center mb-6" style="color: {{ $primaryColor }};">
            {{ $timelineSection?->title_ar ?: 'برنامج الحفل ومواعيد الليلة' }}
        </h3>
        <div class="space-y-4 max-w-md mx-auto">
            @foreach($events as $event)
                <div class="flex items-start gap-4 p-4 rounded-2xl border"
                     style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}20;">
                    <span class="px-3 py-1 rounded-xl text-xs font-bold text-white shrink-0 shadow-sm"
                          style="background-color: {{ $primaryColor }};">
                        {{ $event['time'] ?? '—' }}
                    </span>
                    <div>
                        <h4 class="font-bold text-sm" style="color: inherit;">{{ $event['title'] ?? '' }}</h4>
                        @if(!empty($event['desc']))
                            <p class="text-xs opacity-75 mt-0.5">{{ $event['desc'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
