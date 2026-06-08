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

$tournament = Tournament::whereHas('matches', function($q){ $q->where('stage_type','group'); })->first();
if (! $tournament) {
    echo "NO TOURNAMENT WITH GROUP MATCHES\n";
    exit(1);
}

echo "TOURNAMENT_ID={$tournament->id}\n";
$groupMatches = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','group')->orderBy('id')->get();
echo "GROUP MATCHES BEFORE\n";
foreach ($groupMatches as $m) {
    echo "{$m->id} | {$m->round_name} | home={$m->home_score} | away={$m->away_score} | status={$m->status}\n";
}

echo "\nUPDATING ALL GROUP MATCHES TO FULL_TIME\n";
$controller = new TournamentController();
foreach ($groupMatches as $m) {
    $request = Request::create('/update-match', 'POST', [
        'match_date' => Carbon::now()->toDateString(),
        'match_time' => Carbon::now()->format('H:i'),
        'match_status' => 'scheduled',
        'home_score' => 1,
        'away_score' => 0,
    ]);
    $controller->updateMatch($request, $tournament, $m);
    $m->refresh();
    echo "after {$m->id}: status={$m->status} home={$m->home_score} away={$m->away_score}\n";
}

echo "\nGROUP COMPLETION\n";
$groupCompleted = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','group')->where('status','full_time')->count();
$groupTotal = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','group')->count();
echo "group_completed={$groupCompleted} group_total={$groupTotal}\n";

$knockMatches = TournamentMatch::where('tournament_id',$tournament->id)->where('stage_type','knockout')->orderBy('id')->get();
echo "\nKNOCKOUT MATCHES AFTER\n";
if ($knockMatches->isEmpty()) {
    echo "NO KNOCKOUT MATCHES\n";
} else {
    foreach ($knockMatches as $m) {
        echo "{$m->id} | {$m->round_name} | home_id={$m->home_team_id} | away_id={$m->away_team_id} | home_key={$m->home_team_key} | away_key={$m->away_team_key} | source_home={$m->source_home} | source_away={$m->source_away}\n";
    }
}
