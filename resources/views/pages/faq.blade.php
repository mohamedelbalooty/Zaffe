@php
    $isEn = app()->getLocale() === 'en';
    $faqs = $isEn
        ? [
            [
                'q' => 'How do I share my wedding invitation with guests via WhatsApp?',
                'a' => 'Once published, use the "Share via WhatsApp" button in your Studio or Guest Roster to generate an invitation message with either your public link or each guest\'s personalized invite link.',
            ],
            [
                'q' => 'Do guests need to install any mobile app to open the invitation?',
                'a' => 'Not at all. Every invitation opens smoothly in iOS Safari, Android Chrome, and WhatsApp\'s built-in browser with no app download or guest sign-in required.',
            ],
            [
                'q' => 'How does the RSVP & companion quota system work?',
                'a' => 'Inside your invitation, guests confirm or decline attendance, select adult and child counts within the limit you set, and add dietary notes. All responses appear immediately in your dashboard with one-click Excel/CSV export.',
            ],
            [
                'q' => 'Can I edit invitation details after publishing and sharing the link?',
                'a' => 'Yes. Updates to the venue, date, schedule, or design inside the Live Studio Editor take effect immediately on your existing link without resending.',
            ],
            [
                'q' => 'Does Zaffe support English invitations and Umm al-Qura Hijri dates?',
                'a' => 'Yes. Zaffe is Arabic-first (RTL) with full English (LTR) support, configurable Arabic-Indic or Latin numerals, and automatic Umm al-Qura Hijri + Gregorian date display.',
            ],
        ]
        : [
            [
                'q' => 'كيف يمكنني مشاركة دعوتي مع ضيوفي عبر واتساب؟',
                'a' => 'بمجرد نشر دعوتك، توفر لك المنصة زر «مشاركة عبر واتساب» لإنشاء رسالة دعوة أنيقة مع رابط بطاقتك العام أو الرابط الشخصي لكل مدعو لإرسالها بضغطة زر واحدة.',
            ],
            [
                'q' => 'هل يحتاج ضيوفي إلى تحميل أي تطبيق لفتح الدعوة؟',
                'a' => 'إطلاقاً! تفتح الدعوة في متصفح أي هاتف ذكي (آيفون، أندرويد، أو داخل واتساب مباشرة) بسرعة فائقة ودون الحاجة لأي تسجيل أو تحميل تطبيقات.',
            ],
            [
                'q' => 'كيف يعمل نظام تأكيد الحضور (RSVP) وقوائم الضيوف؟',
                'a' => 'في أسفل بطاقتك، يجد الضيف نموذجاً سهلاً لتسجيل اسمه، وحالة حضوره (بحضور مؤكد أو اعتذار لطيف)، مع تحديد عدد البالغين والأطفال وكتابة تهنئة. تظهر الردود فوراً في لوحة التحكم مع إمكانية التصدير إلى Excel/CSV.',
            ],
            [
                'q' => 'هل يمكنني تعديل بيانات الدعوة بعد نشرها ومشاركتها؟',
                'a' => 'نعم بكل تأكيد! يمكنك في أي وقت الدخول إلى استوديو الدعوة وتحديث المواعيد أو الموقع أو التصميم، وستنعكس التعديلات فوراً على الرابط نفسه دون الحاجة لتغييره.',
            ],
            [
                'q' => 'هل تدعم الدعوات اللغة الإنجليزية والتقويم الهجري؟',
                'a' => 'نعم، المنصة مصممة بأولوية عربية أصيلة مع دعم كامل للغة الإنجليزية (LTR)، والتحويل التلقائي لتقويم أم القرى الهجري وحرية اختيار الأرقام العربية أو اللاتينية.',
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
