@props([
    'template',
    'isEn' => null,
    'saudiSlugs' => null,
    'headingTag' => 'h2',
    'eager' => false,
])

@php
    $isEn = $isEn ?? (app()->getLocale() === 'en');
    $saudiSlugs = $saudiSlugs ?? \App\Support\TemplateStyleCatalog::saudiSignatureSlugs();
    $primaryColor = $template->default_styles['primary_color'] ?? '#C6A76B';
    $isSaudiSignatureCard = in_array($template->slug, $saudiSlugs, true);
    $cardTitle = $isEn && $template->name_en ? $template->name_en : $template->name_ar;
    $cardBadge = $isSaudiSignatureCard
        ? ($isEn ? 'Saudi Signature' : 'التوقيع السعودي')
        : ($isEn ? ($template->category?->name_en ?? 'Collection') : ($template->category?->name_ar ?? 'تصميم'));
    $headingTag = in_array($headingTag, ['h2', 'h3', 'h4'], true) ? $headingTag : 'h2';
@endphp

<article class="template-gallery-card overflow-hidden flex flex-col group">
    <!-- Framed Stationery Preview Mat -->
    <a href="{{ route('templates.preview', $template->slug) }}"
       aria-label="{{ $isEn ? 'Preview ' . $cardTitle : 'معاينة تصميم ' . $cardTitle }}"
       class="template-gallery-stage relative py-5 px-4 sm:py-6 sm:px-5 flex items-center justify-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#B59556]">
        <div class="template-gallery-sheet relative w-[74%] aspect-[5/7] overflow-hidden bg-[#221E1A] ring-1 ring-[#221E1A]/10">
            <!-- Graceful Fallback (Gradient + Template Name) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-5 text-center"
                 style="background: linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%);">
                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/25 flex items-center justify-center text-white mb-2">
                    <x-app-icon name="sparkles" class="w-5 h-5" />
                </div>
                <span class="font-cairo text-sm font-bold text-white">{{ $template->name_ar }}</span>
                @if($template->name_en)
                    <span class="text-[11px] font-medium text-white/80 mt-0.5" dir="ltr">{{ $template->name_en }}</span>
                @endif
            </div>

            @if($template->thumbnail)
                <img src="{{ $template->thumbnail }}"
                     alt=""
                     width="540"
                     height="756"
                     loading="{{ $eager ? 'eager' : 'lazy' }}"
                     decoding="async"
                     onerror="this.style.display='none'"
                     class="relative z-10 w-full h-full object-cover object-top">
            @endif
        </div>
    </a>

    <!-- Minimal Luxury Card Footer -->
    <div class="px-5 pt-4 pb-5 flex-1 flex flex-col justify-between gap-3.5">
        <div class="space-y-1">
            <div class="flex items-center justify-between gap-2.5">
                <{{ $headingTag }} class="font-cairo text-[17px] font-extrabold text-[#221E1A] leading-snug truncate">
                    {{ $cardTitle }}
                </{{ $headingTag }}>
                @if($isSaudiSignatureCard)
                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#F4EBD9]/85 text-[#7A5A20] border border-[#C6A76B]/35">
                        <x-app-icon name="crown" class="w-3 h-3" />
                        <span>{{ $cardBadge }}</span>
                    </span>
                @else
                    <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF7F2] text-[#6E6458] border border-[#E4D9C8]">
                        {{ $cardBadge }}
                    </span>
                @endif
            </div>

            @if($isEn)
                <p class="text-xs font-medium text-[#6E6458] leading-tight truncate" dir="rtl">{{ $template->name_ar }}</p>
            @elseif($template->name_en)
                <p class="text-xs font-medium text-[#6E6458] leading-tight truncate" dir="ltr">{{ $template->name_en }}</p>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-2.5 pt-0.5">
            <a href="{{ route('templates.preview', $template->slug) }}"
               class="btn-ghost-ivory !min-h-[44px] !py-2 !px-3 !text-xs whitespace-nowrap">
                <x-app-icon name="eye" class="w-4 h-4 text-[#7A5A20] shrink-0" />
                <span>{{ $isEn ? 'Preview' : 'معاينة التصميم' }}</span>
            </a>

            <a href="{{ route('dashboard.invitations.create', ['template_id' => $template->id]) }}"
               class="btn-obsidian !min-h-[44px] !py-2 !px-3 !text-xs whitespace-nowrap">
                <x-app-icon name="sparkles" class="w-4 h-4 text-[#DFC99E] shrink-0" />
                <span>{{ $isEn ? 'Use Template' : 'استخدم التصميم' }}</span>
            </a>
        </div>
    </div>
</article>
