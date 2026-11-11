<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvp_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('guest_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('attendance_status')->default('attending'); // attending, declined, tentative
            $table->integer('guest_count')->default(1);
            $table->text('message')->nullable();
            $table->string('dietary_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'attendance_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvp_responses');
    }
};

