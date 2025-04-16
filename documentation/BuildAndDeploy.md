# Support.Bible Maintenance

## Repo Information

[Github repository](https://github.com/ubsicap/assure_support_site/)

Support.Bible is built on the Question2Answer web framework. You can visit its official website to learn about the code structure and plugins. Additionally, there is a dedicated help website where you can explore common questions and issues encountered by other users while building their websites.
Q2A Links: 
- [Question2Answer website](https://www.question2answer.org/)
- [Question2Answer help website](https://www.question2answer.org/qa/)

## Kubernetes/Rancher Information

SIL has two separate Kubernetes clusters that we use for staging and production of the support.bible site. Staging is running on servers in Dallas, while production is running on servers in AWS. Rancher is a web tool to manage a kubernetes enviornment. Below are the two clusters, and their related contexts and rancher links:
- Staging:
    - context: `dallas-stage` (formerly `dallas-rke`)
    - link: [https://rancher.languagetechnology.org/](https://rancher.languagetechnology.org/)
- Production:
    - context: `aws-prod` (formerly `aws-rke`)
    - link: [https://control.languagetechnology.org/](https://control.languagetechnology.org/)
> NOTE: You need WireGuard running and active for you to access the rancher enviornments.

## Setup:
1. Download github repository
```sh
git clone git@github.com:ubsicap/assure_support_site.git
```
2. Replace the `Dockerfile` shared with you under the `/public` directory.
> NOTE: Do not push this change into the public repo.
3. Put the `google-auth.json` file shared with you into the `/public/qa-plugin/sso-authentication` directory.
> NOTE: Do not add this file to the public repo.

## Redeploy:
Do this after you make a code change to the repository.

### 1: Ensure WireGuard is active
1. Open WireGuard and click `Activate`. The `Status` should display a green indicator with `"Active"`.
2. If an error message appears, click `OK` to close the error box and try clicking `Activate` again. Repeat this process until the `Status` successfully changes to `"Active"`.

### 2: Build and Push a Docker Image
1. In a terminal, navigate to the `/public` directory.
2. Build the docker image and push the new image to Docker Hub using these commands:
```sh
docker build -t lingxinchen/sb-php:3.55
docker push lingxinchen/sb-php:3.55
```
> NOTE: Be sure to replace the image version in this example (3.55) with the latest version +1 (e.g. 3.56, 3.57, etc.) for subsequent updates.

### 3: Deploy to Staging or Production
1. Go to the project root directory by running `cd ..`.
2. Run the following command to ensure you are using the correct context:
```sh
kubectl config get-contexts
```
> NOTE: For staging or production, ensure you are on `dallas-stage` or `aws-prod` respectively.
3. If you are using the wrong configuration, be sure to run the following command to change contexts:
```sh
kubectl config use-context dallas-stage
```
4. Run the following command to redeploy to the kubernetes cluster:
```sh
kubectl apply -f supportbible-deployment.yaml
```
5. Check for any error in Rancher

## Connect to DB:
1. Download and install MySQL Workbench
2. Create new connection
    - Hostname: support-bible.c5c5cgu5xuyk.us-east-1.rds.amazonaws.com
    - Port: 3306
    - Username: admin
    - Password: Use the value of `DB_KEY` specified in the Dockerfile that was shared with you.
