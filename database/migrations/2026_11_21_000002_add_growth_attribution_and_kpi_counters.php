<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'signup_source')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('signup_source', 40)->nullable()->index()->after('role');
            });
        }

        if (! Schema::hasTable('funnel_daily_metrics')) {
            Schema::create('funnel_daily_metrics', function (Blueprint $table): void {
                $table->id();
                $table->date('metric_date');
                $table->string('metric_key', 64);
                $table->unsignedBigInteger('count')->default(0);
                $table->timestamps();

                $table->unique(['metric_date', 'metric_key']);
                $table->index(['metric_key', 'metric_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_daily_metrics');

        if (Schema::hasColumn('users', 'signup_source')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['signup_source']);
                $table->dropColumn('signup_source');
            });
        }
    }
};
