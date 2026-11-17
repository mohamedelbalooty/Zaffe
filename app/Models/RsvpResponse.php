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
        'invitation_guest_id',
        'guest_name',
        'phone',
        'email',
        'attendance_status',
        'guest_count',
        'children_count',
        'message',
        'dietary_notes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'children_count' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(InvitationGuest::class, 'invitation_guest_id');
    }
}
