<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_team_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_team_id')->constrained()->cascadeOnDelete();
            $table->string('official_name');
            $table->string('role')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tournament_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_team_officials');
    }
};
