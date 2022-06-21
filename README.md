# Assure Support Site

Repository and information for the Assure Support Site

## Structure

```sh
.
├── README.md               # This file
├── docker-compose.yml      # Launches the web service in two containers
├── q2a_site/               # Question2Answer website source code
└── startup.sh              # Startup script for launching on remote server
```

## Local Startup

Make sure the following credentials are set:
* In `q2a_site/qa-config.php`
   * `QA_MYSQL_HOSTNAME`
      * Must be set to the name of the DB container defined in `docker-compose.yml`
   * `QA_MYSQL_USERNAME`
      * Matches the `MYSQL_USER` environment variable below
   * `QA_MYSQL_PASSWORD`
      * Matches the `MYSQL_PASSWORD` environment variable below
   * `QA_MYSQL_DATABASE`
      * Matches the `MYSQL_DATABASE` environment variable below
* In `./docker-compose.yml`
   * `MYSQL_ROOT_PASSWORD`
   * `MYSQL_DATABASE`
   * `MYSQL_USER`
   * `MYSQL_PASSWORD`

Then run:
```sh
docker compose up -d
```

Finally, navigate to `http://localhost:80` in your web browser

## Launching to AWS

1. Create a new EC2 instance
   - Name can be anything
1. Select an image (Ubuntu 20.04 was used for testing, Amazon Linux works if you change a line in the startup script)
1. Select/create a key pair for `ssh` access
1. Select/create a network security group with the following rules:
   - **Type**: HTTP, **Protocol**: TCP, **Port Range**: 80, **Source**: 0.0.0.0/0
   - **Type**: ssh, **Protocol**: TCP, **Port Range**: 22, **Source**: <Your IP>
1. Click `Launch Instance`
1. While waiting for the instance to boot, click on it and copy its public IPv4 address
1. Connect to your instace with the following command (note you may need to `sudo`, depending on file permissions):
   - `ssh -i <your .pem key> <user>@<instance public IPv4>`
   - `<username>` will either be `ubuntu` or `ec2-user`, depending on whether you chose Ubuntu or Amazon Linux as the image
1. Once connected, run the following commands:
   - `sudo yum install -y git`
   - `git clone https://github.com/ubsicap/assure_support_site.git`
   - `cd assure_support_site`
      - **Note**: If you chose Amazon Linux, change line ~239 of the startup script to `install_dependencies "Amazon Linux"`
   - `sh startup.sh`
1. You will be prompted to create a MySQL root password, account username, account password, and database name.
1. Once you have created credentials, the `docker-compose.yml` file will be ran and two containers will start.
1. Open your web browser to `http://<instance public IP>`
1. You will be prompted to create an administrator account for the website.
1. Once created, you can access the site through the aforementioned IP address.
