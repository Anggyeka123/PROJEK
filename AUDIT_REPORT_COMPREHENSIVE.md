# 🔍 AUDIT REPORT - TOURNAMENT MANAGEMENT SYSTEM
**Date**: 2026-06-08  
**Scope**: Group Settings, Participants, Standings, Bracket  
**Format**: FILE | FUNGSI | MASALAH | DAMPAK

---

## 📋 EXECUTIVE SUMMARY

| Area | Status | Risk | Issues Found |
|------|--------|------|--------------|
| **Group Settings** | ⚠️ RISKY | HIGH | 5 issues |
| **Participants** | ⚠️ RISKY | HIGH | 4 issues |
| **Standings** | ⚠️ RISKY | CRITICAL | 6 issues |
| **Bracket** | ⚠️ RISKY | HIGH | 5 issues |

---

# SECTION 1: PENGATURAN GRUP TOURNAMENT

## 1.1 Group Settings Management Controller

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 315-363) | `groupSettings()` | **Force reload tanpa validation**: `$tournament->load()` + `$tournament->refresh()` dilakukan setiap kali tanpa check apakah data stale atau tidak | Performa query bertambah; jika banyak grup dapat memperlambat response time |
| `TournamentController.php` (Line 338-358) | `groupSettings() - Default creation` | **Default qualified_teams selalu [1,2]**: Tanpa konfigurasi flexibel, hanya 2 tim lolos otomatis | User tidak bisa setup qualified teams yang berbeda saat first access; harus manually edit setelah |
| `TournamentController.php` (Line 1437-1473) | `updateSettings()` | **No validation untuk ranked teams existence**: Input `qualified_teams` dan `relegated_teams` tidak divalidasi apakah teams dengan ranking tersebut ada | Bisa membuat qualified teams ranking 1-5 padahal hanya ada 3 tim per grup → standings error |
| `TournamentController.php` (Line 1450-1473) | `updateSettings()` | **Lock tersimpan tapi tidak dicek di form submission**: Settings di-lock setelah save, tapi form tetap allow edit input jika user bypass frontend | UI menunjukkan "locked" tapi backend bisa update jika form re-submitted manual |
| `TournamentController.php` (Line 1476-1526) | `updateSettings() - Bracket regeneration` | **Auto regenerate bracket tapi qualified_teams mungkin belum final**: Bracket di-regen berdasarkan `qualified_teams` tapi user bisa change ini kapan saja; bracket tidak re-validate | Bracket teams bisa mismatch dengan actual standings setelah ranking change |

---

## 1.2 Group Settings Database Schema

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `tournament_group_settings` table | **No migration untuk `relegated_teams` di awal** | `relegated_teams` ditambah tapi tidak ada explicit migration; bisa inconsistent di old DBs | Old database mungkin tidak punya kolom; schema diff detection bisa fail |
| `TournamentGroupSetting` model (Line 1-40) | `$casts` | **Array casting untuk qualified_teams OK, tapi no validation** | Jika JSON invalid, casting bisa throw exception atau return null tanpa logging | NULL values tidak dicatat → standings calculation silently skip ini |
| `tournament_group_settings` table | **No constraint untuk teams_per_group + qualified_teams** | `teams_per_group=4` tapi `qualified_teams=[1,2,3,4,5]` dimungkinkan di DB | Standings calcs meminta ranking 5 padahal max ranking 4 → missing teams di standings |
| `TournamentGroupSetting` model (Line 33-35) | `getQualifiedTeamsLabel()` | **Hardcoded "Belum ada pengaturan" tanpa check locked status** | UI bisa show confusing message jika locked tapi qualified_teams empty | User lihat "Belum ada pengaturan" padahal sudah locked → confusion |

---

## 1.3 Group Settings View/UI

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `group-settings-panel.blade.php` (Line 1-450) | **JavaScript state management** | `updateQualifiedTeams()` function di-check based on `teamsPerGroup` but DOM bisa out-of-sync jika partial load | User lihat 4 checkboxes tapi backend punya qualified_teams untuk 5 rankings | Mismatch antara UI dan saved data |
| `settings-group.blade.php` (Line 88-120) | **Team count display calculation** | `teamsPerGroup * groupCount` hardcoded di template tanpa validation backend | Jika `group_count` NULL tapi `teams_per_group=4` → result bisa jadi NaN atau 0 | Math error di UI; user confused |
| `group-settings-panel.blade.php` (Line 441-480) | **Relegation checkbox state tidak sync** | Jika competition_type bukan league_playoff, relegation field hidden tapi relegated_teams still in DB | Hidden checkbox state tidak reflect database value | User change competition type, relegated_teams persisted tapi not visible |

