<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;

$tournament = Tournament::find(4);
if (! $tournament) {
    echo "NO TOURNAMENT 4\n";
    exit(1);
}
$tournament->load('groupSetting');
$gs = $tournament->groupSetting;
if (! $gs) {
    echo "NO GROUP SETTING\n";
    exit(1);
}

echo 'qualified_teams=' . json_encode($gs->qualified_teams) . "\n";
echo 'relegated_teams=' . json_encode($gs->relegated_teams) . "\n";
echo 'group_count=' . ($gs->group_count ?? 'NULL') . "\n";
