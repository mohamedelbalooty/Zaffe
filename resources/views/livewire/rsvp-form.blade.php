<div class="w-full">
    @if($isSubmitted)
        <div class="text-center p-8 bg-white/95 rounded-3xl border border-[#C6A76B]/40 shadow-xl space-y-4 animate-fade-in">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-md">
                <x-app-icon name="check-circle" class="w-10 h-10" />
            </div>
            
            <h3 class="font-cairo text-2xl font-bold text-[#292521]">
                {{ $attendance_status === 'attending' ? 'تم تأكيد حضورك بنجاح! 🎉' : 'شكراً لك، نعتز باعتذارك الرقيق 🤍' }}
            </h3>

            <p class="text-sm text-[#292521]/75 max-w-md mx-auto leading-relaxed">
                {{ $attendance_status === 'attending' 
                    ? 'شكراً لك يا ' . $guest_name . '، نحن في غاية الشوق للقائك ومشاركتنا فرحة هذا اليوم الكبير!' 
                    : 'شكراً يا ' . $guest_name . ' لمشاركتنا التهنئة. دعواتكم الصادقة للعروسين بالبركة والتوفيق تعني لنا الكثير.' }}
            </p>

            <div class="pt-4">
                <button wire:click="resetForm" class="text-xs text-[#C6A76B] font-bold hover:underline">
                    تعديل ردك أو إرسال رد إضافي
                </button>
            </div>
        </div>
    @else
        <form wire:submit="submit" class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-[#C6A76B]/30 shadow-xl space-y-5 text-right">
            <div class="text-center pb-3 border-b border-[#E8DED0]">
                <h3 class="font-cairo text-xl sm:text-2xl font-bold text-[#292521]">تأكيد الحضور (RSVP)</h3>
                <p class="text-xs text-[#292521]/60 mt-1">يسعدنا تأكيد حضوركم لمساعدتنا في ترتيب مقاعد القاعة والضيافة</p>
            </div>

            <!-- Attendance Choice -->
            <div>
                <label class="block text-xs font-bold text-[#292521] mb-2">هل ستشرفنا بالحضور؟</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex items-center justify-center gap-2 text-xs font-bold transition {{ $attendance_status === 'attending' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-[#292521]' : 'border-[#E8DED0] hover:bg-[#FAF7F2]' }}">
                        <input type="radio" wire:model.live="attendance_status" value="attending" class="hidden">
                        <span class="text-emerald-600">✓</span>
                        <span>بكل سرور، سأحضر</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex items-center justify-center gap-2 text-xs font-bold transition {{ $attendance_status === 'declined' ? 'border-rose-400 bg-rose-50 text-rose-900' : 'border-[#E8DED0] hover:bg-[#FAF7F2]' }}">
                        <input type="radio" wire:model.live="attendance_status" value="declined" class="hidden">
                        <span class="text-rose-500">✕</span>
                        <span>أعتذر بكل لطف</span>
                    </label>
                </div>
            </div>

            <!-- Guest Name -->
            <div>
                <label for="rsvp_guest_name" class="block text-xs font-bold text-[#292521] mb-1.5">الاسم الكريم / العائلة</label>
                <input type="text" id="rsvp_guest_name" wire:model="guest_name" required
                       class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none"
                       placeholder="مثال: المستشار أحمد الديب وعائلته">
                @error('guest_name')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone / WhatsApp -->
            <div>
                <label for="rsvp_phone" class="block text-xs font-bold text-[#292521] mb-1.5">رقم الهاتف أو الواتساب (اختياري للتذكير)</label>
                <div class="flex items-center gap-2">
                    <select wire:model.live="phone_country" class="px-3 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none font-bold shrink-0">
                        <option value="EG">🇪🇬 +20 (مصر)</option>
                        <option value="SA">🇸🇦 +966 (السعودية)</option>
                    </select>
                    <input type="tel" id="rsvp_phone" wire:model="phone" dir="ltr"
                           class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] focus:ring-2 focus:ring-[#C6A76B]/20 outline-none text-right"
                           placeholder="100 000 0000">
                </div>
                @error('phone')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            @if($attendance_status === 'attending')
                <!-- Guest Count -->
                <div>
                    <label class="block text-xs font-bold text-[#292521] mb-1.5">إجمالي عدد الحضور (شاملاً حضرتك والمرافقين)</label>
                    <div class="flex items-center gap-3">
                        <select wire:model="guest_count" class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none font-bold">
                            @for($i = 1; $i <= $maxGuests; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'فرد واحد' : ($i == 2 ? 'فردان' : 'أفراد') }}</option>
                            @endfor
                        </select>
                    </div>
                    @error('guest_count')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Dietary Preferences -->
                <div>
                    <label class="block text-xs font-bold text-[#292521] mb-1.5">ملاحظات الوجبات الغذائية والحساسية (اختياري)</label>
                    <div class="grid grid-cols-3 gap-2 mb-2 text-xs">
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border border-[#E8DED0] bg-[#FAF7F2]/40 cursor-pointer hover:bg-[#FAF7F2]">
                            <input type="checkbox" wire:model="selected_dietary" value="وجبة نباتية" class="rounded text-[#C6A76B]">
                            <span>نباتي</span>
                        </label>
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border border-[#E8DED0] bg-[#FAF7F2]/40 cursor-pointer hover:bg-[#FAF7F2]">
                            <input type="checkbox" wire:model="selected_dietary" value="خالٍ من الجلوتين" class="rounded text-[#C6A76B]">
                            <span>خالٍ من الجلوتين</span>
                        </label>
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border border-[#E8DED0] bg-[#FAF7F2]/40 cursor-pointer hover:bg-[#FAF7F2]">
                            <input type="checkbox" wire:model="selected_dietary" value="حساسية مكسرات" class="rounded text-[#C6A76B]">
                            <span>حساسية مكسرات</span>
                        </label>
                    </div>
                    <input type="text" wire:model="dietary_notes" 
                           class="w-full px-4 py-2 rounded-xl border border-[#E8DED0] text-xs bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none"
                           placeholder="أي تفضيلات أو حساسيات طعام أخرى...">
                </div>
            @endif

            <!-- Message to the couple -->
            <div>
                <label for="rsvp_message" class="block text-xs font-bold text-[#292521] mb-1.5">تهنئة أو كلمة للعروسين (اختياري)</label>
                <textarea id="rsvp_message" wire:model="message" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none"
                          placeholder="ألف مبروك للعروسين وبارك الله لكما..."></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white font-bold text-sm shadow-md hover:opacity-95 transition flex items-center justify-center gap-2">
                    <span wire:loading.remove>إرسال تأكيد الحضور</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>جاري الإرسال...</span>
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
