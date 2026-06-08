<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Str;
use App\Models\Team;

Artisan::command('teams:fill-manager-tokens', function () {
    $this->comment('Searching teams with NULL manager_token...');

    $teams = Team::whereNull('manager_token')->get();
    $count = 0;
    $created = [];

    foreach ($teams as $team) {
        $prefix = strtoupper(Str::slug($team->name, '')) ?: 'TEAM';

        // try until unique
        do {
            $token = $prefix . '-' . random_int(1000, 9999);
        } while (Team::where('manager_token', $token)->exists());

        $team->update(['manager_token' => $token]);
        $created[] = $token;
        $count++;
    }

    $this->info("Completed. Teams fixed: {$count}");
    if ($count > 0) {
        $this->info('Tokens created:');
        foreach ($created as $t) {
            $this->line($t);
        }
    }
})->describe('Fill NULL manager_token for existing teams using NAME-XXXX format');
