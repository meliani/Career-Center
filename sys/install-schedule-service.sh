#!/bin/bash

# Create the file
touch /etc/systemd/system/laravel-schedule.service

# Write the service configuration

cat <<EOF > /etc/systemd/system/laravel-schedule.service
[Unit]
Description=Laravel Schedule Worker
After=network.target

[Service]
User=www
Group=www
Restart=always
WorkingDirectory=/www/wwwroot/carrieres.inpt.ac.ma/backend
ExecStart=/usr/bin/php artisan schedule:run
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=laravel-schedule

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd daemon
systemctl daemon-reload

# Enable and start the service
systemctl enable laravel-schedule.service

systemctl start laravel-schedule.service
# End install-schedule-service.sh

# Path: install-queue-service.sh
#!/bin/bash
