<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FunnelDailyMetric extends Model
{
    public const KEY_CREATION_START = 'creation_start';

    public const KEY_INVITE_FOOTER_VISIT = 'invite_footer_visit';

    protected $fillable = [
        'metric_date',
        'metric_key',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'count' => 'integer',
        ];
    }

    public static function incrementToday(string $metricKey, int $amount = 1): void
    {
        if (! Schema::hasTable('funnel_daily_metrics')) {
            return;
        }

        $today = now()->toDateString();
        $now = now();

        $updated = DB::table('funnel_daily_metrics')
            ->where('metric_date', $today)
            ->where('metric_key', $metricKey)
            ->increment('count', $amount, ['updated_at' => $now]);

        if ($updated > 0) {
            return;
        }

        try {
            DB::table('funnel_daily_metrics')->insert([
                'metric_date' => $today,
                'metric_key' => $metricKey,
                'count' => $amount,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (QueryException) {
            DB::table('funnel_daily_metrics')
                ->where('metric_date', $today)
                ->where('metric_key', $metricKey)
                ->increment('count', $amount, ['updated_at' => $now]);
        }
    }

    public static function sumForKey(string $metricKey, ?CarbonInterface $since = null): int
    {
        if (! Schema::hasTable('funnel_daily_metrics')) {
            return 0;
        }

        $query = static::query()->where('metric_key', $metricKey);
        if ($since !== null) {
            $query->whereDate('metric_date', '>=', $since->toDateString());
        }

        return (int) $query->sum('count');
    }
}
