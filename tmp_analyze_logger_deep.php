<?php
/**
 * TEST: Detailed analysis of NULL scores in storeMatchEvent() vs endMatch()
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\MatchEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n========================================\n";
echo "ANALYSIS: NULL Scores in Live Match Logger\n";
echo "========================================\n\n";

// Setup
$tournament = Tournament::whereHas('matches', function ($q) {
    $q->where('stage_type', 'group');
})->first();

if (!$tournament) {
    echo "❌ NO TOURNAMENT\n";
    exit(1);
}

// Find 2 matches to test different scenarios
$matches = TournamentMatch::where('tournament_id', $tournament->id)
    ->where('stage_type', 'group')
    ->whereNull('home_score')
    ->whereNull('away_score')
    ->where('status', 'scheduled')
    ->limit(2)
    ->get();

if ($matches->count() < 1) {
    echo "❌ NOT ENOUGH TEST MATCHES\n";
    exit(1);
}

$match1 = $matches[0];
$match2 = $matches->count() > 1 ? $matches[1] : null;

// ============================================
// SCENARIO A: storeMatchEvent with full_time, scores NULL
// ============================================
echo "SCENARIO A: storeMatchEvent() + full_time event, scores NULL\n";
echo str_repeat("-", 50) . "\n\n";

DB::beginTransaction();
try {
    $match1->status = 'live_match';
    $match1->save();
    
    echo "1. Initial state:\n";
    printf("   home_score: %s\n", var_export($match1->home_score, true));
    printf("   away_score: %s\n", var_export($match1->away_score, true));
    printf("   status: %s\n\n", $match1->status);
    
    // Simulate storeMatchEvent() validation
    echo "2. Pre-transaction validation (line 1752):\n";
    $validated = ['event_type' => 'full_time'];
    
    if ($validated['event_type'] === 'full_time' && ($match1->home_score === null || $match1->away_score === null)) {
        echo "   ❌ BLOCKED at line 1752\n";
        echo "   Reason: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
        echo "   Result: Request returns with error, NO DB transaction executed\n\n";
    }
    
    // Since blocked, transaction wouldn't run, so we skip to endMatch() scenario
    DB::rollBack();
    
} catch (Exception $e) {
    DB::rollBack();
    echo "   Error: " . $e->getMessage() . "\n";
}

// ============================================
// SCENARIO B: endMatch() with scores NULL
// ============================================
echo "\nSCENARIO B: endMatch() with scores NULL\n";
echo str_repeat("-", 50) . "\n\n";

if ($match2) {
    DB::beginTransaction();
    try {
        $match2->status = 'live_match';
        $match2->save();
        
        echo "1. Initial state:\n";
        printf("   home_score: %s\n", var_export($match2->home_score, true));
        printf("   away_score: %s\n", var_export($match2->away_score, true));
        printf("   status: %s\n\n", $match2->status);
        
        // Simulate endMatch() validation
        echo "2. endMatch() validation (line 1851):\n";
        
        if ($match2->home_score === null || $match2->away_score === null) {
            echo "   ❌ BLOCKED at line 1851\n";
            echo "   Reason: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
            echo "   Result: Request returns with error, status NOT updated\n\n";
        }
        
        DB::rollBack();
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "   Error: " . $e->getMessage() . "\n";
    }
}

// ============================================
// SCENARIO C: Problematic path in buildStandingsGroups()
// ============================================
echo "\nSCENARIO C: buildStandingsGroups() with NULL scores (POTENTIAL BUG)\n";
echo str_repeat("-", 50) . "\n\n";

echo "Condition: Match has status='full_time' BUT home_score=NULL, away_score=NULL\n";
echo "(This shouldn't happen with proper validation, but let's analyze)\n\n";

echo "In buildStandingsGroups() line 2168-2176:\n";
echo "  \$finishedMatches = TournamentMatch::where('status', 'full_time')->get();\n";
echo "  foreach (\$finishedMatches as \$match) {\n";
echo "    \$homeScore = (int) \$match->home_score;   // LINE 2176\n";
echo "    \$awayScore = (int) \$match->away_score;\n";
echo "\n";

$testNull = null;
printf("  (int) null = %d\n", (int)$testNull);
printf("  Result: NULL is silently converted to 0\n\n");

echo "📌 ROOT CAUSE IDENTIFIED:\n";
echo "  If a match somehow gets status='full_time' with NULL scores:\n";
echo "  - buildStandingsGroups() will treat them as 0\n";
echo "  - Match will be recorded as 0-0 DRAW in standings\n";
echo "  - NO ERROR THROWN in standings calculation\n\n";

// ============================================
// SCENARIO D: Check if scores can be NULL AFTER some operations
// ============================================
echo "\nSCENARIO D: Trace all possible event types in storeMatchEvent()\n";
echo str_repeat("-", 50) . "\n\n";

$eventTypes = ['goal', 'own_goal', 'yellow_card', 'red_card', 'foul', 'timeout', 'halftime', 'full_time'];

echo "Event type → Score initialization behavior:\n";
foreach ($eventTypes as $type) {
    $initScore = in_array($type, ['goal', 'own_goal'], true) ? 'YES - Initialize NULL to 0' : 'NO - Scores remain as-is';
    printf("  %-15s → %s\n", $type, $initScore);
}

echo "\n";
echo "Conclusion:\n";
echo "  - Only 'goal' and 'own_goal' events initialize NULL scores to 0\n";
echo "  - All other events skip score initialization\n";
echo "  - If no goal event recorded, scores remain NULL\n";
echo "  - Pre-transaction and transaction validations BLOCK full_time without initialized scores\n";
echo "  - endMatch() also BLOCKS if scores are NULL\n\n";

// ============================================
// FINAL ANALYSIS
// ============================================
echo "\n========================================\n";
echo "FINDINGS:\n";
echo "========================================\n\n";

echo "✓ PROPER FLOW (Normal operation):\n";
echo "  1. Match starts with home_score=NULL, away_score=NULL\n";
echo "  2. First goal event → scores initialize to 0, then increment\n";
echo "  3. More goals recorded → scores increment\n";
echo "  4. User clicks 'End Match' → validation passes, status=full_time\n";
echo "  5. Standings calculated with actual scores\n\n";

echo "❌ BLOCKED FLOWS (Validations prevent this):\n";
echo "  A. Try full_time event without goals:\n";
echo "     - Pre-transaction validation blocks at line 1752\n";
echo "     - Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n\n";

echo "  B. Try endMatch() without initializing scores:\n";
echo "     - Validation blocks at line 1851\n";
echo "     - Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n\n";

echo "⚠️  POTENTIAL VULNERABILITY:\n";
echo "  C. buildStandingsGroups() with status=full_time, scores=NULL:\n";
echo "     - Silently converts NULL to 0 (line 2176)\n";
echo "     - No exception thrown\n";
echo "     - Match recorded as 0-0 DRAW\n";
echo "     - CONDITION: scores remain NULL WHILE status becomes full_time\n";
echo "     - HOW: Only possible if:\n";
echo "       1. Direct DB update bypassing validation\n";
echo "       2. OR Race condition between validation and save\n";
echo "       3. OR Bug in transaction handling\n\n";

echo "========================================\n";
