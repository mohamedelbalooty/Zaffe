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

                <!-- Concierge Help Guide (Direct Verified Channels Only) -->
                <div class="card-atelier p-7 sm:p-8 space-y-5 flex flex-col justify-between">
                    <div class="space-y-4">
                        <h2 class="font-cairo text-xl font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'How Our Concierge Can Help' : 'كيف يمكن لفريق زفّة مساعدتك؟' }}
                        </h2>
                        <p class="text-sm text-[#52493F] leading-relaxed">
                            {{ $isEn
                                ? 'For the fastest support, message our concierge directly on WhatsApp or email. Sharing your invitation link or account email helps us assist you right away.'
                                : 'للحصول على أسرع مساعدة ممكنة، تواصل معنا مباشرة عبر واتساب أو البريد الإلكتروني الموضحين، مع ذكر بريد حسابك أو رابط دعوتك لتسريع الدعم.' }}
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-[#52493F]">
                            <li class="flex items-center gap-2.5">
                                <x-app-icon name="check" class="w-4 h-4 text-[#0F4C3A] shrink-0" />
                                <span>{{ $isEn ? 'Choosing a Saudi Signature or classic wedding theme' : 'المساعدة في اختيار القالب الأنسب وتنسيق الخطوط والألوان' }}</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-app-icon name="check" class="w-4 h-4 text-[#0F4C3A] shrink-0" />
                                <span>{{ $isEn ? 'Configuring optional RSVP & Hijri dates' : 'ضبط التقويم الهجري (أم القرى) وتفعيل أو إخفاء تأكيد الحضور' }}</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-app-icon name="check" class="w-4 h-4 text-[#0F4C3A] shrink-0" />
                                <span>{{ $isEn ? 'WhatsApp link preview & Google Maps guidance' : 'التأكد من معاينة رابط الدعوة عبر واتساب وموقع القاعة' }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-[#E4D9C8]">
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-royal w-full">
                            <x-app-icon name="whatsapp" class="w-4 h-4" />
                            <span>{{ $isEn ? 'Message Us on WhatsApp' : 'تواصل الآن عبر واتساب' }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
