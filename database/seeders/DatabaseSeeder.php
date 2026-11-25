<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 4 demo students (ids 1-4) that match FakeTeamMembershipService
        $this->call(DemoUsersSeeder::class);
    }
}
