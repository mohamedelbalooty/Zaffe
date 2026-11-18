@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.base :title="$isEn ? 'Set New Password — Zaffe' : 'تعيين كلمة المرور الجديدة — زفّة'">
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
                    <div class="w-12 h-12 rounded-2xl bg-[#F4EBD9] text-[#856326] border border-[#C6A76B]/30 flex items-center justify-center mx-auto">
                        <x-app-icon name="lock" class="w-5 h-5" />
                    </div>
                    <h1 class="font-cairo text-2xl sm:text-3xl font-black text-[#221E1A]">
                        {{ $isEn ? 'Set New Password' : 'تعيين كلمة المرور الجديدة' }}
                    </h1>
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'Enter your new password below to regain access to your studio' : 'أدخل كلمة المرور الجديدة لحسابك لتسجيل الدخول' }}
                    </p>
                </div>

                @if (session('status'))
                    <div role="status" class="p-4 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/25 text-[#0F4C3A] text-sm font-bold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Email Address' : 'البريد الإلكتروني' }}
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus dir="ltr"
                               class="input-atelier text-start @error('email') !border-[#BE123C] @enderror"
                               placeholder="name@example.com">
                        @error('email')
                            <p class="text-xs text-[#BE123C] mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'New Password' : 'كلمة المرور الجديدة' }}
                        </label>
                        <input type="password" id="password" name="password" required
                               class="input-atelier @error('password') !border-[#BE123C] @enderror"
                               placeholder="{{ $isEn ? 'At least 8 characters' : '8 أحرف على الأقل' }}">
                        @error('password')
                            <p class="text-xs text-[#BE123C] mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Confirm New Password' : 'تأكيد كلمة المرور' }}
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="input-atelier"
                               placeholder="{{ $isEn ? 'Re-enter new password' : 'أعد كتابة كلمة المرور' }}">
                    </div>

                    <button type="submit" class="btn-royal w-full">
                        {{ $isEn ? 'Update Password' : 'تحديث كلمة المرور' }}
                    </button>
                </form>

                <div class="text-center pt-5 border-t border-[#E4D9C8]">
                    <a href="{{ route('login') }}" class="text-sm text-[#7A5A20] font-extrabold hover:underline">
                        {{ $isEn ? 'Back to Sign In' : 'العودة لتسجيل الدخول' }}
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-layouts.base>
