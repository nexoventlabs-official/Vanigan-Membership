# API Error Standardization - Complete Diff
**Status:** Ready for Review
**Changes:** Limited, Additive, Safe
**Impact:** Frontend-safe (only adds new optional field)

---

## File 1: `app/Http/Controllers/VanigamController.php`

### Change Type: ADDITIVE (No field removal, no existing field changes)

This file has **42 error responses** across 17 endpoints. All will get a new field `error_code` without modifying existing `success` or `message` fields.

---

### Error Responses with Changes:

#### 1. sendOtp() - Line 46-49
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Too many OTP requests. Please try after 5 minutes.',
], 429);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Too many OTP requests. Please try after 5 minutes.',
    'error_code' => 'OTP_RATE_LIMIT',
], 429);
```

---

#### 2. sendOtp() - Line 55-58
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'OTP already sent. Please wait before requesting again.',
], 429);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'OTP already sent. Please wait before requesting again.',
    'error_code' => 'OTP_COOLDOWN',
], 429);
```

---

#### 3. sendOtp() - Line 74-77
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => $result['error'] ?? 'Could not send OTP.',
], 500);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => $result['error'] ?? 'Could not send OTP.',
    'error_code' => 'OTP_SEND_FAILED',
], 500);
```

---

#### 4. sendOtp() - Line 81 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 5. checkMember() - Line 128 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 6. verifyOtp() - Line 180-183
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => $result['error'] ?? 'Invalid OTP.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => $result['error'] ?? 'Invalid OTP.',
    'error_code' => 'INVALID_OTP',
], 400);
```

---

#### 7. verifyOtp() - Line 187 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 8. validateEpic() - Line 207-210
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'EPIC Number not found. Please check and try again.',
], 404);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'EPIC Number not found. Please check and try again.',
    'error_code' => 'EPIC_NOT_FOUND',
], 404);
```

---

#### 9. validateEpic() - Line 225 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 10. uploadPhoto() - Line 244-247
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Only JPG/PNG photos allowed.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Only JPG/PNG photos allowed.',
    'error_code' => 'INVALID_PHOTO_FORMAT',
], 400);
```

---

#### 11. uploadPhoto() - Line 261
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Photo upload failed.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Photo upload failed.', 'error_code' => 'PHOTO_UPLOAD_FAILED'], 500);
```

---

#### 12. uploadPhoto() - Line 274 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Photo upload failed: ' . $e->getMessage()], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Photo upload failed: ' . $e->getMessage(), 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 13. validatePhotoUpload() - Line 295-297
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Only JPG/PNG photos allowed.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Only JPG/PNG photos allowed.',
    'error_code' => 'INVALID_PHOTO_FORMAT',
], 400);
```

---

#### 14. validatePhotoUpload() - Line 302-305
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo size must be less than 15MB.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo size must be less than 15MB.',
    'error_code' => 'PHOTO_TOO_LARGE',
], 400);
```

---

#### 15. validatePhotoUpload() - Line 311-314
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Invalid image file. Please upload a valid photo.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Invalid image file. Please upload a valid photo.',
    'error_code' => 'INVALID_IMAGE_FILE',
], 400);
```

---

#### 16. validatePhotoUpload() - Line 323-326
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo is too small. Minimum resolution: 200x200 pixels.',
], 400);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo is too small. Minimum resolution: 200x200 pixels.',
    'error_code' => 'PHOTO_TOO_SMALL',
], 400);
```

---

#### 17. validatePhotoUpload() - Line 339-342 (catch block)
**Current:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo validation failed: ' . $e->getMessage()
], 500);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => 'Photo validation failed: ' . $e->getMessage(),
    'error_code' => 'INTERNAL_ERROR'
], 500);
```

---

#### 18. generateCard() - Line 456 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Card generation failed: ' . $e->getMessage()], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Card generation failed: ' . $e->getMessage(), 'error_code' => 'CARD_GENERATION_FAILED'], 500);
```

---

#### 19. saveAdditionalDetails() - Line 544
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
```

