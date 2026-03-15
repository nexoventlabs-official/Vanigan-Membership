<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tamil Nadu Vanigargalin Sangamam - WhatsApp-style chat UI for member registration
|
*/

// ── Main Page (Chat UI) ─────────────────────────────────────────────────
Route::get('/', function () {
    return view('chatbot');
})->name('home');

// ── Member Card View ────────────────────────────────────────────────────
Route::get('/member/card/{uniqueId}', [VanigamController::class, 'showCard'])->name('member.card');

// ── QR: Complete Additional Details ─────────────────────────────────────
Route::get('/member/complete/{uniqueId}', [VanigamController::class, 'completeDetails'])->name('member.complete');

// ── QR: Public Verification ─────────────────────────────────────────────
Route::get('/member/verify/{uniqueId}', [VanigamController::class, 'verifyMember'])->name('member.verify');

// ── Referral Landing Page ──────────────────────────────────────────────
Route::get('/refer/{uniqueId}/{referralId}', [VanigamController::class, 'handleReferral'])->name('referral');

// ── Client-side Card View (reads from localStorage, no MongoDB needed) ──
Route::get('/card-view', function () {
    return view('card.view');
})->name('card.view');

// ── Test Card with Dummy Data ───────────────────────────────────────────
Route::get('/test/card', function () {
    $member = (object) [
        'unique_id'      => 'VNG-A3B7C1D',
        'epic_no'        => 'AYR1454636',
        'name'           => 'SENTHIL KUMAR',
        'membership'     => 'Member',
        'assembly'       => 'Gummidipoondi',
        'district'       => 'Tiruvallur',
        'contact_number' => '+91 8106811285',
        'mobile'         => '8106811285',
        'dob'            => '15/06/1990',
        'age'            => '35',
        'blood_group'    => 'O+',
        'address'        => '12, Main Road, Gummidipoondi, Tiruvallur - 601201',
        'photo_url'      => 'https://res.cloudinary.com/dqndhcmu2/image/upload/v1773487575/vanigan/test/test_1773487573.png',
        'qr_url'         => 'http://localhost:8000/member/verify/VNG-A3B7C1D',
        'details_completed' => true,
    ];
    return view('card.vanigam', [
        'member'       => $member,
        'generated_at' => now()->format('d M Y, h:i A'),
        'base_url'     => config('app.url'),
    ]);
});