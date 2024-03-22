<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            $users = User::where('organization_id', $organization->id)->get();

            foreach ($users as $user) {
                Note::factory()->count(rand(1,10))->create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
