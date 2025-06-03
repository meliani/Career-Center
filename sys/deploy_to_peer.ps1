# PowerShell equivalent of deploy_to_peer.sh
# Variables
# $REMOTE_HOST = "192.168.1.6"
$REMOTE_DIR = "/var/www/html/careers_backend"
$REMOTE_BRANCH = "main"

# Array of services to restart
$QUEUE_SERVICES = @("laravel-default-worker.service", "laravel-emails-queue-worker.service") # Replace with actual service names

# Prompt for the username

$REMOTE_HOST = Read-Host -Prompt "Enter host"
$REMOTE_USER = Read-Host -Prompt "Enter the remote username"

# Push changes to the remote
Write-Host "Pushing changes to remote repository..."
$pushResult = git push "ssh://${REMOTE_USER}@${REMOTE_HOST}${REMOTE_DIR}" $REMOTE_BRANCH
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Push failed!" -ForegroundColor Red
    exit 1
}

# Deployment steps on the remote server
Write-Host "Connecting to remote server for deployment..."

# Create simplified bash commands
$service1 = $QUEUE_SERVICES[0]
$service2 = $QUEUE_SERVICES[1]

$remoteCommands = @"
set -e
echo 'Navigating to $REMOTE_DIR'
cd $REMOTE_DIR
echo 'Resetting working directory...'
git reset --hard
echo 'Building the project...'
npm run build
echo 'Restarting services...'
echo 'Restarting $service1'
sudo systemctl restart $service1
echo 'Restarting $service2'
sudo systemctl restart $service2
echo 'Deployment completed successfully.'
"@

# Execute the remote commands via SSH
# Write commands to a temporary file with Unix line endings
$tmpFile = [System.IO.Path]::GetTempFileName()
[System.IO.File]::WriteAllText($tmpFile, $remoteCommands.Replace("`r`n", "`n"))

# Use Get-Content to read the file and pipe to SSH
Get-Content -Raw $tmpFile | ssh "${REMOTE_USER}@${REMOTE_HOST}" "bash"

# Clean up temporary file
Remove-Item $tmpFile

Write-Host "Deployment process completed" -ForegroundColor Green
