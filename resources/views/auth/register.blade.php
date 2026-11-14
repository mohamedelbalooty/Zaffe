<x-layouts.base :title="'إنشاء حساب جديد — زفّة'">
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-b from-[#FAF7F2] via-[#F4EBD9]/40 to-[#FAF7F2] relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-[#C6A76B]/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-[#D9B7B0]/15 blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-wedding border border-[#C6A76B]/25 relative z-10">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#292521] font-amiri text-2xl font-bold shadow-md group-hover:scale-105 transition-transform">
                        ز
                    </div>
                </a>
                <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">صمم دعوتك الآن</h1>
                <p class="text-sm text-[#292521]/60 mt-2">أنشئ حسابك وابدأ بتخصيص دعوة زفافك في 3 دقائق</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold text-[#292521] mb-1.5">اسم العروسين / اسمك الكريم</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                           placeholder="مثال: يوسف ومريم">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-[#292521] mb-1.5">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                           placeholder="youssef@example.com">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-[#292521] mb-1.5">رقم الهاتف أو الواتساب (اختياري)</label>
                    <div class="flex rounded-xl border border-[#E8DED0] focus-within:border-[#C6A76B] focus-within:ring-2 focus-within:ring-[#C6A76B]/20 overflow-hidden bg-[#FAF7F2]/50 transition">
                        <select name="phone_country" id="phone_country" class="px-3 py-3 bg-[#FAF7F2] border-none text-xs font-bold text-[#292521] focus:outline-none cursor-pointer border-l border-[#E8DED0]">
                            <option value="EG" {{ old('phone_country', 'EG') === 'EG' ? 'selected' : '' }}>مصر (+20) 🇪🇬</option>
                            <option value="SA" {{ old('phone_country') === 'SA' ? 'selected' : '' }}>السعودية (+966) 🇸🇦</option>
                        </select>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" dir="ltr"
                               class="w-full px-4 py-3 border-none outline-none text-sm bg-transparent text-left"
                               placeholder="100 123 4567">
                    </div>
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-[#292521] mb-1.5">كلمة المرور</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                           placeholder="8 أحرف على الأقل">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-[#292521] mb-1.5">تأكيد كلمة المرور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                           placeholder="أعد كتابة كلمة المرور">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-base shadow-md hover:shadow-lg hover:opacity-95 transition">
                        إنشاء حساب وتصميم الدعوة
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-[#E8DED0]/60">
                <p class="text-sm text-[#292521]/70">
                    لديك حساب بالفعل؟ 
                    <a href="{{ route('login') }}" class="text-[#C6A76B] font-bold hover:underline">سجل دخولك هنا</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.base>

