#!/bin/bash

# Variables
REMOTE_HOST="carrieres.inpt.ac.ma"
REMOTE_DIR="/var/www/html/careers_backend"
REMOTE_BRANCH="main"

# Array of services to restart
QUEUE_SERVICES=("laravel-default-worker.service" "laravel-emails-queue-worker.service") # Replace with actual service names

# Prompt for the username
read -p "Enter the remote username: " REMOTE_USER

# Push changes to the remote
git push ssh://$REMOTE_USER@$REMOTE_HOST$REMOTE_DIR $REMOTE_BRANCH
if [ $? -ne 0 ]; then
  echo "Error: Push failed!"
  exit 1
fi

# Deployment steps on the remote server
ssh $REMOTE_USER@$REMOTE_HOST <<EOF
  set -e  # Exit immediately if a command fails
  echo "Navigating to $REMOTE_DIR"
  cd $REMOTE_DIR

  echo "Resetting working directory..."
  git reset --hard

  echo "Building the project..."
  npm run build

  echo "Restarting services..."
  for SERVICE in "${QUEUE_SERVICES[@]}"; do
    echo "Restarting \$SERVICE..."
    sudo systemctl restart \$SERVICE
  done

  echo "Deployment completed successfully."
EOF
