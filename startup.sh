#!/bin/sh



# Paths to app configuration files that will be modified during startup
CONFIG_PATH=''
COMPOSE_PATH=''

# Default values that will be replaced in the above files
USERNAME_TO_REPLACE='USERNAME_TO_REPLACE'
PASSWORD_TO_REPLACE='PASSWORD_TO_REPLACE'
DATABASE_TO_REPLACE='DATABASE_TO_REPLACE'
HOSTNAME_TO_REPLACE='HOSTNAME_TO_REPLACE'
ROOT_PASSWORD_TO_REPLACE='ROOT_PASSWORD_TO_REPLACE'

# Values to be read from the user
QA_MYSQL_HOSTNAME=''
QA_MYSQL_USERNAME=''
QA_MYSQL_PASSWORD=''
QA_MYSQL_DATABASE=''
QA_MYSQL_ROOT_PASSWORD=''

# SSL Certification information
# This information needs to be changed so that it is provided by the user/sysadmin
SSL_EMAIL='daniel_hammer@sil.org'
DOMAIN_NAME='supportsitetest.tk'
WEBROOT=$(realpath q2a_site)



#===============================================================================
#
# Installs any docker/git dependencies on this OS needed for the server to run.
#
# Currently only installs for debian-based distros, as VM is Ubuntu. 
#
#===============================================================================
install_dependencies() {
    echo
    echo 'Installing runtime dependencies...'

    # Only install if necessary
    dpkg -l | grep docker > /dev/null 2>&1
    if [ $? -eq 0 ];
    then
        echo 'Docker already detected on system. Skipping installation.'
    else
        # Follows the steps outlined on: https://docs.docker.com/engine/install/ubuntu/
        # Pre-install setup
        sudo apt-get update -y 
        sudo apt-get install -y ca-certificates curl gnupg lsb-release
        
        # Adding GPG key
        sudo mkdir -p /etc/apt/keyrings
        curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor --yes -o /etc/apt/keyrings/docker.gpg
        
        # Set up repository
        echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
        
        # Refresh & install Docker packages
        sudo apt-get update -y
        sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    fi

    # Add user to docker group if needed
    grep 'docker' /etc/group | grep $USER > /dev/null 2>&1
    if [ $? -eq 0 ];
    then
        echo "User $USER already has docker permissions."
    else
        # Allow non-root users to use Docker (requires logout/login)
        sudo groupadd docker
        sudo usermod -aG docker $USER
    fi
    
    # Start the docker process on boot
    sudo systemctl enable docker.service
    sudo systemctl enable containerd.service

    echo 'Docker installation complete'
}



