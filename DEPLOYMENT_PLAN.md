# Deployment Server Setup Plan

## Overview
This document outlines the steps required to deploy the MonitorBizz application with the newly implemented Compliance & Risk Management modules and React-based UI/UX.

## Prerequisites
1. Ubuntu 20.04 LTS or later server
2. Root or sudo access
3. Domain name (optional but recommended)
4. SSL certificate (Let's Encrypt or commercial)

## Server Requirements
- PHP 8.1 or later
- MySQL 8.0 or later (or MariaDB 10.6+)
- Node.js 16+ for frontend build
- Nginx or Apache web server
- Composer for PHP dependencies
- Git for version control

## Deployment Steps

### 1. Server Setup
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y

# Update package list
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-intl php8.1-soap php8.1-dev

# Install MySQL
sudo apt install -y mysql-server

# Install Nginx
sudo apt install -y nginx

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### 2. Database Setup
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE motorbizz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'motorbizz_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON motorbizz.* TO 'motorbizz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/alatralanahitrnahi/Motorbizzzzz___20255223252.git
sudo chown -R www-data:www-data Motorbizzzzz___20255223252

# Navigate to project directory
cd Motorbizzzzz___20255223252

# Install PHP dependencies
sudo -u www-data composer install --no-dev

# Install Node.js dependencies
sudo npm install

# Copy and configure environment file
sudo cp .env.example .env
sudo nano .env  # Update database credentials and other settings

# Generate application key
sudo php artisan key:generate

# Run database migrations
sudo php artisan migrate

# Build frontend assets
sudo npm run build

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 4. Web Server Configuration (Nginx)
Create `/etc/nginx/sites-available/motorbizz`:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/Motorbizzzzz___20255223252/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/motorbizz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5. SSL Configuration (Optional but Recommended)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com

# Set up auto-renewal
sudo crontab -e
# Add this line:
# 0 12 * * * /usr/bin/certbot renew --quiet
```

### 6. Process Management
```bash
# Install Supervisor for queue workers
sudo apt install -y supervisor

# Create supervisor configuration for Laravel queue worker
sudo nano /etc/supervisor/conf.d/motorbizz-worker.conf
```

```ini
[program:motorbizz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/Motorbizzzzz___20255223252/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/Motorbizzzzz___20255223252/storage/logs/worker.log
```

```bash
# Reload Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start motorbizz-worker:*
```

## Post-Deployment Steps
1. Create admin user:
   ```bash
   php artisan tinker
   ```
   ```php
   User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);
   ```

2. Run seeders if available:
   ```bash
   php artisan db:seed
   ```

3. Configure email settings in `.env` file

4. Set up cron job for Laravel scheduler:
   ```bash
   sudo crontab -e
   # Add this line:
   * * * * * cd /var/www/Motorbizzzzz___20255223252 && php artisan schedule:run >> /dev/null 2>&1
   ```

## Monitoring and Maintenance
1. Set up log rotation for Laravel logs
2. Configure monitoring for server resources
3. Regular database backups
4. Keep dependencies updated
5. Monitor application performance

## Troubleshooting
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Check web server logs: `/var/log/nginx/error.log`
3. Check PHP-FPM logs: `/var/log/php8.1-fpm.log`
4. Verify file permissions
5. Check database connection settings

## Backup Strategy
1. Daily database backups
2. Weekly full application backups
3. Store backups in secure, offsite location
4. Regularly test backup restoration process