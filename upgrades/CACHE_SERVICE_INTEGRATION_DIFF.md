# Cache Service Integration - Comprehensive Diff

## File 1: app/Http/Controllers/VanigamController.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 4 Cache operations in `sendOtp()` method

```diff
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\TwoFactorOtpService;
+use App\Services\CacheService;
use App\Services\MongoService;
use App\Helpers\VoterHelper;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

class VanigamController extends Controller
{
    protected $otpService;
    protected $mongo;
    protected $cloudinary;
+   protected $cache;

-   public function __construct(TwoFactorOtpService $otpService, MongoService $mongo)
+   public function __construct(TwoFactorOtpService $otpService, MongoService $mongo, CacheService $cache)
    {
        $this->otpService = $otpService;
        $this->mongo = $mongo;
+       $this->cache = $cache;
        $this->cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.url'));
    }

    /**
     * POST /api/vanigam/send-otp
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');

            // Rate limit: 3 OTPs per 5 minutes per IP
            $rateLimitKey = 'otp_limit:' . $request->ip();
-           $otpCount = Cache::get($rateLimitKey, 0);
+           $otpCount = $this->cache->get($rateLimitKey, 0);
            if ($otpCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP requests. Please try after 5 minutes.',
                ], 429);
            }

            // Cooldown: 60s between OTP requests for same mobile
            $cooldownKey = 'otp_cooldown:' . $mobile;
-           if (Cache::has($cooldownKey)) {
+           if ($this->cache->has($cooldownKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP already sent. Please wait before requesting again.',
                ], 429);
            }

            $result = $this->otpService->sendOtp($mobile);

            if ($result['success']) {
-               Cache::put($rateLimitKey, $otpCount + 1, 300);
-               Cache::put($cooldownKey, true, 60);
+               $this->cache->put($rateLimitKey, $otpCount + 1, 300);
+               $this->cache->put($cooldownKey, true, 60);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to +91' . $mobile,
                    'mobile' => $mobile,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Could not send OTP.',
            ], 500);

        } catch (Exception $e) {
            Log::error('VanigamController::sendOtp Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }
```

---

## File 2: app/Http/Controllers/UserController.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 4 Cache operations (OTP rate limiting and job status tracking)

```diff
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\TwoFactorOtpService;
+use App\Services\CacheService;
use App\Services\OtpService;
use App\Services\MongoService;
use App\Helpers\VoterHelper;
use App\Jobs\GenerateCardJob;
use Exception;

class UserController extends Controller
{
    protected $otpService;
    protected $twoFactorOtpService;
    protected $mongo;
+   protected $cache;

-   public function __construct(OtpService $otpService, TwoFactorOtpService $twoFactorOtpService, MongoService $mongo)
+   public function __construct(OtpService $otpService, TwoFactorOtpService $twoFactorOtpService, MongoService $mongo, CacheService $cache)
    {
        $this->otpService = $otpService;
        $this->twoFactorOtpService = $twoFactorOtpService;
        $this->mongo = $mongo;
+       $this->cache = $cache;
    }

    /**
     * ... other methods ...
     */

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10|regex:/^[6-9]\d{9}$/',
            ]);

            $mobile = $request->input('mobile');
            $rateLimitKey = 'otp_limit:' . $request->ip();
-           $otpCount = Cache::get($rateLimitKey, 0);
+           $otpCount = $this->cache->get($rateLimitKey, 0);
            if ($otpCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP requests. Please try after 5 minutes.',
                ], 429);
            }

-           Cache::put($rateLimitKey, $otpCount + 1, 300); // 5 minutes
+           $this->cache->put($rateLimitKey, $otpCount + 1, 300); // 5 minutes

            // ... rest of method
        }
    }

    public function getJobStatus(Request $request)
    {
        try {
            $jobId = $request->input('job_id');
-           $jobStatus = Cache::get('job:' . $jobId);
+           $jobStatus = $this->cache->get('job:' . $jobId);

            if (!$jobStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found.',
                ], 404);
            }

            return response()->json($jobStatus);
        } catch (Exception $e) {
            Log::error('UserController::getJobStatus Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    public function downloadCertificate(Request $request)
    {
        try {
            $jobId = $request->input('job_id');
-           $jobStatus = Cache::get('job:' . $jobId);
+           $jobStatus = $this->cache->get('job:' . $jobId);

            // ... rest of method
        }
    }
```

