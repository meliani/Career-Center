#!/bin/bash

# Simplified Career Center DevContainer Post-Create Script for Codespaces
# This script runs after the devcontainer is created

set -e

echo "🚀 Setting up Career Center development environment..."

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

# Install additional PHP extensions
print_status "Verifying PHP extensions are available..."
php -m | grep -E "(gd|intl|zip)" || print_warning "Some required PHP extensions may not be loaded"
print_status "PHP extensions handled by devcontainer feature"

# Copy environment file if it doesn't exist
if [ ! -f ".env" ]; then
    if [ -f ".env.development" ]; then
        print_status "Copying environment file..."
        cp .env.development .env
        print_success "Environment file copied from .env.development"
    else
        print_status "Creating basic .env file..."
        cat > .env << 'EOF'
APP_NAME="Career Center"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
        print_success "Basic .env file created"
    fi
fi

# Install PHP dependencies
print_status "Installing PHP dependencies with Composer..."
# First try with platform requirements ignored, then reinstall properly
if composer install --no-progress --prefer-dist --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip; then
    print_success "PHP dependencies installed successfully (with platform requirements ignored)"
    # Try to reinstall without ignoring platform requirements
    print_status "Attempting to reinstall with proper platform requirements..."
    if composer install --no-progress --prefer-dist; then
        print_success "PHP dependencies reinstalled with proper platform requirements"
    else
        print_warning "Could not reinstall with platform requirements, but dependencies are available"
    fi
else
    print_warning "Some PHP dependencies may have failed to install"
fi

# Install Node.js dependencies
print_status "Installing Node.js dependencies..."
if npm install; then
    print_success "Node.js dependencies installed successfully"
else
    print_warning "Some Node.js dependencies may have failed to install"
fi

# Generate application key if needed
if [ -f "vendor/autoload.php" ]; then
    if ! grep -q "APP_KEY=base64:" .env; then
        print_status "Generating Laravel application key..."
        php artisan key:generate
        print_success "Application key generated"
    fi
else
    print_warning "Vendor autoload not found. Skipping Laravel key generation."
fi

# Create SQLite database if using SQLite
if grep -q "DB_CONNECTION=sqlite" .env; then
    print_status "Setting up SQLite database..."
    mkdir -p database
    touch database/database.sqlite
    print_success "SQLite database created"
fi

# Run database migrations
print_status "Running database migrations..."
if [ -f "vendor/autoload.php" ]; then
    if php artisan migrate --force; then
        print_success "Database migrations completed"
    else
        print_warning "Database migrations failed. You may need to configure your database first."
    fi
else
    print_warning "Vendor autoload not found. Skipping database migrations."
fi

# Create storage link
print_status "Creating storage symbolic link..."
if [ -f "vendor/autoload.php" ]; then
    if php artisan storage:link; then
        print_success "Storage link created"
    else
        print_warning "Storage link creation failed"
    fi
else
    print_warning "Vendor autoload not found. Skipping storage link creation."
fi

# Set up useful aliases
print_status "Setting up shell aliases..."
cat >> ~/.zshrc << 'EOF'

# Laravel Career Center Aliases
alias art='php artisan'
alias tinker='php artisan tinker'
alias migrate='php artisan migrate'
alias seed='php artisan db:seed'
alias fresh='php artisan migrate:fresh --seed'
alias serve='php artisan serve --host=0.0.0.0'
alias test='php artisan test'

# NPM aliases
alias nrd='npm run dev'
alias nrb='npm run build'
alias nrw='npm run watch'

# Git aliases
alias gs='git status'
alias ga='git add'
alias gc='git commit'
alias gp='git push'
alias gl='git pull'

EOF

print_success "Shell aliases configured"

# Build frontend assets
print_status "Building frontend assets..."
# Check if vendor directory exists (needed for Filament CSS)
if [ -d "vendor" ]; then
    # Ensure Filament resources are accessible for CSS imports
    if [ -d "vendor/filament/filament/resources" ]; then
        print_status "Filament vendor resources found, proceeding with build..."
        if npm run build; then
            print_success "Frontend assets built successfully"
        else
            print_warning "Frontend assets build failed. You may need to build them manually with 'npm run build'"
        fi
    else
        print_warning "Filament vendor resources not found. Installing Filament first..."
        if [ -f "vendor/autoload.php" ] && composer show filament/filament > /dev/null 2>&1; then
            npm run build && print_success "Frontend assets built successfully" || print_warning "Frontend build failed"
        else
            print_warning "Filament not properly installed. Skipping frontend build."
        fi
    fi
else
    print_warning "Vendor directory not found. Skipping frontend build. Run 'npm run build' manually after composer install."
fi

# Create .vscode directory and basic settings
print_status "Setting up VS Code workspace..."
mkdir -p .vscode

# Create basic launch.json
cat > .vscode/launch.json << 'EOF'
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/workspaces/Career-Center": "${workspaceFolder}"
            }
        }
    ]
}
EOF

# Create tasks.json
cat > .vscode/tasks.json << 'EOF'
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "Laravel: Serve",
            "type": "shell",
            "command": "php artisan serve --host=0.0.0.0",
            "group": "build",
            "presentation": {
                "echo": true,
                "reveal": "always",
                "focus": false,
                "panel": "new"
            },
            "isBackground": true
        },
        {
            "label": "Laravel: Migration",
            "type": "shell",
            "command": "php artisan migrate",
            "group": "build"
        },
        {
            "label": "Laravel: Run Tests",
            "type": "shell",
            "command": "php artisan test",
            "group": {
                "kind": "test",
                "isDefault": true
            }
        },
        {
            "label": "NPM: Build",
            "type": "shell",
            "command": "npm run build",
            "group": "build"
        }
    ]
}
EOF

print_success "VS Code workspace configured"

# Display final status
echo ""
echo "🎉 DevContainer setup completed successfully!"
echo ""
echo "📋 What's available:"
echo -e "   ${GREEN}🌐 Laravel Application:${NC} Run 'php artisan serve' then visit the forwarded port"
echo -e "   ${GREEN}📁 SQLite Database:${NC} database/database.sqlite"
echo -e "   ${GREEN}🎯 VS Code Tasks:${NC} Press Ctrl+Shift+P -> 'Tasks: Run Task'"
echo ""
echo "🔧 Useful commands:"
echo -e "   ${BLUE}art serve${NC} - Start Laravel development server"
echo -e "   ${BLUE}art migrate${NC} - Run database migrations"
echo -e "   ${BLUE}art tinker${NC} - Open Laravel REPL"
echo -e "   ${BLUE}npm run dev${NC} - Build assets for development"
echo -e "   ${BLUE}art test${NC} - Run tests"
echo ""
echo "🎯 Next steps:"
if [ ! -f "vendor/autoload.php" ]; then
    echo "   1. Run 'composer install --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip' to install PHP dependencies"
    echo "   2. Run 'php artisan key:generate' to generate application key"
    echo "   3. Run 'php artisan migrate' to set up database"
    echo "   4. Run 'php artisan serve --host=0.0.0.0' to start the server"
    echo "   5. Run 'npm run build' to build frontend assets"
else
    echo "   1. Run 'php artisan serve --host=0.0.0.0' to start the server"
    echo "   2. Open the forwarded port to see your application"
fi
echo "   Happy coding!"
echo ""
print_success "Career Center DevContainer is ready! 🚀"