#===============================================================================
#
# Perform any necessary post-install cleanup
#
#===============================================================================
cleanup() {
    # Remove package lists
    sudo rm -rf /var/lib/apt/lists/*

    # This script is copied to /var/lib/cloud/instance/user-data.txt upon
    # launching the instance, so it can safely be removed.
    if [ -s ./ec2_user_data.sh ];
    then
        echo "Deleting EC2 User Data script"
        rm ./ec2_user_data.sh
    fi
}



#===============================================================================
#
# Locates the configuration files for setting up the Q2A site and database.
#
# These files will get modified to hold the user-inputted credentials for the
# MySQL database.
#
# Global variables used:
#   COMPOSE_PATH
#   CONFIG_PATH
#
#===============================================================================
locate_config_files() {
    echo
    echo 'Locating configuration files...'

    CONFIG_PATH=$(realpath $(find . -type f -name "qa-config.php"))
    COMPOSE_PATH=$(realpath $(find . -type f -name "docker-compose.yml"))

    echo "Configuration files found:"
    echo "    $CONFIG_PATH"
    echo "    $COMPOSE_PATH"
}



#===============================================================================
#
# Checks if the credentials have already been set, prompting the user if not.
#
# Global variables used:
#   COMPOSE_PATH
#   CONFIG_PATH
#   USERNAME_TO_REPLACE
#   PASSWORD_TO_REPLACE
#   DATABASE_TO_REPLACE
#   HOSTNAME_TO_REPLACE
#   ROOT_PASSWORD_TO_REPLACE
#
#===============================================================================
check_credentials() {
    echo
    echo 'Checking if MySQL credentials have been set...'

    # Set the database root password, if needed
    if grep -q $ROOT_PASSWORD_TO_REPLACE $COMPOSE_PATH;
    then
        # Toggle echo off when reading the password
        stty -echo
        read -p "Set new password for MySQL root account > " QA_MYSQL_ROOT_PASSWORD
        stty echo
        echo
    else
        echo '    Root password already defined'
    fi

    # Set the username, if needed
    if grep -q $USERNAME_TO_REPLACE $CONFIG_PATH;
    then
        read -p "Set username for new MySQL user account > " QA_MYSQL_USERNAME
    else
        echo '    Username already defined'
    fi

    # Set the database password, if needed
    if grep -q $PASSWORD_TO_REPLACE $CONFIG_PATH;
    then
        stty -echo
        read -p "Set password for new MySQL user account > " QA_MYSQL_PASSWORD
        stty echo
        echo
    else
        echo '    Password already defined'
    fi

    # Set the database hostname, if needed
    if grep -q $HOSTNAME_TO_REPLACE $CONFIG_PATH;
    then
        echo -n
    else
        echo '    Hostname already defined'
    fi

    # Set the database name, if needed
    if grep -q $DATABASE_TO_REPLACE $CONFIG_PATH;
    then
        read -p "Set name for MySQL database > " QA_MYSQL_DATABASE
    else
        echo '    Database name already defined'
    fi

    # Lastly, fetch the name of the container that the MySQL database will run in
    QA_MYSQL_HOSTNAME=$(awk '/container_name:/' $COMPOSE_PATH | awk 'FNR == 2 {print $2}')
}



#===============================================================================
#
# Sets the MySQL credentials provided by the user.
#
# Global variables used:
#   COMPOSE_PATH
#   CONFIG_PATH
#   USERNAME_TO_REPLACE
#   PASSWORD_TO_REPLACE
#   DATABASE_TO_REPLACE
#   HOSTNAME_TO_REPLACE
#   ROOT_PASSWORD_TO_REPLACE
#
#===============================================================================
set_credentials() {
    echo
    echo 'Setting MySQL credentials...'

    # Set the root password
    sed -i "s/$ROOT_PASSWORD_TO_REPLACE/$QA_MYSQL_ROOT_PASSWORD/" $COMPOSE_PATH

    # Set the standard account's username
    sed -i "s/$USERNAME_TO_REPLACE/$QA_MYSQL_USERNAME/" $CONFIG_PATH
    sed -i "s/$USERNAME_TO_REPLACE/$QA_MYSQL_USERNAME/" $COMPOSE_PATH

    # Set the standard account's password
    sed -i "s/$PASSWORD_TO_REPLACE/$QA_MYSQL_PASSWORD/" $CONFIG_PATH
    sed -i "s/$PASSWORD_TO_REPLACE/$QA_MYSQL_PASSWORD/" $COMPOSE_PATH

    # Set the hostname of the database (name of the DB container)
    sed -i "s/$HOSTNAME_TO_REPLACE/$QA_MYSQL_HOSTNAME/" $CONFIG_PATH

    # Set the database's name
    sed -i "s/$DATABASE_TO_REPLACE/$QA_MYSQL_DATABASE/" $CONFIG_PATH
    sed -i "s/$DATABASE_TO_REPLACE/$QA_MYSQL_DATABASE/" $COMPOSE_PATH

    echo "MySQL credentials set"
}



#===============================================================================
#
# Sets up the auto-start script to automatically restart the docker containers
# whenever the EC2 instance is restarted.
#
# Note that this doesn't effect machines not hosted on AWS EC2.
#
# Global variables used:
#   COMPOSE_PATH
#
#===============================================================================
enable_autolaunch() {
    echo
    echo 'Setting containers to launch automatically on system start...'

    boot_path='/var/lib/cloud/scripts/per-boot'

    # Allow a script to be created
    sudo chmod -R 777 $boot_path

    # By default, just compose the containers,
    # But we *may* want to re-run the startup script, so I'm leaving this here
    #echo "#!/bin/sh
#sh $(realpath $0) > $boot_path

    sudo echo "#!/bin/sh
docker compose -f $COMPOSE_PATH up -d" > $boot_path/startserver.sh

    # Mark the script as executable for everyone
    sudo chmod -R 755 $boot_path

    echo 'Autostart policy set'
}



#===============================================================================
#
# Launches the web server through docker compose.
#
# This causes two containers to be spawned:
#   q2a-apache      Php Apache web server hosting the website
#   q2a-db          MySQL database to store website information
#
# Global variables used:
#   COMPOSE_PATH
#
#===============================================================================
launch_service() {
    echo
    echo 'Launching website service via docker...'

    docker compose -f $COMPOSE_PATH up -d

    echo 'Docker containers launched'
}



#===============================================================================
#
# Performs the SSL certification process on the web server.
#
# The certification process uses Certbot (certbot.eff.org)
#
# Global variables used:
#   SSL_EMAIL
#   DOMAIN_NAME
#   WEBROOT
#
#===============================================================================
generate_ssl() {
    echo
    echo 'Establishing SSL certification...'

    # Install certbot and packages, if necessary
    sudo apt-get install -y certbot

    #============================================
    #
    #             ***IMPORTANT***
    #
    # This line is for DEVELOPMENT ONLY. Notice
    # the `--dry-run` flag? Removing that will
    # run in production mode. Production mode is
    # RATE LIMITED! Don't use it unless you need
    # to!
    #
    #============================================
    #certbot certonly --dry-run --non-interactive --agree-tos -m daniel_hammer@sil.org -d supportsitetest.tk -d www.supportsitetest.tk --webroot -w ./q2a_site/
    sudo certbot certonly --dry-run \
        --non-interactive \
        --agree-tos \
        --expand \
        -m $SSL_EMAIL \
        --webroot -w $WEBROOT \
        -d $DOMAIN_NAME \
        -d www.$DOMAIN_NAME

    echo 'SSL certification complete'
}



#===============================================================================
#
# Copies SSL certification into the specificed container.
#
# General structure copied from https://blog.zotorn.de/phpmyadmin-docker-image-with-ssl-tls/
#
# Note that this modifies the `/etc/apache2/sites-available/000-default.conf` file.
#
# Parameters:
#   $1  The name of the container to copy SSL cert files into
#
# Global variables used:
#   DOMAIN_NAME
#   SSL_EMAIL
#
#===============================================================================
copy_ssl_to_container() {
    # Setup parameters
    container=$1

    echo
    echo "Copying all SSL certifications to $container service..."

    # Copy the SSL keys to the container
    host_ssl_path="/etc/letsencrypt/live/$DOMAIN_NAME/"
    sudo docker cp -L $host_ssl_path/cert.pem $container:/etc/ssl
    sudo docker cp -L $host_ssl_path/chain.pem $container:/etc/ssl
    sudo docker cp -L $host_ssl_path/fullchain.pem $container:/etc/ssl
    sudo docker cp -L $host_ssl_path/privkey.pem $container:/etc/ssl/private

    # Config files that will be modified
    ssl_conf_path='/etc/apache2/sites-available/000-default.conf'
    default_ssl_conf='/etc/apache2/sites-available/default-ssl.conf'

    # Enable SSL module
    docker exec $container a2enmod ssl

    # Before we do anything, MAKE A BACKUP
    docker exec $container cp $default_ssl_conf $default_ssl_conf.backup

    # Replace the necessary paths for the cert files
	docker exec $container sed -i 's,/etc/ssl/certs/ssl-cert-snakeoil.pem,/etc/ssl/cert.pem,g' $default_ssl_conf
	docker exec $container sed -i 's,/etc/ssl/private/ssl-cert-snakeoil.key,/etc/ssl/private/privkey.pem,g' $default_ssl_conf
    docker exec $container sed -i 's,#SSLCertificateChainFile,SSLCertificateChainFile,g' $default_ssl_conf
    docker exec $container sed -i 's,/etc/apache2/ssl.crt/server-ca.crt,/etc/ssl/fullchain.pem,g' $default_ssl_conf
    docker exec $container sed -i "s,.*ServerAdmin.*,ServerAdmin $SSL_EMAIL\nServerName $DOMAIN_NAME,g" $default_ssl_conf

    # Define paths for cert files
    docker exec $container sed -i -e '/^<\/VirtualHost>/i SSLCertificateFile /etc/ssl/cert.pem' $ssl_conf_path
    docker exec $container sed -i -e '/^<\/VirtualHost>/i SSLCertificateChainFile /etc/ssl/fullchain.pem' $ssl_conf_path
    docker exec $container sed -i -e '/^<\/VirtualHost>/i SSLCertificateKeyFile /etc/ssl/private/privkey.pem' $ssl_conf_path

    # Redirect all HTTP traffic to HTTPS
    docker exec $container sed -i -e "/^<\/VirtualHost>/i Redirect \"/\" \"https://$DOMAIN_NAME\"" $ssl_conf_path

    # Copy the file into the sites-enabled directory
    docker exec $container cp $default_ssl_conf /etc/apache2/sites-enabled/

    # Restart the necessary services
    docker exec $container apachectl restart

    echo "SSL certifications copied to $container service"
}



#===============================================================================
#
# Copies SSL certs to allow Portainer SSL access
#
# Ideas referenced from: https://docs.portainer.io/advanced/ssl
#
# Note that this creates `/local_certs` containing two files:
#   portainer.crt
#   portainer.key
#
# Global variables used:
#   DOMAIN_NAME
#
#===============================================================================
copy_portainer_certs() {
    # Make the directory
    sudo mkdir /local_certs/ &> /dev/null

    host_ssl_path="/etc/letsencrypt/live/$DOMAIN_NAME/"

    # Copy and rename the cert files for Portainer
    sudo cp $host_ssl_path/cert.pem /local_certs/portainer.crt
    sudo cp $host_ssl_path/privkey.pem /local_certs/portainer.key
}



#===============================================================================
#
# Copies SSL certification into the necessary containers
# 
# This accesses the cert files stored at the following:
#   `/etc/letsencrypt/live/<Domain Name>`
#
# The following files are located there:
#   cert.pem
#   chain.pem
#   fullchain.pem
#   privkey.pem
#
#===============================================================================
copy_ssl() {
    echo
    echo 'Copying all SSL certifications...'

    copy_ssl_to_container q2a-apache
    copy_ssl_to_container q2a-phpmyadmin
    copy_portainer_certs

    echo 'All SSL certifications copied'
}



#===============================================================================
#
# Displays status of docker containers after launch.
#
#===============================================================================
display_status() {
    echo
    echo 'All docker containers:'
    docker ps -a
}



# Program execution
install_dependencies
locate_config_files
check_credentials
set_credentials
enable_autolaunch
launch_service
generate_ssl
copy_ssl
cleanup
display_status