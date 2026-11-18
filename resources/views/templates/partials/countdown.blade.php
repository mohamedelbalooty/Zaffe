@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'secondaryColor' => null,
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
    'isDarkTheme' => false,
])

@php
    $countdownSection = collect($invitation->sections ?? [])->where('section_type', 'countdown')->first();
    $isEnabled = $countdownSection ? (bool) $countdownSection->is_enabled : true;

    $dateString = is_string($invitation->wedding_date)
        ? $invitation->wedding_date
        : ($invitation->wedding_date?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d'));
    $timeString = !empty($invitation->wedding_time) ? substr($invitation->wedding_time, 0, 5) : '20:00';
    $tz = !empty($invitation->timezone) && in_array($invitation->timezone, timezone_identifiers_list(), true)
        ? $invitation->timezone
        : 'Africa/Cairo';

    try {
        $targetDate = \Carbon\Carbon::parse($dateString . ' ' . $timeString, $tz)->toIso8601String();
    } catch (\Throwable $e) {
        $targetDate = \Carbon\Carbon::now('Africa/Cairo')->addDays(30)->toIso8601String();
    }

    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $cellBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.22)' : $primaryColor . '28';
@endphp

@if($isEnabled && $targetDate)
    <div class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};"
         x-data="{
            target: new Date('{{ $targetDate }}').getTime(),
            now: new Date().getTime(),
            days: 0, hours: 0, minutes: 0, seconds: 0,
            update() {
                const diff = Math.max(0, this.target - new Date().getTime());
                this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
            }
         }"
         x-init="update(); setInterval(() => update(), 1000);">

        <h3 class="font-cairo text-base sm:text-lg font-extrabold mb-5 inline-flex items-center justify-center gap-2" style="color: {{ $primaryColor }};">
            <x-app-icon name="hourglass" class="w-5 h-5 shrink-0" />
            <span>المتبقي حتى ليلة العمر</span>
        </h3>

        <div class="grid grid-cols-4 gap-2 sm:gap-3.5 max-w-md mx-auto">
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="days">0</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">يوم</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="hours">0</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">ساعة</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="minutes">0</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">دقيقة</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="seconds">0</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">ثانية</span>
            </div>
        </div>
    </div>
@endif
