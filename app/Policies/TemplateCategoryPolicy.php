<?php

namespace App\Policies;

use App\Models\TemplateCategory;
use App\Models\User;

class TemplateCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, TemplateCategory $templateCategory): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TemplateCategory $templateCategory): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TemplateCategory $templateCategory): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
