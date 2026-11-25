<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamRepository extends Model
{
    public const STATUS_CONNECTED = 'Connected';

    // Only these columns can be mass-assigned (SNFR-02: mass assignment protection)
    protected $fillable = [
        'team_id',
        'github_repo_id',
        'repository_name',
        'repository_url',
        'description',
        'default_branch',
        'connected_by',
        'connection_status',
        'connected_at',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'connected_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }
}
