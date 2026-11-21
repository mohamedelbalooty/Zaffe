<?php

namespace App\Http\Controllers;

use App\Models\FunnelDailyMetric;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\SubscriptionPlan;
use App\Models\TemplateCategory;
use App\Support\TemplateStyleCatalog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredTemplates = InvitationTemplate::with('category')
            ->availableForSelection()
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $categories = TemplateCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.home', compact('featuredTemplates', 'categories', 'plans'));
    }

    public function demo()
    {
        $template = InvitationTemplate::query()
            ->where('slug', 'diriyah-heritage')
            ->first();

        $defaultStyles = $template?->default_styles ?? [
            'primary_color' => '#C6A76B',
            'secondary_color' => '#2C231D',
            'background_color' => '#FAF7F2',
            'card_color' => '#FFFFFF',
            'font_family' => 'Tajawal',
        ];

        $demoInvitation = (object) [
            'id' => 0,
            'title' => 'نموذج دعوة زفاف سعودية حية — زفّة',
            'slug' => 'demo',
            'groom_name' => 'فيصل بن عبد العزيز آل مقرن',
            'bride_name' => 'الجوهرة بنت خالد السديري',
            'wedding_date' => now()->addDays(42),
            'wedding_time' => '20:30',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر طويق للاحتفالات — حي السفارات',
            'venue_address' => 'طريق مكة المكرمة، حي السفارات، الرياض، المملكة العربية السعودية',
            'venue_map_url' => 'https://maps.google.com/?q=Tuwaiq+Palace+Riyadh',
            'welcome_message' => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n«وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»",
            'invitation_message' => 'تتشرف العائلتان الكريمتان بدعوتكم لحضور حفل الزفاف ومشاركتنا فرحة العمر في ليلة عامرة بالمودة والسرور.',
            'cover_photo' => null,
            'music_url' => null,
            'is_rsvp_enabled' => false,
            'invitation_mode' => Invitation::MODE_ANNOUNCEMENT,
            'rsvp_deadline' => null,
            'max_guests_per_rsvp' => 5,
            'status' => 'published',
            'template' => $template ?? (object) [
                'name_ar' => 'تراث الدرعية',
                'name_en' => 'Diriyah Heritage',
                'slug' => 'diriyah-heritage',
                'view_name' => 'templates.diriyah_heritage',
            ],
            'effective_styles' => array_merge(
                ['locale' => 'ar', 'digits' => 'arabic_indic', 'show_hijri' => true],
                $defaultStyles
            ),
            'sections' => collect([
                (object) [
                    'section_type' => 'countdown',
                    'title_ar' => 'العد التنازلي للزفاف',
                    'content' => ['target_date' => now()->addDays(42)->toIso8601String()],
                    'is_enabled' => true,
                ],
            ]),
            'photos' => collect([]),
            'public_url' => route('demo.saudi'),
        ];

        return view('pages.demo', compact('demoInvitation'));
    }

    public function templates(Request $request)
    {
        if ($request->query('ref') === 'invite') {
            $request->session()->put('signup_source', 'invite_footer');

            $visitGuardKey = 'kpi_invite_footer_'.now()->toDateString();
            if (! $request->session()->has($visitGuardKey)) {
                $request->session()->put($visitGuardKey, true);
                FunnelDailyMetric::incrementToday(FunnelDailyMetric::KEY_INVITE_FOOTER_VISIT);
            }
        }
        $categorySlug = $request->query('category');
        $collection = $request->query('collection');
        $search = $request->query('search');
        $saudiSignatureSlugs = TemplateStyleCatalog::saudiSignatureSlugs();

        $query = InvitationTemplate::with('category')->availableForSelection();

        if ($collection === 'saudi-signature') {
            $query->whereIn('slug', $saudiSignatureSlugs);
        }

        if ($categorySlug && $categorySlug !== 'all') {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('description_ar', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('sort_order')->get();
        $categories = TemplateCategory::where('is_active', true)->orderBy('sort_order')->get();
        $saudiSignatureCount = InvitationTemplate::availableForSelection()
            ->whereIn('slug', $saudiSignatureSlugs)
            ->count();

        return view('pages.templates', compact(
            'templates',
            'categories',
            'categorySlug',
            'collection',
            'search',
            'saudiSignatureSlugs',
            'saudiSignatureCount'
        ));
    }

    public function previewTemplate(string $slug)
    {
        $template = InvitationTemplate::where('slug', $slug)
            ->availableForSelection()
            ->firstOrFail();

        $isSaudiSignature = in_array($template->slug, TemplateStyleCatalog::saudiSignatureSlugs(), true);

        // Sample wedding invitation data for previewing this template
        $previewData = (object) [
            'id' => 0,
            'title' => 'معاينة: حفل زفاف '.$template->name_ar,
            'slug' => $template->slug,
            'groom_name' => $isSaudiSignature ? 'فيصل بن عبد العزيز آل مقرن' : 'عمر عبد الرحمن الشناوي',
            'bride_name' => $isSaudiSignature ? 'الجوهرة بنت خالد السديري' : 'نور مصطفى الألفي',
            'wedding_date' => now()->addDays(30),
            'wedding_time' => '20:30',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => $isSaudiSignature ? 'قصر طويق للاحتفالات — حي السفارات' : 'قصر البارون إمبان - حديقة القصر الملكية',
            'venue_address' => $isSaudiSignature ? 'طريق مكة المكرمة، حي السفارات، الرياض، المملكة العربية السعودية' : 'شارع العروبة، مصر الجديدة، القاهرة، مصر',
            'venue_map_url' => $isSaudiSignature ? 'https://maps.google.com/?q=Tuwaiq+Palace+Riyadh' : 'https://maps.google.com/?q=Baron+Empain+Palace',
            'welcome_message' => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n«وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»",
            'invitation_message' => $isSaudiSignature
                ? 'تتشرف العائلتان الكريمتان بدعوتكم لحضور حفل الزفاف ومشاركتنا فرحة العمر في ليلة عامرة بالمودة والسرور.'
                : 'يسرنا دعوتكم لمشاركتنا فرحة العمر وتتويج حكاية حبنا في ليلة لا تكتمل إلا بحضوركم العطر ومشاركتكم الكريمة.',
            'cover_photo' => null,
            'music_url' => null,
            'is_rsvp_enabled' => true,
            'rsvp_deadline' => now()->addDays(20),
            'max_guests_per_rsvp' => 4,
            'status' => 'published',
            'template' => $template,
            'effective_styles' => array_merge(
                ['locale' => 'ar', 'digits' => 'latin', 'show_hijri' => true],
                $template->default_styles ?? [
                    'primary_color' => '#C6A76B',
                    'secondary_color' => '#292521',
                    'background_color' => '#FAF7F2',
                    'card_color' => '#FFFFFF',
                    'font_family' => 'Tajawal',
                ]
            ),
            'sections' => collect([
                (object) [
                    'section_type' => 'countdown',
                    'title_ar' => 'العد التنازلي للزفاف',
                    'content' => ['target_date' => now()->addDays(30)->toIso8601String()],
                    'is_enabled' => true,
                ],
                (object) [
                    'section_type' => 'timeline',
                    'title_ar' => 'مواعيد الليلة السعيدة',
                    'content' => [
                        'events' => [
                            ['time' => '08:00 م', 'title' => 'استقبال الضيوف الكرام', 'desc' => 'الترحيب والضيافة العربية'],
                            ['time' => '09:00 م', 'title' => 'الزفّة ودخول العروسين', 'desc' => 'لحظة الفرح والترحيب بالعروسين'],
                            ['time' => '10:00 م', 'title' => 'مأدبة العشاء', 'desc' => 'أشهى الأطباق والضيافة الفاخرة'],
                        ],
                    ],
                    'is_enabled' => true,
                ],
            ]),
            'photos' => collect([]),
        ];

        return view('pages.preview-template', compact('template', 'previewData'));
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.pricing', compact('plans'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
