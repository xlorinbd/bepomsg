# BepoMSG - Bulk SMS Marketing Platform

BepoMSG is a powerful, flexible, and user-friendly Bulk SMS Marketing Platform built with Laravel. It supports multiple payment gateways, SMS credit management, campaigns, and more.

## Prerequisites

Before deploying, ensure your server meets the following requirements:

- **PHP**: ^8.2 or ^8.3 (Recommended)
- **PHP Extensions**: BCMath, CType, CURL, DOM, Fileinfo, GD, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, Zip.
- **Database**: MySQL 5.7+ or MariaDB 10.3+.
- **Web Server**: Apache or Nginx.
- **Composer**: Latest version (v2.x).

## Deployment & Setup from Scratch

Follow these steps to set up the application on a fresh production server:

### 1. Upload & Environment
Upload all project files and create a `.env` file from `.env.example`:
```bash
cp .env.example .env
```
Update these crucial variables in `.env`:
- `APP_URL`: Your live site URL.
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Your MySQL credentials.
- `MAIL_*`: Your SMTP details for system notifications.
- `DB_PREFIX`: If you wish to use a table prefix (e.g., `cg_`).

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Application Initialization
```bash
php artisan key:generate
php artisan storage:link
```

### 4. Database Setup (Crucial)
Run migrations and seeders to set up the schema and default data:
```bash
php artisan migrate --force
php artisan db:seed --force
```
*Note: This will create default roles, permissions, countries, and initial configurations.*

### 5. File Permissions
Ensure the following directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Scheduled Tasks (Cron Job)
Add this to your server's crontab (`crontab -e`):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Production Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Special Note for cPanel Users
If you receive a `composer: command not found` error on your server:
1. Download `composer.phar` manually in your project folder:
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   ```
2. Now, use `php composer.phar` instead of just `composer` for all commands.
   Example: `php composer.phar install --optimize-autoloader --no-dev`

### 9. Git Setup & Version Control
To keep your live server updated with the latest code from GitHub:

**Initial Connection (SSH Recommended):**
1. Generate an SSH key on your server:
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   ```
2. Copy the public key (`cat ~/.ssh/id_rsa.pub`) and add it to your GitHub/GitLab repository's **Deploy Keys**.
3. Initialize and connect the project:
   ```bash
   git init
   git remote add origin git@github.com:xlorinbd/bepomsg.git
   git pull origin main
   ```

**Pulling Updates Correctly:**
Always ensure you don't overwrite local config or logs:
```bash
git pull origin main
```
If you get a "untracked files would be overwritten" error:
1. Delete or move the specific file mentioned (e.g., `rm composer.lock`).
2. Run `git pull origin main` again.

**After every pull, run:**
```bash
# Update dependencies if composer.json changed
php composer.phar install --optimize-autoloader --no-dev
# Update database if new migrations exist
php artisan migrate --force
# Clear old cache
php artisan optimize:clear
php artisan config:cache
```

---

## Default Login Credentials

After successful seeding, you can log in with:

### **Administrator Panel**
- **URL**: `yourdomain.com/admin`
- **Email**: `istiaksakif@gmail.com`
- **Password**: Check terminal output during `db:seed` (usually `12345678` in non-production, or a random string in production).

### **Customer Panel (Test Account)**
- **URL**: `yourdomain.com/login`
- **Email**: `customer@bepomsg.com`
- **Password**: `12345678`

## Features & Configuration
- **bKash Integration**: Enabled in seeders but status is `false` by default. Configure in Admin -> Payment Gateways.
- **SMS Pricing Tiers**: Default tiers (e.g., 0.40 BDT/SMS) are seeded automatically.
- **Primary Sending Server**: The system now supports a `is_primary` flag for easier server selection.

## Support
For technical support, visit [https://sakif.pro.bd](https://sakif.pro.bd)
