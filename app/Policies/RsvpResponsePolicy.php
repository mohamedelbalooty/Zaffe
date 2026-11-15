<?php

namespace App\Policies;

use App\Models\RsvpResponse;
use App\Models\User;

class RsvpResponsePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, RsvpResponse $rsvpResponse): bool
    {
        return $user->isAdmin() || $rsvpResponse->invitation?->user_id === $user->id;
    }

    /**
     * Guests submit RSVP responses directly through the public invitation form.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Protect the integrity of original guest RSVP responses against admin tampering.
     */
    public function update(User $user, RsvpResponse $rsvpResponse): bool
    {
        return false;
    }

    public function delete(User $user, RsvpResponse $rsvpResponse): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
