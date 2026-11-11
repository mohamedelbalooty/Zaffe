<x-layouts.base :title="'استعادة كلمة المرور — زفّة'">
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-b from-[#FAF7F2] via-[#F4EBD9]/40 to-[#FAF7F2] relative overflow-hidden">
        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-wedding border border-[#C6A76B]/25 relative z-10">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#C6A76B] to-[#DFC99E] flex items-center justify-center text-[#292521] font-amiri text-2xl font-bold shadow-md">
                        ز
                    </div>
                </a>
                <h1 class="font-cairo text-2xl font-extrabold text-[#292521]">استعادة كلمة المرور</h1>
                <p class="text-sm text-[#292521]/60 mt-2">أدخل بريدك الإلكتروني وسنرسل لك رابط استعادة الحساب</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-bold text-[#292521] mb-2">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none transition text-sm bg-[#FAF7F2]/50"
                           placeholder="name@example.com">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-base shadow-md hover:shadow-lg hover:opacity-95 transition">
                    إرسال رابط الاستعادة
                </button>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-[#E8DED0]/60">
                <a href="{{ route('login') }}" class="text-sm text-[#C6A76B] font-bold hover:underline">
                    العودة لتسجيل الدخول
                </a>
            </div>
        </div>
    </div>
</x-layouts.base>

