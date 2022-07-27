# Local Development

This document is a guide on how to set up the web server on a local machine for development or testing.

## Table of Contents

1. [Before you begin](#before-you-begin)
1. [Getting Started](#getting-started)

## Before you begin

The local development environment is vastly different than the production environment in terms of setup.
Once setup, the environments differ only in three major aspects:

-   Local deployments include a local database running in a Docker container.
-   Local deployments lack SSL certification (this can be rectified, but it is a [complicated process](https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-apache-in-ubuntu-20-04)).
-   Local deployments do not have a custom domain name.

A local deployment is necessary during the ETL process to ensure that modified data does not break the site.

## Getting Started

1. Ensure that your system is running the latest version of Docker with support for Docker Compose
1. Clone the contents of this repository to your machine.
   `git clone https://github.com/ubsicap/assure_support_site.git`
1. Set the credentials for MySQL access. You will need to change their existing values.
    - In `./config/qa-config-secure.php`:
        - `QA_MYSQL_HOSTNAME`
            - Must be set to the name of the DB container defined in `local-compose.yml` (default `q2a-db`)
        - `QA_MYSQL_USERNAME`
            - Matches the `MYSQL_USER` environment variable below
        - `QA_MYSQL_PASSWORD`
            - Matches the `MYSQL_PASSWORD` environment variable below
        - `QA_MYSQL_DATABASE`
            - Matches the `MYSQL_DATABASE` environment variable below
    - In `./local-compose.yml`:
        - `MYSQL_ROOT_PASSWORD`
        - `MYSQL_DATABASE`
        - `MYSQL_USER`
        - `MYSQL_PASSWORD`
1. Launch the containers with the following command:
   `docker compose -f local-compose.yml up -d`
1. Open your web browser to `localhost`.
1. You will be prompted to create a "Super Administrator" account for the website. This is different than the administrator account for the database, but the same credentials may be used.
1. Once created, you will be brought to the site's homepage.

You can access the database through [MySQL Workbench](https://www.mysql.com/products/workbench) with the following credentials:

-   **Hostname**: `localhost` or `127.0.0.1`
-   **Port**: `9906`
-   **Username**: `root`
-   **Password**: The value you used for `MYSQL_ROOT_PASSWORD` in `local-compose.yml`
