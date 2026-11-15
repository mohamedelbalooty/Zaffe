<?php

namespace App\Policies;

use App\Models\InvitationGate;
use App\Models\User;

class InvitationGatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, InvitationGate $gate): bool
    {
        return $user->isAdmin() || $gate->invitation?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, InvitationGate $gate): bool
    {
        return $user->isAdmin() || $gate->invitation?->user_id === $user->id;
    }

    public function delete(User $user, InvitationGate $gate): bool
    {
        return $user->isAdmin() || $gate->invitation?->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
