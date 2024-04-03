#!/bin/bash

# Create the file
touch /etc/systemd/system/laravel-worker.service

# Write the service configuration
cat <<EOF > /etc/systemd/system/laravel-worker.service
[Unit]
Description=Laravel Queue Worker
After=network.target
 
[Service]
User=www
Group=www
Restart=always
WorkingDirectory=/www/wwwroot/carrieres.inpt.ac.ma/backend
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --env=production
 
[Install]
WantedBy=multi-user.target
EOF

# Reload systemd daemon
systemctl daemon-reload

# Enable and start the service
systemctl enable laravel-worker.service
systemctl start laravel-worker.service
