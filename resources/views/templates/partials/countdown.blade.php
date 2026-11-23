@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
    'isDarkTheme' => false,
])

@php
    $countdown = \App\Services\InvitationDateFormatter::countdownConfig($invitation);
    $isEnabled = $countdown['enabled'];
    $targetDate = $countdown['target_iso'];
    $isArabicIndic = $countdown['is_arabic_indic'];
    $isEnInv = $countdown['is_english'];
    $zeroChar = $countdown['zero_char'];
    $countdownKey = $countdown['key'];
    $countdownUnits = $countdown['units'];

    $lightSubtleBorder = \App\Support\TemplateStyleCatalog::hexWithAlpha(
        (string) $primaryColor,
        '28',
        'rgba(198, 167, 107, 0.16)'
    );
    $cardBorder = $isDarkTheme
        ? 'rgba(230, 198, 135, 0.28)'
        : \App\Support\TemplateStyleCatalog::hexWithAlpha((string) $primaryColor, '35', 'rgba(198, 167, 107, 0.21)');
    $cellBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.22)' : $lightSubtleBorder;
    $seamColor = $isDarkTheme ? 'rgba(0, 0, 0, 0.55)' : $lightSubtleBorder;
@endphp

@if($isEnabled && $targetDate)
    <div wire:key="zaffe-countdown-{{ $countdownKey }}"
         class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }}; --flip-bg: {{ $bgColor }}; --flip-accent: {{ $primaryColor }}; --flip-border: {{ $cellBorder }}; --flip-seam: {{ $seamColor }};"
         x-data="{
            target: new Date('{{ $targetDate }}').getTime(),
            arabicIndic: {{ $isArabicIndic ? 'true' : 'false' }},
            initialized: false,
            expired: false,
            intervalId: null,
            units: {
                days: { curr: 0, prev: 0, flipping: false, timer: null },
                hours: { curr: 0, prev: 0, flipping: false, timer: null },
                minutes: { curr: 0, prev: 0, flipping: false, timer: null },
                seconds: { curr: 0, prev: 0, flipping: false, timer: null },
            },
            fmt(v) {
                const s = String(v);
                if (!this.arabicIndic) return s;
                const map = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
                return s.replace(/[0-9]/g, (d) => map[Number(d)]);
            },
            prefersReducedMotion() {
                return Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
            },
            setUnit(key, nextVal) {
                const unit = this.units[key];
                if (!this.initialized || this.prefersReducedMotion()) {
                    unit.prev = nextVal;
                    unit.curr = nextVal;
                    unit.flipping = false;
                    return;
                }
                if (unit.curr === nextVal) return;
                if (unit.timer) {
                    clearTimeout(unit.timer);
                    unit.timer = null;
                }
                unit.prev = unit.curr;
                unit.curr = nextVal;
                unit.flipping = false;
                this.$nextTick(() => {
                    const tile = this.$refs['tile_' + key];
                    if (tile) void tile.offsetWidth;
                    requestAnimationFrame(() => {
                        unit.flipping = true;
                        unit.timer = setTimeout(() => {
                            unit.prev = unit.curr;
                            unit.flipping = false;
                            unit.timer = null;
                        }, 560);
                    });
                });
            },
            update() {
                const diff = Math.max(0, this.target - Date.now());
                const nextDays = Math.floor(diff / 86400000);
                const nextHours = Math.floor((diff % 86400000) / 3600000);
                const nextMinutes = Math.floor((diff % 3600000) / 60000);
                const nextSeconds = Math.floor((diff % 60000) / 1000);

                this.setUnit('days', nextDays);
                this.setUnit('hours', nextHours);
                this.setUnit('minutes', nextMinutes);
                this.setUnit('seconds', nextSeconds);
                this.initialized = true;

                if (diff <= 0 && !this.expired) {
                    this.expired = true;
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                }
            },
            start() {
                this.update();
                if (!this.expired) {
                    this.intervalId = setInterval(() => this.update(), 1000);
                }
            },
            destroy() {
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                    this.intervalId = null;
                }
                Object.values(this.units).forEach((u) => {
                    if (u.timer) {
                        clearTimeout(u.timer);
                        u.timer = null;
                    }
                });
            }
         }"
         x-init="start()"
         @visibilitychange.window="if (!document.hidden && !expired) update()">

        <h3 class="font-cairo text-base sm:text-lg font-extrabold mb-5 inline-flex items-center justify-center gap-2" style="color: {{ $primaryColor }};">
            <x-app-icon name="hourglass" class="w-5 h-5 shrink-0" />
            <span>{{ $isEnInv ? 'Countdown to the Celebration' : 'المتبقي حتى ليلة العمر' }}</span>
        </h3>

        <div class="grid grid-cols-4 gap-2 sm:gap-3.5 max-w-md mx-auto">
            @foreach($countdownUnits as $unit)
                <div class="flex flex-col items-center">
                    <div x-ref="tile_{{ $unit['key'] }}"
                         :class="{ 'is-flipping': units.{{ $unit['key'] }}.flipping }"
                         class="zaffe-flip-tile {{ $isDarkTheme ? 'zaffe-flip-tile--dark' : '' }}">
                        <div class="zaffe-flip-half zaffe-flip-half--top">
                            <span class="zaffe-flip-text text-xl sm:text-3xl"
                                  dir="ltr"
                                  x-text="fmt(units.{{ $unit['key'] }}.curr)">{{ $zeroChar }}</span>
                        </div>

                        <div class="zaffe-flip-half zaffe-flip-half--bottom" aria-hidden="true">
                            <span class="zaffe-flip-text text-xl sm:text-3xl"
                                  dir="ltr"
                                  x-text="fmt(units.{{ $unit['key'] }}.flipping ? units.{{ $unit['key'] }}.prev : units.{{ $unit['key'] }}.curr)">{{ $zeroChar }}</span>
                        </div>

                        <div class="zaffe-flip-half zaffe-flip-half--top zaffe-flip-flap-top" aria-hidden="true">
                            <span class="zaffe-flip-text text-xl sm:text-3xl"
                                  dir="ltr"
                                  x-text="fmt(units.{{ $unit['key'] }}.prev)">{{ $zeroChar }}</span>
                        </div>

                        <div class="zaffe-flip-half zaffe-flip-half--bottom zaffe-flip-flap-bottom" aria-hidden="true">
                            <span class="zaffe-flip-text text-xl sm:text-3xl"
                                  dir="ltr"
                                  x-text="fmt(units.{{ $unit['key'] }}.curr)">{{ $zeroChar }}</span>
                        </div>

                        <div class="zaffe-flip-seam" aria-hidden="true"></div>
                    </div>

                    <span class="text-[11px] sm:text-xs font-semibold block mt-2"
                          style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $unit['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
