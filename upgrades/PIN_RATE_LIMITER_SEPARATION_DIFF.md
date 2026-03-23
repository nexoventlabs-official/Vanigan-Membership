# PIN Rate Limiter Separation - Complete Diff

**Status:** Diff prepared - Ready for review before implementation
**Date:** March 22, 2026
**Branch:** trial-staging

---

## Changes Summary

Separate the shared `pin_verify` rate limiter into two independent limiters:
- `pin_login` - For `/api/vanigam/verify-pin` (login workflow)
- `pin_scan` - For `/api/vanigam/verify-member-pin` (QR scan workflow)

Both: **10 requests per 5 minutes per IP**

---

## File 1: `routes/api.php`

### Current Code (Lines 70-76)

```php
    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:pin_verify');

    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:pin_verify');
```

### Changed Code (Lines 70-76)

```php
    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes (login workflow)
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:pin_login');

    // Rate limit: 10 per 5 minutes (QR scan workflow)
    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:pin_scan');
```

### Diff

```diff
     // === PIN Verification - Brute Force Protection ===
-    // Rate limit: 10 per 5 minutes
+    // Rate limit: 10 per 5 minutes (login workflow)
     Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
-        ->middleware('throttle:pin_verify');
+        ->middleware('throttle:pin_login');

+    // Rate limit: 10 per 5 minutes (QR scan workflow)
     Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
-        ->middleware('throttle:pin_verify');
+        ->middleware('throttle:pin_scan');
```

---

## File 2: `app/Providers/RouteServiceProvider.php`

### Current Code (Lines 33-35)

```php
        RateLimiter::for('pin_verify', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });
```

### Changed Code (Lines 33-40)

```php
        RateLimiter::for('pin_login', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('pin_scan', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });
```

### Diff

```diff
-        RateLimiter::for('pin_verify', function (Request $request) {
+        RateLimiter::for('pin_login', function (Request $request) {
+            return Limit::perMinutes(5, 10)->by($request->ip());
+        });
+
+        RateLimiter::for('pin_scan', function (Request $request) {
             return Limit::perMinutes(5, 10)->by($request->ip());
         });
```

---

## Complete File Diffs

