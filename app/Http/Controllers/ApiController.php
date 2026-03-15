<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    /**
     * GET /api/health
     * Health check endpoint
     */
    public function health()
    {
        try {
            $health = [
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),
                'uptime' => floor(microtime(true)),
            ];

            // Check MySQL connections
            try {
                DB::connection('mysql')->getPdo();
                $health['mysql'] = 'ok';
            } catch (\Exception $e) {
                $health['mysql'] = 'error';
                $health['mysql_error'] = $e->getMessage();
            }

            try {
                DB::connection('voters')->getPdo();
                $health['voters_db'] = 'ok';
            } catch (\Exception $e) {
                $health['voters_db'] = 'error';
                $health['voters_db_error'] = $e->getMessage();
            }

            // Check Cache (skip Redis for local testing)
            try {
                $cacheDriver = config('cache.default');
                if ($cacheDriver === 'file' || $cacheDriver === 'array') {
                    Cache::get('health_check');
                    $health['cache'] = 'ok (' . $cacheDriver . ')';
                } else {
                    // Skip Redis check for now
                    $health['cache'] = 'skipped (using ' . $cacheDriver . ')';
                }
            } catch (\Exception $e) {
                $health['cache'] = 'error';
                $health['cache_error'] = $e->getMessage();
            }

            return response()->json($health);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
