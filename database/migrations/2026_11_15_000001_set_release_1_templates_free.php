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

        DB::table('invitation_templates')
            ->whereIn('slug', [
                'royal-gold',
                'floral-romance',
                'minimal-chic',
                'islamic-elegance',
                'emerald-night',
                'classic-aristocrat',
            ])
            ->update([
                'is_premium' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('invitation_templates')) {
            return;
        }

        DB::table('invitation_templates')
            ->whereIn('slug', [
                'islamic-elegance',
                'emerald-night',
            ])
            ->update([
                'is_premium' => true,
                'updated_at' => now(),
            ]);
    }
};
