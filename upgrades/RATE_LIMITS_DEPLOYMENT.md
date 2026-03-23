# Rate Limits Implementation - Deployment & Testing Guide

**Status:** ✅ IMPLEMENTED & PUSHED TO trial-staging
**Date:** March 21, 2026
**Branch:** trial-staging
**Commit:** b4cd780

---

## 📊 Implementation Summary

Laravel's built-in throttle middleware has been successfully added to all public API endpoints with hardcoded rate limit values for reliability and performance.

### Files Changed

| File | Status | Lines | Changes |
|------|--------|-------|---------|
| `config/rate-limits.php` | ✅ CREATED | 49 | New documentation reference file |
| `routes/api.php` | ✅ MODIFIED | 92 | Added throttle middleware to 14 endpoints |

### Git Status

```
Commit: b4cd780
Message: "security: add Laravel throttle middleware to all API endpoints"
Branch: trial-staging
Status: Pushed to origin/trial-staging
```

---

## 🎯 Endpoint Rate Limits Applied

### OTP Endpoints (50 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/check-member`
- `POST /api/vanigam/send-otp`
- `POST /api/vanigam/verify-otp`

**Middleware:** `throttle:50,5`
**Rationale:** DDoS + general brute force protection on authentication endpoints

---

### Validation Endpoints (40 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/validate-epic` (MySQL lookup)
- `POST /api/vanigam/upload-photo` (Cloudinary)
- `POST /api/vanigam/validate-photo` (pre-upload validation)

**Middleware:** `throttle:40,5`
**Rationale:** Resource-intensive operations (uploads, MySQL batch lookups)

---

### Card Generation Endpoints (15 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/generate-card`
- `POST /api/vanigam/save-details`

**Middleware:** `throttle:15,5`
**Rationale:** Most resource-intensive (generates QR codes, card images)

---

### Member Read Endpoints (200 per 1 minute)
**Endpoints:**
- `GET /api/vanigam/member/{uniqueId}`
- `GET /api/vanigam/qr/{uniqueId}`

**Middleware:** `throttle:200,1`
**Rationale:** Read-only, lightweight, high frequency expected

---

### PIN Verification Endpoints (10 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/verify-pin`
- `POST /api/vanigam/verify-member-pin`

**Middleware:** `throttle:10,5`
**Rationale:** Strict limit - protects against 4-digit PIN brute force attacks

---

### Referral & Loan Endpoints (30 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/get-referral`
- `POST /api/vanigam/increment-referral`
- `POST /api/vanigam/loan-request`
- `POST /api/vanigam/check-loan-status`

**Middleware:** `throttle:30,5`
**Rationale:** Standard user operations, moderate frequency

---

### Admin Protected Endpoints (10 per 5 minutes)
**Endpoints:**
- `POST /api/vanigam/reset-members`
- `POST /api/vanigam/upload-card-images`

**Middleware Stack:**
```php
[
    'validate.admin.api.key',      // Checked first (HTTP 401)
    'throttle:10,5',               // Checked second (HTTP 429)
]
```

**Rationale:** Destructive operations. API key validates first, throttle provides secondary protection.

---

### Health Check Endpoint (NO THROTTLE)
**Endpoint:**
- `GET /api/health`

**Middleware:** None
**Rationale:** Monitoring/internal use - should always respond, no rate limiting

---

## 📝 File Details

### config/rate-limits.php (Documentation Reference)

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
    // OTP endpoints - 50 per 5 minutes
    'otp' => '50,5',

    // PIN verification - 10 per 5 minutes
    'pin' => '10,5',

    // Card generation - 15 per 5 minutes
    'card_generation' => '15,5',

    // Validation endpoints - 40 per 5 minutes
    'validation' => '40,5',

    // Member read operations - 200 per 1 minute
    'member_read' => '200,1',

    // Referral & Loan - 30 per 5 minutes
    'referral_loan' => '30,5',

    // Admin protected - 10 per 5 minutes
    'admin_protected' => '10,5',
];
```

**Purpose:** Central documentation for all rate limit values. Not referenced at runtime (hardcoded in routes for reliability).

---

### routes/api.php (Implementation)

**Key Changes:**

1. **All 14 endpoints in `/api/vanigam/` now have throttle middleware**
2. **Hardcoded throttle values** (e.g., `'throttle:50,5'`) for direct reliability
3. **Health check remains unthrottled**
4. **Admin endpoints retain API key middleware + new throttle layer**
5. **Clear section comments** for endpoint grouping and maintainability

**Example - OTP Endpoints:**
```php
Route::post('/check-member', [VanigamController::class, 'checkMember'])
    ->middleware('throttle:50,5');

