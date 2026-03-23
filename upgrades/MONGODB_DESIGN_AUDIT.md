# MongoDB Design Quality Audit
## Tamil Nadu Vanigargalin Sangamam (ID Card System)

**Date:** March 22, 2026
**Status:** Read-only Audit (No Changes Made)
**Scope:** MongoDB schema completeness, flow integrity, indexing, edge cases

---

## 1. SCHEMA COMPLETENESS CHECK

### 1.1 All Fields Written to Members Collection

**Members Collection - Complete Field Inventory:**

| Field | Type | Source | When Written | Required |
|-------|------|--------|--------------|----------|
| `unique_id` | String (TNVS-XXXXXX) | generateCard() | Card generation | ✅ Yes |
| `mobile` | String (10-digit) | generateCard() | Card generation | ✅ Yes |
| `epic_no` | String | generateCard() | Card generation | ✅ Yes |
| `name` | String (1-100 chars) | generateCard() | Card generation | ✅ Yes |
| `membership` | String | generateCard() | Card generation (default: "Member") | ✅ Yes |
| `assembly` | String (1-100 chars) | generateCard() | Card generation | ✅ Yes |
| `district` | String (1-100 chars) | generateCard() | Card generation | ✅ Yes |
| `photo_url` | URL String | generateCard() | Card generation | ✅ Yes |
| `qr_url` | URL String | generateCard() | Card generation | ✅ Yes |
| `card_url` | URL String | generateCard() | Card generation | ✅ Yes |
| `dob` | String or Date | generateCard() | Card generation (optional) | ❌ No |
| `age` | String | generateCard() | Card generation (calculated from DOB) | ❌ No |
| `blood_group` | String (1-10 chars) | generateCard() | Card generation (optional) | ❌ No |
| `address` | String (1-300 chars) | generateCard() or saveAdditionalDetails() | Card generation or details update | ❌ No |
| `contact_number` | String (+91 + mobile) | generateCard() | Card generation | ✅ Yes |
| `details_completed` | Boolean | generateCard() or saveAdditionalDetails() | Card generation or after details filled | ✅ Yes |
| `referred_by` | String (unique_id) | generateCard() | Card generation (optional) | ❌ No |
| `manually_entered` | Boolean | generateCard() | Card generation (default: false) | ❌ No |
| `pin_hash` | Bcrypt Hash | generateCard() | Card generation (optional) or verify-pin flow | ❌ No |
| `card_front_url` | URL String | updateCardUrls() → uploadCardImages | Called via API for card storage | ❌ No |
| `card_back_url` | URL String | updateCardUrls() → uploadCardImages | Called via API for card storage | ❌ No |
| `referral_id` | String (REF-XXXXXXXX) | getOrCreateReferralId() | On first referral request | ❌ No |
| `referral_count` | Integer | incrementReferralCount() or getOrCreateReferralId() | On first referral or increment | ❌ No |
| `created_at` | ISO8601 Timestamp | upsertMember() | Card generation | ✅ Yes |
| `updated_at` | ISO8601 Timestamp | upsertMember(), updateMemberDetails() | On any update | ✅ Yes |
| `skipped_details` | Boolean | saveAdditionalDetails() | When details are updated | ❌ No |

**Total Fields:** 25
**Required Fields:** 9
**Optional Fields:** 16

---

### 1.2 Field Expectations vs Actual Writes

**Fields Frontend/Endpoints Expect to Read:**

✅ **All expected fields ARE written:**
- `unique_id`, `mobile`, `epic_no`, `name` — written in generateCard()
- `membership`, `assembly`, `district`, `photo_url` — written in generateCard()
- `dob`, `age`, `blood_group`, `address` — written optionally in generateCard() or saveAdditionalDetails()
- `contact_number`, `details_completed` — written in generateCard()
- `referral_id`, `referral_count` — created on demand via getOrCreateReferralId()
- `pin_hash` — written if PIN provided to generateCard()
- `card_front_url`, `card_back_url` — written via uploadCardImages
- `qr_url`, `card_url` — written in generateCard()

