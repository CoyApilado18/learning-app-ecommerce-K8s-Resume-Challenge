# Context
- Scale our deployment as load increases and monitor scaling out of the mnumber of pods. 

# Goals
- To show how easy it is to scale our deployment as load increases and observe/monitor scaling out the number of pods.

# Commands and Notes
## Check current number of pods
k -n ecomwebapp get po

## Scale out number of pods (--replicas=6)of the ecomwebapp deployment to handle increase load.
k -n ecomwebapp scale deploy/ecom-webapp --replicas=6

## Monitor scaling to observe deployment scaling out
k -n ecomwebapp get po -w 



