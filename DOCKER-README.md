# Career Center - Complete Docker Setup

This document provides comprehensive instructions for dockerizing and running the Career Center application using Docker and Docker Compose.

## 🚀 Quick Start

### Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose v2.0+
- At least 4GB RAM available for containers

### Development Setup

1. **Clone and Navigate**
   ```bash
   git clone https://github.com/meliani/Career-Center.git
   cd Career-Center
   ```

2. **Run Setup Script**
   
   **Windows (PowerShell):**
   ```powershell
   .\docker-setup.ps1
   ```
   
   **Linux/Mac:**
   ```bash
   chmod +x docker-setup.sh
   ./docker-setup.sh
   ```

3. **Access Application**
   - Website: http://localhost
   - Database: localhost:3306
   - Redis: localhost:6379
   - MailHog (Dev): http://localhost:8025

## 📁 Docker Architecture

### Services Overview

| Service | Purpose | Port | Image |
|---------|---------|------|-------|
| `app` | Laravel PHP-FPM | 9000 | Custom PHP 8.3 |
| `nginx` | Web Server | 80/443 | nginx:alpine |
| `db` | MySQL Database | 3306 | mysql:8.0 |
| `redis` | Cache/Sessions | 6379 | redis:alpine |
| `queue` | Queue Worker | - | Custom PHP 8.3 |
| `scheduler` | Cron Jobs | - | Custom PHP 8.3 |
| `mailhog` | Email Testing (Dev) | 1025/8025 | mailhog/mailhog |

### Directory Structure
```
docker/
├── nginx/
│   ├── nginx.conf          # Main Nginx configuration
│   └── sites/
│       └── default.conf    # Site-specific configuration
├── php/
│   ├── php.ini            # PHP configuration
│   └── www.conf           # PHP-FPM pool configuration
├── mysql/
│   └── my.cnf             # MySQL configuration
└── supervisor/
    └── supervisord.conf   # Process management
```

## 🔧 Configuration Files

### Environment Files
- `.env.development` - Development environment
- `.env.docker` - Production environment
- `.env` - Active environment (copied from above)

### Docker Files
- `Dockerfile.new` - Multi-stage build (replaces old Dockerfile)
- `docker-compose.yml` - Production setup
- `docker-compose.dev.yml` - Development setup
- `.dockerignore` - Excludes unnecessary files from build

## 🛠 Common Commands

### Development
```bash
# Start development environment
docker-compose -f docker-compose.dev.yml up -d

# View logs
docker-compose logs -f app

# Enter app container
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Install new dependencies
docker-compose exec app composer install
docker-compose exec app npm install
```

### Production
```bash
# Start production environment
docker-compose up -d

# Scale queue workers
docker-compose up -d --scale queue=3

# Update application
docker-compose exec app composer install --no-dev --optimize-autoloader
docker-compose exec app php artisan optimize
```

### Maintenance
```bash
# Stop all services
docker-compose down

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Clean up unused containers/images
docker system prune -a

# Backup database
docker-compose exec db mysqldump -u root -p career_center > backup.sql

# View resource usage
docker stats
```

## 🔐 Security Features

### Application Security
- PHP-FPM running as www-data user
- Nginx security headers
- Rate limiting on sensitive endpoints
- Redis password protection
- File upload restrictions

### Container Security
- Non-root user execution
- Minimal Alpine-based images
- Regular security updates
- Isolated network

## ⚡ Performance Optimizations

### PHP Optimizations
- OPcache enabled with optimized settings
- Redis for sessions and caching
- Composer optimized autoloader
- Asset compilation and optimization

### Database Optimizations
- InnoDB buffer pool tuning
- Query caching configuration
- Slow query logging
- Connection pooling

### Web Server Optimizations
- Gzip compression
- Static asset caching
- FastCGI buffering
- Connection keep-alive

## 🔍 Monitoring & Debugging

### Logs
```bash
# Application logs
docker-compose logs app

# Nginx logs
docker-compose logs nginx

# Database logs
docker-compose logs db

# All logs
docker-compose logs
```

### Health Checks
```bash
# Check container status
docker-compose ps

# Check resource usage
docker stats

# Test database connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

## 🚀 Deployment

### Production Deployment
1. **Prepare Environment**
   ```bash
   cp .env.docker .env
   # Edit .env with production values
   ```

2. **Build and Deploy**
   ```bash
   docker-compose build
   docker-compose up -d
   ```

3. **Initialize Application**
   ```bash
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan storage:link
   docker-compose exec app php artisan optimize
   ```

### SSL/HTTPS Setup
1. Add SSL certificates to `docker/nginx/ssl/`
2. Update `docker/nginx/sites/default.conf` for SSL
3. Restart nginx: `docker-compose restart nginx`

## 🔧 Troubleshooting

### Common Issues

**Port Already in Use**
```bash
# Find process using port
netstat -tulpn | grep :80
# Or change port in docker-compose.yml
```

**Permission Issues**
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**Database Connection Issues**
```bash
# Check database is running
docker-compose ps db
# Check database logs
docker-compose logs db
```

**Memory Issues**
```bash
# Increase Docker memory limit in Docker Desktop
# Or optimize PHP memory_limit in docker/php/php.ini
```

### Performance Issues
- Check `docker stats` for resource usage
- Review MySQL slow query log
- Monitor Redis memory usage
- Check Nginx access logs for bottlenecks

## 📈 Scaling

### Horizontal Scaling
```bash
# Scale queue workers
docker-compose up -d --scale queue=5

# Scale with load balancer
# Use Docker Swarm or Kubernetes for multi-node scaling
```

### Database Scaling
- Implement read replicas
- Use database connection pooling
- Consider Redis Cluster for cache scaling

## 🔄 Updates & Maintenance

### Regular Maintenance
1. **Update Dependencies**
   ```bash
   docker-compose exec app composer update
   docker-compose exec app npm update
   ```

2. **Update Docker Images**
   ```bash
   docker-compose pull
   docker-compose up -d
   ```

3. **Database Maintenance**
   ```bash
   docker-compose exec app php artisan optimize:clear
   docker-compose exec db mysql -u root -p -e "OPTIMIZE TABLE career_center.*"
   ```

### Backup Strategy
- Database: Daily automated dumps
- Storage: Regular file system backups
- Configuration: Version control all config files

## 🆘 Support

For issues related to:
- **Docker setup**: Check this documentation
- **Laravel application**: Refer to main application docs
- **Performance**: Review monitoring section
- **Security**: Check security features section

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
