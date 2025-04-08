#!/bin/bash

# Variables
REMOTE_HOST="carrieres.inpt.ac.ma"
REMOTE_DIR="/var/www/html/careers_backend"
LOCAL_DIR="/home/mo/code/www/active/careers_backend"
TEMP_DIR="/tmp/careers_dependencies"

# Prompt for the username
read -p "Enter the remote username: " REMOTE_USER

# Create temporary directory
mkdir -p $TEMP_DIR
if [ $? -ne 0 ]; then
  echo "Error: Failed to create temporary directory!"
  exit 1
fi

echo "Compressing local dependencies..."
# Compress vendor directory (Composer dependencies)
if [ -d "$LOCAL_DIR/vendor" ]; then
  tar -czf "$TEMP_DIR/vendor.tar.gz" -C "$LOCAL_DIR" vendor
  if [ $? -ne 0 ]; then
    echo "Error: Failed to compress vendor directory!"
    rm -rf $TEMP_DIR
    exit 1
  fi
  echo "Vendor directory compressed successfully."
else
  echo "Warning: Vendor directory not found locally. Skipping."
fi

# Compress node_modules directory (NPM packages)
if [ -d "$LOCAL_DIR/node_modules" ]; then
  tar -czf "$TEMP_DIR/node_modules.tar.gz" -C "$LOCAL_DIR" node_modules
  if [ $? -ne 0 ]; then
    echo "Error: Failed to compress node_modules directory!"
    rm -rf $TEMP_DIR
    exit 1
  fi
  echo "Node modules compressed successfully."
else
  echo "Warning: Node modules directory not found locally. Skipping."
fi

echo "Uploading dependencies to remote server..."
# Create the temporary directory on the remote server
ssh $REMOTE_USER@$REMOTE_HOST "mkdir -p /tmp/careers_dependencies"

# Upload the compressed files
if [ -f "$TEMP_DIR/vendor.tar.gz" ]; then
  scp "$TEMP_DIR/vendor.tar.gz" $REMOTE_USER@$REMOTE_HOST:/tmp/careers_dependencies/
  if [ $? -ne 0 ]; then
    echo "Error: Failed to upload vendor.tar.gz!"
    rm -rf $TEMP_DIR
    exit 1
  fi
  echo "Vendor dependencies uploaded successfully."
fi

if [ -f "$TEMP_DIR/node_modules.tar.gz" ]; then
  scp "$TEMP_DIR/node_modules.tar.gz" $REMOTE_USER@$REMOTE_HOST:/tmp/careers_dependencies/
  if [ $? -ne 0 ]; then
    echo "Error: Failed to upload node_modules.tar.gz!"
    rm -rf $TEMP_DIR
    exit 1
  fi
  echo "Node modules uploaded successfully."
fi

# Extract files on remote server and set proper permissions
echo "Extracting dependencies on the remote server..."
ssh $REMOTE_USER@$REMOTE_HOST <<EOF
  set -e  # Exit immediately if a command fails
  echo "Navigating to $REMOTE_DIR"
  cd $REMOTE_DIR

  if [ -f "/tmp/careers_dependencies/vendor.tar.gz" ]; then
    echo "Extracting Composer dependencies..."
    rm -rf vendor
    tar -xzf /tmp/careers_dependencies/vendor.tar.gz
    if [ \$? -ne 0 ]; then
      echo "Error: Failed to extract vendor.tar.gz!"
      exit 1
    fi
  fi

  if [ -f "/tmp/careers_dependencies/node_modules.tar.gz" ]; then
    echo "Extracting NPM packages..."
    rm -rf node_modules
    tar -xzf /tmp/careers_dependencies/node_modules.tar.gz
    if [ \$? -ne 0 ]; then
      echo "Error: Failed to extract node_modules.tar.gz!"
      exit 1
    fi
  fi

  # Clean up remote temp files
  rm -rf /tmp/careers_dependencies

  echo "Dependencies sync completed successfully."
EOF

# Check if SSH command was successful
if [ $? -ne 0 ]; then
  echo "Error: SSH connection or remote commands failed!"
  rm -rf $TEMP_DIR
  exit 1
fi

# Clean up local temp files
rm -rf $TEMP_DIR

echo "Dependencies sync process completed."
