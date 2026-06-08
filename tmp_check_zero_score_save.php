<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

$tournament = Tournament::whereHas('matches', function ($q) {
    $q->where('stage_type', 'group');
})->first();
if (! $tournament) {
    echo "NO GROUP TOURNAMENT FOUND\n";
    exit(1);
}

$match = TournamentMatch::where('tournament_id', $tournament->id)
    ->where('stage_type', 'group')
    ->whereNull('home_score')
    ->whereNull('away_score')
    ->whereIn('status', ['scheduled', 'live_match'])
    ->first();

if (! $match) {
    echo "NO MATCH FOUND WITH NULL SCORES IN GROUP STAGE\n";
    exit(1);
}

$controller = new TournamentController();

function printMatchState($label, TournamentMatch $m) {
    printf("%s\n", $label);
    printf("  id=%d status=%s home_score=%s away_score=%s\n",
        $m->id,
        $m->status,
        var_export($m->home_score, true),
        var_export($m->away_score, true)
    );
}

printMatchState('BEFORE UPDATE', $match);

DB::beginTransaction();
try {
    $request = Request::create('/tournaments/' . $tournament->id . '/matches/' . $match->id, 'PATCH', [
        'match_date' => $match->match_date ? $match->match_date->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
        'match_time' => $match->match_date ? $match->match_date->format('H:i') : Carbon::now()->format('H:i'),
        'match_status' => 'full_time',
        'home_score' => '0',
        'away_score' => '0',
    ]);

    echo "\n--- CALLING updateMatch() ---\n";
    $response = $controller->updateMatch($request, $tournament, $match);
    echo "updateMatch returned class: " . get_class($response) . "\n";

    printMatchState('AFTER updateMatch (before refresh)', $match);
    $match->refresh();
    printMatchState('AFTER refresh', $match);

    // Now show DB read again
    $matchDb = TournamentMatch::find($match->id);
    printMatchState('AFTER refresh (reloaded from DB)', $matchDb);

    echo "\n--- PREPARING endMatch() CHECK ---\n";
    printMatchState('BEFORE endMatch()', $match);

    // If the match got status full_time, endMatch short-circuits before null-check.
    if ($match->status === 'full_time') {
        echo "NOTE: match status is full_time, so endMatch() would return early and skip the null-check.\n";
    } else {
        echo "NOTE: match status is not full_time, so endMatch() would evaluate the null-check.\n";
    }

    DB::rollBack();
    echo "\nTransaction rolled back; no permanent DB change.\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "EXCEPTION: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
