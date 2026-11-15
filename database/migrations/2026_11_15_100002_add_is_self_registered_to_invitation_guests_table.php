<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_guests', function (Blueprint $table) {
            $table->boolean('is_self_registered')
                ->default(false)
                ->after('rsvp_response_id');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_guests', function (Blueprint $table) {
            $table->dropColumn('is_self_registered');
        });
    }
};
