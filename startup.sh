#!/bin/sh



# Paths to the files that will be modified
config_path=''
compose_path=''

# Default values that will be replaced in the above files
USERNAME_TO_REPLACE='USERNAME_TO_REPLACE'
PASSWORD_TO_REPLACE='PASSWORD_TO_REPLACE'
DATABASE_TO_REPLACE='DATABASE_TO_REPLACE'
HOSTNAME_TO_REPLACE='HOSTNAME_TO_REPLACE'
ROOT_PASSWORD_TO_REPLACE='ROOT_PASSWORD_TO_REPLACE'

# Values to be read from the user
qa_mysql_hostname=''
qa_mysql_username=''
qa_mysql_password=''
qa_mysql_database=''
qa_mysql_root_password=''

# SSL Certification information
# This information needs to be changed so that it is provided by the user/sysadmin
SSL_EMAIL='daniel_hammer@sil.org'
DOMAIN_NAME='supportsitetest.tk'
WEBROOT='/home/$USER/app/q2a_site'


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
        sudo apt-get install -y ca-certificates curl gnupg lsb-release # Doesnt do anything
        
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

    # This script is copied to /var/lib/cloud/instance/user-data.txt upon,
    # launching the instance so it can safely be removed.
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
#===============================================================================
locate_config_files() {
    echo
    echo 'Locating configuration files...'

    config_path=$(realpath $(find . -type f -name "qa-config.php"))
    compose_path=$(realpath $(find . -type f -name "docker-compose.yml"))

    echo "Configuration files found:"
    echo "    $config_path"
    echo "    $compose_path"
}



#===============================================================================
#
# Checks if the credentials have already been set, prompting the user if not.
#
#===============================================================================
check_credentials() {
    echo
    echo 'Checking if MySQL credentials have been set...'

    # Set the database root password, if needed
    if grep -q $ROOT_PASSWORD_TO_REPLACE $compose_path;
    then
        # Toggle echo off when reading the password
        stty -echo
        read -p "Set new password for MySQL root account > " qa_mysql_root_password
        stty echo
        echo
    else
        echo '    Root password already defined'
    fi

    # Set the username, if needed
    if grep -q $USERNAME_TO_REPLACE $config_path;
    then
        read -p "Set username for new MySQL user account > " qa_mysql_username
    else
        echo '    Username already defined'
    fi

    # Set the database password, if needed
    if grep -q $PASSWORD_TO_REPLACE $config_path;
    then
        stty -echo
        read -p "Set password for new MySQL user account > " qa_mysql_password
        stty echo
        echo
    else
        echo '    Password already defined'
    fi

    # Set the database hostname, if needed
    if grep -q $HOSTNAME_TO_REPLACE $config_path;
    then
        echo -n
    else
        echo '    Hostname already defined'
    fi

    # Set the database name, if needed
    if grep -q $DATABASE_TO_REPLACE $config_path;
    then
        read -p "Set name for MySQL database > " qa_mysql_database
    else
        echo '    Database name already defined'
    fi

    # Lastly, fetch the name of the container that the MySQL database will run in
    qa_mysql_hostname=$(awk '/container_name:/' $compose_path | awk 'FNR == 2 {print $2}')
}



#===============================================================================
#
# Sets the MySQL credentials provided by the user.
#
#===============================================================================
set_credentials() {
    echo
    echo 'Setting MySQL credentials...'

    # Set the root password
    sed -i "s/$ROOT_PASSWORD_TO_REPLACE/$qa_mysql_root_password/" $compose_path

    # Set the standard account's username
    sed -i "s/$USERNAME_TO_REPLACE/$qa_mysql_username/" $config_path
    sed -i "s/$USERNAME_TO_REPLACE/$qa_mysql_username/" $compose_path

    # Set the standard account's password
    sed -i "s/$PASSWORD_TO_REPLACE/$qa_mysql_password/" $config_path
    sed -i "s/$PASSWORD_TO_REPLACE/$qa_mysql_password/" $compose_path

    # Set the hostname of the database (name of the DB container)
    sed -i "s/$HOSTNAME_TO_REPLACE/$qa_mysql_hostname/" $config_path

    # Set the database's name
    sed -i "s/$DATABASE_TO_REPLACE/$qa_mysql_database/" $config_path
    sed -i "s/$DATABASE_TO_REPLACE/$qa_mysql_database/" $compose_path

    echo "MySQL credentials set"
}



