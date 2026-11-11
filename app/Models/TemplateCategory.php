<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'icon',
        'description_ar',
        'description_en',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function templates(): HasMany
    {
        return $this->hasMany(InvitationTemplate::class, 'category_id')->orderBy('sort_order');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->description_en ?: $this->description_ar) : $this->description_ar;
    }
}