---

## 1.4 Group Settings - TournamentGroupSetting Model

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentGroupSetting.php` (Line 22-40) | `isQualified()` method | **No null safety**: `in_array($teamRanking, $this->qualified_teams ?? [])` - jika qualified_teams NULL, uses empty array | Bisa return false untuk semua rankings jika qualified_teams corruption | Semua teams dianggap tidak lolos padahal seharusnya ada |

---

# SECTION 2: MANAJEMEN PESERTA (PARTICIPANTS)

## 2.1 Participant Add/Create

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentParticipantController.php` (Line 37-65) | `store()` | **No check untuk duplicate team dalam tournament**: Bisa add tim yang sama 2x ke tournament yang sama | Database unique constraint catch tapi user see generic DB error, tidak friendly message | User frustration; bingung apa salahnya |
| `TournamentParticipantController.php` (Line 48-50) | `store() - Logo upload` | **Fallback ke placeholder jika upload fail**: `$logoPath = null` tapi tidak log error | User tidak tau logo gagal upload; gambar blank di standings | Silent failure; no visibility |
| `TournamentParticipantController.php` (Line 62-65) | `store() - Auto schedule regenerate** | `generateForTournament()` called immediately setelah add peserta | Jika peserta add sambil matches sedang running, schedule race condition | Schedule bisa corrupt atau matches duplicate |
| `TournamentParticipantController.php` (Line 37-41) | `store() - Country validation** | Indonesia require province, lain require state; tapi no check untuk data quality | User input "State: XYZ" tapi negara Indonesia → inconsistent data | Profile data quality jelek |

---

## 2.2 Participant Edit/Update

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentParticipantController.php` (Line 73-121) | `update()` | **No check untuk group_label consistency**: Update tim name/logo tapi group_label bisa sudah assign | Edit peserta bisa corrupt group assignments jika jadwal sudah running | Peserta bisa jadi "ghost" di standings karena group mismatch |
| `TournamentParticipantController.php` (Line 121-125) | `update() - Schedule regenerate` | `generateForTournament()` called setelah edit; bisa reset matches | User edit tim detail, schedule reset tanpa warning | All matches deleted dan di-regenerate; user kehilangan poin entry |

---

## 2.3 Participant Delete/Destroy

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentParticipantController.php` (Line 129-145) | `destroy()` | **No cascade check untuk players/officials**: TournamentTeam delete cascades, tapi orphaned players bisa remain | Player records tanpa tournament_team_id | Orphaned data di DB |
| `TournamentParticipantController.php` (Line 138-145) | `destroy() - Schedule regenerate` | `generateForTournament()` called; schedule updated immediately without state check | Jika jadwal sudah running, delete peserta bisa corrupt matches | Matches jadi invalid (missing teams) |

---

## 2.4 Participant Database Schema

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `tournament_teams` table | **No unique constraint untuk manager_token per tournament** | Unique [`tournament_id`, `manager_token`] tapi `manager_token` bisa duplicate across tournaments | Token tidak unique global; security weak | Token reuse possible across tournaments |
| `tournament_teams` table | **bracket_position stored tapi tidak used consistently** | Column ada tapi mostly NULL; tidak clear kapan di-populate | Bracket position logic unclear; risk of stale data | Confusion tentang when/where ini diset |
| `TournamentTeam` model (Line 14-25) | **No relationship untuk TournamentMatch** | Model tidak define relationship ke matches (home/away); harus manual query | Code harus explicitly query matches per team | Performance: N+1 queries jika list peserta dengan match count |

---

## 2.5 Participant Management View

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `participants/index.blade.php` (Line 50-75) | **Manager token display + copy/reset buttons** | Token shown plaintext di UI; copy button easy social engineering | Manager tokens visible to admin easily; bisa leak | Token compromise risk |
| `participants/create.blade.php` (Line 30-60) | **Form tidak show existing qualified_teams di UI** | User add peserta tapi tidak tau qualified_teams setting already locked | User bisa add tim lalu surprised bahwa qualified_teams immutable | Expectation mismatch |

