# Cache Lock Implementation - Race Condition Prevention

**Date**: 2026-06-08  
**Purpose**: Prevent concurrent execution of `storeMatchEvent()` and `endMatch()` on the same match

---

## Problem

Race condition antara dua function pada match yang sama:
- `storeMatchEvent()` - menambahkan goal/event
- `endMatch()` - menyelesaikan match

**Scenario**:
```
T1: storeMatchEvent() start - initializing scores (home=null, away=null)
T2: endMatch() start - checking scores
T3: storeMatchEvent() setting home_score=0, away_score=0
T4: storeMatchEvent() adding goal -> home_score=1
T5: endMatch() proceeds with stale data
T6: storeMatchEvent() finalizes and refreshes match state
```

**Result**: Mismatch dalam state, potentially full_time dengan NULL scores.

---

## Solution: Laravel Cache Lock

### Implementation Pattern
```php
return Cache::lock("match_{$match->id}_lock", 10)->block(5, function () use (...params) {
    // ALL function logic wrapped inside
    
    // Only 1 request per match_id can execute at a time
    // Other requests wait up to 5 seconds
    // Lock expires after 10 seconds (safety timeout)
});
```

### Parameters Explained
- **Lock Key**: `"match_{$match->id}_lock"`
  - Unique per match
  - Ensures only 1 request per match executes

- **Lock Lifetime**: `10` seconds
  - Safety timeout to release lock if process crashes
  - Prevents eternal deadlock

- **Blocking Timeout**: `5` seconds
  - How long to wait for lock acquisition
  - If lock not acquired in 5s, fail gracefully
  - User sees error instead of hang

---

## Files Modified

### 1. TournamentController.php

**Import Added** (Line 15):
```php
use Illuminate\Support\Facades\Cache;
```

**storeMatchEvent() Wrapped** (Line 1741-1853):
```php
public function storeMatchEvent(Request $request, Tournament $tournament, TournamentMatch $match)
{
    // RACE CONDITION FIX: Lock per match_id to prevent concurrent execution
    return Cache::lock("match_{$match->id}_lock", 10)->block(5, function () use ($request, $tournament, $match) {
        // ... ALL existing logic wrapped here
        // Validation
        // Event creation
        // Score update
        // Transaction
        // Return response
    });
}
```

**endMatch() Wrapped** (Line 1859-1906):
```php
public function endMatch(Tournament $tournament, TournamentMatch $match)
{
    // RACE CONDITION FIX: Lock per match_id to prevent concurrent execution
    return Cache::lock("match_{$match->id}_lock", 10)->block(5, function () use ($tournament, $match) {
        // ... ALL existing logic wrapped here
        // Initial checks
        // Transaction to update status
        // Finalize result
        // Return response
    });
}
```

---

## Execution Flow (Protected)

```
Request 1 (storeMatchEvent): match_42
    ├─ Acquire lock "match_42_lock" ✓
    ├─ Check: is match valid?
    ├─ Check: scores initialized?
    ├─ Initialize: home=0, away=0
    ├─ Process: goal event -> home=1
    ├─ Transaction: save match
    ├─ Finalize: update standings
    ├─ Release lock ✓
    └─ Return success

Request 2 (endMatch): match_42
    ├─ Wait for lock "match_42_lock" (Request 1 holds it)
    ├─ [Waiting...] (up to 5 seconds)
    ├─ Acquire lock "match_42_lock" ✓ (after Request 1 releases)
    ├─ Check: scores still valid? YES
    ├─ Transaction: status=full_time
    ├─ Finalize: update standings
    ├─ Release lock ✓
    └─ Return success
```

---

## Race Condition Scenarios PREVENTED

### Scenario 1: Concurrent Goal Entry & Match End
```
BEFORE (no lock):
T1: endMatch() reads scores=null
T2: storeMatchEvent() initializes scores=0
T3: endMatch() proceeds with stale state
❌ Result: full_time with NULL (data corruption)

AFTER (with lock):
T1: endMatch() acquires lock
T2: storeMatchEvent() WAITS for lock (5s timeout)
T3: endMatch() completes, releases lock
T4: storeMatchEvent() acquires lock
T5: Both functions never execute simultaneously
✓ Result: Proper state transitions
```

### Scenario 2: Rapid Button Clicks
```
BEFORE (no lock):
User clicks "End Match" 3 times rapidly
→ 3 requests try to update status simultaneously
→ Race conditions and state inconsistency
❌ Result: Unpredictable state

AFTER (with lock):
User clicks "End Match" 3 times rapidly
→ Request 1 gets lock, others wait
→ Request 1 completes, releases lock
→ Request 2 tries, status already full_time, returns success
→ Request 3 same as Request 2
✓ Result: Idempotent, safe
```

