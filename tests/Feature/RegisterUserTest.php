<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    use Tools;

    public string $url;

    public Organization $organization;

    public array $userToRegister;

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->url = $this->getEndPoint().'organizations/'.$this->organization->id.'/register';
        $this->userToRegister = [
            'first_name' => 'First name',
            'last_name' => 'last_name',
            'email' => fake()->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }

    public function test_the_registration_needs_data(): void
    {
        $response = $this->postJson($this->url);

        $response
            ->assertStatus(422)
            ->assertInvalid(['email', 'password', 'last_name']);
    }

    public function test_the_email_must_be_valid_to_register(): void
    {
        $this->userToRegister['email'] = 'not an email';

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['email']);
    }

    public function test_the_email_must_be_unique_to_register(): void
    {
        $existingUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $this->userToRegister['email'] = $existingUser->email;

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['email']);
    }

    public function test_the_last_name_is_required_to_register(): void
    {
        $this->userToRegister['last_name'] = null;

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['last_name']);
    }

    public function test_the_last_name_must_have_50_characters_max_to_register(): void
    {
        $this->userToRegister['last_name'] = fake()->sentence(50);

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['last_name']);
    }

    public function test_the_password_and_passwordconfirmation_must_be_the_same_to_register(): void
    {
        $this->userToRegister['password_confirmation'] = 'otherpassword';

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['password']);
    }

    public function test_the_password_must_have_8_characters_minimum_to_register(): void
    {
        $this->userToRegister['password'] = 'short';

        $response = $this->postJson($this->url, $this->userToRegister);

        $response
            ->assertStatus(422)
            ->assertInvalid(['password']);
    }

    public function test_the_user_can_be_registred_with_correct_body(): void
    {
        $response = $this->postJson($this->url, $this->userToRegister);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'organization_id' => $this->organization->id,
            'last_name' => strtoupper($this->userToRegister['last_name']),
            'first_name' => $this->userToRegister['first_name'],
            'status' => 'ACTIVE',

        ]);
    }

    public function test_impossible_to_register_more_users_than_the_max_authorized()
    {
        $maxUsers = config('params.max_users_per_organization');
        User::factory()->count($maxUsers)->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->postJson($this->url, $this->userToRegister);

        $response->assertStatus(422)->assertInvalid('max_users_per_organization');
    }
}