---

## File 3: app/Services/TwoFactorOtpService.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 3 Cache operations in OTP session management

```diff
<?php

namespace App\Services;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class TwoFactorOtpService
{
    protected $apiKey;
+   protected $cache;

-   public function __construct()
+   public function __construct(CacheService $cache)
    {
+       $this->cache = $cache;
        $this->apiKey = config('2factor.api_key');
    }

    /**
     * ... other methods ...
     */

    public function sendOtp($mobile)
    {
        try {
            // ... API call logic ...

            if ($response['Status'] === 'Success') {
                $sessionId = $response['Details'];
-               Cache::put('otp_session:' . $mobile, $sessionId, 600);
+               $this->cache->put('otp_session:' . $mobile, $sessionId, 600);

                return [
                    'success' => true,
                    'session_id' => $sessionId,
                ];
            }

            // ... error handling ...
        } catch (Exception $e) {
            Log::error('TwoFactorOtpService::sendOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to send OTP'];
        }
    }

    public function verifyOtp($mobile, $otp)
    {
        try {
-           $sessionId = Cache::get('otp_session:' . $mobile);
+           $sessionId = $this->cache->get('otp_session:' . $mobile);

            if (!$sessionId) {
                return [
                    'success' => false,
                    'error' => 'No OTP session found for this mobile.',
                ];
            }

            // ... OTP verification logic ...

            if ($verifyResponse['Status'] === 'Success') {
-               Cache::forget('otp_session:' . $mobile);
+               $this->cache->forget('otp_session:' . $mobile);

                return [
                    'success' => true,
                    'message' => 'OTP verified successfully',
                ];
            }

            // ... error handling ...
        } catch (Exception $e) {
            Log::error('TwoFactorOtpService::verifyOtp Error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'OTP verification failed'];
        }
    }
```

---

## File 4: app/Services/VoterService.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 3 Cache operations (voter caching and invalidation)

```diff
<?php

namespace App\Services;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class VoterService
{
    protected $voterTable;
+   protected $cache;

-   public function __construct()
+   public function __construct(CacheService $cache)
    {
+       $this->cache = $cache;
        // ... initialization ...
    }

    public function getVoterByEpic($epicNo)
    {
        try {
            $cacheKey = "voter:epic:{$epicNo}";
-           return Cache::remember($cacheKey, 600, function () use ($epicNo) {
+           return $this->cache->remember($cacheKey, 600, function () use ($epicNo) {
                // Query voter from MySQL
                return DB::table('voters')->where('epic_no', $epicNo)->first();
            });
        } catch (Exception $e) {
            Log::error('VoterService::getVoterByEpic Error: ' . $e->getMessage());
            return null;
        }
    }

    public function invalidateVoterCache($epicNo)
    {
        try {
-           Cache::forget("voter:epic:{$epicNo}");
-           Cache::forget('assembly_tables');
+           $this->cache->forget("voter:epic:{$epicNo}");
+           $this->cache->forget('assembly_tables');

            return true;
        } catch (Exception $e) {
            Log::error('VoterService::invalidateVoterCache Error: ' . $e->getMessage());
            return false;
        }
    }
```

---

## File 5: app/Helpers/VoterHelper.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 10 Cache operations (voter lookups and assembly data caching)

