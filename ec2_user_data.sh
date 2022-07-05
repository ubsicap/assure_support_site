#!/bin/bash

#===============================================================================
#
# Automatically installs runtime dependencies and downloads the GitHub repo
# containing the Support Site code.
#
# This script is to placed in the User Data of the AWS EC2 instance BEFORE the
# instance is launched for the first time. For best results, include it in a
# launch template.
#
# Steps:
#   1. Click `Launch Instance`
#   2. Scroll down and expand `Advanced details`
#   3. Paste the contents of this script into the `User Data` field
#   4. That's it! Launch the instance.
#===============================================================================



# Primary user of the system
user='ubuntu'
# Location to host/deploy the web server
workdir="/home/$user/app"
# Link to the GitHub repository to download
repo='https://github.com/ubsicap/assure_support_site'



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
sudo usermod -aG docker $user

# Start the docker process on boot
sudo systemctl enable docker.service
sudo systemctl enable containerd.service

# Install runtime dependencies and dev tools
sudo apt-get install -y certbot

# Clone the development repo
mkdir -p $workdir
git clone $repo $workdir
chown -R $user $workdir