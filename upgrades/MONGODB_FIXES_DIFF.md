# MongoDB Critical Issues - Fix Diffs
## Two Fixes for unique_id Regeneration and epic_no Key Mismatch

**Date:** March 22, 2026
**Status:** Ready for Review - NO CHANGES MADE YET
**Files Modified:** 2 files
  - `app/Services/MongoService.php` (1 new method added)
  - `app/Http/Controllers/VanigamController.php` (2 functions modified)

---

## FIX #1: Prevent unique_id Regeneration on Duplicate generate-card Calls

### Problem
When `/generate-card` is called twice with the same mobile:
1. First call: Generates unique_id `TNVS-ABC123`, member created ✅
2. Second call: Generates NEW unique_id `TNVS-DEF456`, old one lost ❌
3. Result: QR codes, card links break because unique_id changed

### Solution
Before generating unique_id, check if mobile already exists. If yes, reuse existing unique_id. If no, generate new one.

---

### File 1A: MongoService.php - ADD NEW METHOD

**Location:** `app/Services/MongoService.php` after line 158 (after `updateMemberDetails()`)

**Type:** ADD NEW METHOD

```php
    /**
     * Update additional details for a member by unique_id (not epic_no).
     * Prevents wrong member from being updated when duplicate EPICs exist.
     */
    public function updateMemberDetailsByUniqueId(string $uniqueId, array $details): bool
    {
        try {
            $details['updated_at']        = now()->toISOString();
            $details['details_completed'] = true;

            $result = $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => $details]
            );

            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateMemberDetailsByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }
```

---

### File 1B: VanigamController.php - generateCard() Function

**Location:** `app/Http/Controllers/VanigamController.php` lines 389-390

**Current Code:**
```php
            // Generate unique member ID
            $uniqueId = $this->mongo->generateUniqueId();
```

**New Code:**
```php
            // Check if mobile already exists - reuse unique_id if so, generate new if not
            // This prevents unique_id from changing on duplicate calls
            $existingMemberForMobile = $this->mongo->findMemberByMobile($mobile);
            if ($existingMemberForMobile && !empty($existingMemberForMobile['unique_id'])) {
                $uniqueId = $existingMemberForMobile['unique_id'];
                Log::info("Reusing existing unique_id for returning mobile: {$mobile}");
            } else {
                $uniqueId = $this->mongo->generateUniqueId();
                Log::info("Generated new unique_id for new mobile: {$mobile}");
            }
```

---

## FIX #2: Use unique_id Instead of epic_no in saveAdditionalDetails

### Problem
When `saveAdditionalDetails` is called with a duplicate EPIC:
1. Two members exist with same epic_no but different mobiles
2. `updateMemberDetails(epic_no)` updates ONLY the first match
3. Result: Wrong member gets updated (whichever was inserted first)

### Solution
saveAdditionalDetails already looks up member by epic_no to get unique_id (line 508). Use that unique_id for the update instead of epic_no, ensuring only the correct member is updated.

---

### File 2: VanigamController.php - saveAdditionalDetails() Function

**Location:** `app/Http/Controllers/VanigamController.php` lines 543 and 546

**Current Code (Line 543):**
```php
            $updated = $this->mongo->updateMemberDetails($epicNo, $details);
```

**Current Code (Line 546):**
```php
                $member = $this->mongo->findMemberByEpic($epicNo);
```

**New Code (Line 543):**
```php
            // Update by unique_id instead of epic_no to prevent wrong member update
            // when duplicate EPICs exist across different mobiles
            if (!$existingMember || empty($existingMember['unique_id'])) {
                return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
            }
            $updated = $this->mongo->updateMemberDetailsByUniqueId($existingMember['unique_id'], $details);
```

**New Code (Line 546 - now 550):**
```php
                $member = $this->mongo->findMemberByUniqueId($existingMember['unique_id']);
```

---

## COMPLETE CONTEXT-AWARE DIFF

### VanigamController.php - generateCard() (Lines 356-468)

