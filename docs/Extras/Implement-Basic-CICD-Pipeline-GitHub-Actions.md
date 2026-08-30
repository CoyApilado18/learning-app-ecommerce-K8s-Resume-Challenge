# Introduction  

Welcome to my documentation of the Kubernetes challenge Extras -> [Implement Basic CI/CD Pipeline](https://cloudresumechallenge.dev/docs/extensions/kubernetes-challenge/?utm_source=substack&utm_medium=email#implement-basic-cicd-pipeline). This demo is for learning purposes only and a great way to gain a hands-on knowledge learning automation of deployments using GitHub Actions as your CI/CD Pipeline.

What's more, if you want to build your own local kubeadm homelab for your Kubernetes cluster with minimum spec requirements to test this out, feel free to clone or fork my GitHub repo [Build-your-local-Kubernetes-cluster](https://github.com/CoyApilado18/Build-your-local-Kubernetes-cluster.git). It's free and all open source. :)


# Key Concepts of GitHub Actions
![GitHub Actions](https://github.com/features/actions) is GitHub’s built-in, event-driven automation engine that lets you define CI/CD (`Build-Test-Deploy`) pipelines and other repository workflows as YAML files, which run on hosted or self-hosted runners whenever specified GitHub events occur. 

A workflow consists of:
- Events: What starts the workflow, such as a push to main/master branch.
- Jobs: Major units of work.
- Runners: Temporary virtual machines that execute jobs.
- Steps: Individual commands or reusable actions within a job.
- Actions: Reusable automation components published by GitHub or other developers.

GitHub describes a `workflow` as one or more jobs, with each job containing a sequence of steps. Each step runs in the runner environment.

# What does CI/CD means for this challenge?
- Continuous Integration, or CI, means building and validating your Docker image.
- Continuous Delivery/Deployment, or CD, means pushing the image and deploying it to Kubernetes.

# Context 
On this project, we will automate the build and deployment process using GitHub Actions.
We will create GitHub Actions workflow that automatically:
- Runs when code is pushed to main.
- Checks out our repository.
- Builds our ecommerce Docker image.
- Login to Docker Hub (as Container Registry).
- Pushes the image to Docker Hub.
- Updates your Kubernetes deployment to use the new image.
- Waits until Kubernetes confirms the rollout succeeded.  

Automates the flow above:
```bash
git push to main
        ↓
GitHub Actions starts a temporary runner
        ↓
Checkout source code
        ↓
Build Docker image
        ↓
Log in to Docker Hub
        ↓
Push image to Docker Hub
        ↓
Update Kubernetes Deployment
        ↓
Wait for rollout to complete
```

For this challenge:
- Continuous Integration (CI), means building and validating your Docker image.
- Continuous Delivery/Deployment (CD), means pushing the image and deploying it to Kubernetes.

# Goal
- GitHub Actions Workflow: Create a `.github/workflows/deploy.yml` file to build the Docker image, push it to Docker Hub, and update the Kubernetes deployment upon push to the main branch.
- Outcome: Changes to the application are automatically built and deployed, showcasing an efficient CI/CD pipeline.


# Commands and Notes
Repository structure: 
```bash 
our-repository/
├── .github/
│   └── workflows/
│       └── deploy.yml
├── Dockerfile
├── application-source/
├── kubernetes/
│   └── ecomdb/
│       └── ecomdb.yaml files
│   └── ecomwebapp/
│       └── ecomwebapp.yaml files
└── README.md
```

1. Create workflow  
- The workflow must be located at `.github/workflows`. GitHub automatically discovers workflow yaml files in this directory. Create the directory and create the `deploy.yaml` file where we will define the job to automate our CI/CD
```bash
mkdir -p .github/workflows/
touch .github/workflows/deploy.yaml
```

2. DockerHub Preparation
- Create a Docker Hub repository. ![Create a repository](https://docs.docker.com/docker-hub/repos/create/). I have an existing repo and named it `ecomwwebapp`. So my final image name is `testyoc/ecomwebapp`. 

- Create a Docker Hub access token  
Do not use your Docker Hub account password in GitHub Actions. Create a Docker Hub personal access token and give it permission to push images.  
You will use that token as a GitHub repository secret. Docker specifically documents using Docker credentials or an access token for authentication from GitHub Actions ref: ![Introduction to GitHub Actions with Docker](https://docs.docker.com/guides/gha/).  
- Log in to ![Docker Hub](https://hub.docker.com) → Click your avatar (top-right) → `Account Settings` → in the left menu, choose `Personal access tokens` →  Click `Generate new token` → Fill in:  
 - `Token name` e.g. github-actions-push  
 - `Expiration` choose a reasonable lifetime (e.g. 90 days, 1 year)  
 - `Access` push image to your own repo enable `Read & Write`. If you only need to pull, `Read` would be enough; for CI that builds and pushes, you need `Write`.  
 - Click `Generate` then copy the token immediately as you cannot view it again. Treat this token like a password. You’ll store it in GitHub Secrets, not in your repo.  

3. Store the token in GitHub repository secrets
In your GitHub repo:  
Go to `Settings` → `Secrets and variables` → `Actions`.  
Under Repository secrets, click New repository secret.

Add:  
Name: DOCKERHUB_USERNAME  
Value: <your_Docker_Hub_username>  

Name: DOCKERHUB_TOKEN  
Value: <the_PAT_you_just_copied>  

(Optionally, you can use a variable for the username and a secret only for the token, but using both as secrets is common and simple.) 

You will then use the token in a GitHub Actions workflow in `Login to Docker Hub` step. 

5. Basic workflow: build & push on every push to both branches







