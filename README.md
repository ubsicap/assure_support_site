# Assure Support Site

Repository and information for the Assure Support Site

## Table of Contents

1. [Repository Structure](#repository-structure)
1. [Overview](#overview)

## Repository Structure

Notable Repository Contents:

```sh
./
├── config/                     # Secure config info
│   └── qa-config-secure.php    # Contains MySQL credentials*
├── documentation/              # Additional documentation
├── public/                     # Question2Answer website source code (webroot)
│   ├── assets/                 # Images, audio/video files, etc.
│   ├── qa-custom-pages/        # HTML for custom pages
│   ├── qa-plugin/              # External plugins; new feature development
│   ├── qa-theme/               # Custom UI themes
│   ├── Dockerfile              # Constructs an image of the website
│   ├── DockerfileNoSSL         # Basic website image with no SSL certification
│   ├── index.php               # Initial file served by site
│   └── qa-config.php           # Sets up MySQL database
├── docker-compose.yml          # Launches all web service containers
├── ec2_user_data.sh            # User Data for the EC2 launch
└── startup.sh                  # Startup script for launching in AWS
```

`*` Removed from `public/` and referenced by `public/qa-config.php` for [security](https://docs.question2answer.org/install/security/).

## Overview

This repository holds the source code for the Assure Alliance Support Site (working title "`Ask Ebenezer`"). This project was the work of the Summer 2022 internship at SIL, by Lingxin Chen, Danny Hammer, and Daniel March.

This site uses the [Question2Answer](https://www.question2answer.org/) framework, along with several custom plugins, and lives within Docker containers intended to be hosted with Amazon Web Services.

The setup and installation for this website is a multi-step process that involves Amazon Web Services, Docker, LetsEncrypt, and DNS servers. As such, the installation process can be found on the [installation and setup](https://github.com/ubsicap/assure_support_site/tree/master/documentation/InstallationAndSetup.md) guide. Information about plugins and site settings can be found on the [configuration](https://github.com/ubsicap/assure_support_site/tree/master/documentation/Configuration.md) page. Information regarding the maintenance of the web service, once active, can be found on the [maintenance](https://github.com/ubsicap/assure_support_site/tree/master/documentation/Maintenance.md) page.
