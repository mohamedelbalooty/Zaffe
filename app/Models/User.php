<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * Media paths belonging to cascaded invitations captured prior to SQL user deletion.
     *
     * @var array<int, string>
     */
    protected array $pendingInvitationMediaPurgePaths = [];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'signup_source',
        'avatar_url',
    ];

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $paths = [];

            foreach ($user->invitations()->with('photos')->get() as $invitation) {
                array_push($paths, ...$invitation->collectMediaPaths());
            }

            $user->pendingInvitationMediaPurgePaths = array_values(array_unique($paths));
        });

        static::deleted(function (User $user): void {
            $paths = $user->pendingInvitationMediaPurgePaths;
            $user->pendingInvitationMediaPurgePaths = [];

            Invitation::schedulePostCommitMediaPurge($paths, $user->getConnectionName());
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
