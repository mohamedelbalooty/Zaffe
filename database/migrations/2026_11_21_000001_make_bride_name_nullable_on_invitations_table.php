<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table): void {
            $table->string('bride_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('invitations')
            ->whereNull('bride_name')
            ->update(['bride_name' => '']);

        Schema::table('invitations', function (Blueprint $table): void {
            $table->string('bride_name')->nullable(false)->change();
        });
    }
};
