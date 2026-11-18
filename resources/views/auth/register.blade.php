@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.base :title="$isEn ? 'Create Account — Zaffe' : 'إنشاء حساب جديد — زفّة'">
    <div class="min-h-screen flex flex-col bg-luxury-grain">
        <header class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-sm font-bold text-[#221E1A] hover:text-[#7A5A20] transition">
                <span class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#161311] font-amiri text-lg font-bold">ز</span>
                <span class="font-cairo font-extrabold">زفّــة</span>
            </a>
            <a href="{{ route('lang.switch', $isEn ? 'ar' : 'en') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#E4D9C8] bg-white text-xs font-bold text-[#221E1A] hover:border-[#C6A76B] transition">
                <x-app-icon name="globe" class="w-3.5 h-3.5 text-[#856326]" />
                <span>{{ $isEn ? 'العربية' : 'EN' }}</span>
            </a>
        </header>

        <main id="main-content" class="flex-1 flex items-center justify-center px-4 py-8">
            <div class="max-w-md w-full card-atelier p-7 sm:p-10 space-y-6">
                <div class="text-center space-y-2">
                    <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                        {{ $isEn ? 'Create Your Free Account' : 'صمم دعوتك الآن' }}
                    </h1>
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'Set up your studio account and design your wedding card in minutes' : 'أنشئ حسابك المجاني وابدأ بتخصيص دعوة زفافك خلال دقائق' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Couple or Full Name' : 'اسم العروسين / اسمك الكريم' }}
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="input-atelier @error('name') !border-[#BE123C] @enderror"
                               placeholder="{{ $isEn ? 'Youssef & Mariam' : 'مثال: فيصل والجوهرة' }}">
                        @error('name')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Email Address' : 'البريد الإلكتروني' }}
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required dir="ltr"
                               class="input-atelier text-start @error('email') !border-[#BE123C] @enderror"
                               placeholder="couple@example.com">
                        @error('email')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Mobile / WhatsApp (Optional)' : 'رقم الهاتف أو الواتساب (اختياري)' }}
                        </label>
                        <div class="flex gap-2" dir="ltr">
                            <select name="phone_country" id="phone_country"
                                    aria-label="{{ $isEn ? 'Country Code' : 'رمز الدولة' }}"
                                    class="min-h-[44px] px-3 rounded-xl border border-[#E4D9C8] bg-[#FAF7F2] text-xs font-bold text-[#221E1A] focus:outline-none focus:border-[#B59556] shrink-0">
                                <option value="SA" {{ old('phone_country', 'SA') === 'SA' ? 'selected' : '' }}>SA (+966)</option>
                                <option value="EG" {{ old('phone_country') === 'EG' ? 'selected' : '' }}>EG (+20)</option>
                            </select>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" dir="ltr"
                                   class="input-atelier flex-1 text-left @error('phone') !border-[#BE123C] @enderror"
                                   placeholder="501 234 567">
                        </div>
                        @error('phone')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Password' : 'كلمة المرور' }}
                        </label>
                        <input type="password" id="password" name="password" required
                               class="input-atelier @error('password') !border-[#BE123C] @enderror"
                               placeholder="{{ $isEn ? 'At least 8 characters' : '8 أحرف على الأقل' }}">
                        @error('password')
                            <p class="text-xs text-[#BE123C] mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Confirm Password' : 'تأكيد كلمة المرور' }}
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="input-atelier"
                               placeholder="{{ $isEn ? 'Re-enter password' : 'أعد كتابة كلمة المرور' }}">
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="btn-royal w-full !text-base">
                            {{ $isEn ? 'Create Account & Start' : 'إنشاء حساب وتصميم الدعوة' }}
                        </button>
                    </div>
                </form>

                <div class="text-center pt-5 border-t border-[#E4D9C8]">
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'Already have an account?' : 'لديك حساب بالفعل؟' }}
                        <a href="{{ route('login') }}" class="text-[#7A5A20] font-extrabold hover:underline">
                            {{ $isEn ? 'Sign in here' : 'سجل دخولك هنا' }}
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </div>
</x-layouts.base>
