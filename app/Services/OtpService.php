<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send OTP via 2Factor.in API
     */
    public function sendOtpVia2Factor($mobile, $otp)
    {
        try {
            // LOCAL TESTING MODE - Show OTP in logs and return success
            if (config('app.env') === 'local') {
                Log::info("🧪 LOCAL TESTING MODE - OTP for {$mobile}: {$otp}");

                // Also store in cache for easy retrieval in testing
                app(\App\Services\CacheService::class)->put("test_otp_{$mobile}", $otp, 300); // 5 minutes
                
                return [
                    'success' => true, 
                    'message' => "LOCAL MODE: OTP is {$otp} (check logs)",
                    'test_otp' => $otp
                ];
            }

            $apiKey = config('services.sms.api_key');
            $apiUrl = config('services.sms.api_url');

            if (!$apiKey || !$apiUrl) {
                Log::error('SMS API credentials not configured');
                return ['success' => false, 'error' => 'SMS not configured'];
            }

            // 2Factor.in API: https://2factor.in/API/V1/sendSMS/{API_KEY}/{phone}/{message}
            $message = "Your Voter ID Card OTP is: {$otp}. Valid for 5 minutes. Do not share.";
            
            $url = "{$apiUrl}/{$apiKey}/{$mobile}/{$message}";

            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                Log::info("OTP sent successfully to {$mobile}");
                return ['success' => true];
            } else {
                Log::error("SMS API failed: " . $response->body());
                return ['success' => false, 'error' => 'SMS delivery failed'];
            }

        } catch (\Exception $e) {
            Log::error('SendOtpVia2Factor Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp($mobile, $otp)
    {
        // Logic handled in UserController
        // This is a placeholder for additional verification logic if needed
        return true;
    }
}
