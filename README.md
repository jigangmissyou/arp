# WordPress Multi-Site Logging with ELK Stack (Docker)

This project runs two WordPress sites with their respective MySQL databases and centralized logging using the ELK (Elasticsearch, Logstash, Kibana) stack. Filebeat collects logs from each container for analysis and visualization.

##  Features

- Two isolated WordPress + MySQL environments.
- Centralized logging with ELK (Elasticsearch, Logstash, Kibana).
- Filebeat agents collect logs from WordPress and MySQL.
- Optional Metricbeat support for container metrics.
- Nginx proxy for frontend access (optional).
- Docker-based deployment.

##  Requirements

- Docker
- Docker Compose

##  Getting Started

### 1. Clone the Repository

git clone https://github.com/jigangmissyou/arp.git

cd arp

### 2. Start the Stack

docker compose up -d
