# Omnichannel Support Helpdesk & Live Chat Documentation

Welcome to the official documentation for the **Omnichannel Support Helpdesk & Live Chat System**.

This documentation hub covers the architectural design, feature guides, integration patterns, and API references for building and deploying OmniDesk.

---

## 📚 Table of Contents

1. [System Architecture & Tech Stack](./architecture.md)
   - High-level system architecture
   - Database schema & relationships
   - Asset pipeline (Tailwind CSS v4 + Dart Sass + Vite)
   - Real-time messaging architecture (WebSockets / Broadcast)

2. [Agent Workspace & Dashboard](./agent-workspace.md)
   - Unified Inbox navigation & multi-channel ticket views
   - Ticket status lifecycle & priority management
   - Agent replies vs. Internal Notes (team collaboration)
   - Canned Responses & quick reply shortcuts
   - Contact profile drawer & conversation context

3. [Embeddable Live Chat Widget](./live-chat-widget.md)
   - Embedding the floating chat widget on external sites
   - Dynamic configuration & real-time theme customization
   - Visitor session initialization & pre-chat form handling
   - Custom launcher icons, branding colors, and messaging

4. [Channel Integrations Guide](./channel-integrations.md)
   - Supported channels: Web Chat, WhatsApp, Email, Facebook, Telegram
   - Channel JSON configuration schemas
   - Inbound webhook processing & message routing

5. [API Reference](./api-reference.md)
   - Public Widget API endpoints (`/api/v1/widget/*`)
   - Agent Workspace Ticket endpoints (`/tickets/*`)
   - Request/response payloads and error handling

---

## 🚀 Quick Links

- [Main Repository README](../README.md)
- [Agent Workspace Dashboard](http://localhost:8000/)
- [Live Chat Demo Page](http://localhost:8000/demo)
