<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\TournamentController;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Arr;

$tournament = Tournament::find(4);
if (! $tournament) {
    echo "NO TOURNAMENT 4\n";
    exit(1);
}

$reflector = new ReflectionClass(TournamentController::class);
$method = $reflector->getMethod('buildStandingsGroups');
$method->setAccessible(true);
$groups = $method->invoke(new TournamentController(), $tournament);

foreach ($groups as $groupLabel => $rows) {
    echo "GROUP {$groupLabel}\n";
    foreach ($rows as $row) {
        echo implode(' | ', [
            $row['team_id'],
            $row['name'],
            $row['group_label'],
            $row['played'],
            $row['wins'],
            $row['draws'],
            $row['losses'],
            $row['points'],
            $row['goal_difference'],
            $row['ranking'],
        ]) . "\n";
    }
}
