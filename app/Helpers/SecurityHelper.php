<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Hash PIN for storage
     */
    public static function hashPin($pin)
    {
        return bcrypt($pin);
    }

    /**
     * Verify PIN against hash
     */
    public static function verifyPin($pin, $hash)
    {
        return password_verify($pin, $hash);
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Sanitize EPIC number
     */
    public static function sanitizeEpicNo($epicNo)
    {
        return strtoupper(trim($epicNo));
    }

    /**
     * Validate mobile number format
     */
    public static function isValidMobile($mobile)
    {
        return preg_match('/^[6-9]\d{9}$/', $mobile) === 1;
    }

    /**
     * Generate PTC (Public To Candidate) code
     */
    public static function generatePtcCode()
    {
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return "PTC-{$timestamp}-{$random}";
    }
}
