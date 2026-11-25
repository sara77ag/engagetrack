<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberGitHubAccount extends Model
{
    protected $fillable = ['team_id', 'user_id', 'github_login'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
