<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;

class NotePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user, Organization $organization): bool
    {
        return $user->organization_id == $organization->id;
    }

    public function view(User $user, Note $note, Organization $organization): bool
    {
        return $user->organization_id == $organization->id && $note->organization_id == $organization->id;
    }

    public function create(User $user, Organization $organization): bool
    {
        return $user->organization_id == $organization->id;
    }

    public function update(User $user, Note $note, Organization $organization): bool
    {
        return $user->organization_id == $organization->id && $note->organization_id == $organization->id;
    }

    public function delete(User $user, Note $note, Organization $organization): bool
    {
        return $user->organization_id == $organization->id && $note->organization_id == $organization->id;
    }
}
