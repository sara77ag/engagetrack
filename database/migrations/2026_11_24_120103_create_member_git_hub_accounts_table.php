<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_git_hub_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('user_id')->constrained();
            $table->string('github_login');
            $table->timestamps();

            // one username per member per team
            $table->unique(['team_id', 'user_id']);
            // the same username can't belong to two members of one team (AC-S3-01.4, 409)
            $table->unique(['team_id', 'github_login']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_git_hub_accounts');
    }
};
