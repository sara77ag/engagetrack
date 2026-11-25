<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team repository - EngageTrack</title>
    <style>
        body { font-family: system-ui, sans-serif; line-height: 1.5; margin: 0; background: #f5f5f7; color: #1a1a1a; }
        main { max-width: 40rem; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border: 1px solid #c9c9d1; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }
        label { display: block; font-weight: 600; margin-bottom: .25rem; }
        input[type=text] { width: 100%; box-sizing: border-box; padding: .5rem; border: 2px solid #595959; border-radius: 4px; font-size: 1rem; }
        button { margin-top: .75rem; padding: .5rem 1rem; font-size: 1rem; background: #0b4fa8; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        :focus-visible { outline: 3px solid #ffbf47; outline-offset: 2px; }
        .alert { border-left: 6px solid #b00020; background: #fdecee; padding: .75rem 1rem; margin-bottom: 1rem; }
        .success { border-left-color: #1b6e2d; background: #e8f5ea; }
        dt { font-weight: 600; } dd { margin: 0 0 .5rem; }
    </style>
</head>
<body>
<main>
    <h1>Team repository</h1>

    @if (session('status'))
        <div class="alert success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert" role="alert">
            <strong>Something went wrong:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($repository)
        <section class="card" aria-labelledby="details-heading">
            <h2 id="details-heading">Repository details</h2>
            <dl>
                <dt>Name</dt>
                <dd><a href="{{ $repository->repository_url }}">{{ $repository->repository_name }}</a></dd>
                <dt>Description</dt>
                <dd>{{ $repository->description ?? 'No description' }}</dd>
                <dt>Status</dt>
                <dd>{{ $repository->connection_status }}</dd>
                <dt>Last synced at</dt>
                <dd>{{ $repository->last_synced_at?->format('d M Y, H:i') ?? 'Not yet synced' }}</dd>
            </dl>
        </section>

        <section class="card" aria-labelledby="username-heading">
            <h2 id="username-heading">Your GitHub username</h2>
            <form method="POST" action="{{ route('s3.username.store', $teamId) }}">
                @csrf
                <label for="github_login">GitHub username</label>
                <input type="text" id="github_login" name="github_login"
                       value="{{ old('github_login', $account?->github_login) }}" autocomplete="off" required>
                <button type="submit">Save</button>
            </form>
        </section>
    @else
        <section class="card" aria-labelledby="link-heading">
            <h2 id="link-heading">Link your team's repository</h2>
            <form method="POST" action="{{ route('s3.repository.store', $teamId) }}">
                @csrf
                <label for="repository_url">Public repository link</label>
                <input type="text" id="repository_url" name="repository_url"
                       value="{{ old('repository_url') }}" placeholder="https://github.com/owner/repo" required>
                <button type="submit">Link</button>
            </form>
        </section>
    @endif
</main>
</body>
</html>
