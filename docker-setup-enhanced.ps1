# Career Center Docker Setup Script (Enhanced PowerShell Version)
# This script provides an enhanced setup experience for the Career Center application

param(
    [Parameter(HelpMessage="Environment to setup: 'dev' or 'prod'")]
    [ValidateSet("dev", "prod")]
    [string]$Environment
)

# Colors for output
function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    else {
        $input | Write-Output
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

Write-ColorOutput Cyan "🚀 Career Center Docker Setup for Windows"
Write-ColorOutput Cyan "========================================="
Write-Host ""

# Check if Docker is installed
try {
    $dockerVersion = docker --version
    Write-ColorOutput Green "✅ Docker found: $dockerVersion"
} catch {
    Write-ColorOutput Red "❌ Docker is not installed or not in PATH."
    Write-ColorOutput Yellow "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop"
    exit 1
}

# Check if Docker Compose is installed
try {
    $composeVersion = docker-compose --version
    Write-ColorOutput Green "✅ Docker Compose found: $composeVersion"
} catch {
    Write-ColorOutput Red "❌ Docker Compose is not installed or not in PATH."
    exit 1
}

# Check if we're in the correct directory
if (-not (Test-Path "composer.json")) {
    Write-ColorOutput Red "❌ This doesn't appear to be a Laravel project directory."
    Write-ColorOutput Yellow "Please run this script from the Career Center project root."
    exit 1
}

Write-Host ""

# Function to generate a random key
function New-RandomKey {
    $bytes = New-Object byte[] 32
    [Security.Cryptography.RNGCryptoServiceProvider]::Create().GetBytes($bytes)
    return [Convert]::ToBase64String($bytes)
}

# Ask for environment if not provided
if (-not $Environment) {
    Write-ColorOutput Yellow "Select environment:"
    Write-ColorOutput White "1) Development (dev) - Includes debugging tools, MailHog"
    Write-ColorOutput White "2) Production (prod) - Optimized for performance"
    $choice = Read-Host "Enter choice (1 or 2)"
    
    switch ($choice) {
        "1" { $Environment = "dev"; $EnvFile = ".env.development" }
        "2" { $Environment = "prod"; $EnvFile = ".env.docker" }
        default {
            Write-ColorOutput Red "❌ Invalid choice. Exiting."
            exit 1
        }
    }
} else {
    switch ($Environment) {
        "dev" { $EnvFile = ".env.development" }
        "prod" { $EnvFile = ".env.docker" }
    }
}

Write-ColorOutput Green "🔧 Setting up $Environment environment..."

# Check if environment file exists
if (-not (Test-Path $EnvFile)) {
    Write-ColorOutput Red "❌ Environment file $EnvFile not found."
    Write-ColorOutput Yellow "Please ensure all environment files are present."
    exit 1
}

# Copy environment file
if (-not (Test-Path ".env")) {
    Copy-Item $EnvFile ".env"
    Write-ColorOutput Green "✅ Environment file copied from $EnvFile"
} else {
    $overwrite = Read-Host "⚠️  .env file already exists. Overwrite? (y/N)"
    if ($overwrite -match "^[Yy]$") {
        Copy-Item $EnvFile ".env"
        Write-ColorOutput Green "✅ Environment file overwritten"
    }
}

# Generate APP_KEY if not set
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=base64:your-32-character-secret-key") {
    $appKey = New-RandomKey
    $envContent = $envContent -replace "APP_KEY=base64:your-32-character-secret-key", "APP_KEY=base64:$appKey"
    Set-Content ".env" $envContent -NoNewline
    Write-ColorOutput Green "✅ Generated new APP_KEY"
}

# Create necessary directories
Write-ColorOutput Cyan "📁 Creating necessary directories..."
$directories = @(
    "storage\logs",
    "storage\framework\sessions",
    "storage\framework\views", 
    "storage\framework\cache",
    "bootstrap\cache",
    "storage\app\public",
    "storage\keys"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

Write-ColorOutput Green "✅ Directories created"

# Choose compose file based on environment
$composeFile = if ($Environment -eq "dev") { "docker-compose.dev.yml" } else { "docker-compose.yml" }

# Build and start containers
Write-ColorOutput Cyan "🏗️  Building Docker containers..."
Write-ColorOutput Yellow "This may take several minutes on first run..."

try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile build
    } else {
        & docker-compose build
    }
    Write-ColorOutput Green "✅ Containers built successfully"
} catch {
    Write-ColorOutput Red "❌ Failed to build containers: $($_.Exception.Message)"
    exit 1
}

Write-ColorOutput Cyan "🚀 Starting containers..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile up -d
    } else {
        & docker-compose up -d
    }
    Write-ColorOutput Green "✅ Containers started successfully"
} catch {
    Write-ColorOutput Red "❌ Failed to start containers: $($_.Exception.Message)"
    exit 1
}

# Wait for database to be ready
Write-ColorOutput Cyan "⏳ Waiting for services to be ready..."
Write-ColorOutput Yellow "Waiting 30 seconds for database initialization..."
Start-Sleep -Seconds 30

# Check container status
Write-ColorOutput Cyan "📊 Checking container status..."
if ($Environment -eq "dev") {
    & docker-compose -f $composeFile ps
} else {
    & docker-compose ps
}

# Install dependencies and set up Laravel
Write-ColorOutput Cyan "📦 Installing PHP dependencies..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile exec app composer install --verbose
    } else {
        & docker-compose exec app composer install --no-dev --optimize-autoloader --verbose
    }
    Write-ColorOutput Green "✅ PHP dependencies installed"
} catch {
    Write-ColorOutput Red "❌ Failed to install PHP dependencies: $($_.Exception.Message)"
}