✅ **No Missing Fields:** All fields read in getMember(), verifyOtp(), etc. are guaranteed to exist on newer documents.

---

### 1.3 Write-But-Never-Read Fields

**Fields written but potentially never read:**

| Field | Written Where | Read Where | Risk Level |
|-------|----------------|-----------|-----------|
| `skipped_details` | saveAdditionalDetails() | Never read in code | ⚠️ Low — informational only |
| `manually_entered` | generateCard() | storeManualEntry() (admin workflow only) | ⚠️ Low — used for data categorization |
| `referred_by` | generateCard() | getMembersReferredBy() (admin panel only) | ⚠️ Low — used for referral analysis |

**Assessment:** These are design fields for analytics/admin purposes. No structural risk.

---

### 1.4 Schema Backward Compatibility Issues

**Potential Problem:** Old documents missing optional fields will not break code:

- If `pin_hash` missing → `password_verify()` returns false, treated as "PIN not set"
- If `referral_id` missing → Falls through to `getOrCreateReferralId()` which generates one on-demand
- If `details_completed` missing → Treated as false (member needs to complete details)
- If `dob`/`age`/`blood_group` missing → Read as empty string/null, UI handles gracefully
- If `card_front_url`/`card_back_url` missing → Read as empty string, card generation re-runs if needed

✅ **No breaking changes:** All optional fields have graceful defaults.

---

## 2. FLOW INTEGRITY CHECK

### 2.1 Step-by-Step Registration Trace

```
Registration Flow (6 Steps):
Mobile Registration Path → OTP Verification → Voter Validation → Photo Upload → Card Generation → PIN Setup
```

#### **STEP 1: check-member**
```
Endpoint: POST /api/vanigam/check-member
Input: mobile (10-digit)
MongoDB Operation: findMemberByMobile(mobile)
  → Queries: { mobile: "9876543210" }
  → Returns: Member document or null

Flow Logic:
  if member exists:
    → Check if pin_hash set
    → Return: { exists: true, has_pin: boolean, name: string }
  else:
    → Return: { exists: false }

Database State Change: NONE (read-only)
Risk: ✅ SAFE
  - Read-only operation
  - No state changes
  - Safe for duplicate calls
```

**New vs Returning User Detection:** ✅ Correctly identifies if mobile is new or returning by presence in collection.

---

#### **STEP 2: send-otp**
```
Endpoint: POST /api/vanigam/send-otp
Input: mobile (10-digit)
MongoDB Operation: NONE (OTP track in Cache, not MongoDB)

Flow Logic:
  - Rate limit check (cache): otp_limit:{ip} < 3 per 5 minutes
  - Cooldown check (cache): otp_cooldown:{mobile} (60 seconds)
  - Call 2Factor.in API to send voice OTP
  - If success: Increment cache counter, set cooldown flag

Database State Change: NONE (uses Redis/File cache only)
Risk: ✅ SAFE
  - No MongoDB writes
  - Cache handles rate limiting
  - Stateless from MongoDB perspective
```

---

#### **STEP 3: verify-otp**
```
Endpoint: POST /api/vanigam/verify-otp
Input: mobile, otp (6-digit)
MongoDB Operations:
  1. findMemberByMobile(mobile) — Check if existing member
  2. Session write: session(['verified_mobile' => mobile])

Flow Logic:
  - Call 2Factor.in API to verify OTP code
  - If OTP valid:
    → Check if member already exists in MongoDB
    → If exists: Return full member data + has_membership flag
    → If new: Return { has_membership: false, member: null }
  - If OTP invalid: Return error

Database State Change:
  ✅ SAFE - Session state only, no member data written
  ✅ Read-only check for existing member

Risk Assessment:
  ✅ SAFE - Non-destructive read
  ✅ SAFE - Session doesn't need persistence to MongoDB
  ✅ SAFE - Flow correctly identifies new vs returning
```

**New vs Returning User Detection:** ✅ Correctly checks findMemberByMobile() to determine if this is returning user. Returns full member data for returning, null for new.