---

# SECTION 3: STANDING/KLASEMEN

## 3.1 Standings Calculation - buildStandingsGroups()

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 2187-2245) | `buildStandingsGroups()` | **NULL scores silently cast to 0**: `$homeScore = (int) $match->home_score;` if NULL → becomes 0 | Match dengan NULL scores dianggap 0-0 tapi sebenernya error | Standings inaccurate jika ada NULL scores (data corruption case) |
| `TournamentController.php` (Line 2195-2235) | `buildStandingsGroups() - Head-to-head calc** | Head-to-head array keyed by [home_team_id][away_team_id]; bisa asymmetric | H2H points mungkin tidak match antara kedua tim | Standings bisa inconsistent antara perspective tim A vs tim B |
| `TournamentController.php` (Line 2219-2238) | `buildStandingsGroups() - Tiebreakers** | `$tieBreakers` dari `pointSettings['tiebreakers']` tapi no validation order | Jika tiebreakers include invalid key, comparison fail silently | Standings tidak sort dengan tiebreaker yang diexpect |
| `TournamentController.php` (Line 2203-2210) | `buildStandingsGroups() - Tracer logging** | Tracer log before scoring tapi tidak log hasil; only half-visibility | Log tidak show hasil calculation | Hard to debug if standings wrong |
| `TournamentController.php` (Line 2250-2280) | `buildStandingsGroups() - Group label NULL handling** | Jika `group_label` NULL, group jadi "Tanpa Grup"; bisa jadi default group | Teams tanpa group_label grouped together secara silent | Accidental grouping; user confused kenapa teams di grup salah |

---

## 3.2 Standings Display

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 2128-2185) | `standings()` view logic | **No check untuk group stage complete**: View show standings even jika belum ada full_time matches | UI show incomplete standings dengan 0 matches played | Misleading classification; user think tournament error |
| `standings.blade.php` (Line 94-120) | **"Belum ada data klasemen" message** | Message shown jika `count($groups) === 0` tapi tidak show WHY | User tidak tau penyebab: no matches, atau no completed matches | Confusing UX |
| `standings.blade.php` (Line 131-180) | **Team ranking display** | Rankings calculated per group; jika 2 groups, tim ranking 1 each group | Standing show ranking 1 but user think global ranking 1 | Confusion between group rank vs global rank |

---

## 3.3 Standings Model/Service

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 2270-2290) | `compareTeamRows()` (called but not shown in audit) | Tiebreaker comparison logic complex; possible bugs dalam custom sort | Sort order uncertain jika multiple tiebreakers apply | Standings could be wrong relative order |
| `OfficialStandingsController.php` (Line 144-180) | **Separate standings calc for official view** | Different calculation than `buildStandingsGroups()` in TournamentController | 2 standing calculation paths; bisa diverge | Official standings mungkin berbeda dari admin standings |

---

## 3.4 Standings Database/Points System

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `AppSetting` table | **Points system stored in AppSetting, tidak dedicated table** | `tournament_{id}_score_system` key in AppSetting; not normalized | Schema not clear; no type hint | Maintenance risk; hard to query point systems |
| `TournamentController.php` (Line 365-405) | `pointsSettings()` | **Default points hardcoded**: win=3, draw=1, loss=0; jika key missing, uses default | Jika AppSetting corrupted, silently uses default | Default assumption bisa wrong |

---

# SECTION 4: BRACKET TOURNAMENT

## 4.1 Bracket Generation - MatchGenerator Service

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `MatchGenerator.php` (Line 15-70) | `generateForTournament()` | **Delete all matches then insert**: `TournamentMatch::where(...).delete()` then insert | Jika ada running matches, all deleted; race condition | Live matches bisa corrupted atau deleted |
| `MatchGenerator.php` (Line 40-50) | `generateForTournament()` | **No transaction around delete + insert** | Jika insert fail, all matches deleted tapi new ones not created | Schedule jadi kosong; tournament broken |
| `MatchGenerator.php` (Line 103-130) | `generateBracketStructureForTournament()` | **Only runs if NO existing bracket matches**: `if ($existingBracketMatches) return;` | Bracket never regenerated; jika settings change, bracket stale | Group settings change tidak di-reflect di bracket |

