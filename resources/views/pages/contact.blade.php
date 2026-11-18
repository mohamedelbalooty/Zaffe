@php
    $isEn = app()->getLocale() === 'en';
    $contactEmail = \App\Models\PlatformSetting::get('contact_email', 'support@zaffe.app');
    $contactPhone = \App\Models\PlatformSetting::get('contact_phone', '+20 100 000 0000');
    $whatsappUrl = \App\Models\PlatformSetting::whatsappUrl();
@endphp

<x-layouts.marketing :title="$isEn ? 'Contact Us — Zaffe' : 'تواصل معنا — زفّة'">
    <div class="bg-[#FAF7F2] py-14 sm:py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-3">
                <h1 class="font-cairo text-3xl sm:text-5xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Our Wedding Concierge Team Is Listening' : 'فريق زفّــة يسعد بالاستماع إليك' }}
                </h1>
                <p class="text-base text-[#52493F] max-w-xl mx-auto">
                    {{ $isEn
                        ? 'Need help customizing your invitation, importing your guest list, or setting up bilingual invitations? Reach out directly.'
                        : 'سواء كان لديك استفسار حول تخصيص دعوتك أو إدارة قائمة الضيوف، تواصل معنا مباشرة.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <!-- Direct Channels -->
                <div class="card-atelier p-7 sm:p-8 space-y-5">
                    <h2 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Direct Support Channels' : 'قنوات التواصل الفوري' }}
                    </h2>

                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-4 p-4 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/20 text-[#0F4C3A] hover:border-[#0F4C3A]/45 transition">
                        <div class="w-11 h-11 rounded-xl bg-[#0F4C3A] text-white flex items-center justify-center shrink-0">
                            <x-app-icon name="whatsapp" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold block opacity-80">{{ $isEn ? 'WhatsApp Support' : 'واتساب مخصص للمساعدة' }}</span>
                            <span class="text-base font-extrabold tabular-nums" dir="ltr">{{ $contactPhone }}</span>
                        </div>
                    </a>

                    <a href="mailto:{{ $contactEmail }}"
                       class="flex items-center gap-4 p-4 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] hover:border-[#C6A76B] transition">
                        <div class="w-11 h-11 rounded-xl bg-[#C6A76B] text-[#151210] flex items-center justify-center shrink-0">
                            <x-app-icon name="mail" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-[#52493F] block">{{ $isEn ? 'Email Support' : 'البريد الإلكتروني' }}</span>
                            <span class="text-base font-extrabold text-[#221E1A] break-all">{{ $contactEmail }}</span>
                        </div>
                    </a>

                    <div class="flex items-center gap-4 p-4 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8]">
                        <div class="w-11 h-11 rounded-xl bg-[#221E1A] text-[#DFC99E] flex items-center justify-center shrink-0">
                            <x-app-icon name="map-pin" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-[#52493F] block">{{ $isEn ? 'Regional Coverage' : 'نطاق الخدمة' }}</span>
                            <span class="text-sm font-bold text-[#221E1A]">
                                {{ $isEn ? 'Saudi Arabia, Egypt & the Gulf' : 'المملكة العربية السعودية، جمهورية مصر العربية، والخليج' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Contact Form -->
                <div class="card-atelier p-7 sm:p-8 space-y-5" x-data="{ submitted: false }">
                    <h2 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Send a Quick Note' : 'أرسل لنا رسالة سريعة' }}
                    </h2>

                    <div x-show="submitted"
                         x-cloak
                         role="status"
                         class="p-6 rounded-xl bg-[#ECF5F0] border border-[#0F4C3A]/25 text-[#0F4C3A] text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-[#0F4C3A] text-white flex items-center justify-center mx-auto">
                            <x-app-icon name="check" class="w-6 h-6" />
                        </div>
                        <h3 class="font-cairo font-extrabold text-base">
                            {{ $isEn ? 'Message received' : 'تم إرسال رسالتك بنجاح!' }}
                        </h3>
                        <p class="text-xs leading-relaxed">
                            {{ $isEn
                                ? 'Thank you for contacting Zaffe. Our team will follow up via WhatsApp or email shortly.'
                                : 'شكراً لتواصلك مع زفّــة. يقوم فريق الدعم بمراجعة استفسارك وسنعاود التواصل معك عبر الواتساب أو البريد الإلكتروني في أسرع وقت.' }}
                        </p>
                    </div>

                    <form x-show="!submitted" @submit.prevent="submitted = true" class="space-y-4">
                        <div>
                            <label for="contact_name" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                                {{ $isEn ? 'Full Name' : 'الاسم الكامل' }}
                            </label>
                            <input id="contact_name" type="text" required
                                   placeholder="{{ $isEn ? 'Ahmed Mahmoud' : 'أحمد محمود' }}"
                                   class="input-atelier">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                                {{ $isEn ? 'Phone / WhatsApp Number' : 'رقم الهاتف / الواتساب' }}
                            </label>
                            <input id="contact_phone" type="tel" required dir="ltr"
                                   placeholder="+966 50 000 0000"
                                   class="input-atelier text-start">
                        </div>
                        <div>
                            <label for="contact_message" class="block text-xs font-bold text-[#221E1A] mb-1.5">
                                {{ $isEn ? 'Message or Inquiry' : 'الرسالة أو الاستفسار' }}
                            </label>
                            <textarea id="contact_message" rows="4" required
                                      placeholder="{{ $isEn ? 'How can we help with your wedding invitation?' : 'كيف يمكننا مساعدتك في تجهيز دعوة زفافك؟' }}"
                                      class="input-atelier !min-h-[110px]"></textarea>
                        </div>
                        <button type="submit" class="btn-royal w-full">
                            {{ $isEn ? 'Send Message' : 'إرسال الرسالة' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
