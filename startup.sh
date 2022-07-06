#!/bin/sh



# Paths to app configuration files that will be modified during startup
CONFIG_PATH=''
COMPOSE_PATH=''

# Default values that will be replaced in the above files
RDS_MASTER_USERNAME='RDS_MASTER_USERNAME'
RDS_MASTER_PASSWORD='RDS_MASTER_PASSWORD'
RDS_DB_NAME='RDS_DB_NAME'
RDS_ENDPOINT='RDS_ENDPOINT'

# Values to be read from the user
QA_MYSQL_HOSTNAME=''
QA_MYSQL_USERNAME=''
QA_MYSQL_PASSWORD=''
QA_MYSQL_DATABASE=''

# SSL Certification information
ADMIN_EMAIL_ADDRESS=''
DOMAIN=''
WEBROOT=$(realpath public)



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
    #   launching the instance, so it can safely be removed.
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

    CONFIG_PATH=$(realpath $(find . -type f -name "qa-config-secure.php"))
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
#   CONFIG_PATH
#   RDS_MASTER_USERNAME
#   RDS_MASTER_PASSWORD
#   RDS_DB_NAME
#   RDS_ENDPOINT
#   QA_MYSQL_HOSTNAME
#   QA_MYSQL_USERNAME
#   QA_MYSQL_PASSWORD
#   QA_MYSQL_DATABASE
#
#===============================================================================
check_credentials() {
    echo
    echo 'Checking if MySQL credentials have been set...'

    # Set the username, if needed
    if grep -q $RDS_MASTER_USERNAME $CONFIG_PATH;
    then
        read -p "Set username for the RDS MySQL master account > " QA_MYSQL_USERNAME
    else
        echo '    Username already defined'
    fi

    # Set the database password, if needed
    if grep -q $RDS_MASTER_PASSWORD $CONFIG_PATH;
    then
        stty -echo
        read -p "Set password for the RDS MySQL master account > " QA_MYSQL_PASSWORD
        stty echo
        echo
    else
        echo '    Password already defined'
    fi

    # Set the database hostname, if needed
    if grep -q $RDS_ENDPOINT $CONFIG_PATH;
    then
        read -p "Provide the endpoint/URL of RDS MySQL database to connect to > " QA_MYSQL_HOSTNAME
    else
        echo '    Hostname already defined'
    fi

    # Set the database name, if needed
    if grep -q $RDS_DB_NAME $CONFIG_PATH;
    then
        read -p "Provide the name of the RDS MySQL database (Probably already defined in AWS) > " QA_MYSQL_DATABASE
    else
        echo '    Database name already defined'
    fi

    # Reload the environment variables
    . /etc/environment
    if [ -z $DOMAIN_NAME ]; then
        read -p "Enter the domain name of the site to be hosted > " DOMAIN
    else
        echo "    Domain name already set to $DOMAIN_NAME"
        echo "      To change this, modify /etc/environment"
        DOMAIN=$DOMAIN_NAME
    fi

    if [ -z $ADMIN_EMAIL ]; then
        read -p "Enter an email account to utilize as the administrator contact > " ADMIN_EMAIL_ADDRESS
    else
        echo "    Administrator email already set to $ADMIN_EMAIL"
        echo "      To change this, modify /etc/environment"
        ADMIN_EMAIL_ADDRESS=$ADMIN_EMAIL
    fi
}



#===============================================================================
#
# Sets the MySQL credentials provided by the user.
#
# Global variables used:
#   CONFIG_PATH
#   RDS_MASTER_USERNAME
#   RDS_MASTER_PASSWORD
#   RDS_DB_NAME
#   RDS_ENDPOINT
#   QA_MYSQL_HOSTNAME
#   QA_MYSQL_USERNAME
#   QA_MYSQL_PASSWORD
#   QA_MYSQL_DATABASE
#
#===============================================================================
set_credentials() {
    echo
    echo 'Setting MySQL credentials...'

    # Set the standard account's username
    sed -i "s/$RDS_MASTER_USERNAME/$QA_MYSQL_USERNAME/" $CONFIG_PATH

    # Set the standard account's password
    sed -i "s/$RDS_MASTER_PASSWORD/$QA_MYSQL_PASSWORD/" $CONFIG_PATH

    # Set the hostname of the database (name of the DB container)
    sed -i "s/$RDS_ENDPOINT/$QA_MYSQL_HOSTNAME/" $CONFIG_PATH

    # Set the database's name
    sed -i "s/$RDS_DB_NAME/$QA_MYSQL_DATABASE/" $CONFIG_PATH

    # Set the web server's domain name
    if [ ! -z $DOMAIN ]; then
        export DOMAIN_NAME=$DOMAIN
        echo "export DOMAIN_NAME=$DOMAIN" | sudo tee -a /etc/environment
    fi

    # Set the web server's admin email
    if [ ! -z $ADMIN_EMAIL_ADDRESS ]; then
        export ADMIN_EMAIL=$ADMIN_EMAIL_ADDRESS
        echo "export ADMIN_EMAIL=$ADMIN_EMAIL_ADDRESS" | sudo tee -a /etc/environment
    fi

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

    # Re-run this startup script
    sudo echo "#!/bin/sh
sh $(realpath $0)" > $boot_path/startserver.sh

    # In case we just want to re-start the containers, uncomment this
    #sudo echo "#!/bin/sh
#docker compose -f $COMPOSE_PATH up -d" > $boot_path/startserver.sh

    # Mark the script as executable for everyone
    sudo chmod -R 755 $boot_path

    echo 'Autostart policy set'
}



