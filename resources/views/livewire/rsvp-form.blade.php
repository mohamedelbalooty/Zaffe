<div class="w-full">
    @if($isSubmitted)
        <div class="text-center p-8 bg-white/95 rounded-3xl border border-[#C6A76B]/40 shadow-xl space-y-4 animate-fade-in">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-md">
                <x-app-icon name="check-circle" class="w-10 h-10" />
            </div>
            
            <h3 class="font-cairo text-2xl font-bold text-[#292521]">
                {{ $attendance_status === 'attending' ? 'تم تأكيد حضورك بنجاح' : 'شكراً لك، نعتز باعتذارك اللطيف' }}
            </h3>

            <p class="text-sm text-[#292521]/75 max-w-md mx-auto leading-relaxed">
                {{ $attendance_status === 'attending' 
                    ? 'شكراً لك يا ' . $guest_name . '، يسعدنا تشريفك ومشاركتنا فرحة هذا اليوم السعيد.' 
                    : 'شكراً يا ' . $guest_name . ' لمشاركتنا التهنئة. دعواتكم الصادقة للعروسين بالتوفيق والبركة تعني لنا الكثير.' }}
            </p>

            @if($attendance_status === 'attending' && $issuedPassUrl)
                <div class="pt-2 flex flex-col items-center gap-3">
                    <div class="inline-flex flex-col items-center p-4 rounded-2xl bg-[#FAF7F2] border border-[#C6A76B]/35 shadow-sm">
                        <span class="text-xs font-bold text-[#8A6D3B] mb-2">بطاقة الدخول الخاصة بكم (صالح لمرة واحدة عند البوابة)</span>
                        <div class="bg-white p-2 rounded-xl border border-[#E8DED0]">
                            {!! app(\App\Services\QrCodeService::class)->renderSvg($issuedPassUrl, 185, '#111827', '#FFFFFF', 'بطاقة الدخول ' . $guest_name) !!}
                        </div>
                        @if($issuedPassCode)
                            <div class="mt-2.5 flex items-center gap-2 text-xs">
                                <span class="text-[#292521]/60">كود الدخول البديل:</span>
                                <span dir="ltr" class="font-mono font-black tracking-widest text-[#292521] bg-white px-2.5 py-1 rounded-lg border border-[#E8DED0]">
                                    {{ $issuedPassCode }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <a href="{{ $issuedPassUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#292521] text-white text-xs font-bold hover:opacity-90 transition">
                        <x-app-icon name="qr-code" class="w-4 h-4" />
                        <span>فتح وحفظ بطاقة الدخول للطباعة</span>
                    </a>
                </div>
            @elseif($attendance_status === 'attending' && $passPreviouslyIssued)
                <p class="text-xs text-[#8A6D3B] bg-[#FAF7F2] border border-[#C6A76B]/30 rounded-xl px-4 py-3 max-w-md mx-auto leading-relaxed">
                    تم تحديث تأكيد الحضور بنجاح. لحماية خصوصية بطاقتك، يرجى الاحتفاظ ببطاقة الدخول الصادرة لك مسبقاً أو مراجعة أصحاب الحفل لإعادة مشاركتها.
                </p>
            @endif

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
                @if($guestId)
                    <div class="mt-2 inline-flex flex-wrap items-center justify-center gap-2 text-[11px] font-bold text-[#8A6D3B] bg-[#C6A76B]/10 px-3 py-1 rounded-full">
                        <span>المقاعد المخصصة لك: حتى {{ $maxGuests }} بالغين</span>
                        @if($maxChildren > 0)
                            <span>و {{ $maxChildren }} أطفال</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Attendance Choice -->
            <div>
                <label class="block text-xs font-bold text-[#292521] mb-2">هل ستشرفنا بالحضور؟</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex items-center justify-center gap-2 text-xs font-bold transition {{ $attendance_status === 'attending' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-[#292521]' : 'border-[#E8DED0] hover:bg-[#FAF7F2]' }}">
                        <input type="radio" wire:model.live="attendance_status" value="attending" class="hidden">
                        <x-app-icon name="check" class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>بكل سرور، سأحضر</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex items-center justify-center gap-2 text-xs font-bold transition {{ $attendance_status === 'declined' ? 'border-rose-400 bg-rose-50 text-rose-900' : 'border-[#E8DED0] hover:bg-[#FAF7F2]' }}">
                        <input type="radio" wire:model.live="attendance_status" value="declined" class="hidden">
                        <x-app-icon name="x" class="w-4 h-4 text-rose-500 shrink-0" />
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
                        <option value="SA">+966 (السعودية)</option>
                        <option value="EG">+20 (مصر)</option>
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
                <div class="{{ $maxChildren > 0 ? 'grid grid-cols-1 sm:grid-cols-2 gap-3' : '' }}">
                    <div>
                        <label class="block text-xs font-bold text-[#292521] mb-1.5">عدد البالغين (شاملاً حضرتك)</label>
                        <select wire:model="guest_count" class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none font-bold">
                            @for($i = 1; $i <= $maxGuests; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'فرد واحد' : ($i == 2 ? 'فردان' : 'أفراد') }}</option>
                            @endfor
                        </select>
                        @error('guest_count')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($maxChildren > 0)
                        <div>
                            <label class="block text-xs font-bold text-[#292521] mb-1.5">عدد الأطفال المرافقين</label>
                            <select wire:model="children_count" class="w-full px-4 py-3 rounded-xl border border-[#E8DED0] text-sm bg-[#FAF7F2]/60 focus:border-[#C6A76B] outline-none font-bold">
                                @for($c = 0; $c <= $maxChildren; $c++)
                                    <option value="{{ $c }}">{{ $c === 0 ? 'بدون أطفال' : $c . ($c === 1 ? ' طفل' : ' أطفال') }}</option>
                                @endfor
                            </select>
                            @error('children_count')
                                <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
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