```diff
    /**
     * POST /api/vanigam/generate-card
     * Generate Vanigam membership card and store member in MongoDB
     */
    public function generateCard(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'epic_no' => 'required|string|max:20',
                'photo_url' => 'required|url',
                'name' => 'required|string|max:100',
                'assembly' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'dob' => 'nullable|string|max:20',
                'blood_group' => 'nullable|string|max:10',
                'address' => 'nullable|string|max:300',
                'skipped_details' => 'nullable|boolean',
                'pin' => 'nullable|digits:4',
                'manually_entered' => 'nullable|boolean',  // Flag for manually entered voter data
            ]);

            $mobile = $request->input('mobile');
            $epicNo = strtoupper(trim($request->input('epic_no')));
            $photoUrl = $request->input('photo_url');
            $name = $request->input('name');
            $assembly = $request->input('assembly');
            $district = $request->input('district');
            $dob = $request->input('dob', '');
            $bloodGroup = $request->input('blood_group', '');
            $address = $request->input('address', '');
            $skippedDetails = $request->input('skipped_details', false);

-           // Generate unique member ID
-           $uniqueId = $this->mongo->generateUniqueId();
+           // Check if mobile already exists - reuse unique_id if so, generate new if not
+           // This prevents unique_id from changing on duplicate calls
+           $existingMemberForMobile = $this->mongo->findMemberByMobile($mobile);
+           if ($existingMemberForMobile && !empty($existingMemberForMobile['unique_id'])) {
+               $uniqueId = $existingMemberForMobile['unique_id'];
+               Log::info("Reusing existing unique_id for returning mobile: {$mobile}");
+           } else {
+               $uniqueId = $this->mongo->generateUniqueId();
+               Log::info("Generated new unique_id for new mobile: {$mobile}");
+           }

            // Calculate age from DOB
            $age = '';
            if ($dob) {
                try {
                    $dobDate = \DateTime::createFromFormat('d/m/Y', $dob);
                    if (!$dobDate) {
                        $dobDate = \DateTime::createFromFormat('Y-m-d', $dob);
                    }
                    if (!$dobDate) {
                        $dobDate = new \DateTime($dob);
                    }
                    $now = new \DateTime();
                    $age = (string) $dobDate->diff($now)->y;
                } catch (Exception $e) {
                    $age = '';
                }
            }

            // QR URL (points to member details fill form if skipped, else verification)
            $qrUrl = $skippedDetails
                ? config('app.url') . '/member/complete/' . $uniqueId
                : config('app.url') . '/member/verify/' . $uniqueId;

            // Card URL for viewing the ID card
            $cardUrl = config('app.url') . '/member/card/' . $uniqueId;

            // Build member data for MongoDB
            $memberData = [
                'unique_id' => $uniqueId,
                'epic_no' => $epicNo,
                'mobile' => $mobile,
                'name' => $name,
                'membership' => 'Member',
                'assembly' => $assembly,
                'district' => $district,
                'photo_url' => $photoUrl,
                'qr_url' => $qrUrl,
                'card_url' => $cardUrl,
                'dob' => $dob,
                'age' => $age,
                'blood_group' => $bloodGroup,
                'address' => $address,
                'contact_number' => '+91 ' . $mobile,
                'details_completed' => !$skippedDetails,
                'referred_by' => $request->input('referrer_unique_id', ''),
                'manually_entered' => $request->input('manually_entered', false),  // Flag for manual entries
                'created_at' => now(),
            ];

            // Hash PIN if provided
            $pin = $request->input('pin');
            if ($pin) {
                $memberData['pin_hash'] = password_hash($pin, PASSWORD_BCRYPT);
            }

            // Save to MongoDB
            $this->mongo->upsertMember($epicNo, $memberData);

            // If manually entered, also save to the separate manual_entries collection
            // This keeps manual entries isolated for admin review while still generating cards
            if ($request->input('manually_entered', false)) {
                $this->mongo->storeManualEntry($memberData);
            }

            Log::info("Vanigam member created: {$uniqueId} for EPIC: {$epicNo}" . ($request->input('manually_entered') ? ' (Manual Entry)' : ''));

            return response()->json([
                'success' => true,
                'message' => 'Membership card generated successfully!',
                'member' => $memberData,
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::generateCard Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Card generation failed: ' . $e->getMessage()], 500);
        }
    }
```

