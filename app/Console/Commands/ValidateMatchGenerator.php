<?php

namespace App\Console\Commands;

use App\Models\Tournament;
use App\Models\TournamentGroupSetting;
use App\Models\AppSetting;
use App\Services\MatchGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ValidateMatchGenerator extends Command
{
    protected $signature = 'validate:match-generator';
    protected $description = 'Validate MatchGenerator dengan 8 skenario berbeda';

    public function handle()
    {
        $this->info('=== VALIDASI MATCH GENERATOR ===');
        $this->line('');

        $scenarios = [
            ['name' => 'Turnamen 1 Grup', 'type' => 'tournament', 'groups' => 1, 'teams_per_group' => 4, 'qualified' => [1, 2]],
            ['name' => 'Turnamen 2 Grup', 'type' => 'tournament', 'groups' => 2, 'teams_per_group' => 4, 'qualified' => [1, 2]],
            ['name' => 'Turnamen 4 Grup', 'type' => 'tournament', 'groups' => 4, 'teams_per_group' => 4, 'qualified' => [1, 2]],
            ['name' => 'Turnamen Kelolosan Berbeda', 'type' => 'tournament', 'groups' => 2, 'teams_per_group' => 8, 'qualified' => [1, 2, 3]],
            ['name' => 'Liga', 'type' => 'league', 'groups' => 1, 'teams_per_group' => 12, 'qualified' => [1, 2]],
            ['name' => 'Liga + Playoff Promosi', 'type' => 'league_playoff', 'groups' => 2, 'teams_per_group' => 6, 'qualified' => [1], 'playoff' => 'promotion'],
            ['name' => 'Liga + Playoff Degradasi', 'type' => 'league_playoff', 'groups' => 2, 'teams_per_group' => 6, 'qualified' => [1], 'playoff' => 'relegation', 'relegated' => [3]],
            ['name' => 'Liga + Playoff Promosi & Degradasi', 'type' => 'league_playoff', 'groups' => 3, 'teams_per_group' => 6, 'qualified' => [1], 'playoff' => 'both', 'relegated' => [3]],
        ];

        foreach ($scenarios as $index => $scenario) {
            $this->line('');
            $this->info("SKENARIO " . ($index + 1) . ": {$scenario['name']}");
            $this->line(str_repeat('-', 80));

            $tournament = $this->createTestTournament($scenario);
            $this->setupGroupSettings($tournament, $scenario);
            $this->setupBracketSettings($tournament, $scenario);
            
            app(MatchGenerator::class)->generateForTournament($tournament);
            
            $this->analyzeResults($tournament, $scenario);
        }

        $this->line('');
        $this->info('=== VALIDASI SELESAI ===');
    }

    private function createTestTournament($scenario)
    {
        $tournament = Tournament::create([
            'name' => 'TEST: ' . $scenario['name'],
            'match_date' => Carbon::now(),
            'division' => 'Test Division',
            'venue' => 'Test Venue',
            'created_by' => 1,
        ]);

        return $tournament;
    }

    private function setupGroupSettings($tournament, $scenario)
    {
        TournamentGroupSetting::create([
            'tournament_id' => $tournament->id,
            'group_count' => $scenario['groups'],
            'teams_per_group' => $scenario['teams_per_group'],
            'qualified_teams' => $scenario['qualified'],
            'relegated_teams' => $scenario['relegated'] ?? [],
            'locked' => false,
        ]);
    }

    private function setupBracketSettings($tournament, $scenario)
    {
        $value = [
            'match_type' => 'single',
            'third_place' => false,
            'competition_type' => $scenario['type'],
            'group_count' => $scenario['groups'],
            'matches' => [],
        ];

        if ($scenario['type'] === 'league_playoff') {
            if ($scenario['playoff'] === 'promotion') {
                $value['playoff_options'] = ['promotion'];
                $value['matches_promotion'] = $this->generateDefaultBracketMatches(
                    $scenario['groups'],
                    $scenario['qualified'],
                    false
                );
            } elseif ($scenario['playoff'] === 'relegation') {
                $value['playoff_options'] = ['relegation'];
                $value['matches_relegation'] = $this->generateDefaultBracketMatches(
                    $scenario['groups'],
                    $scenario['relegated'] ?? [],
                    false
                );
            } elseif ($scenario['playoff'] === 'both') {
                $value['playoff_options'] = ['promotion', 'relegation'];
                $value['matches_promotion'] = $this->generateDefaultBracketMatches(
                    $scenario['groups'],
                    $scenario['qualified'],
                    false
                );
                $value['matches_relegation'] = $this->generateDefaultBracketMatches(
                    $scenario['groups'],
                    $scenario['relegated'] ?? [],
                    false
                );
            }
        } elseif ($scenario['type'] === 'tournament') {
            $value['matches'] = $this->generateDefaultBracketMatches(
                $scenario['groups'],
                $scenario['qualified'],
                false
            );
        }

        AppSetting::create([
            'key' => 'tournament_' . $tournament->id . '_bracket_settings',
            'value' => $value,
        ]);
    }

    private function generateDefaultBracketMatches($groupCount, $qualifiedRanks, $thirdPlace)
    {
        $groupLabels = array_slice(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'], 0, $groupCount);
        $matches = [];
        $matchId = 0;

        foreach ($groupLabels as $group) {
            foreach ($qualifiedRanks as $rank) {
                $matches[] = [
                    'id' => $matchId++,
                    'left' => strtoupper($group) . $rank,
                    'right' => '',
                    'round' => 'Round 1',
                    'is_bye' => false,
                ];
            }
        }

        // Kelompokkan ke dalam bracket tree
        $result = [];
        $roundMatches = [];
        foreach ($matches as $match) {
            $roundMatches[] = [
                'id' => $match['id'],
                'left' => $match['left'],
                'right' => $match['right'] ?? '',
                'round' => $match['round'],
            ];
        }

        // Simple bracket (pairing)
        for ($i = 0; $i < count($roundMatches); $i += 2) {
            $m1 = $roundMatches[$i] ?? null;
            $m2 = $roundMatches[$i + 1] ?? null;
            
            if ($m1 && $m2) {
                $result[] = [
                    'id' => $m1['id'],
                    'left' => $m1['left'],
                    'right' => $m2['left'],
                    'round' => 'Round 1',
                ];
            } elseif ($m1) {
                $result[] = [
                    'id' => $m1['id'],
                    'left' => $m1['left'],
                    'right' => '',
                    'round' => 'Round 1',
                    'is_bye' => true,
                ];
            }
        }

        return $result;
    }

    private function analyzeResults($tournament, $scenario)
    {
        $matches = $tournament->matches()->get();
        
        $stats = [
            'total_matches' => $matches->count(),
            'stages' => $matches->pluck('stage_type')->unique()->values()->toArray(),
            'rounds' => $matches->pluck('round_name')->unique()->count(),
            'groups' => $matches->where('stage_type', 'group')->pluck('group_label')->unique()->count(),
        ];

        $this->line("Total Pertandingan: {$stats['total_matches']}");
        $this->line("Stage Types: " . implode(', ', $stats['stages']));
        $this->line("Jumlah Babak: {$stats['rounds']}");

        if ($stats['groups'] > 0) {
            $this->line("Jumlah Grup: {$stats['groups']}");
        }

        // Tab analysis
        $tabs = $this->analyzeTabs($matches);
        $this->line("Tabs yang muncul: " . implode(', ', $tabs));

        // Group details
        if ($matches->where('stage_type', 'group')->count() > 0) {
            $this->line("");
            $this->line("Detail Pertandingan Grup:");
            foreach ($matches->where('stage_type', 'group')->groupBy('group_label') as $group => $groupMatches) {
                $roundCount = $groupMatches->pluck('round_name')->unique()->count();
                $this->line("  Grup {$group}: {$groupMatches->count()} pertandingan, {$roundCount} babak");
            }
        }

        // League details
        if ($matches->where('stage_type', 'league')->count() > 0) {
            $leagueMatches = $matches->where('stage_type', 'league');
            $this->line("");
            $this->line("Detail Pertandingan Liga:");
            $this->line("  Total: {$leagueMatches->count()} pertandingan");
            $roundCount = $leagueMatches->pluck('round_name')->unique()->count();
            $this->line("  Babak: {$roundCount}");
        }

        // Knockout details
        if ($matches->where('stage_type', 'knockout')->count() > 0) {
            $knockoutMatches = $matches->where('stage_type', 'knockout');
            $this->line("");
            $this->line("Detail Pertandingan Knockout:");
            $this->line("  Total: {$knockoutMatches->count()} pertandingan");
            foreach ($knockoutMatches->pluck('round_name')->unique() as $round) {
                $count = $knockoutMatches->where('round_name', $round)->count();
                $this->line("  {$round}: {$count} pertandingan");
            }
        }

        // Playoff details
        foreach (['promotion_playoff', 'relegation_playoff'] as $type) {
            if ($matches->where('stage_type', $type)->count() > 0) {
                $playoffMatches = $matches->where('stage_type', $type);
                $label = $type === 'promotion_playoff' ? 'Promosi' : 'Degradasi';
                $this->line("");
                $this->line("Detail Playoff {$label}:");
                $this->line("  Total: {$playoffMatches->count()} pertandingan");
            }
        }

        // Potential issues
        $issues = $this->checkForIssues($matches, $scenario);
        if (!empty($issues)) {
            $this->line("");
            $this->line("<fg=yellow>Potensi Issues:</>");
            foreach ($issues as $issue) {
                $this->line("  ⚠ {$issue}");
            }
        }
    }

    private function analyzeTabs($matches)
    {
        $tabs = ['all'];

        if ($matches->contains(fn($m) => $m->stage_type === 'group')) {
            $tabs[] = 'group';
        }

        if ($matches->contains(fn($m) => $m->stage_type === 'league')) {
            $tabs[] = 'league';
        }

        if ($matches->contains(fn($m) => $m->stage_type === 'knockout')) {
            $tabs[] = 'bracket';
        }

        if ($matches->contains(fn($m) => $m->stage_type === 'promotion_playoff')) {
            $tabs[] = 'promotion';
        }

        if ($matches->contains(fn($m) => $m->stage_type === 'relegation_playoff')) {
            $tabs[] = 'relegation';
        }

        return $tabs;
    }

    private function checkForIssues($matches, $scenario)
    {
        $issues = [];

        // Check empty matches
        if ($matches->count() === 0) {
            $issues[] = "CRITICAL: Tidak ada pertandingan yang dihasilkan!";
        }

        // Check for matches dengan empty team slots
        $emptySlots = $matches->filter(function ($m) {
            return empty($m->home_team_key) && empty($m->away_team_key);
        })->count();

        if ($emptySlots > 0) {
            $issues[] = "Ada {$emptySlots} pertandingan dengan slot tim kosong";
        }

        // Check for bye matches
        $byeMatches = $matches->where('is_bye', true)->count();
        if ($byeMatches > 0 && $scenario['type'] === 'tournament') {
            $issues[] = "Ditemukan {$byeMatches} pertandingan bye (mungkin jumlah tim ganjil)";
        }

        // Check bracket consistency
        if ($matches->contains(fn($m) => $m->stage_type === 'knockout')) {
            $knockoutMatches = $matches->where('stage_type', 'knockout');
            $rounds = $knockoutMatches->pluck('round_name')->unique();
            $expectedRounds = log2($knockoutMatches->groupBy('round_name')->count());
            if ($expectedRounds != intval($expectedRounds) && count($rounds) > 1) {
                $issues[] = "Jumlah babak knockout mungkin tidak konsisten dengan power-of-2";
            }
        }

        return $issues;
    }
}
