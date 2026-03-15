<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TwilioOtpService
{
    protected $accountSid;
    protected $authToken;
    protected $verifyServiceSid;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid');
        $this->authToken = config('services.twilio.auth_token');
        $this->verifyServiceSid = config('services.twilio.verify_service_sid');
    }

    /**
     * Send OTP via Twilio Verify API
     */
    public function sendOtp($mobile)
    {
        try {
            $phone = '+91' . $mobile;

            $url = "https://verify.twilio.com/v2/Services/{$this->verifyServiceSid}/Verifications";

            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->asForm()
                ->timeout(15)
                ->post($url, [
                    'To' => $phone,
                    'Channel' => 'sms',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Twilio OTP sent to {$mobile}, SID: " . ($data['sid'] ?? 'N/A'));
                return ['success' => true, 'message' => 'OTP sent successfully'];
            } else {
                $error = $response->json();
                Log::error("Twilio send OTP failed: " . json_encode($error));
                return ['success' => false, 'error' => $error['message'] ?? 'Failed to send OTP'];
            }
        } catch (Exception $e) {
            Log::error('TwilioOtpService::sendOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify OTP via Twilio Verify API
     */
    public function verifyOtp($mobile, $code)
    {
        try {
            $phone = '+91' . $mobile;

            $url = "https://verify.twilio.com/v2/Services/{$this->verifyServiceSid}/VerificationCheck";

            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->asForm()
                ->timeout(15)
                ->post($url, [
                    'To' => $phone,
                    'Code' => $code,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'approved') {
                    Log::info("Twilio OTP verified for {$mobile}");
                    return ['success' => true, 'message' => 'OTP verified successfully'];
                } else {
                    Log::warning("Twilio OTP verification status: " . ($data['status'] ?? 'unknown'));
                    return ['success' => false, 'error' => 'Invalid OTP. Please try again.'];
                }
            } else {
                $error = $response->json();
                Log::error("Twilio verify OTP failed: " . json_encode($error));
                return ['success' => false, 'error' => $error['message'] ?? 'Verification failed'];
            }
        } catch (Exception $e) {
            Log::error('TwilioOtpService::verifyOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