```diff
<?php

namespace App\Helpers;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CacheService;
use Exception;

class VoterHelper
{
+   protected static $cache;
+
+   public static function setCache(CacheService $cache)
+   {
+       self::$cache = $cache;
+   }

    public static function searchByEpic($epicNo)
    {
        try {
            $cacheKey = "voter:epic:{$epicNo}";
-           $cached = Cache::get($cacheKey);
+           $cached = self::$cache->get($cacheKey);
            if ($cached) {
                return $cached;
            }

            // Query using DB...
            $result = DB::table('voters')->where('epic_no', $epicNo)->first();

            if ($result) {
-               Cache::put($cacheKey, $result, 600);
+               self::$cache->put($cacheKey, $result, 600);
                return $result;
            } else {
-               Cache::put($cacheKey, ['epic_no' => ''], 120);
+               self::$cache->put($cacheKey, ['epic_no' => ''], 120);
                return null;
            }
        } catch (Exception $e) {
            Log::error('VoterHelper::searchByEpic Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getAssemblyTables()
    {
        try {
-           return Cache::remember('voter:assembly_tables', 3600, function () {
+           return self::$cache->remember('voter:assembly_tables', 3600, function () {
                return DB::table('assembly')->get();
            });
        } catch (Exception $e) {
            Log::error('VoterHelper::getAssemblyTables Error: ' . $e->getMessage());
            return collect();
        }
    }

    public static function invalidateAllCaches($epicNo)
    {
        try {
-           Cache::forget("voter:epic:{$epicNo}");
-           Cache::forget('voter:assembly_tables');
-           Cache::forget('voter:assemblies');
-           Cache::forget('voter:districts');
+           self::$cache->forget("voter:epic:{$epicNo}");
+           self::$cache->forget('voter:assembly_tables');
+           self::$cache->forget('voter:assemblies');
+           self::$cache->forget('voter:districts');

            return true;
        } catch (Exception $e) {
            Log::error('VoterHelper::invalidateAllCaches Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAssemblies()
    {
        try {
-           return Cache::remember('voter:assemblies', 3600, function () {
+           return self::$cache->remember('voter:assemblies', 3600, function () {
                return DB::table('assembly')->get();
            });
        } catch (Exception $e) {
            Log::error('VoterHelper::getAssemblies Error: ' . $e->getMessage());
            return collect();
        }
    }

    public static function getDistricts()
    {
        try {
-           return Cache::remember('voter:districts', 3600, function () {
+           return self::$cache->remember('voter:districts', 3600, function () {
                return DB::table('district')->get();
            });
        } catch (Exception $e) {
            Log::error('VoterHelper::getDistricts Error: ' . $e->getMessage());
            return collect();
        }
    }
```

---

## File 6: app/Helpers/StatisticsHelper.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 4 Cache operations (dashboard statistics caching)

```diff
<?php

namespace App\Helpers;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CacheService;
use Exception;

class StatisticsHelper
{
+   protected static $cache;
+
+   public static function setCache(CacheService $cache)
+   {
+       self::$cache = $cache;
+   }

    public static function getDashboardStats()
    {
        try {
-           return Cache::remember('stats:dashboard', 60, function () {
+           return self::$cache->remember('stats:dashboard', 60, function () {
                return [
                    'total_members' => DB::table('members')->count(),
                    'verified_members' => DB::table('members')->where('verified', true)->count(),
                    // ... more stats
                ];
            });
        } catch (Exception $e) {
            Log::error('StatisticsHelper::getDashboardStats Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function invalidateAllCaches()
    {
        try {
-           Cache::forget('stats:dashboard');
-           Cache::forget('stats:assembly');
-           Cache::forget('stats:district');
+           self::$cache->forget('stats:dashboard');
+           self::$cache->forget('stats:assembly');
+           self::$cache->forget('stats:district');

            return true;
        } catch (Exception $e) {
            Log::error('StatisticsHelper::invalidateAllCaches Error: ' . $e->getMessage());
            return false;
        }
    }
```

---

## File 7: app/Services/OtpService.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 1 cache() helper call

```diff
<?php

namespace App\Services;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\VoterHelper;
+use App\Services\CacheService;
use Exception;

class OtpService
{
+   protected $cache;
+
+   public function __construct(CacheService $cache)
+   {
+       $this->cache = $cache;
+   }

    public function generateOtp($mobile)
    {
        try {
            $otp = random_int(100000, 999999);
-           cache()->put("test_otp_{$mobile}", $otp, 300); // 5 minutes
+           $this->cache->put("test_otp_{$mobile}", $otp, 300); // 5 minutes

            return $otp;
        } catch (Exception $e) {
            Log::error('OtpService::generateOtp Error: ' . $e->getMessage());
            return null;
        }
    }
```

---

