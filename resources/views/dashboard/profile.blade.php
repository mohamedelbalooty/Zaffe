<x-layouts.dashboard :title="'الملف الشخصي والحساب — زفّة'">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="font-cairo text-2xl sm:text-3xl font-extrabold text-[#292521]">الملف الشخصي والحساب</h1>
            <p class="text-xs sm:text-sm text-[#292521]/60 mt-1">تعديل بيانات حسابك، كلمة المرور، ومعلومات التواصل</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E8DED0] shadow-sm space-y-6">
            <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-bold text-[#292521] mb-1.5">الاسم الكريم / اسم العروسين</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-[#292521] mb-1.5">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-[#292521] mb-1.5">رقم الهاتف / الواتساب</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr"
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none text-right"
                           placeholder="+20 100 000 0000">
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-[#E8DED0] space-y-4">
                    <h3 class="font-cairo text-base font-bold text-[#292521]">تغيير كلمة المرور (اختياري)</h3>
                    <p class="text-xs text-[#292521]/60">اترك الحقول فارغة إذا كنت لا ترغب بتغيير كلمة المرور الحالية.</p>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#292521] mb-1.5">كلمة المرور الجديدة</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none"
                               placeholder="••••••••">
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[#292521] mb-1.5">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" 
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:opacity-95 transition">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>

