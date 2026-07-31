# 🐳 Docker & VPS Deployment Guide — OmniDesk

This guide explains how to deploy dedicated, single-tenant **OmniDesk** instances for client organizations on any VPS (DigitalOcean, Hetzner, AWS EC2, Linode) using Docker and Docker Compose.

---

## 🏗️ Deployment Architecture Overview

Each client instance runs in an isolated, containerized stack:

- **Nginx** (Port `9880`): High-performance web server serving Blade views and static assets.
- **PHP 8.3 FPM**: Application runtime executing Laravel core with `opcache` and `phpredis`.
- **Laravel Reverb** (Port `9881`): Real-time WebSocket server for live chat and agent updates.
- **Laravel Queue Worker**: Asynchronous background job worker for webhooks and email notifications.
- **Laravel Scheduler**: Native task runner executing scheduled commands every minute (`php artisan schedule:work`).
- **MySQL 8.0** (Port `3306`): Relational database storing tickets, messages, contacts, and channels.
- **phpMyAdmin** (Port `9882`): Web-based database management interface.
- **Redis (Alpine)** (Port `6379`): Microsecond in-memory store for active sessions, caching, and queues.

```
       Internet / Visitors (HTTPS)
                  │
                  ▼
   ┌───────────────────────────────┐
   │ Host Reverse Proxy            │ (SSL Certificate Management)
   │ (Host Nginx / Caddy)          │
   └──────────────┬────────────────┘
                  │
         ┌────────┴────────┐
         ▼                 ▼
   ┌───────────┐     ┌───────────┐
   │ Web App   │     │ WebSockets│
   │ Port 9880 │     │ Port 9881 │
   └─────┬─────┘     └─────┬─────┘
         │                 │
         └────────┬────────┘
                  ▼
   ┌───────────────────────────────┐
   │       Dockerized OmniDesk     │
   │ Nginx + PHP-FPM + Supervisord │
   └───────────────────────────────┘
```

---

## 🚀 One-Command VPS Deployment

### 1. Provision a Dedicated VPS
- **Minimum Requirements**: 1 vCPU, 1 GB RAM, 25 GB SSD (e.g. $5–$6/mo droplet).
- **Supported OS**: Ubuntu 22.04 / 24.04 LTS, Debian 12.

### 2. Install Docker & Docker Compose on Host
```bash
sudo apt update && sudo apt install -y docker.io docker-compose-plugin
sudo systemctl enable --now docker
```

### 3. Clone Repository & Launch
```bash
# Clone repository on VPS
git clone https://github.com/your-org/omnichannel-helpdesk.git client-company-helpdesk
cd client-company-helpdesk

# Build & launch container stack
docker compose up -d --build
```

OmniDesk is now running live inside Docker:
- 🖥️ **Web Application**: `http://127.0.0.1:9880`
- 🛰️ **WebSocket Server**: `http://127.0.0.1:9881`

---

## 🔒 Host Reverse Proxy & SSL Setup Options

Choose one of the reverse proxy options below depending on your server configuration:

---

### Option 1: Host Nginx + Let's Encrypt (Certbot) — Single Domain

Use this option if you run Nginx on your host VPS and want automatic single-domain SSL via Certbot.

Create `/etc/nginx/sites-available/omnidesk.conf`:
```nginx
server {
    server_name support.clientcompany.com;

    # 1. Proxy Web App Traffic to Docker (Port 9880)
    location / {
        proxy_pass http://127.0.0.1:9880;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # 2. Proxy WebSocket Traffic to Dockerized Reverb (Port 9881)
    location /app/ {
        proxy_pass http://127.0.0.1:9881;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400s;
        proxy_send_timeout 86400s;
    }

    # 3. Proxy phpMyAdmin Database Interface (Port 9882)
    location /phpmyadmin/ {
        proxy_pass http://127.0.0.1:9882/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable site and configure SSL:
```bash
sudo ln -s /etc/nginx/sites-available/omnidesk.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
sudo certbot --nginx -d support.clientcompany.com
```

---

### Option 2: Host Nginx + Wildcard SSL (`*.sejan.dev`) — Instant Subdomains

Use this option if you have a Wildcard SSL certificate (e.g. `fullchain.pem` & `privkey.pem`) for all your client subdomains (`client1.sejan.dev`, `acme.sejan.dev`).

Create `/etc/nginx/sites-available/sejan-wildcard.conf`:
```nginx
server {
    listen 443 ssl http2;
    server_name client1.sejan.dev; # Or use *.sejan.dev

    # Wildcard SSL Certificate Files on Host VPS
    ssl_certificate     /etc/ssl/sejan.dev/fullchain.pem;
    ssl_certificate_key /etc/ssl/sejan.dev/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    location / {
        proxy_pass http://127.0.0.1:9880;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
    }

    location /app/ {
        proxy_pass http://127.0.0.1:9881;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_read_timeout 86400s;
        proxy_send_timeout 86400s;
    }
}

