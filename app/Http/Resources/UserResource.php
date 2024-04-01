<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $last_name
 * @property string $first_name
 * @property string $role_id
 * @property string $email
 * @property string $status
 * @property string $created_at
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** @var string The user id (uuid) */
            'id' => $this->id,
            /** @var string The organization id the user belongs to (uuid) */
            'organization_id' => $this->organization_id,

            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'role_id' => $this->role_id,
            'email' => $this->email,
            /** @var string The status of the user 'ACTIVE' or 'INACTIVE' */
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
