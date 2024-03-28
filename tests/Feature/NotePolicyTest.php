<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotePolicyTest extends TestCase
{
    use RefreshDatabase;
    use Tools;

    const RESOURCE = 'notes';
    public Organization $organization;
    public User $userSuperAdmin,$userAdmin, $user;
    public string $url;
    public Collection $notes;

    public function setUp(): void
    {
        parent::setUp();

        $this->userSuperAdmin = User::factory()->create(['role_id' => 'SUPERADMIN']);
        $this->organization = Organization::factory()->create();
        $this->userAdmin = User::factory()->create(['role_id' => 'ADMIN', 'organization_id' => $this->organization->id]);
        $this->user = User::factory()->create(['role_id' => 'USER', 'organization_id' => $this->organization->id]);
        $this->notes = Note::factory()->count(21)->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->userSuperAdmin->id,
        ]);
        $this->url = $this->getEndPoint().'organizations/'.$this->organization->id.'/notes';
    }

    public function test_authentication_is_required_to_access_notes(): void
    {
        $this->getJson($this->url)->assertUnauthorized();
    }

    public function test_only_the_superadmin_can_list_the_notes_of_any_organization(): void
    {
        $this->actingAs($this->userSuperAdmin)->getJson($this->url)->assertOk();
        $this->actingAs($this->userAdmin)->getJson($this->url)->assertOk();
        $this->actingAs($this->user)->getJson($this->url)->assertOk();

        $anotherOrganization = Organization::factory()->create();
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes';

        $this->actingAs($this->userSuperAdmin)->getJson($anotherUrl)->assertOk();
        $this->actingAs($this->userAdmin)->getJson($anotherUrl)->assertForbidden();
        $this->actingAs($this->user)->getJson($anotherUrl)->assertForbidden();
    }

    public function test_only_the_superadmin_can_create_a_note_for_any_organization(): void
    {
        $this->actingAs($this->userSuperAdmin)->postJson($this->url)->assertUnprocessable();
        $this->actingAs($this->userAdmin)->postJson($this->url)->assertUnprocessable();
        $this->actingAs($this->user)->postJson($this->url)->assertUnprocessable();

        $anotherOrganization = Organization::factory()->create();
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes';

        $this->actingAs($this->userSuperAdmin)->postJson($anotherUrl)->assertUnprocessable();
        $this->actingAs($this->userAdmin)->postJson($anotherUrl)->assertForbidden();
        $this->actingAs($this->user)->postJson($anotherUrl)->assertForbidden();
    }

    public function test_only_the_superadmin_can_update_a_note_for_any_organization(): void
    {
        $url = $this->url.'/'.$this->notes[0]->id;
        $this->actingAs($this->userSuperAdmin)->putJson($url)->assertOK();
        $this->actingAs($this->userAdmin)->putJson($url)->assertOk();
        $this->actingAs($this->user)->putJson($url)->assertOk();

        $anotherOrganization = Organization::factory()->create();
        $anotherUser = User::factory()->create([
            'role_id' => 'USER',
            'organization_id' => $anotherOrganization->id,
        ]);
        $anotherNote = Note::factory()->create([
            'organization_id' => $anotherOrganization->id,
            'user_id' => $anotherUser->id
        ]);
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes/'.$anotherNote->id;

        $this->actingAs($this->userSuperAdmin)->putJson($anotherUrl)->assertOk();
        $this->actingAs($this->userAdmin)->putJson($anotherUrl)->assertForbidden();
        $this->actingAs($this->user)->putJson($anotherUrl)->assertForbidden();
    }

    public function test_only_the_superadmin_can_view_a_note_of_any_organization(): void
    {
        $url = $this->url.'/'.$this->notes[0]->id;
        $this->actingAs($this->userSuperAdmin)->getJson($url)->assertOK();
        $this->actingAs($this->userAdmin)->getJson($url)->assertOk();
        $this->actingAs($this->user)->getJson($url)->assertOk();

        $anotherOrganization = Organization::factory()->create();
        $anotherUser = User::factory()->create([
            'role_id' => 'USER',
            'organization_id' => $anotherOrganization->id,
        ]);
        $anotherNote = Note::factory()->create([
            'organization_id' => $anotherOrganization->id,
            'user_id' => $anotherUser->id
        ]);
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes/'.$anotherNote->id;

        $this->actingAs($this->userSuperAdmin)->getJson($anotherUrl)->assertOk();
        $this->actingAs($this->userAdmin)->getJson($anotherUrl)->assertForbidden();
        $this->actingAs($this->user)->getJson($anotherUrl)->assertForbidden();
    }

    public function test_only_the_superadmin_can_delete_a_note_of_any_organization(): void
    {
        $url = $this->url.'/'.$this->notes[0]->id;
        $this->actingAs($this->userSuperAdmin)->deleteJson($url)->assertNoContent();

        $url = $this->url.'/'.$this->notes[1]->id;
        $this->actingAs($this->userAdmin)->deleteJson($url)->assertNoContent();

        $url = $this->url.'/'.$this->notes[2]->id;
        $this->actingAs($this->user)->deleteJson($url)->assertNoContent();

        $anotherOrganization = Organization::factory()->create();
        $anotherUser = User::factory()->create([
            'role_id' => 'USER',
            'organization_id' => $anotherOrganization->id,
        ]);
        $anotherNotes = Note::factory()->count(3)->create([
            'organization_id' => $anotherOrganization->id,
            'user_id' => $anotherUser->id
        ]);
        
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes/'.$anotherNotes[0]->id;
        $this->actingAs($this->userSuperAdmin)->deleteJson($anotherUrl)->assertNoContent();
        
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes/'.$anotherNotes[1]->id;
        $this->actingAs($this->userAdmin)->deleteJson($anotherUrl)->assertForbidden();
        
        $anotherUrl = $this->getEndPoint().'organizations/'.$anotherOrganization->id.'/notes/'.$anotherNotes[2]->id;
        $this->actingAs($this->user)->deleteJson($anotherUrl)->assertForbidden();
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
