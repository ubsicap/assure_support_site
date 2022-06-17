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


# The hostname of the database is already defined as the container's name
qa_mysql_hostname=''
qa_mysql_username=''
qa_mysql_password=''
qa_mysql_database=''
qa_mysql_root_password=''

install_dependencies() {
    echo -e "\nInstalling runtime dependencies..."

    apt-get update -y
    apt-get install -y docker docker-compose
    apt-get install -y docker-compose-plugin
    
    
    #apk add --update docker docker-compose docker-compose git openrc
    #addgroup root docker
    #rc-update add docker boot
    #rc-service docker start

    #rm install_docker.sh
}

fetch_repository() {
    echo -e "\nFetching GitHub repository..."

    # TODO: Change this to the default repo link
    git clone --branch danny-docker https://github.com/ubsicap/assure_support_site.git
    cd assure_support_site
}

locate_config_files() {
    echo -e "\nLocating configuration files..."

    config_path=$(find . -type f -name "qa-config.php")
    compose_path=$(find . -type f -name "docker-compose.yml")

    echo "Configuration files found:"
    echo -e "\t$config_path"
    echo -e "\t$compose_path"
}

check_credentials() {
    echo -e "\nChecking if MySQL credentials have been set..."

    # Set the database root password, if needed
    if grep -q $ROOT_PASSWORD_TO_REPLACE $compose_path;
    then
        # Toggle echo off when reading the password
        stty -echo
        read -p "Set new password for MySQL root account > " qa_mysql_root_password
        stty echo
        echo
    else
        echo -e "\tRoot password already defined"
    fi

    # Set the username, if needed
    if grep -q $USERNAME_TO_REPLACE $config_path;
    then
        read -p "Set username for new MySQL user account > " qa_mysql_username
    else
        echo -e "\tUsername already defined"
    fi

    # Set the database password, if needed
    if grep -q $PASSWORD_TO_REPLACE $config_path;
    then
        stty -echo
        read -p "Set password for new MySQL user account > " qa_mysql_password
        stty echo
        echo
    else
        echo -e "\tPassword already defined"
    fi

    # Set the database hostname, if needed
    if grep -q $HOSTNAME_TO_REPLACE $config_path;
    then
        echo -n
    else
        echo -e "\tHostname already defined"
    fi

    # Set the database name, if needed
    if grep -q $DATABASE_TO_REPLACE $config_path;
    then
        read -p "Set name for MySQL database > " qa_mysql_database
    else
        echo -e "\tDatabase name already defined"
    fi

    qa_mysql_hostname=$(awk '/container_name:/' $compose_path | awk 'FNR == 2 {print $2}')
}



set_credentials() {
    echo -e "\nSetting MySQL credentials..."

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

launch_service() {
    echo -e "\nLaunching website service via docker"

    docker compose up
}

install_dependencies
#fetch_repository
locate_config_files
check_credentials
set_credentials
launch_service