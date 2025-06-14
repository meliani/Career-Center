# Career Center Docker - Quick Reference

## 🚀 Quick Commands

### Development
```powershell
# Start development environment
docker-compose -f docker-compose.dev.yml up -d

# Stop development environment
docker-compose -f docker-compose.dev.yml down

# View logs
docker-compose -f docker-compose.dev.yml logs -f

# Enter container
docker-compose -f docker-compose.dev.yml exec app bash
```

### Production
```powershell
# Start production environment
docker-compose up -d

# Stop production environment
docker-compose down

# View logs
docker-compose logs -f

# Enter container
docker-compose exec app bash
```

### Laravel Commands
```powershell
# Run migrations
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear

# Generate key
docker-compose exec app php artisan key:generate

# Storage link
docker-compose exec app php artisan storage:link

# Optimize for production
docker-compose exec app php artisan optimize
```

### Composer & NPM
```powershell
# Install PHP dependencies
docker-compose exec app composer install

# Install Node dependencies
docker-compose exec app npm install

# Build assets
docker-compose exec app npm run build
```

### Database
```powershell
# Access MySQL
docker-compose exec db mysql -u root -p

# Backup database
docker-compose exec db mysqldump -u root -p career_center > backup.sql

# Restore database
docker-compose exec -T db mysql -u root -p career_center < backup.sql
```

### Debugging
```powershell
# Check container status
docker-compose ps

# Check logs for specific service
docker-compose logs nginx
docker-compose logs app
docker-compose logs db
docker-compose logs redis

# Check resource usage
docker stats

# Remove all containers and volumes
docker-compose down -v
docker system prune -a
```

## 🌐 Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| Website | http://localhost | - |
| Database | localhost:3306 | career_user/dev_password |
| Redis | localhost:6379 | dev_redis_password |
| MailHog (Dev) | http://localhost:8025 | - |

## 📁 Important Files

| File | Purpose |
|------|---------|
| `docker-compose.yml` | Production setup |
| `docker-compose.dev.yml` | Development setup |
| `Dockerfile.new` | Application container |
| `.env.docker` | Production environment |
| `.env.development` | Development environment |
| `DOCKER-README.md` | Full documentation |
| `DEVELOPMENT-SETUP.md` | Development container guide |
| `PRODUCTION-DEPLOYMENT.md` | Production deployment guide |

## 🔧 Environment Variables

### Required Variables
```bash
APP_KEY=base64:your-32-character-secret-key
DB_PASSWORD=your-secure-password
DB_ROOT_PASSWORD=your-root-password
REDIS_PASSWORD=your-redis-password
```

### Optional but Recommended
```bash
MAIL_HOST=your-smtp-host
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

## 🚨 Troubleshooting

### Common Issues

**Port 80 already in use:**
```powershell
# Change port in docker-compose.yml
ports:
  - "8080:80"  # Use port 8080 instead
```

**Permission denied:**
```powershell
# Fix permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**Database connection failed:**
```powershell
# Check if database container is running
docker-compose ps db
# Check database logs
docker-compose logs db
```

**Out of memory:**
```powershell
# Increase Docker memory in Docker Desktop settings
# Or reduce PHP memory_limit in docker/php/php.ini
```

### Reset Everything
```powershell
# Complete reset (WARNING: This deletes all data!)
docker-compose down -v
docker system prune -a
docker volume prune
# Then run setup again
.\docker-setup.ps1
```
