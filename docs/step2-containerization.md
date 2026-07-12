## Context 
- Run the Kubernetes Resume Challenge
- Using Docker (can also be Podman)
- Using Docker Hub for image registry
- Target cluster: local kubeadm 

## Goals
- Build a docker image for the PHP ecommerce webapp 
- Use official MariaDB for database setup
- Push the image to Docker Hub 
- Document all commands and decisions

## Commands and Notes

### To install docker -> https://docs.docker.com/engine/install/ubuntu/
### Image naming convention: testyoc/ecomwebapp:<tag>

### 1. Git Branch and setup
# clone the repo and go to the working directory
git clone https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge.git
cd learning-app-ecommerce-K8s-resume-challenge

# Create a new branch and switch to the new branch 
git checkout -b <your-branch-name>
git checkout <your-branch-name>

### 2. Web Application Docker Image
# Image name chosen for step 2 (Docker Hub) : '<dockerhub-username>/ecomwebapp:v1'
# create Dockerfile -> see Dockerfile in root dir where it's written in PHP. Base image:php:7.4-apache, install mysqli (connection to MySQL DB) via docker-php-install, copy the php app to /var/www/html, make sure PHP code will use mysql-service as the host 

# build and tag the image locally
docker build -t tesyoc/ecomwebapp:v1 -f Dockerfile .
# check/verify the image is built successfully. This lists local docker images, filtered to show the newly build docker image ecomwebapp with tag v1.
docker images | grep ecomwebapp
# or just the docker images command then just manually search the image with your dockerhub-username/ecomwebapp:v1
docker images

# Login to Docker hub and push the image
docker login
docker push testyoc/ecomwebapp:v1


### 3. Database Containerization
# Database Preparation
- See deployment mysql-deploy.yaml with the official MariaDB image.
- See configmap db-init-cm.yaml as this is where we will store the db-load-script.sql ecomdb database and seed data for the Producst table.




