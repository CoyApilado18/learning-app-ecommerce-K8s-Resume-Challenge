# Step 2 - Containerize Web App and Database

## Context 
- Run the Kubernetes Resume Challenge
- Using Docker (can also be Podman)
- Using GHCR instead of Docker Hub
- Target cluster: local kubeadm 

## Goals
- Build a docker image for the PHP ecommerce webapp 
- Use official MariaDB for database setup
- Push the image to GHCR 
- Document all commands and decisions

## Commands and Notes

### To install docker -> https://docs.docker.com/engine/install/ubuntu/

### 1. Git Branch and setup
# clone the repo and go to the working directory
git clone https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge.git
cd learning-app-ecommerce-K8s-resume-challenge

# Create a new branch and switch to the new branch 
git checkout -b <your-branch-name>
git checkout <your-branch-name>

### 2. Web Application Docker Image
# create Dockerfile



### 3. Database Containerization
