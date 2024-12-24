#!/bin/bash

# Variables
REMOTE_DIR="/var/www/html/careers_backend"

# Check if the remote directory exists
if [ ! -d "$REMOTE_DIR" ]; then
  echo "Error: Directory $REMOTE_DIR does not exist!"
  exit 1
fi

# Navigate to the remote directory
cd "$REMOTE_DIR" || exit

# Allow push to the checked-out branch
git config receive.denyCurrentBranch ignore
if [ $? -ne 0 ]; then
  echo "Error: Failed to configure Git to allow push to the checked-out branch."
  exit 1
fi

echo "Push to the checked-out branch is now allowed."

# Reset the working directory to match the latest pushed state
git reset --hard
if [ $? -ne 0 ]; then
  echo "Error: Failed to reset the working directory."
  exit 1
fi

echo "Working directory reset to match the latest pushed state."

# Done
echo "Setup complete. You can now push to the checked-out branch."
