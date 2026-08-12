# Omnichannel Integrations & Channels Guide

OmniDesk normalizes incoming interactions across multiple communication channels (**WhatsApp**, **Facebook Messenger**, **Instagram Direct**, **Telegram**, **Email**, and **Web Live Chat**) into a unified `Ticket` and `Message` model for seamless agent workspace routing.

---

## 🌐 Supported Communication Channels

| Channel | Type | Provider / Protocol | Webhook Endpoint | Key Setup Requirements |
| :--- | :--- | :--- | :--- | :--- |
| **WhatsApp Business** | `whatsapp` | Meta WhatsApp Cloud API | `/api/v1/webhooks/whatsapp` | Phone Number, Phone Number ID, Access Token, Verify Token |
| **Facebook Messenger** | `facebook` | Meta Graph API | `/api/v1/webhooks/facebook` | Page ID, Page Access Token, Verify Token |
| **Instagram Direct** | `instagram` | Meta Graph API | `/api/v1/webhooks/instagram` | Instagram Account ID, Page Access Token, Verify Token |
| **Telegram Bot** | `telegram` | Telegram Bot API | `/api/v1/webhooks/telegram` | Bot Username, Bot Token from `@BotFather` |
| **Email Support Desk** | `email` | SMTP / IMAP | N/A (Inbound Parser) | Support Email Address, SMTP Host & Port |
| **Web Live Chat** | `web_chat` | Native JavaScript Widget | `/api/v1/widget/*` | Embedded JavaScript snippet (`/widget.js`) |

---

## ⚙️ Channel Configuration Schemas

Each integration entry stored in the `channels` database table contains a dynamic JSON `configuration` column.

### 1. WhatsApp Business Cloud API (`type: whatsapp`)
```json
{
  "phone_number": "+18005550199",
  "phone_number_id": "109827364519283",
  "token": "EAAG_SAMPLE_WHATSAPP_TOKEN",
  "verify_token": "omnihelp_secret"
}
```

### 2. Facebook Messenger API (`type: facebook`)
```json
{
  "page_id": "10928374615",
  "page_access_token": "EAAH_SAMPLE_FACEBOOK_TOKEN",
  "verify_token": "omnihelp_secret"
}
```

### 3. Instagram Direct Messaging (`type: instagram`)
```json
{
  "instagram_account_id": "178414092817263",
  "page_access_token": "EAAI_SAMPLE_INSTAGRAM_TOKEN",
  "verify_token": "omnihelp_secret"
}
```

### 4. Telegram Bot API (`type: telegram`)
```json
{
  "bot_username": "@OmniSupportBot",
  "bot_token": "123456789:ABC_SAMPLE_TELEGRAM_TOKEN"
}
```

### 5. Email Support Desk (`type: email`)
```json
{
  "support_email": "support@helpdesk.com",
  "smtp_host": "smtp.mailtrap.io",
  "smtp_port": "587"
}
```

### 6. Web Live Chat Widget (`type: web_chat`)
```json
{
  "title": "Customer Support",
  "subtitle": "We typically reply in under 5 minutes",
  "welcome_message": "Hello! How can we assist you today?",
  "widget_color": "#4f46e5",
  "theme": "light",
  "launcher_icon": "message-dots",
  "require_prechat": false
}
```

---

## 📖 Step-by-Step Setup Guides

### 1. Setting Up Meta WhatsApp Business Cloud API
1. Navigate to [Meta for Developers](https://developers.facebook.com/) and create a **Business** App.
2. Add the **WhatsApp** product to your app.
3. In Meta Developer Console, retrieve your **Phone Number ID** and generate a **Permanent User Access Token**.
4. In OmniDesk admin panel, navigate to **Channel Integrations** (`/channels`) and edit the WhatsApp channel.
5. Enter your WhatsApp Phone Number, Phone Number ID, Access Token, and set a custom **Webhook Verify Token** (e.g. `omnihelp_secret`).
6. Copy your Webhook Callback URL: `https://yourdomain.com/api/v1/webhooks/whatsapp`.
7. Paste this Webhook Callback URL into Meta Console under **WhatsApp > Configuration > Edit Webhook**, select subscription fields `messages`, and verify.

---

### 2. Setting Up Meta Facebook Messenger API
1. Go to [Meta Developer Console](https://developers.facebook.com/) and select your Facebook App.
2. Add **Messenger** product and link your Facebook Page.
3. Generate a **Page Access Token** for your linked Facebook Page.
4. Open OmniDesk **Channel Integrations** (`/channels`) and edit the Facebook Messenger channel.
5. Enter your **Facebook Page ID**, **Page Access Token**, and **Verify Token**.
6. Set Webhook Callback URL: `https://yourdomain.com/api/v1/webhooks/facebook`.
7. Subscribe to `messages` and `messaging_postbacks` events in Meta Console.

---

### 3. Setting Up Meta Instagram Direct Messaging API
1. Connect your Instagram Professional / Creator account to a Facebook Page.
2. In Meta Developer Console, add **Instagram Graph API** permissions (`instagram_basic`, `instagram_manage_messages`).
3. Retrieve your **Instagram Business Account ID** and **Page Access Token**.
4. In OmniDesk **Channel Integrations** (`/channels`), edit the Instagram Direct channel.
5. Input your **Instagram Business Account ID** and **Page Access Token**.
6. Set Webhook Callback URL: `https://yourdomain.com/api/v1/webhooks/instagram`.
7. Subscribe to `messages` field in Meta Instagram Webhook settings.

---

### 4. Setting Up Telegram Bot API
1. Open Telegram app and start a chat with [@BotFather](https://t.me/botfather).
2. Send `/newbot` and follow prompt to create your support bot.
3. Copy the HTTP API **Bot Token** (e.g. `123456789:ABCdefGh...`).
4. In OmniDesk **Channel Integrations** (`/channels`), edit the Telegram Bot channel.
5. Enter your **Bot Username** (e.g. `@OmniSupportBot`) and **Bot Token**.
6. Register the webhook with Telegram by sending a POST request or browsing:
   ```http
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://yourdomain.com/api/v1/webhooks/telegram
   ```

---

## 📥 Inbound Webhook Lifecycle Architecture

```
[ Incoming Webhook Payload (WhatsApp / FB / IG / Telegram / Web) ]
                             │
                             ▼
     [ Signature & Verify Token Check (Security Validation) ]
                             │
                             ▼
     [ Contact Resolution (Match Phone / Email / External PSID / IGSID / Chat ID) ]
                             │
                             ▼
     [ Active Ticket Lookup (Status != resolved / closed) ]
         ├── Found? ───> Append Message & Update `last_activity_at`
         └── Not Found? ─> Auto-create New Ticket + Initial Customer Message
                             │
                             ▼
     [ Broadcast `MessageSent` Event via WebSockets to Agent Workspace ]
```
