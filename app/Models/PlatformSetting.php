<?php

namespace App\Models;

use App\Support\PlatformSettingCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'platform_settings.all';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Retrieve a cached setting value with JSON decoding and migration-safe fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = Cache::rememberForever(
                self::CACHE_KEY,
                fn (): array => static::query()->pluck('value', 'key')->all()
            );

            if (! array_key_exists($key, $settings) || $settings[$key] === null) {
                return $default;
            }

            $raw = $settings[$key];
            $decoded = json_decode((string) $raw, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $raw;
        } catch (QueryException) {
            return $default;
        }
    }

    /**
     * Format the configured WhatsApp support number into a clean wa.me link.
     */
    public static function whatsappUrl(string $fallback = '201000000000'): string
    {
        return PlatformSettingCatalog::whatsappPreviewUrl(
            (string) static::get('whatsapp_support', $fallback),
            $fallback,
        );
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        $stringValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue, 'group' => $group]
        );
        static::clearCache();
    }
}
