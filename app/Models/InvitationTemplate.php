<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvitationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'thumbnail',
        'preview_url',
        'view_name',
        'default_styles',
        'is_premium',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'default_styles' => 'array',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'template_id');
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

