
DB_HOST = localhost
DB_USER = ecomuser
DB_PASSWORD = ecompassword
DB_NAME = ecomdb


# create secret object first in ecomwebapp namespace
k -n ecomwebapp create secret generic db-credentials --from-literal='username=ecomuser' --from-literal='password=ecompassword'

# to test changes made in a file of the source code in a current running pod before building a new image.
k -n ecomwebapp cp ./readyz.php ecom-webapp-77d545ff8b-cksgc:/var/www/html/





# Created kubernetest manifests: 
- mysql-deployment.yaml (db-pod) - runs the database (MariaDB v10.5) pod 
- mysql-service.yaml (mysql-service) - gives the db pod a stable DNS name and a virtualIP. Selects the pod with labels app:mysql and forwards traffic to port 3306 so that other pods can talk to the database pod by Service name.
- db-init-job.yaml - is the initialization, a one off pod to bootstrap the db-load-script.sql where this data content is stored in db-init-cm cm. It's job is to load the sql script via volume (db-init-script) configMapKeyRef db-init-cm mounted in the container at volumeMountPath using subPath so that file appears at the exact path inside the container.
- app-config configmap - ecom-webapp pod connection to db pod using env var DB_HOST and DB_NAME. The DB_HOST is where to connect to and, DB_NAME what database to open. The DB_HOST = "mysql-service.ecomwebapp.svc.cluster.local" is the K8s Service DNS name. So that the ecom-webapp pod can find the db pod even if the db pod IP changes.  The ecom-webapp reads this env var from the the app-config cm and uses it to connect to the db pod.  
- db-credentials.yaml - stored as secret object, authentication to database. 