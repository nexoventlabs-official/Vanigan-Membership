# CLAUDE.md - Tamil Nadu Vanigargalin Sangamam (Project-5)

## 📋 Project Overview

**Name:** Tamil Nadu Vanigargalin Sangamam
**Type:** Member ID Card System (Laravel 11)
**Status:** Production-ready & Deployed
**Website:** https://vanigan.digital
**Framework:** Laravel 11 (PHP 8.2+)
**Current Branch:** `trial-staging` (for testing) / `main` (production)

### Purpose
Mobile-first registration system for Tamil Nadu merchants' organization members. Users register via WhatsApp-style chat UI, get OTP verification, upload photos, generate membership cards, and access a referral system. Admin panel for dashboard and statistics.

---

## 🏗️ Architecture & Tech Stack

### Frontend
- **Vanilla JavaScript** + **Tailwind CSS** (Chat UI for registration)
- **Bootstrap** (Admin panel)
- **QR Code Generation** (Endroid library)
- **Image Uploads** with face detection capability

### Backend
- **Laravel 11** (PHP 8.2+)
- **9 Controllers** managing different features
- **9 Services** for business logic
- **8 Models** for data management
- **3 Middleware** for authentication/validation

### Databases
1. **MySQL** (Read-only, voter lookup)
   - Host: `174.138.49.116`
   - Database: `hkqbnymdjz` (234 tables, ~5M voters)
   - Connection: `voters` (separate from default)
   - Tables: Assembly-based (`ac001`, `ac002`, etc.)

2. **MongoDB Atlas** (Member data, profiles)
   - Database: `vanigan`
   - Collections: `members` (primary storage)
   - BSON/JSON conversion handled in `MongoService::recursiveConvert()`

### External Services
- **Upstash Redis** (Per-environment cache)
  - Trial: `humble-grubworm-79324.upstash.io` (NEW)
  - Production: `striking-jaybird-66451.upstash.io` (ORIGINAL)
  - Used for: OTP rate limiting, session data, cache layer

- **Cloudinary** (Image uploads)
  - Two separate accounts (main + assets)
  - Handles: Member photos, card images, website assets

- **2Factor.in OTP Service** (Voice call verification)
  - API: Voice OTP delivery
  - Fallback in code if service unavailable

### Cache Strategy
- **Primary:** Redis via `CacheService` (configurable per environment)
- **Fallback:** File-based cache (automatic if Redis unavailable)
- **Session:** File-based (no Redis dependency)

---

## 🚀 Deployment Setup

### Branch Strategy
```
main (production only)
├── production deployments (vanigan.digital)
├── locked for safety - merge only after trial validation
└── created from trial-staging after QA passes

trial-staging (testing & upgrades)
├── all trial testing happens here
├── all upgrades developed here
├── QA validated before merge to main
└── separate Redis instance (humble-grubworm-79324)
```

### Servers
| Environment | URL | Host | Redis | Branch |
|-------------|-----|------|-------|--------|
| **Production** | vanigan.digital | Cloudways | striking-jaybird-66451 | main |
| **Trial** | phpstack-1603086-6293159.cloudwaysapps.com | Cloudways | humble-grubworm-79324 | trial-staging |

### Deployment Workflow
1. **Develop on `trial-staging` branch**
   - Make changes, test on trial server
   - Commit with clear messages

2. **QA Testing on Trial**
   - Verify all functionality works
   - Test Redis cache, rate limiting, API endpoints
   - Check logs for errors

3. **Merge to Main**
   ```bash
   git checkout main
   git merge trial-staging
   git push origin main
   ```

4. **Deploy to Production**
   - Cloudways auto-deploys from git (if configured)
   - Or manual deployment via Cloudways console
   - Verify `/api/health` endpoint responds

### Environment Variables
Key differences between trial and production:
- `APP_URL`: Trial vs Production URLs
- `REDIS_HOST` & `REDIS_PASSWORD`: Separate instances per environment
- All other configs (MongoDB, MySQL, Cloudinary) are shared

---

## 🔐 Security Rules (IMPORTANT - Read These)