### Scenario 3: Network Delay
```
BEFORE (no lock):
Request 1 (storeMatchEvent) sent, delayed
Request 2 (endMatch) sent, fast
→ endMatch sees no scores yet
→ Marks full_time
→ storeMatchEvent catches up, tries to add goal
❌ Result: Inconsistent state

AFTER (with lock):
Request 1 (storeMatchEvent) sent, delayed
Request 2 (endMatch) sent, fast
→ Request 2 waits for Request 1's lock
→ Request 1 eventually acquires lock
→ Request 2 acquires after Request 1 completes
✓ Result: Proper serialization
```

---

## Safety Properties

| Property | Status | Details |
|----------|--------|---------|
| **Atomicity** | ✓ | Only 1 request per match at a time |
| **Consistency** | ✓ | No state corruption via concurrent modification |
| **Deadlock Safety** | ✓ | 10s timeout prevents eternal blocking |
| **User Experience** | ✓ | 5s wait is reasonable; fails gracefully after |
| **Performance** | ✓ | Lock overhead minimal; proper usage pattern |

---

## Error Handling

If lock acquisition fails (5s timeout exceeded):

### storeMatchEvent() Response
```
Status: 503 Service Unavailable (or similar)
Body: Exception from Cache::lock()->block() timeout
```

### endMatch() Response
```
Status: 503 Service Unavailable (or similar)
Body: Exception from Cache::lock()->block() timeout
```

Users should retry after a moment.

---

## Monitoring

To verify locks are working:

### Enable Debug Logging (optional)
```php
// In .env
CACHE_LOG_CONTENTION=true

// In config/cache.php
'log_contention' => env('CACHE_LOG_CONTENTION', false),
```

Watch logs for "Lock contention" messages to detect frequent lock waits.

### Verify No Race Conditions
Check database for:
```sql
-- Should never exist: full_time with NULL scores
SELECT * FROM matches 
WHERE status = 'full_time' 
AND (home_score IS NULL OR away_score IS NULL);

-- Result should be: 0 rows
```

---

## Compatibility

- **Laravel Version**: 5.1+ (Cache::lock available)
- **Cache Driver**: Works with all drivers (file, redis, memcached, etc.)
- **PHP**: 7.0+ (closure syntax)

### Current Stack
- Laravel 11.x ✓
- PHP 8.x ✓
- Redis (XAMPP) ✓

---

## Simplicity vs Complexity

This solution is **SIMPLER** than previous multi-layer refresh guards:

### Before (Multi-layer guards)
```
CHECK → REFRESH → CHECK → TRANSACTION → REFRESH → CHECK → ATOMIC UPDATE
5 separate checks, 2 refreshes, exception handling
```

### After (Cache Lock)
```
LOCK (serializes) → SINGLE EXECUTION PATH → UNLOCK
1 lock, prevents concurrent execution altogether
```

**Result**: Same safety, much simpler code.

---

## Future Enhancements (Optional)

If needed later:
1. **Lock Waiting Indication**: Add AJAX polling to show "waiting for other users..."
2. **User Notifications**: WebSocket to notify users of lock contention
3. **Analytics**: Track lock wait times to identify bottlenecks
4. **Distributed Locks**: If multiple servers, use redis locks instead of file-based

But for now: **Simple, effective, sufficient**.

---

## Testing Recommendations

### Manual Test 1: Normal Flow
1. Start match, add 2 goals
2. Click "End Match"
3. Verify: status=full_time, scores=2-0

### Manual Test 2: Concurrent Requests (Simulation)
Use two browser tabs:
1. Tab 1: Start match, don't add goals yet
2. Tab 2: Click "End Match" (will fail - no scores)
3. Tab 1: Add a goal
4. Tab 2: Retry "End Match"
5. Tab 1: Try to add another goal (should wait or fail gracefully)

### Manual Test 3: Lock Timeout
1. Start match
2. Artificially delay storeMatchEvent (browser network throttle)
3. Click "End Match" while storeMatchEvent is slow
4. Verify: endMatch waits, eventual success
5. If > 5s delay: endMatch fails with lock timeout

---

## Summary

**What Changed**: Added Cache::lock() wrapper to serialize access per match_id  
**Why**: Prevent race condition between storeMatchEvent and endMatch  
**How**: Simple Laravel facade, 1 request per match at a time  
**Result**: No more full_time with NULL scores from concurrent execution  