server {
    listen 80;
    server_name *.sejan.dev;
    return 301 https://$host$request_uri;
}
```

---

### Option 3: Host Caddy Reverse Proxy (Zero-Config Auto SSL)

Install Caddy on Host VPS:
```bash
sudo apt install -y caddy
```

Edit `/etc/caddy/Caddyfile`:
```caddy
support.clientcompany.com {
    # Proxy HTTP Web Traffic
    reverse_proxy localhost:9880

    # Proxy WebSocket Traffic
    handle /app/* {
        reverse_proxy localhost:9881
    }
}
```

Reload Caddy:
```bash
sudo systemctl reload caddy
```
Caddy automatically obtains and renews free Let's Encrypt / ZeroSSL certificates!

---

### Option 4: 100% Dockerized Caddy inside `docker-compose.yml`

If you do NOT want to install anything on the host VPS except Docker:

Add Caddy service to `docker-compose.yml`:
```yaml
  caddy:
    image: caddy:latest
    container_name: omnidesk-caddy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./Caddyfile:/etc/caddy/Caddyfile
      - caddy_data:/data
      - caddy_config:/config
    depends_on:
      - omnidesk
```

Create a local `Caddyfile` in the project root:
```caddy
support.clientcompany.com {
    reverse_proxy omnidesk:9880

    handle /app/* {
        reverse_proxy omnidesk:9881
    }
}
```

---

## ⚙️ Environment Configuration

Environment variables can be customized in `docker-compose.yml` or a `.env` file:

```yaml
environment:
  APP_NAME: "Acme Corp Support"
  APP_ENV: "production"
  APP_DEBUG: "false"
  APP_URL: "https://support.acmecorp.com" # Or https://client1.sejan.dev

  # Database Driver (sqlite default or connect to host mysql)
  DB_CONNECTION: "sqlite"

  # High-Concurrency Redis Drivers
  SESSION_DRIVER: "redis"
  QUEUE_CONNECTION: "redis"
  CACHE_STORE: "redis"

  # Production WebSockets over HTTPS/WSS (Port 443 through Reverse Proxy)
  BROADCAST_CONNECTION: "reverb"
  REVERB_HOST: "0.0.0.0"
  REVERB_PORT: "9881"
  REVERB_SCHEME: "http"

  VITE_REVERB_APP_KEY: "bq4kweky886lkzul4zr1"
  VITE_REVERB_HOST: "support.acmecorp.com" # Client Domain
  VITE_REVERB_PORT: "443"                  # Reverse Proxy HTTPS Port
  VITE_REVERB_SCHEME: "https"              # Secure WSS WebSockets
```

---

## 💾 Database Backups & Maintenance

### Backup SQLite Database:
```bash
docker exec omnidesk-app cp /var/www/html/database/database.sqlite /var/www/html/storage/backup.sqlite
```

### View Application Logs:
```bash
docker compose logs -f omnidesk
```

### Run Database Seeders inside Container:
```bash
# Run default database seeder
docker exec -it omnidesk-app php artisan db:seed

# Wipe and re-seed database fresh
docker exec -it omnidesk-app php artisan migrate:fresh --seed
```

### Run `npm run build` Assets Compiler inside Container:
```bash
docker exec -it omnidesk-app npm run build
```

### Open Interactive Bash Shell inside Container:
```bash
docker exec -it omnidesk-app bash
```

### Update Client Instance to Latest Code:
```bash
git pull
docker compose up -d --build
```
