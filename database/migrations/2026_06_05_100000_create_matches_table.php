<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('bracket_match_id')->nullable();
            $table->unsignedInteger('next_bracket_match_id')->nullable();
            $table->unsignedBigInteger('next_match_id')->nullable();
            $table->string('stage_type');
            $table->string('playoff_type')->nullable();
            $table->string('group_label')->nullable();
            $table->string('round_name');
            $table->unsignedBigInteger('home_team_id')->nullable();
            $table->unsignedBigInteger('away_team_id')->nullable();
            $table->string('home_team_key')->nullable();
            $table->string('away_team_key')->nullable();
            $table->string('source_home')->nullable();
            $table->string('source_away')->nullable();
            $table->boolean('is_bye')->default(false);
            $table->boolean('is_third_place')->default(false);
            $table->dateTime('match_date')->nullable();
            $table->string('venue')->nullable();
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();

            $table->index(['tournament_id', 'stage_type']);
            $table->index(['group_label']);
            $table->index(['round_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