---

#### **STEP 4: validate-epic**
```
Endpoint: POST /api/vanigam/validate-epic
Input: epic_no (5-20 chars)
MySQL Operation:
  → VoterHelper::findByEpicNo(epicNo)
  → Searches across 234 assembly tables (READ-ONLY)

MongoDB Operation: NONE

Flow Logic:
  - Look up voter in MySQL by EPIC
  - If found: Return voter data (name, epic_no, assembly_name, district)
  - If not found: Return EPIC_NOT_FOUND error

Database State Change: NONE (read-only)
Risk: ✅ SAFE
  - Read-only MySQL query
  - No MongoDB writes
  - No state changes
```

**Edge Case:** ⚠️ **What if same EPIC is used by multiple mobiles?**
- EPIC validation (Step 4) doesn't prevent duplicate EPICs
- Later stages allow it: `upsertMember()` keys on **mobile**, not epic_no
- **Result:** ✅ ALLOWED - Same EPIC can be registered by different mobiles (valid use case for family members, shared EPICs)
- This is handled correctly via the upsert strategy (see Step 5 below)

---

#### **STEP 5: generate-card**
```
Endpoint: POST /api/vanigam/generate-card
Input: All member details (mobile, epic_no, name, photo_url, etc.)
MongoDB Operations:
  1. generateUniqueId() → Generate TNVS-XXXXXX
  2. upsertMember(epicNo, memberData):
     → MongoDB upsert by KEY: { mobile: mobile }
     → SET memberData: { unique_id, epic_no, name, ... }
     → SET created_at on INSERT only
     → SET updated_at always

Flow Logic:
  - Generate unique member ID: TNVS-XXXXXX
  - Calculate age from DOB if provided
  - Build complete memberData object
  - If PIN provided: Hash using bcrypt
  - Call upsertMember():
    → If mobile exists: UPDATE all fields
    → If mobile new: INSERT all fields with created_at
  - If manually_entered flag: Also save to manual_entries collection

Database State Change:
  ✅ UPSERT by mobile (safe key)
  ✅ All fields set correctly
  ✅ Timestamps managed correctly

Idempotency: ⚠️ PARTIALLY IDEMPOTENT
  - Calling twice with same mobile will UPDATE (not INSERT twice)
  - unique_id will be REGENERATED on second call (new TNVS-XXXXXX!)
  - Problem: QR code links will change if called twice
```

**⚠️ DESIGN ISSUE #1: unique_id Regeneration on Duplicate Call**

```
Scenario: Member calls /generate-card twice
  Call 1: mobile=9876543210 → unique_id=TNVS-ABC123
    MongoDB: { mobile: 9876543210, unique_id: TNVS-ABC123, ... }

  Call 2: Same mobile, same data
    → generateUniqueId() creates new: TNVS-DEF456
    → upsertMember() UPDATE:
       { mobile: 9876543210, unique_id: TNVS-DEF456, ... }

Result: Old unique_id lost, QR code links break!
        Card URLs change: /member/card/TNVS-ABC123 → /member/card/TNVS-DEF456

Risk Level: 🔴 MEDIUM
  - Unlikely in normal flow (user generates once)
  - But if API called twice before UI response received: data corrupted
  - QR codes in the wild would become invalid

Mitigation Suggestion:
  - Check if mobile already exists before upsert
  - If exists: Return existing unique_id instead of regenerating
  - Only generate new unique_id for NEW registrations
```

---

#### **STEP 6: save-details (Additional Details Top-Up)**
```
Endpoint: POST /api/vanigam/save-additional-details
Input: epic_no, dob, blood_group, address (optional)
MongoDB Operations:
  1. findMemberByEpic(epicNo) → Get existing member
  2. updateMemberDetails(epicNo, details):
     → MongoDB UPDATE by KEY: { epic_no: epicNo }
     → SET details_completed: true
     → SET updated_at: now()

Flow Logic:
  - Calculate age from DOB
  - Look up existing member by EPIC
  - Clear old card images from Cloudinary
  - Update member with new details
  - Return updated member

Database State Change:
  ✅ Safe UPDATE
  ✅ Keys on epic_no (one member per EPIC in this flow)
  ✅ Timestamps updated correctly

Idempotency: ⚠️ NOT IDEMPOTENT
  - Calling twice will DELETE old card images twice
  - First call: Deletes cards from Cloudinary
  - Second call: Attempts to delete already-deleted cards (harmless 404)
```

