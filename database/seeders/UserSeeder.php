<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create 1 super admin
        User::factory()->create([
            'role_id' => 'SUPERADMIN',
            'status' => 'ACTIVE',
        ]);

        $organizations = Organization::all();

        foreach ($organizations as $organization) {

            // 1 admin
            User::factory()->create([
                'role_id' => 'ADMIN',
                'organization_id' => $organization->id,
            ]);

            // n users
            User::factory()->count(rand(1, 10))->create([
                'role_id' => 'USER',
                'organization_id' => $organization->id,
            ]);
        }
    }
}
