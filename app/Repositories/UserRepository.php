<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function getUsers(Organization $organization, array $filters): Paginator
    {
        $query = User::where('organization_id', $organization->id)->orderBy('last_name');

        $query->when(isset($filters['email']), function ($q) use ($filters) {
            return $q->where('email', 'like', '%'.$filters['email'].'%');
        });

        $query->when(isset($filters['last_name']), function ($q) use ($filters) {
            return $q->where('last_name', $filters['last_name']);
        });

        return $query->paginate();
    }

    public function insert(Organization $organization, array $data, string $roleId, string $status): User
    {
        $user = new User();
        $user->organization_id = $organization->id;
        $user->fill($data);
        $user->role_id = $roleId;
        $user->status = $status;
        $user->password = Hash::make($data['password']);
        $user->save();

        return $user;
    }

    public function update(Note $note, array $data): Note
    {
        $note->fill($data);
        $note->save();

        return $note;
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }
}
