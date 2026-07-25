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

## Simulate a failure for probe with file existence check. Will patch our webapp deployment and temporarily remove the httpGet probe and replace an exec probe handler to our livenessProbe to check if a file exist in our manifest file.
- livenessProbe:
    exec:
      command:
        - cat
        - /tmp/healthy 

## Apply the patch
k apply -f website.deployment.yaml

## Wait and confirm the ecomwebapp pods stays healthy
k -n ecomwebapp get po -w

## Once healthy, exec in one of the pod and create the file
k -n ecomwebapp exec -it <pod-name> -- touch /tmp/healthy

## Observe the pods how it restarts in real time using the -w option. In this simulation I have 2 replicas of the ecomwebapp pods and I created the file to pod ecom-webapp-84db6fcbb6-vtsrk. The pod ecom-webapp-84db6fcbb6-wqssl keeps restarting until it went to CrashLoopBackOff status while the pod that I created the file doesn't. This demonstrates how Kubernetes automatically restarts unresponsive pods and delays traffic until they're ready, enhancing the applications reliability and availability. 
k -n ecomwebapp get po -w




