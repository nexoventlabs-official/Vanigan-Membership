<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\OtpSession;
use App\Models\GeneratedVoter;
use App\Models\GenerationStat;
use App\Models\VerifiedMobile;
use App\Models\VolunteerRequest;
use App\Models\BoothAgentRequest;
use App\Services\OtpService;
use App\Services\CacheService;
use App\Helpers\SecurityHelper;

class UserController extends Controller
{
    protected $otpService;
    protected $cache;

    public function __construct(OtpService $otpService, CacheService $cache)
    {
        $this->otpService = $otpService;
        $this->cache = $cache;
    }

    /**
     * POST /api/chat/send-otp
     * Send OTP to mobile number
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Check rate limit (3 OTPs per 5 minutes per IP)
            $rateLimitKey = 'otp_limit:' . $request->ip();
            $otpCount = $this->cache->get($rateLimitKey, 0);

            if ($otpCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP requests. Please try after 5 minutes.',
                    'error_type' => 'rate_limit_exceeded'
                ], 429);
            }

            // Check mobile cooldown (60s between OTP requests for same mobile)
            $otpRecord = OtpSession::where('mobile', $mobile)
                ->where('created_at', '>', now()->subSeconds(60))
                ->first();

            if ($otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP already sent. Please wait before requesting again.',
                    'error_type' => 'mobile_cooldown'
                ], 429);
            }

            // Generate OTP
            $otp = random_int(100000, 999999);

            // Send via 2Factor.in
            $smsResult = $this->otpService->sendOtpVia2Factor($mobile, $otp);

            if (!$smsResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not send OTP. Please try again.',
                    'error_type' => 'sms_failed'
                ], 500);
            }

            // Save OTP to database (only if SMS sent successfully)
            OtpSession::updateOrCreate(
                ['mobile' => $mobile],
                [
                    'otp' => $otp,
                    'verified' => false,
                    'attempts' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Increment rate limit counter
            $this->cache->put($rateLimitKey, $otpCount + 1, 300); // 5 minutes

            $response = [
                'success' => true,
                'message' => 'OTP sent successfully.',
                'mobile' => $mobile
            ];

            // Add OTP to response in local testing mode
            if (config('app.env') === 'local' && isset($smsResult['test_otp'])) {
                $response['test_otp'] = $smsResult['test_otp'];
                $response['message'] = "OTP sent! For testing: {$smsResult['test_otp']}";
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('SendOtp Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/verify-otp
     * Verify OTP and set session
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

            // Get OTP session
            $otpSession = OtpSession::where('mobile', $mobile)->first();

            if (!$otpSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP not found. Please request a new OTP.',
                    'error_type' => 'otp_not_found'
                ], 400);
            }

            // Check OTP expiry (5 minutes = 300 seconds)
            if ($otpSession->created_at < now()->subSeconds(300)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP expired. Please request a new one.',
                    'error_type' => 'otp_expired'
                ], 400);
            }

            // Check attempts (max 5)
            if ($otpSession->attempts >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum OTP attempts exceeded. Please request a new OTP.',
                    'error_type' => 'max_attempts_exceeded'
                ], 400);
            }

            // Verify OTP
            if ($otpSession->otp != $otp) {
                $otpSession->increment('attempts');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.',
                    'error_type' => 'invalid_otp',
                    'attempts_remaining' => 5 - $otpSession->attempts
                ], 400);
            }

            // Mark as verified
            $otpSession->update([
                'verified' => true,
                'verified_at' => now(),
            ]);

            // Set session
            session(['verified_mobile' => $mobile]);

            // Check if user has existing card
            $existingCard = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->orderBy('created_at', 'desc')
                ->first();

            $voterName = null;
            if ($existingCard) {
                $voterName = $this->cleanVoterName($existingCard->FM_NAME_EN, $existingCard->LASTNAME_EN);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'mobile' => $mobile,
                'has_card' => !is_null($existingCard),
                'card_url' => $existingCard?->card_url,
                'voter_name' => $voterName,
            ]);

        } catch (\Exception $e) {
            \Log::error('VerifyOtp Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * GET /verify/{epic_no}
     * Public card verification page
     */
    public function verifyCard($epicNo)
    {
        try {
            $card = GeneratedVoter::where('epic_no', $epicNo)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'message' => 'Card not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'card' => [
                    'epic_no' => $card->epic_no,
                    'voter_name' => $card->voter_name,
                    'assembly' => $card->assembly_name,
                    'district' => $card->district,
                    'card_url' => $card->card_url,
                    'generated_at' => $card->created_at,
                    'ptc_code' => $card->ptc_code,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('VerifyCard Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    /**
     * POST /api/chat/generate-card
     * Generate voter ID card (async job dispatch)
     */
    public function generateCard(Request $request)
    {
        try {
            $mobile = session('verified_mobile');
            if (!$mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify mobile first.',
                    'error_type' => 'not_verified'
                ], 401);
            }

            $request->validate([
                'epic_no' => 'required|string|max:20',
                'photo' => 'required|image|max:10240', // 10MB
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $photo = $request->file('photo');

            // Validate file
            if (!in_array($photo->extension(), ['jpg', 'jpeg', 'png'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only JPG/PNG photos allowed.',
                    'error_type' => 'invalid_file_type'
                ], 400);
            }

            // Face detection service
            $faceDetectionService = new \App\Services\FaceDetectionService();
            $faceResult = $faceDetectionService->validatePhotoForIdCard($photo->getRealPath());

            if (!$faceResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $faceResult['message'],
                    'error_type' => 'face_detection_failed'
                ], 400);
            }

            // Upload photo to Cloudinary
            $cloudinaryService = new \App\Services\CloudinaryService();
            $photoUrl = $cloudinaryService->uploadPhoto($photo->getRealPath(), $epicNo);

            // TODO: Fetch voter data from voters DB
            $voterData = [
                'epic_no' => $epicNo,
                'voter_name' => 'Sample Voter', // Replace with actual lookup
                'assembly_name' => 'Sample Assembly',
                'district' => 'Sample District',
                'ptc_code' => '',
            ];

            // Generate job ID
            $jobId = 'job_' . uniqid() . '_' . time();

            // Dispatch async job
            \App\Jobs\GenerateCardJob::dispatch(
                $jobId,
                $mobile,
                $epicNo,
                $photoUrl,
                $voterData,
                $request->input('referral_code')
            );

            return response()->json([
                'success' => true,
                'message' => 'Card generation started. Check status using job_id.',
                'job_id' => $jobId,
                'status_url' => '/api/chat/card-status/' . $jobId,
            ]);

        } catch (\Exception $e) {
            \Log::error('GenerateCard Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * GET /api/chat/card-status/{job_id}
     * Poll card generation job status
     */
    public function cardStatus($jobId)
    {
        try {
            $jobStatus = $this->cache->get('job:' . $jobId);

            if (!$jobStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found.',
                    'error_type' => 'job_not_found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $jobStatus['status'],
                'progress' => $jobStatus['progress'] ?? 0,
                'card_url' => $jobStatus['card_url'] ?? null,
                'message' => $jobStatus['message'] ?? null,
            ]);

        } catch (\Exception $e) {
            \Log::error('CardStatus Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    /**
     * Show user home page
     */
    public function home()
    {
        return view('user.home');
    }

    /**
     * Generate card (web form submission)
     */
    public function generateCardWeb(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'epic_no' => 'required|string|max:20',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'ref_ptc' => 'nullable|string|max:20',
                'ref_rid' => 'nullable|string|max:50',
            ]);

            $mobile = $request->input('mobile');
            $epicNo = strtoupper(trim($request->input('epic_no')));
            $photo = $request->file('photo');
            $refPtc = $request->input('ref_ptc', '');
            $refRid = $request->input('ref_rid', '');

            // For local testing, save photo locally instead of Cloudinary
            if (env('APP_ENV') === 'local') {
                // Save photo locally for testing
                $photoPath = $photo->store('temp_photos', 'local');
                // Fix: Use Storage facade to get the correct full path
                $photoUrl = \Storage::disk('local')->path($photoPath);
            } else {
                // Upload photo to Cloudinary for production
                $cloudinaryService = new \App\Services\CloudinaryService();
                $photoUrl = $cloudinaryService->uploadPhoto($photo->getRealPath(), $epicNo);
            }

            // Fetch voter data
            $voterData = \App\Helpers\VoterHelper::findByEpicNo($epicNo);

            if (!$voterData) {
                return back()->withErrors(['epic_no' => 'Voter not found. Please check your EPIC number.']);
            }

            // Generate job ID
            $jobId = 'job_' . uniqid() . '_' . time();

            // For local testing, generate card synchronously
            if (env('APP_ENV') === 'local') {
                try {
                    $cardService = new \App\Services\CardGenerationService();
                    $cardPath = $cardService->generateCard($voterData, $photoUrl, $epicNo);
                    
                    // Save to database (same as GenerateCardJob does)
                    $generatedVoter = GeneratedVoter::updateOrCreate(
                        ['EPIC_NO' => $epicNo, 'MOBILE_NO' => $mobile],
                        [
                            'ptc_code' => $voterData['ptc_code'] ?? '',
                            'AC_NO' => $voterData['assembly'] ?? '',
                            'ASSEMBLY_NAME' => $voterData['assembly_name'] ?? '',
                            'PART_NO' => $voterData['part_no'] ?? '',
                            'SECTION_NO' => $voterData['section_no'] ?? '',
                            'C_HOUSE_NO' => $voterData['house_no'] ?? '',
                            'FM_NAME_EN' => $voterData['first_name'] ?? '',
                            'LASTNAME_EN' => $voterData['last_name'] ?? '',
                            'RLN_TYPE' => $voterData['relation_type'] ?? '',
                            'RLN_FM_NM_EN' => $voterData['relation_name'] ?? '',
                            'GENDER' => $voterData['sex'] ?? '',
                            'AGE' => $voterData['age'] ?? '',
                            'DOB' => $voterData['dob'] ?? '',
                            'DISTRICT_NAME' => $voterData['district'] ?? '',
                            'card_url' => $cardPath,
                            'photo_url' => $photoUrl,
                            'generated_at' => now(),
                            'referred_by_ptc' => $refPtc ?: null,
                            'referred_by_referral_id' => $refRid ?: null,
                        ]
                    );

                    // Increment referrer count if this was a referral
                    if ($refPtc) {
                        GeneratedVoter::where('ptc_code', $refPtc)
                            ->increment('referred_members_count');
                        
                        \Log::info("Referral tracked: {$epicNo} referred by {$refPtc}");
                    }
                    
                    // Save the result for display
                    session(['card_result' => [
                        'jobId' => $jobId,
                        'status' => 'completed',
                        'card_path' => $cardPath,
                        'voter_data' => $voterData,
                        'mobile' => $mobile
                    ]]);
                    
                    return redirect()->route('user.card-status', ['jobId' => $jobId]);
                    
                } catch (\Exception $e) {
                    \Log::error('Local card generation failed: ' . $e->getMessage());
                    return back()->withErrors(['error' => 'Card generation failed: ' . $e->getMessage()]);
                }
            } else {
                // Dispatch async job for production
                \App\Jobs\GenerateCardJob::dispatch(
                    $jobId,
                    $mobile,
                    $epicNo,
                    $photoUrl,
                    $voterData
                );

                return redirect()->route('user.card-status', ['jobId' => $jobId]);
            }

        } catch (\Exception $e) {
            \Log::error('GenerateCardWeb Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Show card status/result page
     */
    public function cardStatusWeb($jobId)
    {
        try {
            // For local testing, check session first
            if (env('APP_ENV') === 'local') {
                $sessionResult = session('card_result');
                if ($sessionResult && $sessionResult['jobId'] === $jobId) {
                    if ($sessionResult['status'] === 'completed') {
                        return view('user.card', [
                            'card_url' => asset('storage/generated-cards/' . basename($sessionResult['card_path'])),
                            'ptc_code' => 'LOCAL_' . substr($jobId, -6),
                            'voter_name' => $sessionResult['voter_data']['name'] ?? 'N/A',
                            'epic_no' => $sessionResult['voter_data']['epic_no'] ?? '',
                            'assembly_name' => $sessionResult['voter_data']['assembly_name'] ?? '',
                            'gen_count' => 1,
                        ]);
                    }
                }
            }

            // Check cache for production jobs
            $jobStatus = $this->cache->get('job:' . $jobId);

            if (!$jobStatus) {
                return view('user.card-generating', ['jobId' => $jobId]);
            }

            if ($jobStatus['status'] === 'completed') {
                return view('user.card', [
                    'card_url' => $jobStatus['card_url'] ?? '',
                    'ptc_code' => $jobStatus['ptc_code'] ?? '',
                    'voter_name' => $jobStatus['voter_name'] ?? 'N/A',
                    'epic_no' => $jobStatus['epic_no'] ?? '',
                    'assembly_name' => $jobStatus['assembly_name'] ?? '',
                    'gen_count' => $jobStatus['gen_count'] ?? 1,
                ]);
            }

            if ($jobStatus['status'] === 'failed') {
                return view('user.card-error', [
                    'error' => $jobStatus['message'] ?? 'Card generation failed',
                    'jobId' => $jobId,
                ]);
            }

            return view('user.card-generating', [
                'jobId' => $jobId,
                'progress' => $jobStatus['progress'] ?? 0,
                'message' => $jobStatus['message'] ?? 'Generating your card...',
            ]);

        } catch (\Exception $e) {
            \Log::error('CardStatusWeb Error: ' . $e->getMessage());
            return view('user.card-error', ['error' => 'An error occurred']);
        }
    }

    /**
     * POST /api/chat/request-volunteer
     * Submit volunteer request
     */
    public function requestVolunteer(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Check if user has generated a card
            $generatedVoter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();
            
            if (!$generatedVoter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please generate your voter ID card first before requesting volunteer status.',
                    'error_type' => 'no_card_found'
                ], 400);
            }

            // Check if already requested
            $existingRequest = VolunteerRequest::where('mobile', $mobile)->first();
            
            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'already_requested' => true,
                    'status' => $existingRequest->status,
                    'message' => "You have already submitted a volunteer request. Status: {$existingRequest->status}",
                    'error_type' => 'already_requested'
                ], 400);
            }

            // Create volunteer request
            $volunteerRequest = VolunteerRequest::create([
                'ptc_code' => $generatedVoter->ptc_code,
                'epic_no' => $generatedVoter->EPIC_NO,
                'name' => trim(($generatedVoter->FM_NAME_EN ?? '') . ' ' . ($generatedVoter->LASTNAME_EN ?? '')),
                'mobile' => $mobile,
                'assembly' => $generatedVoter->ASSEMBLY_NAME,
                'photo_url' => $generatedVoter->photo_url,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'already_requested' => false,
                'status' => 'pending',
                'message' => 'Volunteer request submitted successfully. You will be notified once reviewed.',
                'request_id' => $volunteerRequest->id
            ]);

        } catch (\Exception $e) {
            \Log::error('RequestVolunteer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your request.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/request-booth-agent
     * Submit booth agent request
     */
    public function requestBoothAgent(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Check if user has generated a card
            $generatedVoter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();
            
            if (!$generatedVoter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please generate your voter ID card first before requesting booth agent status.',
                    'error_type' => 'no_card_found'
                ], 400);
            }

            // Check if already requested
            $existingRequest = BoothAgentRequest::where('mobile', $mobile)->first();
            
            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'already_requested' => true,
                    'status' => $existingRequest->status,
                    'message' => "You have already submitted a booth agent request. Status: {$existingRequest->status}",
                    'error_type' => 'already_requested'
                ], 400);
            }

            // Create booth agent request
            $boothAgentRequest = BoothAgentRequest::create([
                'ptc_code' => $generatedVoter->ptc_code,
                'epic_no' => $generatedVoter->EPIC_NO,
                'name' => trim(($generatedVoter->FM_NAME_EN ?? '') . ' ' . ($generatedVoter->LASTNAME_EN ?? '')),
                'mobile' => $mobile,
                'assembly' => $generatedVoter->ASSEMBLY_NAME,
                'photo_url' => $generatedVoter->photo_url,
                'status' => 'pending',
                'source' => 'web',
                'requested_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'already_requested' => false,
                'status' => 'pending',
                'message' => 'Booth agent request submitted successfully. You will be notified once reviewed.',
                'request_id' => $boothAgentRequest->id
            ]);

        } catch (\Exception $e) {
            \Log::error('RequestBoothAgent Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your request.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    // ========================================
    // PIN AUTHENTICATION SYSTEM (Python Flask Parity)
    // ========================================

    /**
     * Helper function to clean voter name formatting
     */
    private function cleanVoterName($firstName, $lastName)
    {
        $name = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
        // Remove trailing hyphens, underscores and clean up
        $name = trim(str_replace([' -', '- ', ' _', '_ ', '-', '_'], ' ', $name));
        $name = preg_replace('/\s+/', ' ', $name); // Remove extra spaces
        return trim($name);
    }

    /**
     * POST /api/chat/check-mobile
     * Check if mobile number has existing card and PIN status
     */
    public function checkMobile(Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('CheckMobile API called', [
                'mobile' => $request->input('mobile'),
                'all_input' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');
            \Log::info('CheckMobile validated mobile: ' . $mobile);

            // Find existing voter by mobile
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$voter) {
                \Log::info('CheckMobile: No voter found for mobile: ' . $mobile);
                return response()->json([
                    'success' => true,
                    'has_card' => false,
                    'has_pin' => false,
                    'epic_no' => null,
                    'voter_name' => null,
                    'card_url' => null
                ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                  ->header('Pragma', 'no-cache')
                  ->header('Expires', '0');
            }

            $voterName = $this->cleanVoterName($voter->FM_NAME_EN, $voter->LASTNAME_EN);
            $hasPin = !is_null($voter->secret_pin) && $voter->secret_pin !== '';
            
            \Log::info('CheckMobile: Found voter', [
                'epic' => $voter->EPIC_NO,
                'mobile' => $voter->MOBILE_NO,
                'has_pin' => $hasPin,
                'pin_length' => strlen($voter->secret_pin ?? '')
            ]);

            return response()->json([
                'success' => true,
                'has_card' => true,
                'has_pin' => $hasPin,
                'epic_no' => $voter->EPIC_NO,
                'voter_name' => $voterName,
                'card_url' => $voter->card_url,
                'photo_url' => $voter->photo_url
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');

        } catch (\Exception $e) {
            \Log::error('CheckMobile Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/verify-pin
     * Verify 4-digit PIN for returning user login
     */
    public function verifyPin(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'pin' => 'required|digits:4',
            ]);

            $mobile = $request->input('mobile');
            $pin = $request->input('pin');

            // Find voter by mobile
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found for this mobile number.',
                    'error_type' => 'no_account_found'
                ], 404);
            }

            if (!$voter->secret_pin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PIN found for this mobile. Please use OTP login.',
                    'error_type' => 'no_pin_found'
                ], 404);
            }

            // Verify PIN using Hash::check
            if (!Hash::check($pin, $voter->secret_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PIN. Please try again.',
                    'error_type' => 'invalid_pin'
                ], 400);
            }

            // Set session for authenticated user
            session(['verified_mobile' => $mobile]);

            $voterName = $this->cleanVoterName($voter->FM_NAME_EN, $voter->LASTNAME_EN);

            return response()->json([
                'success' => true,
                'has_card' => true,
                'epic_no' => $voter->EPIC_NO,
                'card_url' => $voter->card_url,
                'voter_name' => $voterName,
                'photo_url' => $voter->photo_url,
                'ptc_code' => $voter->ptc_code
            ]);

        } catch (\Exception $e) {
            \Log::error('VerifyPin Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/set-pin
     * Set 4-digit PIN for user (after card generation or anytime)
     */
    public function setPin(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'pin' => 'required|digits:4',
                'epic_no' => 'nullable|string|max:20',
            ]);

            $mobile = $request->input('mobile');
            $pin = $request->input('pin');
            $epicNo = $request->input('epic_no');

            // Find voter by mobile
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No card found for this mobile. Please generate your card first.',
                    'error_type' => 'no_card_found'
                ], 404);
            }

            // Hash and save PIN
            $hashedPin = Hash::make($pin);
            $voter->update(['secret_pin' => $hashedPin]);

            \Log::info("PIN set for mobile: {$mobile}");

            return response()->json([
                'success' => true,
                'message' => 'PIN set successfully. You can now use PIN for quick login.',
            ]);

        } catch (\Exception $e) {
            \Log::error('SetPin Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while setting PIN.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/forgot-pin
     * Send OTP for PIN reset (voice OTP like Python Flask)
     */
    public function forgotPin(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Verify this mobile has a card
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found for this mobile.',
                    'error_type' => 'no_account_found'
                ], 404);
            }

            // Rate limit: max 1 OTP per mobile per 60 seconds
            $existingOtp = OtpSession::where('mobile', $mobile)
                ->where('created_at', '>', now()->subSeconds(60))
                ->first();

            if ($existingOtp) {
                $elapsed = now()->diffInSeconds($existingOtp->created_at);
                $wait = 60 - $elapsed;
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$wait}s before requesting another OTP.",
                    'error_type' => 'rate_limit'
                ], 429);
            }

            // Generate OTP
            $otp = random_int(100000, 999999);

            // Send OTP via SMS
            $smsResult = $this->otpService->sendOtpVia2Factor($mobile, $otp);

            if (!$smsResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not send OTP. Please try again.',
                    'error_type' => 'sms_failed'
                ], 500);
            }

            // Store OTP with purpose 'pin_reset'
            OtpSession::updateOrCreate(
                ['mobile' => $mobile],
                [
                    'otp' => $otp,
                    'verified' => false,
                    'purpose' => 'pin_reset',
                    'attempts' => 0,
                    'created_at' => now(),
                ]
            );

            \Log::info("PIN reset OTP sent to mobile: {$mobile}");

            return response()->json([
                'success' => true,
                'message' => 'OTP sent for PIN reset. Please check your SMS.',
            ]);

        } catch (\Exception $e) {
            \Log::error('ForgotPin Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }
    /**
     * Verify OTP for forgot PIN flow (separate from reset-pin)
     */
    public function verifyForgotOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'otp' => 'required|digits:6',
            ]);

            $mobile = $request->input('mobile');
            $otp = $request->input('otp');

            // Find OTP session for pin_reset purpose
            $otpSession = OtpSession::where('mobile', $mobile)
                ->where('otp', $otp)
                ->where('purpose', 'pin_reset')
                ->where('verified', false)
                ->where('created_at', '>', now()->subMinutes(10)) // 10 min expiry
                ->first();

            if (!$otpSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.',
                    'error_type' => 'invalid_otp'
                ], 400);
            }

            // Mark OTP as verified but don't delete it yet (needed for reset-pin)
            $otpSession->update(['verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);

        } catch (\Exception $e) {
            \Log::error('VerifyForgotOtp Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/reset-pin
     * Reset PIN using OTP verification
     */
    public function resetPin(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
                'otp' => 'required|digits:6',
                'new_pin' => 'required|digits:4',
            ]);

            $mobile = $request->input('mobile');
            $otp = $request->input('otp');
            $newPin = $request->input('new_pin');

            // Verify OTP
            $otpSession = OtpSession::where('mobile', $mobile)
                ->where('purpose', 'pin_reset')
                ->where('verified', false)
                ->first();

            if (!$otpSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PIN reset OTP found. Please request a new OTP.',
                    'error_type' => 'otp_not_found'
                ], 400);
            }

            // Check OTP expiry (5 minutes)
            if ($otpSession->created_at < now()->subSeconds(300)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP expired. Please request a new one.',
                    'error_type' => 'otp_expired'
                ], 400);
            }

            // Verify OTP
            if ($otpSession->otp != $otp) {
                $otpSession->increment('attempts');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.',
                    'error_type' => 'invalid_otp'
                ], 400);
            }

            // Find voter and update PIN
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found.',
                    'error_type' => 'no_account_found'
                ], 404);
            }

            // Update PIN
            $hashedPin = Hash::make($newPin);
            $voter->update(['secret_pin' => $hashedPin]);

            // Mark OTP as verified
            $otpSession->update(['verified' => true]);

            \Log::info("PIN reset successful for mobile: {$mobile}");

            return response()->json([
                'success' => true,
                'message' => 'PIN reset successfully. You can now use your new PIN to login.',
            ]);

        } catch (\Exception $e) {
            \Log::error('ResetPin Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting PIN.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * GET /api/voter-lookup/{epic}
     * Find voter by EPIC number across all assembly tables
     */
    public function voterLookup($epicNo)
    {
        try {
            $epicNo = strtoupper(trim($epicNo));

            if (empty($epicNo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'EPIC number is required.',
                    'error_type' => 'invalid_input'
                ], 400);
            }

            // Use VoterHelper to find voter
            $voter = \App\Helpers\VoterHelper::findByEpicNo($epicNo);

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found. Please check your EPIC number.',
                    'error_type' => 'voter_not_found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'voter' => [
                    'epic_no' => $voter['epic_no'],
                    'name' => $voter['name'],
                    'assembly_name' => $voter['assembly_name'],
                    'district' => $voter['district'],
                    'age' => $voter['age'],
                    'sex' => $voter['sex'],
                    'relation_type' => $voter['relation_type'] ?? '',
                    'relation_name' => $voter['relation_name'] ?? '',
                    'house_no' => $voter['house_no'] ?? '',
                    'part_no' => $voter['part_no'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('VoterLookup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while looking up voter.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    // ========================================
    // ADDITIONAL ENDPOINTS (chat JS parity)
    // ========================================

    /**
     * POST /api/chat/validate-epic
     * POST alias for voter lookup (used by chat JS)
     */
    public function validateEpic(Request $request)
    {
        try {
            $request->validate([
                'epic_no' => 'required|string|max:20',
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $voter = \App\Helpers\VoterHelper::findByEpicNo($epicNo);

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found. Please check your EPIC number.',
                    'error_type' => 'voter_not_found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'voter' => [
                    'epic_no'       => $voter['epic_no'],
                    'name'          => $voter['name'],
                    'assembly_name' => $voter['assembly_name'],
                    'district'      => $voter['district'],
                    'age'           => $voter['age'],
                    'sex'           => $voter['sex'],
                    'relation_type' => $voter['relation_type'] ?? '',
                    'relation_name' => $voter['relation_name'] ?? '',
                    'house_no'      => $voter['house_no'] ?? '',
                    'part_no'       => $voter['part_no'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('ValidateEpic Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/profile
     * Return voter's card + profile data for the sidebar "My ID Card" view
     */
    public function getProfile(Request $request)
    {
        try {
            $request->validate(['mobile' => 'required|digits:10']);
            $mobile = $request->input('mobile');

            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No profile found for this mobile.',
                    'error_type' => 'not_found'
                ], 404);
            }

            $name = $this->cleanVoterName($voter->FM_NAME_EN, $voter->LASTNAME_EN);

            return response()->json([
                'success'       => true,
                'name'          => $name,
                'epic_no'       => $voter->EPIC_NO,
                'assembly'      => $voter->ASSEMBLY_NAME,
                'district'      => $voter->DISTRICT_NAME,
                'ptc_code'      => $voter->ptc_code,
                'card_url'      => $voter->card_url,
                'photo_url'     => $voter->photo_url,
                'referred_members_count' => $voter->referred_members_count ?? 0,
                'generated_at'  => $voter->generated_at,
            ]);

        } catch (\Exception $e) {
            \Log::error('GetProfile Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * POST /api/chat/booth
     * Return voter's booth / part info for the "Booth Info" sidebar view
     */
    public function getBoothInfo(Request $request)
    {
        try {
            $request->validate(['mobile' => 'required|digits:10']);
            $mobile = $request->input('mobile');

            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'No voter found for this mobile.',
                    'error_type' => 'not_found'
                ], 404);
            }

            return response()->json([
                'success'       => true,
                'epic_no'       => $voter->EPIC_NO,
                'assembly_name' => $voter->ASSEMBLY_NAME,
                'assembly_no'   => $voter->AC_NO,
                'part_no'       => $voter->PART_NO,
                'section_no'    => $voter->SECTION_NO,
                'house_no'      => $voter->C_HOUSE_NO,
                'district'      => $voter->DISTRICT_NAME,
            ]);

        } catch (\Exception $e) {
            \Log::error('GetBoothInfo Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * GET /api/whatsapp-channel
     * Return WhatsApp channel link for the sidebar
     */
    public function whatsappChannel()
    {
        return response()->json([
            'success' => true,
            'channel_url' => config('app.whatsapp_channel', 'https://whatsapp.com/channel/0029VazKqBh1bVLFsFwO8G1H'),
        ]);
    }
}
