# Testing Instructions - API Key Middleware

## 🚀 How to Test Before Production

### Prerequisites
- Bash shell (macOS, Linux) or Git Bash (Windows)
- `curl` command available
- Access to trial/staging URL or production URL
- The new code deployed to trial environment

---

## Option 1: Automated Test Script

### Step 1: Run the Test Script

```bash
bash d:/Cloudways/project-5/upgrades/test-api-middleware.sh
```

**What it tests:**
1. ✅ Protected endpoints WITHOUT key → Should return 401
2. ✅ Protected endpoints WITH invalid key → Should return 401
3. ✅ Protected endpoints WITH valid key → Should work (or fail at controller level)
4. ✅ Public endpoints → Should work normally (unchanged)

### Expected Output

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

... (more public endpoint tests) ...

==========================================
TEST SUMMARY
==========================================
✓ Passed: 13
✗ Failed: 0
==========================================
ALL TESTS PASSED!
```

---

## Option 2: Manual Testing with curl

### Test 2A: Protected Endpoint WITHOUT Key (Should FAIL with 401)

```bash
curl -X POST https://vanigan.digital/api/vanigam/reset-members \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"test"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:**
```json
{
  "success": false,
  "message": "Missing X-Admin-Key header."
}
HTTP Status: 401
```

---

### Test 2B: Protected Endpoint WITH Invalid Key (Should FAIL with 401)

```bash
curl -X POST https://vanigan.digital/api/vanigam/reset-members \
  -H "X-Admin-Key: wrong-api-key" \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"test"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:**
```json
{
  "success": false,
  "message": "Invalid X-Admin-Key."
}
HTTP Status: 401
```

---

### Test 2C: Protected Endpoint WITH Valid API Key, Invalid Confirm Key (Should FAIL with 403)

```bash
curl -X POST https://vanigan.digital/api/vanigam/reset-members \
  -H "X-Admin-Key: b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2" \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"wrong-key"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:**
```json
{
  "success": false,
  "message": "Invalid confirmation key."
}
HTTP Status: 403
```

---

### Test 2D: Protected Endpoint WITH Both Valid Keys (Should SUCCEED with 200)

```bash
curl -X POST https://vanigan.digital/api/vanigam/reset-members \
  -H "X-Admin-Key: b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2" \
  -H "Content-Type: application/json" \
  -d '{"confirm_key":"a8e2f1d9c3b6e4a7f2c5d8b1e9a3f6c2d5e8b1a4f7c0d3e6a9b2c5f8e1d4a7"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected (if members exist):**
```json
{
  "success": true,
  "message": "Deleted N members from MongoDB.",
  "deleted_count": N
}
HTTP Status: 200
```

---

### Test 2E: Second Protected Endpoint WITHOUT Key (Should FAIL with 401)

```bash
curl -X POST https://vanigan.digital/api/vanigam/upload-card-images \
  -H "Content-Type: application/json" \
  -d '{"unique_id":"test-id"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:**
```json
{
  "success": false,
  "message": "Missing X-Admin-Key header."
}
HTTP Status: 401
```

---

### Test 2F: Public Endpoint - Should Still Work (Should return 200 or other non-401)

```bash
curl -X POST https://vanigan.digital/api/vanigam/check-member \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"TEST123"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:** HTTP 200 or other 2xx/4xy/5xx (NOT 401 Unauthorized)

---

### Test 2G: Another Public Endpoint - Should Still Work

```bash
curl -X POST https://vanigan.digital/api/vanigam/send-otp \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"TEST123"}' \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:** HTTP 200 or other non-401 response

---

### Test 2H: GET Public Endpoint - Should Still Work

```bash
curl -X GET https://vanigan.digital/api/vanigam/member/TEST-UNIQUE-ID \
  -w "\nHTTP Status: %{http_code}\n"
```

**Expected:** HTTP 200 or other non-401 response (even if member not found, should be 404 not 401)

---

## Testing Checklist

Run through these and verify results:

### Protected Endpoints (Should require X-Admin-Key)
- [ ] `/reset-members` without key → 401 ✓
- [ ] `/reset-members` with invalid key → 401 ✓
- [ ] `/reset-members` with valid key → 200 or 403 (depending on confirm_key) ✓
- [ ] `/upload-card-images` without key → 401 ✓
- [ ] `/upload-card-images` with invalid key → 401 ✓

### Public Endpoints (Should work normally - NO 401 errors)
- [ ] `/check-member` → Works (200 or expected error, NOT 401) ✓
- [ ] `/send-otp` → Works (200 or expected error, NOT 401) ✓
- [ ] `/verify-otp` → Works (200 or expected error, NOT 401) ✓
- [ ] `/validate-epic` → Works (200 or expected error, NOT 401) ✓
- [ ] `/upload-photo` → Works (200 or expected error, NOT 401) ✓
- [ ] `/validate-photo` → Works (200 or expected error, NOT 401) ✓
- [ ] `/generate-card` → Works (200 or expected error, NOT 401) ✓
- [ ] `/save-details` → Works (200 or expected error, NOT 401) ✓
- [ ] `/member/{uniqueId}` (GET) → Works (200 or 404, NOT 401) ✓
- [ ] `/qr/{uniqueId}` (GET) → Works (200 or expected error, NOT 401) ✓
- [ ] `/verify-pin` → Works (200 or expected error, NOT 401) ✓
- [ ] `/verify-member-pin` → Works (200 or expected error, NOT 401) ✓
- [ ] `/get-referral` → Works (200 or expected error, NOT 401) ✓
- [ ] `/increment-referral` → Works (200 or expected error, NOT 401) ✓
- [ ] `/loan-request` → Works (200 or expected error, NOT 401) ✓
- [ ] `/check-loan-status` → Works (200 or expected error, NOT 401) ✓

---

## Success Criteria

✅ **PASS** if:
1. Protected endpoints return 401 without valid X-Admin-Key
2. Protected endpoints accept valid X-Admin-Key and proceed
3. All 15 public endpoints work normally (no 401 errors)
4. No other routes or controllers were affected

---

## Troubleshooting

### Issue: All endpoints return 401
**Cause:** Middleware is being applied to all routes
**Solution:** Check `routes/api.php` - middleware should only be on 2 routes (lines 45-46 and 49-50)

### Issue: Protected endpoint returns 500
**Cause:** Possible configuration error
**Solution:** Check Laravel logs in `storage/logs/laravel.log`

### Issue: Public endpoint returns 401
**Cause:** Middleware was added to wrong route
**Solution:** Verify `routes/api.php` middleware applied only to `/reset-members` and `/upload-card-images`

### Issue: Middleware class not found
**Cause:** File not deployed or class name mismatch
**Solution:** Verify `app/Http/Middleware/ValidateAdminApiKey.php` exists with correct class name

---

## Data to Provide When Confirming Tests

When you've completed the tests, please share:

1. **Test Results:**
   - [ ] All 5 protected endpoint tests passed (missing key, invalid key, valid key scenarios)
   - [ ] All 15+ public endpoint tests passed (no 401 errors)
   - [ ] X tests total, Y passed, Z failed

2. **Sample Output:**
   - Copy/paste output of 2-3 test commands showing correct responses

3. **Any Errors?**
   - If any test failed, please share the error response and HTTP status code

Once confirmed, I'll:
1. Stage all files
2. Create commit
3. Push to production

---

## Ready to Push?

Once you've completed the testing and confirmed all tests pass, reply with:
- "Tests passed - ready to push" or similar confirmation

Then I'll proceed with:
```bash
git add <files>
git commit -m "Add API key middleware..."
git push origin main
```

---

**Good luck with testing!** Let me know the results.
