# Career Center - Production Deployment Guide

This comprehensive guide covers deploying the Career Center Laravel application to production using Docker containers.

## 🎯 Deployment Overview

### Deployment Options

1. **Single Server Deployment** - Docker Compose on one server
2. **Container Orchestration** - Kubernetes or Docker Swarm
3. **Cloud Platforms** - AWS ECS, Google Cloud Run, Azure Container Instances
4. **VPS Deployment** - DigitalOcean, Linode, Vultr

This guide focuses on **Single Server Deployment** as it's the most common scenario.

## 🛠 Pre-Deployment Checklist

### Server Requirements

**Minimum Specifications:**
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

**Recommended Specifications:**
- **CPU**: 4 cores
- **RAM**: 8GB
- **Storage**: 50GB SSD
- **Network**: 1Gbps

### Server Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y curl wget git unzip

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker --version
docker-compose --version
```

## 🚀 Production Deployment Steps

### Step 1: Server Preparation

```bash
# Create application directory
sudo mkdir -p /opt/career-center
sudo chown $USER:$USER /opt/career-center
cd /opt/career-center

# Clone repository
git clone https://github.com/meliani/Career-Center.git .

# Set proper permissions
sudo chown -R $USER:$USER /opt/career-center
chmod +x docker-setup.sh
```

### Step 2: Environment Configuration

```bash
# Copy production environment template
cp .env.docker .env

# Edit environment variables
nano .env
```

**Critical Environment Variables to Configure:**

```bash
# Application
APP_NAME=Career-Center
APP_ENV=production
APP_KEY=base64:YOUR_32_CHAR_SECRET_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_DATABASE=career_center_prod
DB_USERNAME=career_user
DB_PASSWORD=YOUR_SECURE_DATABASE_PASSWORD
DB_ROOT_PASSWORD=YOUR_SECURE_ROOT_PASSWORD

# Redis
REDIS_PASSWORD=YOUR_SECURE_REDIS_PASSWORD

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com

# Google Services (if used)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

### Step 3: SSL Certificate Setup

**Option A: Let's Encrypt (Recommended)**

```bash
# Install Certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d your-domain.com

# Create SSL directory
mkdir -p docker/nginx/ssl

# Copy certificates
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem docker/nginx/ssl/
sudo chown $USER:$USER docker/nginx/ssl/*
```

**Option B: Custom SSL Certificate**

```bash
# Copy your SSL files
cp your-certificate.crt docker/nginx/ssl/certificate.crt
cp your-private-key.key docker/nginx/ssl/private.key
```

### Step 4: Build and Deploy

```bash
# Generate application key
docker run --rm -v ${PWD}:/app -w /app php:8.3-cli php artisan key:generate --show

# Add the generated key to .env file
echo "APP_KEY=base64:YOUR_GENERATED_KEY" >> .env

# Build production images
docker-compose build --no-cache

# Start production containers
docker-compose up -d

# Wait for containers to start
sleep 30

# Check container status
docker-compose ps
```

### Step 5: Application Initialization

```bash
# Install dependencies
docker-compose exec app composer install --no-dev --optimize-autoloader

# Run database migrations
docker-compose exec app php artisan migrate --force

# Create storage symlink
docker-compose exec app php artisan storage:link

# Build and optimize assets
docker-compose exec app npm ci --only=production
docker-compose exec app npm run build

# Optimize Laravel for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan event:cache

# Set proper permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## 🔧 Production Configuration

### Database Backup Strategy

**Create backup script:**

```bash
# Create backup directory
mkdir -p /opt/career-center/backups

# Create backup script
cat > /opt/career-center/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/opt/career-center/backups"
DB_NAME="career_center_prod"

# Create database backup
docker-compose exec -T db mysqldump -u career_user -p${DB_PASSWORD} ${DB_NAME} > ${BACKUP_DIR}/db_backup_${DATE}.sql

# Compress backup
gzip ${BACKUP_DIR}/db_backup_${DATE}.sql

# Keep only last 7 days of backups
find ${BACKUP_DIR} -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Backup completed: db_backup_${DATE}.sql.gz"
EOF

chmod +x /opt/career-center/backup.sh
```

**Setup automated backups:**

```bash
# Add to crontab
crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * /opt/career-center/backup.sh
```

## 🔍 Monitoring and Maintenance

### Health Checks

```bash
# Create health check script
cat > /opt/career-center/health-check.sh << 'EOF'
#!/bin/bash

# Check container status
echo "=== Container Status ==="
docker-compose ps

# Check application health
echo -e "\n=== Application Health ==="
curl -f http://localhost/health || echo "Health check failed"

# Check disk usage
echo -e "\n=== Disk Usage ==="
df -h

# Check memory usage
echo -e "\n=== Memory Usage ==="
free -h

# Check Docker stats
echo -e "\n=== Docker Stats ==="
docker stats --no-stream
EOF

chmod +x /opt/career-center/health-check.sh
```

### Log Management

```bash
# View application logs
docker-compose logs app

# View specific service logs
docker-compose logs nginx
docker-compose logs db
docker-compose logs redis

# Follow logs in real-time
docker-compose logs -f
```

## 🚨 Troubleshooting Production Issues

### Common Production Problems

**1. Application Not Accessible**

```bash
# Check container status
docker-compose ps

# Check nginx logs
docker-compose logs nginx

# Check if ports are open
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :443
```

**2. Database Connection Issues**

```bash
# Check database container
docker-compose logs db

# Test database connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Restart database
docker-compose restart db
```

**3. Performance Issues**

```bash
# Check system resources
htop
docker stats

# Check application performance
docker-compose exec app php artisan optimize
docker-compose exec app php artisan queue:restart

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
```

## 🔐 Security Hardening

### Server Security

```bash
# Update system regularly
sudo apt update && sudo apt upgrade -y

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Install fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Application Security

```bash
# Set secure file permissions
sudo chown -R root:root /opt/career-center
sudo chown -R $USER:$USER /opt/career-center/storage
sudo chmod -R 755 /opt/career-center
sudo chmod -R 775 /opt/career-center/storage

# Secure environment file
chmod 600 .env
```

## 🔄 Updates and Deployments

### Zero-Downtime Deployment

```bash
# Create deployment script
cat > /opt/career-center/deploy.sh << 'EOF'
#!/bin/bash
set -e

echo "Starting deployment..."

# Pull latest changes
git pull origin main

# Build new images
docker-compose build app

# Scale up with new version
docker-compose up -d --scale app=2

# Wait for health check
sleep 30

# Remove old containers
docker-compose up -d --scale app=1

# Run post-deployment tasks
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan optimize

echo "Deployment completed successfully!"
EOF

chmod +x /opt/career-center/deploy.sh
```

---

## 🎉 Deployment Complete!

Your Career Center application is now running in production. Remember to:

✅ Monitor application health regularly  
✅ Keep backups current  
✅ Update security patches  
✅ Monitor performance metrics  
✅ Test disaster recovery procedures  

For ongoing support, refer to:
- `DOCKER-README.md` - Complete documentation
- `DEVELOPMENT-SETUP.md` - Development guide
- Application logs for troubleshooting
