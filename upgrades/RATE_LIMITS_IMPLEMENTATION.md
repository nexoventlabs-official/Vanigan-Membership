# Rate Limits Implementation - Option C (Named Limits)

**Status:** Awaiting confirmation before implementation
**Branch:** trial-staging
**Date:** March 21, 2026

---

## Overview

This document outlines the complete implementation of Laravel's built-in throttle middleware with named rate limit groups for all public API endpoints.

**Files to be created/modified:**
1. **NEW:** `config/rate-limits.php` (named limit groups)
2. **MODIFIED:** `routes/api.php` (apply throttle middleware)
3. **NO CHANGES:** Controllers, middleware, or other files

---

## Rate Limit Groups

| Group | Limit | Period | Use Case | Endpoints |
|-------|-------|--------|----------|-----------|
| `otp` | 50 | 5 min | OTP endpoints - DDoS + brute force | check-member, send-otp, verify-otp |
| `pin` | 10 | 5 min | PIN verification - 4-digit brute force | verify-pin, verify-member-pin |
| `card_generation` | 15 | 5 min | Resource intensive - QR + images | generate-card, save-details |
| `validation` | 40 | 5 min | Photo uploads + MySQL lookups | validate-epic, upload-photo, validate-photo |
| `member_read` | 200 | 1 min | Read-only, lightweight, high frequency | member/{uniqueId}, qr/{uniqueId} |
| `referral_loan` | 30 | 5 min | Standard user operations | get-referral, increment-referral, loan-request, check-loan-status |
| `admin_protected` | 10 | 5 min | Destructive ops (secondary to API key) | reset-members, upload-card-images |

---

## NEW FILE: `config/rate-limits.php` (Documentation Only)

**Location:** `D:\Cloudways\project-5\config\rate-limits.php`

**Purpose:** Reference documentation for rate limit values used in routes. Not referenced at runtime.

```php
<?php

/**
 * Rate Limiting Configuration (Documentation Reference)
 *
 * Defines rate limit groups for API endpoints.
 * Format: 'requests_per_period,period_in_minutes'
 *
 * These values are hardcoded in routes/api.php for reliability.
 * This file serves as a central reference for all throttle limits.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limit Groups
    |--------------------------------------------------------------------------
    | Each group defines requests allowed per time period for related endpoints
    | Values are hardcoded in routes/api.php for direct reliability
    */

    // OTP endpoints (send/verify) - DDoS + brute force protection
    // 50 requests per 5 minutes per IP
    'otp' => '50,5',

    // PIN verification - Brute force protection for 4-digit codes
    // 10 requests per 5 minutes per IP
    'pin' => '10,5',

    // Card generation - Resource intensive (generates QR + images)
    // 15 requests per 5 minutes per IP
    'card_generation' => '15,5',

    // Validation endpoints - Resource intensive (uploads, MySQL lookups)
    // 40 requests per 5 minutes per IP
    'validation' => '40,5',

    // Member read operations - Lightweight, read-only, high frequency
    // 200 requests per 1 minute per IP
    'member_read' => '200,1',

    // Referral & Loan operations - Standard user operations
    // 30 requests per 5 minutes per IP
    'referral_loan' => '30,5',

    // Admin protected endpoints - Already behind API key; secondary protection
    // 10 requests per 5 minutes per IP
    'admin_protected' => '10,5',
];
```

---

## MODIFIED FILE: `routes/api.php`

**Location:** `D:\Cloudways\project-5\routes\api.php`

### BEFORE (Current State)

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| API Routes - Tamil Nadu Vanigargalin Sangamam
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', [VanigamController::class, 'health']);

// Tamil Nadu Vanigargalin Sangamam API
Route::prefix('vanigam')->group(function () {
    // OTP via 2Factor.in Voice Call
    Route::post('/check-member', [VanigamController::class, 'checkMember']);
    Route::post('/send-otp', [VanigamController::class, 'sendOtp']);
    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp']);

    // EPIC Voter Lookup (MySQL read-only)
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic']);

    // Photo upload to Cloudinary
    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto']);

    // Validate photo before PIN setup
    Route::post('/validate-photo', [VanigamController::class, 'validatePhotoUpload']);

    // Generate membership card & save to MongoDB
    Route::post('/generate-card', [VanigamController::class, 'generateCard']);

    // Save additional details (from chat or QR form)
    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails']);

    // Get member info
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember']);

    // QR code image (generated on-the-fly)
    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr']);

    // Reset MongoDB members (does NOT touch MySQL)
    Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
        ->middleware('validate.admin.api.key');

    // Upload card images to Cloudinary
    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages'])
        ->middleware('validate.admin.api.key');

    // Verify returning user PIN
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin']);

    // Verify member PIN for QR scan
    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin']);

    // Referral
    Route::post('/get-referral', [VanigamController::class, 'getReferral']);
    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral']);

    // Loan Request
    Route::post('/loan-request', [VanigamController::class, 'loanRequest']);
    Route::post('/check-loan-status', [VanigamController::class, 'checkLoanStatus']);
});
```

### AFTER (With Hardcoded Rate Limits Applied)

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| API Routes - Tamil Nadu Vanigargalin Sangamam
|--------------------------------------------------------------------------
*/

// Health check (no throttle - monitoring/internal use)
Route::get('/health', [VanigamController::class, 'health']);

// Tamil Nadu Vanigargalin Sangamam API
Route::prefix('vanigam')->group(function () {
    // === OTP Endpoints - DDoS + Brute Force Protection ===
    // Rate limit: 50 per 5 minutes
    Route::post('/check-member', [VanigamController::class, 'checkMember'])
        ->middleware('throttle:50,5');

    Route::post('/send-otp', [VanigamController::class, 'sendOtp'])
        ->middleware('throttle:50,5');

    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp'])
        ->middleware('throttle:50,5');

    // === Validation Endpoints - Uploads & Lookups ===
    // Rate limit: 40 per 5 minutes
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic'])
        ->middleware('throttle:40,5');

    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto'])
        ->middleware('throttle:40,5');

    Route::post('/validate-photo', [VanigamController::class, 'validatePhotoUpload'])
        ->middleware('throttle:40,5');

    // === Card Generation - Resource Intensive ===
    // Rate limit: 15 per 5 minutes
    Route::post('/generate-card', [VanigamController::class, 'generateCard'])
        ->middleware('throttle:15,5');

    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails'])
        ->middleware('throttle:15,5');

    // === Member Read Operations - Lightweight ===
    // Rate limit: 200 per 1 minute
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember'])
        ->middleware('throttle:200,1');

    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr'])
        ->middleware('throttle:200,1');

    // === Admin Protected Endpoints - API Key + Secondary Rate Limit ===
    // Rate limit: 10 per 5 minutes
    Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:10,5',
        ]);

    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:10,5',
        ]);

    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:10,5');

    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:10,5');

    // === Referral & Loan - Standard User Operations ===
    // Rate limit: 30 per 5 minutes
    Route::post('/get-referral', [VanigamController::class, 'getReferral'])
        ->middleware('throttle:30,5');

    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral'])
        ->middleware('throttle:30,5');

    Route::post('/loan-request', [VanigamController::class, 'loanRequest'])
        ->middleware('throttle:30,5');

    Route::post('/check-loan-status', [VanigamController::class, 'checkLoanStatus'])
        ->middleware('throttle:30,5');
});
```

