# Agent Workspace & Dashboard Guide

The **Agent Workspace Dashboard** (`/`) is the command center for support agents. It provides a real-time, unified inbox for managing incoming support tickets across all integrated channels.

---

## 🖥️ Layout & Core Sections

```
+------------------+------------------------------+-------------------------------+
| Sidebar Nav      | Ticket Feed / Inbox          | Conversation & Reply Pane     |
| - Channel Filter | - Priority Badges            | - Message History             |
| - Status Filter  | - Channel Indicators         | - Reply Box vs Internal Note  |
| - Agent Status   | - Customer Avatar & Snippet  | - Canned Response Selector    |
+------------------+------------------------------+-------------------------------+
```

---

## 📌 Features & Capabilities

### 1. Multi-Channel Unified Inbox
- View incoming support requests in real-time.
- Filter tickets by **Channel** (Web Chat, WhatsApp, Email, Facebook, Telegram).
- Filter tickets by **Status**:
  - `Open`: Unhandled new tickets.
  - `In Progress`: Agent currently engaged with customer.
  - `Pending`: Waiting on customer response or external blocker.
  - `Resolved`: Ticket issue resolved.
  - `Closed`: Ticket finalized.

### 2. Priority & SLA Badging
Tickets display color-coded priority badges and SLA status indicators:
- 🔴 **Urgent**: High SLA risk / immediate escalation required.
- 🟠 **High**: Time-sensitive customer request.
- 🟡 **Medium**: Standard inquiry.
- ⚪ **Low**: General feedback or low urgency task.

### 3. Agent Replies vs. Internal Notes
Support team members can toggle between two communication modes:
- **Public Customer Reply**: Sent directly back to the customer's channel (e.g. live chat widget or WhatsApp).
- **Internal Note**: Highlighted with a distinct amber styling. Visible only to team members within the workspace. Used for internal handoffs, manager approvals, or context notes.

### 4. Canned Responses / Quick Reply Templates
- Agents can click the **Canned Response** menu or type shortcuts (e.g. `/greeting`, `/reset_password`, `/refund_policy`) to instantly insert pre-formatted support answers.

### 5. Real-Time Conversation Updates
- Leverages Laravel Broadcasting (`MessageSent` event).
- When a customer sends a message via the live chat widget or API, the ticket timeline and inbox snippet update dynamically without requiring a page refresh.

---

## 🎮 Workflow Example

1. Customer initiates a session on the live chat widget.
2. A new ticket (e.g. `TCK-3492`) appears at the top of the Unified Inbox under `Open`.
3. Agent selects the ticket to inspect customer history and channel context.
4. Agent posts an **Internal Note**: *"Checking account status with billing team."*
5. Agent inserts a **Canned Response**: *"Hello Alex! I am looking into your request right now."*
6. Ticket status automatically transitions to `In Progress`.
7. Once completed, agent updates ticket status to `Resolved`.
