<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAdminApiKey
{
    /**
     * Handle an incoming request.
     *
     * Validates that the X-Admin-Key header matches the configured admin API key.
     * This middleware is applied to sensitive endpoints like reset-members and upload-card-images.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-Admin-Key');
        $expectedKey = config('vanigam.admin_api_key');

        // Check if key is provided
        if (empty($providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing X-Admin-Key header.',
            ], 401);
        }

        // Check if key matches
        if ($providedKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid X-Admin-Key.',
            ], 401);
        }

        return $next($request);
    }
}
