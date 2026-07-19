# Context
- Rollback the deployment to the previous version 

# Goals
- Make sure the deployment was rolled back to the previous version

# Commands and Notes
### 1. Check the rollout history
k -n ecomwebapp rollout history deploy ecom-webapp

### 2. Command to rollback a deployment imperatively with the --record=true flag. This will rollback to the last known good revision. 
k -n ecomwebapp undo deploy ecom-webapp --record=true

### 2.1 Or if you want to rollback to a specific version
k -n ecomwebapp undo deploy ecom-webapp --to-revision=<revision-number>

### 3. Verify the deployment's image by running either of this commands and it should be reverted back to the previous image.
k -n ecomwebapp get deploy -oyaml | grep image
k -n ecomwebapp describe deploy ecom-webapp | grep Image 

### In a real world scenario, the imperative way is usually done as "hot fix" or immediate fix (or break-fix) to stop the bad rollout fast, reduce downtime and restore to the  previous replicaset. But this drifts the cluster away from Git.
### Permanent fix Git-based:
- Revert the bad commit or create a new commit in Git to point to the previous image tag.  
- Update the deployment manifest/Helm image: to the previous working version
- Commit and push then let your CI/CD apply the change. This make the cluster state match code again. 

### Proper reconciliation flow after the imperative way of rollback:
### Refer to step. 1 above to get the rollout history 
### Once you have determined the last known good image update the deployment's image spec (spec.template.spec.containers.image)
### Commit, push and apply:
### If plain kubectl:
k -n ecomwebapp apply -f deployment.yaml
- restart the pods
k -n ecomwebapp delete po -l app=ecom-webapp

- Verify and Document:
- Confirm the Deployment's image and revision history
k -n ecomwebapp get deploy ecom-webapp -oyaml | grep image
- Add a "change-cause" annotation for future traceability
k -n ecomewebapp annotate deploy ecom-webapp kubernetes.io/change-cause="your-message-here"

 ### If CI/CD eg: GitHub Actions just commit and push
- Verify reconciliation and confirm the deployment's image and revision history
k -n ecomwebapp get deploy ecom-webapp -oyaml | grep image
k -n ecomwebapp rollout status deploy ecom-webapp 

