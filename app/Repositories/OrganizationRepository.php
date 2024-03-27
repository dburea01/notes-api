<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Contracts\Pagination\Paginator;

class OrganizationRepository
{
    public function getOrganizations(array $filters): Paginator
    {
        $query = Organization::orderBy('name');

        $query->when(isset($filters['name']), function ($q) use ($filters) {
            return $q->where('name', 'like', '%'.$filters['name'].'%');
        });

        $query->when(isset($filters['status']), function ($q) use ($filters) {
            return $q->where('status', $filters['status']);
        });

        return $query->paginate();
    }

    public function insert(array $data): Organization
    {
        $organization = new Organization();
        $organization->fill($data);
        $organization->save();

        return $organization;
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->fill($data);
        $organization->save();

        return $organization;
    }

    public function delete(Organization $organization): void
    {
        $organization->delete();
    }
}
