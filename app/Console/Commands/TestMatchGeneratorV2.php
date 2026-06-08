<?php

namespace App\Console\Commands;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\AppSetting;
use App\Services\MatchGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMatchGeneratorV2 extends Command
{
    protected $signature = 'test:match-generator-v2 {--tournament-id=}';
    protected $description = 'Test MatchGenerator with comprehensive validation and summary report';

    private $results = [];

    public function handle(): int
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║       MATCHGENERATOR COMPREHENSIVE TEST & VALIDATION       ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $tournaments = $this->getTournaments();

        if ($tournaments->isEmpty()) {
            $this->error('❌ No tournaments found in database.');
            return Command::FAILURE;
        }

        foreach ($tournaments as $tournament) {
            $this->testTournament($tournament);
            $this->newLine(2);
        }

        // Summary Report
        $this->displaySummaryReport();

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
        $this->line('╔' . str_repeat('═', 62) . '╗');
        $this->line('║ ' . str_pad("Tournament: {$tournament->name}", 61) . '║');
        $this->line('╚' . str_repeat('═', 62) . '╝');

        $result = [
            'tournament_id' => $tournament->id,
            'tournament_name' => $tournament->name,
            'status' => 'PENDING',
            'error' => null,
        ];

        try {
            // 1. Load Configuration
            $this->newLine();
            $this->section('📋 STEP 1: LOAD TOURNAMENT CONFIGURATION');
            $config = $this->loadConfiguration($tournament);

            if (empty($config)) {
                throw new \Exception('Incomplete configuration');
            }

            // 2. Generate Matches
            $this->newLine();
            $this->section('⚙️ STEP 2: GENERATE MATCHES');
            $this->generateMatches($tournament);

            // 3. Verify Results
            $this->newLine();
            $this->section('✅ STEP 3: VERIFICATION & VALIDATION');
            $verificationData = $this->verifyResults($tournament, $config);

            $result['status'] = 'SUCCESS';
            $result['config'] = $config;
            $result['verification'] = $verificationData;

        } catch (\Exception $e) {
            $result['status'] = 'FAILED';
            $result['error'] = $e->getMessage();
            $this->error("  ❌ {$e->getMessage()}");
        }

        $this->results[] = $result;
    }

    private function loadConfiguration(Tournament $tournament): array
    {
        $tournament->load('groupSetting');

        if (!$tournament->groupSetting) {
            throw new \Exception('No group settings configured');
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
        $this->table(
            ['Setting', 'Value'],
            [
                ['📍 Sistem Kompetisi', ucfirst(str_replace('_', ' ', $config['competition_type']))],
                ['👥 Tim per Grup', $config['teams_per_group']],
                ['🏅 Ranking Lolos', implode(', ', $config['qualified_teams']) ?: '(none)'],
                ['⬇️  Ranking Degradasi', implode(', ', $config['relegated_teams']) ?: '(none)'],
                ['🎯 Playoff Options', implode(', ', $config['playoff_options']) ?: '(none)'],
            ]
        );

        $this->info('  ✅ Configuration loaded successfully');

        return $config;
    }

    private function generateMatches(Tournament $tournament): void
    {
        try {
            $generator = app(MatchGenerator::class);
            $generator->generateForTournament($tournament);
            $this->info('  ✅ Matches generated successfully to database');
        } catch (\Exception $e) {
            throw new \Exception('Match generation failed: ' . $e->getMessage());
        }
    }

    private function verifyResults(Tournament $tournament, array $config): array
    {
        $matches = TournamentMatch::where('tournament_id', $tournament->id)->get();

        $verification = [
            'total_matches' => $matches->count(),
            'matches_by_stage' => [],
            'groups' => [],
            'playoff_details' => [],
            'validation_checks' => [],
        ];

        // Verify match count
        $verification['total_matches'] = $matches->count();
        $this->line("\n🔢 Match Count Summary:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Matches', $matches->count()],
                ['Matches with Team Keys', $matches->whereNotNull('home_team_key')->count()],
                ['Bye Matches', $matches->where('is_bye', true)->count()],
                ['Third Place Matches', $matches->where('is_third_place', true)->count()],
            ]
        );

        // Verify groups
        if ($config['competition_type'] === 'tournament') {
            $this->verifyTournamentGroups($matches, $verification);
        } else {
            $this->verifyLeagueGroups($matches, $verification);
        }

        // Verify stages
        $this->verifyStages($matches, $config, $verification);

        // Verify playoffs if applicable
        if (!empty($config['playoff_options'])) {
            $this->verifyPlayoffs($matches, $config, $verification);
        }

        // Display detailed breakdown
        $this->displayDetailedBreakdown($matches);

        // Validation summary
        $this->newLine();
        $this->section('📊 VALIDATION SUMMARY');
        $this->displayValidationChecks($verification);

        return $verification;
    }

    private function verifyTournamentGroups(&$matches, &$verification): void
    {
        $groups = $matches->pluck('group_label')->filter()->unique()->sort()->values();
        
        $this->newLine();
        $this->line('📍 Group Verification:');
        $this->line("  Groups: " . ($groups->isNotEmpty() ? $groups->implode(', ') : '(none)'));
        $this->line("  Count: {$groups->count()} groups");

        if ($groups->count() === 4) {
            $this->line("  ✅ Expected 4 groups for tournament");
        } else {
            $this->line("  ⚠️  Expected 4 groups, got {$groups->count()}");
        }

        $verification['groups'] = $groups->toArray();
    }

    private function verifyLeagueGroups(&$matches, &$verification): void
    {
        $groups = $matches->pluck('group_label')->filter()->unique()->values();

        $this->newLine();
        $this->line('📍 Group Verification (League):');
        if ($groups->isEmpty()) {
            $this->line('  League system: Single group (no group labels)');
            $this->line('  ✅ Correct for league system');
        } else {
            $this->line("  Groups: " . $groups->implode(', '));
        }

        $verification['groups'] = $groups->toArray();
    }

    private function verifyStages(&$matches, array $config, &$verification): void
    {
        $stages = $matches->pluck('stage_type')->unique()->sort()->values();

        $this->newLine();
        $this->line('📍 Stage Breakdown:');
        
        $stageBreakdown = [];
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
            
            $stageBreakdown[] = [$icon . ' ' . $stage, $count . ' matches'];
            $verification['matches_by_stage'][$stage] = $count;
        }

        $this->table(['Stage', 'Matches'], $stageBreakdown);

        // Verify expected stages
        $expectedStages = $this->getExpectedStages($config);
        $this->newLine();
        $this->line('✅ Stage Verification:');
        foreach ($expectedStages as $expectedStage) {
            $hasStage = $stages->contains($expectedStage);
            $icon = $hasStage ? '✅' : '❌';
            $this->line("  {$icon} {$expectedStage}");
            $verification['validation_checks']['stage_' . $expectedStage] = $hasStage;
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

    private function verifyPlayoffs(&$matches, array $config, &$verification): void
    {
        $this->newLine();
        $this->line('⚽ Playoff Verification:');

        if (in_array('promotion', $config['playoff_options'], true)) {
            $promotionMatches = $matches->where('stage_type', 'promotion_playoff');
            $count = $promotionMatches->count();
            $this->line("  ⬆️  Promotion Playoff: {$count} matches");
            
            if ($promotionMatches->isNotEmpty()) {
                $teamCount = $promotionMatches->pluck('home_team_key')
                    ->merge($promotionMatches->pluck('away_team_key'))
                    ->filter()
                    ->unique()
                    ->count();
                $this->line("      └─ Teams: {$teamCount} unique positions");
                $verification['playoff_details']['promotion'] = [
                    'matches' => $count,
                    'teams' => $teamCount
                ];
            }
            $verification['validation_checks']['promotion_playoff'] = $count > 0;
        }

        if (in_array('relegation', $config['playoff_options'], true)) {
            $relegationMatches = $matches->where('stage_type', 'relegation_playoff');
            $count = $relegationMatches->count();
            $this->line("  ⬇️  Relegation Playoff: {$count} matches");
            
            if ($relegationMatches->isNotEmpty()) {
                $teamCount = $relegationMatches->pluck('home_team_key')
                    ->merge($relegationMatches->pluck('away_team_key'))
                    ->filter()
                    ->unique()
                    ->count();
                $this->line("      └─ Teams: {$teamCount} unique positions");
                $verification['playoff_details']['relegation'] = [
                    'matches' => $count,
                    'teams' => $teamCount
                ];
            }
            $verification['validation_checks']['relegation_playoff'] = $count > 0;
        }
    }

    private function displayDetailedBreakdown(&$matches): void
    {
        $this->newLine();
        $this->section('📋 DETAILED MATCH BREAKDOWN');

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
            $stageIcon = match ($stage) {
                'group' => '🏆',
                'league' => '📈',
                'knockout' => '⚔️',
                'promotion_playoff' => '⬆️',
                'relegation_playoff' => '⬇️',
                default => '•'
            };
            
            $this->line("\n{$stageIcon} {$stage}:");
            foreach ($groups as $group => $data) {
                $groupLabel = $group ?: '(league system)';
                $roundsText = $data['rounds'] ? "Rounds: {$data['rounds']}" : 'Round: N/A';
                $this->line("   ├─ {$groupLabel}: {$data['count']} matches | {$roundsText}");
            }
        }
    }

    private function displayValidationChecks(&$verification): void
    {
        $checks = [];
        
        foreach ($verification['validation_checks'] as $check => $passed) {
            $icon = $passed ? '✅' : '❌';
            $label = ucfirst(str_replace('_', ' ', $check));
            $checks[] = [$icon . ' ' . $label, $passed ? 'PASSED' : 'FAILED'];
        }

        if (!empty($checks)) {
            $this->table(['Check', 'Status'], $checks);
        }

        // Overall status
        $allPassed = !in_array(false, $verification['validation_checks']);
        $this->newLine();
        if ($allPassed) {
            $this->info('✅ All validation checks PASSED');
        } else {
            $this->warn('⚠️  Some validation checks FAILED');
        }
    }

    private function displaySummaryReport(): void
    {
        $this->newLine(2);
        $this->line('╔' . str_repeat('═', 62) . '╗');
        $this->line('║ ' . str_pad('FINAL SUMMARY REPORT', 61) . '║');
        $this->line('╚' . str_repeat('═', 62) . '╝');
        $this->newLine();

        $summaryTable = [];
        foreach ($this->results as $result) {
            $icon = match ($result['status']) {
                'SUCCESS' => '✅',
                'FAILED' => '❌',
                default => '⚠️'
            };

            $matchCount = $result['verification']['total_matches'] ?? 0;
            $stageCount = count($result['verification']['matches_by_stage'] ?? []);
            
            $summary = "{$icon} {$result['tournament_name']} - {$matchCount} matches, {$stageCount} stages";
            
            $summaryTable[] = [
                $result['tournament_name'],
                $result['status'],
                $matchCount,
                $stageCount,
                $result['config']['competition_type'] ?? 'N/A'
            ];
        }

        $this->table(
            ['Tournament', 'Status', 'Matches', 'Stages', 'Type'],
            $summaryTable
        );

        // Success percentage
        $successCount = count(array_filter($this->results, fn($r) => $r['status'] === 'SUCCESS'));
        $totalCount = count($this->results);
        $percentage = ($totalCount > 0) ? round(($successCount / $totalCount) * 100) : 0;

        $this->newLine();
        $statusText = $percentage === 100 ? '✅ ALL TESTS PASSED' : "⚠️ {$percentage}% Success Rate";
        $this->info("📊 Result: {$statusText} ({$successCount}/{$totalCount} tournaments)");
        $this->newLine();
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->line("┌─ {$title}");
    }
}
