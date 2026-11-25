<?php

use App\Http\Controllers\S3\RepositoryController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ---- S3: repository linking (US-S3-01) ----
Route::middleware('auth')
    ->prefix('s3/teams/{teamId}')
    ->whereNumber('teamId')
    ->group(function () {
        Route::get('/repository', [RepositoryController::class, 'show'])->name('s3.repository.show');
        Route::post('/repository', [RepositoryController::class, 'store'])->name('s3.repository.store');
        Route::post('/github-username', [RepositoryController::class, 'saveUsername'])->name('s3.username.store');
    });

// ---- TEMPORARY dev login until S1 (Sundus) delivers real authentication ----
// Remove when S1 is merged. Only exists on your own machine (APP_ENV=local).
if (app()->environment('local')) {
    Route::get('/login', fn () => response(
        '<p>Dev login: <a href="/dev/login/1">Omar (approved)</a> | <a href="/dev/login/2">Sarah (approved)</a> | '
        .'<a href="/dev/login/3">Pending student</a> | <a href="/dev/login/4">Outsider</a></p>'
    ))->name('login');

    Route::get('/dev/login/{user}', function (User $user) {
        Auth::login($user);

        return redirect()->route('s3.repository.show', 1);
    });
}
