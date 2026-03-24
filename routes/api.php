<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| API Routes - Tamil Nadu Vanigargalin Sangamam
|--------------------------------------------------------------------------
*/

// Health check (no throttle - monitoring/internal use)
Route::get('/health', [VanigamController::class, 'health']);

// Tamil Nadu Vanigargalin Sangamam API
Route::prefix('vanigam')->group(function () {
    // === OTP Endpoints - DDoS + Brute Force Protection ===
    // Rate limit: 50 per 5 minutes
    Route::post('/check-member', [VanigamController::class, 'checkMember'])
        ->middleware('throttle:otp');

    Route::post('/send-otp', [VanigamController::class, 'sendOtp'])
        ->middleware('throttle:otp');

    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp'])
        ->middleware('throttle:otp');

    // === Validation Endpoints - Uploads & Lookups ===
    // Rate limit: 40 per 5 minutes
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic'])
        ->middleware('throttle:validation');

    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto'])
        ->middleware('throttle:validation');

    Route::post('/validate-photo', [VanigamController::class, 'validatePhotoUpload'])
        ->middleware('throttle:validation');

    // === Card Generation - Resource Intensive ===
    // Rate limit: 15 per 5 minutes
    Route::post('/generate-card', [VanigamController::class, 'generateCard'])
        ->middleware('throttle:card_generation');

    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails'])
        ->middleware('throttle:card_generation');

    // === Member Read Operations - Lightweight ===
    // Rate limit: 200 per 1 minute
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember'])
        ->middleware('throttle:member_read');

    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr'])
        ->middleware('throttle:member_read');

    // === Admin Protected Endpoints - API Key + Secondary Rate Limit ===
    // Rate limit: 10 per 5 minutes
    Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:admin_reset',
        ]);

    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages'])
        ->middleware([
            'validate.admin.api.key',
            'throttle:card_generation',
        ]);

    // === PIN Verification - Brute Force Protection ===
    // Rate limit: 10 per 5 minutes (login workflow)
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin'])
        ->middleware('throttle:pin_login');

    // Rate limit: 10 per 5 minutes (QR scan workflow)
    Route::post('/verify-member-pin', [VanigamController::class, 'verifyMemberPin'])
        ->middleware('throttle:pin_scan');

    // === Referral & Loan - Standard User Operations ===
    // Rate limit: 30 per 5 minutes
    Route::post('/get-referral', [VanigamController::class, 'getReferral'])
        ->middleware('throttle:referral_loan');

    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral'])
        ->middleware('throttle:referral_loan');

    Route::post('/get-referred-members', [VanigamController::class, 'getReferredMembers'])
        ->middleware('throttle:referral_loan');

    Route::post('/loan-request', [VanigamController::class, 'loanRequest'])
        ->middleware('throttle:referral_loan');

    Route::post('/check-loan-status', [VanigamController::class, 'checkLoanStatus'])
        ->middleware('throttle:referral_loan');

    // === Registration Tracking - Lightweight ===
    Route::post('/track-step', [VanigamController::class, 'trackRegistrationStep'])
        ->middleware('throttle:referral_loan');
});
