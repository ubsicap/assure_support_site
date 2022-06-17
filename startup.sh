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



#===============================================================================
#
# Installs any docker/git dependencies on this OS needed for the server to run.
#
# Currently only installs for debian-based distros, as VM is Ubuntu. 
#
#===============================================================================
install_dependencies() {
    echo '\nInstalling runtime dependencies...'

    #apt-get update -y
    #apt-get install -y docker docker-compose
    #apt-get install -y docker-compose-plugin
    
    
    #apk add --update docker docker-compose docker-compose git openrc
    #addgroup root docker
    #rc-update add docker boot
    #rc-service docker start

    #rm install_docker.sh

    # The following works on an AWS Amazon Linux server
    sudo yum install -y docker docker-compose
    #git clone --branch danny-docker https://github.com/ubsicap/assure_support_site.git
    #cd assure_support_site

    sudo systemctl start docker

    DOCKER_CONFIG=${DOCKER_CONFIG:-$HOME/.docker}
    mkdir -p $DOCKER_CONFIG/cli-plugins
    curl -SL https://github.com/docker/compose/releases/download/v2.6.0/docker-compose-linux-x86_64 -o $DOCKER_CONFIG/cli-plugins/docker-compose
    chmod +x $DOCKER_CONFIG/cli-plugins/docker-compose

    sudo groupadd docker
    sudo usermod -aG docker ${USER}
    sudo systemctl restart docker
    sudo chmod 666 /var/run/docker.sock
}



#===============================================================================
#
# Fetches the latest files from the GitHub repository.
#
# This function may not be used, depending on whether this setup script is
# downloaded alongside the rest of the files.
#
#===============================================================================
fetch_repository() {
    echo '\nFetching GitHub repository...'

    # TODO: Change this to the default repo link
    git clone --branch danny-docker https://github.com/ubsicap/assure_support_site.git
    cd assure_support_site
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
    echo '\nLocating configuration files...'

    config_path=$(find . -type f -name "qa-config.php")
    compose_path=$(find . -type f -name "docker-compose.yml")

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
    echo '\nChecking if MySQL credentials have been set...'

    # Set the database root password, if needed
    if grep -q $ROOT_PASSWORD_TO_REPLACE $compose_path;
    then
        # Toggle echo off when reading the password
        stty -echo
        read -p "Set new password for MySQL root account > " qa_mysql_root_password
        stty echo
        echo
    else
        echo '\tRoot password already defined'
    fi

    # Set the username, if needed
    if grep -q $USERNAME_TO_REPLACE $config_path;
    then
        read -p "Set username for new MySQL user account > " qa_mysql_username
    else
        echo '\tUsername already defined'
    fi

    # Set the database password, if needed
    if grep -q $PASSWORD_TO_REPLACE $config_path;
    then
        stty -echo
        read -p "Set password for new MySQL user account > " qa_mysql_password
        stty echo
        echo
    else
        echo '\tPassword already defined'
    fi

    # Set the database hostname, if needed
    if grep -q $HOSTNAME_TO_REPLACE $config_path;
    then
        echo -n
    else
        echo '\tHostname already defined'
    fi

    # Set the database name, if needed
    if grep -q $DATABASE_TO_REPLACE $config_path;
    then
        read -p "Set name for MySQL database > " qa_mysql_database
    else
        echo '\tDatabase name already defined'
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
    echo '\nSetting MySQL credentials...'

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
# Launches the web server through docker compose.
#
# This causes two containers to be spawned:
#   q2a-apache      Php Apache web server hosting the website
#   q2a-db          MySQL database to store website information
#
#===============================================================================
launch_service() {
    echo '\nLaunching website service via docker'

    docker compose up -d
}



# Program execution
install_dependencies
#fetch_repository
locate_config_files
check_credentials
set_credentials
launch_service