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
        : 'Asia/Riyadh';
    $digits = \App\Services\InvitationDateFormatter::resolveDigits($invitation);
    $isArabicIndic = $digits === 'arabic_indic';
    $isEnInv = \App\Services\InvitationDateFormatter::resolveLocale($invitation) === 'en';
    $zeroChar = $isArabicIndic ? '٠' : '0';

    try {
        $targetDate = \Carbon\Carbon::parse($dateString . ' ' . $timeString, $tz)->toIso8601String();
    } catch (\Throwable $e) {
        $targetDate = \Carbon\Carbon::now('Asia/Riyadh')->addDays(30)->toIso8601String();
    }

    $cardBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.28)' : $primaryColor . '35';
    $cellBorder = $isDarkTheme ? 'rgba(230, 198, 135, 0.22)' : $primaryColor . '28';
@endphp

@if($isEnabled && $targetDate)
    <div class="rounded-3xl p-6 sm:p-8 shadow-[0_14px_38px_rgba(21,18,16,0.06)] text-center border transition-all"
         style="background-color: {{ $cardColor }}; border-color: {{ $cardBorder }};"
         x-data="{
            target: new Date('{{ $targetDate }}').getTime(),
            arabicIndic: {{ $isArabicIndic ? 'true' : 'false' }},
            days: 0, hours: 0, minutes: 0, seconds: 0,
            fmt(v) {
                const s = String(v);
                if (!this.arabicIndic) return s;
                const map = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
                return s.replace(/[0-9]/g, (d) => map[Number(d)]);
            },
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
            <span>{{ $isEnInv ? 'Countdown to the Celebration' : 'المتبقي حتى ليلة العمر' }}</span>
        </h3>

        <div class="grid grid-cols-4 gap-2 sm:gap-3.5 max-w-md mx-auto">
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="fmt(days)">{{ $zeroChar }}</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $isEnInv ? 'Days' : 'يوم' }}</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="fmt(hours)">{{ $zeroChar }}</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $isEnInv ? 'Hours' : 'ساعة' }}</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="fmt(minutes)">{{ $zeroChar }}</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $isEnInv ? 'Mins' : 'دقيقة' }}</span>
            </div>
            <div class="p-3 sm:p-3.5 rounded-2xl border" style="background-color: {{ $bgColor }}; border-color: {{ $cellBorder }};">
                <span class="font-cairo tabular-nums text-2xl sm:text-3xl font-black block leading-tight" style="color: {{ $primaryColor }};" x-text="fmt(seconds)">{{ $zeroChar }}</span>
                <span class="text-xs font-semibold block mt-0.5" style="{{ $secondaryColor ? "color: {$secondaryColor}; opacity: 0.9;" : 'opacity: 0.78;' }}">{{ $isEnInv ? 'Secs' : 'ثانية' }}</span>
            </div>
        </div>
    </div>
@endif
