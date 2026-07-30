# REST API Reference

This document outlines the public Live Chat Widget API endpoints and internal ticket API endpoints available in the system.

---

## 🔌 Public Live Chat Widget APIs (`/api/v1/widget`)

### 1. Get Widget Configuration
Returns styling, parameters, and brand settings for the floating live chat widget.

- **URL**: `GET /api/v1/widget/config`
- **Query Parameters**:
  - `channel_id` *(optional, integer)*: ID of a specific web chat channel.

#### Response `200 OK`
```json
{
  "channel_id": 1,
  "channel_name": "Website Live Chat",
  "is_active": true,
  "configuration": {
    "widget_color": "#6366f1",
    "position": "bottom-right",
    "title": "Customer Support",
    "subtitle": "We typically reply in under 5 minutes",
    "welcome_message": "👋 Hello! How can our support team help you today?",
    "logo_url": "https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk",
    "theme": "dark",
    "launcher_icon": "chat",
    "require_prechat": false
  }
}
```

---

### 2. Update Widget Configuration
Updates widget parameters for a specified channel (Admin / Workspace setting).

- **URL**: `POST /api/v1/widget/config/{channel}`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
  "widget_color": "#06b6d4",
  "position": "bottom-right",
  "title": "OmniDesk Live Help",
  "subtitle": "Online now",
  "welcome_message": "Welcome! How can we assist you?",
  "theme": "dark",
  "launcher_icon": "sparkles",
  "require_prechat": false
}
```

#### Response `200 OK`
```json
{
  "success": true,
  "message": "Widget configuration updated successfully!",
  "channel": { ... }
}
```

---

### 3. Initialize Visitor Session
Creates or resumes a visitor session, matches/creates a `Contact`, and assigns an active `Ticket`.

- **URL**: `POST /api/v1/widget/init`
- **Body**:
```json
{
  "channel_id": 1,
  "session_token": "vis_a1b2c3d4e5f6g7h8",
  "name": "Sarah Connor",
  "email": "sarah@example.com"
}
```

#### Response `200 OK`
```json
{
  "success": true,
  "session_token": "vis_a1b2c3d4e5f6g7h8",
  "ticket_id": 4,
  "ticket_number": "TCK-4892",
  "contact": {
    "id": 2,
    "name": "Sarah Connor",
    "email": "sarah@example.com"
  },
  "messages": [
    {
      "id": 12,
      "ticket_id": 4,
      "sender_type": "system",
      "sender_name": "Customer Support",
      "content": "👋 Hello! How can our support team help you today?",
      "is_internal_note": false,
      "created_at": "2026-07-30T13:00:00.000000Z"
    }
  ]
}
```

---

### 4. Fetch Ticket Messages
Retrieves non-internal messages for an active ticket.

- **URL**: `GET /api/v1/widget/messages?ticket_id={ticket_id}`

#### Response `200 OK`
```json
{
  "ticket_id": 4,
  "status": "open",
  "messages": [ ... ]
}
```

---

### 5. Send Visitor Message
Sends a new message from the live chat widget to the support team.

- **URL**: `POST /api/v1/widget/messages`
- **Body**:
```json
{
  "ticket_id": 4,
  "content": "I need help configuring my custom domain SSL certificate.",
  "sender_name": "Sarah Connor"
}
```

#### Response `200 OK`
```json
{
  "success": true,
  "message": {
    "id": 18,
    "ticket_id": 4,
    "sender_type": "customer",
    "sender_id": 2,
    "sender_name": "Sarah Connor",
    "content": "I need help configuring my custom domain SSL certificate.",
    "is_internal_note": false,
    "created_at": "2026-07-30T13:05:00.000000Z"
  }
}
```

---

## 🎯 Agent Workspace Internal Ticket APIs

### 1. Fetch Ticket Messages (Agent Workspace)
- **URL**: `GET /tickets/{ticket}/messages`

### 2. Send Agent Message / Internal Note
- **URL**: `POST /tickets/{ticket}/messages`
- **Body**:
```json
{
  "content": "Checking domain DNS records now.",
  "is_internal_note": true
}
```
