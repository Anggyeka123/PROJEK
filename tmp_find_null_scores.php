<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

$m = TournamentMatch::whereNull('home_score')->orWhereNull('away_score')->first();
if (! $m) {
    echo "NO_NULL_MATCH\n";
    exit(0);
}

echo "MATCH_ID=" . $m->id . PHP_EOL;
echo "ROUND=" . ($m->round_name ?? '') . PHP_EOL;
echo "HOME_SCORE=" . (is_null($m->home_score) ? 'NULL' : $m->home_score) . PHP_EOL;
echo "AWAY_SCORE=" . (is_null($m->away_score) ? 'NULL' : $m->away_score) . PHP_EOL;
echo "STATUS=" . ($m->status ?? '') . PHP_EOL;
echo "HOME_TEAM_ID=" . (is_null($m->home_team_id) ? 'NULL' : $m->home_team_id) . PHP_EOL;
echo "AWAY_TEAM_ID=" . (is_null($m->away_team_id) ? 'NULL' : $m->away_team_id) . PHP_EOL;
