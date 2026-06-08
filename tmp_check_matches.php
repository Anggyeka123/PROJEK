<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\TournamentMatch;

$count = TournamentMatch::whereNull('home_score')
    ->whereNull('away_score')
    ->where('status', 'scheduled')
    ->where('stage_type', 'group')
    ->count();
echo "Matches with NULL scores and scheduled status: $count\n";

$all = TournamentMatch::where('stage_type', 'group')
    ->whereIn('status', ['scheduled', 'live_match'])
    ->limit(3)
    ->get();
    
foreach ($all as $m) {
    echo "ID: {$m->id}, Status: {$m->status}, Home: " . var_export($m->home_score, true) . ", Away: " . var_export($m->away_score, true) . "\n";
}
