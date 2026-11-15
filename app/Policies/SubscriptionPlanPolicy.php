<?php

namespace App\Policies;

use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionPlanPolicy
{
    /**
     * Monetization plans (F11) remain disabled throughout Release 1.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return false;
    }

    public function delete(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
