# Embeddable Live Chat Widget Guide

The **Live Chat Widget** enables website visitors to initiate live support conversations directly with your support team.

Demo URL: [http://localhost:8000/demo](http://localhost:8000/demo)

---

## 🎨 Widget Customization & Configuration

The widget is dynamically configured via the database (`channels` table `configuration` JSON field) or updated via the Admin API:

```json
{
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
```

### Configurable Parameters

| Option | Type | Values / Example | Description |
| :--- | :--- | :--- | :--- |
| `widget_color` | String | `#6366f1`, `#06b6d4`, `#10b981` | Accent color for launcher button, headers, and outgoing bubbles. |
| `position` | String | `bottom-right`, `bottom-left`, `top-right`, `top-left` | Screen placement of the floating trigger button. |
| `title` | String | `"Customer Support"` | Main header title displayed in the widget window. |
| `subtitle` | String | `"We reply in minutes"` | Subheading text displayed under the title. |
| `welcome_message` | String | `"👋 How can we help?"` | Auto-generated welcome greeting when session opens. |
| `theme` | String | `dark`, `light`, `auto` | Visual color theme of the chat container. |
| `launcher_icon` | String | `chat`, `sparkles`, `help`, `message` | Icon rendered inside the launcher button. |
| `require_prechat` | Boolean | `true`, `false` | Prompts visitors for Name & Email before starting a chat session. |

---

## 💻 Embedding on External Websites

To embed the widget on any website, include the following JavaScript snippet before `</body>`:

```html
<!-- OmniDesk Live Chat Widget -->
<script>
  window.OmniDeskConfig = {
    channelId: 1, // ID of your Web Chat Channel
    apiHost: "http://localhost:8000"
  };
</script>
<script src="http://localhost:8000/js/widget-loader.js" async></script>
```

---

## 🔄 Visitor Session Lifecycle

1. **Initialization (`/api/v1/widget/init`)**:
   - Checks local storage for an existing `session_token`.
   - If missing, generates a unique guest token (e.g. `vis_8f9a2b...`) and creates or re-uses a `Contact` record.
   - Assigns or resumes an active `Ticket` for the visitor session.

2. **Fetching History (`/api/v1/widget/messages`)**:
   - Loads existing chat history for the active ticket.
   - Ignores internal agent notes (`is_internal_note: true`).

3. **Sending Messages (`/api/v1/widget/messages`)**:
   - Visitor types a message and clicks send.
   - Message is stored in `messages` table with `sender_type: "customer"`.
   - Broadcasts real-time event to the agent dashboard.

4. **Receiving Replies**:
   - Listens to broadcast channels for `MessageSent` events to append agent responses instantly.