---

## 4.2 Bracket Matches Building

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `MatchGenerator.php` (Line 260-300) | `buildBracketMatchesFromArray()` | **Team keys hardcoded (A1, A2, etc)**: `'home_team_key' => $match['left']` | Bracket relying on string keys A1, A2; jika standings calc different | Bracket teams mungkin tidak match standings | Finals mungkin jadi bye (team not found) |
| `MatchGenerator.php` (Line 382-400) | `attachBracketNextMatchIds()` | **Mapping via bracket_match_id**: Map old bracket structure to new; assumes consistency | Jika bracket regenerate, mapping bisa break | next_match_id mismatch; bracket structure broken |

---

## 4.3 Bracket Settings Management

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 443-580) | `bracketSettings()` | **Default values set every load**: If field missing, auto-set default | Silent default application; not clear yang missing | Corrupt settings auto-fix tapi user tidak tau |
| `TournamentController.php` (Line 532-560) | `bracketSettings()` | **Generate default bracket matches kesetiap load**: `generateDefaultBracketMatches()` called jika invalid/empty | Bracket regenerated every time settings accessed; performance hit | Response slow jika tournament besar |
| `AppSetting` table | **Bracket settings stored in AppSetting, multiple keys** | `matches`, `matches_promotion`, `matches_relegation` semua separate keys in JSON | Complex nested structure; hard to query | Maintenance nightmare |

---

## 4.4 Bracket Validation

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `TournamentController.php` (Line 921-945) | `isBracketStructureValid()` | **Check bracket array shape tapi not semantic validity** | Validation hanya check structure, not whether teams actually qualified | Bracket bisa reference teams yang tidak lolos | Invalid bracket structure |
| `TournamentController.php` (Line 1028-1070) | `validateAndEnsureBracketTeamConsistency()` | **Regenerate bracket if inconsistent**: Tapi regenerate bisa delete existing match data | Inconsistency detected → auto-regen → existing matches purged | User kehilangan match scores |

---

## 4.5 Bracket Next Match Linking

| FILE | FUNGSI | MASALAH | DAMPAK |
|------|--------|---------|--------|
| `MatchGenerator.php` (Line 382-400) | `attachBracketNextMatchIds()` | **Uses bracket_match_id mapping**: Assumes bracket structure stable | Jika bracket structure change, mapping bisa fail | next_match_id corrupted; winner tidak propagate ke next |
| `TournamentController.php` (Line 1944-1970) | `updateBracketForTournament()` | **Find next match via next_match_id**: `$nextMatch = TournamentMatch::find($match->next_match_id)` | Jika next_match_id NULL or broken, no update | Winner tidak propagate; playoff stuck |

---

# SECTION 5: CRITICAL INTER-DEPENDENCIES

## 5.1 Group Settings → Standings

| DEPENDENCY | FLOW | MASALAH | DAMPAK |
|-----------|------|--------|--------|
| `qualified_teams` used in standings | Settings define ranking; standings show by ranking | Jika qualified_teams change mid-tournament | Standings teams might not match bracket qualified selection |
| `teams_per_group` vs standings | Standings rank teams 1..N per group | Jika qualified_teams include ranking > teams_per_group | Standings missing teams untuk bracket |

---

## 5.2 Standings → Bracket

| DEPENDENCY | FLOW | MASALAH | DAMPAK |
|-----------|------|--------|--------|
| Standings calculate rankings | Top N teams per group → bracket positions | NULL scores silently cast to 0 | Wrong teams could be "qualified" due to 0-0 miscount |
| Bracket team keys (A1, A2) reference standings | Bracket use key A1 to find top team dari group A | Standings incomplete/wrong | Bracket jadi bye or wrong opponent |

---

## 5.3 Participants → Group Stage

| DEPENDENCY | FLOW | MASALAH | DAMPAK |
|-----------|------|--------|--------|
| Add/edit peserta trigger generateForTournament() | Participant count change → matches regenerated | Delete peserta saat matches running | All matches deleted; data loss |
| group_label assigned via generateForTournament() | Team assigned ke group A, B, C... | No validation group_label vs teams_per_group | Group bisa jadi incomplete |

