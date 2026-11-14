<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\SubscriptionPlan;
use App\Models\TemplateCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredTemplates = InvitationTemplate::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $categories = TemplateCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $demoInvitation = Invitation::with('template')->where('slug', 'ahmed-and-sarah')->first();

        return view('pages.home', compact('featuredTemplates', 'categories', 'plans', 'demoInvitation'));
    }

    public function templates(Request $request)
    {
        $categorySlug = $request->query('category');
        $search = $request->query('search');

        $query = InvitationTemplate::with('category')->where('is_active', true);

        if ($categorySlug && $categorySlug !== 'all') {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('sort_order')->get();
        $categories = TemplateCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.templates', compact('templates', 'categories', 'categorySlug', 'search'));
    }

    public function previewTemplate(string $slug)
    {
        $template = InvitationTemplate::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Sample wedding invitation data for previewing this template
        $previewData = (object) [
            'id' => 0,
            'title' => 'معاينة: حفل زفاف ' . $template->name_ar,
            'slug' => $template->slug,
            'groom_name' => 'عمر عبد الرحمن الشناوي',
            'bride_name' => 'نور مصطفى الألفي',
            'wedding_date' => now()->addDays(30),
            'wedding_time' => '20:30',
            'timezone' => 'Asia/Riyadh',
            'venue_name' => 'قصر البارون إمبان - حديقة القصر الملكية',
            'venue_address' => 'شارع العروبة، مصر الجديدة، القاهرة، مصر',
            'venue_map_url' => 'https://maps.google.com/?q=Baron+Empain+Palace',
            'welcome_message' => "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n«وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»",
            'invitation_message' => 'يسرنا دعوتكم لمشاركتنا فرحة العمر وتتويج حكاية حبنا في ليلة لا تكتمل إلا بحضوركم العطر ومشاركتكم الكريمة.',
            'cover_photo' => null,
            'music_url' => null,
            'is_rsvp_enabled' => true,
            'rsvp_deadline' => now()->addDays(20),
            'max_guests_per_rsvp' => 4,
            'status' => 'published',
            'template' => $template,
            'effective_styles' => $template->default_styles ?? [
                'primary_color' => '#C6A76B',
                'secondary_color' => '#292521',
                'background_color' => '#FAF7F2',
                'card_color' => '#FFFFFF',
                'font_family' => 'Tajawal',
            ],
            'sections' => collect([
                (object)[
                    'section_type' => 'countdown',
                    'title_ar' => 'العد التنازلي للزفاف',
                    'content' => ['target_date' => now()->addDays(30)->toIso8601String()],
                    'is_enabled' => true,
                ],
                (object)[
                    'section_type' => 'timeline',
                    'title_ar' => 'مواعيد الليلة السعيدة',
                    'content' => [
                        'events' => [
                            ['time' => '08:00 م', 'title' => 'استقبال الضيوف الكرام', 'desc' => 'الترحيب والمشروبات المنعشة'],
                            ['time' => '09:00 م', 'title' => 'الزفّة ودخول العروسين', 'desc' => 'دخول أسطوري وزفة المحبة'],
                            ['time' => '10:00 م', 'title' => 'مأدبة العشاء', 'desc' => 'أشهى الأطباق'],
                        ]
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

