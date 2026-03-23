# Testing Guide - API Key Middleware on Trial Environment

## 🎯 Testing Objective

Verify that:
1. ✅ Protected endpoints REQUIRE X-Admin-Key header (return 401 without it)
2. ✅ Protected endpoints ACCEPT valid key and proceed (return 200/403 based on other validation)
3. ✅ Public endpoints work NORMALLY without any key (no 401 errors)

---

## 📋 Step-by-Step Testing

### Step 1: Deploy Code to Trial Server

```bash
# SSH into your trial/staging server
ssh your_trial_user@your_trial_host

# Navigate to project directory
cd /path/to/project

# Pull latest changes (includes middleware + routes + config)
git pull origin main

# Verify files exist
ls -la app/Http/Middleware/ValidateAdminApiKey.php
grep "admin_api_key" config/vanigam.php
grep "VANIGAM_ADMIN_API_KEY" .env
```

**Expected Output:**
- Middleware file should exist
- config/vanigam.php should contain `admin_api_key` line
- .env should contain `VANIGAM_ADMIN_API_KEY=b7f3c9e2...`

---

### Step 2: Verify Configuration is Loaded

```bash
# Check Laravel can read the config
php artisan tinker
>>> config('vanigam.admin_api_key')
=> "b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2"
>>> exit
```

**Expected:** Should output the admin API key value

---

### Step 3: Run Automated Test Suite

```bash
# From project root directory
bash upgrades/test-api-middleware.sh
```

**Expected Output:**
```
==========================================
API Key Middleware Test Suite
==========================================

TEST: Reset Members - Missing X-Admin-Key
---
Request: POST /api/vanigam/reset-members
Response Status: 401
Response Body: {"success":false,"message":"Missing X-Admin-Key header."}
✓ PASS (Expected HTTP 401, got 401)

TEST: Reset Members - Invalid X-Admin-Key
---
Request: POST /api/vanigam/reset-members
Headers: -H 'X-Admin-Key: wrong-key'
Response Status: 401
Response Body: {"success":false,"message":"Invalid X-Admin-Key."}
✓ PASS (Expected HTTP 401, got 401)

TEST: Reset Members - Valid API Key, Invalid Confirm Key
---
Request: POST /api/vanigam/reset-members
Headers: -H 'X-Admin-Key: b7f3c9e2...'
Response Status: 403
Response Body: {"success":false,"message":"Invalid confirmation key."}
✓ PASS (Expected HTTP 403, got 403)

TEST: Reset Members - Valid API Key & Valid Confirm Key
---
Request: POST /api/vanigam/reset-members
Headers: -H 'X-Admin-Key: b7f3c9e2...'
Response Status: 200
Response Body: {"success":true,"message":"Deleted N members from MongoDB.","deleted_count":N}
✓ PASS (Expected HTTP 200, got 200)

TEST: Upload Card Images - Missing X-Admin-Key
---
Request: POST /api/vanigam/upload-card-images
Response Status: 401
Response Body: {"success":false,"message":"Missing X-Admin-Key header."}
✓ PASS (Expected HTTP 401, got 401)

TEST: Upload Card Images - Invalid X-Admin-Key
---
Request: POST /api/vanigam/upload-card-images
Headers: -H 'X-Admin-Key: invalid-key'
Response Status: 401
Response Body: {"success":false,"message":"Invalid X-Admin-Key."}
✓ PASS (Expected HTTP 401, got 401)

TEST: Check Member - Should Work (Public Endpoint)
---
Request: POST /api/vanigam/check-member
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Send OTP - Should Work (Public Endpoint)
---
Request: POST /api/vanigam/send-otp
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Validate EPIC - Should Work (Public Endpoint)
---
Request: POST /api/vanigam/validate-epic
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Get Member - Should Work (Public Endpoint)
---
Request: GET /api/vanigam/member/TEST-ID
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Get QR Code - Should Work (Public Endpoint)
---
Request: GET /api/vanigam/qr/TEST-ID
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Verify PIN - Should Work (Public Endpoint)
---
Request: POST /api/vanigam/verify-pin
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

TEST: Check Loan Status - Should Work (Public Endpoint)
---
Request: POST /api/vanigam/check-loan-status
Response Status: 200
✓ PASS (Expected HTTP 200, got 200)

==========================================
TEST SUMMARY
==========================================
✓ Passed: 13
✗ Failed: 0
==========================================
ALL TESTS PASSED!
```

