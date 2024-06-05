#!/bin/bash

# Create the file
touch /etc/systemd/system/laravel-default-worker.service

# Load .env file
set -o allexport
source .env
set +o allexport

# Write the service configuration
cat <<EOF > /etc/systemd/system/laravel-default-worker.service
[Unit]
Description=Laravel Default Queue Worker
After=network.target

[Service]
User=$WWW_USER
Group=$WWW_GROUP
Restart=always
WorkingDirectory=/var/www/html/careers_backend
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=2 --rest=2 --env=production

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd daemon
systemctl daemon-reload

# Enable and start the service
systemctl enable laravel-default-worker.service
systemctl start laravel-default-worker.service
