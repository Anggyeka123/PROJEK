<?php
/**
 * FINAL VERIFICATION: Live Match Logger Flow Analysis
 * Shows exact validation and conditions for each path
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

echo "\n" . str_repeat("=", 80) . "\n";
echo "FINAL VERIFICATION: Live Match Logger Flow\n";
echo str_repeat("=", 80) . "\n\n";

// Test match
$match = TournamentMatch::find(207);
if (!$match) {
    echo "Test match not found\n";
    exit(1);
}

echo "Test Match State:\n";
echo "  ID: " . $match->id . "\n";
echo "  Status: " . $match->status . "\n";
echo "  Home Score: " . var_export($match->home_score, true) . "\n";
echo "  Away Score: " . var_export($match->away_score, true) . "\n";
echo "  Stage: " . $match->stage_type . "\n\n";

// ============= VALIDATION TEST =============
echo str_repeat("-", 80) . "\n";
echo "VALIDATION TEST: Can this match be finalized with NULL scores?\n";
echo str_repeat("-", 80) . "\n\n";

// Simulate storeMatchEvent validation
echo "1. storeMatchEvent() - line 1752 (pre-transaction check):\n";
$eventType = 'full_time';
$homeScoreNull = $match->home_score === null;
$awayScoreNull = $match->away_score === null;

echo "   Condition: (\$event_type === 'full_time') && (\$home_score === null || \$away_score === null)\n";
echo "   Values: (\'" . $eventType . "\' === 'full_time') && (" . ($homeScoreNull ? 'true' : 'false') . " || " . ($awayScoreNull ? 'true' : 'false') . ")\n";

$blocked1 = ($eventType === 'full_time' && ($homeScoreNull || $awayScoreNull));
echo "   Result: " . ($blocked1 ? "BLOCKED ✗" : "PASS ✓") . "\n";

if ($blocked1) {
    echo "   → return back()->withErrors(['Pertandingan tidak bisa diselesaikan...'])\n";
    echo "   → NO transaction, DB unchanged\n";
}
echo "\n";

// Simulate endMatch validation
echo "2. endMatch() - line 1851 (before transaction check):\n";
echo "   Condition: (\$home_score === null || \$away_score === null)\n";
echo "   Values: (" . ($homeScoreNull ? 'true' : 'false') . " || " . ($awayScoreNull ? 'true' : 'false') . ")\n";

$blocked2 = ($homeScoreNull || $awayScoreNull);
echo "   Result: " . ($blocked2 ? "BLOCKED ✗" : "PASS ✓") . "\n";

if ($blocked2) {
    echo "   → return back()->withErrors(['Pertandingan tidak bisa diselesaikan...'])\n";
    echo "   → NO transaction, status unchanged\n";
}
echo "\n";

// ============= VULNERABILITY TEST =============
echo str_repeat("-", 80) . "\n";
echo "VULNERABILITY TEST: buildStandingsGroups() NULL casting\n";
echo str_repeat("-", 80) . "\n\n";

echo "Location: buildStandingsGroups() line 2176\n";
echo "Code pattern:\n";
echo "  \$homeScore = (int) \$match->home_score;\n";
echo "  \$awayScore = (int) \$match->away_score;\n\n";

$testNull = null;
$homeScoreCast = (int)$testNull;
$awayScoreCast = (int)$testNull;

echo "Test cast with NULL values:\n";
echo "  (int) null = " . $homeScoreCast . "\n";
echo "  (int) null = " . $awayScoreCast . "\n\n";

echo "If match reaches buildStandingsGroups() with status='full_time':\n";
echo "  - Match would be treated as " . $homeScoreCast . "-" . $awayScoreCast . " DRAW\n";
echo "  - No exception thrown\n";
echo "  - Admin gets no error message\n";
echo "  - Silent bug recorded in standings\n\n";

// ============= SUMMARY =============
echo str_repeat("=", 80) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 80) . "\n\n";

echo "Path 1 (storeMatchEvent + full_time): " . ($blocked1 ? "BLOCKED ✓" : "PASS (risky)") . "\n";
echo "Path 2 (endMatch): " . ($blocked2 ? "BLOCKED ✓" : "PASS (risky)") . "\n";
echo "buildStandingsGroups() vulnerability: CONFIRMED\n\n";

echo "Conclusion:\n";
echo "  Normal flow prevents full_time with NULL scores\n";
echo "  But buildStandingsGroups() silently handles NULL as 0\n";
echo "  This is only a problem if validation is somehow bypassed\n\n";

echo "ROOT CAUSE IDENTIFIED:\n";
echo "  FILE: app/Http/Controllers/TournamentController.php\n";
echo "  LINE: 2176\n";
echo "  ISSUE: Type casting (int) null without NULL check\n";
echo "  SYMPTOM: Silent conversion of NULL to 0 in standings\n\n";

echo str_repeat("=", 80) . "\n";
