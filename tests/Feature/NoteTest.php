<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;
    use Tools;

    const RESOURCE = 'notes';

    public Organization $organization;

    public User $userSuperAdmin;

    public string $url;

    public Collection $notes;

    public function setUp(): void
    {
        parent::setUp();

        $this->userSuperAdmin = User::factory()->create(['role_id' => 'SUPERADMIN']);
        $this->actingAs($this->userSuperAdmin);
        $this->organization = Organization::factory()->create();
        $this->notes = Note::factory()->count(21)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->userSuperAdmin->id,
        ]);
        $this->url = $this->getEndPoint().'organizations/'.$this->organization->id.'/notes';
    }

    public function test_the_notes_of_an_organization_can_be_displayed(): void
    {
        $response = $this->getJson($this->url);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(15, count($data));
    }

    public function test_the_notes_can_be_displayed_with_filter_on_note(): void
    {
        Note::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->userSuperAdmin->id,
            'note' => 'toto',
        ]);

        $response = $this->getJson($this->url.'?note=toto');

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(1, count($data));
    }

    public function test_a_note_can_be_displayed(): void
    {
        $response = $this->getJson($this->url.'/'.$this->notes[0]->id);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($this->notes[0]->note, $data['note']);
    }

    public function test_a_note_cannot_be_created_without_required_body(): void
    {
        $response = $this->postJson($this->url);

        $response
            ->assertStatus(422)
            ->assertInvalid(['note']);
    }

    public function test_a_note_cannot_be_created_with_errors_on_the_body(): void
    {
        $noteToPost = [
            'note' => fake()->sentence(1000),
            'background_color' => fake()->sentence(100),
        ];

        $response = $this->postJson($this->url, $noteToPost);

        $response
            ->assertStatus(422)
            ->assertInvalid(['note', 'background_color']);
    }

    public function test_a_note_can_be_created_with_correct_body()
    {
        $noteToPost = [
            'note' => fake()->word(),
            'background_color' => fake()->hexColor(),
        ];

        $response = $this->postJson($this->url, $noteToPost);

        $response->assertStatus(201);

        $this->assertDatabaseHas('notes', [
            'note' => $noteToPost['note'],
            'background_color' => $noteToPost['background_color'],
            'organization_id' => $this->organization->id,
            'user_id' => $this->userSuperAdmin->id,
        ]);
    }

    public function test_impossible_to_create_more_notes_than_the_max_authorized()
    {
        $maxNotes = config('params.max_notes_per_organization');

        Note::where('organization_id', $this->organization->id)->delete();

        Note::factory()->count($maxNotes)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->userSuperAdmin->id,
        ]);

        $noteToPost = [
            'note' => fake()->word(),
            'background_color' => fake()->hexColor(),
        ];

        $response = $this->postJson($this->url, $noteToPost);

        $response->assertStatus(422)->assertInvalid('max_notes_per_organization');
    }

    public function test_a_note_can_be_updated(): void
    {
        $noteToPut = [
            'note' => 'modified note',
            'background_color' => fake()->hexColor(),
        ];

        $response = $this->putJson($this->url.'/'.$this->notes[0]->id, $noteToPut);

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($noteToPut['note'], $data['note']);
        $this->assertEquals($noteToPut['background_color'], $data['background_color']);
    }

    public function test_a_note_can_be_deleted(): void
    {
        $response = $this->deleteJson($this->url.'/'.$this->notes[0]->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('notes', [
            'id' => $this->notes[0]->id,
        ]);
    }
}
