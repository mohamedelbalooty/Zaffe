<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InvitationGate extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'name',
        'access_token',
        'is_active',
        'scan_count',
        'last_scan_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'scan_count' => 'integer',
        'last_scan_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (InvitationGate $gate): void {
            if (empty($gate->access_token)) {
                $gate->access_token = Str::random(64);
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function checkedInGuests(): HasMany
    {
        return $this->hasMany(InvitationGuest::class, 'checked_in_by_gate_id');
    }

    public function getPortalUrl(): string
    {
        return route('gate.portal', ['gateToken' => $this->access_token]);
    }
}
