<?php

namespace App\Policies;

use App\Models\InvitationTemplate;
use App\Models\User;

class InvitationTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, InvitationTemplate $invitationTemplate): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, InvitationTemplate $invitationTemplate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, InvitationTemplate $invitationTemplate): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
