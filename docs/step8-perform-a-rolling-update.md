# Context
- Update the website to include a new promotional banner for the marketing campaign and perform a rolling update. 

# Goals
- Perform a rolling update of the ecomwebapp deployment succesfully with 0 downtime and watch the rolling update process.

# Commands and Notes
- Added a piece of code for the promotional banner in the index.php code -see <!-- Promotional Banner --> and <style> block .promo-banner

## Before building the image, run the command below to test the banner using PHP's built-in web server from the root directory where the index.php code resides. then open http://localhost:8080/index.php
php -S 0.0.0.0:8080
## if php cli not installed, you may need to install it first and in my ubuntu2.24 machine I ran the cmd below
sudo apt install php8.1-cli

## You can also test the banner in an existing ecomwebapp pods by copying the index.php with the banner code in the pods.
k -n ecomwebapp cp ./index.php <ecom-webapp-pod-name>:/var/www/html/index.php

## Once tested, build and tag the new image and push to docker hub 
docker build -t testyoc/ecomwebapp:v3 -f Dockerfile .

## Check the image once built you should see the new image <yourdockerhub-username/image-name:tag>
docker images 

## Push to docker hub
docker push <yourdockerhub-username/image-name:tag>

## Rollout the new image, change the ecomwebapp deployment's image with the new version of the image (spec.containers.image: testyoc/ecomwebapp:v3) then kubectl apply cmd with the --record=true flag so that k8s stores a short note in the history such as the cmd that trigerred the rollout. We'll go with this as this is what task suggest in this step
k -n ecomwebapp apply -f website-deployment.yaml --record=true

## Restart the ecomwebapp pods for changes to kick in. since the pods were deleted, you'll need to run the kubectl port-forward again to open the tunnel for localhost then open http://localhost:8080
k -n ecomwebapp delete po -l app=ecom-webapp
k -n ecomwebapp port-forward svc/ecom-web-svc 8080:80

## Or you can also do the rollout by imperative command using kubectl set image cmd. you can also use '--dry-run=client -oyaml' to get a preview of the deployment without executing the changes imemdiately. With the --record=true flag, k8s stores a short note in the history such as the cmd that trigerred the rollout.
k -n ecomwebapp set image deploy ecom-webapp ecom-webapp=testyoc/ecomwebapp:v3 --record=true
k -n ecomwebapp set image deploy ecom-webapp ecom-webapp=testyoc/ecomwebapp:v3 --dry-run=client -oyaml

## Watch the rolling update process and check history where there 
k -n ecomwebapp rollout status deploy ecom-webapp
sample output:
deployment "ecom-webapp" successfully rolled out
k -n ecomwebapp rollout history deploy ecom-webapp
sample output:
deployment.apps/ecom-webapp 
REVISION  CHANGE-CAUSE
1         <none>
2         <none>

## Once applied, you can check the new deployment and check the image section in which it should show the new image
k -n ecomwebapp describe deploy ecom-webapp 

## You can also check the changes inside the container if successful using curl cmd with the output 200 OK status
## exec in the container 
k -n ecomwebapp exec -it <pod-name> -- sh
## inside the container run curl cmd
curl -I http://localhost 
## sample output:
HTTP/1.1 200 OK
Date: Mon, 13 Jul 2026 21:54:50 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/7.4.33
Content-Type: text/html; charset=UTF-8
