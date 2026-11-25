<?php

namespace App\Adapters;

interface GitHubClient
{
    /**
     * @return array{github_repo_id: int, repository_name: string, repository_url: string,
     *               description: string|null, default_branch: string|null}
     *
     * @throws \App\Exceptions\RepositoryNotFoundException when missing or private
     */
    public function getRepository(string $owner, string $repo): array;
}
