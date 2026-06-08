<?php
/**
 * TEST: Reproduce 0-0 score issue
 * 
 * Check:
 * 1. How isset() behaves with score = 0
 * 2. How form value is rendered
 * 3. How input submission is validated
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;
use App\Models\TournamentMatch;

echo str_repeat("=", 60) . PHP_EOL;
echo "TEST 1: PHP isset() behavior with score = 0" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;

$match_array_with_zero = [
    'id' => 1,
    'score_left' => 0,
    'score_right' => 0,
];

$match_array_with_null = [
    'id' => 2,
    'score_left' => null,
    'score_right' => null,
];

printf("isset(\$match['score_left'] = 0): %s (expected: true)\n", 
    isset($match_array_with_zero['score_left']) ? 'TRUE' : 'FALSE');
printf("isset(\$match['score_right'] = 0): %s (expected: true)\n", 
    isset($match_array_with_zero['score_right']) ? 'TRUE' : 'FALSE');

printf("isset(\$match['score_left'] = null): %s (expected: false)\n", 
    isset($match_array_with_null['score_left']) ? 'TRUE' : 'FALSE');
printf("isset(\$match['score_right'] = null): %s (expected: false)\n", 
    isset($match_array_with_null['score_right']) ? 'TRUE' : 'FALSE');

echo "\n" . str_repeat("=", 60) . PHP_EOL;
echo "TEST 2: Form rendering simulation" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;

$test_cases = [
    ['name' => 'Score is 0', 'score_left' => 0, 'score_right' => 0],
    ['name' => 'Score is null', 'score_left' => null, 'score_right' => null],
    ['name' => 'Score is 1-2', 'score_left' => 1, 'score_right' => 2],
];

foreach ($test_cases as $test) {
    echo "\n{$test['name']}:\n";
    $match = ['id' => 1, 'score_left' => $test['score_left'], 'score_right' => $test['score_right']];
    
    // Simulate Blade template: value="{{ isset($match['score_left']) ? $match['score_left'] : '' }}"
    $rendered_left = isset($match['score_left']) ? $match['score_left'] : '';
    $rendered_right = isset($match['score_right']) ? $match['score_right'] : '';
    
    echo "  Rendered value (home): value=\"{$rendered_left}\"\n";
    echo "  Rendered value (away): value=\"{$rendered_right}\"\n";
    
    // Simulate form submission
    $submitted_left = ($rendered_left !== '') ? (string)$rendered_left : null;
    $submitted_right = ($rendered_right !== '') ? (string)$rendered_right : null;
    
    echo "  Form would send (home): " . var_export($submitted_left, true) . "\n";
    echo "  Form would send (away): " . var_export($submitted_right, true) . "\n";
}

echo "\n" . str_repeat("=", 60) . PHP_EOL;
echo "TEST 3: Laravel validation with 0 values" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;

$payload_zero = [
    'match_date' => date('Y-m-d'),
    'match_time' => date('H:i'),
    'match_status' => 'full_time',
    'home_score' => '0',
    'away_score' => '0',
];

$payload_empty = [
    'match_date' => date('Y-m-d'),
    'match_time' => date('H:i'),
    'match_status' => 'full_time',
    'home_score' => '',
    'away_score' => '',
];

$rules = [
    'match_date' => 'required|date',
    'match_time' => 'required|date_format:H:i',
    'match_status' => 'required|in:scheduled,live_match,full_time',
    'home_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
    'away_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
];

echo "\nPayload with home_score='0', away_score='0':\n";
$validator = Validator::make($payload_zero, $rules);
if ($validator->fails()) {
    echo "  VALIDATION FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "    - $error\n";
    }
} else {
    $validated = $validator->validated();
    echo "  VALIDATION PASSED\n";
    printf("  Validated home_score: %s (type: %s)\n", 
        var_export($validated['home_score'], true), gettype($validated['home_score']));
    printf("  Validated away_score: %s (type: %s)\n", 
        var_export($validated['away_score'], true), gettype($validated['away_score']));
    printf("  home_score !== null: %s\n", $validated['home_score'] !== null ? 'TRUE' : 'FALSE');
    printf("  away_score !== null: %s\n", $validated['away_score'] !== null ? 'TRUE' : 'FALSE');
}

echo "\nPayload with home_score='', away_score='':\n";
$validator = Validator::make($payload_empty, $rules);
if ($validator->fails()) {
    echo "  VALIDATION FAILED (as expected):\n";
    foreach ($validator->errors()->all() as $error) {
        echo "    - $error\n";
    }
} else {
    $validated = $validator->validated();
    echo "  VALIDATION PASSED (unexpected!)\n";
}

echo "\n" . str_repeat("=", 60) . PHP_EOL;
echo "SUMMARY" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo "If tests pass normally, the bug is likely in database saving logic.\n";
