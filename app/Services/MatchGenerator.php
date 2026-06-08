<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentGroupSetting;
use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MatchGenerator
{
    public function generateForTournament(Tournament $tournament): void
    {
        $tournament->load('groupSetting');

        if (! $tournament->groupSetting instanceof TournamentGroupSetting) {
            return;
        }

        $bracketSetting = $this->getBracketSetting($tournament);
        $bracketValue = $bracketSetting->value ?? [];

        $competitionType = $bracketValue['competition_type'] ?? 'tournament';
        $playoffOptions = $bracketValue['playoff_options'] ?? [];

        $matches = [];

        if ($competitionType === 'tournament') {
            // Tournament mode now generates group stage matches only.
            // Knockout placeholders are created later once the group stage is complete.
            $matches = array_merge(
                $matches,
                $this->buildTournamentStageMatches($tournament, $bracketValue)
            );
        } elseif ($competitionType === 'league') {
            $matches = array_merge(
                $matches,
                $this->buildLeagueStageMatches($tournament)
            );
        } elseif ($competitionType === 'league_playoff') {
            $matches = array_merge(
                $matches,
                $this->buildLeagueStageMatches($tournament)
            );

            if (in_array('promotion', $playoffOptions, true)) {
                $matches = array_merge(
                    $matches,
                    $this->buildPlayoffMatches($tournament, $bracketValue, 'promotion')
                );
            }

            if (in_array('relegation', $playoffOptions, true)) {
                $matches = array_merge(
                    $matches,
                    $this->buildPlayoffMatches($tournament, $bracketValue, 'relegation')
                );
            }
        }

        Log::info('Generating tournament matches', [
            'tournament_id' => $tournament->id,
            'competition_type' => $competitionType,
            'playoff_options' => $playoffOptions,
            'matches_generated' => count($matches),
        ]);

        DB::transaction(function () use ($tournament, $matches) {
            TournamentMatch::where('tournament_id', $tournament->id)->delete();

            if (! empty($matches)) {
                TournamentMatch::insert($matches);
            }

            $this->attachBracketNextMatchIds($tournament);
        });
    }

    private function getBracketSetting(Tournament $tournament): AppSetting
    {
        $key = $this->bracketSettingsKey($tournament);
        $setting = AppSetting::firstOrCreate(
            ['key' => $key],
            ['value' => []]
        );

        return $setting;
    }

    private function bracketSettingsKey(Tournament $tournament): string
    {
        return 'tournament_' . $tournament->id . '_bracket_settings';
    }

    private function buildTournamentStageMatches(Tournament $tournament, array $bracketValue): array
    {
        return $this->buildGroupStageMatches($tournament);
    }

    public function generateBracketStructureForTournament(Tournament $tournament): void
    {
        $tournament->load('groupSetting');

        $bracketSetting = $this->getBracketSetting($tournament);
        $bracketValue = $bracketSetting->value ?? [];
        $competitionType = $bracketValue['competition_type'] ?? 'tournament';

        if ($competitionType !== 'tournament') {
            return;
        }

        $existingBracketMatches = TournamentMatch::where('tournament_id', $tournament->id)
            ->where('stage_type', 'knockout')
            ->exists();

        if ($existingBracketMatches) {
            return;
        }

        $matches = $this->buildBracketMatchesFromArray(
            $tournament,
            $bracketValue['matches'] ?? [],
            'knockout',
            null
        );

        if (empty($matches)) {
            return;
        }

        DB::transaction(function () use ($tournament, $matches) {
            TournamentMatch::insert($matches);
            $this->attachBracketNextMatchIds($tournament);
        });
    }

    private function buildLeagueStageMatches(Tournament $tournament): array
    {
        $tournament->load(['tournamentTeams.team']);

        $teamDescriptors = $tournament->tournamentTeams
            ->sortBy(fn ($team) => $team->seed ?? 0)
            ->map(function ($tournamentTeam) {
                return [
                    'id' => $tournamentTeam->id,
                    'key' => $tournamentTeam->team?->name ?? 'TBD',
                ];
            })
            ->values()
            ->all();

        if (count($teamDescriptors) < 2) {
            return [];
        }

        return $this->buildRoundRobinMatchRows(
            $tournament,
            $teamDescriptors,
            'league',
            'League',
            'Matchday'
        );
    }

    private function buildPlayoffMatches(Tournament $tournament, array $bracketValue, string $playoffType): array
    {
        $stageType = $playoffType === 'promotion' ? 'promotion_playoff' : 'relegation_playoff';
        $matches = [];

        if ($playoffType === 'promotion' && isset($bracketValue['matches_promotion'])) {
            $matches = $this->buildBracketMatchesFromArray(
                $tournament,
                $bracketValue['matches_promotion'],
                $stageType,
                'promotion'
            );
        } elseif ($playoffType === 'relegation' && isset($bracketValue['matches_relegation'])) {
            $matches = $this->buildBracketMatchesFromArray(
                $tournament,
                $bracketValue['matches_relegation'],
                $stageType,
                'relegation'
            );
        } elseif (isset($bracketValue['matches'])) {
            $matches = $this->buildBracketMatchesFromArray(
                $tournament,
                $bracketValue['matches'],
                $stageType,
                $playoffType
            );
        }

        return $matches;
    }

    private function buildGroupStageMatches(Tournament $tournament): array
    {
        $groupSetting = $tournament->groupSetting;
        $groupCount = $groupSetting->group_count;
        $groupLabels = $this->buildGroupLabels($groupCount);

        // Ensure teams are loaded so we can log and operate on fresh data
        $tournament->load(['tournamentTeams.team']);

        // Log incoming tournamentTeams (id, seed, group_label)
        $teamsForLog = $tournament->tournamentTeams->map(function ($t) {
            return [
                'id' => $t->id,
                'seed' => $t->seed,
                'group_label' => $t->group_label,
            ];
        })->values()->all();

        Log::info('buildGroupStageMatches: tournamentTeams loaded', [
            'tournament_id' => $tournament->id,
            'teams' => $teamsForLog,
        ]);

        $teamsByGroupCollection = $tournament->tournamentTeams
            ->filter(fn ($team) => ! empty($team->group_label))
            ->groupBy('group_label')
            ->mapWithKeys(function ($teams, $groupLabel) {
                $sortedTeams = $teams->sortBy(fn ($team) => $team->seed ?? 0);

                return [$groupLabel => $sortedTeams->map(function ($tournamentTeam) {
                    return [
                        'id' => $tournamentTeam->id,
                        'key' => $tournamentTeam->team?->name ?? 'TBD',
                    ];
                })->values()->all()];
            });

        // Log counts per group after grouping
        $groupCounts = $teamsByGroupCollection->map(fn ($teams) => count($teams))->toArray();
        Log::info('buildGroupStageMatches: teams grouped', [
            'tournament_id' => $tournament->id,
            'group_counts' => $groupCounts,
        ]);

        $teamsByGroup = $teamsByGroupCollection->toArray();

        $rows = [];

        foreach ($groupLabels as $groupLabel) {
            $teamDescriptors = $teamsByGroup[$groupLabel] ?? [];
            if (count($teamDescriptors) < 2) {
                continue;
            }

            $groupMatches = $this->buildRoundRobinMatchRows(
                $tournament,
                $teamDescriptors,
                'group',
                strtoupper($groupLabel),
                'Matchday'
            );

            $rows = array_merge($rows, $groupMatches);
        }

        return $rows;
    }

    private function buildBracketMatchesFromArray(Tournament $tournament, array $bracketMatches, string $stageType, ?string $playoffType, array $extra = []): array
    {
        $rows = [];
        $now = Carbon::now();

        foreach ($bracketMatches as $match) {
            $rows[] = [
                'tournament_id' => $tournament->id,
                'bracket_match_id' => isset($match['id']) ? (int) $match['id'] : null,
                'next_bracket_match_id' => isset($match['next_match_id']) ? $match['next_match_id'] : null,
                'stage_type' => $stageType,
                'playoff_type' => $playoffType,
                'group_label' => null,
                'round_name' => $match['round'] ?? 'Bracket',
                'home_team_key' => $match['left'] ?? null,
                'away_team_key' => $match['right'] ?? null,
                'source_home' => $match['left'] ?? null,
                'source_away' => $match['right'] ?? null,
                'is_bye' => isset($match['is_bye']) ? (bool) $match['is_bye'] : false,
                'is_third_place' => isset($match['is_third_place']) ? (bool) $match['is_third_place'] : false,
                'status' => 'scheduled',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $rows;
    }

    private function buildRoundRobinMatchRows(Tournament $tournament, array $teamDescriptors, string $stageType, string $groupLabel, string $roundLabelPrefix): array
    {
        $rows = [];
        $rounds = $this->generateRoundRobinSchedule($teamDescriptors);
        $now = Carbon::now();

        foreach ($rounds as $roundIndex => $round) {
            $roundName = $roundLabelPrefix . ' ' . ($roundIndex + 1);

            foreach ($round as $match) {
                $home = $match['home'];
                $away = $match['away'];

                $homeTeamId = is_array($home) ? ($home['id'] ?? null) : null;
                $awayTeamId = is_array($away) ? ($away['id'] ?? null) : null;
                $homeTeamKey = is_array($home) ? ($home['key'] ?? ($home['name'] ?? null)) : $home;
                $awayTeamKey = is_array($away) ? ($away['key'] ?? ($away['name'] ?? null)) : $away;

                $rows[] = [
                    'tournament_id' => $tournament->id,
                    'bracket_match_id' => null,
                    'next_bracket_match_id' => null,
                    'stage_type' => $stageType,
                    'playoff_type' => null,
                    'group_label' => $groupLabel,
                    'round_name' => $roundName,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'home_team_key' => $homeTeamKey,
                    'away_team_key' => $awayTeamKey,
                    'source_home' => $homeTeamKey,
                    'source_away' => $awayTeamKey,
                    'is_bye' => $match['is_bye'],
                    'is_third_place' => false,
                    'status' => 'scheduled',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $rows;
    }

    private function generateRoundRobinSchedule(array $teams): array
    {
        $teams = array_values($teams);

        if (count($teams) % 2 !== 0) {
            $teams[] = 'Bye';
        }

        $count = count($teams);
        $fixed = array_shift($teams);
        $rounds = [];
        $roundCount = $count - 1;

        for ($round = 0; $round < $roundCount; $round++) {
            $pairings = [];
            $teamList = array_merge([$fixed], $teams);
            $teamCount = count($teamList);

            for ($i = 0; $i < $teamCount / 2; $i++) {
                $home = $teamList[$i];
                $away = $teamList[$teamCount - 1 - $i];

                $pairings[] = [
                    'home' => $home,
                    'away' => $away,
                    'is_bye' => $home === 'Bye' || $away === 'Bye',
                ];
            }

            $rounds[] = $pairings;
            array_unshift($teams, array_pop($teams));
        }

        return $rounds;
    }

    private function attachBracketNextMatchIds(Tournament $tournament): void
    {
        $matches = TournamentMatch::where('tournament_id', $tournament->id)
            ->whereNotNull('next_bracket_match_id')
            ->get();

        $mapping = TournamentMatch::where('tournament_id', $tournament->id)
            ->whereNotNull('bracket_match_id')
            ->get()
            ->pluck('id', 'bracket_match_id')
            ->toArray();

        foreach ($matches as $match) {
            $nextBracketId = $match->next_bracket_match_id;
            if ($nextBracketId !== null && isset($mapping[$nextBracketId])) {
                $match->next_match_id = $mapping[$nextBracketId];
                $match->save();
            }
        }
    }

    private function buildGroupLabels(int $groupCount): array
    {
        return array_slice(['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'], 0, $groupCount);
    }

    private function generateLeagueTeamKeys(int $teamCount): array
    {
        $teams = [];
        for ($index = 1; $index <= $teamCount; $index++) {
            $teams[] = 'League ' . $index;
        }
        return $teams;
    }
}
