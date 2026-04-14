# Simple Reverse Proxy
This project shows how to create a simple reverse proxy that routes two services that expose two endpoints using different url prefixes in a Docker Swarm enviroment.

## Reverse Proxy Topology

```mermaid
graph TD
    Client((Client))

    subgraph "Endpoints"
        direction LR
        QReq["GET http://localhost:50/quarkus/hello"]
        DReq["GET http://localhost:5050/dashboard/"]
        LReq["GET http://localhost:50/laravel/hello"]
    end

    Client --> QReq
    Client --> DReq
    Client --> LReq

    Traefik["traefik-reverse-proxy\nimage: traefik:v3.6\nentrypoint: :50\ndashboard: :5050"]

    QReq --> Traefik
    DReq --> Traefik
    LReq --> Traefik

    subgraph "traefik-network (overlay)"
        Traefik -->|PathPrefix /quarkus| Quarkus["quarkus-api\nport: 8080\nendpoint: /hello\nhealth: /q/health"]
        Traefik -->|PathPrefix /laravel| Laravel["laravel-api\nendpoint: /hello\nport: 8000\nhealth: /up"]
    end

    Traefik -.->|Health check /q/health| Quarkus
    Laravel -.->|Health check /up| Traefik
```

## Services

- `traefik-reverse-proxy`
  - Entry point: `web` on container port `80` (host `50`)
  - Dashboard/API: host port `5050`
  - Routes requests to backend services using labels.

- `quarkus-api`
  - Routed by Traefik with path prefix: `/quarkus`
  - Internal service port: `8080`
  - Health check: `/q/health`

- `laravel-api`
  - Routed by Traefik with path prefix: `/laravel`
  - Internal service port: `8000`
  - Health check: `/up`

## How to Run Project

### Quarkus API

First of all, make sure you are in the `quarkus-hello-world` folder.

1. Compile Quarkus Project (native way)

   ```bash
   ./mvnw package -Dnative -Dquarkus.native.container-build=true
   ```

2. Create Docker Image

   ```bash
   docker build -f src/main/docker/Dockerfile.native -t quarkus-api-image .
   ```

### Laravel API

First of all, make sure you are in the `laravel-hello-world` folder.

1. Create Docker Image

   ```bash
   docker build -t laravel-api-image .
   ```

### Docker Compose
After you create laravel-api-image and quarkus-api-image you can run the docker-compose.yaml

1. Deploy stack

   ```bash
   docker stack deploy -c docker-compose.yaml simple-reverse-proxy-stack -d
   ```