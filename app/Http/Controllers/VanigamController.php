<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TwoFactorOtpService;
use App\Services\CacheService;
use App\Services\MongoService;
use App\Helpers\VoterHelper;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

class VanigamController extends Controller
{
    protected $otpService;
    protected $mongo;
    protected $cloudinary;
    protected $cache;

    public function __construct(TwoFactorOtpService $otpService, MongoService $mongo, CacheService $cache)
    {
        $this->otpService = $otpService;
        $this->mongo = $mongo;
        $this->cache = $cache;
        $this->cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
    }

    /**
     * POST /api/vanigam/send-otp
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Rate limit: 3 OTPs per 5 minutes per IP
            $rateLimitKey = 'otp_limit:' . $request->ip();
            $otpCount = $this->cache->get($rateLimitKey, 0);
            if ($otpCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP requests. Please try after 5 minutes.',
                    'error_code' => 'OTP_RATE_LIMIT',
                ], 429);
            }

            // Cooldown: 60s between OTP requests for same mobile
            $cooldownKey = 'otp_cooldown:' . $mobile;
            if ($this->cache->has($cooldownKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP already sent. Please wait before requesting again.',
                    'error_code' => 'OTP_COOLDOWN',
                ], 429);
            }

            $result = $this->otpService->sendOtp($mobile);

            if ($result['success']) {
                $this->cache->put($rateLimitKey, $otpCount + 1, 300);
                $this->cache->put($cooldownKey, true, 60);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to +91' . $mobile,
                    'mobile' => $mobile,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Could not send OTP.',
                'error_code' => 'OTP_SEND_FAILED',
            ], 500);

        } catch (Exception $e) {
            Log::error('VanigamController::sendOtp Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/check-member
     * Check if a mobile number already exists in MongoDB (returning user).
     */
    public function checkMember(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');
            $referrerUniqueId = $request->input('referrer_unique_id');

            // Check if this mobile belongs to the referrer (self-referral)
            if ($referrerUniqueId) {
                $referrer = $this->mongo->findMemberByUniqueId($referrerUniqueId);
                if ($referrer && isset($referrer['mobile']) && $referrer['mobile'] === $mobile) {
                    return response()->json([
                        'success' => true,
                        'is_self_referral' => true,
                    ]);
                }
            }

            $member = $this->mongo->findMemberByMobile($mobile);

            if ($member) {
                $hasPin = !empty($member['pin_hash']);
                return response()->json([
                    'success' => true,
                    'exists'  => true,
                    'has_pin' => $hasPin,
                    'name'    => $member['name'] ?? '',
                ]);
            }

            return response()->json([
                'success' => true,
                'exists'  => false,
            ]);
        } catch (Exception $e) {
            Log::error('VanigamController::checkMember Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'otp' => 'required|digits:6',
            ]);

            $mobile = $request->input('mobile');
            $otp = $request->input('otp');

            $result = $this->otpService->verifyOtp($mobile, $otp);

