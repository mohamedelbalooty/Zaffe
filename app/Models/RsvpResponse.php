<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsvpResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'guest_name',
        'phone',
        'email',
        'attendance_status',
        'guest_count',
        'message',
        'dietary_notes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}