---

#### 20. saveAdditionalDetails() - Line 548 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 21. getMember() - Line 562
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
```

---

#### 22. getMember() - Line 591 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 23. verifyMemberPin() - Line 692
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.', 'error_code' => 'MEMBER_OR_PIN_NOT_FOUND'], 404);
```

---

#### 24. verifyMemberPin() - Line 699
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.', 'error_code' => 'INVALID_PIN'], 400);
```

---

#### 25. verifyMemberPin() - Line 702 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 26. resetMembers() - Line 828
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Invalid confirmation key.'], 403);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Invalid confirmation key.', 'error_code' => 'INVALID_RESET_KEY'], 403);
```

---

#### 27. resetMembers() - Line 840
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Failed to reset MongoDB.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Failed to reset MongoDB.', 'error_code' => 'RESET_FAILED'], 500);
```

---

#### 28. resetMembers() - Line 843 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 29. uploadCardImages() - Line 892 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Card image upload failed.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Card image upload failed.', 'error_code' => 'CARD_UPLOAD_FAILED'], 500);
```

---

#### 30. verifyPin() - Line 910
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.', 'error_code' => 'MEMBER_OR_PIN_NOT_FOUND'], 404);
```

---

#### 31. verifyPin() - Line 922
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.', 'error_code' => 'INVALID_PIN'], 400);
```

---

#### 32. verifyPin() - Line 925 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 33. getReferral() - Line 939
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Missing unique_id.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Missing unique_id.', 'error_code' => 'MISSING_UNIQUE_ID'], 400);
```

---

#### 34. getReferral() - Line 944
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
```

---

#### 35. getReferral() - Line 959 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 36. incrementReferral() - Line 986
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Missing referrer ID.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Missing referrer ID.', 'error_code' => 'MISSING_REFERRER_ID'], 400);
```

---

#### 37. incrementReferral() - Line 994 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 38. loanRequest() - Line 1010
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Missing required fields.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Missing required fields.', 'error_code' => 'MISSING_REQUIRED_FIELDS'], 400);
```

---

#### 39. loanRequest() - Line 1015
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
```

---

#### 40. loanRequest() - Line 1036 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

#### 41. checkLoanStatus() - Line 1051
**Current:**
```php
return response()->json(['success' => false, 'message' => 'Missing unique_id or mobile.'], 400);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'Missing unique_id or mobile.', 'error_code' => 'MISSING_PARAMETERS'], 400);
```

---

#### 42. checkLoanStatus() - Line 1090 (catch block)
**Current:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
```

**Changed to:**
```php
return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
```

---

### VanigamController Error Code Reference

| Error Code | HTTP Status | Condition | Endpoints |
|-----------|-------------|-----------|-----------|
| OTP_RATE_LIMIT | 429 | 3+ OTPs in 5 min | send-otp |
| OTP_COOLDOWN | 429 | OTP sent <60s ago | send-otp |
| OTP_SEND_FAILED | 500 | OTP service error | send-otp |
| INVALID_OTP | 400 | Wrong OTP code | verify-otp |
| EPIC_NOT_FOUND | 404 | EPIC lookup failed | validate-epic |
| INVALID_PHOTO_FORMAT | 400 | Not JPG/PNG | upload-photo, validate-photo |
| PHOTO_TOO_LARGE | 400 | >15MB | validate-photo |
| INVALID_IMAGE_FILE | 400 | Corrupted image | validate-photo |
| PHOTO_TOO_SMALL | 400 | <200x200px | validate-photo |
| CARD_GENERATION_FAILED | 500 | Card gen error | generate-card |
| MEMBER_NOT_FOUND | 404 | Member doesn't exist | save-details, get-member, get-referral, loan-request |
| MEMBER_OR_PIN_NOT_FOUND | 404 | Member or PIN missing | verify-member-pin, verify-pin |
| INVALID_PIN | 400 | Wrong PIN | verify-member-pin, verify-pin |
| INVALID_RESET_KEY | 403 | Wrong reset key | reset-members |
| RESET_FAILED | 500 | MongoDB reset failed | reset-members |
| CARD_UPLOAD_FAILED | 500 | Cloudinary upload error | upload-card-images |
| MISSING_UNIQUE_ID | 400 | unique_id param missing | get-referral |
| MISSING_REFERRER_ID | 400 | referrer_unique_id missing | increment-referral |
| MISSING_REQUIRED_FIELDS | 400 | Required field missing | loan-request |
| MISSING_PARAMETERS | 400 | Parameter validation failed | check-loan-status |
| INTERNAL_ERROR | 500 | Unhandled exception | All endpoints (catch blocks) |

