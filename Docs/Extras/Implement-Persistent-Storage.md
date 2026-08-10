# Context
- Ensure data persistence for the MariaDB database across pod restarts and redeployments. 

# Goals
- Create a Persistent Volume (PV) and a Persistent Volume Claim (PVC) for MariaDB storage needs.
- Update MariaDB Deployment: Modify the deployment to use the PVC for storing database data.
- Outcome: Database data persists beyond the lifecycle of MariaDB pods, ensuring data durability.

# Commands and Notes

### NOTE: The challenge only requires to create a PVC where it will fall for dynamic provisioning. But I created a PV for this challenge to demonstrate static provisioning in a lab environment. The advantage of having a PV is control and portability. A simple PV "hostPath" for storage is NOT recommended in a production environment but for a simple lab (for testing), on-prem and tightly controlled environments. One disadvantage of using this is that when a db pod restarts, the db pod may be scheduled to a different node and it won't see the data where it originally was tied to the node's local filesystem from where the pod originally is scheduled. Another one is a node failure is a database failure. So having hostPath PV in our lab environment is just to understand how PV and PVC works together to demonstrate the full Kubernetes storage lifecycle. 

##  Create a Persistent Volume (PV) and a Persistent Volume Claim (PVC) for MariaDB storage needs.
- See ecomdb/05-pv-pvc.yaml for the PV and PVC objects manifests. The PV manifests has ReadWriteOnce (RWO) which means the volume can be mounted with read/write access by one Kubernetes node at a time. 
- mysql-pv.yaml
```bash
spec:
  accessModes:
    - ReadWriteOnce
  storageClassName: manual
  resources:
    requests:
      storage: 2Gi
```
- Apply the PV and PVC yaml files.
```bash
k apply -f ecomdb/05-pv-pvc.yaml
```

## Update MariaDB Deployment: Modify the deployment to use the PVC for storing database data.
- See 06-mysql-deploy.yaml where the PVC is mounted in the database pod and requests of 2gibibytes of storage. When you run the command below, you'll see the  
```bash
k -n ecomwebapp get pv
k -n ecomwebapp get pvc
```
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/92f076e6c579326b9087c28dcc55a2d74ec21102/Docs/images/pv-pvc-bound.png)

## The MariaDB pod shows it was running on node01 and a pod restart happened just 9 seconds ago and then it shows the pod is scheduled on the same node and is running and ready. This clearly shows the persistence of the db pod even after a restart. 
```bash
k -n ecomwebapp get po mysql-76d94b96cb-gznfg -owide --show-labels
k -n ecomwebapp delete po -l app=mysql
k -n ecomwebapp get po -owide
```
![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/92f076e6c579326b9087c28dcc55a2d74ec21102/Docs/images/mysql-pod-restart.png)

## Further testing shows, when I exec into the db pod, input db credentials and ran sql scripts; to show database/tables, a select statement for the Products table, the result shows I can still see the database pod before the pod deletion. This demonstrates data persistence even after the db pod restart took place.
```bash
k -n ecomwebapp exec -it -- sh
```
- Input db credentials.
```bash
mariadb -u <db-username> -p<db-password>
```

- Run some sql scripts to verify ecomdb Database and Products table still exists.
```bash
SHOW DATABASES;
USE ecomdb;
select * from Products;
```

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/92f076e6c579326b9087c28dcc55a2d74ec21102/Docs/images/exec-db-pod.png)

### Resource links:  
- Persistent Volume: https://kubernetes.io/docs/concepts/storage/persistent-volumes/
- Persissten Volume Claims: https://kubernetes.io/docs/concepts/storage/persistent-volumes/#persistentvolumeclaims
- hostPath PV: https://kubernetes.io/docs/concepts/storage/volumes/#hostpath