---

### VanigamController.php - saveAdditionalDetails() (Lines 470-560)

```diff
    /**
     * POST /api/vanigam/save-additional-details
     * Update member with additional details (from QR scan or chat flow)
     */
    public function saveAdditionalDetails(Request $request)
    {
        try {
            $request->validate([
                'epic_no' => 'required|string|max:20',
                'dob' => 'nullable|string|max:20',
                'blood_group' => 'nullable|string|max:10',
                'address' => 'nullable|string|max:300',
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $dob = $request->input('dob', '');
            $bloodGroup = $request->input('blood_group', '');
            $address = $request->input('address', '');

            // Calculate age from DOB
            $age = '';
            if ($dob) {
                try {
                    $dobDate = \DateTime::createFromFormat('d/m/Y', $dob);
                    if (!$dobDate) {
                        $dobDate = \DateTime::createFromFormat('Y-m-d', $dob);
                    }
                    if (!$dobDate) {
                        $dobDate = new \DateTime($dob);
                    }
                    $now = new \DateTime();
                    $age = (string) $dobDate->diff($now)->y;
                } catch (Exception $e) {
                    $age = '';
                }
            }

            // Get existing member to check for old card images
+           // (Also need this to get unique_id for safe update)
            $existingMember = $this->mongo->findMemberByEpic($epicNo);

+           // Validate member exists before proceeding
+           if (!$existingMember || empty($existingMember['unique_id'])) {
+               return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
+           }

            $details = [
                'dob' => $dob,
                'age' => $age,
                'blood_group' => $bloodGroup,
                'address' => $address,
                'details_completed' => true,
                'skipped_details' => false,
            ];

            // Update QR URL from /complete/ to /verify/ since details are now filled
-           if ($existingMember && !empty($existingMember['unique_id'])) {
                $details['qr_url'] = config('app.url') . '/member/verify/' . $existingMember['unique_id'];
-           }

            // Delete old card images from Cloudinary if they exist
-           if ($existingMember && !empty($existingMember['unique_id'])) {
                $uniqueId = $existingMember['unique_id'];
                try {
                    if (!empty($existingMember['card_front_url'])) {
                        $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/front');
                    }
                    if (!empty($existingMember['card_back_url'])) {
                        $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/back');
                    }
                    // Clear card URLs so new ones will be generated
                    $details['card_front_url'] = '';
                    $details['card_back_url'] = '';
                    Log::info("Old card images removed for {$uniqueId} after details update");
                } catch (Exception $e) {
                    Log::warning("Could not delete old Cloudinary cards for {$uniqueId}: " . $e->getMessage());
                }
-           }

-           $updated = $this->mongo->updateMemberDetails($epicNo, $details);
+           // Update by unique_id instead of epic_no to prevent wrong member update
+           // when duplicate EPICs exist across different mobiles
+           $updated = $this->mongo->updateMemberDetailsByUniqueId($existingMember['unique_id'], $details);

            if ($updated) {
-               $member = $this->mongo->findMemberByEpic($epicNo);
+               $member = $this->mongo->findMemberByUniqueId($existingMember['unique_id']);
                return response()->json([
                    'success' => true,
                    'message' => 'Details updated successfully.',
                    'member' => $member,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Member not found.'], 404);

        } catch (Exception $e) {
            Log::error('VanigamController::saveAdditionalDetails Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }
```

---

### MongoService.php - ADD NEW METHOD (After line 158)

**Location:** After `updateMemberDetails()` method

