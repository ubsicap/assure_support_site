#!/bin/bash

# Follows the steps outlined on: https://docs.docker.com/engine/install/ubuntu/
# Pre-install setup
sudo apt-get update -y 
sudo apt-get install -y ca-certificates curl gnupg lsb-release # Doesnt do anything

# Adding GPG key
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor --yes -o /etc/apt/keyrings/docker.gpg

# Set up repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Refresh & install Docker packages
sudo apt-get update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Allow non-root users to use Docker (requires logout/login)
sudo groupadd docker
sudo usermod -aG docker ubuntu

# Start the docker process on boot
sudo systemctl enable docker.service
sudo systemctl enable containerd.service

# Clone the development repo
mkdir -p /home/ubuntu
git clone https://github.com/ubsicap/assure_support_site /home/ubuntu/app
chown -R ubuntu /home/ubuntu/app