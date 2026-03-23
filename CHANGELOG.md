# Changelog — Post Git Pull Updates

**Date:** March 23, 2026

---

## 1. Photo Crop & Rotate UI (Cropper.js Integration)

**File:** `resources/views/chatbot.blade.php`

- Added Cropper.js CSS and JS via CDN (`<link>` and `<script>` tags).
- Added a full-screen crop modal with controls: **Rotate Left/Right**, **Flip Horizontal/Vertical**, **Zoom In/Out**, and **Reset**.
- Aspect ratio locked to **137:136** to match the ID card photo dimensions.
- Modified `handlePhotoFile()` to open the crop modal after photo selection/capture instead of directly processing.
- On confirm, the cropped image is exported as a 400×400 JPEG and sent for validation, then continues the normal upload flow.

---

## 2. Card Regeneration Fix (API-Based Member Data Fetching)

### Problem
When a user skipped details during registration and later filled them (via QR scan or website), old card images were deleted from Cloudinary but **new cards were never generated**. This happened because `card/view.blade.php` relied solely on `localStorage` for member data — which doesn't exist on a different device.

### Fix

**File:** `resources/views/card/view.blade.php`
- Modified `getMember()` to accept a `?uid=` query parameter.
- When `uid` is present, fetches member data from the backend API (`GET /api/vanigam/member/{uid}`) instead of localStorage.
- Falls back to localStorage if no `uid` param is provided.
- Updated initialization to async-load member data before triggering `autoSaveCardImages()`.

**File:** `resources/views/chatbot.blade.php`
- Updated `doSaveUpdatedDetails()` to pass `&uid={unique_id}` to the card-view iframe URL.
- Updated `doGenerateCard()` to pass `&uid={unique_id}` to the card-view iframe URL.

**File:** `resources/views/member/complete.blade.php`
- Updated card regeneration iframe URL to include `&uid={{ $unique_id }}` parameter.

**File:** `routes/api.php`
- Removed `validate.admin.api.key` middleware from the `/upload-card-images` route to allow client-side iframe uploads without requiring the admin API key header. Kept `throttle` middleware for rate limiting.

---

## 3. Cloudinary Configuration Fix

### Problem
The `VanigamController` constructor used `config('cloudinary.url')` to initialize the Cloudinary SDK, but no `config/cloudinary.php` file existed in the project. This caused a **500 Internal Server Error** on all API endpoints because Cloudinary received `null` as its configuration.

### Fix

**File (New):** `config/cloudinary.php`
- Created the Cloudinary config file mapping `CLOUDINARY_URL` env var to the `cloud_url` config key (matching the `cloudinary-laravel` package convention).

**File:** `app/Http/Controllers/VanigamController.php`
- Changed `config('cloudinary.url')` → `config('cloudinary.cloud_url')` on line 27.

---

## 4. Completed Details Redirect

### Problem
When a user with `details_completed: true` visited `/member/complete/{uniqueId}` (e.g., from an old QR code that hadn't been updated), they saw a static "All Details Complete!" message instead of the interactive 3D card view.

### Fix

**File:** `app/Http/Controllers/VanigamController.php`
- Added a redirect in `completeDetails()`: if the member's details are already completed, automatically redirect to `/member/verify/{uniqueId}` which shows the PIN verification → 3D card view.

---

## 5. Production Server Deployment (Cloudways)

**Server:** `165.22.223.28` (Cloudways)

### Files deployed:
- All modified files listed above were uploaded via SCP and synced to `/home/1604344.cloudwaysapps.com/xwxwrwacam/private_html/`.

### Production-only changes:
- **`public_html/index.php`** — Restored to correctly point to `private_html/` app base path (was broken during rsync deployment).
- **`.env`** — Updated `TWO_FACTOR_API_KEY` to new key `32ceb23b-269a-11f1-bcb0-0200cd936042`.

### Cache operations:
- `php artisan config:cache` — Rebuilt configuration cache.
- `php artisan route:cache` — Rebuilt route cache.

---

## 6. Data Cleanup

- Deleted member **TNVS-3D4ED0** (Suresh, EPIC: AYR0531673, mobile: 8106811285):
  - Removed member photo from Cloudinary (`vanigan/member_photos/AYR0531673_1774004786`).
  - Removed cards folder from Cloudinary (`vanigan/cards/TNVS-3D4ED0`).
  - Deleted member record from MongoDB.

---

## 7. MongoDB `created_at` Serialization Fix

### Problem
`generateCard()` stored `created_at` as `now()` (a Laravel Carbon object). The MongoDB PHP driver **doesn't know how to serialize Carbon**, so it stored it as an empty BSON object `{}` instead of a proper date. This caused:
- **"Today" / "This Week" / "This Month" member counts** always returned **0** in the admin dashboard.
- **"Recent Members" list** showed in random order instead of newest-first.
- **"Members List"** pagination sorted incorrectly.

### Fix

**File:** `app/Http/Controllers/VanigamController.php`
- Changed `'created_at' => now()` → `'created_at' => now()->toISOString()` so future records store a proper ISO date string.

**File:** `app/Services/MongoService.php`
- Changed all date-based queries (`membersToday`, `membersThisWeek`, `membersThisMonth`) to use MongoDB `_id` ObjectId timestamp instead of `created_at`.
- Changed all `sort` operations from `'created_at' => -1` to `'_id' => -1` across `getAllMembers`, `getMembersReferredBy`, `getStats`, `findAllMembersByEpic`.

### Data Migration
- Ran a one-time script on the server that fixed all **27 existing records** — extracted each document's real creation timestamp from its `_id` ObjectId and wrote it back as a proper ISO string.

---

## 8. 2Factor API Key Update

- Updated `TWO_FACTOR_API_KEY` in production `.env` from old key to `32ceb23b-269a-11f1-bcb0-0200cd936042`.
- Rebuilt config cache on server.

---

## 9. Audit Bug Fixes (3 Critical Items)

### 9a. `storeManualEntry` overwrites `created_at` on update

**File:** `app/Services/MongoService.php`

**Bug:** When updating an existing manual entry, the full `$data` array (including `created_at`) was merged into `$set`, overwriting the original creation date with the current time.

**Fix:** Removed `created_at` from `$data` before the `$set` update. `created_at` is now only set on insert (new entries).

### 9b. `getAllManualEntries` sorted by broken `created_at`

**File:** `app/Services/MongoService.php`

**Bug:** `manual_entries` collection sorted by `created_at: -1`, which would break if entries had empty `{}` dates.

**Fix:** Changed to `_id: -1` for reliable sort order.

### 9c. `loanRequest` used inconsistent date format

**File:** `app/Http/Controllers/VanigamController.php`

**Bug:** Loan requests stored `created_at` as `new \MongoDB\BSON\UTCDateTime(...)` while every other collection used ISO strings. This caused inconsistent date formats returned to the frontend.

**Fix:** Changed to `now()->toISOString()` for consistency.

---

## Summary of All Changed Files

| # | File | Action |
|---|------|--------|
| 1 | `resources/views/chatbot.blade.php` | Modified |
| 2 | `resources/views/card/view.blade.php` | Modified |
| 3 | `resources/views/member/complete.blade.php` | Modified |
| 4 | `routes/api.php` | Modified |
| 5 | `app/Http/Controllers/VanigamController.php` | Modified |
| 6 | `app/Services/MongoService.php` | Modified |
| 7 | `config/cloudinary.php` | **New file** |
| 8 | `public_html/index.php` (production only) | Restored |
| 9 | `.env` (production only) | Updated |
