# Career Center - Development Container Setup

This guide walks you through setting up a development environment using Docker containers for the Career Center Laravel application.

## 🚀 Quick Start for Development

### Prerequisites

Before you begin, ensure you have:

- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
- **Docker Compose** v2.0 or higher
- **Git** for version control
- At least **4GB RAM** available for containers
- **8GB free disk space** for images and volumes

### Step 1: Clone and Setup

```bash
# Clone the repository
git clone https://github.com/meliani/Career-Center.git
cd Career-Center

# Make setup script executable (Linux/Mac)
chmod +x docker-setup.sh

# Or use PowerShell on Windows
# No chmod needed for .ps1 files
```

### Step 2: Run Development Setup

**Windows (PowerShell):**
```powershell
# Run the interactive setup script
.\docker-setup.ps1

# When prompted, select option 1 for Development
```

**Linux/Mac (Bash):**
```bash
# Run the interactive setup script
./docker-setup.sh

# When prompted, select option 1 for Development
```

**Manual Setup (if script fails):**
```bash
# Copy development environment
cp .env.development .env

# Generate application key
docker run --rm -v ${PWD}:/app composer:latest composer install --working-dir=/app
docker run --rm -v ${PWD}:/app php:8.3-cli php /app/artisan key:generate --working-dir=/app

# Start development containers
docker-compose -f docker-compose.dev.yml up -d
```

### Step 3: Initialize Application

```bash
# Wait for containers to start (about 30 seconds)
docker-compose -f docker-compose.dev.yml ps

# Install PHP dependencies
docker-compose -f docker-compose.dev.yml exec app composer install

# Install Node.js dependencies
docker-compose -f docker-compose.dev.yml exec app npm install

# Run database migrations
docker-compose -f docker-compose.dev.yml exec app php artisan migrate

# Create storage symlink
docker-compose -f docker-compose.dev.yml exec app php artisan storage:link

# Build frontend assets
docker-compose -f docker-compose.dev.yml exec app npm run dev
```

### Step 4: Access Your Development Environment

| Service | URL | Purpose |
|---------|-----|---------|
| **Main Application** | http://localhost | Your Laravel app |
| **Database** | localhost:3306 | MySQL database |
| **Redis** | localhost:6379 | Cache/sessions |
| **MailHog** | http://localhost:8025 | Email testing interface |

**Default Credentials:**
- Database: `career_user` / `dev_password`
- Redis: `dev_redis_password`

## 🛠 Development Workflow

### Daily Development Commands

```bash
# Start development environment
docker-compose -f docker-compose.dev.yml up -d

# View logs (all services)
docker-compose -f docker-compose.dev.yml logs -f

# View logs (specific service)
docker-compose -f docker-compose.dev.yml logs -f app

# Enter the application container
docker-compose -f docker-compose.dev.yml exec app bash

# Stop development environment
docker-compose -f docker-compose.dev.yml down
```

### Laravel Development Commands

```bash
# Run Artisan commands
docker-compose -f docker-compose.dev.yml exec app php artisan migrate
docker-compose -f docker-compose.dev.yml exec app php artisan make:controller TestController
docker-compose -f docker-compose.dev.yml exec app php artisan route:list

# Clear caches during development
docker-compose -f docker-compose.dev.yml exec app php artisan cache:clear
docker-compose -f docker-compose.dev.yml exec app php artisan config:clear
docker-compose -f docker-compose.dev.yml exec app php artisan view:clear

# Run tests
docker-compose -f docker-compose.dev.yml exec app php artisan test

# Queue management
docker-compose -f docker-compose.dev.yml exec app php artisan queue:work
docker-compose -f docker-compose.dev.yml exec app php artisan queue:failed
```

### Frontend Development

```bash
# Install new NPM packages
docker-compose -f docker-compose.dev.yml exec app npm install <package-name>

# Run development build (with file watching)
docker-compose -f docker-compose.dev.yml exec app npm run dev

# Run production build
docker-compose -f docker-compose.dev.yml exec app npm run build

# Run with hot module replacement
docker-compose -f docker-compose.dev.yml exec app npm run hot
```

### Database Management

```bash
# Access MySQL directly
docker-compose -f docker-compose.dev.yml exec db mysql -u career_user -p career_center_dev

# Run fresh migrations with seeding
docker-compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed

# Create database backup
docker-compose -f docker-compose.dev.yml exec db mysqldump -u career_user -p career_center_dev > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database from backup
docker-compose -f docker-compose.dev.yml exec -T db mysql -u career_user -p career_center_dev < backup_file.sql
```

## 🔧 Development Configuration

### Environment Variables

The development environment uses `.env.development` with these key settings:

```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=career_center_dev
DB_USERNAME=career_user
DB_PASSWORD=dev_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

# Mail (uses MailHog)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Volume Mounts

Development containers use bind mounts for live code reloading:

```yaml
volumes:
  - .:/var/www/html              # Full project directory
  - ./storage:/var/www/html/storage
  - ./bootstrap/cache:/var/www/html/bootstrap/cache
