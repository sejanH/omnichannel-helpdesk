<p align="center">
  <img src="https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp" width="100" alt="OmniHelp Logo">
</p>

<h1 align="center">OmniHelp — Omnichannel Support Helpdesk & Live Chat System</h1>

<p align="center">
  <strong>A modern, real-time omnichannel customer support platform built with Laravel 12, Vite 7, Tailwind CSS v4, Dart Sass, and WebSockets.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind v4">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/License-MIT-green.style=for-the-badge" alt="License MIT">
</p>

---

## 🌟 Overview

**OmniHelp** consolidates customer communications from **Web Live Chat, WhatsApp, Email, Facebook Messenger, and Telegram** into a single, high-performance agent workspace dashboard.

Designed for speed and productivity, OmniHelp equips support teams with real-time bidirectional messaging, internal team collaboration notes, canned responses, SLA tracking, and a fully customizable floating live chat widget for web applications.

---

## ✨ Key Features

- 📥 **Unified Omnichannel Inbox**: Stream line all incoming inquiries across Web Chat, WhatsApp, Email, Facebook, and Telegram into one centralized dashboard.
- 💬 **Embeddable Live Chat Widget**: Lightweight, responsive floating chat widget customizable via API (colors, theme, position, title, launcher icons).
- 🔒 **Internal Team Notes**: Post private internal notes (`is_internal_note: true`) on customer tickets to collaborate with teammates without notifying the customer.
- ⚡ **Canned Responses & Quick Shortcuts**: Insert pre-formatted answers with quick shortcuts to boost support resolution speed.
- ⏱️ **SLA Management & Priority Escalation**: Color-coded ticket priority matrix (Urgent, High, Medium, Low) with automated status tracking.
- 🛰️ **Real-Time WebSockets**: Live broadcast updates using Laravel Broadcasting (`MessageSent` event) for dynamic UI updates without page reloads.
- 🎨 **Modern Glassmorphism Design**: Styled using **Tailwind CSS v4** + **Dart Sass (SCSS)** with custom tokens, mixins, animations, and custom dark mode scrollbars.

---

## 📂 Documentation Directory

Comprehensive feature guides and technical specifications are available in the [`docs/`](./docs/README.md) folder:

- 📖 [**System Architecture & Tech Stack**](./docs/architecture.md): High-level system architecture, database schema & Vite asset compilation pipeline.
- 🖥️ [**Agent Workspace & Dashboard**](./docs/agent-workspace.md): Unified inbox guide, ticket lifecycle, internal notes, and canned responses.
- 💬 [**Embeddable Live Chat Widget**](./docs/live-chat-widget.md): Embedding, styling parameters, pre-chat forms, and visitor session initialization.
- 🌐 [**Omnichannel Integrations**](./docs/channel-integrations.md): WhatsApp, Email, Facebook, and Telegram channel schemas and webhooks.
- 🔌 [**API Reference**](./docs/api-reference.md): Public Widget API endpoints (`/api/v1/widget/*`) and Agent Ticket endpoints.
- 🐳 [**Docker & VPS Deployment Guide**](./docs/deployment.md): Deploy single-tenant client instances on any VPS via Docker Compose with Nginx, PHP 8.3 FPM, Reverb WebSockets, and Redis.

---

## 🚀 Quick Start Guide

### Prerequisites

Ensure your system has the following installed:
- **PHP** >= 8.2 with `sqlite3` / `pdo_sqlite` extension
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**

### Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/omnichannel-helpdesk.git
   cd omnichannel-helpdesk
   ```

2. **Install PHP & Node Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment File**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database & Seed Sample Data**:
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

5. **Start Development Servers**:
   Run Laravel backend server and Vite asset compiler concurrently:
   ```bash
   # Terminal 1: Laravel Backend Server
   php artisan serve

   # Terminal 2: Vite Dev Asset Compiler
   npm run dev
   ```

6. **Access the Application**:
   - 🖥️ **Agent Workspace Dashboard**: [http://localhost:8000](http://localhost:8000)
   - 💬 **Live Chat Widget Demo Page**: [http://localhost:8000/demo](http://localhost:8000/demo)

---

## 🐳 Docker & Single-Tenant VPS Deployment

Deploying dedicated OmniDesk client instances to any VPS (DigitalOcean, Hetzner, AWS EC2) takes **one command**:

```bash
# 1. Clone repo on Client VPS
git clone https://github.com/your-username/omnichannel-helpdesk.git client-instance
cd client-instance

