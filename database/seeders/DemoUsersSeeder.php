<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Ids 1-4 must match FakeTeamMembershipService.
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Omar Ali', 'Sarah Alrashidi', 'Pending Student', 'Outsider Student'] as $i => $name) {
            User::create([
                'name' => $name,
                'email' => 'student'.($i + 1).'@example.com',
                'password' => 'password', // hashed automatically by the cast in User
            ]);
        }
    }
}
