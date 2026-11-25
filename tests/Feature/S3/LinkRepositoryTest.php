<?php

namespace Tests\Feature\S3;

use App\Models\MemberGitHubAccount;
use App\Models\TeamRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// T-S3-01 (AC-S3-01.1 to 01.4). GitHub is faked with Http::fake() + a JSON fixture.
class LinkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private User $omar;      // id 1, approved in team 1
    private User $sarah;     // id 2, approved in team 1
    private User $pending;   // id 3, pending in team 1
    private User $outsider;  // id 4, not in the team

    protected function setUp(): void
    {
        parent::setUp();

        // Created in this order so ids match FakeTeamMembershipService.
        [$this->omar, $this->sarah, $this->pending, $this->outsider] = User::factory()->count(4)->create();
    }

    private function fakeGitHub(int $status = 200, array $override = []): void
    {
        $repo = json_decode(file_get_contents(base_path('tests/Fixtures/github/repository.json')), true);

        Http::fake(['api.github.com/repos/owner/repo' => Http::response($override + $repo, $status)]);
    }

    private function link(User $user)
    {
        return $this->actingAs($user)->postJson(route('s3.repository.store', 1), [
            'repository_url' => 'https://github.com/owner/repo',
        ]);
    }

    public function test_approved_member_links_public_repository(): void // AC-S3-01.1
    {
        $this->fakeGitHub();

        $this->link($this->omar)
            ->assertCreated()
            ->assertJsonPath('connection_status', 'Connected')
            ->assertJsonPath('repository_name', 'repo');

        $this->assertDatabaseHas('team_repositories', ['team_id' => 1, 'github_repo_id' => 123456]);
    }

    public function test_missing_repository_returns_404_and_saves_nothing(): void // AC-S3-01.2
    {
        $this->fakeGitHub(404, ['message' => 'Not Found']);

        $this->link($this->omar)
            ->assertNotFound()
            ->assertJsonPath('message', 'Repository not found or not public');

        $this->assertDatabaseCount('team_repositories', 0);
    }

    public function test_private_repository_returns_404_and_saves_nothing(): void // AC-S3-01.2
    {
        $this->fakeGitHub(200, ['private' => true]);

        $this->link($this->omar)->assertNotFound();

        $this->assertDatabaseCount('team_repositories', 0);
    }

    public function test_pending_member_and_outsider_are_refused(): void // AC-S3-01.3
    {
        $this->fakeGitHub();

        $this->link($this->pending)->assertForbidden();
        $this->link($this->outsider)->assertForbidden();

        $this->assertDatabaseCount('team_repositories', 0);
    }

    public function test_invalid_url_is_rejected_by_validation(): void
    {
        $this->actingAs($this->omar)
            ->postJson(route('s3.repository.store', 1), ['repository_url' => 'https://evil.com/owner/repo'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('repository_url');
    }

    public function test_second_repository_for_same_team_returns_409(): void
    {
        $this->fakeGitHub();
        $this->link($this->omar)->assertCreated();

        $this->link($this->sarah)->assertStatus(409);

        $this->assertDatabaseCount('team_repositories', 1);
    }

    public function test_member_saves_github_username_and_teammate_cannot_reuse_it(): void // AC-S3-01.4
    {
        $this->fakeGitHub();
        $this->link($this->omar)->assertCreated();

        $url = route('s3.username.store', 1);

        $this->actingAs($this->omar)->postJson($url, ['github_login' => 'omar-ali'])->assertCreated();
        $this->assertDatabaseHas('member_git_hub_accounts', ['user_id' => $this->omar->id, 'github_login' => 'omar-ali']);

        $this->actingAs($this->sarah)->postJson($url, ['github_login' => 'Omar-Ali'])
            ->assertStatus(409)
            ->assertJsonPath('message', 'This GitHub username is already linked to another team member');

        $this->assertSame(1, MemberGitHubAccount::count());
    }

    public function test_github_token_never_appears_in_a_response(): void // C-S3-02 (part of T-S3-06)
    {
        config(['services.github.token' => 'super-secret-token']);
        $this->fakeGitHub();

        $this->link($this->omar)->assertDontSee('super-secret-token');
        $this->actingAs($this->omar)->get(route('s3.repository.show', 1))->assertDontSee('super-secret-token');
        $this->assertSame(1, TeamRepository::count());
    }
}
