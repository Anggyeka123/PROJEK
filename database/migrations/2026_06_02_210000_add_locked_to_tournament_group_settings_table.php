<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_group_settings', function (Blueprint $table) {
            $table->boolean('locked')->default(true)->after('group_count');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_group_settings', function (Blueprint $table) {
            $table->dropColumn('locked');
        });
    }
};