### Middleware Architecture
```
bootstrap/app.php
├── 'admin.auth' → AdminAuthMiddleware (session-based)
└── 'validate.admin.api.key' → ValidateAdminApiKey (header-based)

routes/api.php
├── Public endpoints (OTP, member lookup, card generation - NO middleware)
├── Protected: /reset-members (API key required)
└── Protected: /upload-card-images (API key required)

routes/web.php
├── Public: Chat UI, card views, referral pages
└── Protected: /admin/* (requires admin.auth middleware)
```

### Code-Level Security
1. **OTP Rate Limiting** (VanigamController::sendOtp)
   - 3 OTPs per 5 minutes per IP
   - 60-second cooldown between requests per mobile number
   - Cached in Redis (with file fallback)

2. **Admin Authentication** (AdminAuthMiddleware)
   - Session-based, bcrypt password hashing
   - Credentials: `ADMIN_USERNAME` + `ADMIN_PASSWORD_HASH` in .env

3. **API Key Protection** (ValidateAdminApiKey)
   - Sensitive endpoints protected via `X-Admin-Key` header
   - Key stored in `config/vanigam.php` as `admin_api_key`
   - Endpoints: `/reset-members`, `/upload-card-images`

4. **Input Validation**
   - Mobile: 10 digits, starts with 6-9
   - Photo uploads: Max 15MB (CONSISTENT across all upload methods)
   - EPIC validation: Numeric, fuzzy lookup across 234 tables
   - PIN: 4-digit numeric codes

5. **Database Security**
   - MySQL: Read-only (no injection risk)
   - MongoDB: Document validation in MongoService
   - Credentials stored in .env (NEVER in version control)

### What to Check Before Making Changes
- [ ] Will this change expose API endpoints unintentionally?
- [ ] Does this modify rate limiting logic?
- [ ] Does this add new OTP/PIN verification?
- [ ] Does this touch MySQL queries (read-only?)?
- [ ] Does this affect `.env` or secrets? (should NOT be committed)
- [ ] Does this require API key middleware?

---

## 📁 Project Structure

```
project-5/
├── app/
│   ├── Console/
│   │   └── Commands/ (Artisan commands)
│   ├── Http/
│   │   ├── Controllers/ (9 controllers)
│   │   │   ├── VanigamController.php (main API - 35KB)
│   │   │   ├── AdminPanelController.php (admin dashboard)
│   │   │   ├── AdminController.php, ApiController.php, etc.
│   │   ├── Middleware/ (3 middleware)
│   │   │   ├── AdminAuthMiddleware.php (session-based)
│   │   │   ├── ValidateAdminApiKey.php (API key check)
│   │   │   └── AdminAuth.php (legacy)
│   ├── Services/ (9 services)
│   │   ├── CacheService.php (Redis + file fallback)
│   │   ├── MongoService.php (MongoDB operations + BSON conversion)
│   │   ├── TwoFactorOtpService.php (2Factor.in integration)
│   │   ├── CloudinaryService.php (image uploads)
│   │   ├── VoterLookupService.php, etc.
│   ├── Models/ (8 models)
│   │   ├── OtpSession.php, User.php, VerifiedMobile.php
│   │   ├── GeneratedVoter.php, AssemblyConstituency.php, etc.
│   ├── Helpers/ (3 helpers)
│   │   ├── VoterHelper.php (EPIC lookup with caching)
│   │   ├── StatisticsHelper.php (dashboard stats)
│   │   └── SecurityHelper.php
│   ├── Jobs/
│   │   └── GenerateCardJob.php (card generation job)
│
├── config/
│   ├── app.php, cache.php, database.php, services.php
│   ├── vanigam.php (reset key + admin API key)
│   ├── mongodb.php (MongoDB connection config)
│   └── cloudinary.php
│
├── routes/
│   ├── api.php (17 endpoints, all public or API-key protected)
│   └── web.php (11 routes, mix of public + admin-protected)
│
├── resources/
│   ├── views/ (Blade templates for chat UI, admin panel)
│   ├── css/, js/ (Tailwind, Vanilla JS, Bootstrap)
│
├── bootstrap/
│   └── app.php (middleware registration, routing setup)
│
├── database/
│   ├── migrations/ (Laravel migrations)
│   └── seeders/ (optional data seeders)
│
├── docs/ (7 comprehensive docs)
│   ├── README.md, API.md, ARCHITECTURE.md
│   ├── ADMIN_GUIDE.md, INSTALLATION.md, TROUBLESHOOTING.md
│
├── upgrades/ (tracking document)
│   ├── UPGRADES.md (all upgrades + pending tasks)
│   ├── API_KEY_MIDDLEWARE_TEST_GUIDE.md
│   ├── DEPLOYMENT_SUMMARY.md
│   └── IMPLEMENTATION_COMPLETE.md
│
├── .env.example (base template)
├── .env.trial.example (trial-specific template)
├── .env.production.example (production-specific template)
├── composer.json (dependencies)
└── CLAUDE.md (this file)
```

