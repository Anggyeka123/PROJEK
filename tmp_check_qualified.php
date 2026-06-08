<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\TournamentController;
use App\Models\Tournament;

$tournament = Tournament::find(4);
if (! $tournament) {
    echo "NO TOURNAMENT 4\n";
    exit(1);
}

$reflector = new ReflectionClass(TournamentController::class);
$method = $reflector->getMethod('getQualifiedTeams');
$method->setAccessible(true);
$qualified = $method->invoke(new TournamentController(), $tournament);

echo "QUALIFIED TEAMS (position => tournament_team_id)\n";
foreach ($qualified as $position => $teamId) {
    echo "$position => $teamId\n";
}
