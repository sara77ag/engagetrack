<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_repositories', function (Blueprint $table) {
            $table->id();
            // S5 owns teams, so we store only the id (no foreign key yet)
            $table->unsignedBigInteger('team_id')->index();
            // GitHub's own id for the repo; unique = same repo can't be linked twice
            $table->unsignedBigInteger('github_repo_id')->unique();
            $table->string('repository_name');
            $table->string('repository_url');
            $table->text('description')->nullable();
            $table->string('default_branch')->nullable();
            $table->foreignId('connected_by')->constrained('users');
            $table->string('connection_status')->default('Connected');
            $table->timestamp('connected_at');
            // null = "Not yet synced" (sync is built in Sprint 2)
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_repositories');
    }
};
