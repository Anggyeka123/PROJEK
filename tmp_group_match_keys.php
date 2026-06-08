<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

$matches = TournamentMatch::where('tournament_id', 4)->where('stage_type','group')->orderBy('id')->get();
foreach ($matches as $m) {
    echo implode(' | ', [
        $m->id,
        $m->round_name,
        $m->home_team_id === null ? 'NULL' : $m->home_team_id,
        $m->away_team_id === null ? 'NULL' : $m->away_team_id,
        $m->home_team_key ?? 'NULL',
        $m->away_team_key ?? 'NULL',
        $m->source_home ?? 'NULL',
        $m->source_away ?? 'NULL',
        $m->home_score === null ? 'NULL' : $m->home_score,
        $m->away_score === null ? 'NULL' : $m->away_score,
        $m->status ?? 'NULL'
    ]) . "\n";
}
