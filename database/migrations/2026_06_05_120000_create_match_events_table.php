<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('event_type');
            $table->string('team_side')->nullable();
            $table->string('player_name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('minute')->default(0);
            $table->timestamps();

            $table->index(['match_id']);
            $table->index(['event_type']);
            $table->index(['minute']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_events');
    }
};
