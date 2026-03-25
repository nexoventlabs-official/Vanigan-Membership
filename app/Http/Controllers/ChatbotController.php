<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OtpService;
use App\Helpers\VoterHelper;
use App\Models\OtpSession;
use App\Models\GeneratedVoter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\VanigamController;

class ChatbotController extends Controller
{
    /**
     * Display welcome page (legacy)
     */
    public function welcome()
    {
        return view('chatbot.welcome');
    }

    /**
     * Display WhatsApp-style chat UI (single-page app)
     */
    public function chatbot()
    {
        return view('chatbot');
    }

    /**
     * Display OTP verification page
     */
    public function verify(Request $request)
    {
        $mobile = session('verified_mobile') ?? $request->query('mobile');
        return view('chatbot.verify', compact('mobile'));
    }

    /**
     * Display EPIC lookup page
     */
    public function epic(Request $request)
    {
        return view('chatbot.epic');
    }

    /**
     * Display PIN setup and photo upload page
     */
    public function pinPhoto(Request $request)
    {
        return view('chatbot.pin-photo');
    }

    /**
     * Send OTP to mobile number
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10'
            ]);

            $mobile = $request->input('mobile');

            // Use existing OTP service
            $otpService = new OtpService();
            $result = $otpService->sendOtpVia2Factor($mobile);

            if ($result['success']) {
                session(['verified_mobile' => $mobile]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'mobile' => $mobile
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send OTP'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid mobile number format'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'otp' => 'required|digits:6'
            ]);

            $mobile = $request->input('mobile');
            $otp = $request->input('otp');

            // Verify OTP using existing OtpSession model
            $verified = OtpSession::where('mobile', $mobile)
                ->where('otp', $otp)
                ->where('expires_at', '>', now())
                ->where('verified_at', null)
                ->first();

            if (!$verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 401);
            }

            // Mark as verified
            session(['verified_mobile' => $mobile]);
            $verified->update(['verified_at' => now()]);

            // Check if user has existing card
            $existingCard = GeneratedVoter::where('mobile', $mobile)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'mobile' => $mobile,
                'has_card' => $existingCard ? true : false,
                'card_url' => $existingCard?->card_url
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input format'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Voter lookup by EPIC number
     */
    public function voterLookup($epicNo)
    {
        try {
            // Validate EPIC format
            if (empty($epicNo) || strlen($epicNo) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid EPIC number format'
                ], 400);
            }

            $voter = VoterHelper::findByEpicNo($epicNo);

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found. Please check your EPIC number.'
                ], 404);
            }

            $assemblyName = $voter['assembly_name'] ?? $voter['assembly'] ?? 'N/A';
            $district = $voter['district'] ?? 'N/A';

            // Look up zone and CORRECT district from static config (Excel-based, not MySQL)
            // MySQL has correct assembly names but some districts are wrong
            $zoneData = config('zone_data');
            $zone = '';
            if ($zoneData && !empty($zoneData['assembly_map'])) {
                $asmMap = $zoneData['assembly_map'];

                // Normalize: uppercase, collapse whitespace
                $assemblyUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $assemblyName)));

                // Direct match
                $matched = $asmMap[$assemblyUpper] ?? null;

                // If no direct match, try without parentheses/brackets differences
                if (!$matched) {
                    // Normalize further: remove dots, hyphens for comparison
                    $normalizedInput = preg_replace('/[\.\-\(\)]/', '', $assemblyUpper);
                    $normalizedInput = preg_replace('/\s+/', ' ', trim($normalizedInput));
                    foreach ($asmMap as $key => $val) {
                        $normalizedKey = preg_replace('/[\.\-\(\)]/', '', $key);
                        $normalizedKey = preg_replace('/\s+/', ' ', trim($normalizedKey));
                        if ($normalizedKey === $normalizedInput) {
                            $matched = $val;
                            break;
                        }
                    }
                }

                if ($matched) {
                    // Override district with correct one from Excel sheet
                    $district = $matched['d'];
                    $zone = $matched['z'];
                } elseif (!empty($zoneData['district_zone'])) {
                    // Fallback: district-based zone lookup only
                    $districtUpper = strtoupper(trim($district));
                    if (!empty($zoneData['district_zone'][$districtUpper])) {
                        $zone = $zoneData['district_zone'][$districtUpper];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'voter' => [
                    'name' => $voter['name'] ?? 'N/A',
                    'epic_no' => $epicNo,
                    'assembly_name' => $assemblyName,
                    'district' => $district,
                    'zone' => $zone,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error looking up voter: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Setup user PIN
     */
    public function setupPin(Request $request)
    {
        try {
            $mobile = session('verified_mobile');
            if (!$mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mobile number not verified'
                ], 401);
            }

            $request->validate([
                'pin' => 'required|digits:4',
                'pin_confirm' => 'required|digits:4|same:pin'
            ]);

            $pin = $request->input('pin');

            if (VanigamController::isWeakPin($pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN is too easy to guess. Avoid 1234, 0000, 1111, etc.'
                ], 400);
            }

            // Store PIN hash in user_pins table
            DB::table('user_pins')->updateOrCreate(
                ['mobile' => $mobile],
                [
                    'pin_hash' => Hash::make($pin),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            session(['pin_set' => true]);

            return response()->json([
                'success' => true,
                'message' => 'PIN set successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = 'PIN validation failed';
            
            if ($errors->has('pin_confirm')) {
                $message = 'PINs do not match';
            } elseif ($errors->has('pin')) {
                $message = 'PIN must be 4 digits';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting PIN: ' . $e->getMessage()
            ], 500);
        }
    }
}