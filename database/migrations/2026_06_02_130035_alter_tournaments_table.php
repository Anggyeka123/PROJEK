<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            // Drop columns yang tidak perlu
            $table->dropColumn(['description', 'status', 'max_teams', 'start_date', 'end_date']);
            
            // Tambah column baru
            $table->dateTime('match_date')->nullable();
            $table->string('division')->nullable();
            $table->string('venue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            // Restore columns lama
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'finished'])->default('draft');
            $table->integer('max_teams')->default(16);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            
            // Drop columns baru
            $table->dropColumn(['match_date', 'division', 'venue']);
        });
    }
};