---

## 🔄 Known Patterns Already Implemented

### 1. CacheService - Resilient Redis Wrapper
**File:** `app/Services/CacheService.php` (263 lines)

Handles Redis caching with automatic fallback to file-based cache:
```php
// Usage in any controller/service
public function __construct(CacheService $cache) {
    $this->cache = $cache;
}

// All operations auto-fallback if Redis fails
$this->cache->get($key, $default);
$this->cache->put($key, $value, $seconds);
$this->cache->has($key);
$this->cache->remember($key, $seconds, $callback);
$this->cache->forget($key);
```

**Used in:** 31 locations across controllers, services, and helpers
- OTP rate limiting
- Voter lookup caching
- Session data
- Rate limit counters

### 2. ValidateAdminApiKey Middleware
**File:** `app/Http/Middleware/ValidateAdminApiKey.php`

Validates `X-Admin-Key` header for sensitive API endpoints:
```php
// In routes/api.php
Route::post('/reset-members', [VanigamController::class, 'resetMembers'])
    ->middleware('validate.admin.api.key');

// In requests
headers: {'X-Admin-Key': 'your_admin_api_key_here'}
```

**Configuration:** `config/vanigam.php`
- `admin_api_key` from `VANIGAM_ADMIN_API_KEY` env variable

### 3. MongoService - BSON/JSON Conversion
**File:** `app/Services/MongoService.php` (80+ lines)

Handles MongoDB document conversion:
- BSON dates to PHP timestamps
- ObjectIds to strings
- Recursive conversion ensures nested data works
- Used in all MongoDB write/read operations

### 4. VoterHelper - MySQL Lookup with Caching
**File:** `app/Helpers/VoterHelper.php`

EPIC voter lookups across 234 assembly tables:
- Batch search (30 tables at a time to avoid SQL timeout)
- Redis caching: 10 minutes for hits, 2 minutes for misses
- Handles voter data transformation

### 5. OTP Rate Limiting
**File:** `app/Http/Controllers/VanigamController.php::sendOtp()`

Enforced at code level:
- Rate limit cache key: `otp_limit:{ip_address}`
- Cooldown cache key: `otp_cooldown:{mobile}`
- HTTP 429 response when limits exceeded

---

## 🛑 What NOT to Do

### ❌ Never Push Direct to Main
- Always work on `trial-staging` branch
- Test thoroughly on trial server first
- Only merge to main after validation
- Never force-push to main

### ❌ Never Modify .env in Git
- .env contains secrets (database passwords, API keys, Redis credentials)
- Always use `.env.example` for template changes
- Production secrets are configured directly on Cloudways server
- Check: `git status` should NOT show `.env` changes

### ❌ Never Change MySQL Queries to Write Operations
- MySQL connection is **read-only by design**
- All write operations use MongoDB
- Voter lookup only (no updates/inserts)

### ❌ Never Skip Security Checks on Sensitive Endpoints
- `/api/vanigam/reset-members` - must have API key middleware
- `/api/vanigam/upload-card-images` - must have API key middleware
- New admin endpoints - must have `admin.auth` middleware

### ❌ Never Remove Existing Middleware Protected Routes
- Don't remove `->middleware('admin.auth')` from admin routes
- Don't remove `->middleware('validate.admin.api.key')` from sensitive endpoints

### ❌ Never Change Photo Upload Size Limits Without Consistency
- Current limit: **15MB** (consistent across all upload methods)
- Check: `VanigamController::uploadPhoto()` and `::validatePhotoUpload()`
- Both must have same limit

### ❌ Never Commit with —no-verify or —no-gpg-sign
- Let pre-commit hooks run
- If they fail, fix the underlying issue
- Don't bypass safety checks

### ❌ Never Create Test Routes in routes/web.php
- Previously had `/test/card` and `/test/pin` - REMOVED in security audit
- Use dedicated test files or API endpoints instead
- Don't pollute route files with temporary endpoints

