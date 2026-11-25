<?php

namespace App\Http\Controllers\S3;

use App\Http\Controllers\Controller;
use App\Http\Requests\S3\LinkRepositoryRequest;
use App\Http\Requests\S3\SaveGitHubUsernameRequest;
use App\Services\S3\RepositoryConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Thin controller: authorise + validate (Form Requests), call the service, return a response.
class RepositoryController extends Controller
{
    public function __construct(private RepositoryConnectionService $service)
    {
    }

    public function show(int $teamId): View
    {
        Gate::authorize('s3-member', $teamId); // throws 403 if not approved

        $repository = $this->service->connectedRepository($teamId);

        return view('s3.repository', [
            'teamId' => $teamId,
            'repository' => $repository,
            'account' => $repository
                ? $this->service->githubAccount($teamId, auth()->id())
                : null,
        ]);
    }

    public function store(LinkRepositoryRequest $request, int $teamId): JsonResponse|RedirectResponse
    {
        $repository = $this->service->link($teamId, $request->user()->id, $request->validated('repository_url'));

        if ($request->expectsJson()) {
            return response()->json($repository, 201);
        }

        return redirect()->route('s3.repository.show', $teamId)->with('status', 'Repository linked.');
    }

    public function saveUsername(SaveGitHubUsernameRequest $request, int $teamId): JsonResponse|RedirectResponse
    {
        $account = $this->service->saveUsername($teamId, $request->user()->id, $request->validated('github_login'));

        if ($request->expectsJson()) {
            return response()->json($account, $account->wasRecentlyCreated ? 201 : 200);
        }

        return redirect()->route('s3.repository.show', $teamId)->with('status', 'GitHub username saved.');
    }
}
