@props([
    'stages',
    'dateInfo',
    'primaryColor',
    'secondaryColor',
    'accentColor' => null,
    'cardColor' => '#FFFFFF',
    'variant' => 'thread',
])

@php
    $isEnInv = ($dateInfo['locale'] ?? 'ar') === 'en';
    $highlightColor = $accentColor ?: $primaryColor;
@endphp

<section class="pt-2 font-tajawal" aria-label="{{ $isEnInv ? 'Ceremony Schedule' : 'مراحل الحفل' }}">
    @if($variant === 'columns')
        <div class="grid grid-cols-3 divide-x rtl:divide-x-reverse max-w-md mx-auto py-1"
             style="border-color: {{ $primaryColor }}40;">
            @foreach($stages as $stage)
                <div class="flex flex-col items-center text-center px-1.5 sm:px-3" style="border-color: {{ $primaryColor }}38;">
                    <div class="h-9 flex items-center justify-center mb-1.5" aria-hidden="true">
                        @if($stage['key'] === 'reception')
                            <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 8 C 14 5.5, 17 4.5, 16 2" stroke="{{ $highlightColor }}" stroke-width="1.1" />
                                <path d="M21 9 C 20 6.5, 23 5.5, 22 3" stroke="{{ $highlightColor }}" stroke-width="1.1" />
                                <path d="M10 14 L 26 14 L 23.5 19.5 L 12.5 19.5 Z" />
                                <path d="M12.5 19.5 Q 18 22.5, 23.5 19.5" />
                                <path d="M16 20.8 L 15.2 26 L 20.8 26 L 20 20.8" />
                                <path d="M11 31 L 25 31 L 23 26 L 13 26 Z" />
                                <line x1="9" y1="31" x2="27" y2="31" />
                                <circle cx="13" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                                <circle cx="18" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                                <circle cx="23" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                            </svg>
                        @elseif($stage['key'] === 'zaffe')
                            <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 7 Q 18 10, 21.5 7 L 20.5 14.5 L 15.5 14.5 Z" />
                                <line x1="15" y1="15.5" x2="21" y2="15.5" stroke="{{ $highlightColor }}" stroke-width="1.4" />
                                <path d="M15.5 16.5 L 8.5 31 Q 18 33.5, 27.5 31 L 20.5 16.5" />
                                <path d="M16.8 18 L 13.5 31" stroke-opacity="0.45" stroke-width="0.95" />
                                <path d="M19.2 18 L 22.5 31" stroke-opacity="0.45" stroke-width="0.95" />
                            </svg>
                        @else
                            <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9 Q 13 7, 14 5" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                <path d="M18 8 Q 17 6, 18 4" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                <path d="M22 9 Q 21 7, 22 5" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                <circle cx="18" cy="12" r="1.6" />
                                <path d="M7.5 24.5 C 7.5 17.2, 12 13.8, 18 13.8 C 24 13.8, 28.5 17.2, 28.5 24.5" />
                                <path d="M12.5 22 C 13.2 18.8, 15.2 17, 17.5 16.6" stroke-opacity="0.4" stroke-width="0.95" />
                                <path d="M5.5 25 L 30.5 25" />
                                <path d="M8 25 Q 18 28, 28 25" stroke-opacity="0.65" />
                            </svg>
                        @endif
                    </div>

                    <span class="text-xs sm:text-sm font-extrabold leading-tight" style="color: {{ $secondaryColor }};">
                        {{ $stage['label'] }}
                    </span>
                    <span class="text-[11px] sm:text-xs font-semibold tabular-nums mt-0.5 opacity-85" style="color: {{ $primaryColor }};">
                        <bdi dir="{{ $dateInfo['dir'] }}">{{ $stage['time'] }}</bdi>
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div class="relative max-w-md mx-auto">
            <div class="pointer-events-none absolute top-[47px] start-[16.66%] end-[16.66%] h-px"
                 style="background-color: {{ $primaryColor }}5E;"
                 aria-hidden="true"></div>

            <div class="grid grid-cols-3 gap-2 sm:gap-4 items-start">
                @foreach($stages as $stage)
                    <div class="flex flex-col items-center text-center">
                        <div class="h-9 flex items-center justify-center mb-1.5" aria-hidden="true">
                            @if($stage['key'] === 'reception')
                                <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 8 C 14 5.5, 17 4.5, 16 2" stroke="{{ $highlightColor }}" stroke-width="1.1" />
                                    <path d="M21 9 C 20 6.5, 23 5.5, 22 3" stroke="{{ $highlightColor }}" stroke-width="1.1" />
                                    <path d="M10 14 L 26 14 L 23.5 19.5 L 12.5 19.5 Z" />
                                    <path d="M12.5 19.5 Q 18 22.5, 23.5 19.5" />
                                    <path d="M16 20.8 L 15.2 26 L 20.8 26 L 20 20.8" />
                                    <path d="M11 31 L 25 31 L 23 26 L 13 26 Z" />
                                    <line x1="9" y1="31" x2="27" y2="31" />
                                    <circle cx="13" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                                    <circle cx="18" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                                    <circle cx="23" cy="14" r="0.6" fill="{{ $primaryColor }}" />
                                </svg>
                            @elseif($stage['key'] === 'zaffe')
                                <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 7 Q 18 10, 21.5 7 L 20.5 14.5 L 15.5 14.5 Z" />
                                    <line x1="15" y1="15.5" x2="21" y2="15.5" stroke="{{ $highlightColor }}" stroke-width="1.4" />
                                    <path d="M15.5 16.5 L 8.5 31 Q 18 33.5, 27.5 31 L 20.5 16.5" />
                                    <path d="M16.8 18 L 13.5 31" stroke-opacity="0.45" stroke-width="0.95" />
                                    <path d="M19.2 18 L 22.5 31" stroke-opacity="0.45" stroke-width="0.95" />
                                </svg>
                            @else
                                <svg viewBox="0 0 36 36" class="w-8 h-8" fill="none" stroke="{{ $primaryColor }}" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 9 Q 13 7, 14 5" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                    <path d="M18 8 Q 17 6, 18 4" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                    <path d="M22 9 Q 21 7, 22 5" stroke="{{ $highlightColor }}" stroke-width="1.05" />
                                    <circle cx="18" cy="12" r="1.6" />
                                    <path d="M7.5 24.5 C 7.5 17.2, 12 13.8, 18 13.8 C 24 13.8, 28.5 17.2, 28.5 24.5" />
                                    <path d="M12.5 22 C 13.2 18.8, 15.2 17, 17.5 16.6" stroke-opacity="0.4" stroke-width="0.95" />
                                    <path d="M5.5 25 L 30.5 25" />
                                    <path d="M8 25 Q 18 28, 28 25" stroke-opacity="0.65" />
                                </svg>
                            @endif
                        </div>

                        <span class="relative z-10 w-2.5 h-2.5 rotate-45 border mb-2.5"
                              style="background-color: {{ $primaryColor }}; border-color: {{ $cardColor }};"
                              aria-hidden="true"></span>

                        <span class="text-xs sm:text-sm font-extrabold leading-tight" style="color: {{ $secondaryColor }};">
                            {{ $stage['label'] }}
                        </span>
                        <span class="text-[11px] sm:text-xs font-semibold tabular-nums mt-0.5 opacity-80" style="color: {{ $primaryColor }};">
                            <bdi dir="{{ $dateInfo['dir'] }}">{{ $stage['time'] }}</bdi>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
