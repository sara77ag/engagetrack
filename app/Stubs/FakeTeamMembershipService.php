<?php

namespace App\Stubs;

use App\Contracts\TeamMembershipService;

// Stand-in for S5 until Hana's slice is ready. Lives in /app (not /tests)
// so the slice still works for marking even if S5 is late.
class FakeTeamMembershipService implements TeamMembershipService
{
    // team id => [user id => status]. Matches DemoUsersSeeder (users 1-4).
    private const MEMBERSHIPS = [
        1 => [1 => 'approved', 2 => 'approved', 3 => 'pending'],
    ];

    public function getMembership(int $userId, int $teamId): array
    {
        return [
            'status' => self::MEMBERSHIPS[$teamId][$userId] ?? 'none',
            'supervisorId' => null,
        ];
    }
}
