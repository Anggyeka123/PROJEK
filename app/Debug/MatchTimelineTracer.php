<?php

namespace App\Debug;

use App\Models\TournamentMatch;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * MatchTimelineTracer
 * 
 * Reusable debug utility to trace match execution lifecycle and capture state changes.
 * Logs all state snapshots to detect where match becomes full_time with NULL scores.
 */
class MatchTimelineTracer
{
    /**
     * Log match state at a specific action point in the lifecycle.
     * 
     * @param int $matchId
     * @param string $action Descriptive action name (e.g., 'updateMatch:before', 'storeMatchEvent:goal')
     * @param array $context Additional context-specific data
     * @return void
     */
    public static function log(int $matchId, string $action, array $context = []): void
    {
        try {
            // Fetch current match state from DB
            $match = TournamentMatch::find($matchId);
            if (!$match) {
                Log::warning('MATCH_TIMELINE_NOT_FOUND', [
                    'match_id' => $matchId,
                    'action' => $action,
                ]);
                return;
            }

            $payload = [
                'timestamp' => Carbon::now()->toIso8601String(),
                'match_id' => $match->id,
                'action' => $action,
                'status' => $match->status,
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
                'stage_type' => $match->stage_type,
                'group_label' => $match->group_label,
                'home_team_id' => $match->home_team_id,
                'away_team_id' => $match->away_team_id,
                'home_team_key' => $match->home_team_key,
                'away_team_key' => $match->away_team_key,
                'context' => $context,
            ];

            // Check for suspicious condition: full_time + NULL scores
            if ($match->status === 'full_time' && ($match->home_score === null || $match->away_score === null)) {
                self::logSuspiciousCondition($payload);
            }

            // Log the timeline entry
            Log::info('MATCH_TIMELINE', $payload);

        } catch (\Exception $e) {
            Log::error('MATCH_TIMELINE_ERROR', [
                'match_id' => $matchId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log a safety warning when suspicious condition is detected.
     * This indicates a potential bug where status became full_time while scores are NULL.
     * 
     * @param array $payload The match state payload
     * @return void
     */
    private static function logSuspiciousCondition(array $payload): void
    {
        // Capture backtrace (limit to 10 frames)
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        
        // Format backtrace for logging
        $traceFrames = [];
        foreach ($backtrace as $index => $frame) {
            $traceFrames[] = sprintf(
                '%d. %s%s%s() at %s:%d',
                $index,
                $frame['class'] ?? '',
                $frame['type'] ?? '',
                $frame['function'] ?? 'unknown',
                $frame['file'] ?? 'unknown',
                $frame['line'] ?? 0
            );
        }

        Log::warning('MATCH_TIMELINE_SUSPICIOUS', [
            'match_id' => $payload['match_id'],
            'action' => $payload['action'],
            'timestamp' => $payload['timestamp'],
            'status' => $payload['status'],
            'home_score' => $payload['home_score'],
            'away_score' => $payload['away_score'],
            'condition' => 'status=full_time AND (home_score IS NULL OR away_score IS NULL)',
            'stage_type' => $payload['stage_type'],
            'group_label' => $payload['group_label'],
            'context' => $payload['context'],
            'backtrace_frames' => $traceFrames,
        ]);
    }

    /**
     * Convenience method: log a match with additional event data.
     * Useful for storeMatchEvent() tracing.
     * 
     * @param int $matchId
     * @param string $action
     * @param array $eventData Event-specific data (event_type, team_side, etc.)
     * @return void
     */
    public static function logWithEvent(int $matchId, string $action, array $eventData = []): void
    {
        self::log($matchId, $action, ['event_data' => $eventData]);
    }
}
