@php
    $isEn = app()->getLocale() === 'en';
    $storedPhone = (string) ($user->phone ?? '');
    $detectedPhoneCountry = match (true) {
        str_starts_with($storedPhone, '+20') => 'EG',
        str_starts_with($storedPhone, '+966') => 'SA',
        $storedPhone !== '' && str_starts_with($storedPhone, '+') => 'OTHER',
        default => 'SA',
    };
    $defaultPhoneCountry = old('phone_country', $detectedPhoneCountry);
@endphp

<x-layouts.dashboard :title="$isEn ? 'Profile & Security — Zaffe' : 'الملف الشخصي والحساب — زفّة'">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                {{ $isEn ? 'Profile & Account Settings' : 'الملف الشخصي والحساب' }}
            </h1>
            <p class="text-xs sm:text-sm text-[#52493F] mt-1">
                {{ $isEn ? 'Update your display name, contact number, and account password' : 'تعديل بيانات حسابك، كلمة المرور، ومعلومات التواصل' }}
            </p>
        </div>

        <div class="card-atelier p-6 sm:p-8">
            <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                        {{ $isEn ? 'Couple or Account Name' : 'الاسم الكريم / اسم العروسين' }}
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="input-atelier @error('name') !border-[#BE123C] @enderror">
                    @error('name')
                        <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                        {{ $isEn ? 'Email Address' : 'البريد الإلكتروني' }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr"
                           class="input-atelier text-start @error('email') !border-[#BE123C] @enderror">
                    @error('email')
                        <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ phoneCountry: @js($defaultPhoneCountry) }">
                    <label for="phone" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                        {{ $isEn ? 'Phone / WhatsApp Number (Optional)' : 'رقم الهاتف / الواتساب (اختياري)' }}
                    </label>
                    <div class="flex gap-2" dir="ltr">
                        <select name="phone_country" id="phone_country" x-model="phoneCountry"
                                aria-label="{{ $isEn ? 'Country Code' : 'رمز الدولة' }}"
                                class="min-h-[44px] px-3 rounded-xl border border-[#E4D9C8] bg-[#FAF7F2] text-xs font-bold text-[#221E1A] focus:outline-none focus:border-[#B59556] shrink-0">
                            <option value="SA" {{ $defaultPhoneCountry === 'SA' ? 'selected' : '' }}>SA (+966)</option>
                            <option value="EG" {{ $defaultPhoneCountry === 'EG' ? 'selected' : '' }}>EG (+20)</option>
                            <option value="OTHER" {{ $defaultPhoneCountry === 'OTHER' ? 'selected' : '' }}>{{ $isEn ? 'Other (+)' : 'دولة أخرى (+)' }}</option>
                        </select>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr"
                               class="input-atelier flex-1 text-left @error('phone') !border-[#BE123C] @enderror"
                               :placeholder="phoneCountry === 'OTHER' ? '+971 50 123 4567' : (phoneCountry === 'EG' ? '+20 100 123 4567' : '+966 50 000 0000')"
                               placeholder="+966 50 000 0000">
                    </div>
                    @error('phone')
                        <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-5 border-t border-[#E4D9C8] space-y-4">
                    <div>
                        <h2 class="font-cairo text-base font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Change Password (Optional)' : 'تغيير كلمة المرور (اختياري)' }}
                        </h2>
                        <p class="text-xs text-[#52493F] mt-0.5">
                            {{ $isEn ? 'Leave blank to preserve your current password.' : 'اترك الحقول فارغة إذا كنت لا ترغب بتغيير كلمة المرور الحالية.' }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'New Password' : 'كلمة المرور الجديدة' }}
                        </label>
                        <input type="password" id="password" name="password"
                               class="input-atelier @error('password') !border-[#BE123C] @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Confirm New Password' : 'تأكيد كلمة المرور الجديدة' }}
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="input-atelier"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="submit" class="btn-royal !px-7">
                        <x-app-icon name="check" class="w-4 h-4" />
                        <span>{{ $isEn ? 'Save Changes' : 'حفظ التغييرات' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