# 2. Build & Launch Container Stack
docker compose up -d --build
```

The stack automatically provisions:
- **Nginx** + **PHP 8.3 FPM** (Web server on port `9880`)
- **Laravel Reverb** (Real-Time WebSocket server on port `9881`)
- **MySQL 8.0** (Containerized database on port `3306`)
- **phpMyAdmin** (Database GUI manager on port `9882`)
- **Redis Alpine** (In-memory session, cache, and queue store)
- **Laravel Queue Worker** & **Task Scheduler** (`php artisan schedule:work`)

### Client Production Domain & SSL Environment Configuration:
Before launching a client instance in production over HTTPS, customize the domain variables in `docker-compose.yml`:

```yaml
    environment:
      # 1. Main Application HTTPS URL
      APP_NAME: "Acme Support"
      APP_URL: "https://support.acmecorp.com" # Or https://client1.sejan.dev

      # 2. Browser WebSocket Connection Settings (WSS over Port 443)
      VITE_REVERB_HOST: "support.acmecorp.com" # Your client's domain
      VITE_REVERB_PORT: "443"                  # Reverse Proxy HTTPS Port
      VITE_REVERB_SCHEME: "https"              # Enables secure wss:// connections
```

For complete reverse proxy configuration files, read the [**Docker & VPS Deployment Guide**](./docs/deployment.md).

---

## 📊 Database Seeded Data

After running `php artisan migrate:fresh --seed`, the database includes sample demonstration data:

- 👤 **Admin User**: `admin@helpdesk.com` (Password: `password`)
- 👤 **Support Agents**: Sarah Rivera, Alex Vance, Marcus Chen
- 🌐 **Channels**: Website Live Chat, WhatsApp Business, Support Email, Facebook Page, Telegram Bot
- 💬 **Tickets & Messages**: Pre-populated tickets with active messages, internal notes, and customer context.
- ⚡ **Canned Responses**: Pre-written templates for greetings, password resets, and refund policies.

---

## 🛠️ Architecture & Tech Stack

```
+-------------------------------------------------------------------+
|                        FRONTEND PRESENTATION                      |
| Blade Engine | Tailwind CSS v4 | Dart Sass | Vanilla JavaScript    |
+-------------------------------------------------------------------+
                                  │
                                  ▼
+-------------------------------------------------------------------+
|                         BACKEND & SERVICES                        |
| Laravel 12 Core | Eloquent ORM | SQLite DB | Broadcast Events     |
+-------------------------------------------------------------------+
                                  │
                                  ▼
+-------------------------------------------------------------------+
|                           EXTERNAL APIS                           |
| Widget API (/api/v1/widget) | Webhooks (WhatsApp/FB/Telegram/Email)|
+-------------------------------------------------------------------+
```

---

## 🗂️ Project Directory Structure

```
omnichannel-helpdesk/
├── app/
│   ├── Events/          # MessageSent broadcast events
│   ├── Http/
│   │   └── Controllers/
│   │       ├── OmnichannelController.php # Agent Workspace Dashboard APIs
│   │       └── WidgetController.php     # Public Live Chat Widget APIs
│   └── Models/          # Channel, Contact, Ticket, Message, CannedResponse, SlaPolicy
├── config/              # App, Database, Broadcaster configurations
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/         # OmnichannelSeeder with rich sample data
├── docs/                # Feature documentation directory
│   ├── README.md
│   ├── architecture.md
│   ├── agent-workspace.md
│   ├── live-chat-widget.md
│   ├── channel-integrations.md
│   └── api-reference.md
├── resources/
│   ├── css/             # app.css (Tailwind CSS v4 entrypoint)
│   ├── scss/            # app.scss (Custom Sass tokens, mixins, animations)
│   ├── js/              # app.js & WebSocket listeners
│   └── views/
│       ├── dashboard.blade.php  # Unified Agent Inbox Dashboard
│       └── demo.blade.php       # Live Chat Widget Demo Page
├── routes/
│   └── web.php          # Web routes & /api/v1/widget API endpoints
└── vite.config.js       # Vite 7 + Tailwind v4 + Dart Sass configuration
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
