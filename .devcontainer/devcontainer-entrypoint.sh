#!/bin/bash

# DevContainer entrypoint script
# This script ensures the container stays running for DevContainer usage

set -e

echo "🚀 Career Center DevContainer Starting..."

# Ensure proper permissions
sudo chown -R www-data:www-data /var/www/html 2>/dev/null || true

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm -D

# Start any additional services if needed
echo "DevContainer services started successfully!"

# Keep the container running
echo "DevContainer is ready for development 🎉"
echo "Use VS Code's integrated terminal or SSH to connect."

# Execute any additional commands passed to the container
if [ $# -gt 0 ]; then
    exec "$@"
else
    # Keep container alive for DevContainer
    tail -f /dev/null
fi
