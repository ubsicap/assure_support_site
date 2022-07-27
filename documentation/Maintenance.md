# Maintenance

This document contains information regarding maintenance for the web server.

## Table of Contents

1. [Container Management](#container-management)
1. [Database Management](#database-management)
1. [Migrating Database Content](#migrating-database-content)
    1. [Amazon RDS](#amazon-rds)
    1. [Docker Volumes](#docker-volumes)
1. [SSL Certification](#ssl-certification)

## Container Management

This installation is configured to work with [Portainer](https://www.portainer.io/), a web-based container management GUI. It's like Docker Desktop, but in a web browser.

The service is launched automatically alongside the rest of the web server container. To access, navigate to `https://<Domain Name>:9443`

## Database Management

The database can be accessed through [MySQL Workbench](https://www.mysql.com/products/workbench/) (or another MySQL access tool) through SSH with the following settings:

-   **Connection Method**: `Standard TCP/IP over SSH`
-   **SSH Hostname**: The hostname of the EC2 instance (i.e. `ec2...amazonaw.com`)
-   **SSH Username**: Username of account on EC2 instance (i.e. `ubuntu`)
-   **SSH Key File**: The key file path of the credentials needed to log into the EC2 instance (i.e. `/path/to/dev/key.pem`)
-   **MySQL Hostname**: The hostname of the RDS instance (i.e. `q2a...rds.amazon.com`)
-   **MySQL Server port**: `3306`
-   **Username**: `admin`
-   **Password**: Whatever you configured as the RDS password

More information can be found [here](https://www.digitalocean.com/community/tutorials/how-to-connect-to-a-mysql-server-remotely-with-mysql-workbench).

## Migrating Database Content

**Note**: Some site-specific information, such as UI changes, custom pages, etc. is stored in the database. Thus, when migrating data, be sure to back up any of these changes to ensure they will not be lost forever!

Migrating data to the server depends on which database is implemented.

### Amazon RDS

The site is likely configured to use an AWS RDS database. Thus, the following process will allow you to import new data:

1. Getting a dump of the desired data from your local machine.

    1. View your database from MySql Workbench.
    1. Go to Server > Data Export
    1. Select the schema with the desired tables (if you don’t want to overwrite the tables storing site configuration do not select `qa_options` and `qa_pages`).
    1. Choose Export to Self-Contained File
    1. Start Export

1. Moving the dump to the EC2 instance.

    1. Before you do this make sure you can SSH (secure shell) into the EC2 instance.
    1. Use `scp` to send the dump file to the EC2 instance
        - `scp -i <your .pem key> <local dump file> <user>@<Elastic IP>:<destination of dump file>`
        - Example: `scp -i q2a_intern_key Dump20220701.sql ubuntu@supportsitetest.tk:~/dumps/Dump20220701.sql`

1. Importing the data to the RDS instance.
    1. Now that the dump is on the EC2 instance we can import the data to RDS.
    1. Connect to the EC2 instance via ssh.
        - `ssh -i <your .pem key> ubuntu@<Elastic IP>`
    1. Make sure mysql is installed
        - `sudo apt install -y mysql-client-core-8.0`
    1. (Optional) Verify your connection to the database works, for example:
        - `mysql -h q2a-db-test.cmnnis04whwr.us-east-1.rds.amazonaws.com -P 3306 -u admin -p`
        - You can run `\q` to exit the MySQL connection
    1. Import the file into RDS, for example:
        - `mysql -h q2a-db-test.cmnnis04whwr.us-east-1.rds.amazonaws.com -P 3306 -u admin -p q2adb < Dump20220701.sql`

### Docker Volumes

In the event that the site is using a MySQL Docker container, the following steps will allow you to import new data:

1. On the host of the source of the database, archive the `_data` folder located in `/var/lib/docker/volumes/<db container volume>/`
    1. `<db container volume>` is either `app_q2a_db_volume` or `assure_support_site_q2a_db_volume`
1. Ensure the destination machine has enough storage capacity for the new database. If not, [increase its disk space](https://linuxhint.com/increase-disk-space-ec2/).
1. Transfer the archive to the destination machine ([`gdown`](https://pypi.org/project/gdown/) for downloading from Google Drive)
1. Stop all running containers.
1. Create a backup of `/var/lib/docker/volumes/app_q2a_db_volume/_data` on the destination machine.
    1. `sudo mv /var/lib/docker/volumes/app_q2a_db_volume/_data ./_data_db_backup`
1. Unzip the archive so that the new `_data` volume is located in place of the folder you just backed up.
1. Re-start containers and ensure that all data was transported successfully.

## SSL Certification

Please note that this requires a custom domain name to bet set up. Before attempting this, ensure that HTTPS traffic is not yet allowed by navigating to `https://<Domain Name>` in your web browser. Do not attempt the following steps if the site is already certified.

**VERY IMPORTANT NOTE**: If you are _developing_, be sure to include the `--dry-run` flag when running `certbot`, otherwise you will be rate limited! For production, simply omit this flag.

1. Ensure that the web server is live (EC2 instance & Docker containers).
1. Connect to the EC2 instance (`ssh -i </path/to/key.pem> <user>@<domain name>`)
1. Once connected, run the following two commands:

    ```sh
    # Install certbot & apache plugin (if needed)
    apt-get install -y certbot

    # Run certbot interactively
    certbot --dry-run --webroot
    ```

1. Follow the prompts
1. Alternative, running certbot can be run in "non-interactive" mode:
    ```sh
    certbot certonly --dry-run \  # Just generate the cert files
         --non-interactive \
         --agree-tos \            # Automatically agree to the ToS
         --expand \               # Append new domains
         -m $ADMIN_EMAIL \        # Email to contact about renewal
         --webroot -w $WEBROOT \  # Root dir of the website
         -d $DOMAIN_NAME \        # Each domain you want to certify
         -d www.$DOMAIN_NAME
    ```
1. If successful, you will see a message stating that the site is now certified
1. Navigate to `https://<Domain Name>` and verify that HTTPS traffic is allowed

More information can be found [here](https://certbot.eff.org/instructions?ws=other&os=ubuntufocal).
