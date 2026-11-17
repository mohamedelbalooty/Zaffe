<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->unsignedInteger('cover_photo_width')->nullable()->after('cover_photo');
            $table->unsignedInteger('cover_photo_height')->nullable()->after('cover_photo_width');
        });

        Schema::table('invitation_photos', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('image_path');
            $table->unsignedInteger('width')->nullable()->after('thumbnail_path');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->unsignedInteger('thumbnail_width')->nullable()->after('height');
            $table->unsignedInteger('thumbnail_height')->nullable()->after('thumbnail_width');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn(['cover_photo_width', 'cover_photo_height']);
        });

        Schema::table('invitation_photos', function (Blueprint $table) {
            $table->dropColumn([
                'thumbnail_path',
                'width',
                'height',
                'thumbnail_width',
                'thumbnail_height',
            ]);
        });
    }
};
