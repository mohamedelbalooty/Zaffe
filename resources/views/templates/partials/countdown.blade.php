@props([
    'invitation',
    'primaryColor' => '#C6A76B',
    'cardColor' => '#FFFFFF',
    'bgColor' => '#FAF7F2',
])

@php
    $countdownSection = collect($invitation->sections ?? [])->where('section_type', 'countdown')->first();
    $isEnabled = $countdownSection ? (bool) $countdownSection->is_enabled : true;

    $dateString = is_string($invitation->wedding_date) 
        ? $invitation->wedding_date 
        : ($invitation->wedding_date?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d'));
    $timeString = !empty($invitation->wedding_time) ? substr($invitation->wedding_time, 0, 5) : '20:00';
    $tz = !empty($invitation->timezone) ? $invitation->timezone : 'Africa/Cairo';

    try {
        $targetDate = \Carbon\Carbon::parse($dateString . ' ' . $timeString, $tz)->toIso8601String();
    } catch (\Throwable $e) {
        $targetDate = \Carbon\Carbon::now($tz)->addDays(30)->toIso8601String();
    }
@endphp

@if($isEnabled && $targetDate)
    <div class="rounded-3xl p-6 sm:p-8 shadow-xl text-center border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $primaryColor }}40;"
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
        
        <h3 class="font-cairo text-base sm:text-lg font-bold mb-4" style="color: {{ $primaryColor }};">
            ⏳ المتبقي حتى ليلة العمر
        </h3>

        <div class="grid grid-cols-4 gap-2 sm:gap-4 max-w-md mx-auto">
            <div class="p-3 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}20;">
                <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="days">0</span>
                <span class="text-xs opacity-70">يوم</span>
            </div>
            <div class="p-3 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}20;">
                <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="hours">0</span>
                <span class="text-xs opacity-70">ساعة</span>
            </div>
            <div class="p-3 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}20;">
                <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="minutes">0</span>
                <span class="text-xs opacity-70">دقيقة</span>
            </div>
            <div class="p-3 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $primaryColor }}20;">
                <span class="font-cairo text-2xl sm:text-3xl font-black block" style="color: {{ $primaryColor }};" x-text="seconds">0</span>
                <span class="text-xs opacity-70">ثانية</span>
            </div>
        </div>
    </div>
@endif

