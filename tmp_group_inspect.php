<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

$rows = TournamentMatch::where('stage_type', 'group')
    ->orderBy('id')
    ->get(['id','round_name','home_score','away_score','status']);

foreach ($rows as $row) {
    echo implode(' | ', [
        $row->id,
        $row->round_name,
        isset($row->home_score) ? $row->home_score : 'NULL',
        isset($row->away_score) ? $row->away_score : 'NULL',
        $row->status ?? 'NULL',
    ]) . "\n";
}
