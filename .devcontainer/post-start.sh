#!/bin/bash

# Career Center DevContainer Post-Start Script
# This script runs every time the devcontainer starts

set -e

echo "🚀 Starting Career Center development services..."

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if Laravel is properly configured
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Copying from .env.development"
    cp .env.development .env
fi

# Check database connection
print_status "Checking database connection..."
max_attempts=10
attempt=1

while [ $attempt -le $max_attempts ]; do
    if php artisan migrate:status >/dev/null 2>&1; then
        print_success "Database connection is healthy"
        break
    fi
    
    if [ $attempt -eq $max_attempts ]; then
        print_warning "Database connection failed. Please check your database service."
    else
        echo "Attempt $attempt/$max_attempts - waiting for database..."
        sleep 2
    fi
    ((attempt++))
done

# Check Redis connection
print_status "Checking Redis connection..."
if php -r "
try {
    \$redis = new Redis();
    \$redis->connect('redis', 6379);
    if (getenv('REDIS_PASSWORD')) {
        \$redis->auth(getenv('REDIS_PASSWORD'));
    }
    \$redis->ping();
    echo 'Redis connection successful';
} catch (Exception \$e) {
    echo 'Redis connection failed: ' . \$e->getMessage();
    exit(1);
}
" >/dev/null 2>&1; then
    print_success "Redis connection is healthy"
else
    print_warning "Redis connection failed. Please check your Redis service."
fi

# Clear Laravel caches for fresh start
print_status "Clearing application caches..."
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true
php artisan view:clear >/dev/null 2>&1 || true
print_success "Application caches cleared"

# Display service status
echo ""
echo "🎉 DevContainer services are ready!"
echo ""
echo "📋 Service Status:"
echo -e "   ${GREEN}✅ Laravel Application${NC}"
echo -e "   ${GREEN}✅ PHP 8.3 with Extensions${NC}"
echo -e "   ${GREEN}✅ Composer Dependencies${NC}"
echo -e "   ${GREEN}✅ Node.js & NPM${NC}"

# Check database status
if php artisan migrate:status >/dev/null 2>&1; then
    echo -e "   ${GREEN}✅ MySQL Database${NC}"
else
    echo -e "   ${YELLOW}⚠️  MySQL Database (connection issues)${NC}"
fi

# Check Redis status
if php -r "
try {
    \$redis = new Redis();
    \$redis->connect('redis', 6379);
    if (getenv('REDIS_PASSWORD')) {
        \$redis->auth(getenv('REDIS_PASSWORD'));
    }
    \$redis->ping();
} catch (Exception \$e) {
    exit(1);
}
" >/dev/null 2>&1; then
    echo -e "   ${GREEN}✅ Redis Cache${NC}"
else
    echo -e "   ${YELLOW}⚠️  Redis Cache (connection issues)${NC}"
fi

echo ""
echo "🔗 Quick Links:"
echo -e "   ${BLUE}🌐 Application:${NC} http://localhost"
echo -e "   ${BLUE}📧 MailHog:${NC} http://localhost:8025"
echo ""
echo "💡 Pro Tips:"
echo "   • Use 'art' alias for Laravel Artisan commands"
echo "   • Press F5 to start debugging with Xdebug"
echo "   • Use Ctrl+Shift+P → 'Tasks: Run Task' for common operations"
echo "   • All Laravel, Git, and NPM aliases are available in terminal"
echo ""
print_success "Happy coding! 🎯"