**If test passes:** ✅ All middleware working correctly! Proceed to Step 4.
**If test fails:** ❌ See troubleshooting section below.

---

### Step 4: Manual Verification - Protected Endpoint (Test without key)

```bash
curl -X POST https://your-trial-url/api/vanigam/reset-members \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"test"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Missing X-Admin-Key header."
}
HTTP Status: 401
```

**If you see 401:** ✅ Protection working! Proceed.
**If you see 200:** ❌ Middleware not applied. Check routes/api.php.

---

### Step 5: Manual Verification - Protected Endpoint (Test with invalid key)

```bash
curl -X POST https://your-trial-url/api/vanigam/reset-members \
  -H "X-Admin-Key: this-is-wrong" \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"test"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Invalid X-Admin-Key."
}
HTTP Status: 401
```

**If you see 401:** ✅ Validation working! Proceed.
**If you see 200:** ❌ Validation not working. Check middleware code.

---

### Step 6: Manual Verification - Protected Endpoint (Test with valid key)

```bash
curl -X POST https://your-trial-url/api/vanigam/reset-members \
  -H "X-Admin-Key: b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2" \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"wrong-confirm-key"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Invalid confirmation key."
}
HTTP Status: 403
```

**Explanation:**
- 403 means middleware passed (valid API key) ✅
- But confirm_key validation failed (as expected with wrong key) ✅
- This shows TWO layers of security working correctly!

**If you see 403:** ✅ Middleware passed! Proceed.
**If you see 401:** ❌ Key validation failed when it should pass.

---

### Step 7: Manual Verification - Public Endpoint (Test send-otp)

```bash
curl -X POST https://your-trial-url/api/vanigam/send-otp \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"TEST123"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected Response:**
- HTTP 200-299, 400-599 (any HTTP code EXCEPT 401)
- Example: 200, 400, 422, 500, etc.
- Just NOT 401 Unauthorized

**If you see non-401 code:** ✅ Public endpoint works! Proceed.
**If you see 401:** ❌ Middleware accidentally applied to public endpoint.

---

### Step 8: Verify Second Protected Endpoint (upload-card-images)

```bash
curl -X POST https://your-trial-url/api/vanigam/upload-card-images \
  -H "Content-Type: application/json" \
  -d '{"unique_id":"test"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Missing X-Admin-Key header."
}
HTTP Status: 401
```

**If you see 401:** ✅ Second endpoint protected! Proceed.
**If you see 200:** ❌ Middleware not applied to this endpoint.

---

### Step 9: Verify More Public Endpoints

Test a few more public endpoints to ensure nothing broke:

```bash
# Test 1: check-member
curl -X POST https://your-trial-url/api/vanigam/check-member \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"TEST"}' \
  -w "\nHTTP Status: %{http_code}\n"
# Expected: NOT 401

# Test 2: validate-epic
curl -X POST https://your-trial-url/api/vanigam/validate-epic \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"TEST"}' \
  -w "\nHTTP Status: %{http_code}\n"
# Expected: NOT 401

# Test 3: member GET
curl -X GET https://your-trial-url/api/vanigam/member/TEST-ID \
  -w "\nHTTP Status: %{http_code}\n"
# Expected: NOT 401 (even if 404 not found, that's fine)

