# System Architecture & Technical Stack

The **Omnichannel Support Helpdesk & Live Chat System** is designed for modern, high-throughput customer support across web chat, social media, email, and messaging platforms.

---

## 🏗️ Architecture Overview

```
                          +-----------------------------------+
                          |  External Website / Visitor App   |
                          +-----------------------------------+
                                            |
                                            | (REST API / JSON)
                                            v
+-----------------------+         +-------------------+         +-----------------------+
|  Messaging Channels   | ------> |  Laravel 12 Core  | <------ |    Agent Workspace    |
| (WhatsApp/Email/etc.) |         |  Backend Engine   |         | (Unified Web Inbox)   |
+-----------------------+         +-------------------+         +-----------------------+
                                            |
                                            | (Broadcasting / WebSockets)
                                            v
                                  +-------------------+
                                  |   Pusher / Echo   |
                                  +-------------------+
```

---

## 🛠️ Technology Stack

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Backend Core** | PHP 8.2+ / Laravel 12.x | High-performance application framework handling routing, ORM, queues, and broadcasting. |
| **Database** | SQLite / MySQL / PostgreSQL | Relational storage for tickets, channels, messages, contacts, canned responses, and SLA policies. |
| **Asset Bundler** | Vite 7.x + `@tailwindcss/vite` | Lightning-fast HMR and bundle compilation. |
| **Styling & Design** | Tailwind CSS v4 + Dart Sass (SCSS) | CSS-first Tailwind utilities integrated with custom SCSS design tokens, mixins, glassmorphism, and animations. |
| **Real-Time Layer** | Laravel Echo + Pusher / Soketi | Real-time event broadcasting for instant message delivery (`App\Events\MessageSent`). |
| **Frontend Templates** | Laravel Blade + Vanilla JS | Lightweight, dynamic server-side rendering paired with reactive DOM updates for chat widgets. |

---

## 🗄️ Database Schema & Entity Relationships

The system relies on 6 core entities:

```
[Channel] 1 ──── N [Ticket] N ──── 1 [Contact]
                      |
                      | 1 ──── N [Message]
                      |
                      | N ──── 1 [User (Agent)]
```

### Models & Definitions

- **`Channel`** (`channels`):
  Represents communication channels (`type`: `web_chat`, `whatsapp`, `email`, `facebook`, `telegram`). Stores channel metadata and JSON `configuration` (widget styling, title, welcome message, etc.).

- **`Contact`** (`contacts`):
  Represents end-user customers/visitors. Stores `name`, `email`, `phone`, `avatar`, `notes`, and `external_ids` JSON array for tracking widget sessions and social handles.

- **`Ticket`** (`tickets`):
  The core support case unit. Stores `ticket_number` (e.g. `TCK-1001`), `subject`, `status` (`open`, `in_progress`, `pending`, `resolved`, `closed`), `priority` (`urgent`, `high`, `medium`, `low`), `assigned_agent_id`, `channel_id`, `contact_id`, `sla_policy_id`, and `last_activity_at`.

- **`Message`** (`messages`):
  Stores conversation history. Fields include `ticket_id`, `sender_type` (`customer`, `agent`, `system`), `sender_id`, `sender_name`, `content`, and `is_internal_note` (boolean for private team notes).

- **`CannedResponse`** (`canned_responses`):
  Pre-written reply templates (`title`, `shortcut`, `content`, `category`).

- **`SlaPolicy`** (`sla_policies`):
  Service level agreement metrics (`name`, `first_response_time_minutes`, `resolution_time_hours`, `priority`).

---

## ⚡ Asset Compilation & Styling Pipeline

The asset compilation uses **Vite 7** with **Tailwind CSS v4** and **Dart Sass**:

- **Entry Point**: `resources/css/app.css`
- **SCSS Components**: `resources/scss/app.scss` (custom glassmorphism, badges, keyframes, scrollbars)
- **Dart Sass Deprecation Safeguard**: Configured in `vite.config.js` via `css.preprocessorOptions.scss`:
  ```javascript
  css: {
      preprocessorOptions: {
          scss: {
              api: 'modern-compiler',
              silenceDeprecations: ['import'],
          },
      },
  }
  ```
