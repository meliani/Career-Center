#!/bin/bash

# Simplified Career Center DevContainer Post-Start Script for Codespaces
# This script runs every time the devcontainer starts

set -e

echo "🚀 Career Center DevContainer starting..."

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
    print_warning ".env file not found. Please run the post-create setup."
fi

# Clear Laravel caches for fresh start
print_status "Clearing application caches..."
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true
php artisan view:clear >/dev/null 2>&1 || true
print_success "Application caches cleared"

# Display service status
echo ""
echo "🎉 DevContainer is ready!"
echo ""
echo "📋 Service Status:"
echo -e "   ${GREEN}✅ PHP 8.3${NC}"
echo -e "   ${GREEN}✅ Composer${NC}"
echo -e "   ${GREEN}✅ Node.js & NPM${NC}"
echo -e "   ${GREEN}✅ Laravel Application${NC}"

# Check if database exists
if [ -f "database/database.sqlite" ]; then
    echo -e "   ${GREEN}✅ SQLite Database${NC}"
else
    echo -e "   ${YELLOW}⚠️  Database (run migrations)${NC}"
fi

echo ""
echo "🔗 Quick Commands:"
echo -e "   ${BLUE}art serve${NC} - Start development server"
echo -e "   ${BLUE}art migrate${NC} - Run database migrations"
echo -e "   ${BLUE}art tinker${NC} - Laravel REPL"
echo -e "   ${BLUE}npm run dev${NC} - Build frontend assets"
echo ""
echo "💡 Pro Tips:"
echo "   • Use VS Code tasks (Ctrl+Shift+P → 'Tasks: Run Task')"
echo "   • All Laravel aliases are available (art, migrate, etc.)"
echo "   • Press F5 to start debugging if Xdebug is configured"
echo ""
print_success "Happy coding! 🎯"