**⚠️ DESIGN ISSUE #2: updateMemberDetails Keys on epic_no**

```
Problem: generateCard() keys on mobile, but saveAdditionalDetails() keys on epic_no

Scenario: Duplicate EPICs across different mobiles
  1. User A: mobile=9876543210, epic_no=ACT001
  2. User B: mobile=9876543211, epic_no=ACT001 (same EPIC, different mobile)

When User A calls save-details:
  → updateMemberDetails(ACT001, details)
  → MongoDB: UPDATE { epic_no: "ACT001" } SET details_completed=true

Result: BOTH User A and User B updated!
        User A's details-completion sets User B's details too!

Risk Level: 🔴 MEDIUM-HIGH
  - Updates FIRST member with that EPIC only (MongoDB updateOne)
  - But could affect wrong member if called out of order
  - Duplicate EPICs are allowed by design (same EPIC, different mobiles)
  - This is a CRITICAL FLAW in the flow

Root Cause: Mixing two different lookup keys (mobile vs epic_no)
  - upsertMember() uses mobile (CORRECT - unique per family)
  - updateMemberDetails() uses epic_no (WRONG - could be duplicate)

Mitigation Urgent:
  - updateMemberDetails() should key on unique_id, not epic_no
  - OR validate at saveAdditionalDetails() for duplicate EPIC and reject
  - OR ensure frontend always has unique_id at this stage
```

---

#### **STEP 6b: verify-pin (Login Workflow - PIN Check)**
```
Endpoint: POST /api/vanigam/verify-pin
Input: mobile, pin (4-digit)
MongoDB Operations:
  1. findMemberByMobile(mobile) → Get member data
  2. Verify pin_hash using password_verify()

Flow Logic:
  - Look up member by mobile
  - If member not found OR pin_hash not set: Return 404
  - If pin_hash exists: Verify against provided PIN
  - If PIN matches: Return full member data (excluding pin_hash)
  - If PIN incorrect: Return 400 "INVALID_PIN"

Database State Change: NONE (read-only)
Risk: ✅ SAFE
  - Read-only operation
  - PIN verification only (no writes)
  - Correct error handling

Edge Case: What if PIN never set?
  → Member not found OR pin_hash empty
  → Returns 404 "MEMBER_OR_PIN_NOT_FOUND"
  → User sees: "Member not found or PIN not set"
  → User must go through setPin flow
  → ✅ Correct behavior
```

**PIN Setup Flow Missing:** ⚠️ The code doesn't show an explicit `/set-pin` endpoint. PIN is set during generateCard() only.

---

#### **STEP 6c: verify-member-pin (QR Scan Workflow - PIN Check)**
```
Endpoint: POST /api/vanigam/verify-member-pin
Input: unique_id, pin (4-digit)
MongoDB Operations:
  1. findMemberByUniqueId(unique_id) → Get member data
  2. Verify pin_hash using password_verify()

Flow Logic:
  - Look up member by unique_id (QR scan data)
  - If member not found OR pin_hash not set: Return 404
  - If pin_hash exists: Verify against provided PIN
  - If PIN matches: Return { success: true }
  - If PIN incorrect: Return 400 "INVALID_PIN"

Database State Change: NONE (read-only)
Risk: ✅ SAFE
  - Read-only operation
  - PIN verification only (no writes)
  - Correct error handling

Difference from verify-pin:
  - verifyPin: Returns full member data on success (login workflow)
  - verifyMemberPin: Returns only { success: true } (QR verification workflow)
  - Both should use separate rate limiters (recently implemented ✅)
```

---

### 2.2 Flow Integrity Summary