---

## 🚦 Development Workflow

### Quick Start
1. **Check current branch:**
   ```bash
   git status
   ```
   Should show: `On branch trial-staging`

2. **Pull latest changes:**
   ```bash
   git pull origin trial-staging
   ```

3. **Create feature branch (optional):**
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Make changes, commit locally:**
   ```bash
   git add app/Http/Controllers/YourController.php
   git commit -m "feat: add new feature description"
   ```

5. **Push to trial-staging:**
   ```bash
   git push origin trial-staging
   ```

6. **Test on trial server:**
   - Visit: https://phpstack-1603086-6293159.cloudwaysapps.com
   - Test API: `/api/health`, `/api/vanigam/send-otp`, etc.
   - Check logs: Cloudways console → Logs

7. **After validation, merge to main:**
   ```bash
   git checkout main
   git pull origin main
   git merge trial-staging
   git push origin main
   ```

### Git Commit Message Format
```
[type]: [description]

Examples:
feat: add password reset functionality
fix: correct OTP rate limiting calculation
docs: update API documentation for new endpoint
refactor: simplify voter lookup caching logic
security: add API key validation to reset endpoint
test: add unit tests for MongoService
```

### Before Committing - Show Diff
```bash
# Always check what you're committing
git diff app/Http/Controllers/VanigamController.php

# Stage specific files (not entire directories)
git add app/Http/Controllers/VanigamController.php
git status  # verify what's staged

# Then commit
git commit -m "fix: clear description of changes"
```

---

## 📊 API Endpoints (17 Total)

### Base Path: `/api/vanigam/`

#### Authentication & OTP
- `POST /check-member` - Check if mobile exists (returning user)
- `POST /send-otp` - Send OTP via 2Factor (rate limited: 3 per 5 min)
- `POST /verify-otp` - Verify OTP code

#### Voter Validation & Photo
- `POST /validate-epic` - Lookup voter by EPIC number (MySQL)
- `POST /upload-photo` - Upload member photo (15MB limit)
- `POST /validate-photo` - Validate photo (15MB limit)

#### Card Generation
- `POST /generate-card` - Generate membership card (MongoDB)
- `POST /save-details` - Save additional member details
- `GET /member/{uniqueId}` - Retrieve member data
- `GET /qr/{uniqueId}` - Generate QR code image

#### Admin Operations (API Key Required)
- `POST /reset-members` - Reset all MongoDB members (X-Admin-Key)
- `POST /upload-card-images` - Upload card front/back (X-Admin-Key)

#### PIN & Verification
- `POST /verify-pin` - Verify member PIN
- `POST /verify-member-pin` - Verify PIN for QR scan

#### Referral & Loan
- `POST /get-referral` - Get referral info
- `POST /increment-referral` - Increment referral count
- `POST /loan-request` - Submit loan request
- `POST /check-loan-status` - Check loan status

#### Health
- `GET /health` - Health check endpoint

---

## 💾 Models & Database Schema

### MongoDB Collections (`vanigan` database)
- **members** - Core collection for member data
  - Fields: uniqueId, mobile, name, photo, card details, PIN
  - Includes: referral info, loan requests, verification status

### MySQL Voters Tables (Read-only)
- **ac001 through ac234** - Assembly constituency tables
  - Fields: EPIC_NO, NAME, RELATIVE_NAME, ADDRESS, AGE, etc.
  - ~5 million voter records across all tables

### Laravel Models
- `User.php` - Admin users
- `OtpSession.php` - OTP session tracking
- `VerifiedMobile.php` - Verified phone numbers
- `GeneratedVoter.php` - Generated voter data
- `AssemblyConstituency.php` - Assembly info
- `GenerationStat.php` - Statistics tracking
- `BoothAgentRequest.php` - Booth agent requests
- `VolunteerRequest.php` - Volunteer requests

---

## 🔧 Configuration Files Reference

### Key Configs
- `config/app.php` - App name, timezone, providers
- `config/database.php` - MySQL + voters connection
- `config/cache.php` - Cache store (redis or file)
- `config/services.php` - 2Factor API key
- `config/mongodb.php` - MongoDB connection
- `config/vanigam.php` - Reset key + Admin API key (NEW)
- `config/cloudinary.php` - Cloudinary credentials

