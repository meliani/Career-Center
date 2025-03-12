#!/bin/bash

# Variables
REMOTE_HOST="10.0.2.2"
REMOTE_DIR="/var/www/Career-Center"
REMOTE_BRANCH="main"

# Array of services to restart
QUEUE_SERVICES=("laravel-default-worker.service" "laravel-emails-queue-worker.service")

# Prompt for the username
read -p "Enter the remote username: " REMOTE_USER

# Use SSH with -t flag to force pseudo-terminal allocation for interactive prompts
ssh -t $REMOTE_USER@$REMOTE_HOST "
  set -e
  echo \"Navigating to $REMOTE_DIR\"
  cd $REMOTE_DIR

  # Check for uncommitted changes
  if [ -n \"\$(git status --porcelain)\" ]; then
    echo \"⚠️ Warning: There are uncommitted changes on the remote server.\"
    echo \"Choose an option:\"
    echo \"  1) Stash the changes (can be restored later)\"
    echo \"  2) Force reset (discard changes)\"
    echo \"  3) Abort deployment\"
    read -p \"Enter your choice (1-3): \" choice
    
    case \$choice in
      1)
        echo \"Stashing changes...\"
        git stash
        ;;
      2)
        echo \"Force resetting...\"
        git reset --hard
        ;;
      3)
        echo \"Aborting deployment.\"
        exit 1
        ;;
      *)
        echo \"Invalid choice. Aborting deployment.\"
        exit 1
        ;;
    esac
  fi

  echo \"Fetching latest changes...\"
  git fetch origin

  echo \"Updating to latest $REMOTE_BRANCH branch...\"
  git checkout $REMOTE_BRANCH
  git pull origin $REMOTE_BRANCH

  echo \"Building the project...\"
  npm run build

  echo \"Restarting services...\"
  for SERVICE in ${QUEUE_SERVICES[*]}; do
    echo \"Restarting \$SERVICE...\"
    sudo systemctl restart \$SERVICE
  done

  echo \"Deployment completed successfully.\"
"
