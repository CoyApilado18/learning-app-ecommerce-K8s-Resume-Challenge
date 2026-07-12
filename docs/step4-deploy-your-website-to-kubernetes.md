
## Context 
- Create deployment manifests for the webapp and database along with application config using a configmap.
- Deploy the website in kubernetes in my local kubeadm using the docker image created in step 2 with the necessary env vars and database connection with the webapp. 

## Goals
- Make sure the webapp and database deployment is wired correctly via the env var and configmaps having an up and running ecommerce site in kubernetes. 

## Commands and Notes
# create namespace
kubectl create namespace ecomwebapp

# create the webapp deployment 
kubectl apply -n ecomwebapp -f db-credentials.yaml

# creates the configmap that stores the db-init-script.sql 
kubectl apply -n ecomwebapp -f db-init-cm.yaml

# creates the mysql-service for the db pod
kubectl apply -n ecomwebapp -f mysql-service.yaml

# creates the deployment for the db pod
kubectl apply -n ecomwebapp -f mysql-deploy.yaml

# creates the job, a one off pod that mounts the db-init-cm volume backed to the db container to run the db-init-script.sql exec at the subpath
kubectl apply -n ecomwebapp -f db-init-job.yaml

# check the deployment 
kubectl get deploy -n ecomwebapp 
kubectl describe deploy -n ecomwebapp

# check the pods
kubectl get po -n ecomwebapp
kubectl describe po -n ecomwebapp

# check the configmaps
kubectl get cm -n ecomwebapp
kubectl describe cm -n ecomwebapp

# check the job
kubectl get job -n ecomwebapp
kubectl describe job -n ecomwebapp

# check the service
kubectl get svc -n ecomwebapp
kubectl describe svc -n ecomwebapp

# to test locally using kubectl port-forward, then open a browser and check via http://localhost:
kubectl -n ecomwebapp port-forward svc/ecom-web-svc 8080:80