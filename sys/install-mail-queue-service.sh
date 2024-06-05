#!/bin/bash

# Create the file
touch /etc/systemd/system/laravel-email-queue-worker.service

# Load .env file
set -o allexport
source .env
set +o allexport

# Write the service configuration
cat <<EOF > /etc/systemd/system/laravel-email-queue-worker.service
[Unit]
Description=Laravel Email Queue Worker
After=network.target

[Service]
User=$WWW_USER
Group=$WWW_GROUP
Restart=always
WorkingDirectory=/var/www/html/careers_backend
ExecStart=/usr/bin/php artisan queue:work --queue=emails --sleep=3 --tries=3 --max-time=3600 --rest=3 --env=production

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd daemon
systemctl daemon-reload

# Enable and start the service
systemctl enable laravel-email-queue-worker.service
systemctl start laravel-email-queue-worker.service