| Step | Operation | New vs Returning | Idempotent | Safe |
|------|-----------|------------------|-----------|------|
| check-member | Read by mobile | ✅ Yes | ✅ Yes | ✅ Yes |
| send-otp | Cache only | N/A | ✅ Yes | ✅ Yes |
| verify-otp | Read by mobile | ✅ Yes | ✅ Yes | ✅ Yes |
| validate-epic | MySQL read-only | N/A | ✅ Yes | ✅ Yes |
| generate-card | Upsert by mobile | ✅ Yes | ⚠️ No | ⚠️ Partial |
| save-details | Update by epic_no | ✅ Yes (via epic lookup) | ⚠️ No | 🔴 No |
| verify-pin | Read by mobile | N/A | ✅ Yes | ✅ Yes |
| verify-member-pin | Read by unique_id | N/A | ✅ Yes | ✅ Yes |

---

## 3. INDEX CHECK

### 3.1 Queried Fields Analysis

**Fields queried in MongoDB (from MongoService):**

| Field | Query Method | Frequency | Indexed? |
|-------|--------------|-----------|----------|
| `mobile` | findMemberByMobile() | HIGH (every flow) | ❌ NO |
| `unique_id` | findMemberByUniqueId() | HIGH (card view, QR scan) | ❌ NO |
| `epic_no` | findMemberByEpic(), updateMemberDetails() | MEDIUM (details update) | ❌ NO |
| `referred_by` | getMembersReferredBy() | LOW (admin panel) | ❌ NO |
| `created_at` | Sorting in getAllMembers(), getStats() | LOW (admin panel) | ❌ NO |
| `details_completed` | Filter in getStats() | LOW (admin dashboard) | ❌ NO |

**Full-text Search in getAllMembers():**
```php
$filter['$or'] = [
    ['name' => $regex],
    ['epic_no' => $regex],
    ['mobile' => $regex],
    ['unique_id' => $regex],
];
```

**Scanning fields (no $regex projection):**
- `name`, `epic_no`, `mobile`, `unique_id` → Full collection scans without index!

### 3.2 Performance Risk Assessment

| Field | Usage | Risk Level | Impact |
|-------|-------|-----------|--------|
| `mobile` | Called on every login check | 🔴 HIGH | Collection scan O(n) every OTP verify |
| `unique_id` | Called on every QR scan | 🔴 HIGH | Collection scan O(n) every card view |
| `epic_no` | Filtered in searches | 🔴 MEDIUM | Regex scan on admin panel |
| `referred_by` | Admin panel only | 🟡 LOW | Background queries only |
| `created_at` | Sorting in admin | 🟡 LOW | Used with limit, moderate impact |

### 3.3 MongoDB Indexes NOT Defined

**Current State:** ❌ **NO INDEXES DEFINED IN CODE**

**Recommendation:** Create indexes in MongoDB Atlas or via migration:

```javascript
// Critical indexes for production
db.members.createIndex({ mobile: 1 })           // For findMemberByMobile()
db.members.createIndex({ unique_id: 1 })        // For findMemberByUniqueId()
db.members.createIndex({ epic_no: 1 })          // For findMemberByEpic()
db.members.createIndex({ referred_by: 1 })      // For getMembersReferredBy()

// Performance indexes
db.members.createIndex({ created_at: -1 })      // For sorting in admin panel
db.members.createIndex({ details_completed: 1 }) // For stats aggregation

// Text search (optional for frontend search)
db.members.createIndex({ name: "text", epic_no: "text", mobile: "text" })
```

**Why indexes matter at scale:**
- **Without indexes:** 1M members = 1M document scans per query
- **With indexes:** O(log n) lookup, ~20 scans per query
- Trial server problem: Small dataset hides performance issues

---

## 4. EDGE CASES & RISK ANALYSIS

### 4.1 What Happens if User Registers Halfway and Abandons?