### routes/api.php (Full Context)

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
        ->middleware('throttle:otp');

    Route::post('/send-otp', [VanigamController::class, 'sendOtp'])
        ->middleware('throttle:otp');

    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp'])
        ->middleware('throttle:otp');

    // === Validation Endpoints - Uploads & Lookups ===
    // Rate limit: 40 per 5 minutes
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic'])
        ->middleware('throttle:validation');

    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto'])
        ->middleware('throttle:validation');

    Route::post('/validate-photo', [VanigamController::class, 'validatePhotoUpload'])
        ->middleware('throttle:validation');

    // === Card Generation - Resource Intensive ===
    // Rate limit: 15 per 5 minutes
    Route::post('/generate-card', [VanigamController::class, 'generateCard'])
        ->middleware('throttle:card_generation');

    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails'])
        ->middleware('throttle:card_generation');

    // === Member Read Operations - Lightweight ===
    // Rate limit: 200 per 1 minute
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember'])
        ->middleware('throttle:member_read');

    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr'])
        ->middleware('throttle:member_read');

    // === Admin Protected Endpoints - API Key + Secondary Rate Limit ===
    // Rate limit: 10 per 5 minutes
    Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:admin_reset',
        ]);

    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:admin_upload',
        ]);

    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes (login workflow)
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:pin_login');

    // Rate limit: 10 per 5 minutes (QR scan workflow)
    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:pin_scan');

    // === Referral & Loan - Standard User Operations ===
    // Rate limit: 30 per 5 minutes
    Route::post('/get-referral', [VanigamController::class, 'getReferral'])
        ->middleware('throttle:referral_loan');

    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral'])
        ->middleware('throttle:referral_loan');

    Route::post('/loan-request', [VanigamController::class, 'loanRequest'])
        ->middleware('throttle:referral_loan');

    Route::post('/check-loan-status', [VanigamController::class, 'checkLoanStatus'])
        ->middleware('throttle:referral_loan');
});
```

### app/Providers/RouteServiceProvider.php (Full Context)

```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinutes(5, 50)->by($request->ip());
        });

        RateLimiter::for('validation', function (Request $request) {
            return Limit::perMinutes(5, 40)->by($request->ip());
        });

        RateLimiter::for('card_generation', function (Request $request) {
            return Limit::perMinutes(5, 15)->by($request->ip());
        });

        RateLimiter::for('member_read', function (Request $request) {
            return Limit::perMinute(200)->by($request->ip());
        });

        RateLimiter::for('pin_login', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('pin_scan', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('admin_reset', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('admin_upload', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('referral_loan', function (Request $request) {
            return Limit::perMinutes(5, 30)->by($request->ip());
        });
    }
}
```

---

## Impact Analysis

### Benefits ✅

| Benefit | Details |
|---------|---------|
| **Independent rate limiting** | Each workflow has its own 10/5min quota |
| **Better UX** | Failed login attempts don't block QR scans |
| **Security isolation** | Different attack vectors handled separately |
| **Semantic clarity** | Limiter names reflect use case (login vs scan) |
| **Frontend flexibility** | Can tune each workflow independently |
| **No breaking changes** | Only middleware name changes, controller logic unchanged |

### Affected Endpoints

| Endpoint | Previous Limiter | New Limiter | Impact |
|----------|------------------|-------------|--------|
| `POST /api/vanigam/verify-pin` | `pin_verify` (shared) | `pin_login` (independent) | ✅ Gets own 10/5min quota |
| `POST /api/vanigam/verify-member-pin` | `pin_verify` (shared) | `pin_scan` (independent) | ✅ Gets own 10/5min quota |

### Backward Compatibility

- ✅ No controller code changes
- ✅ No request/response format changes
- ✅ No HTTP status codes change
- ✅ No error message changes
- ✅ Route URLs unchanged
- ✅ Rate limits unchanged (still 10 per 5 minutes)
- ⚠️ Rate limiter counter now separate (users can try each endpoint 10 times independently instead of sharing 10 total)

---

## Testing Scenarios After Implementation

### Scenario 1: Login Workflow Rate Limit
```bash
# User attempts login PIN verification 10 times
# All 10 should succeed (pin_login limiter has 10/5min quota)
# Request 11 should be rate limited (429)
curl -X POST https://...api.../api/vanigam/verify-pin \
  -H "Content-Type: application/json" \
  -d '{"mobile":"9876543210","pin":"1234"}'
```

### Scenario 2: QR Scan Workflow Rate Limit
```bash
# User attempts QR PIN verification 10 times
# All 10 should succeed (pin_scan limiter has 10/5min quota)
# Request 11 should be rate limited (429)
curl -X POST https://...api.../api/vanigam/verify-member-pin \
  -H "Content-Type: application/json" \
  -d '{"unique_id":"MEMBER_001","pin":"1234"}'
```

### Scenario 3: Independent Rate Limits
```bash
# User does 10 login attempts (pin_login: 10/10 used)
# User then does 10 QR scans (pin_scan: 10/10 used)
# Both should succeed independently
# Request 11 on each should be rate limited
```

---

## Rollback Plan (If Needed)

To revert to shared limiter:

**routes/api.php:**
```php
Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
    ->middleware('throttle:pin_verify');

Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
    ->middleware('throttle:pin_verify');
```

**RouteServiceProvider.php:**
```php
RateLimiter::for('pin_verify', function (Request $request) {
    return Limit::perMinutes(5, 10)->by($request->ip());
});
```

---

## Summary

| Aspect | Current | After Change |
|--------|---------|--------------|
| **pin_verify limiter** | 1 (shared) | Removed |
| **pin_login limiter** | ❌ None | ✅ 10/5min |
| **pin_scan limiter** | ❌ None | ✅ 10/5min |
| **Total rate quota per IP** | 10/5min (shared) | 20/5min (10 each, independent) |
| **User experience** | Login blocks QR scans | Each workflow independent |

---

## Ready for Implementation ✅

All changes are:
- ✅ Minimal (2 files, small changes)
- ✅ Safe (no breaking changes)
- ✅ Beneficial (independent rate limiting)
- ✅ Well-documented
- ✅ Easily reversible

**Proceed with implementation? (Y/N)**
