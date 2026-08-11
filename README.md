# Introduction 

Welcome to my Kubernetes Resume Challenge documentation in reference to the link below. This is a sample e-commerce application written in PHP and uses MariaDB image for the database and this is built for learning purposes. This homelab is specially for someone new to containerized applications or, if you want a refresher with Docker containers and Kubernetes for container orchestration or, if you're a seasoned Devops practitioner, you'll still find this useful to build your K8s cluster for your homelab with minimum specification requirements. This hands-on challenge involves several technologies that you'll be exposed to; from source code version control using GitHub, deployment to cloud using AWS EKS and ECR for container registry and automating deployment via CI/CD through a pipeline using GitHub Actions. 

Reference link [The Kubernetes Resume Challenge](https://cloudresumechallenge.dev/docs/extensions/kubernetes-challenge/?utm_source=substack&utm_medium=email#intro )

This project documents my hands-on work with:  
- Kubernetes cluster administration
- Containerized applications (Docker)
- Helm charts
- ConfigMaps and Secrets, Horizontal Pod Autoscaling (HPA), Data persistence (Persistent Volume & Persistent Volume Claim)
- Liveness and Readiness Probes
- CI/CD pipelines via GitHub Actions
- AWS EKS deployment and AWS ECR for container registry

## Documentation 
- [Step 01:Kubernetes-Certification](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/01-Certification.md)
- [Step 02: Containerize-Your-Ecommerce-Website-and-Database](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/02-Containerize-Your-Ecommerce-Website-and-Database.md)
- [Step 03 and 05: Setup-k8s-on-AWS-using-EKS-and-expose-your-website](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/03and05-Setup-k8s-on-AWS-using-EKS-and-expose-your-website.md)
- [Step 04: Deploy-Your-website-to-Kubernetes-kubeadm-Local-Cluster](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/04-Deploy-Your-website-to-Kubernetes-kubeadm-Local-Cluster.md)
- [Step 06: Implement-Configuration-Management](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/06-Implement-Configuration-Management.md)
- [Step 07: Scale-Your-Application](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/07-Scale-Your-Application.md)
- [Step 08: Perform-a-Rolling-Update](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/08-Perform-a-Rolling-Update.md)
- [Step 09: Rollback-a-Deployment](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/09-Rollback-a-Deployment.md)
- [Step 10: Autoscale-Your-Application](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/10-Autoscale-Your-Application.md)
- [Step 11: Implement-Liveness-and-Readiness-Probes](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/11-Implement-Liveness-and-Readiness-Probes.md)
- [Step 12: Utilize-Configmaps-and-Secrets](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/ae8d15c4aa3f6228ece6d4787bfd57c02e0c5621/docs/Steps/11-Implement-Liveness-and-Readiness-Probes.md)  

Extras:
- [Implement-Persistent-Storage](https://github.com/CoyApilado18/learning-app-ecommerce-K8s-Resume-Challenge/blob/6a965c06f28ddfc23d42fa15fe0d9b6edbbd1f3c/docs/Extras/Implement-Persistent-Storage.md)
- 



- See my gitub repo to build your local kubeadm cluster https://github.com/CoyApilado18/Build-your-local-Kubernetes-cluster.git with one Controlplane and one Worker node. I deployed this using Ubuntu 22.04 for my homelab.