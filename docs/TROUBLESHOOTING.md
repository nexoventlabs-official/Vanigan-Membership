# Troubleshooting Guide

## Common Issues and Solutions

---

## 1. EPIC Number Not Found

### Symptoms
- User enters valid EPIC number
- System returns "EPIC Number not found"
- Error in API response

### Possible Causes
1. Wrong database configured
2. Empty voter tables
3. Cache issues
4. Database connection failed
5. Table name pattern mismatch

### Solutions

**Check Database Connection:**
```bash
php artisan tinker
DB::connection('voters')->getPdo();
```

**Verify Database Name:**
```bash
# Check .env file
cat .env | grep DB_DATABASE
# Should be: hkqbnymdjz
```

**Check Table Count:**
```bash
php artisan tinker
$tables = DB::connection('voters')->select('SHOW TABLES');
echo count($tables); // Should be 251
```

**Clear Cache:**
```bash
php artisan cache:clear
Cache::forget('voter:assembly_tables');
```

**Test EPIC Lookup:**
```bash
php artisan tinker
App\Helpers\VoterHelper::findByEpicNo('IJB0768549');
```

---

## 2. OTP Not Received

### Symptoms
- User doesn't receive OTP call
- "OTP sent" message appears but no call
- Timeout errors

### Possible Causes
1. 2Factor.in API key invalid
2. Insufficient API credits
3. Mobile number format incorrect
4. Rate limiting active
5. Network issues

### Solutions

**Verify API Key:**
```bash
# Check .env file
cat .env | grep TWO_FACTOR_API_KEY
```

**Test API Connection:**
```bash
php artisan tinker
$service = new App\Services\TwoFactorOtpService();
$result = $service->sendOtp('9876543210');
print_r($result);
```

**Check Rate Limiting:**
```bash
# Clear rate limit cache
php artisan cache:clear
```

**Verify Mobile Format:**
- Must be 10 digits
- Must start with 6, 7, 8, or 9
- No country code (+91)

**Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "2Factor"
```

---

## 3. Photo Upload Failed

### Symptoms
- "Upload failed" error
- "No face detected" message
- Cloudinary errors

### Possible Causes
1. Cloudinary credentials invalid
2. File size too large (>5MB)
3. Invalid file format
4. No face in photo
5. Network timeout

### Solutions

**Verify Cloudinary Config:**
```bash
# Check .env file
cat .env | grep CLOUDINARY_URL
```

**Test Cloudinary Connection:**
```bash
php artisan tinker
$cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.url'));
print_r($cloudinary);
```

**Check File Requirements:**
- Format: JPG or PNG
- Size: Max 5MB
- Must contain clear face
- Good lighting

**Clear Config Cache:**
```bash
php artisan config:clear
```

---

## 4. Admin Panel Not Accessible

### Symptoms
- Cannot login to admin panel
- "Invalid credentials" error
- 404 error on /admin/login

### Possible Causes
1. Wrong username/password
2. Password hash incorrect
3. Routes not cached
4. Middleware issue

### Solutions

**Verify Credentials:**
```bash
# Check .env file
cat .env | grep ADMIN_
# Username: admin
# Password: admin
```

**Test Password Hash:**
```bash
php artisan tinker
$hash = config('services.admin.password_hash');
password_verify('admin', $hash); // Should return true
```

**Clear Route Cache:**
```bash
php artisan route:clear
php artisan route:cache
```

**Check Routes:**
```bash
php artisan route:list | grep admin
```

---

## 5. MongoDB Connection Failed

### Symptoms
- "MongoDB connection error"
- Cannot save member data
- Timeout errors

### Possible Causes
1. MongoDB URL invalid
2. Network connectivity
3. MongoDB Atlas IP whitelist
4. Credentials expired

### Solutions

**Verify MongoDB URL:**
```bash
# Check .env file
cat .env | grep MONGO_URL
```

**Test Connection:**
```bash
php artisan tinker
$mongo = new App\Services\MongoService();
// Should not throw error
```

**Check MongoDB Atlas:**
1. Login to MongoDB Atlas
2. Check cluster status
3. Verify IP whitelist (0.0.0.0/0 for all IPs)
4. Check user permissions

**Update Config:**
```bash
php artisan config:clear
```

---

## 6. QR Code Shows Localhost

### Symptoms
- QR code points to localhost
- Card URL shows http://localhost:8000
- Links don't work in production

### Possible Causes
1. APP_URL not updated
2. BASE_URL not updated
3. Config cached with old values

### Solutions

**Update .env:**
```env
APP_URL=https://vanigan.digital
BASE_URL=https://vanigan.digital
```

**Clear Config:**
```bash
php artisan config:clear
php artisan config:cache
```

**Verify:**
```bash
php artisan tinker
echo config('app.url'); // Should show https://vanigan.digital
```

---

## 7. 500 Internal Server Error

### Symptoms
- White page with "500 Internal Server Error"
- No specific error message
- All pages affected

### Possible Causes
1. PHP error
2. Missing dependencies
3. Permission issues
4. Config error

### Solutions

**Check Laravel Logs:**
```bash
tail -100 storage/logs/laravel.log
```

**Check PHP Error Log:**
```bash
tail -100 /var/log/apache2/error.log
# or
tail -100 /var/log/nginx/error.log
```

**Fix Permissions:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Reinstall Dependencies:**
```bash
composer install --no-dev --optimize-autoloader
```

**Clear All Caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 8. Slow Performance

### Symptoms
- Pages load slowly
- API responses delayed
- Database queries timeout

### Possible Causes
1. No caching enabled
2. Database not optimized
3. Too many queries
4. Server resources low

### Solutions

**Enable Caching:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Check Database:**
```bash
php artisan tinker
DB::connection()->enableQueryLog();
// Run your query
DB::connection()->getQueryLog();
```

**Optimize Database:**
```sql
-- Add indexes if missing
ALTER TABLE tbl_voters_alandur_28 ADD INDEX idx_epic (EPIC_NO);
```

**Monitor Resources:**
```bash
top
free -m
df -h
```

---

## 9. Config Cache Issues

### Symptoms
- Changes to .env not taking effect
- Old values still being used
- env() returns null

### Possible Causes
1. Config is cached
2. Using env() in code instead of config()

### Solutions

**Clear Config Cache:**
```bash
php artisan config:clear
```

**Never Use env() in Code:**
```php
// ❌ Wrong
$url = env('CLOUDINARY_URL');