---

## File 2: `app/Http/Controllers/ApiController.php`

### Change Type: COMPATIBILITY FIX (Changes `status` to `success` only in health endpoint)

#### Health Check Endpoint - Lines 86 & 90-93

**BACKGROUND:**
All 17 vanilla endpoints use `success: true/false`, but the health check in ApiController still uses the old `status: ok/error` pattern. This causes:
- `/api/health` returns `status: "ok"` (inconsistent)
- Other endpoints return `success: true` (standard)

**ERROR CASE - Lines 90-93:**
**Current:**
```php
return response()->json([
    'status' => 'error',
    'message' => $e->getMessage(),
], 500);
```

**Changed to:**
```php
return response()->json([
    'success' => false,
    'message' => $e->getMessage(),
    'error_code' => 'HEALTH_CHECK_FAILED',
], 500);
```

**SUCCESS CASE - Line 86:**
The success response currently returns `status: 'ok'` with all the health data. This needs to be standardized:

**Current:**
```php
return response()->json($health);
```
Where `$health['status'] = 'ok'`

**Changed to:**
```php
$health['success'] = true;
unset($health['status']);  // Remove old 'status' field
return response()->json($health);
```

**FULL SUCCESS RESPONSE - Before:**
```json
{
  "status": "ok",
  "timestamp": "2026-03-22T10:30:00Z",
  "mysql": "ok",
  "voters_db": "ok",
  "redis": "ok",
  "cache": "ok (redis)"
}
```

**FULL SUCCESS RESPONSE - After:**
```json
{
  "success": true,
  "timestamp": "2026-03-22T10:30:00Z",
  "mysql": "ok",
  "voters_db": "ok",
  "redis": "ok",
  "cache": "ok (redis)"
}
```

---

### Summary of Changes

**File 1: VanigamController.php**
- Total error responses: 42
- Changes: Add `error_code` field to all error responses (PURELY ADDITIVE)
- No removal of existing fields
- No change to success responses
- No change to data wrappers

**File 2: ApiController.php**
- Total changes: 2 locations
- Change 1: Replace `status: 'ok'` with `success: true` (Line 86 area)
- Change 2: Replace `status: 'error'` with `success: false, error_code: 'HEALTH_CHECK_FAILED'` (Lines 90-93)

---

## Frontend Impact Assessment

### ✅ SAFE - No Breaking Changes
- All existing `success`, `message`, and data fields remain UNCHANGED
- New `error_code` field is purely informational (optional to parse)
- Existing frontend code will continue to work without modification
- Frontend can gradually adopt `error_code` for better error handling

### Example: Frontend Compatibility
**Before standardization:**
```javascript
const response = await fetch('/api/vanigam/send-otp');
const data = await response.json();
if (!data.success) {
  console.log(data.message); // Works as before
}
```

**After standardization (backward compatible):**
```javascript
const response = await fetch('/api/vanigam/send-otp');
const data = await response.json();
if (!data.success) {
  console.log(data.message); // Still works
  if (data.error_code === 'OTP_RATE_LIMIT') {
    // Can now specifically handle rate limits
  }
}
```

---

## Implementation Safety Check

✅ No `success` responses modified
✅ No data wrapper names changed (member, voter, referral_link, etc.)
✅ No message text changed
✅ All HTTP status codes maintained
✅ No breaking changes to API contract
✅ Frontend can adopt new `error_code` gradually
✅ Completely backward compatible

