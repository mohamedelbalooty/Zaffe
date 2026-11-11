<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('invitation_templates')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('groom_name');
            $table->string('bride_name');
            $table->date('wedding_date');
            $table->string('wedding_time')->nullable();
            $table->string('venue_name');
            $table->text('venue_address')->nullable();
            $table->text('venue_map_url')->nullable();
            $table->text('welcome_message')->nullable();
            $table->text('invitation_message')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('music_url')->nullable();
            $table->json('custom_styles')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_rsvp_enabled')->default(true);
            $table->date('rsvp_deadline')->nullable();
            $table->integer('max_guests_per_rsvp')->default(5);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};

