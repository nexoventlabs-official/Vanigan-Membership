<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * GET /api/health
     * Health check endpoint
     */
    public function health()
    {
        try {
            $health = [
                'success' => true,
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

            // Check Cache (including Redis connectivity)
            try {
                $cacheDriver = config('cache.default');

                if ($cacheDriver === 'redis') {
                    // Test Redis PING explicitly
                    $redisTest = $this->cache->testRedisPing();

                    if ($redisTest['status'] === 'ok') {
                        $health['redis'] = 'ok';
                        $health['cache'] = 'ok (redis)';
                    } elseif ($redisTest['status'] === 'unavailable') {
                        $health['redis'] = 'unavailable';
                        $health['cache'] = 'error (redis)';
                        $health['redis_error'] = $redisTest['message'];
                    } else {
                        $health['redis'] = $redisTest['status'];
                        $health['cache'] = $redisTest['status'] . ' (redis)';
                        if (isset($redisTest['message'])) {
                            $health['redis_message'] = $redisTest['message'];
                        }
                    }
                } else {
                    // File or other cache driver
                    try {
                        $this->cache->get('health_check_test');
                        $health['cache'] = 'ok (' . $cacheDriver . ')';
                    } catch (\Exception $cacheException) {
                        $health['cache'] = 'error (' . $cacheDriver . ')';
                        $health['cache_error'] = $cacheException->getMessage();
                    }
                }
            } catch (\Exception $e) {
                $health['cache'] = 'error';
                $health['cache_error'] = $e->getMessage();
                Log::warning('Health check cache test failed', ['exception' => $e->getMessage()]);
            }

            return response()->json($health);

        } catch (\Exception $e) {
            Log::error('Health check failed', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'HEALTH_CHECK_FAILED',
            ], 500);
        }
    }
}