Route::post('/send-otp', [VanigamController::class, 'sendOtp'])
    ->middleware('throttle:50,5');

Route::post('/verify-otp', [VanigamController::class, 'verifyOtp'])
    ->middleware('throttle:50,5');
```

**Example - Admin Protected:**
```php
Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
    ->middleware([
        'validate.admin.api.key',
        'throttle:10,5',
    ]);
```

---

## 🚀 Deployment Instructions

### Step 1: Deploy to Trial Server

SSH into trial server (`phpstack-1603086-6293159.cloudwaysapps.com`):

```bash
# Navigate to project root
cd /home/cloudways/public_html/tamilnadu-vanigargalin-sangamam-ID-card

# 1. Pull latest changes from trial-staging
git pull origin trial-staging

# 2. Clear config cache (no new config loaded, but best practice)
php artisan config:clear

# 3. Clear route cache
php artisan route:clear

# 4. Verify deployment
curl https://phpstack-1603086-6293159.cloudwaysapps.com/api/health
```

**Expected output for Step 4:**
```json
HTTP 200 with health status (unthrottled)
```

---

## 🧪 Testing Instructions

### Test Environment
- **URL:** https://phpstack-1603086-6293159.cloudwaysapps.com
- **Pre-requisites:** Deployment steps completed, cache cleared

---

### TEST 1: PIN Verification Rate Limit (10 per 5 minutes)

**Objective:** Verify throttle middleware fires correctly at limit

**Test Command:**
```bash
# Send 11 rapid requests to /api/vanigam/verify-pin
# Expected: Requests 1-10 return valid response (200 or error)
#           Request 11 returns HTTP 429 (Too Many Requests)

for i in {1..11}; do
    RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" \
        -X POST "https://phpstack-1603086-6293159.cloudwaysapps.com/api/vanigam/verify-pin" \
        -H "Content-Type: application/json" \
        -d '{"pin":"1234","uniqueId":"test"}')

    HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE:" | cut -d: -f2)
    echo "Request $i: HTTP $HTTP_CODE"
done
```

**Expected Results:**
```
Request 1: HTTP 422  (or 200/other - valid response)
Request 2: HTTP 422
Request 3: HTTP 422
Request 4: HTTP 422
Request 5: HTTP 422
Request 6: HTTP 422
Request 7: HTTP 422
Request 8: HTTP 422
Request 9: HTTP 422
Request 10: HTTP 422
Request 11: HTTP 429  ✅ RATE LIMITED
```

**Pass Criteria:** Request 11 returns HTTP 429

**What This Tests:**
- Throttle middleware is active and counting requests per IP
- Limit of 10 per 5 minutes is enforced
- HTTP 429 response is returned when exceeded

---

### TEST 2: Health Check - No Throttle

**Objective:** Verify health check endpoint is NOT throttled

**Test Command:**
```bash
# Send 20 rapid requests to /api/health
# Expected: All return HTTP 200 (NEVER 429)

SUCCESS_COUNT=0
THROTTLED_COUNT=0

for i in {1..20}; do
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
        "https://phpstack-1603086-6293159.cloudwaysapps.com/api/health")

    if [ "$HTTP_CODE" = "200" ]; then
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
    else
        THROTTLED_COUNT=$((THROTTLED_COUNT + 1))
    fi

    echo "Request $i: HTTP $HTTP_CODE"
done

echo ""
echo "Summary: $SUCCESS_COUNT succeeded, $THROTTLED_COUNT throttled"
```

**Expected Results:**
```
Request 1: HTTP 200
Request 2: HTTP 200
... (all 20 requests)
Request 20: HTTP 200

Summary: 20 succeeded, 0 throttled
```

**Pass Criteria:** All 20 requests return HTTP 200 (NEVER 429)

**What This Tests:**
- Health check is intentionally excluded from rate limiting
- No throttle middleware applied to `/api/health`
- Monitoring endpoint remains accessible under load

---

### TEST 3: Admin API Key Middleware - Still Protected

**Objective:** Verify API key middleware still fires correctly (not bypassed by throttle)

**Test Command:**
```bash
# Send request to /api/vanigam/reset-members WITHOUT X-Admin-Key header
# Expected: HTTP 401 (Unauthorized - API key required)
# NOT 429 (throttle should not fire if API key check fails first)

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
    -X POST "https://phpstack-1603086-6293159.cloudwaysapps.com/api/vanigam/reset-members" \
    -H "Content-Type: application/json" \
    -d '{}')