// ✅ Correct
$url = config('cloudinary.url');
```

**Rebuild Cache:**
```bash
php artisan config:cache
```

---

## 10. Redis Connection Error

### Symptoms
- "Error while reading line from the server"
- Redis connection timeout
- Cache errors

### Possible Causes
1. Redis not accessible
2. Wrong credentials
3. Network issues

### Solutions

**Switch to File Cache:**
```env
# In .env
CACHE_STORE=file
CACHE_DRIVER=file
SESSION_DRIVER=file
```

**Update Config Default:**
```php
// In config/cache.php
'default' => env('CACHE_STORE', 'file'),
```

**Clear Cache:**
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Diagnostic Commands

### Check System Status

```bash
# Check PHP version
php -v

# Check Laravel version
php artisan --version

# Check installed extensions
php -m

# Check database connection
php artisan tinker
DB::connection()->getPdo();

# Check MongoDB connection
php artisan tinker
$mongo = new App\Services\MongoService();

# List all routes
php artisan route:list

# Check config values
php artisan tinker
config('app.url');
config('database.connections.voters.database');
```

### View Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Clear Everything

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled files
php artisan clear-compiled

# Rebuild autoloader
composer dump-autoload
```

---

## Error Messages Reference

| Error | Meaning | Solution |
|-------|---------|----------|
| "EPIC Number not found" | EPIC not in database | Check database connection |
| "Invalid OTP" | Wrong OTP entered | Request new OTP |
| "OTP session expired" | OTP timeout (10 min) | Request new OTP |
| "Too many OTP requests" | Rate limited | Wait 5 minutes |
| "No face detected" | Photo has no face | Upload clear photo |
| "Invalid credentials" | Wrong admin password | Check credentials |
| "Database connection failed" | Cannot connect to DB | Check .env settings |
| "Cloudinary error" | Image upload failed | Check Cloudinary config |
| "MongoDB connection error" | Cannot connect to MongoDB | Check MongoDB URL |

---

## Getting Help

### Before Asking for Help

1. Check this troubleshooting guide
2. Review Laravel logs
3. Test with diagnostic commands
4. Try clearing all caches
5. Verify .env configuration

### Information to Provide

When reporting an issue, include:
- Error message (exact text)
- Laravel log excerpt
- Steps to reproduce
- Environment (production/local)
- Recent changes made
- Browser/device (if frontend issue)

### Log Files to Check

1. `storage/logs/laravel.log` - Application logs
2. `/var/log/apache2/error.log` - Apache errors
3. `/var/log/nginx/error.log` - Nginx errors
4. `/var/log/php8.2-fpm.log` - PHP-FPM errors

---

## Prevention Tips

1. **Always clear cache after .env changes**
2. **Use config() instead of env() in code**
3. **Monitor logs regularly**
4. **Keep backups updated**
5. **Test in staging before production**
6. **Document all changes**
7. **Use version control (Git)**

---

**Last Updated:** March 19, 2026
