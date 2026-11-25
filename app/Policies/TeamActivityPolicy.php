<?php

namespace App\Policies;

use App\Contracts\TeamMembershipService;
use App\Models\User;

// "Access Policy Service" in the C4 Level 3 diagram.
class TeamActivityPolicy
{
    // Laravel injects whichever class is bound to the interface (stub now, S5 later)
    public function __construct(private TeamMembershipService $memberships)
    {
    }

    public function member(User $user, int $teamId): bool
    {
        return $this->memberships->getMembership($user->id, $teamId)['status'] === 'approved';
    }
}
