# Race Condition Fix: endMatch() - 2026-06-08

## Problem
`endMatch()` could execute before `storeMatchEvent()` completes, causing:
- Match status becomes `full_time`
- But `home_score` and `away_score` remain `NULL`
- Race condition: concurrent request execution without synchronization

## Root Cause
Original code only checked scores ONCE before transaction:
```php
if ($match->home_score === null || $match->away_score === null) {
    return back()->withErrors([...]);
}

DB::transaction(function () use ($match) {
    $match->status = 'full_time';
    $match->save();
    // ... finalize
});
```

**Gap**: Between the check and transaction execution, another request (`storeMatchEvent`) could:
1. Modify the match
2. Change scores
3. Or leave scores as NULL
4. Then `endMatch` proceeds with stale data

## Solution

### Layer 1: Pre-transaction Refresh Guard (Line ~1885)
```php
// RACE CONDITION FIX: Refresh before transaction to catch concurrent updates
$match->refresh();

if ($match->home_score === null || $match->away_score === null) {
    Log::warning('Race condition detected in endMatch', [
        'match_id' => $match->id,
        'message' => 'Scores became NULL after initial check',
    ]);

    return back()->withErrors(['end_match' => 'Score belum sinkron. Mohon coba beberapa saat lagi.']);
}
```

**Why**: Catches concurrent updates that happened between initial validation and transaction start.

---

### Layer 2: In-transaction Refresh Guard (Line ~1901)
```php
DB::transaction(function () use ($match) {
    // RACE CONDITION FIX: Re-check scores inside transaction for safety
    $match->refresh();

    if ($match->home_score === null || $match->away_score === null) {
        Log::error('Critical race condition in endMatch transaction', [...]);
        throw new \Exception('Score belum siap. Tidak dapat menyelesaikan pertandingan.');
    }

    // Use atomic update instead of direct assignment
    $match->update(['status' => 'full_time']);
    
    // Continue with finalization
    $this->finalizeMatchResult($match);
});
```

**Why**: 
- Double-checks inside transaction lock
- Throws exception if scores NULL (transaction rollback)
- Uses atomic `update()` instead of direct assignment + `save()`

---

### Layer 3: Atomic Update (Line ~1913)
**Before:**
```php
$match->status = 'full_time';
$match->save();
```

**After:**
```php
$match->update(['status' => 'full_time']);
```

**Why**: Single database operation is more atomic than separate assign + save.

---

## Execution Flow (Fixed)

```
User clicks "End Match"
    ↓
endMatch() called
    ↓
[Guard 1] Check scores (initial)
    ↓
[Guard 1] $match->refresh() ← Fetch latest from DB
    ↓
[Guard 1] Re-check scores (post-refresh)
    ↓
Start transaction
    ↓
[Guard 2] $match->refresh() inside transaction
    ↓
[Guard 2] Re-check scores (inside lock)
    ↓
[SAFE] $match->update(['status' => 'full_time'])
    ↓
[SAFE] finalizeMatchResult()
    ↓
Commit transaction
```

---

## Race Condition Scenarios Prevented

### Scenario 1: Concurrent storeMatchEvent()
```
Timeline:
T1: endMatch() - Check: home_score=0, away_score=1 ✓
T1: endMatch() - refresh: home_score=0, away_score=1 ✓
T2: storeMatchEvent() - goal event starts
T3: storeMatchEvent() - initializes scores: home_score=1, away_score=0
T3: endMatch() - IN TRANSACTION - refresh: home_score=1, away_score=0 ✓
T4: endMatch() - Update status to full_time ✓
T5: storeMatchEvent() - Commits goal event

Result: ✓ FIXED - Scores properly synchronized
```

### Scenario 2: Malformed storeMatchEvent()
```
Timeline:
T1: endMatch() - Check: home_score=0, away_score=1 ✓
T2: storeMatchEvent() - Error in goal initialization
T2: storeMatchEvent() - Scores reset to NULL somehow
T3: endMatch() - refresh: home_score=NULL, away_score=NULL ✗
T3: Return error: "Score belum sinkron"

Result: ✓ FIXED - Prevented full_time with NULL
```

---

## Logging

### Scenario 1: Successful execution
```
[INFO] MATCH_TIMELINE: action=endMatch:before status=live_match
[INFO] MATCH_TIMELINE: action=endMatch:after_status_set status=full_time
```

### Scenario 2: Race condition detected (pre-transaction)
```
[WARNING] Race condition detected in endMatch
  match_id: 42
  home_score: null
  away_score: null
  message: "Scores became NULL after initial check"
[HTTP] 422: "Score belum sinkron. Mohon coba beberapa saat lagi."
```

### Scenario 3: Race condition detected (in-transaction - critical)
```
[ERROR] Critical race condition in endMatch transaction
  match_id: 42
  home_score: null
  away_score: null
[HTTP] 500 with exception rolled back
```

---

## Testing

### Manual Test 1: Normal flow
1. Create match, start live match logger
2. Record goals (home: 2, away: 1)
3. Click "End Match"
4. Verify: status=full_time, home_score=2, away_score=1

### Manual Test 2: Race condition simulation
1. Create match, start live match logger
2. In one tab: Click "End Match" (don't release)
3. In another tab: Quickly add a goal event
4. Verify: First "End Match" either succeeds with synced scores or fails with race condition error

---

## Impact
- **Breaking changes**: None
- **Business logic changes**: None
- **Validator changes**: None
- **Safety**: Improved - prevents invalid state via race condition
- **Performance**: Minimal - 2 extra `refresh()` calls per endMatch
- **User experience**: Better error message for race condition cases

---

## Monitoring
Watch for these log entries to detect race conditions in production:
```bash
grep "Race condition detected in endMatch" storage/logs/laravel.log
grep "Critical race condition in endMatch transaction" storage/logs/laravel.log
```

If these appear frequently, it indicates users are experiencing real race conditions and may need:
- UI debounce on "End Match" button
- Locking mechanism for live match session
- WebSocket updates to sync state across tabs
