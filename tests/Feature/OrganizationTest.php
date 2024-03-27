<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_an_organization_can_be_displayed(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->getJson($this->getEndPoint().self::RESOURCE.'/'.$organization->id);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($organization->name, $data['name']);
        $this->assertEquals($organization->comment, $data['comment']);
        $this->assertEquals($organization->status, $data['status']);
    }

    public function test_an_organization_cannot_be_created_without_required_body(): void
    {
        $response = $this->postJson($this->getEndPoint().self::RESOURCE);

        $response
            ->assertStatus(422)
            ->assertInvalid(['name', 'status']);
    }

    public function test_an_organization_cannot_be_created_with_errors_on_the_body(): void
    {
        $dataToPost = [
            'name' => fake()->sentence(100),
            'comment' => fake()->sentence(100),
            'status' => 'WRONG',
        ];

        $response = $this->postJson($this->getEndPoint().self::RESOURCE, $dataToPost);

        $response
            ->assertStatus(422)
            ->assertInvalid(['name', 'comment', 'status']);
    }

    public function test_an_organization_can_be_created_with_correct_body()
    {
        $dataToPost = [
            'name' => fake()->word(),
            'comment' => fake()->word(),
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint().self::RESOURCE, $dataToPost);

        $response->assertStatus(201);

        $this->assertDatabaseHas('organizations', [
            'name' => $dataToPost['name'],
            'comment' => $dataToPost['comment'],
            'status' => $dataToPost['status'],
        ]);
    }

    public function test_an_organization_can_be_updated(): void
    {
        $organization = Organization::factory()->create();

        $dataToPut = [
            'name' => 'modified name',
            'comment' => 'modified comment',
            'status' => 'ACTIVE',
        ];

        $response = $this->putJson($this->getEndPoint().self::RESOURCE.'/'.$organization->id, $dataToPut);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($dataToPut['name'], $data['name']);
        $this->assertEquals($dataToPut['comment'], $data['comment']);
        $this->assertEquals($dataToPut['status'], $data['status']);
    }

    public function test_an_organization_can_be_deleted(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->deleteJson($this->getEndPoint().self::RESOURCE.'/'.$organization->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);
    }
}
