<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\PlatformSetting;
use App\Models\RsvpResponse;
use App\Models\SubscriptionPlan;
use App\Models\TemplateCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@zaffe.app'],
            [
                'name' => 'مدير منصة زفّة',
                'phone' => '+201000000001',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
            ]
        );

        // 2. Demo Couple User
        $couple = User::updateOrCreate(
            ['email' => 'couple@zaffe.app'],
            [
                'name' => 'أحمد وسارة (العروسان)',
                'phone' => '+201123456789',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=200&q=80',
            ]
        );

        // 3. Template Categories
        $categories = [
            [
                'slug' => 'luxury',
                'name_ar' => 'ملكي فاخر',
                'name_en' => 'Luxury Royal',
                'icon' => 'crown',
                'description_ar' => 'تصاميم مستوحاة من القصور الملكية باللمسات الذهبية المهيبة',
                'sort_order' => 1,
            ],
            [
                'slug' => 'floral',
                'name_ar' => 'زهور ورومانسية',
                'name_en' => 'Floral & Botanical',
                'icon' => 'flower-2',
                'description_ar' => 'ألوان ناعمة وزهور مائية تفيض رقة ودفئاً',
                'sort_order' => 2,
            ],
            [
                'slug' => 'minimal',
                'name_ar' => 'بسيط وعصري',
                'name_en' => 'Minimal Modern',
                'icon' => 'sparkles',
                'description_ar' => 'خطوط نقية وهدوء يعكس الأناقة المعاصرة',
                'sort_order' => 3,
            ],
            [
                'slug' => 'islamic',
                'name_ar' => 'أصالة إسلامية',
                'name_en' => 'Islamic Heritage',
                'icon' => 'moon-star',
                'description_ar' => 'زخارف إسلامية أرابيسك وآيات قرآنية بديعة',
                'sort_order' => 4,
            ],
            [
                'slug' => 'classic',
                'name_ar' => 'كلاسيكي عريق',
                'name_en' => 'Classic Heritage',
                'icon' => 'scroll',
                'description_ar' => 'الطابع المصري والعربي الأصيل مع ختم الشمع والخط الأندلسي',
                'sort_order' => 5,
            ],
            [
                'slug' => 'romantic',
                'name_ar' => 'ليلة النجوم والغموض',
                'name_en' => 'Celestial Night',
                'icon' => 'stars',
                'description_ar' => 'أجواء مسائية ساحرة بلون كحلي وذهبي لامع',
                'sort_order' => 6,
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = TemplateCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 4. Invitation Templates (6 rich templates)
        $templates = [
            [
                'category_id' => $categoryModels['luxury']->id,
                'slug' => 'royal-gold',
                'name_ar' => 'الفخامة الذهبية (Royal Gold)',
                'name_en' => 'Royal Gold & Ivory',
                'view_name' => 'templates.royal_gold',
                'description_ar' => 'إطارات مذهبة هندسية مع خط عربي ملكي وخلفية عاجية دافئة تليق بأفخم الأعراس.',
                'description_en' => 'Gilded geometric framing with royal Arabic calligraphy on warm ivory parchment.',
                'thumbnail' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#C6A76B',
                    'secondary_color' => '#292521',
                    'background_color' => '#FAF7F2',
                    'card_color' => '#FFFFFF',
                    'accent_color' => '#D9B7B0',
                    'font_family' => 'Tajawal',
                    'border_style' => 'double-gold',
                    'ornament_style' => 'royal_crest',
                ],
                'is_premium' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categoryModels['floral']->id,
                'slug' => 'floral-romance',
                'name_ar' => 'سحر الزهور والحديقة (Floral Romance)',
                'name_en' => 'Floral Romance & Rose',
                'view_name' => 'templates.floral_romance',
                'description_ar' => 'ألوان الباستيل الوردية وزهور الأوركيد والمشمش المائية الرقيقة المفعمة بالحب.',
                'description_en' => 'Delicate blush pink watercolor botanicals and romantic garden vibes.',
                'thumbnail' => 'https://images.unsplash.com/photo-1519225429402-957e8fa134d1?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#D9828A',
                    'secondary_color' => '#4A3E3D',
                    'background_color' => '#FDF8F7',
                    'card_color' => '#FFFFFF',
                    'accent_color' => '#8BA888',
                    'font_family' => 'Cairo',
                    'border_style' => 'floral-wreath',
                    'ornament_style' => 'blooming_flowers',
                ],
                'is_premium' => false,
                'sort_order' => 2,
            ],
            [
                'category_id' => $categoryModels['minimal']->id,
                'slug' => 'minimal-chic',
                'name_ar' => 'البساطة الراقية (Minimal Chic)',
                'name_en' => 'Minimalist Modern Chic',
                'view_name' => 'templates.minimal_chic',
                'description_ar' => 'عصرية فائقة بخطوط بسيطة ومساحات بيضاء نقية تركز على جمال التفاصيل.',
                'description_en' => 'Ultra modern aesthetic with clean typography, generous whitespace, and understated luxury.',
                'thumbnail' => 'https://images.unsplash.com/photo-1544077960-604201fe74bc?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#1F2937',
                    'secondary_color' => '#6B7280',
                    'background_color' => '#FAFAFA',
                    'card_color' => '#FFFFFF',
                    'accent_color' => '#C6A76B',
                    'font_family' => 'Tajawal',
                    'border_style' => 'clean-line',
                    'ornament_style' => 'minimal_dots',
                ],
                'is_premium' => false,
                'sort_order' => 3,
            ],
            [
                'category_id' => $categoryModels['islamic']->id,
                'slug' => 'islamic-elegance',
                'name_ar' => 'الأصالة الإسلامية (Islamic Heritage)',
                'name_en' => 'Islamic Heritage & Arabesque',
                'view_name' => 'templates.islamic_elegance',
                'description_ar' => 'زخارف أرابيسك هندسية متداخلة مع آية المودة والرحمة ودرجات الزمرد والذهب.',
                'description_en' => 'Intricate arabesque motifs with Quranic blessings in majestic emerald and gold.',
                'thumbnail' => 'https://images.unsplash.com/photo-1584824486509-112e4181ff6b?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#0F4C3A',
                    'secondary_color' => '#C6A76B',
                    'background_color' => '#F4F7F5',
                    'card_color' => '#FFFFFF',
                    'accent_color' => '#2D6A4F',
                    'font_family' => 'Amiri',
                    'border_style' => 'arabesque-lattice',
                    'ornament_style' => 'islamic_star',
                ],
                'is_premium' => true,
                'sort_order' => 4,
            ],
            [
                'category_id' => $categoryModels['romantic']->id,
                'slug' => 'emerald-night',
                'name_ar' => 'ليلة النجوم الملكية (Starry Gala)',
                'name_en' => 'Midnight Starry Gala',
                'view_name' => 'templates.emerald_night',
                'description_ar' => 'سحر المساء بلون الليل الكحلي الداكن والنجوم المتلألئة المذهبة لأمسية أسطورية.',
                'description_en' => 'Midnight sapphire and shimmering constellation stardust for an enchanting evening.',
                'thumbnail' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#E6C687',
                    'secondary_color' => '#E2E8F0',
                    'background_color' => '#0F172A',
                    'card_color' => '#1E293B',
                    'accent_color' => '#38BDF8',
                    'font_family' => 'Cairo',
                    'border_style' => 'stardust-glow',
                    'ornament_style' => 'constellation',
                ],
                'is_premium' => true,
                'sort_order' => 5,
            ],
            [
                'category_id' => $categoryModels['classic']->id,
                'slug' => 'classic-aristocrat',
                'name_ar' => 'الكلاسيكي العريق (Aristocrat)',
                'name_en' => 'Classic Aristocrat & Seal',
                'view_name' => 'templates.classic_aristocrat',
                'description_ar' => 'طابع البطاقات الورقية الكلاسيكية الفاخرة مع ختم الشمع الأحمر وخطوط ديوانية مهيبة.',
                'description_en' => 'Traditional high-society invitation aesthetic featuring a wax seal motif and noble script.',
                'thumbnail' => 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?auto=format&fit=crop&w=600&q=80',
                'default_styles' => [
                    'primary_color' => '#8B263E',
                    'secondary_color' => '#2B2B2B',
                    'background_color' => '#F8F6F0',
                    'card_color' => '#FFFFFF',
                    'accent_color' => '#D4AF37',
                    'font_family' => 'Tajawal',
                    'border_style' => 'wax-seal-border',
                    'ornament_style' => 'wax_seal',
                ],
                'is_premium' => false,
                'sort_order' => 6,
            ],
        ];

        $templateModels = [];
        foreach ($templates as $tmpl) {
            $templateModels[$tmpl['slug']] = InvitationTemplate::updateOrCreate(['slug' => $tmpl['slug']], $tmpl);
        }

        // 5. Create Demo Published Invitation
        $invitation = Invitation::updateOrCreate(
            ['slug' => 'ahmed-and-sarah'],
            [
                'user_id' => $couple->id,
                'template_id' => $templateModels['royal-gold']->id,
                'title' => 'حفل زفاف أحمد وسارة',
                'groom_name' => 'أحمد محمود العطار',
                'bride_name' => 'سارة حسن الشربيني',
                'wedding_date' => now()->addDays(45)->toDateString(),
                'wedding_time' => '20:00:00',
                'venue_name' => 'فندق فورسيزونز نايل بلازا - قاعة بلازا الكبرى',
                'venue_address' => '1089 كورنيش النيل، جاردن سيتي، القاهرة، مصر',
                'venue_map_url' => 'https://maps.google.com/?q=Four+Seasons+Hotel+Cairo+at+Nile+Plaza',
                'welcome_message' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\n«وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً»',
                'invitation_message' => 'يسرنا ويسعدنا مشاركتكم فرحتنا وتلبية دعوتنا في ليلة العمر التي تكتمل بوجودكم ودفء لقائكم. دعوتكم تسعدنا وحضوركم يزين ليلتنا بالبهجة والسرور.',
                'cover_photo' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_rsvp_enabled' => true,
                'rsvp_deadline' => now()->addDays(35)->toDateString(),
                'max_guests_per_rsvp' => 4,
                'view_count' => 184,
                'custom_styles' => [
                    'primary_color' => '#C6A76B',
                    'secondary_color' => '#292521',
                    'background_color' => '#FAF7F2',
                ],
            ]
        );

        // Sections for Ahmed & Sarah
        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'countdown'],
            [
                'title_ar' => 'العد التنازلي لليلتنا المميزة',
                'title_en' => 'Countdown to the Big Day',
                'content' => ['target_date' => now()->addDays(45)->toIso8601String()],
                'sort_order' => 1,
                'is_enabled' => true,
            ]
        );

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'timeline'],
            [
                'title_ar' => 'برنامج الحفل',
                'title_en' => 'Wedding Timeline',
                'content' => [
                    'events' => [
                        ['time' => '07:30 م', 'title' => 'استقبال الضيوف والترحيب', 'desc' => 'مشروبات الاستقبال والموسيقى الهادئة'],
                        ['time' => '08:30 م', 'title' => 'الزفّة ودخول العروسين', 'desc' => 'اللحظة المنتظرة وزفة المحبة'],
                        ['time' => '09:30 م', 'title' => 'مأدبة العشاء الفاخرة', 'desc' => 'بوفيه مفتوح مع أشهى الأطباق الشرقية والغربية'],
                        ['time' => '11:00 م', 'title' => 'تقطيع كعكة الزفاف والاحتفال', 'desc' => 'الرقص والتقاط أجمل الصور التذكارية'],
                    ]
                ],
                'sort_order' => 2,
                'is_enabled' => true,
            ]
        );

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'gallery'],
            [
                'title_ar' => 'ألبوم ذكرياتنا الجميلة',
                'title_en' => 'Our Sweet Moments',
                'content' => [
                    'photos' => [
                        'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=800&q=80',
                    ]
                ],
                'sort_order' => 3,
                'is_enabled' => true,
            ]
        );

        InvitationSection::updateOrCreate(
            ['invitation_id' => $invitation->id, 'section_type' => 'dress_code'],
            [
                'title_ar' => 'قواعد الأناقة واللباس (Dress Code)',
                'title_en' => 'Dress Code',
                'content' => [
                    'title' => 'ملابس رسمية أنيقة (Black Tie / Formal Evening)',
                    'notes' => 'نرجو من ضيوفنا الكرام الالتزام بالملابس الرسمية المسائية، ونطلب بكل لطف تجنب ارتداء اللون الأبيض لتتألق به العروس وحدها.'
                ],
                'sort_order' => 4,
                'is_enabled' => true,
            ]
        );

        // Invitation Photos
        InvitationPhoto::updateOrCreate(
            ['invitation_id' => $invitation->id, 'image_path' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'],
            ['caption' => 'جلسة تصوير الخطوبة على ضفاف النيل', 'is_cover' => true, 'sort_order' => 1]
        );
        InvitationPhoto::updateOrCreate(
            ['invitation_id' => $invitation->id, 'image_path' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=800&q=80'],
            ['caption' => 'معاً نبدأ أجمل حكاية', 'is_cover' => false, 'sort_order' => 2]
        );

        // Demo RSVP responses
        $demoRsvps = [
            [
                'guest_name' => 'د. حسام الشربيني وعائلته',
                'phone' => '+201012345678',
                'email' => 'hossam@example.com',
                'attendance_status' => 'attending',
                'guest_count' => 3,
                'message' => 'ألف مبروك يا حبيبي وأجمل عروسين في الدنيا! نراكم على ألف خير إن شاء الله ❤️',
                'dietary_notes' => 'لا توجد ملاحظات',
                'ip_address' => '197.34.12.80',
            ],
            [
                'guest_name' => 'المهندس كريم عبد العزيز',
                'phone' => '+201198765432',
                'email' => 'karim@example.com',
                'attendance_status' => 'attending',
                'guest_count' => 2,
                'message' => 'مبارك لأعز الأصدقاء، متطلع جداً لمشاركتكم هذا اليوم الرائع!',
                'dietary_notes' => 'نباتي لشخص واحد',
                'ip_address' => '41.233.15.112',
            ],
            [
                'guest_name' => 'الأستاذة هبة الرداد',
                'phone' => '+201255566677',
                'email' => 'heba@example.com',
                'attendance_status' => 'declined',
                'guest_count' => 1,
                'message' => 'ألف ألف مبروك للعروسين.. كنت أتمنى الحضور بشدة ولكن ظروف السفر خارج البلاد تحول دون ذلك. قلبي معكم ودعواتي بالتوفيق والسعادة الدائمة!',
                'dietary_notes' => null,
                'ip_address' => '82.165.197.1',
            ],
            [
                'guest_name' => 'طارق ونهى المنشاوي',
                'phone' => '+201500112233',
                'email' => 'tarek@example.com',
                'attendance_status' => 'attending',
                'guest_count' => 2,
                'message' => 'فرحتكم فرحتنا، بارك الله لكما وبارك عليكما وجمع بينكما في خير.',
                'dietary_notes' => null,
                'ip_address' => '156.204.30.5',
            ],
        ];

        foreach ($demoRsvps as $rsvp) {
            RsvpResponse::updateOrCreate(
                ['invitation_id' => $invitation->id, 'guest_name' => $rsvp['guest_name']],
                array_merge($rsvp, ['invitation_id' => $invitation->id])
            );
        }

        // 6. Subscription Plans
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name_ar' => 'الباقة المجانية التجريبية',
                'name_en' => 'Starter Free',
                'price' => 0,
                'currency' => 'EGP',
                'features' => [
                    'دعوة زفاف رقمية واحدة',
                    'قوالب قياسية محددة',
                    'إدارة حتى 30 تأكيد حضور RSVP',
                    'رابط مخصص للدعوة',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'premium'],
            [
                'name_ar' => 'باقة الزفاف الملكية (الأكثر طلباً)',
                'name_en' => 'Royal Wedding Pro',
                'price' => 799,
                'currency' => 'EGP',
                'features' => [
                    'كل القوالب الفاخرة والملكية',
                    'تأكيدات حضور RSVP غير محدودة',
                    'مشاركة فورية مخصصة عبر واتساب',
                    'ألبوم صور عالي الدقة وتنزيل قائمة الضيوف كملف Excel/CSV',
                    'موسيقى خلفية مخصصة',
                    'تخصيص كامل للألوان والخطوط',
                    'دعم فني مخصص على مدار الساعة',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // 7. Platform Settings
        PlatformSetting::set('site_name_ar', 'زفّة — منصة دعوات الزفاف الرقمية الفاخرة');
        PlatformSetting::set('site_name_en', 'Zaffe — Luxury Digital Wedding Invitations');
        PlatformSetting::set('contact_email', 'support@zaffe.app');
        PlatformSetting::set('contact_phone', '+20 100 000 0000');
        PlatformSetting::set('whatsapp_support', '+201000000000');
    }
}
