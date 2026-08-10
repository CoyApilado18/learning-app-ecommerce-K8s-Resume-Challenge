
# Context 
- Create a deployment manifests for the webapp and database along with application config using a configmap.
- Deploy the website in kubernetes in my local kubeadm using the docker image created in step 2 with the necessary env vars and database connection with the webapp. 

## NOTE: See gitub repo to build your local kubeadm cluster https://github.com/CoyApilado18/Build-your-local-Kubernetes-cluster.git with one Controlplane and one Worker node. 

# Goals
- Make sure the webapp and database deployment is wired correctly via the env var and configmaps having an up and running ecommerce site in kubernetes. 

# Commands and Notes

## -- Manually run to create objects in Kubernetes in proper order and check the objects created. --
## Create namespace
```bash
kubectl create namespace ecomwebapp
```

## Create the webapp deployment.
```bash
kubectl apply -n ecomwebapp -f db-credentials.yaml
```

## Creates the configmap that stores the db-init-script.sql 
```bash
kubectl apply -n ecomwebapp -f db-init-cm.yaml
```

## Creates the mysql-service for the db pod.
```bash
kubectl apply -n ecomwebapp -f mysql-service.yaml
```

## Creates the deployment for the db pod.
```bash
kubectl apply -n ecomwebapp -f mysql-deploy.yaml
```

## Creates the job, a one off pod that mounts the db-init-cm volume backed to the db container to run the db-init-script.sql exec at the subpath.
```bash
kubectl apply -n ecomwebapp -f db-init-job.yaml
```

## Check the deployment.
```bash
kubectl get deploy -n ecomwebapp 
kubectl describe deploy -n ecomwebapp
```

## Check the pods.
```bash
kubectl get po -n ecomwebapp
kubectl describe po -n ecomwebapp
```

## Check the configmaps.
```bash
kubectl get cm -n ecomwebapp
kubectl describe cm -n ecomwebapp
```

## Check the job.
```bash
kubectl get job -n ecomwebapp
kubectl describe job -n ecomwebapp
```

## Check the service.
```bash
kubectl get svc -n ecomwebapp
kubectl describe svc -n ecomwebapp
```

## -- Or just apply the manifests files for the directories of the ecomdb/ and ecomwebapp. --  
- ecomdb/ first then ecomwebapp second. The file names are numbered accordingly so that when applied, objects will be created in proper order.
```bash
kubectl apply -f ecomdb/
kubectl apply -f ecomwebapp/
```

## To test locally using kubectl port-forward, then open a browser and check via http://localhost:
```bash
kubectl -n ecomwebapp port-forward svc/ecom-web-svc 8080:80
```

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/29ec15a78f1189d1cbc4b6320eb34e584c32ef8a/docs/images/localhost-website.png)

