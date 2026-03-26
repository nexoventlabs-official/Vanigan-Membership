<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TwoFactorOtpService
{
    protected $twoFactorKey;
    protected $fast2smsKey;
    protected $cache;

    public function __construct(CacheService $cache)
    {
        $this->twoFactorKey = config('services.twofactor.api_key');
        $this->fast2smsKey  = config('services.fast2sms.api_key');
        $this->cache = $cache;
    }

    /**
     * Send OTP via 2Factor.in SMS API (primary), fallback to Fast2SMS Quick SMS
     */
    public function sendOtp($mobile)
    {
        // Try 2Factor SMS first
        $result = $this->sendVia2Factor($mobile);
        if ($result['success']) return $result;

        // Fallback to Fast2SMS Quick SMS
        Log::warning("2Factor failed for {$mobile}, trying Fast2SMS fallback");
        return $this->sendViaFast2SMS($mobile);
    }

    private function sendVia2Factor($mobile)
    {
        try {
            $url = "https://2factor.in/API/V1/{$this->twoFactorKey}/SMS/{$mobile}/AUTOGEN2";

            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['Status'] ?? '') === 'Success') {
                    $sessionId = $data['Details'] ?? '';
                    // Store session ID in cache for verification (valid 10 minutes)
                    $this->cache->put('otp_session:' . $mobile, $sessionId, 600);
                    $this->cache->put('otp_provider:' . $mobile, '2factor', 600);
                    Log::info("2Factor SMS OTP sent to {$mobile}, Session: {$sessionId}");
                    return ['success' => true, 'message' => 'OTP sent via SMS'];
                }
            }

            $error = $response->json();
            Log::error("2Factor send OTP failed: " . json_encode($error));
            return ['success' => false, 'error' => $error['Details'] ?? 'Failed to send OTP'];

        } catch (Exception $e) {
            Log::error('2Factor sendOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sendViaFast2SMS($mobile)
    {
        try {
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            $response = Http::timeout(15)
                ->withHeaders([
                    'authorization' => $this->fast2smsKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://www.fast2sms.com/dev/bulkV2', [
                    'message'  => "Your OTP for Tamil Nadu Vanigargalin Sangamam is {$otp}. Do not share this with anyone.",
                    'route'    => 'q',
                    'numbers'  => $mobile,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['return'] ?? false) === true) {
                $this->cache->put('otp_code:' . $mobile, $otp, 600);
                $this->cache->put('otp_provider:' . $mobile, 'fast2sms', 600);
                Log::info("Fast2SMS OTP sent to {$mobile}, request_id: " . ($data['request_id'] ?? 'N/A'));
                return ['success' => true, 'message' => 'OTP sent via SMS'];
            }

            Log::error("Fast2SMS send OTP failed: " . json_encode($data));
            return ['success' => false, 'error' => $data['message'] ?? 'Failed to send OTP'];

        } catch (Exception $e) {
            Log::error('Fast2SMS sendOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify OTP — routes to correct provider based on which one sent the OTP
     */
    public function verifyOtp($mobile, $code)
    {
        $provider = $this->cache->get('otp_provider:' . $mobile) ?? '2factor';

        if ($provider === '2factor') {
            return $this->verifyVia2Factor($mobile, $code);
        } else {
            return $this->verifyViaLocal($mobile, $code);
        }
    }

    private function verifyVia2Factor($mobile, $code)
    {
        try {
            $sessionId = $this->cache->get('otp_session:' . $mobile);
            if (!$sessionId) {
                return ['success' => false, 'error' => 'OTP session expired. Please request a new OTP.'];
            }

            $url = "https://2factor.in/API/V1/{$this->twoFactorKey}/SMS/VERIFY/{$sessionId}/{$code}";

            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['Status'] ?? '') === 'Success' && ($data['Details'] ?? '') === 'OTP Matched') {
                    $this->cache->forget('otp_session:' . $mobile);
                    $this->cache->forget('otp_provider:' . $mobile);
                    Log::info("2Factor OTP verified for {$mobile}");
                    return ['success' => true, 'message' => 'OTP verified successfully'];
                } else {
                    Log::warning("2Factor OTP verification failed: " . json_encode($data));
                    return ['success' => false, 'error' => 'Invalid OTP. Please try again.'];
                }
            }

            $error = $response->json();
            Log::error("2Factor verify OTP failed: " . json_encode($error));
            return ['success' => false, 'error' => $error['Details'] ?? 'Verification failed'];

        } catch (Exception $e) {
            Log::error('2Factor verifyOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function verifyViaLocal($mobile, $code)
    {
        try {
            $storedOtp = $this->cache->get('otp_code:' . $mobile);
            if (!$storedOtp) {
                return ['success' => false, 'error' => 'OTP expired. Please request a new OTP.'];
            }

            if ((string) $storedOtp === (string) $code) {
                $this->cache->forget('otp_code:' . $mobile);
                $this->cache->forget('otp_provider:' . $mobile);
                Log::info("Fast2SMS OTP verified for {$mobile}");
                return ['success' => true, 'message' => 'OTP verified successfully'];
            } else {
                Log::warning("Fast2SMS OTP mismatch for {$mobile}");
                return ['success' => false, 'error' => 'Invalid OTP. Please try again.'];
            }

        } catch (Exception $e) {
            Log::error('Fast2SMS verifyOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
