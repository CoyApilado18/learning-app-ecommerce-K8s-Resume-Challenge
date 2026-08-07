# Context  
- Automate scaling based on CPU usage to handle unpredictable traffic spikes, utilize the Kubernetes feature Horizontal Pod Autoscaler (HPA).

# Goals  
- Implement HPA: Create a Horizontal Pod Autoscaler targeting 50% CPU utilization, with a minimum of 2 and a maximum of 10 pods.
- Apply HPA: Execute kubectl autoscale deployment ecom-web --cpu-percent=50 --min=2 --max=10.
- Simulate Load: Use a tool like Apache Bench to generate traffic and increase CPU load.
- Monitor Autoscaling: Observe the HPA in action with kubectl get hpa.
- Outcome: The deployment automatically adjusts the number of pods based on CPU load, showcasing Kubernetes’ capability to maintain performance under varying loads.

# Comamnds and Notes
### Horizontal POD Autoscaling (HPA) - is a Kubernetes feature to automate scale-out and scale-in of pods of a workload resource (Deployments or StatefulSet) depending on the metrics (cpu, memory utilization or any other custom metric) to match demand. In Kubernetes terms, Horizontal scaling means that the response to increased load is to deploy more pods. This is different from Vertical scaling which for Kubernetes is to add more resources (eg: cpu, memory) to the pods that are already running for the workload. 
### The HPA Controller periodically queries the metrics api and adjust the replicas field of a target resource to bring the current metric value closer to the target value you defined. The HPA calculate the utilization (cpu and memory of resourceRequests in the containers) as percentage of this requests. Without them the HPA cannot function.
### Reference:  
- https://kubernetes.io/docs/concepts/workloads/autoscaling/horizontal-pod-autoscale/ 

## For HPA to work properly, a cluster should have a metric source such as a Metric Server; otherwise 'kubectl autoscale' can create the HPA object but it will not have the metrics it needed for scaling decisions. As a prerequisite we'll install the Metric Server first.  

- Install Metric Server and you'll more likely to get an error for self hosted cluster when installing the metrics-server. The error means it's trying to securely verify self-signed certificates of the nodes by the kubelets which it cannot do by default. 
```bash
k apply –f https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml
```
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/install-metric-server-error.png)

- To address this, edit the metrics-server pod in the kube-system ns and add the "--kubelet-insecure-tls".
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/kubelet-insecure-tls.png)

## Create an HPA object. 
- To easily create the HPA manifest, you can --dry-run=client then output to yaml file then just edit the apply. See 03-hpa-ecomwebapp.yaml in the ecomwebapp/
```bash
k -n <namespace> austoscale deployment <name-of-deployment> --cpu-percent=CPU --min=<desired-min-num-of-replicas> --max=<desired-max-num-of-replicas> --dry-run=client-oyaml > some-name.yaml
k -n ecomwebapp autoscale deployment ecom-webapp --cpu-percent=50 --min=2 --max=10 --dry-run=client -oyaml > ecomwebapp-hpa.yaml
k apply -f hpa.yaml
```
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/hpa-created.png)

## There should be a requests and limits for the cpu resource in your deployment yaml file. See 01-website-deployment.yaml (spec.containers.resources.requests.limits). Run the jsonpath command to make sure the deployment have the resources requests and limits block.
```bash
k -n <namespace> get pod <pod-name> -o jsonpath='{.spec.containers[0].containers}'
```
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/requests-limits-jsonpath.png)

## Simulate load to test using Apache Bench. 
- Install Apache Bench and check if successfully installed.
```bash
sudo apt update && sudo apt install apache2-utils
ab -V 
```

## Make sure you open tunnel for portforward to access the ecomwebapp pod via the service. Open another terminal the run
```bash
k -n <namespace> port-forward svc/<service-name> 8080:<service-port>
k -n ecomwebapp port-forward svc/ecom-web-svc 8080:80
```

## Run load test, generate load and see it scale. Keep the url ending in / otherwise Apache Bench can fail without it. Where -n = total requests and -c = concurrent request at a time. 
```bash
ab -n 2000 -c 150 http://localhost:8080/
```
- So at 150 concurrent request hitting our website @ 24.26 request per second, we saw that at 61% cpu utilization which went over bounds of our targetCPUUtilizationPercentage: 50%, the ecom-webapp-hap in reference to ecom-webapp deployment scaled-out an addtional (1) pod (see REPLICAS column). This shows how workload in Kubernetes automatically scale to match the demand. And as you can see, when the cpu utilization had gone below the threshold of 50%, the pod scaled-in to the minimum desired replicas of 2. 
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/HPA-result.png)

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/199fc1d8c447d4aaeafadbfcec34584344bb4089/docs/images/hpa-triggered-pod-scaled-in.png)

