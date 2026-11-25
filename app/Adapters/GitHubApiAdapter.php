<?php

namespace App\Adapters;

use App\Exceptions\RepositoryNotFoundException;
use Illuminate\Support\Facades\Http;

// Adapter pattern: the rest of the app never sees GitHub's JSON shape.
class GitHubApiAdapter implements GitHubClient
{
    public function getRepository(string $owner, string $repo): array
    {
        $token = config('services.github.token');

        $response = Http::acceptJson()
            ->timeout(10)
            ->when($token, fn ($request) => $request->withToken($token))
            ->get("https://api.github.com/repos/{$owner}/{$repo}");

        // GitHub answers 404 for missing AND for private repos we can't see.
        if ($response->status() === 404 || $response->json('private') === true) {
            throw new RepositoryNotFoundException();
        }

        // Any other failure (429, 500...) throws here; Sprint 2 handles those properly.
        $response->throw();

        return [
            'github_repo_id' => $response->json('id'),
            'repository_name' => $response->json('name'),
            'repository_url' => $response->json('html_url'),
            'description' => $response->json('description'),
            'default_branch' => $response->json('default_branch'),
        ];
    }
}
