#!/bin/bash

# Variables
REMOTE_HOST="carrieres.inpt.ac.ma"
REMOTE_DIR="/var/www/html/careers_backend"
REMOTE_BRANCH="main"

# Prompt for the username
read -p "Enter the remote username: " REMOTE_USER

# Push changes to the remote
git push ssh://$REMOTE_USER@$REMOTE_HOST$REMOTE_DIR $REMOTE_BRANCH

if [ $? -ne 0 ]; then
  echo "Error: Push failed!"
  exit 1
fi

# Reset the working directory on the remote
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && git reset --hard"

if [ $? -ne 0 ]; then
  echo "Error: Remote reset failed!"
  exit 1
fi

echo "Push and reset completed successfully."
