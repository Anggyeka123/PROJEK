<?php
$lines = file('app/Http/Controllers/TournamentController.php');
for ($i = 1760; $i < 1980; $i++) {
    echo sprintf('%4d: %s', $i + 1, $lines[$i]);
}
