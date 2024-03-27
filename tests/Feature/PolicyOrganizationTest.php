<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyOrganizationTest extends TestCase
{
    use RefreshDatabase;
    use Tools;

    const RESOURCE = 'organizations';

    public function test_authentication_is_required_to_access_organizations(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE;
        $this->getJson($url)->assertUnauthorized();
    }

    public function test_only_the_superadmin_can_list_the_organizations(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE;

        $superAdmin = $this->createUser('SUPERADMIN', null);
        $admin = $this->createUser('ADMIN', $organization);
        $user = $this->createUser('USER', $organization);

        $this->actingAs($superAdmin)->getJson($url)->assertOk();
        $this->actingAs($admin)->getJson($url)->assertForbidden();
        $this->actingAs($user)->getJson($url)->assertForbidden();
    }

    public function test_only_the_superadmin_can_create_an_organization(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE;

        $superAdmin = $this->createUser('SUPERADMIN', null);
        $admin = $this->createUser('ADMIN', $organization);
        $user = $this->createUser('USER', $organization);

        $this->actingAs($superAdmin)->postJson($url)->assertUnprocessable();
        $this->actingAs($admin)->postJson($url)->assertForbidden();
        $this->actingAs($user)->postJson($url)->assertForbidden();
    }

    public function test_only_the_superadmin_can_update_an_organization(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE.'/'.$organization->id;

        $superAdmin = $this->createUser('SUPERADMIN', null);
        $admin = $this->createUser('ADMIN', $organization);
        $user = $this->createUser('USER', $organization);

        $this->actingAs($superAdmin)->putJson($url)->assertUnprocessable();
        $this->actingAs($admin)->putJson($url)->assertForbidden();
        $this->actingAs($user)->putJson($url)->assertForbidden();
    }

    public function test_only_the_superadmin_and_the_admin_can_view_an_organization(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE.'/'.$organization->id;

        $superAdmin = $this->createUser('SUPERADMIN', null);
        $admin = $this->createUser('ADMIN', $organization);
        $user = $this->createUser('USER', $organization);

        $this->actingAs($superAdmin)->getJson($url)->assertOK();
        $this->actingAs($admin)->getJson($url)->assertOK();
        $this->actingAs($user)->getJson($url)->assertForbidden();
    }

    public function test_only_the_superadmin_can_delete_an_organization(): void
    {
        $organization = Organization::factory()->create();
        $url = $this->getEndPoint().self::RESOURCE.'/'.$organization->id;

        $superAdmin = $this->createUser('SUPERADMIN', null);
        $admin = $this->createUser('ADMIN', $organization);
        $user = $this->createUser('USER', $organization);

        $this->actingAs($admin)->deleteJson($url)->assertForbidden();
        $this->actingAs($user)->deleteJson($url)->assertForbidden();
        $this->actingAs($superAdmin)->deleteJson($url)->assertNoContent();
    }

    public function createUser(string $roleId, ?Organization $organization): User
    {
        return User::factory()->create([
            'role_id' => 'SUPERADMIN',
            'organization_id' => $organization ? $organization->id : null,
            'role_id' => $roleId,
        ]);
    }
}
