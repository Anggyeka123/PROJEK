<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;

echo "TEST: Detailed validation casting behavior\n";
echo str_repeat("=", 60) . "\n";

$payload = [
    'match_date' => date('Y-m-d'),
    'match_time' => date('H:i'),
    'match_status' => 'full_time',
    'home_score' => '0',
    'away_score' => '0',
];

$rules = [
    'home_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
    'away_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
];

$validator = Validator::make($payload, $rules);

if (!$validator->fails()) {
    $validated = $validator->validated();
    
    echo "Input values:\n";
    foreach (['home_score', 'away_score'] as $field) {
        printf("  %s: %s (type: %s, is_numeric: %s)\n",
            $field,
            var_export($payload[$field], true),
            gettype($payload[$field]),
            is_numeric($payload[$field]) ? 'yes' : 'no'
        );
    }
    
    echo "\nValidated values:\n";
    foreach (['home_score', 'away_score'] as $field) {
        printf("  %s: %s (type: %s, is_numeric: %s)\n",
            $field,
            var_export($validated[$field], true),
            gettype($validated[$field]),
            is_numeric($validated[$field]) ? 'yes' : 'no'
        );
    }
    
    echo "\nComparison operations:\n";
    printf("  validated['home_score'] === null: %s\n", $validated['home_score'] === null ? 'TRUE' : 'FALSE');
    printf("  validated['home_score'] === 0: %s\n", $validated['home_score'] === 0 ? 'TRUE' : 'FALSE');
    printf("  validated['home_score'] == 0: %s\n", $validated['home_score'] == 0 ? 'TRUE' : 'FALSE');
    printf("  validated['home_score'] !== null: %s\n", $validated['home_score'] !== null ? 'TRUE' : 'FALSE');
    printf("  empty(validated['home_score']): %s\n", empty($validated['home_score']) ? 'TRUE' : 'FALSE');
    printf("  isset(validated['home_score']): %s\n", isset($validated['home_score']) ? 'TRUE' : 'FALSE');
    
    echo "\nArray operations:\n";
    printf("  array_key_exists('home_score', \$validated): %s\n",
        array_key_exists('home_score', $validated) ? 'TRUE' : 'FALSE');
    
    // Simulate ternary from controller
    $result = array_key_exists('home_score', $validated) ? $validated['home_score'] : 'NOT_FOUND';
    printf("  Ternary result: %s (type: %s)\n", var_export($result, true), gettype($result));
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "OBSERVATION: Check if string '0' passes through ternary unchanged\n";
