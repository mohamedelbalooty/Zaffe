<?php

namespace App\Policies;

use App\Models\PlatformSetting;
use App\Models\User;

class PlatformSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, PlatformSetting $platformSetting): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, PlatformSetting $platformSetting): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PlatformSetting $platformSetting): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
