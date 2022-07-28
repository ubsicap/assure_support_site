# Installation and Setup

This document is a guide on how to set up a web server to run the contents of this repository in a production environment.

If you are looking to set up the site locally, such as for development, please refer to the [Local Development](./LocalDevelopment.md) guide.

## Table of Contents

1. [Before you begin](#before-you-begin)
1. [Creating RDS Instance](#creating-rds-instance)
    1. [RDS Security](#rds-security)
1. [Launching to AWS](#launching-to-aws)
    1. [Creating the Instance](#creating-ec2-instance)
    1. [Domain Name](#domain-name)
    1. [Launching the Website](#launching-the-website)
1. [Importing Data](#importing-data)
1. [Post Installation](#post-installation)

## Before you begin

It is important to follow the steps in this document exactly as outlined, unless stated otherwise. The process for setting up this website involves Amazon Web Services (AWS), Docker, DNS servers, LetsEncrypt, and more. Ensure you have an [AWS](https://console.aws.amazon.com/console) account.

You will need an email address to server as the webmaster. It will be used for administrative purposes on the site, such as the site's admin account, registering the domain name, etc. This does not need to be the same email address associated with your AWS account.

When dealing with Amazon Web Services, ensure that your location is the same across all platforms (EC2, RDS). The location should be displayed in the top right.

**VERY IMPORTANT NOTE**: In the [`startup.sh`](../startup.sh) file, within the `generate_ssl()` function, [Certbot](https://certbot.eff.org/instructions?ws=other&os=ubuntufocal) is used to generate SSL certificates. _If you are running this for development_, you must **make sure** that the call to `certbot` includes the `--dry-run` flag. If you do not, you risk being rate limted (per 168 hours).

## Creating RDS Instance

The first step is to use [AWS RDS](https://console.aws.amazon.com/rds) to create a MySQL database for the site.

Configuration will be dependent on your preferences and needs, this section will setup a simple, free RDS instance.

In AWS create an RDS instance with the following settings:

-   Create Database (Standard)
-   MySql 8.0.28 (default)
-   Burstable classes: db.t3.micro
-   General Purpose SSD
-   20 GiB allocated
-   Enable storage autoscaling
-   100 GiB maximum
-   Default VPC (make sure id matches the existing EC2 instance)
-   Public Access: No
-   VPC security groups: MySQL Access (if that isn't a group, just use default)
-   Password authentication
-   Additional Configuration > Initial database name: q2adb

Make sure to note down the database password and username (`admin` by default).

### RDS Security:

-   To protect the site data, the database should have been configured so that only instances on the same VPC (i.e. the EC2 instance) can access it.
-   The EC2 instance should only have ports open for ssh (22), http (80), ssl (443), MySql (3306).
-   For the RDS instance, the only inbound port permitted should be MySql(3306). The source ip of the inbound rule should be the private ip of the EC2 instance.
-   This can be configured in the security groups of the EC2 and RDS instance.

## Launching to AWS

Now you can begin the process of launching the web server and setting up the website.

### Creating EC2 Instance

The EC2 instance is the host of the web server and its details will depend entirely on the deployment's needs.

1. Click "Launch Instance" or "Create an EC2 Instance" from [AWS EC2 instance](https://console.aws.amazon.com/ec2/v2).
1. Select Ubuntu 20.04 as the image.
1. Select/create a key pair for `ssh` access.
    - Make sure you save the `.pem` key file, as these will be used to access the server later.
1. Select/create a network security group according to your deployment needs. For development, the following rules were used:
    - **Type**: `HTTP`, **Protocol**: `TCP`, **Port Range**: `80`, **Source**: `0.0.0.0/0`
    - **Type**: `HTTPS`, **Protocol**: `TCP`, **Port Range**: `443`, **Source**: `0.0.0.0/0`
    - **Type**: `ssh`, **Protocol**: `TCP`, **Port Range**: `22`, **Source**: `<Your IP>`
    - **Type**: `MYSQL/Aurora`, **Protocol**: `TCP`, **Port Range**: `3306`, **Source**: `<Your IP>`
1. Expand the `Advanced details` section.
1. Scroll down until you see a field marked `User data`.
1. Paste the contents of the [`ec2_user_data.sh`](../ec2_user_data.sh) script into this field.
1. Click `Launch Instance`.
1. While waiting for the instance to boot (it may take a few minutes), click on it and copy its public IPv4 address.
1. Connect to your instance through `ssh` using your preferred method. The command-line method will look like the following (note you may need to `sudo`, depending on file permissions):
    - `ssh -i </path/to/key.pem> <username>@<instance public IPv4>`
    - `<username>` should be `ubuntu` if you chose an Ubuntu AMI.
1. Once connected, ensure that the contents of this repository have been coped into `/home/<username>/app`.

### Domain Name

You will need a static ("Elastic") IP address, a domain name, and the ability to create DNS records for the domain name.

1. Navigate back to the [AWS EC2 Console](https://console.aws.amazon.com/ec2/v2) and search "Elastic IP" in the menu.
1. Click "Allocate Elastic IP Address."
    - Note there is a [charge](https://aws.amazon.com/premiumsupport/knowledge-center/elastic-ip-charges/) for this if the address does not get associated with an EC2 instance.
1. Select the IPv4 address pool option desired and allocate the address.
    - For development, we used "Amazon's pool of IPv4 addresses."
1. After the IP was generated, click "Actions" and then "Associate" and associate the Elastic IP with the EC2 instance running the server.
1. If you do not already have a domain name registered, register one before proceeding.
    - [freenom](https://my.freenom.com/domains.php) was used for development.
    - Dev domain name is `supportsitetest.tk`.
    - Used "freenom DNS" instead of custom name server.
1. Configure the following DNS records for your domain:
   | NAME | TYPE | TTL | TARGET |
   |---------------|------|------|--------------|
   | | `A` | `3600` | `<Elastic IP>` |
   | `www` | `A` | `3600` | `<Elastic IP>` |

### Launching the Website

Now you can launch the website itself.

1. After the records have updated, reconnect to your EC2 instance through `ssh`.
1. Once connected, run the following commands:
    - `cd ~/app`
    - `sh startup.sh`
1. You may be prompted to provide information such as the AWS RDS credentials, website's domain name, and webmaster's email address.
1. Once you have provided credentials, the `docker-compose.yml` file will be ran and the Docker containers will start.
1. Open your web browser to `http://<instance public IP>`.
1. You will be prompted to create a "Super Administrator" account for the website. This is different than the administrator account for the database, but the same email address may be used.
1. Once created, you will be brought to the site's homepage.
1. Ensure that you can navigate to `https://<Elastic IP>` **OR** `https://<Domain Name>` and arrive at the same web page.

## Importing Data

In order to transfer data to the site, you will need to have an accessible copy and `ssh` access to the EC2 instance.

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
        - `ssh -i <your .pem key> <user>@<Elastic IP>`
    1. Ensure mysql is installed
        - `sudo apt install -y mysql-client-core-8.0`
    1. (Optional) Verify your connection to the database works, for example:
        - `mysql -h q2a-db-test.cmnnis04whwr.us-east-1.rds.amazonaws.com -P 3306 -u admin -p`
        - You can run `\q` to exit the MySQL connection
    1. Import the file into RDS, for example:
        - `mysql -h q2a-db-test.cmnnis04whwr.us-east-1.rds.amazonaws.com -P 3306 -u admin -p q2adb < Dump20220701.sql`

## Post Installation

Once the site has been set up properly, you may want to perfom the following actions:

-   Check that you are always redirected to `https://<Domain Name>/` when attempting to access the site through the Elastic IP or domain name using either HTTP or HTTPS.
-   Install the [Dynamic Mentions](https://bitbucket.org/pupi1985/q2a-dynamic-mentions-public) plugin by copying the `pupi-dm/` folder into `public/qa-plugin/` in the web server.
    -   This plugin is not included in this repository as, at the time of writing, the plugin is premium and not to be publicized.
