#!/bin/bash

# Career Center Docker Setup Script
# This script helps you set up the dockerized environment

set -e

echo "🚀 Setting up Career Center Docker Environment..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Function to generate a random key
generate_key() {
    echo $(openssl rand -base64 32)
}

# Ask for environment
echo "Select environment:"
echo "1) Development"
echo "2) Production"
read -p "Enter choice (1 or 2): " env_choice

case $env_choice in
    1)
        ENV_FILE=".env.development"
        echo "🔧 Setting up Development environment..."
        ;;
    2)
        ENV_FILE=".env.docker"
        echo "🔧 Setting up Production environment..."
        ;;
    *)
        echo "❌ Invalid choice. Exiting."
        exit 1
        ;;
esac

# Copy environment file
if [ ! -f .env ]; then
    cp $ENV_FILE .env
    echo "✅ Environment file copied from $ENV_FILE"
else
    read -p "⚠️  .env file already exists. Overwrite? (y/N): " overwrite
    if [[ $overwrite =~ ^[Yy]$ ]]; then
        cp $ENV_FILE .env
        echo "✅ Environment file overwritten"
    fi
fi

# Generate APP_KEY if not set
if grep -q "APP_KEY=base64:your-32-character-secret-key" .env; then
    APP_KEY=$(generate_key)
    sed -i.bak "s|APP_KEY=base64:your-32-character-secret-key|APP_KEY=base64:$APP_KEY|g" .env
    echo "✅ Generated new APP_KEY"
fi

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/keys

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Build and start containers
echo "🏗️  Building Docker containers..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 30

# Install dependencies and set up Laravel
echo "📦 Installing dependencies..."
docker-compose exec app composer install --no-dev --optimize-autoloader

echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

echo "⚡ Optimizing Laravel..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "✅ Setup complete!"
echo ""
echo "📋 Your application is now running:"
echo "   🌐 Web: http://localhost"
echo "   🗄️  Database: localhost:3306"
echo "   🔴 Redis: localhost:6379"
echo ""
echo "🔧 Useful commands:"
echo "   📊 View logs: docker-compose logs -f"
echo "   🛑 Stop: docker-compose down"
echo "   🔄 Restart: docker-compose restart"
echo "   🏗️  Rebuild: docker-compose down && docker-compose up --build -d"
echo ""
echo "🎉 Happy coding!"
