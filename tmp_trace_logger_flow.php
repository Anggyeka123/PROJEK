<?php
/**
 * TEST: Live Match Logger - Trace flow for 0-0 match without goal events
 * Match: Already live_match with NULL scores
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

echo "\n" . str_repeat("=", 70) . "\n";
echo "INVESTIGATION: Live Match Logger → 0-0 Match without Goal Events\n";
echo str_repeat("=", 70) . "\n\n";

// Use match 207 which is live_match with NULL scores
$match = TournamentMatch::find(207);

if (!$match) {
    echo "❌ Match 207 not found\n";
    exit(1);
}

echo "✓ Target Match:\n";
echo "  ID: {$match->id}\n";
echo "  Status: {$match->status}\n";
echo "  Home Score: " . var_export($match->home_score, true) . "\n";
echo "  Away Score: " . var_export($match->away_score, true) . "\n";
echo "  Stage: {$match->stage_type}\n\n";

// Get any events for this match
$events = MatchEvent::where('match_id', $match->id)->get();
echo "Existing Events: " . $events->count() . "\n";
foreach ($events as $evt) {
    echo "  - {$evt->event_type} ({$evt->team_side})\n";
}
echo "\n";

// ============================================
// TRACE 1: Path through storeMatchEvent()
// ============================================
echo str_repeat("-", 70) . "\n";
echo "PATH 1: storeMatchEvent() with full_time event\n";
echo str_repeat("-", 70) . "\n\n";

$validated = [
    'event_type' => 'full_time',
    'team_side' => null,
    'player_name' => null,
    'description' => null,
    'minute' => null,
];

echo "Event Data: event_type='{$validated['event_type']}'\n\n";

echo "Step 1: Pre-transaction validation (line 1752)\n";
echo "  Check: \$validated['event_type'] === 'full_time' && (\$match->home_score === null || \$match->away_score === null)\n";
printf("  Result: %s\n", ($validated['event_type'] === 'full_time' && ($match->home_score === null || $match->away_score === null)) ? 'TRUE' : 'FALSE');

if ($validated['event_type'] === 'full_time' && ($match->home_score === null || $match->away_score === null)) {
    echo "\n⚠️  VALIDATION FAILURE at line 1752:\n";
    echo "    Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
    echo "    Action: Request returns with error (back()->withErrors())\n";
    echo "    DB Impact: NO transaction executed, match remains unchanged\n";
    echo "\n✓ THIS PATH IS BLOCKED\n";
} else {
    echo "\n✓ Would proceed to transaction...\n";
    echo "\nStep 2: In transaction - event type check (line 1791)\n";
    if ($validated['event_type'] === 'goal' || $validated['event_type'] === 'own_goal') {
        echo "  → Initialize NULL scores to 0\n";
    } else if ($validated['event_type'] === 'full_time') {
        echo "  → Check again: (line 1789) if (home_score === null || away_score === null)\n";
        if ($match->home_score === null || $match->away_score === null) {
            echo "    → THROW RuntimeException\n";
            echo "    → Transaction ROLLBACK, status stays unchanged\n";
        }
    } else {
        echo "  → No score initialization for this event\n";
    }
}

// ============================================
// TRACE 2: Path through endMatch()
// ============================================
echo "\n" . str_repeat("-", 70) . "\n";
echo "PATH 2: endMatch() button click\n";
echo str_repeat("-", 70) . "\n\n";

echo "Step 1: Status check (line 1835)\n";
if ($match->status === 'full_time') {
    echo "  ✓ Status is already full_time, return early\n";
} else {
    echo "  ✓ Status is not full_time, continue to validation\n";
}

echo "\nStep 2: Scores validation (line 1851)\n";
echo "  Check: home_score === null || away_score === null\n";
printf("  home_score: %s\n", var_export($match->home_score, true));
printf("  away_score: %s\n", var_export($match->away_score, true));
printf("  Result: %s\n", ($match->home_score === null || $match->away_score === null) ? 'TRUE' : 'FALSE');

if ($match->home_score === null || $match->away_score === null) {
    echo "\n⚠️  VALIDATION FAILURE at line 1851:\n";
    echo "    Logged: 'Attempt to end match but scores missing'\n";
    echo "    Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
    echo "    Action: Request returns with error (back()->withErrors())\n";
    echo "    DB Impact: Transaction NOT executed, status remains unchanged\n";
    echo "\n✓ THIS PATH IS BLOCKED\n";
} else {
    echo "\n✓ Would proceed to transaction...\n";
    echo "  → Set status = 'full_time'\n";
    echo "  → Call finalizeMatchResult()\n";
}

// ============================================
// TRACE 3: Potential vulnerability in buildStandingsGroups
// ============================================
echo "\n" . str_repeat("-", 70) . "\n";
echo "PATH 3: Hypothetical - buildStandingsGroups() with full_time + NULL scores\n";
echo str_repeat("-", 70) . "\n\n";

echo "Scenario: Match has status='full_time' but home_score=NULL, away_score=NULL\n";
echo "(Not possible with normal flow due to validations above)\n\n";

echo "In buildStandingsGroups() (line 2168-2176):\n";
echo "  \$finishedMatches = TournamentMatch::where('status', 'full_time')->get();\n";
echo "  foreach (\$finishedMatches as \$match) {\n";
echo "    \$homeScore = (int) \$match->home_score;  // LINE 2176\n";
echo "    \$awayScore = (int) \$match->away_score;\n";
echo "    ...\n";
echo "  }\n\n";

$nullTest = null;
echo "Casting behavior:\n";
printf("  (int) null = %d\n", (int)$nullTest);
printf("  If home_score is NULL: (int) NULL = 0\n");
printf("  If away_score is NULL: (int) NULL = 0\n\n";

echo "⚠️  POTENTIAL BUG:\n";
echo "  No exception thrown\n";
echo "  Match would be recorded as 0-0 DRAW in standings\n";
echo "  No error message to admin\n\n";

// ============================================
// SUMMARY
// ============================================
echo str_repeat("=", 70) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 70) . "\n\n";

echo "Question: How can a 0-0 match exist without goal events?\n\n";

echo "Analysis:\n";
echo "1. storeMatchEvent() + full_time event:\n";
echo "   Status: ❌ BLOCKED at line 1752 (pre-transaction validation)\n";
echo "   Reason: Scores are NULL\n\n";

echo "2. endMatch() directly:\n";
echo "   Status: ❌ BLOCKED at line 1851 (validation)\n";
echo "   Reason: Scores are NULL\n\n";

echo "3. If somehow status=full_time with scores=NULL:\n";
echo "   Status: ✓ NO ERROR in buildStandingsGroups()\n";
echo "   Reason: (int) null silently becomes 0\n";
echo "   FILE: TournamentController.php\n";
echo "   LINE: 2176\n";
echo "   ISSUE: TYPE CASTING without validation\n\n";

echo "ROOT CAUSE:\n";
echo "  buildStandingsGroups() doesn't validate NULL scores\n";
echo "  When scores are NULL, casting to (int) produces 0\n";
echo "  If a match has status='full_time' with NULL scores,\n";
echo "  it will be silently recorded as 0-0\n\n";

echo "CONDITION for this to happen:\n";
echo "  1. Match achieves status='full_time' (bypassing validations)\n";
echo "  2. Both scores remain NULL\n";
echo "  3. No goal events were recorded\n\n";

echo "TO PREVENT:\n";
echo "  Add NULL check in buildStandingsGroups() before casting\n";
echo "  OR ensure validation prevents full_time with NULL scores\n\n";

echo str_repeat("=", 70) . "\n";
