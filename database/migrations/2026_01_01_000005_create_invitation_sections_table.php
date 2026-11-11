<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('section_type'); // countdown, story, timeline, gallery, location, rsvp, dress_code, message
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['invitation_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_sections');
    }
};

