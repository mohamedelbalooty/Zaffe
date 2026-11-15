<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->string('invitation_mode', 20)
                ->default('rsvp_qr')
                ->after('is_rsvp_enabled');
        });

        DB::table('invitations')
            ->where('is_rsvp_enabled', false)
            ->update(['invitation_mode' => 'announcement']);

        Schema::create('invitation_gates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('access_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scan_at')->nullable();
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['invitation_id', 'is_active']);
        });

        Schema::create('invitation_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('phone', 30)->nullable();
            $table->string('category', 30)->default('general');
            $table->unsignedTinyInteger('max_adults')->default(1);
            $table->unsignedTinyInteger('max_children')->default(0);
            $table->string('invite_token', 48)->unique();
            $table->string('qr_token', 48)->unique();
            $table->string('pass_code', 12);
            $table->string('rsvp_status', 20)->default('pending');
            $table->unsignedTinyInteger('confirmed_adults')->default(0);
            $table->unsignedTinyInteger('confirmed_children')->default(0);
            $table->foreignId('rsvp_response_id')
                ->nullable()
                ->constrained('rsvp_responses')
                ->nullOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->unsignedTinyInteger('checked_in_count')->default(0);
            $table->foreignId('checked_in_by_gate_id')
                ->nullable()
                ->constrained('invitation_gates')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['invitation_id', 'pass_code']);
            $table->index(['invitation_id', 'rsvp_status']);
            $table->index(['invitation_id', 'phone']);
            $table->index(['invitation_id', 'checked_in_at']);
        });

        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->foreignId('invitation_guest_id')
                ->nullable()
                ->after('invitation_id')
                ->constrained('invitation_guests')
                ->nullOnDelete();
            $table->unsignedTinyInteger('children_count')
                ->default(0)
                ->after('guest_count');
        });
    }

    public function down(): void
    {
        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invitation_guest_id');
            $table->dropColumn('children_count');
        });

        Schema::dropIfExists('invitation_guests');
        Schema::dropIfExists('invitation_gates');

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('invitation_mode');
        });
    }
};
