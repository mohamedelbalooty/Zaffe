<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'section_type',
        'title_ar',
        'title_en',
        'content',
        'sort_order',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}

