<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TournamentMatch;

$matchId = $argv[1] ?? null;
if (! $matchId) {
    echo "Usage: php tmp_simulate_update_validation.php <matchId> [home_score] [away_score] [status]\n";
    exit(1);
}
$home = $argv[2] ?? '0';
$away = $argv[3] ?? '0';
$status = $argv[4] ?? 'full_time';

$m = TournamentMatch::find($matchId);
if (! $m) {
    echo "MATCH_NOT_FOUND\n";
    exit(1);
}

// Build request payload as browser would send
$payload = [
    'match_date' => ($m->match_date ? $m->match_date->format('Y-m-d') : date('Y-m-d')),
    'match_time' => ($m->match_date ? $m->match_date->format('H:i') : date('H:i')),
    'match_status' => $status,
    'home_score' => $home,
    'away_score' => $away,
];

echo "Simulating form payload for match {$matchId}:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . PHP_EOL;

$rules = [
    'match_date' => 'required|date',
    'match_time' => 'required|date_format:H:i',
    'match_status' => 'required|in:scheduled,live_match,full_time',
    'home_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
    'away_score' => 'required_if:match_status,full_time|nullable|integer|min:0',
];

$validator = Validator::make($payload, $rules, [
    'match_date.required' => 'Tanggal pertandingan wajib diisi.',
    'match_time.required' => 'Waktu pertandingan wajib diisi.',
    'match_status.required' => 'Status laga wajib dipilih.',
    'match_status.in' => 'Status laga tidak valid.',
    'home_score.required_if' => 'Skor Home wajib diisi ketika pertandingan Full Time.',
    'away_score.required_if' => 'Skor Away wajib diisi ketika pertandingan Full Time.',
]);

if ($validator->fails()) {
    echo "Validation FAILED:\n";
    print_r($validator->errors()->all());
    exit(0);
}

$validated = $validator->validated();

echo "Validation PASSED. Validated data:\n";
print_r($validated);

// Show types
echo "Types:\n";
foreach ($validated as $k => $v) {
    printf("%s => (%s) %s\n", $k, gettype($v), var_export($v, true));
}

exit(0);
