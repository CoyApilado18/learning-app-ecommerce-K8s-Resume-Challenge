## Context
- A feature toggle via an external configuration using a configmap that allows the ecommerce webapp to enable dark mode background. 
- The configmap is a configuration data that controls enabling/disabling background color of the ecommerce webapp.

## Goals
- Create a configmap for the feature toggle dark mode. Instead of baking this feature inside the image, we can control the setting of the background for our app via a configmap.


## Commands and Notes
- See app-config configmap where this feature toggle is configured.
- See website-deployment.yaml spec where the env var is set and referenced to the app-config cm. 

# apply the app-config cm manifests with FEATURE_DARK_MODE=true
cd ecomwebapp/
k apply -f app-config.yaml

# restart the ecomwebapp pods to consume this setting of feature toggle for the dark bacground
k -n ecomwebapp delete po -l app=ecom-webapp

# Run kubectl port-forward and open a browser and check ecomwebapp website and dark background should now appear
k -n ecomwebapp port-forward svc/ecom-web-svc 8080:80