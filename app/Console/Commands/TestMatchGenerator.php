<?php

namespace App\Console\Commands;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\AppSetting;
use App\Services\MatchGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMatchGenerator extends Command
{
    protected $signature = 'test:match-generator {--tournament-id=}';
    protected $description = 'Test MatchGenerator implementation with existing tournament configuration';

    public function handle(): int
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║         MATCHGENERATOR IMPLEMENTATION TEST                 ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $tournaments = $this->getTournaments();

        if ($tournaments->isEmpty()) {
            $this->error('No tournaments found in database.');
            return Command::FAILURE;
        }

        foreach ($tournaments as $tournament) {
            $this->testTournament($tournament);
            $this->newLine(2);
        }

        return Command::SUCCESS;
    }

    private function getTournaments()
    {
        if ($tournamentId = $this->option('tournament-id')) {
            return Tournament::where('id', $tournamentId)->get();
        }

        return Tournament::all();
    }

    private function testTournament(Tournament $tournament): void
    {
        $this->line('─ ' . str_repeat('─', 60) . ' ─');
        $this->info("Tournament: {$tournament->name} (ID: {$tournament->id})");
        $this->line('─ ' . str_repeat('─', 60) . ' ─');

        // 1. Load Configuration
        $this->newLine();
        $this->section('1️⃣ LOAD TOURNAMENT CONFIGURATION');
        $config = $this->loadConfiguration($tournament);

        if (empty($config)) {
            $this->warn('  ⚠️  Incomplete configuration - skipping generation');
            return;
        }

        // 2. Generate Matches
        $this->newLine();
        $this->section('2️⃣ GENERATE MATCHES');
        $this->generateMatches($tournament);

        // 3. Verify Results
        $this->newLine();
        $this->section('3️⃣ VERIFICATION RESULTS');
        $this->verifyResults($tournament, $config);
    }

    private function loadConfiguration(Tournament $tournament): array
    {
        $tournament->load('groupSetting');

        if (!$tournament->groupSetting) {
            $this->warn('  ⚠️  No group settings configured');
            return [];
        }

        $bracketSetting = AppSetting::where(
            'key',
            'tournament_' . $tournament->id . '_bracket_settings'
        )->first();

        $bracketValue = $bracketSetting->value ?? [];
        $competitionType = $bracketValue['competition_type'] ?? 'tournament';
        $playoffOptions = $bracketValue['playoff_options'] ?? [];

        $config = [
            'competition_type' => $competitionType,
            'playoff_options' => $playoffOptions,
            'teams_per_group' => $tournament->groupSetting->teams_per_group,
            'qualified_teams' => $tournament->groupSetting->qualified_teams,
            'relegated_teams' => $tournament->groupSetting->relegated_teams ?? [],
        ];

        // Display configuration
        $this->displayConfiguration($tournament, $config);

        return $config;
    }

    private function displayConfiguration(Tournament $tournament, array $config): void
    {
        $this->table(
            ['Setting', 'Value'],
            [
                ['Kompetisi', ucfirst(str_replace('_', ' ', $config['competition_type']))],
                ['Tim per Grup', $config['teams_per_group']],
                ['Ranking Lolos', implode(', ', $config['qualified_teams']) ?: 'N/A'],
                ['Ranking Degradasi', implode(', ', $config['relegated_teams']) ?: 'N/A'],
                ['Playoff Options', implode(', ', $config['playoff_options']) ?: 'None'],
            ]
        );

        // Team count calculation
        if ($config['competition_type'] === 'tournament') {
            $totalTeams = $config['teams_per_group'] * 4; // Assume 4 groups for tournament
            $this->line("\n  📊 Estimated: {$totalTeams} total teams (4 groups × {$config['teams_per_group']} teams)");
        } else {
            $totalTeams = $config['teams_per_group'];
            $this->line("\n  📊 Estimated: {$totalTeams} total teams (1 group - League system)");
        }
    }

    private function generateMatches(Tournament $tournament): void
    {
        try {
            $generator = app(MatchGenerator::class);
            $generator->generateForTournament($tournament);
            $this->info('  ✅ Matches generated successfully');
        } catch (\Exception $e) {
            $this->error('  ❌ Generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function verifyResults(Tournament $tournament, array $config): void
    {
        $matches = TournamentMatch::where('tournament_id', $tournament->id)->get();

        // Verify match count
        $this->verifyMatchCount($matches, $config);

        // Verify groups
        $this->verifyGroups($matches, $config);

        // Verify stages
        $this->verifyStages($matches, $config);

        // Verify playoffs
        if (in_array('promotion', $config['playoff_options'], true) || 
            in_array('relegation', $config['playoff_options'], true)) {
            $this->verifyPlayoffs($matches, $config);
        }

        // Summary
        $this->newLine();
        $this->section('📋 DETAILED BREAKDOWN');
        $this->displayMatchBreakdown($matches);
    }

    private function verifyMatchCount(mixed $matches, array $config): void
    {
        $count = $matches->count();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Matches Generated', $count],
                ['Matches with home_team_key set', $matches->whereNotNull('home_team_key')->count()],
                ['Bye Matches', $matches->where('is_bye', true)->count()],
                ['Third Place Matches', $matches->where('is_third_place', true)->count()],
            ]
        );

        if ($count > 0) {
            $this->line("  ✅ Matches generated: {$count}");
        } else {
            $this->warn('  ⚠️  No matches generated');
        }
    }

    private function verifyGroups(mixed $matches, array $config): void
    {
        $groups = $matches->pluck('group_label')->filter()->unique()->sort()->values();

        $this->newLine();
        $this->table(
            ['Verification', 'Status'],
            [
                ['Groups Found', $groups->count() . ' (' . $groups->implode(', ') . ')'],
                [
                    'Group Count Match',
                    ($config['competition_type'] === 'tournament' && $groups->count() === 4) ? '✅' : 
                    (($config['competition_type'] !== 'tournament' && $groups->count() <= 1) ? '✅' : '❌')
                ],
            ]
        );
    }

    private function verifyStages(mixed $matches, array $config): void
    {
        $stages = $matches->pluck('stage_type')->unique()->sort()->values();

        $this->newLine();
        $this->line('📍 Stages Found:');
        foreach ($stages as $stage) {
            $count = $matches->where('stage_type', $stage)->count();
            $icon = match ($stage) {
                'group' => '🏆',
                'league' => '📈',
                'knockout' => '⚔️',
                'promotion_playoff' => '⬆️',
                'relegation_playoff' => '⬇️',
                default => '•'
            };
            $this->line("  {$icon} {$stage}: {$count} matches");
        }

        // Verify expected stages
        $this->newLine();
        $expectedStages = $this->getExpectedStages($config);
        foreach ($expectedStages as $expectedStage) {
            $hasStage = $stages->contains($expectedStage);
            $icon = $hasStage ? '✅' : '❌';
            $this->line("  {$icon} Expected: {$expectedStage}");
        }
    }

    private function getExpectedStages(array $config): array
    {
        $stages = [];

        if ($config['competition_type'] === 'tournament') {
            $stages = ['group', 'knockout'];
        } elseif ($config['competition_type'] === 'league') {
            $stages = ['league'];
        } elseif ($config['competition_type'] === 'league_playoff') {
            $stages = ['league'];
            if (in_array('promotion', $config['playoff_options'], true)) {
                $stages[] = 'promotion_playoff';
            }
            if (in_array('relegation', $config['playoff_options'], true)) {
                $stages[] = 'relegation_playoff';
            }
        }

        return $stages;
    }

    private function verifyPlayoffs(mixed $matches, array $config): void
    {
        $this->newLine();
        $this->section('🎯 PLAYOFF VERIFICATION');

        if (in_array('promotion', $config['playoff_options'], true)) {
            $promotionMatches = $matches->where('stage_type', 'promotion_playoff');
            $this->line("  ⬆️ Promotion Playoff: {$promotionMatches->count()} matches");
            if ($promotionMatches->isNotEmpty()) {
                $promotionTeams = $promotionMatches->pluck('home_team_key')
                    ->merge($promotionMatches->pluck('away_team_key'))
                    ->filter()
                    ->unique()
                    ->count();
                $this->line("     Teams: {$promotionTeams} unique positions");
            }
        }

        if (in_array('relegation', $config['playoff_options'], true)) {
            $relegationMatches = $matches->where('stage_type', 'relegation_playoff');
            $this->line("  ⬇️ Relegation Playoff: {$relegationMatches->count()} matches");
            if ($relegationMatches->isNotEmpty()) {
                $relegationTeams = $relegationMatches->pluck('home_team_key')
                    ->merge($relegationMatches->pluck('away_team_key'))
                    ->filter()
                    ->unique()
                    ->count();
                $this->line("     Teams: {$relegationTeams} unique positions");
            }
        }
    }

    private function displayMatchBreakdown(mixed $matches): void
    {
        $breakdown = $matches->groupBy(['stage_type', 'group_label'])
            ->map(function ($stageGroup) {
                return $stageGroup->map(function ($groupMatches) {
                    $rounds = $groupMatches->pluck('round_name')->unique();
                    return [
                        'count' => $groupMatches->count(),
                        'rounds' => $rounds->implode(', '),
                    ];
                });
            });

        foreach ($breakdown as $stage => $groups) {
            $this->line("\n📍 {$stage}:");
            foreach ($groups as $group => $data) {
                $groupLabel = $group ?: '(no group)';
                $this->line("   • {$groupLabel}: {$data['count']} matches | Rounds: {$data['rounds']}");
            }
        }
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->line("┌─ {$title}");
    }
}
