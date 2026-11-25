<?php

namespace App\Providers;

use App\Adapters\GitHubApiAdapter;
use App\Adapters\GitHubClient;
use App\Contracts\TeamMembershipService;
use App\Policies\TeamActivityPolicy;
use App\Stubs\FakeTeamMembershipService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GitHubClient::class, GitHubApiAdapter::class);

        // When S5 is ready, change ONLY this line to bind S5's real class.
        $this->app->bind(TeamMembershipService::class, FakeTeamMembershipService::class);
    }

    public function boot(): void
    {
        Gate::define('s3-member', [TeamActivityPolicy::class, 'member']);
    }
}
