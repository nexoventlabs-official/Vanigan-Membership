<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VanigamController;
use App\Http\Controllers\AdminPanelController;

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

// ── Admin Panel ────────────────────────────────────────────────────────
Route::get('/admin/login', [AdminPanelController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminPanelController::class, 'login'])->name('admin.login.submit');

Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [AdminPanelController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminPanelController::class, 'users'])->name('admin.users');
    Route::get('/users/{uniqueId}', [AdminPanelController::class, 'userDetail'])->name('admin.user.detail');
    Route::post('/users/{uniqueId}/update', [AdminPanelController::class, 'updateMember'])->name('admin.user.update');
    Route::post('/users/{uniqueId}/delete', [AdminPanelController::class, 'deleteMember'])->name('admin.user.delete');
    Route::post('/users/{uniqueId}/regenerate-card', [AdminPanelController::class, 'regenerateCard'])->name('admin.user.regenerate_card');
    Route::get('/voters', [AdminPanelController::class, 'voters'])->name('admin.voters');
    Route::get('/voters/{epicNo}', [AdminPanelController::class, 'voterDetail'])->name('admin.voter.detail');
    Route::get('/reports', [AdminPanelController::class, 'reports'])->name('admin.reports');
    Route::get('/loan-requests', [AdminPanelController::class, 'loanRequests'])->name('admin.loan_requests');
    Route::get('/not-registered', [AdminPanelController::class, 'notRegistered'])->name('admin.not_registered');
    Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout');
});