---

## Key Changes Summary

| Aspect | Details |
|--------|---------|
| **New File** | `config/rate-limits.php` — Documentation reference (7 limit groups) |
| **Modified File** | `routes/api.php` — Hardcoded throttle values on 14 endpoints |
| **Health Endpoint** | No throttle applied (monitoring/internal use) |
| **Admin Endpoints** | Throttle (10,5) + API key middleware (both applied) |
| **Controllers** | No changes |
| **Other Middleware** | No changes |
| **Runtime** | Throttle values are hardcoded strings, no config loading |

---

## Middleware Behavior

### Throttle Middleware Format
- **Syntax:** `throttle:requests,minutes` (hardcoded in routes)
- **Example:** `throttle:50,5` = 50 requests per 5 minutes per IP
- **Implementation:** Direct string values in route definitions for reliability and performance

### Hardcoded Values vs Config Reference
**Why hardcoded?**
- No config loading overhead at runtime
- No potential config caching issues
- Explicit values visible in route definitions
- Config file serves as documentation/reference only

### Admin Protected Routes (Dual Middleware)
Admin endpoints get both middlewares in order:

```php
->middleware([
    'validate.admin.api.key',      // Checked first (HTTP 401 if no key)
    'throttle:10,5',               // Checked second (HTTP 429 if exceeded)
])
```

### Rate Limit Responses
- **HTTP 429 Too Many Requests** when limit exceeded
- **Headers:** `RateLimit-Limit`, `RateLimit-Remaining`, `RateLimit-Reset`
- **Shared by IP address** (not per user, since endpoints are public)

---

## Testing Recommendations

After deployment to trial server:

1. **Test OTP endpoint throttle (50 per 5 min):**
   ```bash
   # Send 51 rapid requests to /api/vanigam/send-otp
   # Request 51 should return HTTP 429
   ```

2. **Test PIN endpoint throttle (10 per 5 min):**
   ```bash
   # Send 11 rapid requests to /api/vanigam/verify-pin
   # Request 11 should return HTTP 429
   ```

3. **Test health check (no throttle):**
   ```bash
   # Send many requests to /api/health
   # Should never return HTTP 429
   ```

4. **Test member read (200 per min):**
   ```bash
   # Send requests to /api/vanigam/member/{id}
   # Up to 200 should succeed within same minute
   ```

---

## Configuration Notes

### Reference vs Runtime
- **config/rate-limits.php** — Documentation reference only (not loaded at runtime)
- **routes/api.php** — Hardcoded throttle values (actual implementation)
- Adjusting limits: Edit `routes/api.php` throttle strings directly

### Easy Maintenance
- All limits visible in one route file with clear comments
- Easy to adjust: change `'throttle:50,5'` to `'throttle:100,5'` in route definition
- No config cache issues or loading overhead
- Clear what each endpoint is rate limited to

### Per-Environment
- Currently same limits for trial and production
- To use different limits per environment: add conditional logic in routes/api.php
- For now, keeping simple with hardcoded values

### Cache Storage
- Throttle uses Redis (if available) or cache store (file-based fallback via CacheService)
- Already integrated with existing cache infrastructure

---

## Files Affected

| File | Action | Impact |
|------|--------|--------|
| `config/rate-limits.php` | **CREATE** | New configuration file |
| `routes/api.php` | **MODIFY** | Add throttle middleware to 14 routes |
| Controllers | **NONE** | No changes |
| Middleware | **NONE** | No changes |
| Other configs | **NONE** | No changes |

---

## Rollback Plan

If issues occur:
1. Remove throttle middleware strings from `routes/api.php` (revert to original file)
2. Optionally delete `config/rate-limits.php` (documentation only, no runtime effect)
3. No config cache clearing needed (no config loading used)
4. Restart queue workers (if applicable)

---

## CLAUDE.md Compliance

✅ **Follows all guidelines:**
- Works on `trial-staging` branch
- No changes to controllers or critical middleware
- No modifications to security-critical code
- Adds layer on top of existing validation
- Documented in config file
- Easy to test and rollback

---

**Ready for confirmation? Please review and confirm before implementation.**
