# Introduction  

Welcome to my documentation of the Kubernetes challenge Extras -> [Implement Basic CI/CD Pipeline](https://cloudresumechallenge.dev/docs/extensions/kubernetes-challenge/?utm_source=substack&utm_medium=email#implement-basic-cicd-pipeline). This demo is for learning purposes only and a great way to gain a hands-on knowledge learning automation of deployments using GitHub Actions as your CI/CD Pipeline.

What's more, if you want to build your own local kubeadm homelab for your Kubernetes cluster with minimum spec requirements to test this out, feel free to clone or fork my GitHub repo [Build-your-local-Kubernetes-cluster](https://github.com/CoyApilado18/Build-your-local-Kubernetes-cluster.git). It's free and all open source. :)


# Key Concepts of GitHub Actions
[GitHub Actions](https://github.com/features/actions) is GitHub’s built-in, event-driven automation engine that lets you define CI/CD (`Build-Test-Deploy`) pipelines and other repository workflows as YAML files, which run on hosted or self-hosted runners whenever specified GitHub events occur. 

A `workflow` consists of:
- `Events`: What starts the workflow, such as a push to main/master branch.
- `Jobs`: Major units of work.
- `Runners`: Temporary virtual machines that execute jobs.
- `Steps`: Individual commands or reusable actions within a job.
- `Actions`: Reusable automation components published by GitHub or other developers.

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
You edit code locally
        ↓
git push origin k8s-ecomwebapp
        ↓
GitHub detects the push
        ↓
GitHub assigns the job to your Ubuntu self-hosted runner
        ↓
Runner checks out the pushed commit
        ↓
Runner builds a Docker image
        ↓
Runner pushes it to Docker Hub
        ↓
Runner runs kubectl against your local cluster
        ↓
Kubernetes performs a rolling update
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

4. Kubernetes requirements
Before the deployment portion can work, Kubernetes must be reachable from the GitHub Actions runner.
A local cluster running on your Ubuntu VM is not normally reachable from a GitHub-hosted runner. Therefore, you need one of these approaches:  
1. A public cloud cluster such as EKS, GKE, or AKS.  
2. A self-hosted GitHub Actions runner inside your network.  
3. A securely exposed Kubernetes API endpoint.  

We'll go with option 2 (A self-hosted GitHub Actions runner inside your network.) since I have my k8s cluster running on my local Ubuntu test environment. I will have a separate demo for option 1 -using a public cloud cluster AWS EKS. 

5. Option 2 (A self-hosted GitHub Actions runner inside your network.) High-level overview:  
5.1 Create a self-hosted runner on your Ubuntu VM.  
5.2 Ensure the runner can:  
- Run Docker (build/push images).
- Run kubectl against your local cluster.
5.3 Point your GitHub workflow to use this runner.  
5.4 Test the full pipeline:  
- Push to main → build image → push to Docker Hub → update local k8s Deployment.

6. Prerequisites on your Ubuntu VM
On the Ubuntu machine that hosts your k8s cluster, ensure you have:
- Docker installed and working. Verify:
```bash
docker --version
docker run hello-world
```
- `kubectl` configured and able to talk to your cluster. 
```bash
kubectl version --client
kubectl get nodes
kubectl get pods -A
```
- Network access to GitHub (HTTPS).
- A user account that will run the runner (e.g. ubuntu or a dedicated runner user).  

If `kubectl get nodes` works, your kubeconfig is already set up for that user.

7. Create a self-hosted runner in GitHub
Do this in your GitHub repo:

Go to your repository on GitHub.

Click Settings (top tab).

In the left sidebar, click Actions → Runners.

Click New self-hosted runner.

Choose:

OS: Linux

Architecture: x64 (most likely for your Ubuntu VM)

GitHub will show commands below, this is just a sample commands.

NOTE: YOU SHOULD CREATE THE actions-runner/ OUTSIDE YOUR GIT PROJECT DIRECTORY. THE SELF-HOSTED RUNNER IS MACHINE INFRASTRUCTRUCTURE, NOT SOURCE CODE, SO IT SHOULD NOT LIVE INSIDE THE REPOSITORY YOU COMMIT AND PUSH. THIS DIRECTORY WILL CONTAIN LARGE FILES WHEN YOU THE RUNNER IS RUN AND WHEN YOU START RUNNING THE WORKFLOW.

```bash
mkdir actions-runner && cd actions-runner
curl -O -L https://github.com/actions/runner/releases/download/v2.322.0/actions-runner-linux-x64-2.322.0.tar.gz
tar xzf actions-runner-linux-x64-2.322.0.tar.gz
./config.sh --url https://github.com/YOUR-ORG/YOUR-REPO --token YOUR_TOKEN
./run.sh
```
GitHub’s docs describe this exact process for registering a self-hosted runner.

8. Install and configure the runner

Run the commands GitHub gives you on your Ubuntu VM. 
- Download:
![alt image](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/b9c2ad3fa61fc49e9769042b22ab99a70e12c424/docs/images/selfhosted-runner-download-cmd.png)

- Configure:
![alt image](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/b9c2ad3fa61fc49e9769042b22ab99a70e12c424/docs/images/selfhosted-runner-configure-cmd.png.png)

GitHub’s runner will now appear in Settings → Actions → Runners as “Online”.

Then start the runner. Leave this running in a terminal
```bash
./run.sh
```
Or  
Set it up as a `systemd service`.  
From the runner directory:
```bash
sudo ./svc.sh install
sudo ./svc.sh start
```
Check status
```bash
sudo ./svc.sh status
```

9. Make sure the runner user can use Docker and kubectl
The runner process runs as the user that started it. That user must:
- Be able to run docker without `sudo`. [Docker without sudo](https://docs.docker.com/engine/install/linux-postinstall/)
- Have a valid kubeconfig in `~/.kube/config`.

kubectl access  
- Ensure that when you run `kubectl get nodes` as the same user that will run the runner, it works.
- If your kubeconfig is in a non-standard location, either:
  - Move/copy it to ~/.kube/config, or  
  - Set KUBECONFIG in your shell profile (e.g. ~/.bashrc): 
  ```bash
  export KUBECONFIG=/path/to/your/kubeconfig
  ```
  then reload:
  ```bash
  source ~/.bashrc
  kubectl get nodes
  ```

The runner inherits this environment when started from an interactive shell. For a systemd service, you’ll set `Environment=`lines. 

