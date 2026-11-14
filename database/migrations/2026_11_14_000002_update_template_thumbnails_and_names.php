<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invitation_templates')) {
            return;
        }

        $templates = [
            'royal-gold' => [
                'name_ar' => 'الفخامة الذهبية',
                'name_en' => 'Royal Gold & Ivory',
                'thumbnail' => '/images/templates/royal-gold.webp',
            ],
            'floral-romance' => [
                'name_ar' => 'سحر الزهور والحديقة',
                'name_en' => 'Floral Romance & Rose',
                'thumbnail' => '/images/templates/floral-romance.webp',
            ],
            'minimal-chic' => [
                'name_ar' => 'البساطة الراقية',
                'name_en' => 'Minimalist Modern Chic',
                'thumbnail' => '/images/templates/minimal-chic.webp',
            ],
            'islamic-elegance' => [
                'name_ar' => 'الأصالة الإسلامية',
                'name_en' => 'Islamic Heritage & Arabesque',
                'thumbnail' => '/images/templates/islamic-elegance.webp',
            ],
            'emerald-night' => [
                'name_ar' => 'ليلة النجوم الملكية',
                'name_en' => 'Midnight Starry Gala',
                'thumbnail' => '/images/templates/emerald-night.webp',
            ],
            'classic-aristocrat' => [
                'name_ar' => 'الكلاسيكي العريق',
                'name_en' => 'Classic Aristocrat & Seal',
                'thumbnail' => '/images/templates/classic-aristocrat.webp',
            ],
        ];

        $now = now();

        foreach ($templates as $slug => $attributes) {
            DB::table('invitation_templates')
                ->where('slug', $slug)
                ->update(array_merge($attributes, ['updated_at' => $now]));
        }

        DB::table('invitation_templates')
            ->whereNotIn('slug', array_keys($templates))
            ->get(['id', 'slug', 'name_ar', 'thumbnail'])
            ->each(function (object $template) use ($now): void {
                $updates = [];

                if (is_string($template->thumbnail) && preg_match('#^https?://#i', $template->thumbnail)) {
                    $updates['thumbnail'] = '/images/templates/' . $template->slug . '.webp';
                }

                if (is_string($template->name_ar)) {
                    $cleanedNameAr = trim((string) preg_replace('/\s*\([A-Za-z0-9\s&\'-]+\)\s*$/u', '', $template->name_ar));
                    if ($cleanedNameAr !== '' && $cleanedNameAr !== $template->name_ar) {
                        $updates['name_ar'] = $cleanedNameAr;
                    }
                }

                if ($updates !== []) {
                    $updates['updated_at'] = $now;
                    DB::table('invitation_templates')
                        ->where('id', $template->id)
                        ->update($updates);
                }
            });
    }

    public function down(): void
    {
        // Intentionally non-destructive: do not restore external hotlinked URLs.
    }
};
