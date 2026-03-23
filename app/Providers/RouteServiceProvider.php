<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinutes(5, 50)->by($request->ip());
        });

        RateLimiter::for('validation', function (Request $request) {
            return Limit::perMinutes(5, 40)->by($request->ip());
        });

        RateLimiter::for('card_generation', function (Request $request) {
            return Limit::perMinutes(5, 15)->by($request->ip());
        });

        RateLimiter::for('member_read', function (Request $request) {
            return Limit::perMinute(200)->by($request->ip());
        });

        RateLimiter::for('pin_login', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('pin_scan', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('admin_reset', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('admin_upload', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        RateLimiter::for('referral_loan', function (Request $request) {
            return Limit::perMinutes(5, 30)->by($request->ip());
        });
    }
}
