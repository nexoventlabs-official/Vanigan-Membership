# Repo Audit Report — Silent Failures & Bugs

**Date:** March 23, 2026  
**Scanned:** All controllers, services, helpers, config, routes, and .env

---

## 🔴 Critical (Actively Broken)

### 1. `storeManualEntry` overwrites `created_at` on update

**File:** `app/Services/MongoService.php` — lines 643–655  
**Status:** ✅ Fixed (March 23, 2026)

**Bug:** When updating an existing manual entry, the full `$data` array (including `created_at`) is merged into `$set`, overwriting the original creation date with the current time. The `created_at` should only be set on insert, not on update.

**Impact:** Manual entry records lose their original creation timestamp every time they're updated.

**Fix:** Remove `created_at` from `$data` before the `$set` update, or use `$setOnInsert` for `created_at` like `upsertMember` does.

---

### 2. `getAllManualEntries` sorts by `created_at` instead of `_id`

**File:** `app/Services/MongoService.php` — line 702  
**Status:** ✅ Fixed (March 23, 2026)

**Bug:** `manual_entries` collection sort uses `'sort' => ['created_at' => -1]`. If any manual entries were created before the `now()` → `now()->toISOString()` fix, their `created_at` would be an empty BSON object `{}`, causing incorrect sort order.

**Impact:** Admin manual entries list may show in wrong order.

**Fix:** Change to `'sort' => ['_id' => -1]`.

---

### 3. `loanRequest` uses `UTCDateTime` while rest of project uses ISO strings

**File:** `app/Http/Controllers/VanigamController.php` — line 1054  
**Status:** ✅ Fixed (March 23, 2026)

**Bug:** Loan requests store `created_at` as `new \MongoDB\BSON\UTCDateTime(...)` while every other MongoDB collection uses `now()->toISOString()`. This format inconsistency means `checkLoanStatus` returns a different date format to the frontend.

**Impact:** Loan request dates display differently from other dates. May cause frontend parsing issues.

**Fix:** Change to `'created_at' => now()->toISOString()`.

---

## 🟡 Medium (Potential Issues)

### 4. `CloudinaryService.php` uses old Facade, not direct SDK

**File:** `app/Services/CloudinaryService.php`  
**Status:** ⚠️ Not actively used

**Details:** Uses `CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary` while `VanigamController` uses the direct SDK `new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'))`. The Facade depends on the `cloudinary-laravel` service provider which needs `config('cloudinary.cloud_url')` — this config was missing before our fix. If this service is ever called, it might silently fail.

**Impact:** None currently — not used by any active controller. But a risk if someone wires it in.

**Fix:** Either remove the unused service or update it to use the direct SDK.

---

### 5. `CacheService` always tries Redis first, even when `CACHE_STORE=file`

**File:** `app/Services/CacheService.php`  
**Env:** `.env` has `CACHE_STORE=file`, `CACHE_DRIVER=file`

**Details:** `CacheService` hardcodes `Cache::store('redis')` as the primary store. Every cache operation tries Redis first, catches the exception, logs a warning, then falls back to file cache.

**Impact:** Adds latency to every cache operation and spams log files with Redis fallback warnings. Not broken — fallback works.

**Fix:** Make CacheService respect the configured cache driver, or set `CACHE_STORE=redis` in `.env` if Redis should be primary.

---

### 6. `StatisticsHelper` queries MySQL tables that may not exist

**File:** `app/Helpers/StatisticsHelper.php`

**Details:** References Eloquent models: `GeneratedVoter`, `GenerationStat`, `OtpSession`, `VolunteerRequest`, `BoothAgentRequest`. If these MySQL tables don't exist on production, all queries silently return 0.

**Impact:** Admin API dashboard stats (`/admin/dashboard` via `AdminController`) would show all zeros for these MySQL-based stats. The MongoDB-based stats from `MongoService::getStats()` work fine.

**Fix:** Verify these tables exist on production, or remove/disable the MySQL-based stats if they're not applicable.

---

## 🟢 Low (Code Quality / Minor)

### 7. `findDuplicateEpics` projects `created_at` which may have been `{}`

**File:** `app/Services/MongoService.php` — line 561

**Details:** The aggregation pipeline pushes `created_at` into results. Now fixed by the data migration, but if any new records are created with the raw `now()` Carbon pattern (e.g., by a different code path), it would show `{}` again.

**Impact:** Cosmetic — admin duplicate detection would show empty dates.

---

### 8. `VoterHelper::getOrCreateReferral` queries `generated_voters` MySQL table

**File:** `app/Helpers/VoterHelper.php` — lines 317–354

**Details:** References a MySQL `generated_voters` table and `ptc_code` — leftover from the old Python Flask app. The Vanigam system uses MongoDB's `referral_id` instead.

**Impact:** None — not called by any active route. Dead code.

**Fix:** Remove or mark as deprecated.

---

### 9. `GenerateCardJob` uses raw `now()` for MySQL Eloquent

**File:** `app/Jobs/GenerateCardJob.php` — lines 115, 124, 131

**Details:** Uses `now()` for `generated_at`, `last_generated`, `verified_at` — but these go into **MySQL Eloquent** models (not MongoDB), so Laravel handles Carbon serialization correctly.

**Impact:** None — correct behavior for Eloquent/MySQL.

---

## ✅ Already Fixed (This Session)

| # | Issue | Fix |
|---|-------|-----|
| 1 | `created_at` stored as empty `{}` (Carbon → BSONDocument) | Changed `now()` → `now()->toISOString()` in `generateCard()` |
| 2 | `getStats` today/week/month counts always 0 | Changed to use `_id` ObjectId for date filtering |
| 3 | Recent members sorted incorrectly | Changed all sorts from `created_at: -1` to `_id: -1` |
| 4 | 27 existing records had broken `created_at` | Ran migration script to extract timestamps from `_id` |
| 5 | `config/cloudinary.php` missing | Created the file |
| 6 | `VanigamController` used wrong config key | `config('cloudinary.url')` → `config('cloudinary.cloud_url')` |
| 7 | `/member/complete` showed "All Details Complete" instead of 3D card | Added redirect to `/member/verify` when details completed |
| 8 | 2Factor API key outdated on production | Updated `.env` on server |

---

## Recommended Priority

1. **Fix #1** — `storeManualEntry` overwrites `created_at`
2. **Fix #2** — `getAllManualEntries` sort by `_id`
3. **Fix #3** — Loan request `created_at` format consistency
4. **Fix #5** — CacheService Redis-first when file is configured (performance)
5. **Fix #4, #6, #8** — Cleanup dead code and verify MySQL tables
