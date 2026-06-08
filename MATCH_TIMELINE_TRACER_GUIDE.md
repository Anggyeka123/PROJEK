# Match Timeline Tracer - Debug Utility

## Purpose
Trace match execution lifecycle to identify where a match becomes `full_time` with NULL scores.

## Implementation

### 1. Utility Class: `app/Debug/MatchTimelineTracer.php`
- `log(int $matchId, string $action, array $context = [])` — Main logging function
- `logWithEvent(int $matchId, string $action, array $eventData = [])` — Convenience for event logging
- Automatically detects **suspicious condition**: `status = full_time AND (home_score IS NULL OR away_score IS NULL)`
- When suspicious condition found, logs with WARNING level and includes backtrace (10 frames)

### 2. Injection Points

#### a) `TournamentController@updateMatch()`
**Before Update (line ~1693)**
```php
MatchTimelineTracer::log($match->id, 'updateMatch:before', [
    'incoming_status' => $validated['match_status'],
    'computed_status' => $status,
    'incoming_home_score' => $validated['home_score'] ?? 'not_provided',
    'incoming_away_score' => $validated['away_score'] ?? 'not_provided',
]);
```

**After Update (line ~1705)**
```php
MatchTimelineTracer::log($match->id, 'updateMatch:after', [
    'status_changed_to' => $status,
]);
```

**Why**: Captures if scores become NULL when `updateMatch()` persists status=full_time.

---

#### b) `TournamentController@storeMatchEvent()`
**Before Processing Event (line ~1778)**
```php
MatchTimelineTracer::logWithEvent($match->id, 'storeMatchEvent:before', [
    'event_type' => $validated['event_type'],
    'team_side' => $validated['team_side'] ?? null,
    'player_name' => $validated['player_name'] ?? null,
    'minute' => $validated['minute'] ?? 0,
]);
```

**After Event Stored (line ~1842)**
```php
MatchTimelineTracer::logWithEvent($match->id, 'storeMatchEvent:after', [
    'event_type' => $validated['event_type'],
]);
```

**Why**: Tracks event-driven state changes and validates pre-validation checks.

---

#### c) `TournamentController@endMatch()`
**Before Ending Match (line ~1887)**
```php
MatchTimelineTracer::log($match->id, 'endMatch:before', [
    'current_status' => $match->status,
]);
```

**After Status Set (line ~1894)**
```php
MatchTimelineTracer::log($match->id, 'endMatch:after_status_set', []);
```

**Why**: Verifies status=full_time is only set when scores are properly initialized.

---

#### d) `TournamentController@buildStandingsGroups()`
**Before Scoring Match (line ~2210)**
```php
MatchTimelineTracer::log($match->id, 'buildStandingsGroups:before_scoring', [
    'tournament_id' => $tournament->id,
]);
```

**Why**: Captures the moment standings calculation encounters a full_time match. If scores are NULL here, it means they became NULL BEFORE this point and the bug happened upstream.

---

## Log Output Format

### Normal Timeline Entry (INFO level)
```json
{
  "timestamp": "2026-06-08T14:32:15.000000Z",
  "match_id": 42,
  "action": "updateMatch:before",
  "status": "scheduled",
  "home_score": null,
  "away_score": null,
  "stage_type": "group",
  "group_label": "A",
  "context": {
    "incoming_status": "full_time",
    "incoming_home_score": "0",
    "incoming_away_score": "0"
  }
}
```

### Suspicious Condition Alert (WARNING level)
```json
{
  "match_id": 42,
  "action": "buildStandingsGroups:before_scoring",
  "status": "full_time",
  "home_score": null,
  "away_score": null,
  "condition": "status=full_time AND (home_score IS NULL OR away_score IS NULL)",
  "backtrace_frames": [
    "0. App\Http\Controllers\TournamentController->buildStandingsGroups() at .../TournamentController.php:2210",
    "1. App\Http\Controllers\TournamentController->updateStandingsForTournament() at .../TournamentController.php:1912",
    ...
  ]
}
```

---

## How to Use

### 1. Check Log File
```bash
tail -f storage/logs/laravel.log | grep MATCH_TIMELINE
```

### 2. Filter Suspicious Matches
```bash
grep "MATCH_TIMELINE_SUSPICIOUS" storage/logs/laravel.log
```

### 3. Full Timeline for Specific Match
```bash
grep "\"match_id\":42" storage/logs/laravel.log | grep MATCH_TIMELINE
```

### 4. Track Execution Order
```bash
grep "MATCH_TIMELINE" storage/logs/laravel.log | jq -r '[.timestamp, .match_id, .action] | @csv'
```

---

## Findings It Helps Debug

1. **updateMatch() validation bypass** — If scores submitted as `null` values get persisted
2. **Partial/atomic update race** — If status changes before scores are set
3. **Transaction rollback failure** — If transaction commits partially
4. **Direct DB manipulation** — If external script/tool bypasses validation
5. **Silent NULL→0 conversion** — In `buildStandingsGroups()` casting `(int)$match->home_score`

---

## No Business Logic Changes
- ✓ Only adds logging/tracing
- ✓ Does not modify validation rules
- ✓ Does not change scoring logic
- ✓ Safe to deploy to production
- ✓ Can be disabled by disabling MATCH_TIMELINE log channel if needed