#===============================================================================
#
# Launches the web server through docker compose.
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
# The certification process uses Certbot (certbot.eff.org) and stores the cert
# files in `/etc/letsencrypt/live/<DOMAIN_NAME>/``
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
    #certbot certonly --dry-run --non-interactive --agree-tos -m daniel_hammer@sil.org -d supportsitetest.tk -d www.supportsitetest.tk --webroot -w $WEBROOT
    sudo certbot certonly --dry-run \
        --non-interactive \
        --agree-tos \
        --expand \
        -m $ADMIN_EMAIL \
        --webroot -w $WEBROOT \
        -d $DOMAIN_NAME \
        -d www.$DOMAIN_NAME

    echo 'SSL certification complete'
}



#===============================================================================
#
# Copies SSL certification into the specificed container.
#
# General structure copied from:
#       https://blog.zotorn.de/phpmyadmin-docker-image-with-ssl-tls/
#
# Note that this modifies the `/etc/apache2/sites-available/000-default.conf`.
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
    docker exec $container sed -i "s,.*ServerAdmin.*,ServerAdmin $ADMIN_EMAIL\nServerName $DOMAIN_NAME,g" $default_ssl_conf

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
# Copies SSL certs into ./local_certs as a backup.
#
# Also makes copies labeled for Portainer.
#
# Global variables used:
#   DOMAIN_NAME
#
#===============================================================================
init_ssl() {
    echo
    echo 'Configuring and backing up SSL certificates...'

    # Make a backup directory for the SSL certs
    sudo mkdir ./local_certs > /dev/null 2>&1
    sudo cp -Lr /etc/letsencrypt/live/$DOMAIN_NAME/* ./local_certs

    # Copy the appropriate certs for Portainer
    sudo cp -L ./local_certs/cert.pem ./local_certs/portainer.crt
    sudo cp -L ./local_certs/privkey.pem ./local_certs/portainer.key
}



#===============================================================================
#
# Builds and launches a HTTP docker container of the web server.
#
# This is used for generating SSL certificates and nothing more. The container
# automatically deletes itself after stopping.
#
# Global variables used:
#   WEBROOT
#
#===============================================================================
launch_http() {
    echo
    echo 'Launching basic HTTP web service'

    # The name of this container doesn't really matter
    container_name='apache-no-ssl'

    # First, build the image with no SSL support
    docker build -t q2a-apache-no-ssl -f $WEBROOT/DockerfileNoSSL $WEBROOT

    # If the container is already running, stop it
    docker stop $container_name 2> /dev/null

    # Now run the no-ssl container and delete it afterwards
    docker run -d --rm --name $container_name \
        -v "$WEBROOT:/var/www/html" \
        -v "$(pwd)/config:/var/www/config" \
        -p "80:80" -p "443:443" \
        q2a-apache-no-ssl

    docker ps

    echo 'Launched HTTP web service'
}



#===============================================================================
#
# Stops and deletes ALL running containers
#
#===============================================================================
kill_containers() {
    echo
    echo 'Stopping and removing all containers...'

    docker stop $(docker ps -aq) 2> /dev/null
    docker rm $(docker ps -aq) 2> /dev/null

    echo 'Containers removed'
}



#===============================================================================
#
# Builds and launches a HTTPS docker container of the web server.
#
# This web service automatically redirects all traffic to HTTPS.
#
# Global variables used:
#   COMPOSE_PATH
#
#===============================================================================
launch_https() {
    echo
    echo 'Launching HTTPS web service'

    docker compose -f $COMPOSE_PATH up -d

    echo 'Launched HTTPS web service'
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



#===============================================================================
#
# Entrypoint of this startup script.
#
# Installs dependencies, fetches credentials, generates SSL certificates, and
# launches an autostarting web service via Docker.
#
# Global variables used:
#   DOMAIN_NAME
#
#===============================================================================
main() {
    # Program execution
    install_dependencies
    locate_config_files
    check_credentials
    set_credentials
    enable_autolaunch
    kill_containers
    
    # If SSL certs have not yet been generated, do so
    if [ ! -d /etc/letsencrypt/live/$DOMAIN_NAME ]; then
        launch_http
        generate_ssl
        init_ssl
        kill_containers
    fi
    # Now launch the web server with HTTPS
    launch_https

    #cleanup
    display_status
}

main