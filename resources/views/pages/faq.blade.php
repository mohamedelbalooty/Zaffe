@php
    $isEn = app()->getLocale() === 'en';
    $faqs = $isEn
        ? [
            [
                'q' => 'How do I share my wedding invitation with guests via WhatsApp?',
                'a' => 'Once published, use the "Share via WhatsApp" button in your Studio or Dashboard to generate a refined greeting message with your public invitation link ready to send in one tap.',
            ],
            [
                'q' => 'Do guests need to install any mobile app to open the invitation?',
                'a' => 'Not at all. Every invitation opens smoothly in iOS Safari, Android Chrome, and WhatsApp\'s built-in browser with no app download or guest sign-in required.',
            ],
            [
                'q' => 'Is RSVP confirmation mandatory, or can I create a view-only card?',
                'a' => 'New invitations start as serene view-only cards by default. You can switch RSVP confirmations ON or OFF anytime inside the Studio Editor without losing previously saved guest responses, and export responses to Excel/CSV anytime.',
            ],
            [
                'q' => 'Can I hide the bride name or edit invitation details after publishing?',
                'a' => 'Yes. Leaving the bride name empty is supported cleanly across all 14 themes, and any updates to the venue, date, schedule, or design in the Live Studio Editor take effect immediately on your existing link.',
            ],
            [
                'q' => 'What languages and calendars does Zaffe support?',
                'a' => 'Release 1 wedding cards, the Live Studio Editor, and guest RSVP forms are Arabic-first (RTL) with automatic Umm al-Qura Hijri + Gregorian date conversion and configurable Arabic-Indic or Latin numerals, while the public website and customer dashboard are available in both Arabic and English.',
            ],
        ]
        : [
            [
                'q' => 'كيف يمكنني مشاركة دعوتي مع ضيوفي عبر واتساب؟',
                'a' => 'بمجرد نشر دعوتك، توفر لك المنصة زر «مشاركة عبر واتساب» لإنشاء رسالة دعوة أنيقة مع رابط بطاقتك العام لإرسالها للأهل والأصدقاء بضغطة زر واحدة.',
            ],
            [
                'q' => 'هل يحتاج ضيوفي إلى تحميل أي تطبيق لفتح الدعوة؟',
                'a' => 'إطلاقاً! تفتح الدعوة في متصفح أي هاتف ذكي (آيفون، أندرويد، أو داخل واتساب مباشرة) بسرعة فائقة ودون الحاجة لأي تسجيل أو تحميل تطبيقات.',
            ],
            [
                'q' => 'هل تفعيل تأكيد الحضور (RSVP) إلزامي أم يمكن الاكتفاء ببطاقة عرض؟',
                'a' => 'تبدأ البطاقات الجديدة كبطاقة دعوة للعرض فقط بشكل افتراضي، ويمكنك في أي لحظة تفعيل أو إيقاف نموذج تأكيد الحضور بضغطة زر من الاستوديو دون فقدان أي ردود محفوظة، مع إمكانية التصدير إلى Excel/CSV.',
            ],
            [
                'q' => 'هل يمكن ترك اسم العروس فارغاً أو تعديل البيانات بعد النشر؟',
                'a' => 'نعم، اسم العروس اختياري بالكامل وجميع القوالب الـ ١٤ تتكيّف معه دون أي رموز زائدة. كما يمكنك تعديل المواعيد أو القاعة أو التصميم في أي وقت لتظهر التعديلات فوراً على الرابط نفسه.',
            ],
            [
                'q' => 'ما هي اللغات والتقويم المدعومة في زفّة؟',
                'a' => 'البطاقات واستوديو التصميم ونموذج تأكيد الحضور في الإصدار الأول مصممة باللغة العربية بأولوية أصيلة (RTL) مع التحويل التلقائي لتقويم أم القرى الهجري والأرقام العربية أو اللاتينية، بينما تتوفر صفحات الموقع ولوحة تحكم العميل بالعربية والإنجليزية.',
            ],
        ];
@endphp

<x-layouts.marketing :title="$isEn ? 'FAQ — Zaffe' : 'الأسئلة الشائعة — زفّة'">
    <div class="bg-[#FAF7F2] py-14 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center space-y-3">
                <h1 class="font-cairo text-3xl sm:text-5xl font-black text-[#221E1A]">
                    {{ $isEn ? 'Frequently Asked Questions' : 'الأسئلة الأكثر شيوعاً' }}
                </h1>
                <p class="text-base text-[#52493F] max-w-xl mx-auto">
                    {{ $isEn
                        ? 'Everything you need to know about crafting and sharing digital wedding invitations on Zaffe.'
                        : 'إليك كل ما تود معرفته عن تصميم ومشاركة دعوات الزفاف الرقمية عبر منصة زفّة.' }}
                </p>
            </div>

            <div class="space-y-3.5" x-data="{ openFaq: 0 }">
                @foreach($faqs as $idx => $item)
                    <div class="card-atelier overflow-hidden transition-colors"
                         :class="openFaq === {{ $idx }} ? 'border-[#C6A76B]' : ''">
                        <button type="button"
                                @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                                :aria-expanded="(openFaq === {{ $idx }}).toString()"
                                class="w-full px-6 py-5 flex items-center justify-between gap-4 text-start font-cairo font-bold text-base sm:text-lg text-[#221E1A] cursor-pointer hover:bg-[#FAF7F2]/60 transition">
                            <span>{{ $item['q'] }}</span>
                            <x-app-icon name="chevron-down" class="w-5 h-5 text-[#856326] shrink-0 transition-transform duration-200" ::class="openFaq === {{ $idx }} ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="openFaq === {{ $idx }}"
                             @if($idx !== 0) x-cloak @endif
                             x-collapse
                             class="px-6 pb-5 pt-3 border-t border-[#E4D9C8] text-sm text-[#52493F] leading-[1.8]">
                            {{ $item['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.marketing>
