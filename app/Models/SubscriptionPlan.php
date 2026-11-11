<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'price',
        'currency',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar;
    }
}

