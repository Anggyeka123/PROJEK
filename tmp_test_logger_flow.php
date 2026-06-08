<?php
/**
 * TEST SKENARIO: Match 0-0 tanpa goal event
 * Flow: Live Match Logger → No Goal Events → End Match
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n========================================\n";
echo "TEST: Live Match Logger Flow - No Goal Events\n";
echo "========================================\n\n";

// Cari tournament dengan group stage
$tournament = Tournament::whereHas('matches', function ($q) {
    $q->where('stage_type', 'group');
})->first();

if (!$tournament) {
    echo "❌ NO GROUP TOURNAMENT FOUND\n";
    exit(1);
}

// Cari match dengan NULL scores di group stage
$match = TournamentMatch::where('tournament_id', $tournament->id)
    ->where('stage_type', 'group')
    ->whereNull('home_score')
    ->whereNull('away_score')
    ->where('status', 'scheduled')
    ->first();

if (!$match) {
    echo "❌ NO MATCH WITH NULL SCORES (scheduled) FOUND\n";
    exit(1);
}

echo "✓ Found test match:\n";
printf("  Match ID: %d\n", $match->id);
printf("  Tournament ID: %d\n", $tournament->id);
printf("  Status: %s\n", $match->status);
printf("  Home Score: %s\n", var_export($match->home_score, true));
printf("  Away Score: %s\n", var_export($match->away_score, true));

// Simulasi: Update match ke live_match
echo "\n--- STEP 1: Update status ke live_match ---\n";
$match->status = 'live_match';
$match->save();
$match->refresh();
printf("  ✓ Status: %s\n", $match->status);

// Simulasi: User tidak mencatat event goal apapun (hanya timeout/halftime events)
echo "\n--- STEP 2: Simulate NO goal events ---\n";
echo "  (No goal events recorded, simulating 0-0 match)\n";

// Simulasi: Check state BEFORE storeMatchEvent('full_time')
echo "\n--- STEP 3: Check match state BEFORE attempting full_time event ---\n";
printf("  home_score: %s\n", var_export($match->home_score, true));
printf("  away_score: %s\n", var_export($match->away_score, true));
printf("  status: %s\n", $match->status);

// Simulasi: User attempts to finalize via Event Logger (full_time event)
echo "\n--- STEP 4: Simulate storeMatchEvent('full_time') ---\n";
echo "  This should FAIL because scores are NULL\n";

$validated = [
    'event_type' => 'full_time',
    'team_side' => null,
    'player_name' => null,
    'description' => null,
    'minute' => null,
];

// Check validation dari storeMatchEvent line 1752
echo "\n  Validation check (line 1752):\n";
printf("    event_type === 'full_time': %s\n", $validated['event_type'] === 'full_time' ? 'true' : 'false');
printf("    home_score is null: %s\n", $match->home_score === null ? 'true' : 'false');
printf("    away_score is null: %s\n", $match->away_score === null ? 'true' : 'false');

if ($validated['event_type'] === 'full_time' && ($match->home_score === null || $match->away_score === null)) {
    echo "\n  ❌ BLOCKED: Scores are NULL - cannot finalize\n";
    echo "     Error message: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
} else {
    echo "\n  ✓ Would proceed to transaction\n";
}

// ALTERNATE: Try endMatch() directly
echo "\n--- STEP 5: Simulate endMatch() directly ---\n";
$match->refresh();
printf("  Current status: %s\n", $match->status);
printf("  Current home_score: %s\n", var_export($match->home_score, true));
printf("  Current away_score: %s\n", var_export($match->away_score, true));

// Check from endMatch() line 1851
echo "\n  Validation check (endMatch line 1851):\n";
printf("    home_score is null: %s\n", $match->home_score === null ? 'true' : 'false');
printf("    away_score is null: %s\n", $match->away_score === null ? 'true' : 'false');

if ($match->home_score === null || $match->away_score === null) {
    echo "\n  ❌ BLOCKED: Scores are NULL - cannot end match\n";
    echo "     Error message: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
} else {
    echo "\n  ✓ Would proceed to finalize\n";
}

// POTENTIAL ISSUE: What if scores somehow become 0 (not NULL)?
echo "\n--- STEP 6: Check buildStandingsGroups() with NULL scores ---\n";
echo "  In buildStandingsGroups (line 2176):\n";
echo "    \$homeScore = (int) \$match->home_score;\n";

$nullValue = null;
$castedValue = (int) $nullValue;
printf("    (int) null = %d\n", $castedValue);
printf("    Result: NULL scores would be treated as 0 in standings\n");

// Check if there's any path that bypasses the null-check
echo "\n--- STEP 7: Analyze potential bypass scenarios ---\n";

// Scenario: Direct DB update?
echo "\n  Scenario A: Direct DB update (status = full_time, scores = NULL)\n";
DB::beginTransaction();
try {
    TournamentMatch::where('id', $match->id)->update([
        'status' => 'full_time',
    ]);
    
    $matchAfterUpdate = TournamentMatch::find($match->id);
    printf("    ✓ Match updated to full_time\n");
    printf("    home_score: %s\n", var_export($matchAfterUpdate->home_score, true));
    printf("    away_score: %s\n", var_export($matchAfterUpdate->away_score, true));
    printf("    status: %s\n", $matchAfterUpdate->status);
    
    echo "\n    Question: What happens in buildStandingsGroups()?\n";
    printf("    (int) null = %d (treated as 0)\n", (int)$matchAfterUpdate->home_score);
    printf("    Result: Match recorded as 0-0 DRAW in standings!\n");
    
    DB::rollBack();
} catch (Exception $e) {
    DB::rollBack();
    echo "    Error: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "ANALYSIS COMPLETE\n";
echo "========================================\n";
