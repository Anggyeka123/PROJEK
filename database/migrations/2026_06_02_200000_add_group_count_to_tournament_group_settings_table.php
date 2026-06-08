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
        Schema::table('tournament_group_settings', function (Blueprint $table) {
            $table->integer('group_count')->default(4)->after('teams_per_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_group_settings', function (Blueprint $table) {
            $table->dropColumn('group_count');
        });
    }
};
