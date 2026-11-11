<x-layouts.marketing :title="'باقات الأسعار — زفّة'">
    <div class="bg-[#FAF7F2] py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#C6A76B] bg-[#C6A76B]/15 px-3 py-1 rounded-full border border-[#C6A76B]/30">
                    باقات واضحة وشفافة
                </span>
                <h1 class="font-cairo text-3xl sm:text-5xl font-extrabold text-[#292521] mt-3">
                    استثمر في ذكرى زفاف تدوم للأبد
                </h1>
                <p class="text-base text-[#292521]/70 mt-3">
                    وفر آلاف الجنيهات مقارنة بالطباعة الورقية التقليدية، مع تحكم فوري وتأكيد حضور تفاعلي يريحك وييسر على ضيوفك
                </p>
            </div>

            <!-- Pricing Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @foreach($plans as $plan)
                    <div class="rounded-3xl p-8 sm:p-10 transition-all flex flex-col justify-between relative {{ $plan->slug === 'premium' ? 'bg-[#292521] text-white shadow-2xl border-2 border-[#C6A76B]' : 'bg-white text-[#292521] shadow-wedding border border-[#E8DED0]' }}">
                        
                        @if($plan->slug === 'premium')
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white px-4 py-1 rounded-full text-xs font-black uppercase tracking-wider shadow-md flex items-center gap-1.5">
                                <x-app-icon name="crown" class="w-3.5 h-3.5" />
                                <span>الأكثر اختياراً للعرسان</span>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <h3 class="font-cairo text-2xl font-bold {{ $plan->slug === 'premium' ? 'text-white' : 'text-[#292521]' }}">
                                    {{ $plan->name_ar }}
                                </h3>
                                <p class="text-xs {{ $plan->slug === 'premium' ? 'text-[#E8DED0]/70' : 'text-[#292521]/60' }} mt-1">
                                    {{ $plan->slug === 'premium' ? 'التجربة الملكية المتكاملة مع الدعم والموسيقى وتصدير البيانات' : 'تجربة مبسطة لتجربة المنصة ومشاركة أول دعوة' }}
                                </p>
                            </div>

                            <!-- Price -->
                            <div class="flex items-baseline gap-2">
                                <span class="font-cairo text-4xl sm:text-5xl font-extrabold {{ $plan->slug === 'premium' ? 'text-[#C6A76B]' : 'text-[#292521]' }}">
                                    {{ $plan->price == 0 ? 'مجاناً' : number_format($plan->price) }}
                                </span>
                                @if($plan->price > 0)
                                    <span class="text-sm font-bold {{ $plan->slug === 'premium' ? 'text-[#E8DED0]/70' : 'text-[#292521]/60' }}">
                                        {{ $plan->currency }} / تدفع لمرة واحدة للدعوة
                                    </span>
                                @endif
                            </div>

                            <!-- Features list -->
                            <ul class="space-y-3.5 text-sm pt-4 border-t {{ $plan->slug === 'premium' ? 'border-white/10 text-[#E8DED0]/90' : 'border-[#E8DED0] text-[#292521]/80' }}">
                                @foreach($plan->features ?? [] as $feat)
                                    <li class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-full {{ $plan->slug === 'premium' ? 'bg-[#C6A76B]/20 text-[#C6A76B]' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center shrink-0">
                                            <x-app-icon name="check" class="w-3.5 h-3.5" />
                                        </div>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="pt-8">
                            <a href="{{ route('register') }}" 
                               class="w-full py-4 px-6 rounded-2xl font-bold text-center text-sm shadow-md transition flex items-center justify-center gap-2 {{ $plan->slug === 'premium' ? 'bg-gradient-to-r from-[#C6A76B] to-[#B59556] text-white hover:opacity-95' : 'bg-[#292521] text-white hover:bg-[#3D3731]' }}">
                                <span>{{ $plan->price == 0 ? 'ابدأ مجاناً' : 'اشترك وصمم دعوتك الملكية' }}</span>
                                <x-app-icon name="arrow-left" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Guarantee Box -->
            <div class="max-w-xl mx-auto mt-16 p-6 rounded-2xl bg-white border border-[#C6A76B]/30 text-center text-xs text-[#292521]/70 flex items-center justify-center gap-3">
                <x-app-icon name="shield-check" class="w-6 h-6 text-[#C6A76B] shrink-0" />
                <span>ضمان الرضا الكامل: إذا احتجت لأي مساعدة في تنسيق دعوتك أو إعداد الرابط، فريق زفّة متاح عبر واتساب لخدمتك على مدار الساعة.</span>
            </div>
        </div>
    </div>
</x-layouts.marketing>
