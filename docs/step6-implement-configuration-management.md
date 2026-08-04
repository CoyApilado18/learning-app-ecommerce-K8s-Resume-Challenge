# Context
- Modify the php app to enable a "dark mode" background feature of the ecomwebapp website. A feature toggle via an external configuration using a configmap that allows the ecommerce webapp to enable dark mode background. 
- The configmap is a configuration data that controls enabling/disabling background color of the ecommerce webapp.
- Current theme is white background.

![image alt]()

# Goals
- Create a configmap for the feature toggle dark mode and apply it to ecomwebapp deployment as env var from the configmap. Instead of baking this feature inside the image, we can control the setting of the background for our ecom-webapp via a configmap.

# Commands and Notes
- See app-config configmap where this feature toggle is configured.
- See website-deployment.yaml spec where the env var is set and referenced to the app-config cm. 

## Apply the app-config cm manifests with FEATURE_DARK_MODE=true  
- The app-config cm has data DB_HOST as the internal DNS for our ecomwebsite that even if the ip of the database change, the ecomwebapp still has connection to the database. Think of it as "where" to connect to and DB_NAME is "what" database to open and in our case it's the ecomdb database.
```bash
cd ecomwebapp/
k apply -f app-config.yaml
```

## Restart the ecomwebapp pods to consume this setting of feature toggle for the dark bacground
```bash
k -n ecomwebapp delete po -l app=ecom-webapp
```

## Run kubectl port-forward and open a browser and check ecomwebapp website and dark background should now appear
```bash
k -n ecomwebapp port-forward svc/ecom-web-svc 8080:80
```

![image alt](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/29ec15a78f1189d1cbc4b6320eb34e584c32ef8a/docs/images/localhost-website.png)