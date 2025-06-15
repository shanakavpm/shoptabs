#!/bin/bash

# This script will attempt to run your Dockerized PHP+Nginx+SSL container on an available port.
# It will use 443 if available, otherwise fallback to 8443.

IMAGE="purephp-nginx-ssl"
CONTAINER_NAME="purephp-nginx-ssl-auto"

# Build image (optional, safe to re-run)
echo "[+] Building Docker image..."
docker build -t $IMAGE . || { echo "[!] Docker build failed"; exit 1; }

# Check if port 443 is available
if sudo lsof -i :443 | grep LISTEN; then
  echo "[!] Port 443 is in use. Will use 8443 instead."
  PORT=8443
else
  echo "[+] Port 443 is available. Using standard HTTPS."
  PORT=443
fi

# Stop previous container if running
docker rm -f $CONTAINER_NAME 2>/dev/null

# Run container
echo "[+] Running Docker container on port $PORT..."
docker run --add-host=host.docker.internal:host-gateway --name $CONTAINER_NAME -d -p $PORT:443 -v "$PWD":/var/www/html $IMAGE

if [ $? -eq 0 ]; then
  echo "[+] Container started."
  echo "Open: https://localhost${PORT:+:$PORT}/"
  echo "(Accept the browser's self-signed certificate warning.)"
else
  echo "[!] Failed to start Docker container."
  exit 2
fi
