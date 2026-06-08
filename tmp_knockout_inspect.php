<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

$groupTotal = TournamentMatch::where('stage_type', 'group')->count();
$completedGroup = TournamentMatch::where('stage_type', 'group')->where('status', 'full_time')->count();
$knockoutCount = TournamentMatch::where('stage_type', 'knockout')->count();

echo "group_total={$groupTotal} group_completed={$completedGroup} knockout_total={$knockoutCount}\n";

$rows = TournamentMatch::where('stage_type', 'knockout')
    ->get(['id','round_name','home_team_id','away_team_id','home_team_key','away_team_key','source_home','source_away']);

foreach ($rows as $row) {
    echo implode(' | ', [
        $row->id,
        $row->round_name,
        isset($row->home_team_id) ? $row->home_team_id : 'NULL',
        isset($row->away_team_id) ? $row->away_team_id : 'NULL',
        isset($row->home_team_key) ? $row->home_team_key : 'NULL',
        isset($row->away_team_key) ? $row->away_team_key : 'NULL',
        isset($row->source_home) ? $row->source_home : 'NULL',
        isset($row->source_away) ? $row->source_away : 'NULL',
    ]) . "\n";
}
