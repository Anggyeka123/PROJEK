<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Http\Controllers\TournamentController;

$tournament = Tournament::find(4);
if (! $tournament) {
    echo "NO TOURNAMENT 4\n";
    exit(1);
}

$match = TournamentMatch::where('tournament_id',4)->where('stage_type','group')->where('status','full_time')->first();
if (! $match) {
    echo "NO FULL_TIME GROUP MATCH\n";
    exit(1);
}

echo "CALLING FINALIZE ON MATCH {$match->id}\n";
$reflector = new ReflectionClass(TournamentController::class);
$method = $reflector->getMethod('finalizeMatchResult');
$method->setAccessible(true);
$method->invoke(new TournamentController(), $match);

$knockMatches = TournamentMatch::where('tournament_id',4)->where('stage_type','knockout')->orderBy('id')->get();
foreach ($knockMatches as $m) {
    echo implode(' | ', [
        $m->id,
        $m->round_name,
        $m->home_team_id === null ? 'NULL' : $m->home_team_id,
        $m->away_team_id === null ? 'NULL' : $m->away_team_id,
        $m->home_team_key ?? 'NULL',
        $m->away_team_key ?? 'NULL',
        $m->source_home ?? 'NULL',
        $m->source_away ?? 'NULL',
    ]) . "\n";
}
