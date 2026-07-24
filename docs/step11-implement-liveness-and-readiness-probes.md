# Context
- Implement health checks; a Liveness and Readiness Probes to ensure our webapp is restarted if it becomes unresponsive and doesn't receive traffic until ready.
- livenessProbe - checks if the app in the container is alive.
- readinessProbe - checks if the the app in the container is ready to receive traffic.

# Goals
- Add liveness and readiness probes to our deployment manifest targeting the endpoint of our ecomwebapp that confirms its operational status.
- Create a target endpoint (healthz.php and readyz.php) in the application to confirm its operational status.
- Create a test to simulate failure scenario and observe how Kubernetes respond to it where Kubernetes auto restart unresponsive pods and delay traffic to newly started pods until ready. 

# Commands and Notes
## In the website-deployment.yaml I added a section for readinessProbe and livenesProbe for HTTP GET request with the values below.
- The kubelet waits initialDelaySeconds before performing each probe type and then runs the periodSeconds which the interval for each probe. If the probe does not complete within the timeoutSeconds, the probe is counted as a failure. Then if the failureThreshold consecutive times, the readinessProbe marks the pod Not Ready and the livenessProbe can cause the container to be restarted. 
        readinessProbe:
          httpGet:
            path: /readyz.php
            port: 80
          initialDelaySeconds: 10
          periodSeconds: 5
          timeoutSeconds: 5
          failureThreshold: 6
        livenessProbe:
          httpGet:
            path: /healthz.php
            port: 80
          initialDelaySeconds: 15
          periodSeconds: 10
          timeoutSeconds: 2
          failureThreshold: 3

## Apply the readiness and liveness probes 
k apply -f website-deployment.yaml 

## Simulate a failure for probe with file existence check. Will pach the our webapp deployment and temporarily remove the httpGet probe and replace an exec probe to our livenessProbe to check if a file exist in our manifest file.
- livenessProbe:
    exec:
      commandd:
        - cat
        - /tmp/healthy 

## Apply the patch
k apply -f website.deployment.yaml

## Wait and confirm the ecomwebapp pods stays healthy
k -n ecomwebapp get po -w

## If you want you can check the Events section of the pod and you should see an output:
k -n ecomwebapp describe po <pod-name>
Events:
  Type     Reason     Age                 From               Message
  ----     ------     ----                ----               -------
  Normal   Scheduled  2m8s                default-scheduler  Successfully assigned ecomwebapp/ecom-webapp-84db6fcbb6-5zlpj to node01
  Normal   Killing    38s (x2 over 88s)   kubelet            Container ecom-webapp failed liveness probe, will be restarted
  Normal   Pulled     34s (x3 over 2m7s)  kubelet            Container image "testyoc/ecomwebapp:v2" already present on machine
  Normal   Created    33s (x3 over 2m7s)  kubelet            Created container: ecom-webapp
  Normal   Started    33s (x3 over 2m7s)  kubelet            Started container ecom-webapp
  Warning  Unhealthy  33s (x3 over 83s)   kubelet            Readiness probe failed: Get "http://192.168.196.173:80/readyz.php": dial tcp 192.168.196.173:80: connect: connection refused
  Warning  Unhealthy  8s (x8 over 108s)   kubelet            Liveness probe failed: cat: /tmp/healthy: No such file or directory

## You can also check the status of the ecomwebapp pods and check the RESTARTS column
k -n ecomwebapp get po

## Once healthy, exec in one of the pod and create the file
k -n ecomwebapp exec -it <pod-name> -- touch /tmp/healthy

## Observe the pods how it restarts in real time using the -w option
k -n ecomwebapp get po -w

## In this example I have 2 replicas of the ecomwebapp pods and I created the file to pod ecom-webapp-84db6fcbb6-vtsrk. The pod ecom-webapp-84db6fcbb6-wqssl keeps restarting until it went to CrashLoopBackOff status while the pod that I created the file doesn't. This demonstrates how Kubernetes automatically restarts unresponsive pods and delays traffic until they're ready, enhancing the applications reliability and availability. 