echo "Response: HTTP $HTTP_CODE"
```

**Expected Result:**
```
Response: HTTP 401
```

**Pass Criteria:** HTTP 401 (NOT 429)

**What This Tests:**
- `validate.admin.api.key` middleware fires FIRST (security layer)
- Throttle middleware fires SECOND (rate limit layer)
- Middleware order is correct: authentication → rate limiting
- Admin endpoints not bypassed or affected by throttle addition

---

## ✅ Test Success Criteria

**All 3 tests must pass for deployment to production:**

| Test | Endpoint | Expected | Status |
|------|----------|----------|--------|
| **1** | `/api/vanigam/verify-pin` | Request 11 = 429 | ⬜ Pending |
| **2** | `/api/health` | All 20 = 200 (never 429) | ⬜ Pending |
| **3** | `/api/vanigam/reset-members` | No key = 401 (not 429) | ⬜ Pending |

---

## 📋 Rate Limit Response Format

When rate limit is exceeded (HTTP 429), response includes:

```
HTTP/1.1 429 Too Many Requests
Content-Type: application/json
RateLimit-Limit: 10
RateLimit-Remaining: 0
RateLimit-Reset: 1711046000

{
    "message": "Too many attempts. Please try again in X seconds."
}
```

**Headers:**
- `RateLimit-Limit` — Maximum requests allowed in period
- `RateLimit-Remaining` — Requests remaining before throttle
- `RateLimit-Reset` — Unix timestamp when limit resets

---

## 🔄 Cache Storage

Rate limit counters are stored in:
- **Primary:** Redis (per environment)
  - Trial: `humble-grubworm-79324.upstash.io`
  - Production: `striking-jaybird-66451.upstash.io`
- **Fallback:** File-based cache (if Redis unavailable)

**Current setup:** Handled by existing `CacheService` with automatic fallback

---

## 🛠️ Troubleshooting

### Issue: All requests return 429 immediately

**Cause:** Rate limit counter not resetting or extremely low limit
**Solution:**
1. Clear cache: `php artisan cache:clear`
2. Clear route cache: `php artisan route:clear`
3. Restart PHP-FPM
4. Verify Redis connection: `redis-cli PING`

### Issue: Health check returns 429

**Cause:** Health check should NOT have throttle middleware
**Solution:**
1. Verify `routes/api.php` - `/health` should have NO middleware
2. Check for route caching: `php artisan route:clear`
3. Restart application

### Issue: Admin endpoints bypass API key check

**Cause:** Middleware order incorrect
**Solution:**
1. Verify middleware array in routes/api.php:
   ```php
   ->middleware([
       'validate.admin.api.key',      // FIRST
       'throttle:10,5',               // SECOND
   ])
   ```
2. Middleware evaluated left-to-right; API key must be first

---

## 🔐 Security Considerations

✅ **What's Protected:**
- OTP endpoints: Protected from bot OTP requests (50 per 5 min)
- PIN endpoints: Protected from brute force PIN guessing (10 per 5 min)
- Validation: Protected from resource exhaustion (40 per 5 min)
- Admin endpoints: Keep existing API key + new rate limit layer

✅ **What's NOT Throttled:**
- Health check: Allowed unlimited (monitoring/internal)
- Read-only member lookup: High limit (200 per min - legitimate use)

✅ **Middleware Order:**
- Admin endpoints: API key validated FIRST → Throttle checked SECOND
- Public endpoints: Throttle checked immediately

---

## 📚 Configuration Reference

**Limits are hardcoded for reliability:**
- No config loading overhead
- No potential config caching issues
- Explicit values visible in route definitions

**To adjust limits in future:**
1. Edit `routes/api.php` - change throttle string values
2. Update `config/rate-limits.php` - for documentation
3. Test on trial server
4. Deploy to production

---

## 🚀 Post-Deployment Checklist

After all 3 tests pass:

- [ ] Test 1 passed: PIN endpoint limits request 11 to 429
- [ ] Test 2 passed: Health check never returns 429
- [ ] Test 3 passed: Admin endpoint returns 401 without key
- [ ] Monitor trial server logs for errors (24 hours)
- [ ] Verify no false positives on legitimate user flows
- [ ] Check Redis connection on trial server
- [ ] Ready for production merge

---

## 📞 Quick Reference

**File Locations:**
- Documentation: `config/rate-limits.php`
- Implementation: `routes/api.php` (lines 13-91)
- Tests: Run curl commands from TEST 1, 2, 3 above

**Trial Server:**
- URL: https://phpstack-1603086-6293159.cloudwaysapps.com
- Branch: trial-staging
- Commit: b4cd780

**Production Server:**
- URL: https://vanigan.digital
- Branch: main (after validation)

---

**Ready for deployment to trial? Please confirm the deployment commands and test procedures above.**
