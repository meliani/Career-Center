# Career Center Docker Setup Script for Windows
# This script helps you set up the dockerized environment on Windows

Write-Host "🚀 Setting up Career Center Docker Environment..." -ForegroundColor Green

# Check if Docker is installed
try {
    docker --version | Out-Null
} catch {
    Write-Host "❌ Docker is not installed. Please install Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Check if Docker Compose is installed
try {
    docker-compose --version | Out-Null
} catch {
    Write-Host "❌ Docker Compose is not installed. Please install Docker Compose first." -ForegroundColor Red
    exit 1
}

# Function to generate a random key
function Generate-Key {
    return [Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))
}

# Ask for environment
Write-Host "Select environment:"
Write-Host "1) Development"
Write-Host "2) Production"
$envChoice = Read-Host "Enter choice (1 or 2)"

switch ($envChoice) {
    "1" {
        $envFile = ".env.development"
        Write-Host "🔧 Setting up Development environment..." -ForegroundColor Yellow
    }
    "2" {
        $envFile = ".env.docker"
        Write-Host "🔧 Setting up Production environment..." -ForegroundColor Yellow
    }
    default {
        Write-Host "❌ Invalid choice. Exiting." -ForegroundColor Red
        exit 1
    }
}

# Copy environment file
if (-not (Test-Path ".env")) {
    Copy-Item $envFile ".env"
    Write-Host "✅ Environment file copied from $envFile" -ForegroundColor Green
} else {
    $overwrite = Read-Host "⚠️  .env file already exists. Overwrite? (y/N)"
    if ($overwrite -match "^[Yy]$") {
        Copy-Item $envFile ".env"
        Write-Host "✅ Environment file overwritten" -ForegroundColor Green
    }
}

# Generate APP_KEY if not set
$content = Get-Content ".env" -Raw
if ($content -match "APP_KEY=base64:your-32-character-secret-key") {
    $appKey = Generate-Key
    $content = $content -replace "APP_KEY=base64:your-32-character-secret-key", "APP_KEY=base64:$appKey"
    Set-Content ".env" $content
    Write-Host "✅ Generated new APP_KEY" -ForegroundColor Green
}

# Create necessary directories
Write-Host "📁 Creating necessary directories..." -ForegroundColor Cyan
$directories = @(
    "storage/logs",
    "storage/framework/sessions",
    "storage/framework/views", 
    "storage/framework/cache",
    "bootstrap/cache",
    "storage/app/public",
    "storage/keys"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

# Build and start containers
Write-Host "🏗️  Building Docker containers..." -ForegroundColor Cyan
docker-compose build

Write-Host "🚀 Starting containers..." -ForegroundColor Green
docker-compose up -d

# Wait for database to be ready
Write-Host "⏳ Waiting for database to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Install dependencies and set up Laravel
Write-Host "📦 Installing dependencies..." -ForegroundColor Cyan
docker-compose exec app composer install --no-dev --optimize-autoloader

Write-Host "🗄️  Running database migrations..." -ForegroundColor Cyan
docker-compose exec app php artisan migrate --force

Write-Host "🔗 Creating storage link..." -ForegroundColor Cyan
docker-compose exec app php artisan storage:link

Write-Host "⚡ Optimizing Laravel..." -ForegroundColor Cyan
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

Write-Host ""
Write-Host "✅ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Your application is now running:" -ForegroundColor Cyan
Write-Host "   🌐 Web: http://localhost" -ForegroundColor White
Write-Host "   🗄️  Database: localhost:3306" -ForegroundColor White
Write-Host "   🔴 Redis: localhost:6379" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Useful commands:" -ForegroundColor Cyan
Write-Host "   📊 View logs: docker-compose logs -f" -ForegroundColor White
Write-Host "   🛑 Stop: docker-compose down" -ForegroundColor White
Write-Host "   🔄 Restart: docker-compose restart" -ForegroundColor White
Write-Host "   🏗️  Rebuild: docker-compose down; docker-compose up --build -d" -ForegroundColor White
Write-Host ""
Write-Host "🎉 Happy coding!" -ForegroundColor Green