```

### Hot Reloading

The development setup supports:
- **PHP code changes**: Reflected immediately (no restart needed)
- **Blade templates**: Auto-refreshed
- **Asset changes**: Use `npm run dev` with file watching
- **Environment changes**: Requires container restart

## 🐛 Debugging

### Xdebug Setup

Xdebug is pre-installed in the development container:

1. **VS Code Setup:**
   ```json
   // .vscode/launch.json
   {
     "version": "0.2.0",
     "configurations": [
       {
         "name": "Listen for Xdebug",
         "type": "php",
         "request": "launch",
         "port": 9003,
         "pathMappings": {
           "/var/www/html": "${workspaceFolder}"
         }
       }
     ]
   }
   ```

2. **PHPStorm Setup:**
   - Go to Settings → PHP → Servers
   - Name: `career-center`
   - Host: `localhost`
   - Port: `80`
   - Path mappings: `/var/www/html` → `[your-project-path]`

### Log Monitoring

```bash
# Laravel logs
docker-compose -f docker-compose.dev.yml exec app tail -f storage/logs/laravel.log

# Nginx access logs
docker-compose -f docker-compose.dev.yml logs -f nginx

# PHP-FPM logs
docker-compose -f docker-compose.dev.yml logs -f app | grep php-fpm

# Database logs
docker-compose -f docker-compose.dev.yml logs -f db
```

## 🔄 Common Development Tasks

### Adding New Dependencies

```bash
# Add PHP package
docker-compose -f docker-compose.dev.yml exec app composer require vendor/package

# Add NPM package
docker-compose -f docker-compose.dev.yml exec app npm install package-name

# Add development dependency
docker-compose -f docker-compose.dev.yml exec app npm install --save-dev package-name
```

### Database Seeding

```bash
# Run specific seeder
docker-compose -f docker-compose.dev.yml exec app php artisan db:seed --class=UserSeeder

# Run all seeders
docker-compose -f docker-compose.dev.yml exec app php artisan db:seed

# Fresh migration with seeding
docker-compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed
```

### Testing

```bash
# Run all tests
docker-compose -f docker-compose.dev.yml exec app php artisan test

# Run specific test file
docker-compose -f docker-compose.dev.yml exec app php artisan test tests/Feature/ExampleTest.php

# Run with coverage
docker-compose -f docker-compose.dev.yml exec app php artisan test --coverage

# Run Pest tests (if using Pest)
docker-compose -f docker-compose.dev.yml exec app ./vendor/bin/pest
```

## 🚨 Troubleshooting

### Common Issues

**1. Port Already in Use**
```bash
# Check what's using port 80
netstat -ano | findstr :80     # Windows
lsof -i :80                    # Linux/Mac

# Use different port
# Edit docker-compose.dev.yml:
ports:
  - "8080:80"    # Use port 8080 instead
```

**2. Permission Issues**
```bash
# Fix file permissions
docker-compose -f docker-compose.dev.yml exec app chown -R www-data:www-data storage bootstrap/cache

# On Linux, you might need to match user IDs
docker-compose -f docker-compose.dev.yml exec app chown -R $(id -u):$(id -g) storage bootstrap/cache
```

**3. Database Connection Issues**
```bash
# Check database container status
docker-compose -f docker-compose.dev.yml ps db

# Restart database
docker-compose -f docker-compose.dev.yml restart db

# Check database logs
docker-compose -f docker-compose.dev.yml logs db
```

**4. NPM/Node Issues**
```bash
# Clear NPM cache
docker-compose -f docker-compose.dev.yml exec app npm cache clean --force

# Delete node_modules and reinstall
docker-compose -f docker-compose.dev.yml exec app rm -rf node_modules
docker-compose -f docker-compose.dev.yml exec app npm install
```

**5. Composer Issues**
```bash
# Clear Composer cache
docker-compose -f docker-compose.dev.yml exec app composer clear-cache

# Update autoloader
docker-compose -f docker-compose.dev.yml exec app composer dump-autoload
```

### Reset Development Environment

**Soft Reset (keeps data):**
```bash
docker-compose -f docker-compose.dev.yml down
docker-compose -f docker-compose.dev.yml up -d
```

**Hard Reset (loses all data):**
```bash
docker-compose -f docker-compose.dev.yml down -v
docker system prune -f
docker-compose -f docker-compose.dev.yml up -d --build
```

### Performance Optimization

**1. Increase Docker Resources:**
- Docker Desktop → Settings → Resources
- Increase CPU cores and memory allocation

**2. Use Docker Build Cache:**
```bash
# Build with cache
docker-compose -f docker-compose.dev.yml build

# Force rebuild without cache
docker-compose -f docker-compose.dev.yml build --no-cache
```

**3. Optimize Volume Mounts:**
- Use named volumes for `node_modules`
- Exclude unnecessary files in `.dockerignore`

## 📝 Development Best Practices

### 1. Environment Management
- Never commit `.env` files
- Use `.env.development` as template
- Document required environment variables

### 2. Database Management
- Use migrations for schema changes
- Create seeders for test data
- Regular backups during development

### 3. Code Quality
- Run tests before committing
- Use Laravel's built-in validation
- Follow PSR standards

### 4. Security in Development
- Use strong passwords even in development
- Don't expose unnecessary ports
- Regular security updates

## 🆘 Getting Help

**Check logs for errors:**
```bash
docker-compose -f docker-compose.dev.yml logs app
```

**Access container for debugging:**
```bash
docker-compose -f docker-compose.dev.yml exec app bash
```

**Reset everything if stuck:**
```bash
./docker-setup.sh  # Run setup script again
```

For more detailed information, see:
- `DOCKER-README.md` - Complete documentation
- `DOCKER-QUICK-REFERENCE.md` - Command reference
- Laravel documentation for framework-specific issues