# Test 4: qr GET
curl -X GET https://your-trial-url/api/vanigam/qr/TEST-ID \
  -w "\nHTTP Status: %{http_code}\n"
# Expected: NOT 401
```

**If all show non-401 responses:** ✅ All public endpoints unaffected!

---

## ✅ Testing Checklist

Complete all tests and check off:

### Protected Endpoints (MUST require key)
- [ ] Reset members WITHOUT key → 401 ✅
- [ ] Reset members WITH wrong key → 401 ✅
- [ ] Reset members WITH valid key → 200 or 403 ✅
- [ ] Upload images WITHOUT key → 401 ✅
- [ ] Upload images WITH wrong key → 401 ✅

### Public Endpoints (MUST work normally)
- [ ] check-member → NOT 401 ✅
- [ ] send-otp → NOT 401 ✅
- [ ] validate-epic → NOT 401 ✅
- [ ] generate-card → NOT 401 ✅
- [ ] member GET → NOT 401 ✅
- [ ] qr GET → NOT 401 ✅
- [ ] verify-pin → NOT 401 ✅
- [ ] verify-member-pin → NOT 401 ✅
- [ ] get-referral → NOT 401 ✅
- [ ] increment-referral → NOT 401 ✅
- [ ] loan-request → NOT 401 ✅
- [ ] check-loan-status → NOT 401 ✅

---

## 🆘 Troubleshooting

### Problem: All protected endpoints return 404
**Cause:** API route not found
**Solution:** Verify trial URL includes `/api/` prefix

### Problem: Protected endpoint returns 500
**Cause:** Config not loaded or middleware class issue
**Solution:**
```bash
# Check logs
tail -100 storage/logs/laravel.log | grep -i "error\|exception"

# Clear config cache
php artisan config:cache

# Try again
```

### Problem: Protected endpoint returns 403 with "Unauthenticated"
**Cause:** Different middleware applied
**Solution:** Check `routes/api.php` - verify middleware name is `validate.admin.api.key`

### Problem: Public endpoint returns 401
**Cause:** Middleware accidentally applied to all routes
**Solution:** Check `routes/api.php` - middleware should ONLY be on lines 45-50 (2 routes)

### Problem: Automated test shows "Command not found"
**Cause:** Not in project root directory
**Solution:**
```bash
cd /path/to/project
bash upgrades/test-api-middleware.sh
```

---

## 📊 Expected Test Results Summary

```
Protected Endpoints:
  ✅ Missing key    → 401 "Missing X-Admin-Key header."
  ✅ Invalid key    → 401 "Invalid X-Admin-Key."
  ✅ Valid key      → 200/403 (controller processes request)

Public Endpoints:
  ✅ All 15         → NOT 401 (any other response is fine)

Total Tests:
  ✅ 13 tests       → ALL PASS
  ❌ 0 tests        → FAIL
```

---

## 📝 Report Template - Share When Complete

Once testing is done, please share:

```
TRIAL TESTING RESULTS
====================

Automated Test Suite: _____ PASS / _____ FAIL
  - Tests Passed: _____ / 13
  - Tests Failed: _____

Manual Tests:
  ✓ Protected endpoint without key: PASS / FAIL
  ✓ Protected endpoint with invalid key: PASS / FAIL
  ✓ Protected endpoint with valid key: PASS / FAIL
  ✓ Public endpoints working: PASS / FAIL

Any Errors? (paste below if applicable)
_________________________

Summary: READY FOR PRODUCTION / NEEDS FIXING
```

---

## 🚀 Next Steps

### If ALL tests PASS ✅
Reply with: "Trial testing passed - ready to push to production"

I will then:
1. Stage all files
2. Create commit
3. Push to main branch
4. Ready for production deployment

### If tests FAIL ❌
Reply with specific failures and any error messages

I will help diagnose and fix the issue before proceeding.

---

**Ready to test? Go ahead and run the commands above!**
**Reply when complete with your results.** 🧪
