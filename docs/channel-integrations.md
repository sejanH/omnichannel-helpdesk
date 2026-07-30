# Omnichannel Integrations Guide

The system normalizes incoming interactions from multiple communication channels into a unified `Ticket` and `Message` model.

---

## 🌐 Supported Channels

| Channel | Type | Provider / Protocol | Data Payload Features |
| :--- | :--- | :--- | :--- |
| **Web Live Chat** | `web_chat` | Native Widget API | Session token, visitor IP, page referrer. |
| **WhatsApp Business** | `whatsapp` | Meta Cloud API / Twilio | Phone number, WhatsApp Business API message ID. |
| **Email** | `email` | SMTP / IMAP / Postmark | Email headers, MIME content, attachments. |
| **Facebook Messenger** | `facebook` | Meta Graph API / Webhooks | Page scoped user ID (PSID), page ID. |
| **Telegram** | `telegram` | Telegram Bot API | Chat ID, Telegram username. |

---

## ⚙️ Channel Configuration Schemas

Each channel entry in the `channels` table contains a JSON `configuration` column.

### 1. Web Chat Configuration (`type: web_chat`)
```json
{
  "widget_color": "#6366f1",
  "position": "bottom-right",
  "title": "Customer Support",
  "welcome_message": "👋 How can we help you today?",
  "theme": "dark"
}
```

### 2. WhatsApp Configuration (`type: whatsapp`)
```json
{
  "phone_number_id": "102938475610293",
  "whatsapp_business_account_id": "9876543210",
  "access_token": "EAAG...",
  "webhook_verify_token": "my_secret_token_123"
}
```

### 3. Email Configuration (`type: email`)
```json
{
  "inbound_address": "support@helpdesk.com",
  "smtp_host": "smtp.mailtrap.io",
  "smtp_port": 587,
  "parse_mode": "html"
}
```

### 4. Facebook Messenger Configuration (`type: facebook`)
```json
{
  "page_id": "100293847561",
  "page_access_token": "EAAB...",
  "app_secret": "9f8e7d..."
}
```

### 5. Telegram Configuration (`type: telegram`)
```json
{
  "bot_token": "123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ",
  "bot_username": "@OmniDeskBot"
}
```

---

## 📥 Webhook Ingestion Lifecycle

```
[ External Webhook Payload ] 
          │
          ▼
[ Channel Routing & Verification ]
          │
          ▼
[ Contact Resolution (Match Phone/Email/External ID) ]
          │
          ▼
[ Active Ticket Search (Status != resolved/closed) ]
   ├── Found? ───> Append Message & Update `last_activity_at`
   └── Not Found? ─> Create New Ticket + Create Message
          │
          ▼
[ Broadcast `MessageSent` Event to Agent Workspace ]
```
