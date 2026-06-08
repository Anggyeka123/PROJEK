<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_team_id')->constrained()->cascadeOnDelete();
            $table->string('player_name');
            $table->unsignedSmallInteger('shirt_number')->nullable();
            $table->json('positions')->nullable();
            $table->enum('dominant_position', ['GK', 'Anchor', 'Flank', 'Pivot'])->nullable();
            $table->string('phone')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->string('status')->default('active');
            $table->dateTime('registered_at')->nullable();
            $table->timestamps();

            $table->index(['tournament_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_team_players');
    }
};