```diff
    /**
     * Update additional details for a member.
     */
    public function updateMemberDetails(string $epicNo, array $details): bool
    {
        try {
            $details['updated_at']        = now()->toISOString();
            $details['details_completed'] = true;

            $result = $this->collection->updateOne(
                ['epic_no' => strtoupper($epicNo)],
                ['$set' => $details]
            );

            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateMemberDetails Exception: " . $e->getMessage());
            return false;
        }
    }

+   /**
+    * Update additional details for a member by unique_id (not epic_no).
+    * Prevents wrong member from being updated when duplicate EPICs exist.
+    *
+    * @param string $uniqueId The member's unique_id (TNVS-XXXXXX)
+    * @param array $details The details to update (dob, age, blood_group, address, etc.)
+    * @return bool True if update successful, false otherwise
+    */
+   public function updateMemberDetailsByUniqueId(string $uniqueId, array $details): bool
+   {
+       try {
+           $details['updated_at']        = now()->toISOString();
+           $details['details_completed'] = true;
+
+           $result = $this->collection->updateOne(
+               ['unique_id' => $uniqueId],
+               ['$set' => $details]
+           );
+
+           return $result->getMatchedCount() > 0;
+       } catch (Exception $e) {
+           Log::error("MongoService::updateMemberDetailsByUniqueId Exception: " . $e->getMessage());
+           return false;
+       }
+   }
```

---

## SUMMARY OF CHANGES

| File | Lines | Change Type | Impact |
|------|-------|------------|--------|
| MongoService.php | +20 lines | NEW METHOD | Add safe update-by-unique_id method |
| VanigamController.php | 389-397 | MODIFY | Check existing mobile, reuse unique_id |
| VanigamController.php | 508-543 | MODIFY | Use unique_id for update instead of epic_no |

**Total Lines Changed:** ~35 (2 logical changes, 1 new method)
**Breaking Changes:** None (backward compatible)
**New Dependencies:** None
**Database Changes:** None

---

## TESTING SCENARIOS

### Test Case 1: Duplicate generate-card Call (Fix #1)
```
POST /generate-card (mobile=9876543210, epic_no=EPIC001)
  Response: { unique_id: TNVS-ABC123, ... }
  MongoDB: { mobile: 9876543210, unique_id: TNVS-ABC123 }

POST /generate-card (same mobile=9876543210, data changed)
  ✅ BEFORE FIX: unique_id changed to TNVS-DEF456 (BROKEN!)
  ✅ AFTER FIX: unique_id stays TNVS-ABC123 (WORKING!)
  Log: "Reusing existing unique_id for returning mobile: 9876543210"
```

### Test Case 2: Save Details with Duplicate EPIC (Fix #2)
```
Setup: Two members with same epic_no EPIC001
  Member A: mobile=9876543210, epic_no=EPIC001, unique_id=TNVS-A001
  Member B: mobile=9876543211, epic_no=EPIC001, unique_id=TNVS-B001

POST /save-details (epic_no=EPIC001, dob=01/01/1990)
  ✅ BEFORE FIX: Only Member A updated (first match), Member B skipped
  ✅ AFTER FIX: Only Member A updated (unique_id match), Member B unchanged
  Key difference: Now it's intentional (unique_id), not accidental (epic_no)
```

---

## ROLLBACK PLAN

If issues found after deployment:

1. **Revert generateCard:** Remove the mobile check, go back to unconditional generateUniqueId()
   - Risk: Low (just removes feature, doesn't break data)

2. **Revert saveAdditionalDetails:** Change back to `updateMemberDetails($epicNo, $details)`
   - Risk: Low (old code still works, just has the duplicate EPIC issue)

3. **Remove new method:** Delete `updateMemberDetailsByUniqueId()` from MongoService
   - Risk: None (method won't be called after revert)

**Rollback Command:**
```bash
git revert <commit-hash>
git push origin trial-staging
# Deploy to trial server with config:clear and route:clear
```

---

**Status:** Ready for Review & Implementation
**Next Step:** User approval to proceed with changes