```
Scenario: User starts registration but never completes

Timeline:
  T1: POST /check-member → check mobile (no data written)
  T2: POST /send-otp → OTP sent (cache only)
  T3: POST /verify-otp → OTP verified (session only, no MongoDB write)
  T4: POST /validate-epic → EPIC looked up (MySQL read only)
  T5: POST /upload-photo → Photo uploaded to Cloudinary (not MongoDB yet)
  T6: ❌ User closes app, abandons flow

Result:
  ✅ MongoDB: EMPTY (no record created yet!)
  ⚠️ Cloudinary: Orphaned photo (TNVS-{time}.jpg still stored)
  ✅ Cache: Expired (OTP rate limit & cooldown cleared)
  ✅ Session: No persistent state

Cleanup Needed:
  🔴 MEDIUM - Orphaned Cloudinary images accumulate
  ✅ LOW - MongoDB clean (no duplicates)
  Recommendation: Periodic cleanup job to remove unused Cloudinary images older than 24h
```

---

### 4.2 What Happens if Same EPIC is Used by Two Different Mobiles?

```
Scenario: Two family members with same voter card (shared EPIC)

Timeline:
  Mobile A (9876543210):
    POST /validate-epic → EPIC001 found
    POST /generate-card → upsertMember("EPIC001", data) by MOBILE A
      MongoDB: { mobile: 9876543210, epic_no: EPIC001, unique_id: TNVS-A001, ... } INSERTED

  Mobile B (9876543211):
    POST /validate-epic → EPIC001 found (same EPIC!)
    POST /generate-card → upsertMember("EPIC001", data) by MOBILE B
      MongoDB: { mobile: 9876543211, epic_no: EPIC001, unique_id: TNVS-B001, ... } INSERTED

Result:
  ✅ CORRECT - Two separate documents by mobile key!
  ✅ Members collection now has:
     { mobile: 9876543210, epic_no: EPIC001, unique_id: TNVS-A001 }
     { mobile: 9876543211, epic_no: EPIC001, unique_id: TNVS-B001 }

Later: Mobile A calls save-details:
  POST /save-additional-details (epic_no=EPIC001)
    → updateMemberDetails("EPIC001", details)
    → MongoDB: UPDATE { epic_no: "EPIC001" } SET details_completed=true
    → ⚠️ ONLY FIRST document updated! (updateOne, not updateMany)

Result:
  🔴 CRITICAL - Mobile B's details NOT updated!
     updateMemberDetails() uses updateOne(), only hits first match
     Both documents have same epic_no: EPIC001
     Race condition: Which one gets updated depends on insertion order

Current Implementation Issue:
  ❌ generateCard() keys on mobile (correct)
  ❌ saveAdditionalDetails() keys on epic_no (wrong for duplicate EPICs)

Risk Mitigation Required:
  - OPTION A: Make unique_id primary lookup everywhere (correct)
  - OPTION B: Prevent duplicate EPICs at registration (restrictive)
  - OPTION C: Use unique_id instead of epic_no in saveAdditionalDetails()

Recommended Fix: OPTION C - Use unique_id everywhere
```

---

### 4.3 What Happens if generate-card is Called but Photo Upload Failed?

```
Scenario: User uploads photo successfully, but then tries to generate card twice

Step 1: POST /upload-photo
  → Photo uploaded to Cloudinary
  → Success response: { photo_url: "https://cloudinary.com/..." }

Step 2: POST /generate-card (first time)
  → upsertMember() called
  → MongoDB: New member created with member_photo_url
  → Response sent to client

Step 3: POST /generate-card (second time) - duplicate call due to network retry
  → upsertMember() called again
  → generateUniqueId() creates NEW unique_id (REGENERATION!)
  → MongoDB: { mobile: same, unique_id: REGENERATED, photo_url: same_cloudinary_url }
  → Old unique_id lost!

Result:
  🔴 CRITICAL (same as issue #1):
     - QR codes pointing to /member/card/TNVS-ABC123 now broken
     - Issued card links become invalid
     - New unique_id is TNVS-DEF456 but old one orphaned

  ⚠️ Mitigation:
     - Client should cache response and not retry
     - OR: unique_id should be persisted to localStorage
     - OR: API should check for duplicate mobile before regenerating unique_id
```

