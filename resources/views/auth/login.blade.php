@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.base :title="$isEn ? 'Sign In — Zaffe' : 'تسجيل الدخول — زفّة'">
    <div class="min-h-screen flex flex-col bg-luxury-grain">
        <!-- Minimal Auth Top Bar -->
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
                        {{ $isEn ? 'Sign In to Zaffe' : 'تسجيل الدخول' }}
                    </h1>
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'Welcome back to your wedding invitation studio' : 'أهلاً بك مجدداً في استوديو دعوات زفّة' }}
                    </p>
                </div>

                @if(app()->environment(['local', 'testing']))
                    <div class="p-3 rounded-xl bg-[#FAF7F2] border border-[#C6A76B]/35 text-xs text-[#221E1A] flex items-center justify-between gap-2">
                        <span class="font-semibold">{{ $isEn ? 'Demo couple account:' : 'حساب تجريبي للعروسين:' }}</span>
                        <button type="button"
                                onclick="document.getElementById('email').value='couple@zaffe.app'; document.getElementById('password').value='password123';"
                                class="text-[#7A5A20] font-extrabold hover:underline cursor-pointer">
                            {{ $isEn ? 'Autofill (couple@zaffe.app)' : 'تعبئة تلقائية (couple@zaffe.app)' }}
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                            {{ $isEn ? 'Email Address' : 'البريد الإلكتروني' }}
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus dir="ltr"
                               class="input-atelier text-start @error('email') !border-[#BE123C] @enderror"
                               placeholder="name@example.com">
                        @error('email')
                            <p class="text-xs text-[#BE123C] mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-[#221E1A]">
                                {{ $isEn ? 'Password' : 'كلمة المرور' }}
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-[#7A5A20] font-bold hover:underline">
                                {{ $isEn ? 'Forgot password?' : 'نسيت كلمة المرور؟' }}
                            </a>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="input-atelier @error('password') !border-[#BE123C] @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <p class="text-xs text-[#BE123C] mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer min-h-[36px]">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E4D9C8] accent-[#856326]">
                            <span class="text-xs font-semibold text-[#52493F]">
                                {{ $isEn ? 'Keep me signed in' : 'تذكرني على هذا الجهاز' }}
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn-royal w-full !text-base">
                        {{ $isEn ? 'Sign In' : 'تسجيل الدخول' }}
                    </button>
                </form>

                <div class="text-center pt-5 border-t border-[#E4D9C8]">
                    <p class="text-sm text-[#52493F]">
                        {{ $isEn ? 'New to Zaffe?' : 'ليس لديك حساب بعد؟' }}
                        <a href="{{ route('register') }}" class="text-[#7A5A20] font-extrabold hover:underline">
                            {{ $isEn ? 'Create your free account' : 'ابدأ بإنشاء حسابك مجاناً' }}
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </div>
</x-layouts.base>