Write-ColorOutput Cyan "📦 Installing Node.js dependencies..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile exec app npm install
    } else {
        & docker-compose exec app npm ci --only=production
    }
    Write-ColorOutput Green "✅ Node.js dependencies installed"
} catch {
    Write-ColorOutput Red "❌ Failed to install Node.js dependencies: $($_.Exception.Message)"
}

Write-ColorOutput Cyan "🗄️  Running database migrations..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile exec app php artisan migrate --verbose
    } else {
        & docker-compose exec app php artisan migrate --force --verbose
    }
    Write-ColorOutput Green "✅ Database migrations completed"
} catch {
    Write-ColorOutput Red "❌ Failed to run migrations: $($_.Exception.Message)"
}

Write-ColorOutput Cyan "🔗 Creating storage link..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile exec app php artisan storage:link
    } else {
        & docker-compose exec app php artisan storage:link
    }
    Write-ColorOutput Green "✅ Storage link created"
} catch {
    Write-ColorOutput Red "❌ Failed to create storage link: $($_.Exception.Message)"
}

Write-ColorOutput Cyan "🎨 Building frontend assets..."
try {
    if ($Environment -eq "dev") {
        & docker-compose -f $composeFile exec app npm run dev
    } else {
        & docker-compose exec app npm run build
    }
    Write-ColorOutput Green "✅ Frontend assets built"
} catch {
    Write-ColorOutput Red "❌ Failed to build frontend assets: $($_.Exception.Message)"
}

if ($Environment -eq "prod") {
    Write-ColorOutput Cyan "⚡ Optimizing Laravel for production..."
    try {
        & docker-compose exec app php artisan config:cache
        & docker-compose exec app php artisan route:cache
        & docker-compose exec app php artisan view:cache
        & docker-compose exec app php artisan event:cache
        Write-ColorOutput Green "✅ Laravel optimized for production"
    } catch {
        Write-ColorOutput Red "❌ Failed to optimize Laravel: $($_.Exception.Message)"
    }
}

Write-Host ""
Write-ColorOutput Green "🎉 Setup complete!"
Write-Host ""
Write-ColorOutput Cyan "📋 Your Career Center application is now running:"

if ($Environment -eq "dev") {
    Write-ColorOutput White "   🌐 Main Application: http://localhost"
    Write-ColorOutput White "   🗄️  Database (MySQL): localhost:3306"
    Write-ColorOutput White "   🔴 Redis Cache: localhost:6379"
    Write-ColorOutput White "   📧 MailHog (Email Testing): http://localhost:8025"
    Write-Host ""
    Write-ColorOutput Cyan "🔑 Development Credentials:"
    Write-ColorOutput White "   Database User: career_user"
    Write-ColorOutput White "   Database Password: dev_password"
    Write-ColorOutput White "   Database Name: career_center_dev"
    Write-ColorOutput White "   Redis Password: dev_redis_password"
} else {
    Write-ColorOutput White "   🌐 Main Application: http://localhost"
    Write-ColorOutput White "   🗄️  Database (MySQL): localhost:3306"
    Write-ColorOutput White "   🔴 Redis Cache: localhost:6379"
    Write-Host ""
    Write-ColorOutput Yellow "⚠️  For production, update your domain and SSL certificates!"
}

Write-Host ""
Write-ColorOutput Cyan "🔧 Useful Docker commands:"
if ($Environment -eq "dev") {
    Write-ColorOutput White "   📊 View logs: docker-compose -f docker-compose.dev.yml logs -f"
    Write-ColorOutput White "   🐚 Access container: docker-compose -f docker-compose.dev.yml exec app bash"
    Write-ColorOutput White "   🛑 Stop services: docker-compose -f docker-compose.dev.yml down"
    Write-ColorOutput White "   🔄 Restart services: docker-compose -f docker-compose.dev.yml restart"
    Write-ColorOutput White "   🏗️  Rebuild: docker-compose -f docker-compose.dev.yml down && docker-compose -f docker-compose.dev.yml up --build -d"
} else {
    Write-ColorOutput White "   📊 View logs: docker-compose logs -f"
    Write-ColorOutput White "   🐚 Access container: docker-compose exec app bash"
    Write-ColorOutput White "   🛑 Stop services: docker-compose down"
    Write-ColorOutput White "   🔄 Restart services: docker-compose restart"
    Write-ColorOutput White "   🏗️  Rebuild: docker-compose down && docker-compose up --build -d"
}

Write-Host ""
Write-ColorOutput Cyan "📚 Documentation:"
Write-ColorOutput White "   📖 Full Docker Guide: DOCKER-README.md"
Write-ColorOutput White "   🔧 Development Setup: DEVELOPMENT-SETUP.md"
Write-ColorOutput White "   🚀 Production Deployment: PRODUCTION-DEPLOYMENT.md"
Write-ColorOutput White "   ⚡ Quick Reference: DOCKER-QUICK-REFERENCE.md"

Write-Host ""
Write-ColorOutput Green "🎉 Happy coding with Career Center!"

# Final health check
Write-ColorOutput Cyan "🏥 Running final health check..."
Start-Sleep -Seconds 5

try {
    $response = Invoke-WebRequest -Uri "http://localhost" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-ColorOutput Green "✅ Application is responding successfully!"
    } else {
        Write-ColorOutput Yellow "⚠️  Application returned status code: $($response.StatusCode)"
    }
} catch {
    Write-ColorOutput Yellow "⚠️  Could not verify application health. Please check manually at http://localhost"
}
