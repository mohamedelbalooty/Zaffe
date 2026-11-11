<x-layouts.base :title="'تسجيل الدخول — زفّة'">
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-b from-[#FAF7F2] via-[#F4EBD9]/40 to-[#FAF7F2] relative overflow-hidden">
        <!-- Decorative Ornaments -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-[#C6A76B]/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-[#D9B7B0]/15 blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-wedding border border-[#C6A76B]/25 relative z-10">
            <!-- Header Brand & Title -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#292521] font-amiri text-2xl font-bold shadow-md group-hover:scale-105 transition-transform">
                        ز
                    </div>
                </a>
                <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">تسجيل الدخول</h1>
                <p class="text-sm text-[#292521]/60 mt-2">أهلاً بك مجدداً في استوديو دعوات زفّة</p>
            </div>

            <!-- Demo Quick Fill Pill -->
            <div class="mb-6 p-3 rounded-2xl bg-[#FAF7F2] border border-[#C6A76B]/30 text-xs text-[#292521]/80 flex items-center justify-between">
                <span>حساب تجريبي للعروسين:</span>
                <button type="button" 
                        onclick="document.getElementById('email').value='couple@zaffe.app'; document.getElementById('password').value='password123';"
                        class="text-[#C6A76B] font-bold hover:underline">
                    تعبئة تلقائية (couple@zaffe.app)
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-[#292521] mb-2">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                               placeholder="name@example.com">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-bold text-[#292521]">كلمة المرور</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-[#C6A76B] font-semibold hover:underline">نسيت كلمة المرور؟</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E8DED0] text-[#C6A76B] focus:ring-[#C6A76B]">
                        <span class="text-xs text-[#292521]/70">تذكرني على هذا الجهاز</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-base shadow-md hover:shadow-lg hover:opacity-95 transition">
                    تسجيل الدخول
                </button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-[#E8DED0]/60">
                <p class="text-sm text-[#292521]/70">
                    ليس لديك حساب بعد؟ 
                    <a href="{{ route('register') }}" class="text-[#C6A76B] font-bold hover:underline">ابدأ بإنشاء حسابك مجاناً</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.base>