#===============================================================================
#
# Sets up the auto-start script to automatically restart the docker containers
# whenever the EC2 instance is restarted.
#
# Note that this doesn't effect machines not hosted on AWS EC2.
#
#===============================================================================
enable_autolaunch() {
    echo
    echo 'Setting containers to launch automatically on system start...'

    BOOT_PATH='/var/lib/cloud/scripts/per-boot'

    # Allow a script to be created
    sudo chmod -R 777 $BOOT_PATH

    # By default, just compose the containers,
    # But we *may* want to re-run the startup script, so I'm leaving this here
    #echo "#!/bin/sh
#sh $(realpath $0) > $BOOT_PATH
    sudo echo "#!/bin/sh
docker compose -f $compose_path up -d" > $BOOT_PATH/startserver.sh

    # Mark the script as executable for everyone
    sudo chmod -R 755 $BOOT_PATH

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
#===============================================================================
launch_service() {
    echo
    echo 'Launching website service via docker...'

    docker compose -f $compose_path up -d

    echo 'Docker containers launched'
}



#===============================================================================
#
# Performs the SSL certification process on the web server.
#
# The certification process uses Certbot (certbot.eff.org)
# It utilizes the following configuration variables:
#   SSL_EMAIL
#   DOMAIN_NAME
#
#===============================================================================
generate_ssl() {
    echo
    echo 'Establishing SSL certification...'

    # Install certbot and packages, if necessary
    ##docker exec q2a-apache apt-get install -y certbot python3-certbot-apache

    # Run certbot with the appropriate information
    #docker exec q2a-apache certbot --apache --non-interactive --agree-tos -m $SSL_EMAIL -d $DOMAIN_NAME

    #============================================
    #
    #             ***IMPORTANT***
    #
    # This line is for DEVELOPMENT ONLY. Notice
    # the `--test-cert` flag? Removing that will
    # run in production mode. Production mode is
    # RATE LIMITED! Don't use it unless you need
    # to!
    #
    #============================================
    #docker exec q2a-apache certbot --test-cert --apache --non-interactive --agree-tos -m $SSL_EMAIL -d $DOMAIN_NAME

    sudo apt-get install -y certbot

    #certbot certonly --test-cert --non-interactive --agree-tos -m daniel_hammer@sil.org -d supportsitetest.tk -d www.supportsitetest.tk --webroot -w ./q2a_site/
    certbot certonly --test-cert --non-interactive --agree-tos -m $SSL_EMAIL -d $DOMAIN_NAME -d www.$DOMAIN_NAME --webroot -w $WEBROOT

    echo 'SSL certification complete'
}



#===============================================================================
#
# Copies SSL certification into the Apache web service container.
#
#===============================================================================
copy_ssl_apache() {
    echo
    echo 'Copying SSL certifications to Apache service...'

    # Copy the SSL keys to the apache container
    SSL_PATH="/etc/letsencrypt/live/$DOMAIN_NAME/"
    docker cp $SSL_PATH/cert.pem q2a-apache:/etc/ssl
    docker cp $SSL_PATH/chain.pem q2a-apache:/etc/ssl
    docker cp $SSL_PATH/fullchain.pem q2a-apache:/etc/ssl
    docker cp $SSL_PATH/privkey.pem q2a-apache:/etc/ssl/private

    # Config files that will be modified
    default_ssl_conf='/etc/apache2/sites-available/default-ssl.conf'

    # Before we do anything, MAKE A BACKUP
    docker exec q2a-apache cp $default_ssl_conf $default_ssl_conf.backup

    # Replace the necessary paths for the cert files
	#docker exec q2a-apache sed -i 's,/etc/ssl/certs/ssl-cert-snakeoil.pem,/etc/ssl/cert.pem,g' $default_ssl_conf
	#docker exec q2a-apache sed -i 's,/etc/ssl/private/ssl-cert-snakeoil.key,/etc/ssl/private/privkey.pem,g' $default_ssl_conf
    #docker exec q2a-apache sed -i 's,#SSLCertificateChainFile,SSLCertificateChainFile,g' $default_ssl_conf
    #docker exec q2a-apache sed -i 's,/etc/apache2/ssl.crt/server-ca.crt,/etc/ssl/chain.pem,g' $default_ssl_conf
    #docker exec q2a-apache sed -i "s,.*ServerAdmin.*,ServerAdmin $SSL_EMAIL\nServerName $DOMAIN_NAME,g" $default_ssl_conf

    # Ensure that 443 is the default SSL port
    docker exec q2a-apache sed -ri -e 's,80,443,' $default_ssl_conf 
    # Enable SSL on the virtual host
    docker exec q2a-apache sed -i -e '/^<\/VirtualHost>/i SSLEngine on' $default_ssl_conf
    # Define cert file paths
    docker exec q2a-apache sed -i -e '/^<\/VirtualHost>/i SSLCertificateFile /etc/ssl/cert.pem' $default_ssl_conf
    docker exec q2a-apache sed -i -e '/^<\/VirtualHost>/i SSLCertificateChainFile /etc/ssl/fullchain.pem' $default_ssl_conf
    docker exec q2a-apache sed -i -e '/^<\/VirtualHost>/i SSLCertificateKeyFile /etc/ssl/private/privkey.pem' $default_ssl_conf

    # Copy the file into the sites-enabled directory
    docker exec q2a-apache cp $default_ssl_conf /etc/apache2/sites-enabled/

    # Start/restart the necessary services
    docker exec q2a-apache a2enmod ssl
    docker exec q2a-apache apachectl restart

    echo 'SSL certifications copied to Apache service'
}



#===============================================================================
#
# Copies SSL certification into the phpMyAdmin service container.
#
#===============================================================================
copy_ssl_phpmyadmin() {
    echo
    echo 'Copying all SSL certifications to phpMyAdmin service...'

    # Copy the SSL keys to the apache container
    SSL_PATH="/etc/letsencrypt/live/$DOMAIN_NAME/"
    docker cp $SSL_PATH/cert.pem q2a-phpmyadmin:/etc/ssl
    docker cp $SSL_PATH/chain.pem q2a-phpmyadmin:/etc/ssl
    docker cp $SSL_PATH/fullchain.pem q2a-phpmyadmin:/etc/ssl
    docker cp $SSL_PATH/privkey.pem q2a-phpmyadmin:/etc/ssl/private

    # Config files that will be modified
    ssl_conf='/etc/apache2/sites-available/000-default.conf'

    # Enable SSL module
    docker exec q2a-phpmyadmin a2enmod ssl

    docker exec q2a-phpmyadmin sed -ri -e 's,80,443,' $ssl_conf
    docker exec q2a-phpmyadmin sed -i -e '/^<\/VirtualHost>/i SSLEngine on' $ssl_conf
    docker exec q2a-phpmyadmin sed -i -e '/^<\/VirtualHost>/i SSLCertificateFile /etc/ssl/cert.pem' $ssl_conf
    docker exec q2a-phpmyadmin sed -i -e '/^<\/VirtualHost>/i SSLCertificateChainFile /etc/ssl/fullchain.pem' $ssl_conf
    docker exec q2a-phpmyadmin sed -i -e '/^<\/VirtualHost>/i SSLCertificateKeyFile /etc/ssl/private/privkey.pem' $ssl_conf
    
    # Restart the necessary services
    docker exec q2a-apache apachectl restart

    echo 'SSL certifications copied to phpMyAdmin service'
}



#===============================================================================
#
# Copies SSL certification into the Portainer service container.
#
#===============================================================================
copy_ssl_portainer() {
    echo
    echo 'Copying all SSL certifications to Portainer service...'

    echo 'SSL certifications copied to Portainer service'
}



#===============================================================================
#
# Copies SSL certification into the necessary containers
# 
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

    copy_ssl_apache()
    copy_ssl_phpmyadmin()
    copy_ssl_portainer()

    echo 'All SSL certifications copied'
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