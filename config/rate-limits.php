<?php

/**
 * Rate Limiting Configuration (Documentation Reference)
 *
 * Defines rate limit groups for API endpoints.
 * Format: 'requests_per_period,period_in_minutes'
 *
 * These values are hardcoded in routes/api.php for reliability.
 * This file serves as a central reference for all throttle limits.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limit Groups
    |--------------------------------------------------------------------------
    | Each group defines requests allowed per time period for related endpoints
    | Values are hardcoded in routes/api.php for direct reliability
    */

    // OTP endpoints (send/verify) - DDoS + brute force protection
    // 50 requests per 5 minutes per IP
    'otp' => '50,5',

    // PIN verification - Brute force protection for 4-digit codes
    // 10 requests per 5 minutes per IP
    'pin' => '10,5',

    // Card generation - Resource intensive (generates QR + images)
    // 15 requests per 5 minutes per IP
    'card_generation' => '15,5',

    // Validation endpoints - Resource intensive (uploads, MySQL lookups)
    // 40 requests per 5 minutes per IP
    'validation' => '40,5',

    // Member read operations - Lightweight, read-only, high frequency
    // 200 requests per 1 minute per IP
    'member_read' => '200,1',

    // Referral & Loan operations - Standard user operations
    // 30 requests per 5 minutes per IP
    'referral_loan' => '30,5',

    // Admin protected endpoints - Already behind API key; secondary protection
    // 10 requests per 5 minutes per IP
    'admin_protected' => '10,5',
];