---

### 4.4 What Happens if PIN Not Set During generate-card?

```
Scenario: User generates card without providing PIN

generateCard() endpoint:
  'pin' => 'nullable|digits:4',  ← Can be omitted

  if ($pin) {
    $memberData['pin_hash'] = password_hash($pin, PASSWORD_BCRYPT);
  } else {
    // pin_hash NOT set
  }

Result:
  ✅ Member created WITHOUT pin_hash field
  ✅ pinHash: MISSING from document (not null, just absent)

Later: User tries to verify PIN with verifyPin():
  $member = findMemberByMobile(mobile)  ← Returns member
  if (!$member || empty($member['pin_hash'])) {
    return error: "Member not found or PIN not set"  ✅ Correct!
  }

Flow Summary:
  ✅ SAFE - PIN optional during registration
  ✅ SAFE - Correctly prevents PIN verification if not set
  ✅ SAFE - User is directed to PIN setup flow instead
```

---

### 4.5 What Happens if Voter Lookup Fails at Step 4?

```
Scenario: User provides EPIC that doesn't exist in MySQL

POST /validate-epic (epic_no=INVALID123)
  → VoterHelper::findByEpicNo(INVALID123)
  → MySQL searches 234 tables, finds NOTHING
  → Returns null

Response:
  { success: false, error_code: "EPIC_NOT_FOUND", message: "..." }

User Experience:
  ✅ Clear error message
  ✅ User can manually enter EPIC (manually_entered flag)
  ✅ Flow continues with manually_entered: true

Backend:
  When generate-card called with manually_entered=true:
    → memberData['manually_entered'] = true
    → upsertMember() saves to main members collection
    → storeManualEntry() also saves to manual_entries collection for admin review

MongoDB Result:
  ✅ Two documents created:
     1. members: { mobile, epic_no: MANUAL_ABC, manually_entered: true, ... }
     2. manual_entries: Same data (for admin workflow)

Admin Panel:
  Can view unverified manual entries
  Can verify or reject them

Risk Assessment:
  ✅ SAFE - Manual entries tracked separately
  ✅ SAFE - Admin has visibility
  ✅ SAFE - Doesn't break flow
```

---

### 4.6 Duplicate Mobile Number Registration

```
Scenario: Same mobile registers twice (network retry or malicious intent)

Call 1: POST /generate-card (mobile=9876543210, epic_no=EPIC001)
  → upsertMember("EPIC001", memberData)
  → MongoDB UPSERT { mobile: 9876543210 }:
     - Mobile doesn't exist → INSERT new document
     - Sets: unique_id=TNVS-ABC123, created_at=now()

Call 2: POST /generate-card (mobile=9876543210, epic_no=EPIC001 or different)
  → upsertMember("EPIC001", memberData)
  → MongoDB UPSERT { mobile: 9876543210 }:
     - Mobile EXISTS → UPDATE
     - REGENERATES unique_id=TNVS-DEF456 (PROBLEM!)
     - SAME fields updated except unique_id changes

Database Result:
  Original: { mobile: 9876543210, unique_id: TNVS-ABC123, epic_no: EPIC001, created_at: T1 }
  After 2nd: { mobile: 9876543210, unique_id: TNVS-DEF456, epic_no: EPIC001, created_at: T1 (unchanged) }

🔴 CRITICAL ISSUE:
  - Old unique_id lost
  - QR codes with old ID broken
  - created_at correct but unique_id unstable

Mitigation Required:
  Check if mobile exists BEFORE calling generateUniqueId()
  If exists: Return existing member with existing unique_id
  If new: Generate new unique_id
```

---

## 5. CRITICAL FINDINGS SUMMARY

