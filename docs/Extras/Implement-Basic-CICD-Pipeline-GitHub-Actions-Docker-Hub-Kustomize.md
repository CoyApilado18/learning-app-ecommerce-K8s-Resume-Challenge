# Introduction  

Welcome to my documentation of the Kubernetes challenge Extras -> [Implement-Basic-CICD-Pipeline-GitHub-Actions-Docker-Hub-Kustomize](https://cloudresumechallenge.dev/docs/extensions/kubernetes-challenge/?utm_source=substack&utm_medium=email#implement-basic-cicd-pipeline). This demo is for learning purposes only and a great way to gain a hands-on knowledge learning automation of deployments using GitHub Actions as your CI/CD Pipeline.

What's more, if you want to build your own local kubeadm homelab for your Kubernetes cluster with minimum spec requirements to test this out, feel free to clone or fork my GitHub repo [Build-your-local-Kubernetes-cluster](https://github.com/CoyApilado18/Build-your-local-Kubernetes-cluster.git). It's free and all open source. :)

# Overview
This project implements the Basic CI/CD Pipeline portion of the Kubernetes Resume Challenge using GitHub Actions, Docker Hub, a self-hosted GitHub Actions runner, and Kustomize.
The pipeline is intentionally deployed to an isolated local Kubernetes environment:
Git branch:         cicd-kustomize
Kubernetes namespace: cicd-test
Container registry:  Docker Hub
Runner location:     Local Ubuntu Kubernetes control-plane/test machine
Deployment method:   Kustomize + `kubectl apply -k`

