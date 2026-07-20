# Context
- Scale our deployment as load increases and monitor scaling out of the mnumber of pods. 

# Goals
- To show how easy it is to scale our deployment as load increases and observe/monitor scaling out the number of pods.

# Commands and Notes
## Check current number of pods
k -n ecomwebapp get po

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/1b95a39ca7d20b14e34d00e0ed62953555f9369e/docs/images/current-number-of-pods.png)

## Scale out number of pods (--replicas=6)of the ecomwebapp deployment to handle increase load.
k -n ecomwebapp scale deploy/ecom-webapp --replicas=6

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/k8s-ecomwebapp/docs/images/scaleout-number-of-pods.png?raw=true)

## Monitor scaling to observe deployment scaling out
k -n ecomwebapp get po -w 

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/k8s-ecomwebapp/docs/images/monitor-scalingout-number-of-pods.png?raw=true)