| Issue | Severity | Impact | Status |
|-------|----------|--------|--------|
| **unique_id regeneration on duplicate generate-card** | 🔴 CRITICAL | QR codes break on retry | ⚠️ Unfixed |
| **UPDATE by epic_no instead of unique_id** | 🔴 CRITICAL | Wrong member updated for duplicate EPICs | ⚠️ Unfixed |
| **No MongoDB indexes defined** | 🟡 MEDIUM | Collection scans will slow down at scale | ⚠️ Unfixed |
| **Orphaned Cloudinary images on abandoned flow** | 🟡 MEDIUM | Cloud storage cost accumulates | ⚠️ Unfixed |
| **PIN verification assumes PIN always present** | 🟢 LOW | Actually handled correctly with optional PIN | ✅ Safe |
| **Duplicate EPIC allowed (by design)** | 🟢 LOW | Works correctly when using mobile key | ✅ Safe |

---

## 6. RECOMMENDATIONS

### 6.1 Immediate Fixes (Production Risk)

**Fix #1: Stabilize unique_id on Duplicate Calls**

```php
// In generateCard()
// BEFORE: generateUniqueId() is called unconditionally
// AFTER: Check if mobile exists first

$existingMember = $this->mongo->findMemberByMobile($mobile);
if ($existingMember && isset($existingMember['unique_id'])) {
    $uniqueId = $existingMember['unique_id'];  // Reuse existing
} else {
    $uniqueId = $this->mongo->generateUniqueId();  // Generate new
}

// Then proceed with upsertMember() as before
```

**Fix #2: Use unique_id Instead of epic_no in saveAdditionalDetails**

```php
// In saveAdditionalDetails()
// BEFORE: updateMemberDetails(epicNo, details)
// AFTER: Use unique_id from request or lookup

$member = $this->mongo->findMemberByEpic($epicNo);
if ($member && isset($member['unique_id'])) {
    // Update using unique_id instead
    $this->mongo->updateMemberDetailsByUniqueId($member['unique_id'], $details);
} else {
    return error "Member not found"
}
```

---

### 6.2 Performance Enhancements

**Add MongoDB Indexes:**

```javascript
// Execute in MongoDB Atlas dashboard or via migration
db.members.createIndex({ mobile: 1 }, { unique: false })
db.members.createIndex({ unique_id: 1 }, { unique: false })
db.members.createIndex({ epic_no: 1 }, { unique: false })
db.members.createIndex({ created_at: -1 })
db.members.createIndex({ referred_by: 1 })
db.members.createIndex({ details_completed: 1 })
```

**Expected Impact After Indexing:**
- Login flow (findByMobile): ~1000x faster at 1M members
- QR scan (findByUniqueId): ~1000x faster at 1M members
- Admin search: ~100x faster for text search queries

---

### 6.3 Data Cleanup

**Orphaned Cloudinary Images:**
- Add cleanup job to remove photos not referenced in MongoDB for >24h
- Add metrics to track orphaned images accumulation

**Optional but Recommended:**
- MongoDB text index on name, epic_no, mobile for admin search
- Aggregate statistics cache (refresh hourly) for admin dashboard

---

## 7. AUDIT CONCLUSION

### Schema Design: ✅ **GOOD**
- All necessary fields captured
- Backward compatible with optional fields
- Proper field types and sizes

### Flow Logic: ⚠️ **PARTIALLY PROBLEMATIC**
- New vs returning user detection works correctly
- Duplicate EPIC handling works when keys used correctly
- **BUT:** Mixing lookup keys (mobile vs epic_no) creates update anomalies
- **BUT:** unique_id regeneration on retries breaks QR codes

### Indexing: 🔴 **CRITICAL GAP**
- No indexes defined for high-frequency queries
- Collection scans O(n) for every login/QR scan
- Performance will degrade severely at production scale (>100K members)

### Recommended Action:
1. **Immediate:** Implement Fix #1 and #2 above
2. **Urgent:** Create MongoDB indexes (5-minute job)
3. **Soon:** Add orphaned image cleanup job
4. **Testing:** Test with duplicate EPIC, network retries, missing PIN scenarios

---

**Audit Completed:** March 22, 2026
**Auditor:** Claude
**Classification:** Design Quality Assessment (Read-Only)
**Shared with:** Claude Web Integration

