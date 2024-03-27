<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->organization_id == $organization->id && $user->isAdmin();
    }

    public function create(User $user, Organization $organization): bool
    {
        return false;
    }

    public function update(User $user, Organization $organization): bool
    {
        return false;
    }

    public function delete(User $user, Organization $organization): bool
    {
        return false;
    }
}
