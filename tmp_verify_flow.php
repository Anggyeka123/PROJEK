<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

function printRow($row) {
    echo implode(' | ', array_map(function($v){ return $v === null ? 'NULL' : (string)$v; }, $row)) . "\n";
}

$tournament = Tournament::whereHas('matches', function($q){ $q->where('stage_type','group'); })->first();
if (! $tournament) {
    echo "NO TOURNAMENT WITH GROUP MATCHES\n";
    exit(1);
}

echo "TOURNAMENT_ID={$tournament->id}\n";
$groupMatches = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','group')->orderBy('id')->get();
echo "GROUP MATCHES BEFORE\n";
printRow(['id','round_name','home_score','away_score','status']);
foreach ($groupMatches as $m) {
    printRow([$m->id,$m->round_name,$m->home_score,$m->away_score,$m->status]);
}

echo "KNOCKOUT MATCHES BEFORE\n";
printRow(['id','round_name','home_team_id','away_team_id','home_team_key','away_team_key','source_home','source_away']);
$knockMatches = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','knockout')->orderBy('id')->get();
foreach ($knockMatches as $m) {
    printRow([$m->id,$m->round_name,$m->home_team_id,$m->away_team_id,$m->home_team_key,$m->away_team_key,$m->source_home,$m->source_away]);
}

echo "\n-- SIMULATE SCORE UPDATE ON FIRST GROUP MATCH --\n";
$firstGroup = $groupMatches->first();
if (! $firstGroup) {
    echo "NO GROUP MATCH\n";
    exit(1);
}
$controller = new TournamentController();
$request = Request::create('/update-match', 'POST', [
    'match_date' => Carbon::now()->toDateString(),
    'match_time' => Carbon::now()->format('H:i'),
    'match_status' => 'scheduled',
    'home_score' => 1,
    'away_score' => 0,
]);

$response = $controller->updateMatch($request, $tournament, $firstGroup);
echo "UPDATE RESPONSE: ";
printRow([$response->getStatusCode(), get_class($response)]);

$firstGroup->refresh();
echo "FIRST GROUP MATCH AFTER UPDATE\n";
printRow([$firstGroup->id,$firstGroup->round_name,$firstGroup->home_score,$firstGroup->away_score,$firstGroup->status]);

$groupCompleted = TournamentMatch::where('tournament_id',$tournament->id)
    ->where('stage_type','group')
    ->where('status','full_time')
    ->count();
$groupTotal = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','group')->count();
echo "group_completed={$groupCompleted} group_total={$groupTotal}\n";

$knockMatches = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','knockout')->orderBy('id')->get();
echo "KNOCKOUT MATCHES AFTER\n";
printRow(['id','round_name','home_team_id','away_team_id','home_team_key','away_team_key','source_home','source_away']);
foreach ($knockMatches as $m) {
    printRow([$m->id,$m->round_name,$m->home_team_id,$m->away_team_id,$m->home_team_key,$m->away_team_key,$m->source_home,$m->source_away]);
}
