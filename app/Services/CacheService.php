<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * CacheService - Resilient Cache Wrapper
 *
 * Attempts to use Redis cache store, and gracefully falls back to file-based cache
 * if Redis becomes unavailable. Critical for OTP rate limiting and other time-sensitive
 * cache operations that must not fail completely.
 *
 * This service ensures the application continues functioning even if Redis is down,
 * by using Laravel's file cache as a automatic fallback mechanism.
 */
class CacheService
{
    // Store names from config/cache.php
    private const REDIS_STORE = 'redis';
    private const FILE_STORE = 'file';
    private const WARNING_PREFIX = '[CACHE_FALLBACK]';

    /**
     * Get an item from cache, with automatic Redis-to-file fallback
     *
     * @param string $key The cache key
     * @param mixed $default Default value if key not found
     * @return mixed Cached value or default
     */
    public function get(string $key, $default = null)
    {
        try {
            return Cache::store(self::REDIS_STORE)->get($key, $default);
        } catch (Exception $e) {
            $this->logFallback('get', $key, $e);
            return Cache::store(self::FILE_STORE)->get($key, $default);
        }
    }

    /**
     * Store an item in cache, with automatic Redis-to-file fallback
     *
     * @param string $key The cache key
     * @param mixed $value The value to cache
     * @param int|null $seconds TTL in seconds (null = forever)
     * @return void
     */
    public function put(string $key, $value, $seconds = null): void
    {
        try {
            Cache::store(self::REDIS_STORE)->put($key, $value, $seconds);
        } catch (Exception $e) {
            $this->logFallback('put', $key, $e);
            Cache::store(self::FILE_STORE)->put($key, $value, $seconds);
        }
    }

    /**
     * Delete an item from cache, with automatic Redis-to-file fallback
     *
     * @param string $key The cache key to delete
     * @return void
     */
    public function forget(string $key): void
    {
        try {
            Cache::store(self::REDIS_STORE)->forget($key);
        } catch (Exception $e) {
            $this->logFallback('forget', $key, $e);
            Cache::store(self::FILE_STORE)->forget($key);
        }
    }

    /**
     * Get an item from cache or store the result of a callback, with fallback
     *
     * Perfect for expensive operations like database queries or API calls.
     * If cache hit, returns cached value. On miss, executes callback, caches result, returns value.
     *
     * @param string $key The cache key
     * @param int|null $seconds TTL in seconds (null = forever)
     * @param callable $callback Function to execute on cache miss
     * @return mixed Cached value or callback result
     */
    public function remember(string $key, $seconds, callable $callback)
    {
        try {
            return Cache::store(self::REDIS_STORE)->remember($key, $seconds, $callback);
        } catch (Exception $e) {
            $this->logFallback('remember', $key, $e);
            return Cache::store(self::FILE_STORE)->remember($key, $seconds, $callback);
        }
    }

    /**
     * Check if a key exists in cache, with automatic Redis-to-file fallback
     *
     * @param string $key The cache key
     * @return bool True if key exists, false otherwise
     */
    public function has(string $key): bool
    {
        try {
            return Cache::store(self::REDIS_STORE)->has($key);
        } catch (Exception $e) {
            $this->logFallback('has', $key, $e);
            return Cache::store(self::FILE_STORE)->has($key);
        }
    }

    /**
     * Get multiple items from cache, with automatic fallback
     *
     * @param array $keys Array of cache keys
     * @return array Associative array of key => value pairs
     */
    public function getMany(array $keys): array
    {
        try {
            return Cache::store(self::REDIS_STORE)->getMany($keys);
        } catch (Exception $e) {
            $this->logFallback('getMany', implode(', ', $keys), $e);
            return Cache::store(self::FILE_STORE)->getMany($keys);
        }
    }

    /**
     * Store multiple items in cache, with automatic fallback
     *
     * @param array $values Associative array of key => value pairs
     * @param int|null $seconds TTL in seconds (null = forever)
     * @return void
     */
    public function putMany(array $values, $seconds = null): void
    {
        try {
            Cache::store(self::REDIS_STORE)->putMany($values, $seconds);
        } catch (Exception $e) {
            $this->logFallback('putMany', count($values) . ' items', $e);
            Cache::store(self::FILE_STORE)->putMany($values, $seconds);
        }
    }

    /**
     * Log fallback event with diagnostic information
     *
     * @param string $operation The cache operation (get, put, forget, etc)
     * @param string $key The cache key involved
     * @param Exception $exception The exception that triggered fallback
     * @return void
     */
    private function logFallback(string $operation, string $key, Exception $exception): void
    {
        $message = sprintf(
            "%s Redis %s() failed for key '%s': %s — Falling back to file cache",
            self::WARNING_PREFIX,
            $operation,
            $key,
            $exception->getMessage()
        );

        Log::warning($message, [
            'operation' => $operation,
            'key' => $key,
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'fallback_store' => self::FILE_STORE,
        ]);
    }

    /**
     * Get the current cache store name (for diagnostic purposes)
     *
     * @return string Either 'redis' or 'file'
     */
    public function getCurrentStore(): string
    {
        return config('cache.default') === self::REDIS_STORE ? self::REDIS_STORE : self::FILE_STORE;
    }

    /**
     * Test Redis connectivity without affecting actual cache
     *
     * Useful for health checks and debugging Redis availability.
     * Returns true if Redis is connected, false if not.
     *
     * @return bool True if Redis is available, false otherwise
     */
    public function isRedisAvailable(): bool
    {
        try {
            Cache::store(self::REDIS_STORE)->get('__redis_health_check__');
            return true;
        } catch (Exception $e) {
            Log::debug('Redis health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform explicit Redis PING test
     *
     * Attempts to execute a PING command on Redis using Predis/PhpRedis directly.
     * Returns array with status and optional message. Never throws exceptions.
     *
     * @return array ['status' => 'ok'|'unavailable'|'error'|'skipped', 'message' => string]
     */
    public function testRedisPing(): array
    {
        try {
            $cacheDriver = config('cache.default');

            // Only test if Redis is configured as cache store
            if ($cacheDriver !== self::REDIS_STORE) {
                return [
                    'status' => 'skipped',
                    'message' => 'Redis not configured as cache store (using: ' . $cacheDriver . ')',
                ];
            }

            // Test Redis via a simple cache operation
            try {
                Cache::store(self::REDIS_STORE)->get('__redis_ping_test__');
                return [
                    'status' => 'ok',
                    'message' => 'Redis connection verified',
                ];
            } catch (Exception $e) {
                return [
                    'status' => 'unavailable',
                    'message' => 'Redis connection failed: ' . $e->getMessage(),
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Redis health check exception: ' . $e->getMessage(),
            ];
        }
    }
}
