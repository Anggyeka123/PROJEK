<?php
/**
 * TEST: Live Match Logger - Trace flow for 0-0 match without goal events
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\MatchEvent;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat("=", 70) . "\n";
echo "INVESTIGATION: Live Match Logger - 0-0 Match without Goal Events\n";
echo str_repeat("=", 70) . "\n\n";

$match = TournamentMatch::find(207);
if (!$match) {
    echo "No match found\n";
    exit(1);
}

echo "Match Status:\n";
echo "  ID: " . $match->id . "\n";
echo "  Status: " . $match->status . "\n";
echo "  Home Score: " . var_export($match->home_score, true) . "\n";
echo "  Away Score: " . var_export($match->away_score, true) . "\n\n";

// PATH 1: storeMatchEvent() + full_time
echo str_repeat("-", 70) . "\n";
echo "PATH 1: storeMatchEvent() with full_time event\n";
echo str_repeat("-", 70) . "\n\n";

echo "Validation at line 1752 (pre-transaction):\n";
$eventType = 'full_time';
$homeNull = $match->home_score === null;
$awayNull = $match->away_score === null;

echo "  event_type === 'full_time': " . ($eventType === 'full_time' ? 'TRUE' : 'FALSE') . "\n";
echo "  home_score === null: " . ($homeNull ? 'TRUE' : 'FALSE') . "\n";
echo "  away_score === null: " . ($awayNull ? 'TRUE' : 'FALSE') . "\n";

$blocked1 = ($eventType === 'full_time' && ($homeNull || $awayNull));
echo "  Combined check result: " . ($blocked1 ? 'BLOCKED' : 'PASS') . "\n";

if ($blocked1) {
    echo "\nResult: REQUEST REJECTED\n";
    echo "  Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
    echo "  DB Impact: NO transaction executed\n";
}
echo "\n";

// PATH 2: endMatch()
echo str_repeat("-", 70) . "\n";
echo "PATH 2: endMatch() directly\n";
echo str_repeat("-", 70) . "\n\n";

echo "Validation at line 1851 (before transaction):\n";
echo "  home_score === null: " . ($homeNull ? 'TRUE' : 'FALSE') . "\n";
echo "  away_score === null: " . ($awayNull ? 'TRUE' : 'FALSE') . "\n";

$blocked2 = ($homeNull || $awayNull);
echo "  Combined check result: " . ($blocked2 ? 'BLOCKED' : 'PASS') . "\n";

if ($blocked2) {
    echo "\nResult: REQUEST REJECTED\n";
    echo "  Error: 'Pertandingan tidak bisa diselesaikan tanpa kedua skor terisi.'\n";
    echo "  DB Impact: NO transaction executed\n";
}
echo "\n";

// PATH 3: buildStandingsGroups vulnerability
echo str_repeat("-", 70) . "\n";
echo "PATH 3: buildStandingsGroups() NULL casting\n";
echo str_repeat("-", 70) . "\n\n";

echo "Location: TournamentController.php line 2176\n";
echo "Code: \$homeScore = (int) \$match->home_score;\n\n";

$nullVal = null;
$castResult = (int)$nullVal;
echo "Casting NULL to int:\n";
echo "  (int) null = " . $castResult . "\n";
echo "  Result: NULL silently becomes 0\n\n";

echo "If match has status='full_time' with NULL scores:\n";
echo "  (int) home_score (NULL) = 0\n";
echo "  (int) away_score (NULL) = 0\n";
echo "  Match recorded as: 0-0 DRAW\n";
echo "  Error thrown: NONE\n\n";

// SUMMARY
echo str_repeat("=", 70) . "\n";
echo "FINDINGS SUMMARY\n";
echo str_repeat("=", 70) . "\n\n";

echo "Status: Both paths (storeMatchEvent and endMatch) are BLOCKED\n";
echo "Reason: Validation prevents full_time status with NULL scores\n\n";

echo "However, NULL score handling issue exists:\n";
echo "  FILE: app/Http/Controllers/TournamentController.php\n";
echo "  LINE: 2176\n";
echo "  METHOD: buildStandingsGroups()\n\n";

echo "ISSUE:\n";
echo "  If somehow match reaches status='full_time' with NULL scores:\n";
echo "  - No validation error thrown\n";
echo "  - NULL values silently cast to 0\n";
echo "  - Match recorded as 0-0\n\n";

echo "CONDITION:\n";
echo "  Match status = 'full_time'\n";
echo "  Match home_score = NULL\n";
echo "  Match away_score = NULL\n";
echo "  No goal events recorded\n\n";

echo "ROOT CAUSE:\n";
echo "  Type casting without NULL validation in standings calculation\n";
echo "  (int) NULL = 0 in PHP - this is implicit behavior\n\n";

echo str_repeat("=", 70) . "\n";
