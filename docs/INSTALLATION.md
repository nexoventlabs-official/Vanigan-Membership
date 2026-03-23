# Installation Guide

## Prerequisites

Before installing, ensure you have:

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- MongoDB PHP Extension
- Web server (Apache/Nginx)
- SSL certificate (for production)

## Installation Steps

### 1. Clone/Upload Project

```bash
# If using Git
git clone <repository-url>
cd vanigan-membership

# Or upload files via SFTP to your server
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` with your settings:

```env
APP_NAME="Tamil Nadu Vanigargalin Sangamam"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=174.138.49.116
DB_PORT=3306
DB_DATABASE=hkqbnymdjz
DB_USERNAME=hkqbnymdjz
DB_PASSWORD=your_password

# MongoDB
MONGO_URL=mongodb+srv://user:password@cluster.mongodb.net/?appName=Cluster0
MONGO_DB_NAME=vanigan

# Cloudinary
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name

# 2Factor OTP
TWO_FACTOR_API_KEY=your_api_key

# Admin
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH="$2y$10$..."
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Set Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 6. Configure Web Server

#### Apache (.htaccess)

The project includes `.htaccess` files. Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Set document root to `/public` directory.

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7. Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### 8. Test Installation

Visit your domain and verify:
- Homepage loads
- Admin panel accessible
- EPIC lookup works

## Post-Installation

### Create Admin Password Hash

To create a new admin password hash:

```bash
php artisan tinker
```

Then run:

```php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

Copy the hash to `.env` as `ADMIN_PASSWORD_HASH`.

### Test Database Connections

```bash
php artisan tinker
```

Test MySQL:
```php
DB::connection('voters')->getPdo();
```

Test MongoDB:
```php
$mongo = new App\Services\MongoService();
```

## Cloudways-Specific Installation

### 1. Upload Files

Upload all files to `/public_html` directory via SFTP.

### 2. Set Document Root

In Cloudways panel:
- Go to Application Settings
- Set document root to: `/public_html/public`

### 3. Install Composer Dependencies

Via SSH (if enabled) or Cloudways terminal:

```bash
cd /public_html
composer install --optimize-autoloader --no-dev
```

### 4. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### 5. Configure Environment

Edit `.env` file via SFTP or file manager.

### 6. Clear Caches

Via Cloudways panel or SSH:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Verification Checklist

- [ ] Homepage loads without errors
- [ ] Admin panel accessible
- [ ] Database connections working
- [ ] EPIC lookup functional
- [ ] OTP service configured
- [ ] Cloudinary working
- [ ] MongoDB connected
- [ ] SSL certificate active
- [ ] Caches optimized

## Troubleshooting

### "500 Internal Server Error"

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

### "Permission Denied"

Fix permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### "Database Connection Failed"

Verify credentials in `.env` and test connection:
```bash
php artisan tinker
DB::connection()->getPdo();
```

### "Cloudinary Error"

Ensure `CLOUDINARY_URL` is set correctly in `.env`.

## Next Steps

After installation:
1. Read [Configuration Guide](CONFIGURATION.md)
2. Review [Security Guide](SECURITY.md)
3. Check [Admin Guide](ADMIN_GUIDE.md)
4. Test all features

---

**Need Help?** Check [Troubleshooting Guide](TROUBLESHOOTING.md)