### Redis Configuration - CRITICAL ⚠️
**IMPORTANT FOR UPSTASH REDIS:**

Redis requires **REDIS_SCHEME=tls** and **REDIS_CACHE_DB=0** configuration:
- **REDIS_SCHEME=tls** must be added to `config/database.php` Redis connections
- Without this, Predis connects via TCP and authentication fails (Upstash only accepts TLS)
- **REDIS_CACHE_DB=0** — Upstash does NOT support Redis DB 1 or higher (only DB 0)
- Set in `.env`: `REDIS_SCHEME=tls` and `REDIS_DB=0`

**Example in config/database.php Redis connection:**
```php
'redis' => [
    'scheme' => env('REDIS_SCHEME', 'tcp'),
    'host' => env('REDIS_HOST', 'localhost'),
    'password' => env('REDIS_PASSWORD', null),
    'port' => env('REDIS_PORT', 6379),
    'database' => env('REDIS_db', 0),
],
```

**In .env files (.env.example, .env.trial.example, .env.production.example):**
```
REDIS_SCHEME=tls
REDIS_HOST=your_upstash_host
REDIS_PASSWORD=your_upstash_password
REDIS_PORT=6379
REDIS_DB=0
```

---

### Middleware Registration
**File:** `bootstrap/app.php` (lines 14-18)
```php
$middleware->alias([
    'admin.auth' => \App\Http\Middleware\AdminAuthMiddleware::class,
    'validate.admin.api.key' => \App\Http\Middleware\ValidateAdminApiKey::class,
]);
```

---

## ✅ Pending Upgrades & TODOs

### Recent Completed (Verified)
- ✅ CacheService integration (31 replacements across codebase)
- ✅ Redis separation per environment (trial vs production)
- ✅ ValidateAdminApiKey middleware for sensitive endpoints
- ✅ Health check endpoint with Redis PING testing
- ✅ Photo upload limit consistency (15MB)
- ✅ Test routes removal (`/test/card`, `/test/pin`)
- ✅ API middleware protection on reset-members + upload-card-images

### Critical Pre-Production Tasks
- [ ] **REMOVE public/redis-test.php** before deploying to production (security risk - debugging tool only)
  - Location: `d:/Cloudways/project-5/public/redis-test.php`
  - Status: Currently present in repo, must be deleted before main merge
  - Risk: Exposes Redis debugging endpoint publicly if deployed

### Optional Enhancements
- [ ] Add unique_id ownership verification for upload-card-images (2nd layer security)
- [ ] Apply rate limiting middleware (Laravel throttle) to sensitive endpoints
- [ ] Add monitoring/alerts for Redis connection failures
- [ ] Set up MongoDB automated backups (Atlas dashboard configuration)
- [ ] Create backup restoration procedures documentation
- [ ] Implement API-level rate limiting middleware for general endpoints

### Deployment Checklist
- [ ] Verify `/api/health` returns HTTP 200 on production
- [ ] Test OTP flow (send → verify)
- [ ] Check Laravel logs for errors
- [ ] Verify Redis connection status
- [ ] Test admin login functionality
- [ ] Validate card generation works
- [ ] Confirm QR code generation
- [ ] Check Cloudinary image uploads
- [ ] Test MongoDB member storage
- [ ] Verify email/OTP delivery

---

## 📞 Key Contacts & Documentation

- **API Docs:** `docs/API.md`
- **Architecture:** `docs/ARCHITECTURE.md`
- **Admin Guide:** `docs/ADMIN_GUIDE.md`
- **Troubleshooting:** `docs/TROUBLESHOOTING.md`
- **Upgrades Tracker:** `upgrades/UPGRADES.md`

---

## 🎯 Critical Success Factors

1. **Always test on trial-staging branch first** before production merge
2. **Never modify .env files in git** - use .env.example for templates
3. **Maintain code-level security checks** (rate limiting, validation)
4. **Keep CacheService updated** for all caching operations
5. **Validate API key middleware** on sensitive endpoints
6. **Separate Redis instances** per environment (no data pollution)
7. **Monitor laravel.log** after deployments
8. **Use config() not env()** in application code
9. **Test full user flow** before declaring changes complete
10. **Document all changes** in commit messages

---

**Last Updated:** March 21, 2026
**Branch:** trial-staging
**Status:** Production-ready with continuous improvements
