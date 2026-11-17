<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            return;
        }

        DB::table('platform_settings')
            ->whereIn('key', ['contact_email', 'contact_phone', 'whatsapp_support'])
            ->where('group', 'general')
            ->update(['group' => 'contact']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            return;
        }

        DB::table('platform_settings')
            ->whereIn('key', ['contact_email', 'contact_phone', 'whatsapp_support'])
            ->where('group', 'contact')
            ->update(['group' => 'general']);
    }
};
