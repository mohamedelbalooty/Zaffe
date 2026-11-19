<?php

use App\Support\TemplateStyleCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('template_categories') || ! Schema::hasTable('invitation_templates')) {
            return;
        }

        $baselineSortOrders = [
            'royal-gold' => 9,
            'floral-romance' => 10,
            'minimal-chic' => 11,
            'islamic-elegance' => 12,
            'emerald-night' => 13,
            'classic-aristocrat' => 14,
        ];

        $hasBaselineCatalog = DB::table('invitation_templates')
            ->whereIn('slug', array_keys($baselineSortOrders))
            ->exists();

        // Keep fresh/test databases untouched until seeded explicitly.
        if (! $hasBaselineCatalog) {
            return;
        }

        $now = now();

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

        $categoryIds = [];
        foreach ($categories as $category) {
            $existingCategoryId = DB::table('template_categories')
                ->where('slug', $category['slug'])
                ->value('id');

            if ($existingCategoryId !== null) {
                $categoryIds[$category['slug']] = (int) $existingCategoryId;
            } else {
                $categoryIds[$category['slug']] = (int) DB::table('template_categories')->insertGetId(array_merge($category, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        foreach ($baselineSortOrders as $slug => $sortOrder) {
            DB::table('invitation_templates')
                ->where('slug', $slug)
                ->update([
                    'sort_order' => $sortOrder,
                    'updated_at' => $now,
                ]);
        }

        $presets = TemplateStyleCatalog::presets();

        $saudiSignatureTemplates = [
            [
                'category_slug' => 'floral',
                'slug' => 'taif-rose',
                'name_ar' => 'ورد الطائف',
                'name_en' => 'Taif Rose',
                'view_name' => 'templates.taif_rose',
                'description_ar' => 'تصميم حريري فاخر مستوحى من ورد الطائف وأزهار الكوزموس المائية، مع تخطيط ديواني متوازن للأسماء ومسار ضيافة ثلاثي المراحل.',
                'description_en' => 'Couture silk stationery with watercolor Taif roses and cosmos botanicals, calligraphic Diwani script, and a three-stage ceremony ledger.',
                'thumbnail' => '/images/templates/taif-rose.webp',
                'preset_key' => 'taif_rose',
                'sort_order' => 1,
            ],
            [
                'category_slug' => 'classic',
                'slug' => 'diriyah-heritage',
                'name_ar' => 'تراث الدرعية',
                'name_en' => 'Diriyah Heritage',
                'view_name' => 'templates.diriyah_heritage',
                'description_ar' => 'ورق نجد العاجي بأقواس حروفية عربية في الزوايا، وتخطيط متوازن لأسماء العروسين ومسار الضيافة الثلاثي.',
                'description_en' => 'Najdi parchment stationery with curved Hurufiyya calligraphic corner arcs, symmetrical couple triad, and three-stage hospitality thread.',
                'thumbnail' => '/images/templates/diriyah-heritage.webp',
                'preset_key' => 'diriyah_heritage',
                'sort_order' => 2,
            ],
            [
                'category_slug' => 'islamic',
                'slug' => 'rawashin-elegance',
                'name_ar' => 'أناقة الرواشين',
                'name_en' => 'Rawashin Elegance',
                'view_name' => 'templates.rawashin_elegance',
                'description_ar' => 'بطاقة حجازية فاخرة بنقش الرواشين المحفور في الخلفية وزهور البورسلين الفيروزية والمرجان الدافئ.',
                'description_en' => 'Hijazi alabaster stationery with embossed mashrabiya relief, porcelain-teal and coral garden roses, and three-column ceremony schedule.',
                'thumbnail' => '/images/templates/rawashin-elegance.webp',
                'preset_key' => 'rawashin_elegance',
                'sort_order' => 3,
            ],
            [
                'category_slug' => 'classic',
                'slug' => 'sadu-signature',
                'name_ar' => 'نقوش السدو',
                'name_en' => 'Sadu Signature',
                'view_name' => 'templates.sadu_signature',
                'description_ar' => 'قراءة عصرية راقية لحياكة السدو مع أزهار الخشخاش القرمزية والتيراكوتا وتناغم الخط الديواني.',
                'description_en' => 'Al Sadu wool-ivory stationery with subtle geometric diamond watermark, crimson poppy corners, and sweeping Diwani couple ligature.',
                'thumbnail' => '/images/templates/sadu-signature.webp',
                'preset_key' => 'sadu_signature',
                'sort_order' => 4,
            ],
            [
                'category_slug' => 'luxury',
                'slug' => 'palm-oasis',
                'name_ar' => 'واحة النخيل',
                'name_en' => 'Palm Oasis',
                'view_name' => 'templates.palm_oasis',
                'description_ar' => 'بطاقة من الكتان المصقول تتهادى في أسفلها حديقة بانورامية من أغصان الكينا وسعف الواحات بلمسات ذهبية.',
                'description_en' => 'Brushed linen-ivory stationery anchored by a full-width watercolor eucalyptus and oasis frond bed with golden champagne accents.',
                'thumbnail' => '/images/templates/palm-oasis.webp',
                'preset_key' => 'palm_oasis',
                'sort_order' => 5,
            ],
            [
                'category_slug' => 'minimal',
                'slug' => 'red-sea-pearl',
                'name_ar' => 'لؤلؤ البحر الأحمر',
                'name_en' => 'Red Sea Pearl',
                'view_name' => 'templates.red_sea_pearl',
                'description_ar' => 'تصميم لؤلؤي مضيء مستلهم من صدف البحر الأحمر المنقوش وزهور الماغنوليا البيضاء بحدود فضية ناعمة.',
                'description_en' => 'Luminous mother-of-pearl stationery with sculpted bas-relief shell fans, white magnolia petals, and delicate pearl corner nodes.',
                'thumbnail' => '/images/templates/red-sea-pearl.webp',
                'preset_key' => 'red_sea_pearl',
                'sort_order' => 6,
            ],
            [
                'category_slug' => 'romantic',
                'slug' => 'desert-dusk',
                'name_ar' => 'غروب الصحراء',
                'name_en' => 'Desert Dusk',
                'view_name' => 'templates.desert_dusk',
                'description_ar' => 'حرير موكا دافئ تعبره أشعة الغروب الذهبية مع خط عربي عاجي مضيء وتفاصيل صحراوية راقية.',
                'description_en' => 'Warm mocha-taupe silk stationery crossed by diagonal sunset light folds, luminous ivory calligraphy, and champagne-sand details.',
                'thumbnail' => '/images/templates/desert-dusk.webp',
                'preset_key' => 'desert_dusk',
                'sort_order' => 7,
            ],
            [
                'category_slug' => 'minimal',
                'slug' => 'modern-monogram',
                'name_ar' => 'المونوغرام العصري',
                'name_en' => 'Modern Monogram',
                'view_name' => 'templates.modern_monogram',
                'description_ar' => 'بطاقة تحريرية بيضاء بنقوش نباتية محفورة في الخلفية وشعار مونوغرام هندسي وتباين الأسود مع الذهب.',
                'description_en' => 'Haute-couture gallery ivory stationery featuring oversized embossed botanical line-art, diamond monogram seal, and obsidian typography.',
                'thumbnail' => '/images/templates/modern-monogram.webp',
                'preset_key' => 'modern_monogram',
                'sort_order' => 8,
            ],
        ];

        foreach ($saudiSignatureTemplates as $template) {
            $attributes = [
                'category_id' => $categoryIds[$template['category_slug']],
                'name_ar' => $template['name_ar'],
                'name_en' => $template['name_en'],
                'view_name' => $template['view_name'],
                'description_ar' => $template['description_ar'],
                'description_en' => $template['description_en'],
                'thumbnail' => $template['thumbnail'],
                'default_styles' => json_encode(
                    $presets[$template['preset_key']],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'is_premium' => false,
                'sort_order' => $template['sort_order'],
                'updated_at' => $now,
            ];

            $exists = DB::table('invitation_templates')
                ->where('slug', $template['slug'])
                ->exists();

            if ($exists) {
                DB::table('invitation_templates')
                    ->where('slug', $template['slug'])
                    ->update($attributes);
            } else {
                DB::table('invitation_templates')->insert(array_merge($attributes, [
                    'slug' => $template['slug'],
                    'is_active' => true,
                    'created_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: preserve catalog templates that may already be referenced by user invitations.
    }
};
