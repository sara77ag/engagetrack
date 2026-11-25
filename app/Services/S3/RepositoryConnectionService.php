<?php

namespace App\Services\S3;

use App\Adapters\GitHubClient;
use App\Exceptions\ConflictException;
use App\Models\MemberGitHubAccount;
use App\Models\TeamRepository;

class RepositoryConnectionService
{
    // https://github.com/owner/repo  (optionally .git or a trailing slash)
    public const URL_PATTERN = '#^https://github\.com/([A-Za-z0-9_.-]+)/([A-Za-z0-9_.-]+?)(?:\.git)?/?$#';

    public function __construct(private GitHubClient $github)
    {
    }

    public function connectedRepository(int $teamId): ?TeamRepository
    {
        return TeamRepository::where('team_id', $teamId)
            ->where('connection_status', TeamRepository::STATUS_CONNECTED)
            ->first();
    }

    public function githubAccount(int $teamId, int $userId): ?MemberGitHubAccount
    {
        return MemberGitHubAccount::where('team_id', $teamId)->where('user_id', $userId)->first();
    }

    // AC-S3-01.1 / 01.2
    public function link(int $teamId, int $userId, string $url): TeamRepository
    {
        if ($this->connectedRepository($teamId)) {
            throw new ConflictException('This team already has a connected repository');
        }

        preg_match(self::URL_PATTERN, $url, $parts);
        $details = $this->github->getRepository($parts[1], $parts[2]);

        if (TeamRepository::where('github_repo_id', $details['github_repo_id'])->exists()) {
            throw new ConflictException('This repository is already linked to another team');
        }

        return TeamRepository::create($details + [
            'team_id' => $teamId,
            'connected_by' => $userId,
            'connection_status' => TeamRepository::STATUS_CONNECTED,
            'connected_at' => now(),
        ]);
    }

    // AC-S3-01.4
    public function saveUsername(int $teamId, int $userId, string $login): MemberGitHubAccount
    {
        if (! $this->connectedRepository($teamId)) {
            throw new ConflictException('Link the team repository before saving your GitHub username');
        }

        $login = strtolower($login); // GitHub usernames are case-insensitive

        $takenByTeammate = MemberGitHubAccount::where('team_id', $teamId)
            ->where('github_login', $login)
            ->where('user_id', '!=', $userId)
            ->exists();

        if ($takenByTeammate) {
            throw new ConflictException('This GitHub username is already linked to another team member');
        }

        return MemberGitHubAccount::updateOrCreate(
            ['team_id' => $teamId, 'user_id' => $userId],
            ['github_login' => $login],
        );
    }
}
