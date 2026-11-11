<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('template_categories')->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('view_name');
            $table->json('default_styles')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_templates');
    }
};

