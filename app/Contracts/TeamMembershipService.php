<?php

namespace App\Contracts;

// The contract agreed with Hana (S5), see section 3.5 of the Initial Assessment.
interface TeamMembershipService
{
    /**
     * @return array{status: string, supervisorId: int|null}  status: approved | pending | none
     */
    public function getMembership(int $userId, int $teamId): array;
}
