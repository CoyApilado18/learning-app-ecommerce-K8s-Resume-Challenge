# Context
- Securely manage our database connection string and feature toggles without baking them in the application (or in the image). The feature toggle will allow us to change our website background to either dark, white or even other colors just by using the ConfigMap object without the need of baking it in the image build it and apply changes to our website deployment. 

# Goals
- Create a Secret object for database credentials to authenticate to the database and an exeternal configuration for feature toggle to change the color to dark theme or white of our website via a configmap. 

# Commands and Notes
## Create Secret object where your username and password are set to key value pairs. You'll use this Secret as env var in our website deployment to authenticate to the database.
k -n ecomwebapp create secret generic db-credentials \
--from-literal='<key-username>=<value-of-username>' \
--from-literal='<key-password>=<value-of-password>'

## Create configmap for feature toggle
- See step6 Implement Configuration Management for the documentation and commands to create the Configmap for the FEATURE_TOGGLE.  