---

# SECTION 6: DATA INTEGRITY RISKS

## 6.1 High Risk Scenarios

| SCENARIO | RISK | CONSEQUENCE |
|----------|------|-------------|
| **NULL scores di full_time matches** | CRITICAL | Standings calculation silently fails; wrong rankings |
| **Add/delete participant mid-tournament** | CRITICAL | Schedule regenerate; all matches deleted |
| **Change qualified_teams setelah group stage start** | HIGH | Bracket tidak update; wrong teams qualify |
| **Bracket settings update during live playoff** | HIGH | Next match IDs break; winner propagation fails |
| **Group stage incomplete but standings shown** | MEDIUM | User see incomplete data as final standings |

---

## 6.2 Data Consistency Assumptions

| ASSUMPTION | REALITY CHECK | RISK |
|-----------|---------------|------|
| `home_score` + `away_score` never NULL untuk full_time matches | ❌ Can be NULL (data corruption case) | Standings wrong |
| `group_label` always assigned before group matches | ⚠️ Null possible; grouped as "Tanpa Grup" | Silent grouping error |
| `qualified_teams` ranking ≤ `teams_per_group` | ❌ No validation; can be > | Missing teams in standings |
| `bracket_match_id` stays same across regenerations | ❌ Can change; relying on it is risky | Bracket structure break |
| No concurrent updates to participants during tournament | ❌ No locking; race condition possible | Data corruption |

---

# SECTION 7: RECOMMENDATIONS (AUDIT ONLY - NO CHANGES)

## 7.1 Recommended Validations

```
- Validate qualified_teams ranking ≤ teams_per_group
- Add NOT NULL constraint untuk home_score, away_score di full_time matches
- Check group_label assignment sebelum generating group stage
- Validate bracket_match_id stability sebelum attaching next_match_ids
```

## 7.2 Recommended Constraints

```
- Add FK constraint: group_label must exist dalam group allocation
- Add CHECK constraint: standings ranking ≤ teams_per_group
- Add unique constraint: manager_token globally (not per tournament)
```

## 7.3 Recommended Logging

```
- Log setiap standings calculation dengan final rankings
- Log setiap bracket regeneration dengan teams involved
- Log setiap participant add/delete/update dengan timestamp
- Log bracket team resolution (A1 → Team Name)
```

## 7.4 Recommended Testing

```
- Test case: NULL scores in standings
- Test case: Add participant during live group stage
- Test case: Change qualified_teams mid-tournament
- Test case: Bracket next_match_id linking
- Test case: Standings ranking consistency
```

---

# APPENDIX: DATABASE SCHEMA OVERVIEW

## Tables Involved

| TABLE | PURPOSE | KEY COLUMNS | RELATIONSHIPS |
|-------|---------|-------------|---------------|
| `tournaments` | Tournament master | id, name, status | 1-N tournaments_teams |
| `tournament_group_settings` | Group config | tournament_id, teams_per_group, qualified_teams, relegated_teams | 1-1 tournaments |
| `tournament_teams` | Participants | tournament_id, team_id, group_label, bracket_position | N-1 teams, 1-N matches |
| `tournament_team_players` | Squad | tournament_team_id, player_name, shirt_number | N-1 tournament_teams |
| `tournament_team_officials` | Officials | tournament_team_id, role, name | N-1 tournament_teams |
| `tournament_matches` | Matches | tournament_id, home_team_id, away_team_id, home_score, away_score, status | 1-1 tournaments, N-1 teams |
| `app_settings` | Dynamic configs | key, value (JSON) | N/A (key-value) |

## Critical Columns

| COLUMN | TABLE | TYPE | NULL | ISSUES |
|--------|-------|------|------|--------|
| `home_score` | tournament_matches | INT | YES ⚠️ | Should NOT NULL for full_time |
| `away_score` | tournament_matches | INT | YES ⚠️ | Should NOT NULL for full_time |
| `group_label` | tournament_teams | VARCHAR | YES | Grouping logic unclear |
| `qualified_teams` | tournament_group_settings | JSON | NO | Array but no validation |
| `bracket_position` | tournament_teams | VARCHAR | YES | Mostly unused |

---

**END OF AUDIT REPORT**