## File 8: app/Jobs/GenerateCardJob.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 1 Cache operation (job status tracking)

```diff
<?php

namespace App\Jobs;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use App\Services\CloudinaryService;
+use App\Services\CacheService;
use Exception;

class GenerateCardJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $jobId;
    protected $memberData;
+   protected $cache;

    public function __construct($jobId, $memberData, CacheService $cache)
    {
        $this->jobId = $jobId;
        $this->memberData = $memberData;
+       $this->cache = $cache;
    }

    public function handle()
    {
        try {
            // ... card generation logic ...

            $jobStatus = [
                'status' => 'completed',
                'card_url' => $cardUrl,
            ];

-           Cache::put('job:' . $this->jobId, $jobStatus, 3600);
+           $this->cache->put('job:' . $this->jobId, $jobStatus, 3600);

        } catch (Exception $e) {
            Log::error("GenerateCardJob failed: " . $e->getMessage());
        }
    }
```

---

## File 9: app/Models/AssemblyConstituency.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 1 Cache operation (assembly table caching)

```diff
<?php

namespace App\Models;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
+use App\Services\CacheService;
use Exception;

class AssemblyConstituency extends Model
{
+   protected $cache;
+
+   public function __construct(CacheService $cache)
+   {
+       $this->cache = $cache;
+       parent::__construct();
+   }

    public static function getAssemblyTables()
    {
        try {
-           return Cache::remember('assembly_tables', 600, function () {
+           $cache = app(CacheService::class);
+           return $cache->remember('assembly_tables', 600, function () {
                return DB::table('assembly_constituency')->get();
            });
        } catch (Exception $e) {
            Log::error('AssemblyConstituency::getAssemblyTables Error: ' . $e->getMessage());
            return collect();
        }
    }
```

---

## File 10: app/Http/Controllers/ApiController.php

### Changes:
- Add `CacheService` import and dependency injection
- Replace 1 Cache operation (health check)

```diff
<?php

namespace App\Http\Controllers;

-use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
+use App\Services\CacheService;
use Exception;

class ApiController extends Controller
{
+   protected $cache;
+
+   public function __construct(CacheService $cache)
+   {
+       $this->cache = $cache;
+   }

    public function health()
    {
        try {
            $health = ['status' => 'ok'];

            // Check Cache
            try {
                $cacheDriver = config('cache.default');
                if ($cacheDriver === 'file' || $cacheDriver === 'array') {
-                   Cache::get('health_check');
+                   $this->cache->get('health_check');
                    $health['cache'] = 'ok (' . $cacheDriver . ')';
                } else {
-                   $health['cache'] = 'skipped (using ' . $cacheDriver . ')';
+                   $health['cache'] = $this->cache->isRedisAvailable() ? 'ok (redis)' : 'error (redis)';
                    $health['cache_driver'] = $cacheDriver;
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
```

---

## Summary of Changes

| File | Operations | Type |
|------|-----------|------|
| VanigamController.php | 4 (get, has, put x2) | Dependency Injection |
| UserController.php | 4 (get, put x2, get) | Dependency Injection |
| TwoFactorOtpService.php | 3 (put, get, forget) | Dependency Injection |
| VoterService.php | 3 (remember, forget x2) | Dependency Injection |
| VoterHelper.php | 10 (get, put x2, remember x3, forget x4) | Static Helper |
| StatisticsHelper.php | 4 (remember, forget x3) | Static Helper |
| OtpService.php | 1 (put) | Dependency Injection |
| GenerateCardJob.php | 1 (put) | Constructor Parameter |
| AssemblyConstituency.php | 1 (remember) | Service Container |
| ApiController.php | 1 (get + new method) | Dependency Injection |

**Total Operations Replaced: 31**

---

## Implementation Pattern

All files follow one of these patterns:

1. **Dependency Injection (Controllers/Services):**
   ```php
   public function __construct(CacheService $cache)
   {
       $this->cache = $cache;
   }
   ```

2. **Static Helpers (Helpers):**
   ```php
   public static function setCache(CacheService $cache)
   {
       self::$cache = $cache;
   }
   ```

3. **Service Container (Models):**
   ```php
   $cache = app(CacheService::class);
   ```
