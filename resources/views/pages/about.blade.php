@php
    $isEn = app()->getLocale() === 'en';
@endphp

<x-layouts.marketing :title="$isEn ? 'About Zaffe — Luxury Digital Wedding Studio' : 'من نحن — زفّة'">
    <div class="bg-[#FAF7F2] py-14 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-4">
                <h1 class="font-cairo text-3xl sm:text-5xl font-black text-[#221E1A] leading-tight">
                    {{ $isEn ? 'Crafting Digital Wedding Memories with Arabian Heritage' : 'نصنع ذكريات الزفاف بلمسة رقمية فاخرة' }}
                </h1>
                <p class="text-base text-[#52493F] leading-relaxed max-w-2xl mx-auto">
                    {{ $isEn
                        ? 'Zaffe was created to transform traditional paper wedding cards into a gracious, private, and effortless interactive experience celebrating Arab hospitality.'
                        : 'انطلقت فكرة منصة «زفّة» من الرغبة في تحويل تجربة بطاقات الأفراح التقليدية إلى رحلة رقمية راقية، ومستدامة تحتفي بخصوصية وأصالة الأعراس العربية.' }}
                </p>
            </div>

            <div class="card-atelier p-7 sm:p-11 space-y-8">
                <div class="space-y-3">
                    <h2 class="font-cairo text-2xl font-extrabold text-[#221E1A]">
                        {{ $isEn ? 'Our Vision & Craft' : 'رؤيتنا وفلسفة التصميم' }}
                    </h2>
                    <p class="text-sm sm:text-base text-[#52493F] leading-[1.8]">
                        {{ $isEn
                            ? 'Every wedding story deserves an authentic opening. Zaffe blends classic Arabic calligraphy, Umm al-Qura Hijri conversion, photo-free family privacy, and live RSVP management so hosts focus on their celebration rather than spreadsheet logistics.'
                            : 'نؤمن بأن كل مناسبة فرح فريدة وتستحق بداية استثنائية. تمنح «زفّة» كل عروس وعريس وعائلة الحرية في ابتكار بطاقة تفاعلية مميزة تجمع بين فخامة الخط العربي وجماليات الفن الإسلامي والمعاصر، مع تسهيل وصول الضيوف وتأكيد حضورهم بكل يسر.' }}
                    </p>
                </div>

                <!-- Honest Product Pillars (Replacing invented vanity stats) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-6 border-t border-[#E4D9C8]">
                    <div class="p-5 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center">
                            <x-app-icon name="palette" class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? '14 Curated Themes' : '١٤ قالباً ملكياً وتراثياً' }}
                        </h3>
                        <p class="text-xs text-[#52493F] leading-relaxed">
                            {{ $isEn ? 'Fourteen distinct visual worlds including the Saudi Signature Collection — elegant with or without photos.' : 'أربعة عشر تصميماً متنوعاً تشمل مجموعة التوقيع السعودي، تكتمل أناقتها مع الصور أو بدونها حفاظاً على الخصوصية.' }}
                        </p>
                    </div>

                    <div class="p-5 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-[#ECF5F0] text-[#0F4C3A] flex items-center justify-center">
                            <x-app-icon name="calendar" class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'KSA & Global Ready' : 'توقيت الرياض والقاهرة' }}
                        </h3>
                        <p class="text-xs text-[#52493F] leading-relaxed">
                            {{ $isEn ? 'Umm al-Qura Hijri calendar, +966/+20 & international phone support, and instant WhatsApp sharing.' : 'تقويم أم القرى الهجري، أرقام عربية أو لاتينية، دعم الهواتف الدولية (+966 و +20 وغيرها)، وتوافق تام مع واتساب.' }}
                        </p>
                    </div>

                    <div class="p-5 rounded-xl bg-[#FAF7F2] border border-[#E4D9C8] space-y-2">
                        <div class="w-9 h-9 rounded-lg bg-[#F4EBD9] text-[#7A5A20] flex items-center justify-center">
                            <x-app-icon name="check-badge" class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-cairo text-lg font-extrabold text-[#221E1A]">
                            {{ $isEn ? 'Optional RSVP & CSV Export' : 'تأكيد حضور اختياري وتصدير آمن' }}
                        </h3>
                        <p class="text-xs text-[#52493F] leading-relaxed">
                            {{ $isEn ? 'Start with a serene view-only card and turn simple guest RSVP on anytime with one-click CSV exports.' : 'ابدأ ببطاقة عرض أنيقة وفعّل نموذج تأكيد الحضور بضغطة واحدة متى شئت مع تصدير Excel/CSV.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketing>
