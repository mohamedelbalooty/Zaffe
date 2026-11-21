@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.dashboard :title="$isEn ? 'Create Wedding Invitation — Zaffe' : 'إنشاء دعوة زفاف جديدة — زفّة'">
    <div class="max-w-4xl mx-auto space-y-7" x-data="{ selectedTemplate: '{{ $selectedTemplateId ?? ($templates->first()?->id ?? 1) }}' }">
        <div class="space-y-2">
            <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                {{ $isEn ? 'Create a New Wedding Invitation' : 'صمم بطاقة زفافك الجديدة' }}
            </h1>
            <p class="text-xs sm:text-sm text-[#52493F]">
                {{ $isEn
                    ? 'Select a theme and fill in the core wedding details; you will immediately enter the live customization studio.'
                    : 'اختر القالب الأقرب لذوقك، ثم أدخل البيانات الأساسية وسننقلك فوراً إلى محرر التخصيص الكامل.' }}
            </p>
        </div>

        <form method="POST" action="{{ route('dashboard.invitations.store') }}" class="space-y-6">
            @csrf

            <!-- 1. Choose Template -->
            <div class="card-atelier p-6 sm:p-7 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-[#E4D9C8]">
                    <h2 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                        {{ $isEn ? '1. Choose a Design Theme' : '١. اختر قالب التصميم' }}
                    </h2>
                    <span class="text-xs text-[#52493F]">
                        {{ $isEn ? 'You can switch themes anytime in the studio' : 'يمكنك تغيير القالب لاحقاً في أي وقت' }}
                    </span>
                </div>

                <input type="hidden" name="template_id" :value="selectedTemplate">

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($templates as $tmpl)
                        @php
                            $primaryColor = $tmpl->default_styles['primary_color'] ?? '#C6A76B';
                        @endphp
                        <button type="button"
                                @click="selectedTemplate = '{{ $tmpl->id }}'"
                                :class="selectedTemplate == '{{ $tmpl->id }}' ? 'border-[#856326] ring-2 ring-[#C6A76B]/35 bg-[#FAF7F2]' : 'border-[#E4D9C8] bg-white hover:border-[#C6A76B]/60'"
                                class="rounded-xl p-2.5 border-2 cursor-pointer transition flex flex-col gap-2.5 text-start group">

                            <div class="relative h-36 sm:h-40 w-full rounded-lg overflow-hidden bg-[#221E1A]">
                                <!-- Graceful Fallback (Gradient + Template Name) -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-3 text-center"
                                     style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                    <div class="w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white mb-1.5">
                                        <x-app-icon name="sparkles" class="w-4 h-4" />
                                    </div>
                                    <span class="font-cairo text-xs font-bold text-white line-clamp-1">{{ $tmpl->name_ar }}</span>
                                    @if($tmpl->name_en)
                                        <span class="text-[10px] font-medium text-white/80 line-clamp-1 mt-0.5" dir="ltr">{{ $tmpl->name_en }}</span>
                                    @endif
                                </div>

                                @if($tmpl->thumbnail)
                                    <img src="{{ $tmpl->thumbnail }}"
                                         alt=""
                                         loading="lazy"
                                         onerror="this.style.display='none'"
                                         class="relative z-10 w-full h-full object-cover object-top group-hover:scale-[1.03] transition-transform duration-300">
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-2 w-full px-0.5">
                                <div class="min-w-0 flex-1">
                                    <span class="font-cairo text-xs sm:text-sm font-bold text-[#221E1A] block truncate">{{ $tmpl->name_ar }}</span>
                                    @if($tmpl->name_en)
                                        <span class="text-[10px] text-[#6E6458] font-medium block truncate" dir="ltr">{{ $tmpl->name_en }}</span>
                                    @endif
                                </div>
                                <div :class="selectedTemplate == '{{ $tmpl->id }}' ? 'bg-[#856326] text-white border-[#856326]' : 'border-[#D5C9B6] text-transparent'"
                                     class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 transition">
                                    <x-app-icon name="check" class="w-3 h-3" />
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                @error('template_id')
                    <p class="text-xs text-[#BE123C] font-bold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Basic Info -->
            <div class="card-atelier p-6 sm:p-7 space-y-5">
                <div class="pb-3 border-b border-[#E4D9C8]">
                    <h2 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                        {{ $isEn ? '2. Couple & Celebration Details' : '٢. بيانات العروسين وموعد الحفل' }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="groom_name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Groom Name' : 'اسم العريس الكريم' }}
                        </label>
                        <input type="text" id="groom_name" name="groom_name" value="{{ old('groom_name') }}" required
                               placeholder="{{ $isEn ? 'e.g. Faisal Al-Otaibi' : 'مثال: فيصل بن خالد العتيبي' }}"
                               class="input-atelier @error('groom_name') !border-[#BE123C] @enderror">
                        @error('groom_name')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bride_name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Bride Name or Family Honorific (Optional)' : 'اسم العروس أو الكنية العائلية (اختياري)' }}
                        </label>
                        <input type="text" id="bride_name" name="bride_name" value="{{ old('bride_name') }}"
                               placeholder="{{ $isEn ? 'e.g. Sarah, Daughter of Mansour Family, or leave blank' : 'مثال: الجوهرة أو كريمة عائلة المنصور (أو اتركه فارغاً)' }}"
                               class="input-atelier @error('bride_name') !border-[#BE123C] @enderror">
                        @error('bride_name')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="wedding_date" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Wedding Date' : 'تاريخ حفل الزفاف' }}
                        </label>
                        <input type="date" id="wedding_date" name="wedding_date" value="{{ old('wedding_date', now()->addDays(30)->toDateString()) }}" required
                               class="input-atelier @error('wedding_date') !border-[#BE123C] @enderror">
                        @error('wedding_date')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="venue_name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Hall or Hotel Venue' : 'اسم القاعة أو الفندق' }}
                        </label>
                        <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name') }}" required
                               placeholder="{{ $isEn ? 'e.g. Ritz-Carlton Ballroom' : 'مثال: قاعة الريتز كارلتون الرياض / فورسيزونز القاهرة' }}"
                               class="input-atelier @error('venue_name') !border-[#BE123C] @enderror">
                        @error('venue_name')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="timezone" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                        {{ $isEn ? 'Event Timezone' : 'المنطقة الزمنية لموعد الحفل' }}
                    </label>
                    <select id="timezone" name="timezone" class="input-atelier">
                        <option value="Asia/Riyadh" {{ old('timezone', 'Asia/Riyadh') === 'Asia/Riyadh' ? 'selected' : '' }}>
                            {{ $isEn ? 'Saudi Arabia / Riyadh (Asia/Riyadh)' : 'توقيت السعودية والرياض (Asia/Riyadh)' }}
                        </option>
                        <option value="Africa/Cairo" {{ old('timezone', 'Asia/Riyadh') === 'Africa/Cairo' ? 'selected' : '' }}>
                            {{ $isEn ? 'Egypt / Cairo (Africa/Cairo)' : 'توقيت مصر والقاهرة (Africa/Cairo)' }}
                        </option>
                    </select>
                    @error('timezone')
                        <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3 flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard.invitations.index') }}" class="btn-ghost-ivory">
                        {{ $isEn ? 'Cancel' : 'إلغاء' }}
                    </a>
                    <button type="submit" class="btn-royal !px-7">
                        <span>{{ $isEn ? 'Continue to Live Studio' : 'متابعة إلى المحرر المباشر' }}</span>
                        <x-app-icon name="arrow-left" class="w-4 h-4 dir-arrow" />
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
