<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| API Routes - Tamil Nadu Vanigargalin Sangamam
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', [VanigamController::class, 'health']);

// Tamil Nadu Vanigargalin Sangamam API
Route::prefix('vanigam')->group(function () {
    // OTP via Twilio
    Route::post('/check-member', [VanigamController::class, 'checkMember']);
    Route::post('/send-otp', [VanigamController::class, 'sendOtp']);
    Route::post('/verify-otp', [VanigamController::class, 'verifyOtp']);

    // EPIC Voter Lookup (MySQL read-only)
    Route::post('/validate-epic', [VanigamController::class, 'validateEpic']);

    // Photo upload to Cloudinary
    Route::post('/upload-photo', [VanigamController::class, 'uploadPhoto']);

    // Generate membership card & save to MongoDB
    Route::post('/generate-card', [VanigamController::class, 'generateCard']);

    // Save additional details (from chat or QR form)
    Route::post('/save-details', [VanigamController::class, 'saveAdditionalDetails']);

    // Get member info
    Route::get('/member/{uniqueId}', [VanigamController::class, 'getMember']);

    // QR code image (generated on-the-fly)
    Route::get('/qr/{uniqueId}', [VanigamController::class, 'generateQr']);

    // Reset MongoDB members (does NOT touch MySQL)
    Route::post('/reset-members', [VanigamController::class, 'resetMembers']);

    // Upload card images to Cloudinary
    Route::post('/upload-card-images', [VanigamController::class, 'uploadCardImages']);

    // Verify returning user PIN
    Route::post('/verify-pin', [VanigamController::class, 'verifyPin']);

    // Referral
    Route::post('/get-referral', [VanigamController::class, 'getReferral']);
    Route::post('/increment-referral', [VanigamController::class, 'incrementReferral']);
});
