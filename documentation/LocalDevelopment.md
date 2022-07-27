# Local Development

This document is a guide on how to set up the web server on a local machine for development or testing.

## Table of Contents

1. [Before you begin](#before-you-begin)
1. [Getting Started](#getting-started)

## Before you begin

The local development environment is vastly different than the production environment in terms of setup.
Once setup, the environments differ only in three major aspects: - Local deployments include a local database running in a Docker container. - Local deployments lack SSL certification (this can be rectified, but it is a [complicated process](https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-apache-in-ubuntu-20-04)). - Local deployments do not have a custom domain name.

## Getting Started

1. Ensure that your system is running the latest version of Docker with support for Docker Compose