            if ($result['success']) {
                session(['verified_mobile' => $mobile]);

                // Check if member already exists in MongoDB
                $existingMember = $this->mongo->findMemberByMobile($mobile);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                    'mobile' => $mobile,
                    'has_membership' => !is_null($existingMember),
                    'member' => $existingMember ? [
                        'name' => $existingMember['name'] ?? '',
                        'epic_no' => $existingMember['epic_no'] ?? '',
                        'unique_id' => $existingMember['unique_id'] ?? '',
                        'membership' => $existingMember['membership'] ?? 'Member',
                        'assembly' => $existingMember['assembly'] ?? '',
                        'district' => $existingMember['district'] ?? '',
                        'photo_url' => $existingMember['photo_url'] ?? '',
                        'dob' => $existingMember['dob'] ?? '',
                        'age' => $existingMember['age'] ?? '',
                        'blood_group' => $existingMember['blood_group'] ?? '',
                        'address' => $existingMember['address'] ?? '',
                        'contact_number' => $existingMember['contact_number'] ?? '',
                        'card_front_url' => $existingMember['card_front_url'] ?? '',
                        'card_back_url' => $existingMember['card_back_url'] ?? '',
                        'details_completed' => $existingMember['details_completed'] ?? false,
                        'has_pin' => !empty($existingMember['pin_hash']),
                    ] : null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Invalid OTP.',
                'error_code' => 'INVALID_OTP',
            ], 400);

        } catch (Exception $e) {
            Log::error('VanigamController::verifyOtp Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/validate-epic
     */
    public function validateEpic(Request $request)
    {
        try {
            $request->validate([
                'epic_no' => 'required|string|min:5|max:20',
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));

            // Check MySQL voters DB (READ-ONLY)
            $voterData = VoterHelper::findByEpicNo($epicNo);

            if (!$voterData) {
                return response()->json([
                    'success' => false,
                    'message' => 'EPIC Number not found. Please check and try again.',
                    'error_code' => 'EPIC_NOT_FOUND',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'voter' => [
                    'name' => $voterData['name'] ?? '',
                    'epic_no' => $voterData['epic_no'] ?? $epicNo,
                    'assembly_name' => $voterData['assembly_name'] ?? '',
                    'district' => $voterData['district'] ?? '',
                ],
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::validateEpic Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/upload-photo
     */
    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:15360',
                'epic_no' => 'required|string|max:20',
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $photo = $request->file('photo');

            if (!in_array($photo->extension(), ['jpg', 'jpeg', 'png'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only JPG/PNG photos allowed.',
                ], 400);
            }

            // Upload to Cloudinary via direct SDK
            $result = $this->cloudinary->uploadApi()->upload($photo->getRealPath(), [
                'folder' => 'vanigan/member_photos',
                'public_id' => $epicNo . '_' . time(),
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            $photoUrl = $result['secure_url'] ?? '';

            if (!$photoUrl) {
                return response()->json(['success' => false, 'message' => 'Photo upload failed.', 'error_code' => 'PHOTO_UPLOAD_FAILED'], 500);
            }

            Log::info("Photo uploaded for {$epicNo}: {$photoUrl}");

            return response()->json([
                'success' => true,
                'photo_url' => $photoUrl,
                'message' => 'Photo uploaded successfully.',
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::uploadPhoto Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Photo upload failed: ' . $e->getMessage(), 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/validate-photo
     * Validate photo before PIN setup (real-time validation)
     */
    public function validatePhotoUpload(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:15360',
                'epic_no' => 'nullable|string|max:20',
            ]);

            $photo = $request->file('photo');

            // Validate file format
            if (!in_array($photo->extension(), ['jpg', 'jpeg', 'png'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only JPG/PNG photos allowed.',
                    'error_code' => 'INVALID_PHOTO_FORMAT',
                ], 400);
            }

            // Validate file size (max 15MB for real-time validation)
            if ($photo->getSize() > 15 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo size must be less than 15MB.',
                    'error_code' => 'PHOTO_TOO_LARGE',
                ], 400);
            }

            // Basic image validation: ensure it's a valid image file
            $imageInfo = @getimagesize($photo->getRealPath());
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file. Please upload a valid photo.',
                    'error_code' => 'INVALID_IMAGE_FILE',
                ], 400);
            }

            // Get image dimensions
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Validate image dimensions (must be at least 200x200)
            if ($width < 200 || $height < 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo is too small. Minimum resolution: 200x200 pixels.',
                    'error_code' => 'PHOTO_TOO_SMALL',
                ], 400);
            }

            // Validation passed
            return response()->json([
                'success' => true,
                'message' => 'Photo validated successfully.',
                'width' => $width,
                'height' => $height,
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::validatePhotoUpload Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Photo validation failed: ' . $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

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
                'created_at' => now()->toISOString(),
            ];

            // Hash PIN if provided (reject weak/common PINs)
            $pin = $request->input('pin');
            if ($pin) {
                if (self::isWeakPin($pin)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN is too easy to guess. Please choose a stronger PIN (avoid 1234, 0000, 1111, etc.).',
                        'error_code' => 'WEAK_PIN',
                    ], 400);
                }
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
                'unique_id' => 'nullable|string|max:30',
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $dob = $request->input('dob', '');
            $bloodGroup = $request->input('blood_group', '');
            $address = $request->input('address', '');
            $requestUniqueId = $request->input('unique_id', '');

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

            // Look up member by unique_id first (safe for duplicate EPICs), fall back to epic_no
            $existingMember = null;
            if ($requestUniqueId) {
                $existingMember = $this->mongo->findMemberByUniqueId($requestUniqueId);
            }
            if (!$existingMember) {
                $existingMember = $this->mongo->findMemberByEpic($epicNo);
            }

            // Validate member exists before proceeding
            if (!$existingMember || empty($existingMember['unique_id'])) {
                return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
            }

            $details = [
                'dob' => $dob,
                'age' => $age,
                'blood_group' => $bloodGroup,
                'address' => $address,
                'details_completed' => true,
                'skipped_details' => false,
            ];

            // Update QR URL from /complete/ to /verify/ since details are now filled
            $details['qr_url'] = config('app.url') . '/member/verify/' . $existingMember['unique_id'];

            // Delete old card images from Cloudinary if they exist
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

            // Update by unique_id instead of epic_no to prevent wrong member update
            // when duplicate EPICs exist across different mobiles
            $updated = $this->mongo->updateMemberDetailsByUniqueId($existingMember['unique_id'], $details);

            if ($updated) {
                $member = $this->mongo->findMemberByUniqueId($existingMember['unique_id']);
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

    /**
     * GET /api/vanigam/member/{uniqueId}
     * Get member info (for QR verification)
     */
    public function getMember($uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);

            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found.'], 404);
            }

            return response()->json([
                'success' => true,
                'member' => [
                    'unique_id' => $member['unique_id'] ?? '',
                    'name' => $member['name'] ?? '',
                    'epic_no' => $member['epic_no'] ?? '',
                    'mobile' => $member['mobile'] ?? '',
                    'membership' => $member['membership'] ?? 'Member',
                    'assembly' => $member['assembly'] ?? '',
                    'district' => $member['district'] ?? '',
                    'photo_url' => $member['photo_url'] ?? '',
                    'dob' => $member['dob'] ?? '',
                    'age' => $member['age'] ?? '',
                    'blood_group' => $member['blood_group'] ?? '',
                    'address' => $member['address'] ?? '',
                    'contact_number' => $member['contact_number'] ?? '',
                    'details_completed' => $member['details_completed'] ?? false,
                    'card_front_url' => $member['card_front_url'] ?? '',
                    'card_back_url' => $member['card_back_url'] ?? '',
                    'referral_id' => $member['referral_id'] ?? '',
                    'referral_count' => $member['referral_count'] ?? 0,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::getMember Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * GET /member/card/{uniqueId}
     * Render the Vanigam card HTML (using id_card.html template)
     */
    public function showCard($uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);

            if (!$member) {
                abort(404, 'Member not found.');
            }

            $generatedAt = now()->format('d M Y, h:i A');
            $baseUrl = config('app.url');

            return view('card.vanigam', [
                'member' => (object) $member,
                'generated_at' => $generatedAt,
                'base_url' => $baseUrl,
                'api_v1_prefix' => '/api/vanigam',
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::showCard Error: ' . $e->getMessage());
            abort(500, 'An error occurred.');
        }
    }

    /**
     * GET /member/complete/{uniqueId}
     * Show form to complete additional details (from QR scan)
     */
    public function completeDetails($uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);

            if (!$member) {
                abort(404, 'Member not found.');
            }

            // If details already completed, redirect to verify page (3D card view)
            if (!empty($member['details_completed']) && $member['details_completed']) {
                return redirect()->route('member.verify', ['uniqueId' => $uniqueId]);
            }

            return view('member.complete', [
                'member' => (object) $member,
                'unique_id' => $uniqueId,
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::completeDetails Error: ' . $e->getMessage());
            abort(500, 'An error occurred.');
        }
    }

    /**
     * GET /member/verify/{uniqueId}
     * Public verification page - shows PIN entry first
     */
    public function verifyMember($uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);

            if (!$member) {
                abort(404, 'Member not found.');
            }

            // If details not completed, redirect to complete details form
            if (empty($member['details_completed']) || !$member['details_completed']) {
                return redirect()->route('member.complete', ['uniqueId' => $uniqueId]);
            }

            // Show PIN verification page (card hidden until PIN entered)
            return view('member.verify', [
                'member' => (object) $member,
                'unique_id' => $uniqueId,
            ]);

        } catch (Exception $e) {
            Log::error('VanigamController::verifyMember Error: ' . $e->getMessage());
            abort(500, 'An error occurred.');
        }
    }

    /**
     * POST /api/vanigam/verify-member-pin
     * Verify PIN for QR scan verification (by unique_id)
     */
    public function verifyMemberPin(Request $request)
    {
        try {
            $request->validate([
                'unique_id' => 'required|string|max:20',
                'pin'       => 'required|digits:4',
            ]);

            $member = $this->mongo->findMemberByUniqueId($request->input('unique_id'));
            if (!$member || empty($member['pin_hash'])) {
                return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.'], 404);
            }

            if (password_verify($request->input('pin'), $member['pin_hash'])) {
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.', 'error_code' => 'INVALID_PIN'], 400);
        } catch (Exception $e) {
            Log::error('VanigamController::verifyMemberPin Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * GET /api/vanigam/qr/{uniqueId}
     * Generate QR code PNG on-the-fly for card view
     */
    public function generateQr($uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);
            $qrUrl = $member['qr_url'] ?? (config('app.url') . '/member/verify/' . $uniqueId);

            $qrCode = new QrCode(data: $qrUrl, size: 300, margin: 10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            return response($result->getString(), 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=86400');
        } catch (Exception $e) {
            Log::error('VanigamController::generateQr Error: ' . $e->getMessage());
            abort(500);
        }
    }

    /**
     * GET /api/health
     * Health check endpoint with Redis connectivity testing
     */
    public function health()
    {
        try {
            $health = [
                'success' => true,
                'app' => 'Tamil Nadu Vanigargalin Sangamam',
                'timestamp' => now()->toIso8601String(),
                'uptime' => floor(microtime(true)),
            ];

            // Check MySQL connections
            try {
                \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
                $health['mysql'] = 'ok';
            } catch (\Exception $e) {
                $health['mysql'] = 'error';
                $health['mysql_error'] = $e->getMessage();
            }

            try {
                \Illuminate\Support\Facades\DB::connection('voters')->getPdo();
                $health['voters_db'] = 'ok';
            } catch (\Exception $e) {
                $health['voters_db'] = 'error';
                $health['voters_db_error'] = $e->getMessage();
            }

            // Check Cache (including Redis connectivity)
            try {
                $cacheDriver = config('cache.default');

                if ($cacheDriver === 'redis') {
                    // Test Redis PING explicitly - wrap in try-catch for extra safety
                    try {
                        $redisTest = $this->cache->testRedisPing();

                        if ($redisTest['status'] === 'ok') {
                            $health['redis'] = 'ok';
                            $health['cache'] = 'ok (redis)';
                        } elseif ($redisTest['status'] === 'unavailable') {
                            $health['redis'] = 'unavailable';
                            $health['cache'] = 'unavailable (redis fallback to file)';
                            $health['redis_error'] = $redisTest['message'];
                        } else {
                            $health['redis'] = $redisTest['status'];
                            $health['cache'] = $redisTest['status'] . ' (redis)';
                            if (isset($redisTest['message'])) {
                                $health['redis_message'] = $redisTest['message'];
                            }
                        }
                    } catch (\Exception $redisException) {
                        // Redis test threw exception - report as unavailable but app is still ok
                        $health['redis'] = 'unavailable';
                        $health['cache'] = 'ok (file fallback)';
                        $health['redis_error'] = $redisException->getMessage();
                        Log::warning('Redis health check error (fallback active)', [
                            'exception' => $redisException->getMessage(),
                        ]);
                    }
                } else {
                    // File or other cache driver
                    try {
                        $this->cache->get('health_check_test');
                        $health['cache'] = 'ok (' . $cacheDriver . ')';
                    } catch (\Exception $cacheException) {
                        $health['cache'] = 'error (' . $cacheDriver . ')';
                        $health['cache_error'] = $cacheException->getMessage();
                    }
                }
            } catch (\Exception $e) {
                $health['cache'] = 'error';
                $health['cache_error'] = $e->getMessage();
                Log::warning('Health check cache test failed', ['exception' => $e->getMessage()]);
            }

            return response()->json($health);

        } catch (\Exception $e) {
            Log::error('Health check failed', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'HEALTH_CHECK_FAILED',
            ], 500);
        }
    }

    /**
     * POST /api/vanigam/get-referred-members
     * Get all members referred by a specific member.
     */
    public function getReferredMembers(Request $request)
    {
        try {
            $request->validate([
                'unique_id' => 'required|string',
            ]);

            $uniqueId = $request->input('unique_id');
            $members = $this->mongo->getMembersReferredBy($uniqueId);

            // Return only safe fields for each referred member
            $safeMembers = array_map(function ($m) {
                return [
                    'unique_id'   => $m['unique_id'] ?? '',
                    'name'        => $m['name'] ?? '',
                    'epic_no'     => $m['epic_no'] ?? '',
                    'assembly'    => $m['assembly'] ?? '',
                    'district'    => $m['district'] ?? '',
                    'photo_url'   => $m['photo_url'] ?? '',
                    'mobile'      => $m['mobile'] ?? '',
                    'created_at'  => $m['created_at'] ?? '',
                ];
            }, $members);

            return response()->json([
                'success' => true,
                'members' => $safeMembers,
                'count'   => count($safeMembers),
            ]);
        } catch (Exception $e) {
            Log::error('VanigamController::getReferredMembers Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * POST /api/vanigam/reset-members
     * Delete all members from MongoDB (does NOT touch MySQL)
     */
    public function resetMembers(Request $request)
    {
        try {
            $key = $request->input('confirm_key', '');
            $expectedKey = config('vanigam.reset_key');
            if (empty($expectedKey)) {
                return response()->json(['success' => false, 'message' => 'Reset key not configured.', 'error_code' => 'RESET_KEY_NOT_SET'], 500);
            }
            if (empty($key) || !hash_equals($expectedKey, $key)) {
                return response()->json(['success' => false, 'message' => 'Invalid confirmation key.', 'error_code' => 'INVALID_RESET_KEY'], 403);
            }

            $result = $this->mongo->deleteAllMembers();
            if ($result !== null) {
                $deletedCount = $result['deletedCount'] ?? 0;
                return response()->json([
                    'success' => true,
                    'message' => "Deleted {$deletedCount} members from MongoDB.",
                    'deleted_count' => $deletedCount,
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Failed to reset MongoDB.', 'error_code' => 'RESET_FAILED'], 500);
        } catch (Exception $e) {
            Log::error('VanigamController::resetMembers Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/upload-card-images
     * Upload front/back card images to Cloudinary, organized by unique_id
     */
    public function uploadCardImages(Request $request)
    {
        try {
            $request->validate([
                'unique_id'   => 'required|string|max:20',
                'front_image' => 'required|string',
                'back_image'  => 'required|string',
            ]);

            $uniqueId  = $request->input('unique_id');
            $urls = [];

            foreach (['front' => $request->input('front_image'), 'back' => $request->input('back_image')] as $side => $dataUrl) {
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $tempPath = $tempDir . '/card_' . $uniqueId . '_' . $side . '.png';
                file_put_contents($tempPath, base64_decode($base64));

                $result = $this->cloudinary->uploadApi()->upload($tempPath, [
                    'folder'        => 'vanigan/cards/' . $uniqueId,
                    'public_id'     => $side,
                    'overwrite'     => true,
                    'resource_type' => 'image',
                ]);

                $urls[$side . '_url'] = $result['secure_url'] ?? '';
                @unlink($tempPath);
            }

            $this->mongo->updateCardUrls($uniqueId, $urls['front_url'], $urls['back_url']);

            return response()->json([
                'success'   => true,
                'front_url' => $urls['front_url'],
                'back_url'  => $urls['back_url'],
            ]);
        } catch (Exception $e) {
            Log::error('VanigamController::uploadCardImages Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Card image upload failed.', 'error_code' => 'CARD_UPLOAD_FAILED'], 500);
        }
    }

    /**
     * POST /api/vanigam/verify-pin
     * Verify a returning user's 4-digit PIN
     */
    public function verifyPin(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'pin'    => 'required|digits:4',
            ]);

            $member = $this->mongo->findMemberByMobile($request->input('mobile'));
            if (!$member || empty($member['pin_hash'])) {
                return response()->json(['success' => false, 'message' => 'Member not found or PIN not set.', 'error_code' => 'MEMBER_OR_PIN_NOT_FOUND'], 404);
            }

            if (password_verify($request->input('pin'), $member['pin_hash'])) {
                // Remove pin_hash from response
                unset($member['pin_hash'], $member['_id']);
                return response()->json([
                    'success' => true,
                    'member'  => $member,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid PIN. Please try again.', 'error_code' => 'INVALID_PIN'], 400);
        } catch (Exception $e) {
            Log::error('VanigamController::verifyPin Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/get-referral
     * Get or create referral link for a member
     */
    public function getReferral(Request $request)
    {
        try {
            $uniqueId = $request->input('unique_id');
            Log::info("getReferral called with unique_id: " . ($uniqueId ?? 'null'));
            if (!$uniqueId) {
                return response()->json(['success' => false, 'message' => 'Missing unique_id.', 'error_code' => 'MISSING_UNIQUE_ID'], 400);
            }

            $referralId = $this->mongo->getOrCreateReferralId($uniqueId);
            if (!$referralId) {
                return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
            }

            $member = $this->mongo->findMemberByUniqueId($uniqueId);
            $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/');
            $referralLink = $baseUrl . '/refer/' . $uniqueId . '/' . $referralId;

            return response()->json([
                'success' => true,
                'referral_id' => $referralId,
                'referral_link' => $referralLink,
                'referral_count' => $member['referral_count'] ?? 0,
            ]);
        } catch (Exception $e) {
            Log::error('VanigamController::getReferral Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * GET /refer/{uniqueId}/{referralId}
     * Referral landing page — redirect to registration with referrer tracking
     */
    public function handleReferral($uniqueId, $referralId)
    {
        try {
            // Don't increment here — only increment after new user generates card
            return redirect('/?ref=' . $uniqueId . '&ref_id=' . $referralId);
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    /**
     * POST /api/vanigam/increment-referral
     * Increment referral count (called after new user generates card via referral)
     */
    public function incrementReferral(Request $request)
    {
        try {
            $referrerUniqueId = $request->input('referrer_unique_id');
            if (!$referrerUniqueId) {
                return response()->json(['success' => false, 'message' => 'Missing referrer ID.', 'error_code' => 'MISSING_REFERRER_ID'], 400);
            }

            $this->mongo->incrementReferralCount($referrerUniqueId);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('VanigamController::incrementReferral Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/loan-request
     * Submit a loan request from a member
     */
    public function loanRequest(Request $request)
    {
        try {
            $uniqueId = $request->input('unique_id');
            $businessType = $request->input('business_type');
            $businessName = $request->input('business_name');

            if (!$uniqueId || !$businessType || !$businessName) {
                return response()->json(['success' => false, 'message' => 'Missing required fields.', 'error_code' => 'MISSING_REQUIRED_FIELDS'], 400);
            }

            $member = $this->mongo->findMemberByUniqueId($uniqueId);
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found.', 'error_code' => 'MEMBER_NOT_FOUND'], 404);
            }

            // Store loan request
            $loanRequest = [
                'unique_id' => $uniqueId,
                'member_name' => $member['name'] ?? '',
                'mobile' => $member['mobile'] ?? '',
                'business_type' => $businessType,
                'business_name' => $businessName,
                'status' => 'pending',
                'created_at' => now()->toISOString(),
            ];

            $this->mongo->storeLoanRequest($loanRequest);

            Log::info("Loan request submitted: " . json_encode($loanRequest));

            return response()->json(['success' => true, 'message' => 'Loan request submitted successfully.']);
        } catch (Exception $e) {
            Log::error('VanigamController::loanRequest Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * POST /api/vanigam/check-loan-status
     * Check if a member has already applied for a loan (by mobile number)
     */
    public function checkLoanStatus(Request $request)
    {
        try {
            $uniqueId = $request->input('unique_id');
            $mobile = $request->input('mobile');

            if (!$uniqueId && !$mobile) {
                return response()->json(['success' => false, 'message' => 'Missing unique_id or mobile.', 'error_code' => 'MISSING_PARAMETERS'], 400);
            }

            $loanRequest = null;

            // First check by mobile number (to prevent same mobile from applying multiple times)
            if ($mobile) {
                $loanRequest = $this->mongo->getLoanRequestByMobile($mobile);
            }

            // If not found by mobile and unique_id is provided, check by unique_id
            if (!$loanRequest && $uniqueId) {
                $loanRequest = $this->mongo->getLoanRequestByUniqueId($uniqueId);
            }

            // Also check by getting member's mobile from unique_id
            if (!$loanRequest && $uniqueId) {
                $member = $this->mongo->findMemberByUniqueId($uniqueId);
                if ($member && !empty($member['mobile'])) {
                    $loanRequest = $this->mongo->getLoanRequestByMobile($member['mobile']);
                }
            }

            if ($loanRequest) {
                return response()->json([
                    'success' => true,
                    'has_applied' => true,
                    'loan_request' => [
                        'business_type' => $loanRequest['business_type'] ?? '',
                        'business_name' => $loanRequest['business_name'] ?? '',
                        'status' => $loanRequest['status'] ?? 'pending',
                        'created_at' => $loanRequest['created_at'] ?? '',
                    ]
                ]);
            }

            return response()->json(['success' => true, 'has_applied' => false]);
        } catch (Exception $e) {
            Log::error('VanigamController::checkLoanStatus Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error_code' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * Check if a 4-digit PIN is weak/common and should be rejected.
     * Returns true if the PIN is weak.
     */
    public static function isWeakPin(string $pin): bool
    {
        $blacklist = [
            '0000', '1111', '2222', '3333', '4444', '5555', '6666', '7777', '8888', '9999',
            '1234', '4321', '1212', '2121', '1122', '2211', '0123', '3210',
            '5678', '8765', '6789', '9876', '0987', '7890',
            '1010', '2020', '6969', '1357', '2468',
        ];
        if (in_array($pin, $blacklist, true)) {
            return true;
        }
        // Reject all-same-digit (already covered above but defensive)
        if (preg_match('/^(\d)\1{3}$/', $pin)) {
            return true;
        }
        return false;
    }
}