This approach preserves the existing Helm-managed environment in the `helm` namespace while providing a separate environment for CI/CD testing and learning. Checkout my github repo for [Helm](https://github.com/CoyApilado18/k8s-Helm).

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
We will create GitHub Actions workflow that automates everything according to the architecture below.  

Architecture
```bash
Developer pushes code to cicd-kustomize
                 |
GitHub Actions workflow starts
                 |
Self-hosted runner on local Ubuntu machine receives job
                 |
Checkout repository source and Kubernetes manifests
                 |
Build Docker image with Docker Buildx
                 |
Push commit-tagged image and latest image to Docker Hub
                 |
Create temporary copy of Kustomize manifests
                 |
Replace image placeholder with current Git commit SHA
                 |
Render and server-side validate Kustomize configuration
                 |
                 
Delete previous database initialization Job
                 |
kubectl apply -k to cicd-test overlay
                 |
Wait for MySQL, web application, and database initialization Job

```

# Why a self-hosted runner
The Kubernetes cluster runs locally on an Ubuntu test machine. A GitHub-hosted runner cannot normally access a Kubernetes API server running only inside a local/private network.
A self-hosted runner solves this by running the GitHub Actions job on the same Ubuntu machine that already has access to:
• The local Docker daemon.
• kubectl.
• The Kubernetes kubeconfig.
• The local Kubernetes control plane.
• The private network where the test cluster runs.
The workflow uses these runner labels:
`runs-on: [self-hosted, local-k8s]`

`self-hosted` identifies a runner managed locally instead of a GitHub-hosted virtual machine.  
`local-k8s` is a custom label used to ensure this workflow is routed to the local Kubernetes-capable runner.

# Goal
This implementation satisfies the core CI/CD goal:
```bash
Push to Git branch
        |
        v
GitHub Actions runs on local self-hosted runner
        |
        v
Docker image is built and pushed to Docker Hub
        |
        v
Kustomize renders the cicd-test environment with an immutable commit image tag
        |
        v
Kubernetes resources are applied and verified automatically
```

The result is an isolated, repeatable local Kubernetes CI/CD environment that demonstrates container image automation, Docker Hub integration, Kustomize overlays, static persistent storage, Kubernetes rollout validation, and GitHub Actions self-hosted runner usage.



# Commands and Notes

### DockerHub Preparation
- Create a Docker Hub repository. ![Create a repository](https://docs.docker.com/docker-hub/repos/create/). I have an existing repo and named it `ecomwwebapp`. So my final image name is `testyoc/ecomwebapp`. 

- Create a Docker Hub access token  
Do not use your Docker Hub account password in GitHub Actions. Create a Docker Hub personal access token and give it permission to push images.  
You will use that token as a GitHub repository secret. Docker specifically documents using Docker credentials or an access token for authentication from GitHub Actions ref: ![Introduction to GitHub Actions with Docker](https://docs.docker.com/guides/gha/).  
- Log in to ![Docker Hub](https://hub.docker.com) → Click your avatar (top-right) → `Account Settings` → in the left menu, choose `Personal access tokens` →  Click `Generate new token` → Fill in:  
 - `Token name` e.g. github-actions-push  
 - `Expiration` choose a reasonable lifetime (e.g. 90 days, 1 year)  
 - `Access` push image to your own repo enable `Read & Write`. If you only need to pull, `Read` would be enough; for CI that builds and pushes, you need `Write`.  
 - Click `Generate` then copy the token immediately as you cannot view it again. Treat this token like a password. You’ll store it in GitHub Secrets, not in your repo.  

### Store the token in GitHub repository secrets
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

### Create a self-hosted runner on your Ubuntu VM.  
Ensure the runner can:  
- Run Docker (build/push images).
- Run kubectl against your local cluster.
- Point your GitHub workflow to use this runner.  
- Test the full pipeline:  
`Push to main → build image → push to Docker Hub → update local k8s Deployment.`

### Prerequisites on your Ubuntu VM
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

### Create a self-hosted runner in GitHub
Do this in your GitHub repo:

Go to your repository on GitHub.

Click Settings (top tab).

In the left sidebar, click Actions → Runners.

Click New self-hosted runner.

Choose:

OS: Linux

Architecture: x64 (most likely for your Ubuntu VM)

GitHub will show commands below, this is just a sample commands.

NOTE: YOU SHOULD CREATE THE actions-runner/ OUTSIDE YOUR GIT PROJECT DIRECTORY. THE SELF-HOSTED RUNNER IS MACHINE INFRASTRUCTRUCTURE, NOT SOURCE CODE, SO IT SHOULD NOT LIVE INSIDE THE REPOSITORY YOU COMMIT AND PUSH. THIS DIRECTORY WILL CONTAIN LARGE FILES WHEN THE RUNNER IS RUN AND WHEN YOU START RUNNING THE WORKFLOW.

```bash
mkdir actions-runner && cd actions-runner
curl -O -L https://github.com/actions/runner/releases/download/v2.322.0/actions-runner-linux-x64-2.322.0.tar.gz
tar xzf actions-runner-linux-x64-2.322.0.tar.gz
./config.sh --url https://github.com/YOUR-ORG/YOUR-REPO --token YOUR_TOKEN
./run.sh
```
GitHub’s docs describe this exact process for registering a self-hosted runner.

### Install and configure the runner

Run the commands GitHub gives you on your Ubuntu VM. 
- Download:
![alt image](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/b9c2ad3fa61fc49e9769042b22ab99a70e12c424/docs/images/selfhosted-runner-download-cmd.png)

- Configure:
![alt image](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/b9c2ad3fa61fc49e9769042b22ab99a70e12c424/docs/images/selfhosted-runner-configure-cmd.png.png)

GitHub’s runner will now appear in Settings → Actions → Runners as “Online”.

Then start the runner. Leave this running in another terminal
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

### Make sure the runner user can use Docker and kubectl
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


### Create workflow  
- The workflow must be located at `.github/workflows`. GitHub automatically discovers workflow yaml files in this directory. Create the directory and create the `deploy-kustomize.yaml` file where we will define the job to automate our CI/CD
```bash
mkdir -p .github/workflows/
touch .github/workflows/deploy-kustomize.yaml
```

### Workflow behavior
The filename can be any descriptive .yml or .yaml filename under .github/workflows/. The top-level name: field controls the display name in the GitHub Actions interface.
The workflow performs the following steps:
1. Checks out the commit that triggered the run.
2. Verifies Docker, kubectl, and cluster access on the self-hosted runner.
3. Creates a seven-character image tag from GITHUB_SHA.
4. Logs in to Docker Hub using GitHub Actions secrets.
5. Configures Docker Buildx.
6. Builds and pushes the application image to Docker Hub.
7. Copies the kubernetes/ directory to the runner's temporary directory.
8. Replaces newTag: placeholder in the temporary Kustomize overlay with the current commit tag.
9. Renders the Kustomize overlay with kubectl kustomize.
10. Validates the generated manifests with server-side dry run.
11. Deletes the previous db-init-job if it exists.
12. Applies the Kustomize overlay to the local cluster.
13. Waits for MySQL Deployment rollout.
14. Waits for web application Deployment rollout.
15. Waits for database initialization Job completion.
16. Prints deployment, Pod, Service, PVC, PV, and image status.


### Useful verification commands
Check all workload resources in the CI/CD namespace:
kubectl get all -n cicd-test

Check static storage:
```bash
kubectl get pvc -n cicd-test
kubectl get pv cicd-test-mysql-pv
```

Check rollout status:
```bash
kubectl rollout status deployment/mysql \
  -n cicd-test \
  --timeout=180s
```  

```bash
kubectl rollout status deployment/ecom-webapp \
  -n cicd-test \
  --timeout=180s
```

Check the deployed image:
```bash
kubectl get deployment ecom-webapp \
  -n cicd-test \
  -o jsonpath='{.spec.template.spec.containers[0].image}{"\n"}'
```

Check application readiness from a web Pod:
```bash
kubectl exec -it deployment/ecom-webapp \
  -n cicd-test \
  -- sh -c 'echo "$DB_HOST"; wget -qO- http://127.0.0.1/readyz.php'
```

Inspect failing Pods:
```bash
kubectl get pods -n cicd-test
kubectl describe pod <pod-name> -n cicd-test
kubectl logs <pod-name> -n cicd-test
```

### Security notes
Docker Hub credentials are stored as GitHub Actions secrets. They are not committed into the repository.
The current database Secret manifest uses Base64-encoded values. Base64 is encoding, not encryption, and it should not be treated as a production secret-management solution.


### Future improvements
After validating this local implementation, future enhancements include:
• Move the deployment trigger from `cicd-kustomize` to `master` through an intentional pull request and branch strategy.
• Create separate `development`, `staging`, and `production` Kustomize overlays.
• Deploy the same architecture to Amazon EKS.
• Replace the local self-hosted runner approach with an EKS-compatible runner or GitHub-hosted runner using AWS OIDC.
• Use AWS Secrets Manager, Vault, or External Secrets Operator for database credentials.
• Add automated application tests before Docker image publishing.
• Add image vulnerability scanning.
• Add Kustomize validation and policy checks before deployment.
• Pin third-party GitHub Actions to full commit SHAs for supply-chain hardening.
• Add GitOps reconciliation and pruning with Argo CD or Flux.
• Replace the database initialization Job with a migration strategy suitable for repeated deployments.
