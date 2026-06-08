<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tournament;
use App\Models\TournamentMatch;

$tournaments = Tournament::all();
foreach ($tournaments as $t) {
    $g = TournamentMatch::where('tournament_id',$t->id)->where('stage_type','group')->count();
    $k = TournamentMatch::where('tournament_id',$t->id)->where('stage_type','knockout')->count();
    if ($g>0 && $k>0) {
        echo "TOURNAMENT {$t->id}: groups={$g} knockout={$k}\n";
    }
}
