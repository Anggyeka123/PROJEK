<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TournamentMatch;

$types = TournamentMatch::select('stage_type')->distinct()->get()->pluck('stage_type');
foreach ($types as $type) {
    echo $type . "\n";
}
