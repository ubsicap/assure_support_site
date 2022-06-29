# Assure Support Site

Repository and information for the Assure Support Site

## Table of Contents

1. [Structure](#structure)
1. [Local Startup](#local-startup)
1. [Launching to AWS](#launching-to-aws)
1. [Custom Domain Name](#custom-domain-name)
1. [SSL Certification](#ssl-certification)
1. [Database Management](#database-management)
1. [Container Management](#container-management)

## Structure

Repository Contents:

```sh
assure_support_site/
├── README.md               # This file
├── docker-compose.yml      # Launches the web service in two containers
├── ec2_user_data.sh        # User Data for the EC2 instance (ran at launch)
├── public/                 # Question2Answer website source code
└── startup.sh              # Startup script for launching on remote server
```

## Local Startup

Make sure the following credentials are set:

- In `public/qa-config.php`
  - `QA_MYSQL_HOSTNAME`
    - Must be set to the name of the DB container defined in `docker-compose.yml`
  - `QA_MYSQL_USERNAME`
    - Matches the `MYSQL_USER` environment variable below
  - `QA_MYSQL_PASSWORD`
    - Matches the `MYSQL_PASSWORD` environment variable below
  - `QA_MYSQL_DATABASE`
    - Matches the `MYSQL_DATABASE` environment variable below
- In `./docker-compose.yml`
  - `MYSQL_ROOT_PASSWORD`
  - `MYSQL_DATABASE`
  - `MYSQL_USER`
  - `MYSQL_PASSWORD`

Then run:

```sh
docker compose up -d
```

Finally, navigate to `http://localhost` in your web browser

## Launching to AWS

1. Create a new EC2 instance
1. Select Ubuntu 20.04 as the image
1. Select/create a key pair for `ssh` access
1. Select/create a network security group with the following rules:
   - **Type**: HTTP, **Protocol**: TCP, **Port Range**: 80, **Source**: 0.0.0.0/0
   - **Type**: HTTPS, **Protocol**: TCP, **Port Range**: 443, **Source**: 0.0.0.0/0
   - **Type**: ssh, **Protocol**: TCP, **Port Range**: 22, **Source**: <Your IP>
1. Click `Launch Instance`
1. While waiting for the instance to boot, click on it and copy its public IPv4 address
1. Connect to your instance with the following command (note you may need to `sudo`, depending on file permissions):
   - `ssh -i <your .pem key> <user>@<instance public IPv4>`
   - `<username>` should be `ubuntu`
1. Once connected, run the following commands:
   - `sudo apt install -y git`
   - `git clone https://github.com/ubsicap/assure_support_site.git`
   - `cd assure_support_site`
   - `sh startup.sh`
1. You will be prompted to create a MySQL root password, account username, account password, and database name.
1. Once you have created credentials, the `docker-compose.yml` file will be ran and two containers will start.
1. Open your web browser to `http://<instance public IP>`
1. You will be prompted to create an administrator account for the website.
1. Once created, you will be brought to the site's homepage.

## Custom Domain Name

Setting up a custom domain is split into multiple parts, listed below:

### Elastic IP

1. From the [AWS EC2 Console](https://console.aws.amazon.com/ec2/v2/home?#), search for "Elastic IP"
1. Click "Allocate Elastic IP Address"
   - Note there is a charge for this if the address does not get associated with an EC2 instance
1. Select the IPv4 address pool option desired
   - For development, we used "Amazon's pool of IPv4 addresses"
1. Allocate the address
1. After the IP was generated, click "Actions" and then "Associate"
1. Associate the Elastic IP with the EC2 instance running the server

More info can be found [here](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/elastic-ip-addresses-eip.html).

### Register Domain Name

1. Register a domain name through your service of choice
   - [freenom](https://my.freenom.com/domains.php) was used for development
   - Dev domain name is `supportsitetest.tk`
   - Used "freenom DNS" instead of custom name server
1. Create the following DNS records:
   | NAME | TYPE | TTL | TARGET |
   |---------------|------|------|--------------|
   | | `A` | `3600` | `<Elastic IP>` |
   | `www` | `A` | `3600` | `<Elastic IP>` |

### Link Together

Once the above steps have been taken, you should be able to navigate to `http://<Elastic IP>` **OR** `http://<Domain Name>` and you will arrive at the same web page.

## SSL Certification

### Using Certbot

Before attempting this, please ensure that HTTPS traffic is not yet allowed by navigating to `https://<Domain Name>` in your web browser. Do not attempt the following steps if the site is already certified.

**Note**: This requires a custom domain name to bet set up first. Also, every time `docker compose down` or an equivalent command is run to terminate the `q2a-apache` container, this process will need to be repeated.

**VERY IMPORTANT NOTE**: If you are _developing_, be sure to include the `--dry-run` flag when running `certbot`, otherwise you will be rate limited! For production, simply omit this flag.

1. Ensure that the web server is live (EC2 instance & Docker containers).
1. Connect to the EC2 instance (`ssh -i <your_key.pem> <user>@<domain name>`, just like above)
1. Once connected, run the following two commands:

   ```sh
   # Install certbot & apache plugin (if needed)
   apt-get install -y certbot

   # Run certbot interactively
   certbot --test-cert --webroot
   ```

1. Follow the prompts
1. If successful, you will see a message stating that the site is now certified
1. Navigate to `https://<Domain Name>` and verify that HTTPS traffic is allowed

More information can be found [here](https://certbot.eff.org/instructions?ws=other&os=ubuntufocal).

### Manually (For development ONLY)

More information can be found [here](https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-apache-in-debian-9).

## Database Management

The database can be accessed through two methods:

- [phpMyAdmin](https://www.phpmyadmin.net/), which is running in a container alongside the database, on port `3306`
- [MySQL Workbench](https://www.mysql.com/products/workbench/) (or another MySQL access tool) on port `9906`

**Note**: Not yet implemented with [SSL support](https://blog.zotorn.de/phpmyadmin-docker-image-with-ssl-tls/)

I don't know which method is preferred for production. Both are password protected using the credentials entered at first launch.

## Container Management

This installation is configured to work with [Portainer](https://www.portainer.io/), a web-based container management GUI. It's like Docker Desktop, but in a web browser.

The service is launched automatically alongside the rest of the containers. To access, navigate to `https://<Domain Name>:9443`
