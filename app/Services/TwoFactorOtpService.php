<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TwoFactorOtpService
{
    protected $apiKey;
    protected $cache;

    public function __construct(CacheService $cache)
    {
        $this->apiKey = config('services.twofactor.api_key');
        $this->cache = $cache;
    }

    /**
     * Send OTP via 2Factor.in Voice Call API
     */
    public function sendOtp($mobile)
    {
        try {
            $url = "https://2factor.in/API/V1/{$this->apiKey}/SMS/{$mobile}/AUTOGEN/VOICE";

            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['Status'] ?? '') === 'Success') {
                    $sessionId = $data['Details'] ?? '';
                    // Store session ID in cache for verification (valid 10 minutes)
                    $this->cache->put('otp_session:' . $mobile, $sessionId, 600);
                    Log::info("2Factor Voice OTP sent to {$mobile}, Session: {$sessionId}");
                    return ['success' => true, 'message' => 'OTP sent via voice call'];
                }
            }

            $error = $response->json();
            Log::error("2Factor send OTP failed: " . json_encode($error));
            return ['success' => false, 'error' => $error['Details'] ?? 'Failed to send OTP'];

        } catch (Exception $e) {
            Log::error('TwoFactorOtpService::sendOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify OTP via 2Factor.in API
     */
    public function verifyOtp($mobile, $code)
    {
        try {
            $sessionId = $this->cache->get('otp_session:' . $mobile);
            if (!$sessionId) {
                return ['success' => false, 'error' => 'OTP session expired. Please request a new OTP.'];
            }

            $url = "https://2factor.in/API/V1/{$this->apiKey}/SMS/VERIFY/{$sessionId}/{$code}";

            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['Status'] ?? '') === 'Success' && ($data['Details'] ?? '') === 'OTP Matched') {
                    $this->cache->forget('otp_session:' . $mobile);
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
            Log::error('TwoFactorOtpService::verifyOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
