<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;
    use Tools;

    const RESOURCE = 'organizations';

    public function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role_id' => 'SUPERADMIN']));
    }

    public function test_the_organizations_can_be_displayed(): void
    {
        Organization::factory()->count(21)->create();

        $response = $this->getJson($this->getEndPoint().self::RESOURCE);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        
        $this->assertEquals(15, count($data));
    }

    public function test_the_organizations_can_be_displayed_with_filter_on_name(): void
    {
        Organization::factory()->count(21)->create();
        Organization::factory()->create(['name' => 'toto']);

        $response = $this->getJson($this->getEndPoint().self::RESOURCE.'?name=toto');

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        
        $this->assertEquals(1, count($data));
    }

    public function test_the_organizations_can_be_displayed_with_filter_on_status(): void
    {
        Organization::factory()->count(5)->create(['status' => 'ACTIVE']);
        Organization::factory()->count(1)->create(['status' => 'INACTIVE']);

        $response = $this->getJson($this->getEndPoint().self::RESOURCE.'?status=ACTIVE');

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        
        $this->assertEquals(5, count($data));

        $response = $this->getJson($this->getEndPoint().self::RESOURCE.'?status=INACTIVE');

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        
        $this->assertEquals(1, count($data));
    }

    public function test_the_organization_cannot_be_created_without_required_body(): void
    {
        $response = $this->postJson($this->getEndPoint().self::RESOURCE);

        $response
        ->assertStatus(422)
        ->assertInvalid(['name', 'status']);
    }
}
