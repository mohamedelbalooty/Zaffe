<div x-data="{ mobilePane: 'editor' }"
     class="h-screen h-dvh flex flex-col bg-[#161412] text-[#E4D9C8] overflow-hidden font-tajawal">
    <!-- Top Studio Header Toolbar -->
    <header class="min-h-14 sm:min-h-16 bg-[#24201C] border-b border-[#C6A76B]/25 px-3 sm:px-5 py-2 flex items-center justify-between gap-2 sm:gap-3 z-40 shrink-0">
        <!-- Start: Back & Invitation Title -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 lg:flex-initial">
            <a href="{{ route('dashboard.invitations.index') }}"
               class="inline-flex items-center gap-1.5 min-h-[38px] px-3 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/15 border border-white/10 text-[#FAF7F2] transition text-xs font-bold shrink-0">
                <x-app-icon name="arrow-right" class="w-4 h-4 text-[#DFC99E] dir-arrow" />
                <span class="hidden sm:inline">لوحة التحكم</span>
            </a>

            <div class="h-5 w-px bg-white/15 hidden sm:block shrink-0"></div>

            <div class="flex items-center gap-2 min-w-0">
                <h1 class="font-cairo text-xs sm:text-base font-extrabold text-white truncate max-w-[140px] sm:max-w-[220px] xl:max-w-xs">
                    {{ $title ?: 'تعديل دعوة الزفاف' }}
                </h1>

                @if($status === 'published')
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-400/30 items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>منشورة أونلاين</span>
                    </span>
                @else
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-200 border border-amber-400/30 whitespace-nowrap shrink-0">
                        مسودة خاصة
                    </span>
                @endif
            </div>
        </div>

        <!-- Center: Device Viewport Switcher (SVG Icons Only) -->
        <div class="hidden lg:flex items-center bg-black/35 p-1 rounded-xl border border-white/10 shrink-0">
            <button type="button"
                    wire:click="$set('previewDevice', 'mobile')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'mobile' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white' }}">
                <x-app-icon name="smartphone" class="w-3.5 h-3.5" />
                <span>هاتف</span>
            </button>
            <button type="button"
                    wire:click="$set('previewDevice', 'tablet')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'tablet' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white' }}">
                <x-app-icon name="tablet" class="w-3.5 h-3.5" />
                <span>تابلت</span>
            </button>
            <button type="button"
                    wire:click="$set('previewDevice', 'desktop')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'desktop' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white' }}">
                <x-app-icon name="monitor" class="w-3.5 h-3.5" />
                <span>شاشة كاملة</span>
            </button>
        </div>

        <!-- End: Autosave Status, Publish & Save -->
        <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
            <span class="text-xs text-[#DFC99E] font-semibold hidden xl:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.04] border border-white/10 whitespace-nowrap">
                {{ $saveStatus }}
            </span>

            <a href="{{ route('dashboard.invitations.share', $invitation->id) }}"
               class="min-h-[38px] px-2.5 sm:px-3 py-1.5 rounded-lg bg-white/[0.08] hover:bg-white/15 border border-white/10 text-white text-xs font-bold flex items-center gap-1.5 transition whitespace-nowrap">
                <x-app-icon name="image" class="w-4 h-4 text-[#DFC99E]" />
                <span class="hidden sm:inline">بطاقة واتساب</span>
            </a>

            @if($status === 'published')
                <a href="{{ $invitation->public_url }}" target="_blank" rel="noopener noreferrer"
                   class="min-h-[38px] px-2.5 sm:px-3 py-1.5 rounded-lg bg-white/[0.08] hover:bg-white/15 border border-white/10 text-white text-xs font-bold flex items-center gap-1.5 transition whitespace-nowrap">
                    <x-app-icon name="external-link" class="w-4 h-4 text-[#DFC99E]" />
                    <span class="hidden sm:inline">معاينة الرابط</span>
                </a>
            @endif

            <button type="button"
                    wire:click="togglePublish"
                    class="min-h-[38px] px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-bold shadow-sm transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer {{ $status === 'published' ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-[#0F4C3A] hover:bg-[#14634C] text-white border border-emerald-400/30' }}">
                @if($status === 'published')
                    <span class="hidden sm:inline">إلغاء النشر</span>
                    <span class="sm:hidden">مسودة</span>
                @else
                    <x-app-icon name="sparkles" class="w-4 h-4 text-[#DFC99E]" />
                    <span class="hidden sm:inline">نشر الدعوة الآن</span>
                    <span class="sm:hidden">نشر</span>
                @endif
            </button>

            <button type="button"
                    wire:click="saveChanges"
                    class="min-h-[38px] px-3 sm:px-4 py-1.5 rounded-lg bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-[#151210] text-xs sm:text-sm font-extrabold shadow-sm hover:brightness-105 transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                <x-app-icon name="check" class="w-4 h-4" />
                <span class="hidden sm:inline">حفظ</span>
            </button>
        </div>
    </header>

    <!-- Mobile / Tablet Pane Switcher -->
    <div class="lg:hidden bg-[#1E1A16] border-b border-[#C6A76B]/20 px-3 py-2 flex items-center justify-between gap-2 shrink-0">
        <div class="flex-1 grid grid-cols-2 gap-1.5 bg-black/35 p-1 rounded-xl border border-white/10">
            <button type="button"
                    @click="mobilePane = 'editor'"
                    :class="mobilePane === 'editor' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white'"
                    class="min-h-[38px] py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                <x-app-icon name="edit" class="w-3.5 h-3.5" />
                <span>التعديل والإعدادات</span>
            </button>
            <button type="button"
                    @click="mobilePane = 'preview'"
                    :class="mobilePane === 'preview' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/75 hover:text-white'"
                    class="min-h-[38px] py-1.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                <x-app-icon name="eye" class="w-3.5 h-3.5" />
                <span>معاينة الدعوة الحية</span>
            </button>
        </div>
    </div>

    <!-- Main Split Studio Workspace -->
    <div class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">

        <!-- Studio Controls Sidebar -->
        <aside :class="mobilePane === 'editor' ? 'flex flex-1 min-h-0 lg:flex-none' : 'hidden lg:flex'"
               class="w-full lg:w-[410px] xl:w-[455px] bg-[#24201C] lg:border-e border-[#C6A76B]/20 flex-col lg:shrink-0 min-h-0 overflow-hidden">
            <!-- 6-Tab Navigation Grid with Icons -->
            <div class="grid grid-cols-3 border-b border-white/10 p-2 gap-1.5 bg-black/25 shrink-0">
                @php
                    $tabs = [
                        ['key' => 'basics', 'icon' => 'calendar', 'label' => 'البيانات الأساسية'],
                        ['key' => 'content', 'icon' => 'type', 'label' => 'نصوص الدعوة'],
                        ['key' => 'design', 'icon' => 'palette', 'label' => 'الألوان والخطوط'],
                        ['key' => 'media', 'icon' => 'image', 'label' => 'الصور والألبوم'],
                        ['key' => 'templates', 'icon' => 'layout-grid', 'label' => 'تبديل القالب'],
                        ['key' => 'rsvp', 'icon' => 'sliders', 'label' => 'إعدادات الحضور'],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <button type="button"
                            wire:click="$set('activeTab', '{{ $tab['key'] }}')"
                            class="px-2 py-2 rounded-lg text-[11px] font-bold transition flex items-center justify-center gap-1.5 text-center cursor-pointer {{ $activeTab === $tab['key'] ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/80 hover:bg-white/[0.06] hover:text-white' }}">
                        <x-app-icon :name="$tab['icon']" class="w-3.5 h-3.5 shrink-0" />
                        <span class="truncate">{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Scrollable Form Controls -->
            <div class="flex-1 min-h-0 overflow-y-auto studio-scroll p-4 sm:p-5 space-y-5">

                <!-- 1. Basics Tab -->
                @if($activeTab === 'basics')
                    <div class="space-y-4">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">بيانات العروسين وحفل الزفاف</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="ed_groom_name" class="block text-xs font-bold text-white mb-1.5">اسم العريس</label>
                                <input id="ed_groom_name" type="text" wire:model.live.debounce.300ms="groom_name"
                                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                            <div>
                                <label for="ed_bride_name" class="block text-xs font-bold text-white mb-1.5">اسم العروس (اختياري)</label>
                                <input id="ed_bride_name" type="text" wire:model.live.debounce.300ms="bride_name"
                                       placeholder="اختياري — مثال: سارة أو كريمة عائلة المنصور"
                                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="ed_title" class="block text-xs font-bold text-white mb-1.5">عنوان الدعوة</label>
                            <input id="ed_title" type="text" wire:model.live.debounce.300ms="title"
                                   class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="ed_wedding_date" class="block text-xs font-bold text-white mb-1.5">تاريخ الحفل</label>
                                <input id="ed_wedding_date" type="date" wire:model.live="wedding_date"
                                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                            <div>
                                <label for="ed_wedding_time" class="block text-xs font-bold text-white mb-1.5">ساعة بدء الحفل</label>
                                <input id="ed_wedding_time" type="time" wire:model.live="wedding_time"
                                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="ed_timezone" class="block text-xs font-bold text-white mb-1.5">المنطقة الزمنية لموعد الحفل</label>
                            <select id="ed_timezone" wire:model.live="timezone"
                                    class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                                <option value="Asia/Riyadh" class="bg-[#24201C] text-white">توقيت السعودية والرياض (Asia/Riyadh)</option>
                                <option value="Africa/Cairo" class="bg-[#24201C] text-white">توقيت مصر والقاهرة (Africa/Cairo)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="ed_locale" class="block text-xs font-bold text-white mb-1.5">لغة الدعوة</label>
                                <select id="ed_locale" wire:model.live="invitation_locale"
                                        class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                                    <option value="ar" class="bg-[#24201C] text-white">العربية</option>
                                    <option value="en" class="bg-[#24201C] text-white">English</option>
                                </select>
                            </div>
                            <div>
                                <label for="ed_digits" class="block text-xs font-bold text-white mb-1.5">شكل الأرقام</label>
                                <select id="ed_digits" wire:model.live="digits"
                                        class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none">
                                    <option value="latin" class="bg-[#24201C] text-white">أرقام قياسية (0-9)</option>
                                    <option value="arabic_indic" class="bg-[#24201C] text-white">أرقام عربية (٠-٩)</option>
                                </select>
                            </div>
                        </div>

                        <label class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-white/[0.05] border border-white/10 cursor-pointer hover:bg-white/[0.08] transition">
                            <span class="text-xs font-bold text-white">عرض التاريخ الهجري (تقويم أم القرى)</span>
                            <input type="checkbox" wire:model.live="show_hijri" class="w-4 h-4 rounded accent-[#C6A76B] shrink-0">
                        </label>

                        <div>
                            <label for="ed_venue_name" class="block text-xs font-bold text-white mb-1.5">اسم مكان الحفل (القاعة / الفندق)</label>
                            <input id="ed_venue_name" type="text" wire:model.live.debounce.300ms="venue_name"
                                   class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none"
                                   placeholder="مثال: فندق فورسيزونز قاعة بلازا">
                        </div>

                        <div>
                            <label for="ed_venue_address" class="block text-xs font-bold text-white mb-1.5">العنوان التفصيلي</label>
                            <input id="ed_venue_address" type="text" wire:model.live.debounce.300ms="venue_address"
                                   class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none"
                                   placeholder="كورنيش النيل، جاردن سيتي، القاهرة">
                        </div>

                        <div>
                            <label for="ed_venue_map_url" class="block text-xs font-bold text-white mb-1.5">رابط خريطة Google Maps</label>
                            <input id="ed_venue_map_url" type="url" wire:model.live.debounce.300ms="venue_map_url" dir="ltr"
                                   class="w-full min-h-[42px] px-3.5 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none text-left"
                                   placeholder="https://maps.google.com/?q=...">
                        </div>
                    </div>
                @endif

                <!-- 2. Content Tab -->
                @if($activeTab === 'content')
                    <div class="space-y-4">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">نصوص وكلمات الدعوة</h2>

                        <div>
                            <label for="ed_welcome_message" class="block text-xs font-bold text-white mb-1.5">افتتاحية الدعوة (آية قرآنية أو مقولة ترحيبية)</label>
                            <textarea id="ed_welcome_message" wire:model.live.debounce.400ms="welcome_message" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label for="ed_invitation_message" class="block text-xs font-bold text-white mb-1.5">نص الدعوة الرئيسي</label>
                            <textarea id="ed_invitation_message" wire:model.live.debounce.400ms="invitation_message" rows="4"
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white/[0.06] border border-white/15 text-white text-sm focus:border-[#C6A76B] outline-none leading-relaxed"></textarea>
                        </div>

                        @if($hasLegacyMusic)
                            <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/10 space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-[#DFC99E]">مقطع صوتي محفوظ مسبقاً (إرث قديم)</span>
                                    <button type="button"
                                            wire:click="clearLegacyMusicUrl"
                                            class="px-3 py-1.5 rounded-lg bg-rose-500/15 hover:bg-rose-500/25 border border-rose-400/30 text-rose-200 text-[11px] font-bold transition cursor-pointer">
                                        إزالة الصوت
                                    </button>
                                </div>
                                <p class="text-[11px] text-[#E4D9C8]/70 leading-relaxed">
                                    الرابط الصوتي المحفوظ مسبقاً لا يزال مرتبطاً بهذه البطاقة ويمكنك إزالته في أي وقت. تم إخفاء إضافة روابط الصوت الخارجية على البطاقات الجديدة.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- 3. Design Tab -->
                @if($activeTab === 'design')
                    <div class="space-y-5">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">تخصيص الهوية البصرية والألوان</h2>

                        <div>
                            <label class="block text-xs font-bold text-white mb-2">اللون الأساسي (ألوان العناوين والزخارف)</label>
                            <div class="flex flex-wrap items-center gap-2.5">
                                <input type="color" wire:model.live="primary_color" aria-label="اختيار اللون الأساسي" class="w-11 h-10 rounded-lg cursor-pointer bg-transparent border-0 shrink-0">
                                <input type="text" wire:model.live="primary_color" dir="ltr" class="px-3 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-xs font-mono w-24 uppercase">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" wire:click="$set('primary_color', '#C6A76B')" class="w-7 h-7 rounded-full bg-[#C6A76B] border border-white/30 cursor-pointer" title="ذهبي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#D9828A')" class="w-7 h-7 rounded-full bg-[#D9828A] border border-white/30 cursor-pointer" title="وردي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#0F4C3A')" class="w-7 h-7 rounded-full bg-[#0F4C3A] border border-white/30 cursor-pointer" title="زمردي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#8B263E')" class="w-7 h-7 rounded-full bg-[#8B263E] border border-white/30 cursor-pointer" title="عنابي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#1F2937')" class="w-7 h-7 rounded-full bg-[#1F2937] border border-white/30 cursor-pointer" title="فحمي"></button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-2">لون خلفية الدعوة</label>
                            <div class="flex flex-wrap items-center gap-2.5">
                                <input type="color" wire:model.live="background_color" aria-label="اختيار لون الخلفية" class="w-11 h-10 rounded-lg cursor-pointer bg-transparent border-0 shrink-0">
                                <input type="text" wire:model.live="background_color" dir="ltr" class="px-3 py-2 rounded-xl bg-white/[0.06] border border-white/15 text-white text-xs font-mono w-24 uppercase">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" wire:click="$set('background_color', '#FAF7F2')" class="w-7 h-7 rounded-full bg-[#FAF7F2] border border-white/30 cursor-pointer" title="عاجي"></button>
                                    <button type="button" wire:click="$set('background_color', '#FDF8F7')" class="w-7 h-7 rounded-full bg-[#FDF8F7] border border-white/30 cursor-pointer" title="باستيل"></button>
                                    <button type="button" wire:click="$set('background_color', '#FAFAFA')" class="w-7 h-7 rounded-full bg-[#FAFAFA] border border-white/30 cursor-pointer" title="أبيض"></button>
                                    <button type="button" wire:click="$set('background_color', '#0F172A')" class="w-7 h-7 rounded-full bg-[#0F172A] border border-white/30 cursor-pointer" title="ليلي كحلي"></button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-2">نوع الخط العربي المعتمد</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-bold">
                                @foreach([
                                    ['val' => 'Tajawal', 'label' => 'خط تجوال (Tajawal)', 'cls' => 'font-tajawal'],
                                    ['val' => 'Cairo', 'label' => 'خط كايرو (Cairo)', 'cls' => 'font-cairo'],
                                    ['val' => 'Amiri', 'label' => 'خط أميري (Amiri)', 'cls' => 'font-amiri text-sm'],
                                    ['val' => 'Playfair Display', 'label' => 'Playfair Serif', 'cls' => 'font-playfair'],
                                ] as $f)
                                    <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $font_family === $f['val'] ? 'border-[#C6A76B] bg-[#C6A76B]/15 text-white' : 'border-white/10 bg-white/[0.03] hover:bg-white/[0.06] text-[#E4D9C8]/85' }}">
                                        <input type="radio" wire:model.live="font_family" value="{{ $f['val'] }}" class="sr-only">
                                        <span class="{{ $f['cls'] }}">{{ $f['label'] }}</span>
                                        <x-app-icon name="check" class="w-3.5 h-3.5 {{ $font_family === $f['val'] ? 'text-[#DFC99E]' : 'opacity-20' }}" />
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 4. Media Tab -->
                @if($activeTab === 'media')
                    <div class="space-y-5">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">صور العروسين وألبوم الذكريات (اختياري)</h2>

                        <div class="p-4 rounded-xl bg-white/[0.05] border border-white/10 space-y-3">
                            <label class="block text-xs font-bold text-white">صورة الغلاف / الشعار الرئيسي</label>

                            @if($cover_photo)
                                <div class="w-28 h-28 rounded-xl overflow-hidden border-2 border-[#C6A76B] shadow-md">
                                    <img src="{{ $cover_photo }}" alt="Cover" class="w-full h-full object-cover">
                                </div>
                            @endif

                            <input type="file" wire:model="new_cover_photo" accept="image/*"
                                   class="w-full max-w-full text-xs text-[#E4D9C8]/80 file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#C6A76B] file:text-[#151210] hover:file:opacity-90">

                            <div wire:loading wire:target="new_cover_photo" class="text-xs text-[#DFC99E]">
                                جاري رفع صورة الغلاف...
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-white/[0.05] border border-white/10 space-y-3">
                            <label class="block text-xs font-bold text-white">إضافة صور إلى ألبوم الفرح</label>
                            <input type="file" wire:model="new_gallery_photo" accept="image/*"
                                   class="w-full max-w-full text-xs text-[#E4D9C8]/80 file:me-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-[#C6A76B] file:text-xs file:font-bold file:bg-[#1E1A16] file:text-[#DFC99E]">

                            <div wire:loading wire:target="new_gallery_photo" class="text-xs text-[#DFC99E]">
                                جاري رفع الصورة إلى الألبوم...
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-2">
                                @foreach($invitation->photos as $p)
                                    <div class="relative group rounded-xl overflow-hidden h-24 border border-white/15 bg-black/30">
                                        <img src="{{ $p->url }}" alt="Gallery photo" class="w-full h-full object-cover">
                                        <button type="button"
                                                wire:click="deleteGalleryPhoto({{ $p->id }})"
                                                aria-label="حذف الصورة"
                                                class="absolute top-1.5 start-1.5 p-1.5 bg-[#BE123C] text-white rounded-lg opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition shadow cursor-pointer">
                                            <x-app-icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 5. Templates Switcher Tab -->
                @if($activeTab === 'templates')
                    <div class="space-y-4">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">اختر قالب الدعوة</h2>
                        <p class="text-xs text-[#E4D9C8]/75">يمكنك تبديل مظهر القالب بضغطة زر وستظهر التغييرات فوراً في المعاينة الحية:</p>

                        <div class="grid grid-cols-1 min-[380px]:grid-cols-2 gap-3">
                            @foreach($templates as $tmpl)
                                @php
                                    $primaryColor = $tmpl->default_styles['primary_color'] ?? '#C6A76B';
                                @endphp
                                <button type="button" wire:click="changeTemplate({{ $tmpl->id }})"
                                        class="rounded-xl p-2 border text-start transition flex flex-col gap-2 cursor-pointer {{ $template_id == $tmpl->id ? 'border-[#C6A76B] bg-[#C6A76B]/15 shadow-sm' : 'border-white/10 bg-white/[0.04] hover:border-white/30' }}">
                                    <div class="relative h-24 w-full rounded-lg overflow-hidden bg-[#161412]">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-2 text-center"
                                             style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                            <span class="font-cairo text-[11px] font-bold text-white line-clamp-1">{{ $tmpl->name_ar }}</span>
                                            @if($tmpl->name_en)
                                                <span class="text-[9px] text-white/80 line-clamp-1" dir="ltr">{{ $tmpl->name_en }}</span>
                                            @endif
                                        </div>
                                        @if($tmpl->thumbnail)
                                            <img src="{{ $tmpl->thumbnail }}"
                                                 alt=""
                                                 loading="lazy"
                                                 onerror="this.style.display='none'"
                                                 class="relative z-10 w-full h-full object-cover object-top">
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between gap-2 w-full">
                                        <div class="min-w-0 flex-1">
                                            <span class="text-xs font-bold text-white block truncate">{{ $tmpl->name_ar }}</span>
                                            @if($tmpl->name_en)
                                                <span class="text-[10px] text-[#E4D9C8]/65 block truncate" dir="ltr">{{ $tmpl->name_en }}</span>
                                            @endif
                                        </div>
                                        @if($template_id == $tmpl->id)
                                            <x-app-icon name="check" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 6. RSVP & Settings Tab -->
                @if($activeTab === 'rsvp')
                    <div class="space-y-4">
                        <h2 class="font-cairo text-sm font-extrabold text-[#DFC99E]">تأكيد الحضور (اختياري)</h2>

                        <div class="p-4 rounded-xl bg-white/[0.05] border border-white/10 space-y-3.5">
                            <label for="ed_is_rsvp_enabled" class="flex items-start justify-between gap-3 cursor-pointer">
                                <span class="space-y-1">
                                    <span class="block text-xs font-bold text-white">تفعيل استمارة تأكيد الحضور (RSVP)</span>
                                    <span class="block text-[11px] text-[#E4D9C8]/75 leading-relaxed">
                                        عند التعطيل تكون الدعوة للعرض والمشاركة فقط بدون استمارة رد. لن تُحذف أي ردود محفوظة مسبقاً عند إيقاف الاستمارة.
                                    </span>
                                </span>
                                <input id="ed_is_rsvp_enabled" type="checkbox" wire:model.live="is_rsvp_enabled" class="w-4 h-4 mt-0.5 rounded accent-[#C6A76B] shrink-0">
                            </label>

                            @if($is_rsvp_enabled)
                                <div class="pt-3 border-t border-white/10 space-y-3">
                                    <div>
                                        <label for="ed_max_guests" class="block text-xs font-bold text-white mb-1">أقصى عدد مرافقين مسموح للضيف الواحد</label>
                                        <input id="ed_max_guests" type="number" min="1" max="20" wire:model.live="max_guests_per_rsvp"
                                               class="w-full min-h-[40px] px-3.5 py-2 rounded-xl bg-black/35 border border-white/15 text-white text-sm outline-none focus:border-[#C6A76B]">
                                    </div>

                                    <div>
                                        <label for="ed_rsvp_deadline" class="block text-xs font-bold text-white mb-1">الموعد النهائي لتأكيد الحضور (Deadline)</label>
                                        <input id="ed_rsvp_deadline" type="date" wire:model.live="rsvp_deadline"
                                               class="w-full min-h-[40px] px-3.5 py-2 rounded-xl bg-black/35 border border-white/15 text-white text-sm outline-none focus:border-[#C6A76B]">
                                    </div>
                                </div>
                            @endif

                            <div class="pt-3 border-t border-white/10">
                                <a href="{{ route('dashboard.invitations.guests', $invitation->id) }}"
                                   class="btn-royal w-full !min-h-[40px] !py-2 !text-xs">
                                    <x-app-icon name="users" class="w-4 h-4" />
                                    <span>{{ $hasLegacyRoster ? 'إدارة قائمة المدعوين وبطاقات QR' : 'سجل ردود تأكيد الحضور (RSVP)' }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Sections Toggles (SVG Icons Only) -->
                        <h3 class="font-cairo text-sm font-extrabold text-[#DFC99E] pt-2">أقسام الدعوة</h3>
                        <div class="p-4 rounded-xl bg-white/[0.05] border border-white/10 space-y-3">
                            <label class="flex items-center justify-between gap-3 cursor-pointer py-1">
                                <span class="flex items-center gap-2 text-xs font-bold text-white">
                                    <x-app-icon name="hourglass" class="w-4 h-4 text-[#DFC99E]" />
                                    <span>عداد الأيام التنازلي</span>
                                </span>
                                <input type="checkbox" wire:model.live="section_countdown" class="w-4 h-4 rounded accent-[#C6A76B]">
                            </label>
                            @if($hasCustomTimeline)
                                <label class="flex items-center justify-between gap-3 cursor-pointer py-1">
                                    <span class="flex items-center gap-2 text-xs font-bold text-white">
                                        <x-app-icon name="calendar" class="w-4 h-4 text-[#DFC99E]" />
                                        <span>جدول مواعيد وبرنامج الحفل</span>
                                    </span>
                                    <input type="checkbox" wire:model.live="section_timeline" class="w-4 h-4 rounded accent-[#C6A76B]">
                                </label>
                            @endif
                            <label class="flex items-center justify-between gap-3 cursor-pointer py-1">
                                <span class="flex items-center gap-2 text-xs font-bold text-white">
                                    <x-app-icon name="image" class="w-4 h-4 text-[#DFC99E]" />
                                    <span>ألبوم الصور</span>
                                </span>
                                <input type="checkbox" wire:model.live="section_gallery" class="w-4 h-4 rounded accent-[#C6A76B]">
                            </label>
                            @if($hasCustomDressCode)
                                <label class="flex items-center justify-between gap-3 cursor-pointer py-1">
                                    <span class="flex items-center gap-2 text-xs font-bold text-white">
                                        <x-app-icon name="shirt" class="w-4 h-4 text-[#DFC99E]" />
                                        <span>قواعد اللباس (Dress Code)</span>
                                    </span>
                                    <input type="checkbox" wire:model.live="section_dress_code" class="w-4 h-4 rounded accent-[#C6A76B]">
                                </label>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <!-- Live Preview Stage Area -->
        <div :class="mobilePane === 'preview' ? 'flex flex-1 min-h-0' : 'hidden lg:flex'"
             class="flex-1 min-h-0 flex-col bg-studio-grid overflow-hidden">
            <!-- Mobile / Tablet Device Switcher -->
            <div class="lg:hidden flex items-center justify-center p-2.5 border-b border-white/10 bg-[#1E1A16] shrink-0">
                <div class="flex items-center bg-black/35 p-1 rounded-xl border border-white/10">
                    <button type="button" wire:click="$set('previewDevice', 'mobile')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'mobile' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/70 hover:text-white' }}">
                        <x-app-icon name="smartphone" class="w-3.5 h-3.5" />
                        <span>هاتف</span>
                    </button>
                    <button type="button" wire:click="$set('previewDevice', 'tablet')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'tablet' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/70 hover:text-white' }}">
                        <x-app-icon name="tablet" class="w-3.5 h-3.5" />
                        <span>تابلت</span>
                    </button>
                    <button type="button" wire:click="$set('previewDevice', 'desktop')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold flex items-center gap-1.5 transition cursor-pointer {{ $previewDevice === 'desktop' ? 'bg-[#C6A76B] text-[#151210] shadow-sm' : 'text-[#E4D9C8]/70 hover:text-white' }}">
                        <x-app-icon name="monitor" class="w-3.5 h-3.5" />
                        <span>شاشة كاملة</span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Preview Canvas -->
            <div class="flex-1 min-h-0 overflow-y-auto studio-scroll p-3 sm:p-6 lg:p-8">
                <div class="mx-auto transition-all duration-300 {{ $previewDevice === 'mobile' ? 'max-w-[26rem] w-full rounded-[2.25rem] shadow-2xl border-[6px] border-[#2E2924] overflow-hidden my-2' : ($previewDevice === 'tablet' ? 'max-w-2xl w-full rounded-3xl shadow-2xl border-[6px] border-[#2E2924] overflow-hidden my-2' : 'w-full max-w-4xl rounded-2xl shadow-2xl border border-white/10 overflow-hidden my-2') }}">
                    @include(\App\Support\TemplateStyleCatalog::resolveBladeView($invitation->template->view_name ?? null), [
                        'invitation' => $previewObject,
                        'isPreview' => true,
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- Publish & Share Modal -->
    @if($showPublishModal && $status === 'published')
        <div x-data="{ copied: false }"
             @keydown.escape.window="$wire.closePublishModal()"
             role="dialog"
             aria-modal="true"
             aria-labelledby="publish-modal-title"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm animate-fade-in">
            <div class="absolute inset-0" wire:click="closePublishModal"></div>

            <div class="relative z-10 w-full max-w-md rounded-2xl bg-[#24201C] border border-[#C6A76B]/50 shadow-2xl overflow-hidden text-start">
                <div class="h-1.5 w-full bg-gradient-to-r from-[#C6A76B] via-[#DFC99E] to-[#B59556]"></div>

                <div class="p-6 sm:p-7 space-y-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0">
                                <x-app-icon name="check-circle" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 id="publish-modal-title" class="font-cairo text-lg sm:text-xl font-extrabold text-white">
                                    تم نشر الدعوة بنجاح!
                                </h3>
                                <p class="text-xs text-[#DFC99E] mt-0.5">دعوتك متاحة الآن للمشاركة</p>
                            </div>
                        </div>

                        <button type="button"
                                wire:click="closePublishModal"
                                aria-label="إغلاق"
                                class="p-2 rounded-lg bg-white/5 hover:bg-white/15 text-[#E4D9C8]/75 hover:text-white transition cursor-pointer">
                            <x-app-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>

                    <p class="text-xs sm:text-sm text-[#E4D9C8]/85 leading-relaxed">
                        أصبحت بطاقة دعوتك جاهزة الآن للمعاينة والمشاركة مع ضيوفك. يمكنك تصفح الرابط المباشر أو نسخه وإرساله عبر واتساب.
                    </p>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#DFC99E]">رابط الدعوة المباشر</label>
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-[#161412] border border-[#C6A76B]/30">
                            <input type="text"
                                   readonly
                                   dir="ltr"
                                   value="{{ $invitation->public_url }}"
                                   onclick="this.select()"
                                   class="flex-1 min-w-0 bg-transparent px-2.5 py-1.5 text-xs text-[#FAF7F2] font-mono outline-none select-all">

                            <button type="button"
                                    @click="navigator.clipboard?.writeText('{{ $invitation->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                    class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                                <x-app-icon name="copy" class="w-3.5 h-3.5 text-[#DFC99E]" x-show="!copied" />
                                <x-app-icon name="check" class="w-3.5 h-3.5 text-emerald-300" x-show="copied" x-cloak />
                                <span x-text="copied ? 'تم النسخ!' : 'نسخ'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2.5 pt-1">
                        <a href="{{ route('dashboard.invitations.share', $invitation->id) }}"
                           class="btn-royal w-full">
                            <x-app-icon name="image" class="w-4 h-4" />
                            <span>تحميل ومشاركة صورة الدعوة لواتساب</span>
                        </a>

                        <a href="{{ $invitation->public_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white font-bold text-xs transition flex items-center justify-center gap-2">
                            <x-app-icon name="external-link" class="w-4 h-4 text-[#DFC99E]" />
                            <span>تصفح رابط الدعوة الآن</span>
                        </a>

                        <div class="grid grid-cols-2 gap-2.5">
                            <a href="{{ $invitation->getWhatsAppShareUrl() }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="py-2.5 px-4 rounded-xl bg-[#0F4C3A] hover:bg-[#14634C] border border-emerald-400/25 text-white font-bold text-xs transition flex items-center justify-center gap-1.5">
                                <x-app-icon name="whatsapp" class="w-4 h-4" />
                                <span>مشاركة واتساب</span>
                            </a>

                            <button type="button"
                                    wire:click="closePublishModal"
                                    class="py-2.5 px-4 rounded-xl bg-white/5 hover:bg-white/15 border border-white/10 text-[#E4D9C8] font-bold text-xs transition cursor-pointer">
                                متابعة التعديل
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
