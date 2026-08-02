(function () {
    'use strict';

    // Avoid duplicate initialization
    if (window.OmniChatWidgetInitialized) return;
    window.OmniChatWidgetInitialized = true;

    // Detect script tag parameters and host base URL
    let currentScript = document.currentScript;
    if (!currentScript) {
        const scripts = document.getElementsByTagName('script');
        currentScript = scripts[scripts.length - 1];
    }

    let channelId = 1;
    let baseUrl = window.location.origin;

    if (currentScript) {
        if (currentScript.getAttribute('data-channel-id')) {
            channelId = currentScript.getAttribute('data-channel-id');
        }
        if (currentScript.src) {
            try {
                const urlObj = new URL(currentScript.src);
                baseUrl = urlObj.origin;
            } catch (e) {
                console.warn('[OmniHelp] Could not parse script origin, defaulting to window location.');
            }
        }
    }

    // State Variables
    let config = {
        widget_color: '#6366f1',
        position: 'bottom-right',
        title: 'Customer Support',
        subtitle: 'We typically reply in under 5 minutes',
        welcome_message: '',
        logo_url: 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp',
        theme: 'dark',
        launcher_icon: 'chat',
        require_prechat: false
    };

    let isOpen = false;
    let sessionToken = localStorage.getItem('omni_session_token') || null;
    let ticketId = localStorage.getItem('omni_ticket_id') || null;
    let visitorName = localStorage.getItem('omni_visitor_name') || '';
    let visitorEmail = localStorage.getItem('omni_visitor_email') || '';
    let visitorPhone = localStorage.getItem('omni_visitor_phone') || '';
    let unreadCount = 0;
    let pollInterval = null;
    let knownMessageIds = new Set();
    let reverbConfig = null;
    let wsConnection = null;

    // Fetch Widget Configuration
    fetch(`${baseUrl}/api/v1/widget/config?channel_id=${channelId}`)
        .then(res => res.json())
        .then(data => {
            if (data.configuration) {
                config = Object.assign({}, config, data.configuration);
            }
            if (data.reverb) {
                reverbConfig = data.reverb;
            }
            initWidgetUI();
        })
        .catch(err => {
            console.error('[OmniHelp] Failed to load widget configuration:', err);
            initWidgetUI(); // Fallback to default
        });

    function getPositionStyles() {
        switch (config.position) {
            case 'bottom-left':
                return { launcher: 'bottom: 24px; left: 24px;', window: 'bottom: 90px; left: 24px;' };
            case 'top-right':
                return { launcher: 'top: 24px; right: 24px;', window: 'top: 90px; right: 24px;' };
            case 'top-left':
                return { launcher: 'top: 24px; left: 24px;', window: 'top: 90px; left: 24px;' };
            case 'bottom-right':
            default:
                return { launcher: 'bottom: 24px; right: 24px;', window: 'bottom: 90px; right: 24px;' };
        }
    }

    function getLauncherIconSvg(type) {
        if (config.logo_url) {
            return `<img src="${config.logo_url}" class="omni-w-7 omni-h-7 omni-rounded-full omni-object-cover" alt="Logo">`;
        }
        switch (type) {
            case 'sparkles':
                return `<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>`;
            case 'help':
                return `<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            case 'chat':
            default:
                return `<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>`;
        }
    }

    function initWidgetUI() {
        const pos = getPositionStyles();

        // Inject Stylesheet into page head
        const style = document.createElement('style');
        style.id = 'omni-widget-styles';
        style.innerHTML = `
            #omni-widget-container * {
                box-sizing: border-box;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            #omni-widget-launcher {
                position: fixed;
                ${pos.launcher}
                z-index: 999998;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background-color: ${config.widget_color};
                color: #ffffff;
                border: none;
                cursor: pointer;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
            }
            #omni-widget-launcher:hover {
                transform: scale(1.08);
                box-shadow: 0 15px 30px -5px ${config.widget_color}80;
            }
            #omni-widget-badge {
                position: absolute;
                top: -3px;
                right: -3px;
                background-color: #ef4444;
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid #ffffff;
            }
            #omni-widget-box {
                position: fixed;
                ${pos.window}
                z-index: 999999;
                width: 380px;
                max-width: calc(100vw - 32px);
                height: 540px;
                max-height: calc(100vh - 120px);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4);
                display: flex;
                flex-direction: column;
                background-color: ${config.theme === 'light' ? '#ffffff' : '#0f172a'};
                color: ${config.theme === 'light' ? '#1e293b' : '#f8fafc'};
                border: 1px solid ${config.theme === 'light' ? '#e2e8f0' : '#1e293b'};
                opacity: 0;
                transform: translateY(20px) scale(0.95);
                pointer-events: none;
                transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            #omni-widget-box.omni-open {
                opacity: 1;
                transform: translateY(0) scale(1);
                pointer-events: auto;
            }
            #omni-widget-container.omni-open #omni-widget-launcher,
            #omni-widget-launcher.omni-hidden {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            @media (max-width: 640px) {
                #omni-widget-box {
                    top: 0 !important;
                    bottom: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    width: 100vw !important;
                    max-width: 100vw !important;
                    height: 100vh !important;
                    max-height: 100vh !important;
                    border-radius: 0 !important;
                    border: none !important;
                    z-index: 2147483647 !important;
                }
                #omni-widget-launcher {
                    bottom: 16px !important;
                    right: 16px !important;
                    width: 54px !important;
                    height: 54px !important;
                }
            }
            .omni-header {
                background: linear-gradient(135deg, ${config.widget_color}, ${adjustColor(config.widget_color, -30)});
                padding: 16px;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .omni-header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .omni-avatar {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                object-fit: cover;
                border: 1.5px solid rgba(255, 255, 255, 0.4);
            }
            .omni-close-btn {
                background: transparent;
                border: none;
                color: rgba(255, 255, 255, 0.8);
                cursor: pointer;
                padding: 4px;
                border-radius: 50%;
                transition: background 0.2s ease, color 0.2s ease;
            }
            .omni-close-btn:hover {
                background: rgba(255, 255, 255, 0.2);
                color: #ffffff;
            }
            .omni-messages {
                flex: 1;
                padding: 16px;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 10px;
                background-color: ${config.theme === 'light' ? '#f8fafc' : '#020617'};
            }
            .omni-msg {
                max-width: 82%;
                padding: 10px 14px;
                border-radius: 16px;
                font-size: 13px;
                line-height: 1.45;
                word-wrap: break-word;
                animation: omniPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes omniPopIn {
                from { opacity: 0; transform: translateY(6px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .omni-msg-incoming {
                align-self: flex-start;
                background-color: ${config.theme === 'light' ? '#e2e8f0' : '#1e293b'};
                color: ${config.theme === 'light' ? '#0f172a' : '#f1f5f9'};
                border-bottom-left-radius: 4px;
            }
            .omni-msg-outgoing {
                align-self: flex-end;
                background-color: ${config.widget_color};
                color: #ffffff;
                border-bottom-right-radius: 4px;
            }
            .omni-msg-system {
                align-self: center;
                background-color: ${config.theme === 'light' ? '#f1f5f9' : '#0f172a'};
                color: ${config.theme === 'light' ? '#64748b' : '#94a3b8'};
                font-size: 11px;
                padding: 6px 12px;
                border-radius: 12px;
                border: 1px dashed ${config.theme === 'light' ? '#cbd5e1' : '#334155'};
            }
            .omni-time {
                font-size: 10px;
                opacity: 0.7;
                margin-top: 4px;
                display: block;
                text-align: right;
            }
            .omni-footer {
                padding: 12px;
                border-top: 1px solid ${config.theme === 'light' ? '#e2e8f0' : '#1e293b'};
                background-color: ${config.theme === 'light' ? '#ffffff' : '#0f172a'};
                display: flex;
                gap: 8px;
                align-items: center;
            }
            .omni-input {
                flex: 1;
                padding: 10px 14px;
                border-radius: 16px;
                border: 1px solid ${config.theme === 'light' ? '#cbd5e1' : '#334155'};
                background-color: ${config.theme === 'light' ? '#f8fafc' : '#020617'};
                color: ${config.theme === 'light' ? '#0f172a' : '#f8fafc'};
                font-size: 13px;
                font-family: inherit;
                outline: none;
                resize: none;
                max-height: 90px;
                transition: border-color 0.2s ease;
            }
            .omni-input:focus {
                border-color: ${config.widget_color};
            }
            .omni-send-btn {
                background-color: ${config.widget_color};
                color: #ffffff;
                border: none;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity 0.2s ease;
            }
            .omni-send-btn:hover {
                opacity: 0.9;
            }
            .omni-prechat-form {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 14px;
                flex: 1;
                justify-content: center;
            }
            .omni-field {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .omni-label {
                font-size: 12px;
                font-weight: 600;
                color: ${config.theme === 'light' ? '#475569' : '#94a3b8'};
            }
            .omni-submit-btn {
                background-color: ${config.widget_color};
                color: #ffffff;
                border: none;
                padding: 12px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 13px;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }
            .omni-branding {
                text-align: center;
                font-size: 10px;
                color: #94a3b8;
                padding: 6px 0 2px 0;
            }
        `;
        document.head.appendChild(style);

        // Container
        const container = document.createElement('div');
        container.id = 'omni-widget-container';
        container.innerHTML = `
            <button id="omni-widget-launcher" aria-label="Open Chat">
                ${getLauncherIconSvg(config.launcher_icon)}
                <span id="omni-widget-badge" style="display:none;">0</span>
            </button>
            <div id="omni-widget-box">
                <div class="omni-header">
                    <div class="omni-header-info">
                        <img src="${config.logo_url || 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp'}" class="omni-avatar" alt="Brand Logo">
                        <div>
                            <div style="font-weight: 700; font-size: 14px; line-height: 1.2;">${escapeHtml(config.title)}</div>
                            <div style="font-size: 11px; opacity: 0.85;">${escapeHtml(config.subtitle)}</div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <button id="omni-widget-end-chat" title="Close Ticket & Rate Support" style="font-size:11px; padding:4px 8px; background:rgba(255,255,255,0.18); border-radius:6px; border:none; color:white; cursor:pointer; font-weight:600; transition:background 0.2s;">
                            Close Ticket
                        </button>
                        <button class="omni-close-btn" id="omni-widget-close" aria-label="Close Chat">
                            <svg class="omni-w-5 omni-h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div id="omni-widget-content" style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
                    <!-- Dynamically rendered pre-chat form or messages -->
                </div>
                <div class="omni-branding">Powered by OmniHelp</div>
            </div>
        `;
        document.body.appendChild(container);

        // Event Listeners
        const launcher = document.getElementById('omni-widget-launcher');
        const closeBtn = document.getElementById('omni-widget-close');
        const endChatBtn = document.getElementById('omni-widget-end-chat');

        launcher.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);
        if (endChatBtn) {
            endChatBtn.addEventListener('click', function () {
                renderRatingSurvey();
            });
        }

        window.OmniHelpWidget = window.OmniDeskWidget = {
            toggle: toggleChat,
            open: function () { if (!isOpen) toggleChat(); },
            close: function () { if (isOpen) toggleChat(); }
        };

        // Check pre-chat or initialize directly
        if (!visitorName || (!visitorEmail && !visitorPhone)) {
            renderPreChatForm();
        } else {
            renderChatInterface();
            ensureSession();
        }
    }

    function toggleChat() {
        isOpen = !isOpen;
        const container = document.getElementById('omni-widget-container');
        const box = document.getElementById('omni-widget-box');
        const launcher = document.getElementById('omni-widget-launcher');
        const badge = document.getElementById('omni-widget-badge');
        
        if (isOpen) {
            if (container) container.classList.add('omni-open');
            if (box) box.classList.add('omni-open');
            if (launcher) {
                launcher.classList.add('omni-hidden');
                launcher.style.setProperty('display', 'none', 'important');
                launcher.style.setProperty('visibility', 'hidden', 'important');
                launcher.style.setProperty('opacity', '0', 'important');
            }
            unreadCount = 0;
            if (badge) badge.style.display = 'none';
            scrollToBottom();
        } else {
            if (container) container.classList.remove('omni-open');
            if (box) box.classList.remove('omni-open');
            if (launcher) {
                launcher.classList.remove('omni-hidden');
                launcher.style.setProperty('display', 'flex', 'important');
                launcher.style.setProperty('visibility', 'visible', 'important');
                launcher.style.setProperty('opacity', '1', 'important');
            }
        }
    }

    function renderPreChatForm() {
        const contentArea = document.getElementById('omni-widget-content');
        contentArea.innerHTML = `
            <form class="omni-prechat-form" id="omni-form-prechat">
                <div style="text-align:center; font-size:13px; font-weight:600; margin-bottom:4px;">Start a Conversation</div>
                <div style="text-align:center; font-size:11px; color:#94a3b8; margin-bottom:6px;">Provide your name & at least one contact method</div>
                <div id="omni-prechat-error" style="display:none; color:#f87171; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); padding:8px; border-radius:8px; font-size:11px; text-align:center;"></div>
                <div class="omni-field">
                    <label class="omni-label">Your Name *</label>
                    <input type="text" id="omni-input-name" class="omni-input" placeholder="e.g. Alex Smith" required>
                </div>
                <div class="omni-field">
                    <label class="omni-label">Email Address</label>
                    <input type="email" id="omni-input-email" class="omni-input" placeholder="alex@example.com">
                </div>
                <div class="omni-field">
                    <label class="omni-label">Phone Number</label>
                    <input type="tel" id="omni-input-phone" class="omni-input" placeholder="+1 234 567 8900">
                </div>
                <button type="submit" class="omni-submit-btn">Start Chat</button>
            </form>
        `;

        document.getElementById('omni-form-prechat').addEventListener('submit', function (e) {
            e.preventDefault();
            const errBox = document.getElementById('omni-prechat-error');
            errBox.style.display = 'none';

            visitorName = document.getElementById('omni-input-name').value.trim();
            visitorEmail = document.getElementById('omni-input-email').value.trim();
            visitorPhone = document.getElementById('omni-input-phone').value.trim();

            if (!visitorEmail && !visitorPhone) {
                errBox.textContent = 'Please provide either an Email address or a Phone number so we can reach you.';
                errBox.style.display = 'block';
                return;
            }

            localStorage.setItem('omni_visitor_name', visitorName);
            localStorage.setItem('omni_visitor_email', visitorEmail);
            localStorage.setItem('omni_visitor_phone', visitorPhone);

            renderChatInterface();
            ensureSession();
        });
    }

    function renderChatInterface() {
        const contentArea = document.getElementById('omni-widget-content');
        contentArea.innerHTML = `
            <div class="omni-messages" id="omni-messages-stream"></div>
            <form class="omni-footer" id="omni-form-msg">
                <textarea id="omni-msg-input" class="omni-input" rows="1" placeholder="Type your message..." autocomplete="off"></textarea>
                <button type="submit" class="omni-send-btn" aria-label="Send">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        `;

        const msgForm = document.getElementById('omni-form-msg');
        const msgInput = document.getElementById('omni-msg-input');
        let isSending = false;

        function handleSendMessage() {
            if (isSending) return;

            const input = document.getElementById('omni-msg-input');
            if (!input) return;
            const text = input.value.trim();
            if (!text || !ticketId) return;

            isSending = true;
            input.value = '';

            // Send to server & append ONLY when DB write succeeds
            fetch(`${baseUrl}/api/v1/widget/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    content: text,
                    sender_name: visitorName || 'Visitor'
                })
            })
            .then(res => res.json())
            .then(data => {
                isSending = false;
                if (data.message) {
                    const msgIdStr = String(data.message.id);
                    knownMessageIds.add(msgIdStr);
                    appendMessage(data.message);
                } else {
                    input.value = text;
                    alert('[OmniHelp] Failed to send message. Please try again.');
                }
            })
            .catch(err => {
                isSending = false;
                input.value = text;
                console.error('[OmniHelp] Error sending message:', err);
                alert('[OmniHelp] Network error while sending message. Please check your connection.');
            });
        }

        if (msgInput) {
            msgInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    handleSendMessage();
                }
            });
        }

        msgForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleSendMessage();
        });
    }

    function renderRatingSurvey() {
        const contentArea = document.getElementById('omni-widget-content');
        contentArea.innerHTML = `
            <div style="padding: 24px 20px; display:flex; flex-direction:column; gap:16px; align-items:center; justify-content:center; flex:1; text-align:center;">
                <div style="font-size: 15px; font-weight: 700;">Close Ticket & Rate Support</div>
                <div style="font-size: 12px; color: #94a3b8;">How was your experience with our support team?</div>
                
                <!-- 5-Star Rating Buttons -->
                <div id="omni-star-rating-box" style="display:flex; gap:8px; justify-content:center; margin: 8px 0;">
                    <button type="button" class="omni-star-btn" data-rating="1" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">⭐</button>
                    <button type="button" class="omni-star-btn" data-rating="2" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">⭐</button>
                    <button type="button" class="omni-star-btn" data-rating="3" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">⭐</button>
                    <button type="button" class="omni-star-btn" data-rating="4" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">⭐</button>
                    <button type="button" class="omni-star-btn" data-rating="5" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:1.0; transform:scale(1.2);">⭐</button>
                </div>

                <textarea id="omni-rating-comment" class="omni-input" rows="2" placeholder="Optional comments or feedback..." style="width:100%; box-sizing:border-box;"></textarea>

                <button type="button" id="omni-submit-rating-btn" class="omni-submit-btn" style="width:100%;">
                    Submit Rating & Close Ticket
                </button>
            </div>
        `;

        let selectedRating = 5;

        // Star Selection Event Listeners
        const starBtns = contentArea.querySelectorAll('.omni-star-btn');
        starBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                starBtns.forEach(b => {
                    const r = parseInt(b.getAttribute('data-rating'));
                    if (r <= selectedRating) {
                        b.style.opacity = '1.0';
                        b.style.transform = 'scale(1.1)';
                    } else {
                        b.style.opacity = '0.3';
                        b.style.transform = 'scale(0.9)';
                    }
                });
            });
        });

        // Submit Rating Event Listener
        document.getElementById('omni-submit-rating-btn').addEventListener('click', function () {
            const commentInput = document.getElementById('omni-rating-comment');
            const comment = commentInput ? commentInput.value.trim() : '';

            if (ticketId) {
                fetch(`${baseUrl}/api/v1/widget/rating`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ticket_id: ticketId,
                        rating: selectedRating,
                        comment: comment
                    })
                }).catch(e => console.error('[OmniHelp] Error submitting rating:', e));
            }

            renderChatEndedScreen();
        });
    }

    function renderChatEndedScreen() {
        // Clear active ticket ID so future chats initialize a brand new ticket!
        localStorage.removeItem('omni_ticket_id');
        ticketId = null;

        const contentArea = document.getElementById('omni-widget-content');
        contentArea.innerHTML = `
            <div style="padding: 28px 20px; display:flex; flex-direction:column; gap:16px; align-items:center; justify-content:center; flex:1; text-align:center;">
                <div style="width:52px; height:52px; border-radius:50%; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:bold;">✓</div>
                <div style="font-size: 16px; font-weight: 700;">Ticket Closed & Feedback Saved</div>
                <div style="font-size: 12px; color: #94a3b8;">Your ticket has been marked closed. Thank you for your feedback!</div>
                
                <button type="button" id="omni-start-new-chat-btn" class="omni-submit-btn" style="width:100%; margin-top:12px;">
                    Start New Ticket / Chat
                </button>
            </div>
        `;

        document.getElementById('omni-start-new-chat-btn').addEventListener('click', function () {
            if (config.require_prechat) {
                renderPreChatForm();
            } else {
                renderChatInterface();
                ensureSession();
            }
        });
    }

    function ensureSession() {
        fetch(`${baseUrl}/api/v1/widget/init`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                channel_id: channelId,
                session_token: sessionToken,
                name: visitorName,
                email: visitorEmail,
                phone: visitorPhone
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.session_token) {
                sessionToken = data.session_token;
                ticketId = data.ticket_id;
                localStorage.setItem('omni_session_token', sessionToken);
                localStorage.setItem('omni_ticket_id', ticketId);

                if (data.messages) {
                    const stream = document.getElementById('omni-messages-stream');
                    if (stream) stream.innerHTML = '';
                    data.messages.forEach(msg => {
                        const msgIdStr = String(msg.id);
                        knownMessageIds.add(msgIdStr);
                        appendMessage(msg);
                    });
                }
                startRealtimeOrPolling();
            }
        })
        .catch(err => console.error('[OmniHelp] Session initialization error:', err));
    }

    function startRealtimeOrPolling() {
        const wsStarted = initRealtimeWebSocket();
        if (!wsStarted) {
            startPolling();
        }
    }

    function initRealtimeWebSocket() {
        if (!ticketId || !reverbConfig || !reverbConfig.key) return false;

        try {
            const isHttps = window.location.protocol === 'https:' || reverbConfig.scheme === 'https';
            const wsProtocol = isHttps ? 'wss://' : 'ws://';

            let wsHost = reverbConfig.host;
            if (!wsHost || wsHost === '127.0.0.1' || wsHost === 'localhost' || wsHost === '0.0.0.0') {
                try {
                    wsHost = new URL(baseUrl).hostname;
                } catch (e) {
                    wsHost = window.location.hostname;
                }
            }

            let wsPort = '';
            const portNum = parseInt(reverbConfig.port);
            if (portNum && portNum !== 80 && portNum !== 443 && !isHttps) {
                wsPort = `:${portNum}`;
            }

            const wsUrl = `${wsProtocol}${wsHost}${wsPort}/app/${reverbConfig.key}?protocol=7&client=js&version=8.4.0&flash=false`;
            console.log('[OmniHelp Widget] Connecting WebSocket:', wsUrl);

            if (wsConnection && (wsConnection.readyState === WebSocket.OPEN || wsConnection.readyState === WebSocket.CONNECTING)) {
                return true;
            }

            wsConnection = new WebSocket(wsUrl);

            wsConnection.onopen = function () {
                console.log('[OmniHelp Widget] Connected to Laravel Reverb WebSockets!');
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }

                // Subscribe to Pusher/Reverb ticket channel
                wsConnection.send(JSON.stringify({
                    event: 'pusher:subscribe',
                    data: { channel: `ticket.${ticketId}` }
                }));
            };

            wsConnection.onmessage = function (evt) {
                try {
                    const parsed = JSON.parse(evt.data);
                    const eventName = parsed.event || '';
                    if (eventName.includes('message.sent') || eventName.includes('MessageSent')) {
                        let payload = typeof parsed.data === 'string' ? JSON.parse(parsed.data) : parsed.data;
                        const msg = payload.message || payload;
                        if (msg && msg.id) {
                            const msgIdStr = String(msg.id);
                            if (!knownMessageIds.has(msgIdStr)) {
                                knownMessageIds.add(msgIdStr);

                                // Only append agent & system messages (customer messages are rendered locally upon typing)
                                if (msg.sender_type !== 'customer') {
                                    appendMessage(msg);

                                    if (msg.sender_type === 'agent' && !isOpen) {
                                        unreadCount++;
                                        const badge = document.getElementById('omni-widget-badge');
                                        if (badge) {
                                            badge.textContent = unreadCount;
                                            badge.style.display = 'flex';
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.error('[OmniHelp Widget] Error parsing WS event:', e);
                }
            };

            wsConnection.onerror = function (err) {
                console.warn('[OmniHelp Widget] WebSocket error, falling back to polling:', err);
                startPolling();
            };

            wsConnection.onclose = function () {
                console.warn('[OmniHelp Widget] WebSocket closed, falling back to polling.');
                startPolling();
            };

            return true;
        } catch (e) {
            console.error('[OmniHelp Widget] WebSocket initialization failed:', e);
            return false;
        }
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(fetchNewMessages, 3500);
    }

    function fetchNewMessages() {
        if (!ticketId) return;

        fetch(`${baseUrl}/api/v1/widget/messages?ticket_id=${ticketId}`)
            .then(res => res.json())
            .then(data => {
                if (data.messages && Array.isArray(data.messages)) {
                    let hasNewAgentMsg = false;
                    data.messages.forEach(msg => {
                        const msgIdStr = String(msg.id);
                        if (!knownMessageIds.has(msgIdStr)) {
                            knownMessageIds.add(msgIdStr);

                            // Only append agent & system messages from polling
                            if (msg.sender_type !== 'customer') {
                                appendMessage(msg);
                                if (msg.sender_type === 'agent') {
                                    hasNewAgentMsg = true;
                                }
                            }
                        }
                    });

                    if (hasNewAgentMsg && !isOpen) {
                        unreadCount++;
                        const badge = document.getElementById('omni-widget-badge');
                        if (badge) {
                            badge.textContent = unreadCount;
                            badge.style.display = 'flex';
                        }
                    }
                }
            })
            .catch(err => console.error('[OmniHelp] Message polling error:', err));
    }

    function appendMessage(msg) {
        const stream = document.getElementById('omni-messages-stream');
        if (!stream) return;

        const msgIdStr = msg.id ? String(msg.id) : '';

        // Strict DOM ID check
        if (msgIdStr && document.getElementById(`omni-msg-${msgIdStr}`)) {
            return;
        }

        // Strict content deduplication check against the last rendered bubble
        const lastMsg = stream.lastElementChild;
        if (lastMsg && msg.content) {
            const lastText = lastMsg.innerText || lastMsg.textContent || '';
            if (lastText.includes(msg.content) && (msg.sender_type === 'customer' || lastMsg.classList.contains('omni-msg-outgoing'))) {
                if (msgIdStr && !lastMsg.id.includes(msgIdStr)) {
                    lastMsg.id = `omni-msg-${msgIdStr}`;
                }
                return;
            }
        }

        const isCustomer = msg.sender_type === 'customer';
        const isSystem = msg.sender_type === 'system';

        let msgClass = isSystem ? 'omni-msg-system' : (isCustomer ? 'omni-msg-outgoing' : 'omni-msg-incoming');
        const timeStr = new Date(msg.created_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const msgDiv = document.createElement('div');
        msgDiv.id = msgIdStr ? `omni-msg-${msgIdStr}` : `omni-msg-temp_${Date.now()}`;
        msgDiv.className = `omni-msg ${msgClass}`;

        if (isSystem) {
            msgDiv.textContent = msg.content;
        } else {
            msgDiv.innerHTML = `
                <div style="font-weight:600; font-size:10px; margin-bottom:2px; opacity:0.8;">${escapeHtml(msg.sender_name)}</div>
                <div>${escapeHtml(msg.content)}</div>
                <span class="omni-time">${timeStr}</span>
            `;
        }

        stream.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        const stream = document.getElementById('omni-messages-stream');
        if (stream) {
            stream.scrollTop = stream.scrollHeight;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    function adjustColor(col, amt) {
        let usePound = false;
        if (col[0] === "#") {
            col = col.slice(1);
            usePound = true;
        }
        let num = parseInt(col, 16);
        let r = (num >> 16) + amt;
        if (r > 255) r = 255; else if (r < 0) r = 0;
        let b = ((num >> 8) & 0x00FF) + amt;
        if (b > 255) b = 255; else if (b < 0) b = 0;
        let g = (num & 0x0000FF) + amt;
        if (g > 255) g = 255; else if (g < 0) g = 0;
        return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16).padStart(6, '0');
    }
})();
