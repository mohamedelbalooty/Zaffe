<div class="h-screen flex flex-col bg-[#1E1B18] text-[#E8DED0] overflow-hidden font-tajawal text-right">
    <!-- Top Editor Header Toolbar -->
    <header class="h-16 bg-[#292521] border-b border-[#C6A76B]/30 px-4 sm:px-6 flex items-center justify-between z-40 shrink-0">
        <!-- Left/Start: Back & Title -->
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.invitations.index') }}" 
               class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-white transition flex items-center gap-1.5 text-xs font-bold">
                <x-app-icon name="arrow-right" class="w-4 h-4 text-[#C6A76B]" />
                <span class="hidden sm:inline">لوحة التحكم</span>
            </a>

            <div class="h-5 w-px bg-white/15 hidden sm:block"></div>

            <div class="flex items-center gap-2">
                <h2 class="font-cairo text-sm sm:text-base font-bold text-white truncate max-w-[200px] sm:max-w-xs">
                    {{ $title ?: 'تعديل دعوة الزفاف' }}
                </h2>
                
                @if($status === 'published')
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>منشورة أونلاين</span>
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        مسودة خاصة
                    </span>
                @endif
            </div>
        </div>

        <!-- Center: Device Preview Switcher -->
        <div class="hidden md:flex items-center bg-black/40 p-1 rounded-xl border border-white/10">
            <button wire:click="$set('previewDevice', 'mobile')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition {{ $previewDevice === 'mobile' ? 'bg-[#C6A76B] text-white shadow-sm' : 'text-[#E8DED0]/60 hover:text-white' }}">
                <span>📱 هاتف</span>
            </button>
            <button wire:click="$set('previewDevice', 'tablet')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition {{ $previewDevice === 'tablet' ? 'bg-[#C6A76B] text-white shadow-sm' : 'text-[#E8DED0]/60 hover:text-white' }}">
                <span>📟 تابلت</span>
            </button>
            <button wire:click="$set('previewDevice', 'desktop')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition {{ $previewDevice === 'desktop' ? 'bg-[#C6A76B] text-white shadow-sm' : 'text-[#E8DED0]/60 hover:text-white' }}">
                <span>💻 شاشة كاملة</span>
            </button>
        </div>

        <!-- Right/End: Auto-save status, Publish Toggle, View Public Link -->
        <div class="flex items-center gap-3">
            <span class="text-xs text-[#C6A76B] font-medium hidden lg:inline">
                {{ $saveStatus }}
            </span>

            @if($status === 'published')
                <a href="{{ $invitation->public_url }}" target="_blank"
                   class="p-2 sm:px-3 sm:py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold flex items-center gap-1.5 transition">
                    <x-app-icon name="external-link" class="w-4 h-4 text-[#C6A76B]" />
                    <span class="hidden sm:inline">معاينة الرابط</span>
                </a>
            @endif

            <button wire:click="togglePublish" 
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-md transition flex items-center gap-1.5 {{ $status === 'published' ? 'bg-amber-600/80 hover:bg-amber-600 text-white' : 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:opacity-95' }}">
                @if($status === 'published')
                    <span>إلغاء النشر</span>
                @else
                    <x-app-icon name="sparkles" class="w-4 h-4" />
                    <span>نشر الدعوة الآن</span>
                @endif
            </button>

            <button wire:click="saveChanges" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white text-xs sm:text-sm font-bold shadow-md hover:opacity-95 transition flex items-center gap-1.5">
                <x-app-icon name="check" class="w-4 h-4" />
                <span class="hidden sm:inline">حفظ</span>
            </button>
        </div>
    </header>

    <!-- Main Workspace Split Pane -->
    <div class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">
        
        <!-- Controls Sidebar -->
        <div class="w-full lg:w-[480px] bg-[#292521] border-l border-[#C6A76B]/20 flex flex-col shrink-0 overflow-hidden">
            <!-- Sidebar Navigation Tabs -->
            <div class="flex items-center border-b border-[#E8DED0]/10 overflow-x-auto p-2 gap-1 scrollbar-none bg-black/20 shrink-0">
                <button wire:click="$set('activeTab', 'basics')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'basics' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    البيانات الأساسية
                </button>
                <button wire:click="$set('activeTab', 'content')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'content' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    نصوص الدعوة
                </button>
                <button wire:click="$set('activeTab', 'design')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'design' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    الألوان والخطوط
                </button>
                <button wire:click="$set('activeTab', 'media')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'media' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    الصور والألبوم
                </button>
                <button wire:click="$set('activeTab', 'templates')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'templates' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    تبديل القالب
                </button>
                <button wire:click="$set('activeTab', 'rsvp')"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'rsvp' ? 'bg-[#C6A76B] text-[#292521] shadow-sm' : 'text-[#E8DED0]/70 hover:bg-white/5' }}">
                    إعدادات الحضور
                </button>
            </div>

            <!-- Tab Content Pane (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- 1. Basics Tab -->
                @if($activeTab === 'basics')
                    <div class="space-y-4">
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">بيانات العروسين وحفل الزفاف</h3>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">اسم العريس</label>
                                <input type="text" wire:model.live.debounce.300ms="groom_name" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">اسم العروس</label>
                                <input type="text" wire:model.live.debounce.300ms="bride_name" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">عنوان الدعوة</label>
                            <input type="text" wire:model.live.debounce.300ms="title" 
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">تاريخ الحفل</label>
                                <input type="date" wire:model.live="wedding_date" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">ساعة بدء الحفل</label>
                                <input type="time" wire:model.live="wedding_time" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">المنطقة الزمنية لموعد الحفل</label>
                            <select wire:model.live="timezone" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                                <option value="Africa/Cairo" class="bg-[#292521] text-white">توقيت مصر والقاهرة (Africa/Cairo)</option>
                                <option value="Asia/Riyadh" class="bg-[#292521] text-white">توقيت السعودية والرياض (Asia/Riyadh)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">لغة الدعوة</label>
                                <select wire:model.live="invitation_locale"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                                    <option value="ar" class="bg-[#292521] text-white">العربية</option>
                                    <option value="en" class="bg-[#292521] text-white">English</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-white mb-1.5">شكل الأرقام</label>
                                <select wire:model.live="digits"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none">
                                    <option value="latin" class="bg-[#292521] text-white">أرقام قياسية (0-9)</option>
                                    <option value="arabic_indic" class="bg-[#292521] text-white">أرقام عربية (٠-٩)</option>
                                </select>
                            </div>
                        </div>

                        <label class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                            <span class="text-xs font-bold text-white">عرض التاريخ الهجري (تقويم أم القرى)</span>
                            <input type="checkbox" wire:model.live="show_hijri" class="w-4 h-4 rounded text-[#C6A76B]">
                        </label>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">اسم مكان الحفل (القاعة / الفندق)</label>
                            <input type="text" wire:model.live.debounce.300ms="venue_name" 
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none"
                                   placeholder="مثال: فندق فورسيزونز قاعة بلازا">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">العنوان التفصيلي</label>
                            <input type="text" wire:model.live.debounce.300ms="venue_address" 
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none"
                                   placeholder="كورنيش النيل، جاردن سيتي، القاهرة">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">رابط خريطة Google Maps</label>
                            <input type="url" wire:model.live.debounce.300ms="venue_map_url" dir="ltr"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none text-right"
                                   placeholder="https://maps.google.com/?q=...">
                        </div>
                    </div>
                @endif

                <!-- 2. Content Tab -->
                @if($activeTab === 'content')
                    <div class="space-y-4">
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">نصوص وكلمات الدعوة</h3>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">افتتاحية الدعوة (آية قرآنية أو مقولة ترحيبية)</label>
                            <textarea wire:model.live.debounce.400ms="welcome_message" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">نص الدعوة الرئيسي</label>
                            <textarea wire:model.live.debounce.400ms="invitation_message" rows="4"
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-1.5">رابط ملف موسيقى خلفية (MP3 اختياري)</label>
                            <input type="url" wire:model.live.debounce.400ms="music_url" dir="ltr"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-[#C6A76B] outline-none text-right"
                                   placeholder="https://example.com/wedding-song.mp3">
                            <span class="text-[11px] text-[#E8DED0]/60 mt-1 block">يسمح للضيوف بالاستماع لموسيقاكم المفضلة أثناء قراءة الدعوة.</span>
                        </div>
                    </div>
                @endif

                <!-- 3. Design Tab -->
                @if($activeTab === 'design')
                    <div class="space-y-5">
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">تخصيص الهوية البصرية والألوان</h3>

                        <!-- Color Presets -->
                        <div>
                            <label class="block text-xs font-bold text-white mb-2">اللون الأساسي (ألوان العناوين والزخارف)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model.live="primary_color" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                                <input type="text" wire:model.live="primary_color" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-xs font-mono w-28 uppercase">
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$set('primary_color', '#C6A76B')" class="w-7 h-7 rounded-full bg-[#C6A76B] border border-white/20 shadow-sm" title="ذهبي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#D9828A')" class="w-7 h-7 rounded-full bg-[#D9828A] border border-white/20 shadow-sm" title="وردي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#0F4C3A')" class="w-7 h-7 rounded-full bg-[#0F4C3A] border border-white/20 shadow-sm" title="زمردي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#8B263E')" class="w-7 h-7 rounded-full bg-[#8B263E] border border-white/20 shadow-sm" title="عنابي"></button>
                                    <button type="button" wire:click="$set('primary_color', '#1F2937')" class="w-7 h-7 rounded-full bg-[#1F2937] border border-white/20 shadow-sm" title="فحمي"></button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-white mb-2">لون خلفية الدعوة</label>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model.live="background_color" class="w-12 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                                <input type="text" wire:model.live="background_color" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-xs font-mono w-28 uppercase">
                                <div class="flex gap-2">
                                    <button type="button" wire:click="$set('background_color', '#FAF7F2')" class="w-7 h-7 rounded-full bg-[#FAF7F2] border border-white/20 shadow-sm" title="عاجي"></button>
                                    <button type="button" wire:click="$set('background_color', '#FDF8F7')" class="w-7 h-7 rounded-full bg-[#FDF8F7] border border-white/20 shadow-sm" title="باستيل"></button>
                                    <button type="button" wire:click="$set('background_color', '#FAFAFA')" class="w-7 h-7 rounded-full bg-[#FAFAFA] border border-white/20 shadow-sm" title="أبيض"></button>
                                    <button type="button" wire:click="$set('background_color', '#0F172A')" class="w-7 h-7 rounded-full bg-[#0F172A] border border-white/20 shadow-sm" title="ليلي كحلي"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Font Family -->
                        <div>
                            <label class="block text-xs font-bold text-white mb-2">نوع الخط العربي المعتمد</label>
                            <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $font_family === 'Tajawal' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-white' : 'border-white/10 hover:bg-white/5 text-[#E8DED0]/80' }}">
                                    <input type="radio" wire:model.live="font_family" value="Tajawal" class="hidden">
                                    <span class="font-tajawal">خط تجوال (Tajawal)</span>
                                    <span>✓</span>
                                </label>
                                <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $font_family === 'Cairo' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-white' : 'border-white/10 hover:bg-white/5 text-[#E8DED0]/80' }}">
                                    <input type="radio" wire:model.live="font_family" value="Cairo" class="hidden">
                                    <span class="font-cairo">خط كايرو (Cairo)</span>
                                    <span>✓</span>
                                </label>
                                <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $font_family === 'Amiri' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-white' : 'border-white/10 hover:bg-white/5 text-[#E8DED0]/80' }}">
                                    <input type="radio" wire:model.live="font_family" value="Amiri" class="hidden">
                                    <span class="font-amiri text-base">خط أميري (Amiri)</span>
                                    <span>✓</span>
                                </label>
                                <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $font_family === 'Playfair Display' ? 'border-[#C6A76B] bg-[#C6A76B]/10 text-white' : 'border-white/10 hover:bg-white/5 text-[#E8DED0]/80' }}">
                                    <input type="radio" wire:model.live="font_family" value="Playfair Display" class="hidden">
                                    <span class="font-playfair">Playfair Serif</span>
                                    <span>✓</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 4. Media Tab -->
                @if($activeTab === 'media')
                    <div class="space-y-6">
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">صور العروسين وألبوم الذكريات</h3>

                        <!-- Cover photo -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                            <label class="block text-xs font-bold text-white">صورة الغلاف / صورة العروسين الرئيسية</label>
                            
                            @if($cover_photo)
                                <div class="w-32 h-32 rounded-2xl overflow-hidden border-2 border-[#C6A76B] shadow-md mb-2">
                                    <img src="{{ $cover_photo }}" alt="Cover" class="w-full h-full object-cover">
                                </div>
                            @endif

                            <input type="file" wire:model="new_cover_photo" accept="image/*"
                                   class="text-xs text-[#E8DED0]/80 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#C6A76B] file:text-white hover:file:opacity-90">
                            
                            <div wire:loading wire:target="new_cover_photo" class="text-xs text-[#C6A76B]">
                                جاري رفع صورة الغلاف...
                            </div>
                        </div>

                        <!-- Gallery Photos -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                            <label class="block text-xs font-bold text-white">إضافة صور إلى ألبوم الفرح</label>
                            <input type="file" wire:model="new_gallery_photo" accept="image/*"
                                   class="text-xs text-[#E8DED0]/80 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#292521] file:border file:border-[#C6A76B] file:text-[#C6A76B]">
                            
                            <div wire:loading wire:target="new_gallery_photo" class="text-xs text-[#C6A76B]">
                                جاري رفع الصورة إلى الألبوم...
                            </div>

                            <!-- List existing photos -->
                            <div class="grid grid-cols-3 gap-2 pt-2">
                                @foreach($invitation->photos as $p)
                                    <div class="relative group rounded-xl overflow-hidden h-24 border border-white/10">
                                        <img src="{{ $p->url }}" alt="Gallery photo" class="w-full h-full object-cover">
                                        <button wire:click="deleteGalleryPhoto({{ $p->id }})" 
                                                class="absolute top-1 left-1 p-1 bg-red-600 text-white rounded-lg opacity-0 group-hover:opacity-100 transition shadow">
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
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">اختر قالب الدعوة</h3>
                        <p class="text-xs text-[#E8DED0]/70">يمكنك تبديل مظهر القالب بضغطة زر واحدة وستظهر التغييرات فوراً في المعاينة الحية:</p>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach($templates as $tmpl)
                                @php
                                    $primaryColor = $tmpl->default_styles['primary_color'] ?? '#C6A76B';
                                @endphp
                                <button type="button" wire:click="changeTemplate({{ $tmpl->id }})"
                                        class="rounded-2xl p-2.5 border text-right transition flex flex-col gap-2 {{ $template_id == $tmpl->id ? 'border-[#C6A76B] bg-[#C6A76B]/15 shadow-md' : 'border-white/10 bg-white/5 hover:border-white/30' }}">
                                    <div class="relative h-24 w-full rounded-xl overflow-hidden bg-[#1E1B18]">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-2 text-center"
                                             style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                                            <span class="font-cairo text-[11px] font-bold text-white line-clamp-1">{{ $tmpl->name_ar }}</span>
                                            @if($tmpl->name_en)
                                                <span class="text-[9px] text-white/75 line-clamp-1" dir="ltr">{{ $tmpl->name_en }}</span>
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
                                                <span class="text-[10px] text-[#E8DED0]/60 block truncate" dir="ltr">{{ $tmpl->name_en }}</span>
                                            @endif
                                        </div>
                                        @if($template_id == $tmpl->id)
                                            <span class="text-xs text-[#C6A76B] shrink-0">✓</span>
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
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B]">إعدادات تأكيد الحضور (RSVP)</h3>

                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div>
                                    <span class="text-xs font-bold text-white block">تفعيل تأكيد الحضور (RSVP)</span>
                                    <span class="text-[11px] text-[#E8DED0]/60">السماح للضيوف بتأكيد حضورهم عبر الدعوة</span>
                                </div>
                                <input type="checkbox" wire:model.live="is_rsvp_enabled" class="w-5 h-5 rounded text-[#C6A76B] focus:ring-[#C6A76B]">
                            </label>

                            @if($is_rsvp_enabled)
                                <div class="pt-3 border-t border-white/10 space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-white mb-1">أقصى عدد مرافقين مسموح للضيف الواحد</label>
                                        <input type="number" min="1" max="10" wire:model.live="max_guests_per_rsvp" 
                                               class="w-full px-3.5 py-2 rounded-xl bg-black/30 border border-white/10 text-white text-sm outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-white mb-1">الموعد النهائي لتأكيد الحضور (Deadline)</label>
                                        <input type="date" wire:model.live="rsvp_deadline" 
                                               class="w-full px-3.5 py-2 rounded-xl bg-black/30 border border-white/10 text-white text-sm outline-none">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sections Toggles -->
                        <h3 class="font-cairo text-base font-bold text-[#C6A76B] pt-4">أقسام الدعوة</h3>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-xs text-white">⏳ عداد الأيام التنازلي</span>
                                <input type="checkbox" wire:model.live="section_countdown" class="w-4 h-4 rounded text-[#C6A76B]">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-xs text-white">🗓 جدول مواعيد وبرنامج الحفل</span>
                                <input type="checkbox" wire:model.live="section_timeline" class="w-4 h-4 rounded text-[#C6A76B]">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-xs text-white">🖼 ألبوم الصور</span>
                                <input type="checkbox" wire:model.live="section_gallery" class="w-4 h-4 rounded text-[#C6A76B]">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-xs text-white">👔 قواعد اللباس (Dress Code)</span>
                                <input type="checkbox" wire:model.live="section_dress_code" class="w-4 h-4 rounded text-[#C6A76B]">
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Live Preview Stage Area -->
        <div class="flex-1 overflow-y-auto flex items-center justify-center p-4 sm:p-8 bg-[#161413]">
            <!-- Live Preview Wrapper Container -->
            <div class="transition-all duration-300 {{ $previewDevice === 'mobile' ? 'max-w-md w-full rounded-[2.5rem] shadow-2xl border-4 border-[#3D3731] overflow-hidden my-4' : ($previewDevice === 'tablet' ? 'max-w-2xl w-full rounded-3xl shadow-2xl border-4 border-[#3D3731] overflow-hidden my-4' : 'w-full max-w-4xl rounded-2xl shadow-2xl border border-zinc-800 overflow-hidden my-4') }}">
                
                @include($invitation->template->view_name ?? 'templates.royal_gold', [
                    'invitation' => $previewObject,
                    'isPreview' => true,
                ])

            </div>
        </div>
    </div>
</div>
