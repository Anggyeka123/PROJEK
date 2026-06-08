<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('manager_token')->nullable();
            $table->string('registration_status')->default('registered');
            $table->string('group_label')->nullable();
            $table->unsignedSmallInteger('seed')->nullable();
            $table->string('bracket_position')->nullable();
            $table->boolean('is_promoted')->default(false);
            $table->boolean('is_relegated')->default(false);
            $table->timestamps();

            $table->unique(['tournament_id', 'team_id']);
            $table->unique(['tournament_id', 'manager_token']);
            $table->index(['tournament_id', 'group_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_teams');
    }
};
