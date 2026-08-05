(function(){"use strict";if(window.OmniChatWidgetInitialized)return;window.OmniChatWidgetInitialized=!0;let p=document.currentScript;if(!p){const e=document.getElementsByTagName("script");p=e[e.length-1]}let E=1,g=window.location.origin;if(p&&(p.getAttribute("data-channel-id")&&(E=p.getAttribute("data-channel-id")),p.src))try{g=new URL(p.src).origin}catch{console.warn("[OmniHelp] Could not parse script origin, defaulting to window location.")}let o={widget_color:"#6366f1",position:"bottom-right",title:"Customer Support",subtitle:"We typically reply in under 5 minutes",welcome_message:"",logo_url:"https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp",theme:"dark",launcher_icon:"chat",require_prechat:!1},f=!1,$=localStorage.getItem("omni_session_token")||null,d=localStorage.getItem("omni_ticket_id")||null,y=localStorage.getItem("omni_visitor_name")||"",w=localStorage.getItem("omni_visitor_email")||"",v=localStorage.getItem("omni_visitor_phone")||"",k=0,h=null,b=new Set,u=null,m=null;fetch(`${g}/api/v1/widget/config?channel_id=${E}`).then(e=>e.json()).then(e=>{e.configuration&&(o=Object.assign({},o,e.configuration)),e.reverb&&(u=e.reverb),H()}).catch(e=>{console.error("[OmniHelp] Failed to load widget configuration:",e),H()});function P(){switch(o.position){case"bottom-left":return{launcher:"bottom: 24px; left: 24px;",window:"bottom: 90px; left: 24px;"};case"top-right":return{launcher:"top: 24px; right: 24px;",window:"top: 90px; right: 24px;"};case"top-left":return{launcher:"top: 24px; left: 24px;",window:"top: 90px; left: 24px;"};default:return{launcher:"bottom: 24px; right: 24px;",window:"bottom: 90px; right: 24px;"}}}function W(e){if(o.logo_url)return`<img src="${o.logo_url}" class="omni-w-7 omni-h-7 omni-rounded-full omni-object-cover" alt="Logo">`;switch(e){case"sparkles":return'<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>';case"help":return'<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';default:return'<svg class="omni-w-6 omni-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'}}function H(){const e=P(),n=document.createElement("style");n.id="omni-widget-styles",n.innerHTML=`
            #omni-widget-container * {
                box-sizing: border-box;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            #omni-widget-launcher {
                position: fixed;
                ${e.launcher}
                z-index: 999998;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background-color: ${o.widget_color};
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
                box-shadow: 0 15px 30px -5px ${o.widget_color}80;
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
                ${e.window}
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
                background-color: ${o.theme==="light"?"#ffffff":"#0f172a"};
                color: ${o.theme==="light"?"#1e293b":"#f8fafc"};
                border: 1px solid ${o.theme==="light"?"#e2e8f0":"#1e293b"};
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
                background: linear-gradient(135deg, ${o.widget_color}, ${J(o.widget_color,-30)});
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
                background-color: ${o.theme==="light"?"#f8fafc":"#020617"};
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
                background-color: ${o.theme==="light"?"#e2e8f0":"#1e293b"};
                color: ${o.theme==="light"?"#0f172a":"#f1f5f9"};
                border-bottom-left-radius: 4px;
            }
            .omni-msg-outgoing {
                align-self: flex-end;
                background-color: ${o.widget_color};
                color: #ffffff;
                border-bottom-right-radius: 4px;
            }
            .omni-msg-system {
                align-self: center;
                background-color: ${o.theme==="light"?"#f1f5f9":"#0f172a"};
                color: ${o.theme==="light"?"#64748b":"#94a3b8"};
                font-size: 11px;
                padding: 6px 12px;
                border-radius: 12px;
                border: 1px dashed ${o.theme==="light"?"#cbd5e1":"#334155"};
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
                border-top: 1px solid ${o.theme==="light"?"#e2e8f0":"#1e293b"};
                background-color: ${o.theme==="light"?"#ffffff":"#0f172a"};
                display: flex;
                gap: 8px;
                align-items: center;
            }
            .omni-input {
                flex: 1;
                padding: 10px 14px;
                border-radius: 16px;
                border: 1px solid ${o.theme==="light"?"#cbd5e1":"#334155"};
                background-color: ${o.theme==="light"?"#f8fafc":"#020617"};
                color: ${o.theme==="light"?"#0f172a":"#f8fafc"};
                font-size: 13px;
                font-family: inherit;
                outline: none;
                resize: none;
                max-height: 90px;
                transition: border-color 0.2s ease;
            }
            .omni-input:focus {
                border-color: ${o.widget_color};
            }
            .omni-send-btn {
                background-color: ${o.widget_color};
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
                color: ${o.theme==="light"?"#475569":"#94a3b8"};
            }
            .omni-submit-btn {
                background-color: ${o.widget_color};
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
        `,document.head.appendChild(n);const t=document.createElement("div");t.id="omni-widget-container",t.innerHTML=`
            <button id="omni-widget-launcher" aria-label="Open Chat">
                ${W(o.launcher_icon)}
                <span id="omni-widget-badge" style="display:none;">0</span>
            </button>
            <div id="omni-widget-box">
                <div class="omni-header">
                    <div class="omni-header-info">
                        <img src="${o.logo_url||"https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp"}" class="omni-avatar" alt="Brand Logo">
                        <div>
                            <div style="font-weight: 700; font-size: 14px; line-height: 1.2;">${_(o.title)}</div>
                            <div style="font-size: 11px; opacity: 0.85;">${_(o.subtitle)}</div>
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
        `,document.body.appendChild(t);const i=document.getElementById("omni-widget-launcher"),s=document.getElementById("omni-widget-close"),r=document.getElementById("omni-widget-end-chat");i.addEventListener("click",S),s.addEventListener("click",S),r&&r.addEventListener("click",function(){N()}),window.OmniHelpWidget=window.OmniDeskWidget={toggle:S,open:function(){f||S()},close:function(){f&&S()}},!y||!w&&!v?T():(C(),z())}function S(){f=!f;const e=document.getElementById("omni-widget-container"),n=document.getElementById("omni-widget-box"),t=document.getElementById("omni-widget-launcher"),i=document.getElementById("omni-widget-badge");f?(e&&e.classList.add("omni-open"),n&&n.classList.add("omni-open"),t&&(t.classList.add("omni-hidden"),t.style.setProperty("display","none","important"),t.style.setProperty("visibility","hidden","important"),t.style.setProperty("opacity","0","important")),k=0,i&&(i.style.display="none"),j()):(e&&e.classList.remove("omni-open"),n&&n.classList.remove("omni-open"),t&&(t.classList.remove("omni-hidden"),t.style.setProperty("display","flex","important"),t.style.setProperty("visibility","visible","important"),t.style.setProperty("opacity","1","important")))}function T(){const e=document.getElementById("omni-widget-content");e.innerHTML=`
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
        `,document.getElementById("omni-form-prechat").addEventListener("submit",function(n){n.preventDefault();const t=document.getElementById("omni-prechat-error");if(t.style.display="none",y=document.getElementById("omni-input-name").value.trim(),w=document.getElementById("omni-input-email").value.trim(),v=document.getElementById("omni-input-phone").value.trim(),!w&&!v){t.textContent="Please provide either an Email address or a Phone number so we can reach you.",t.style.display="block";return}localStorage.setItem("omni_visitor_name",y),localStorage.setItem("omni_visitor_email",w),localStorage.setItem("omni_visitor_phone",v),C(),z()})}function C(){const e=document.getElementById("omni-widget-content");e.innerHTML=`
            <div class="omni-messages" id="omni-messages-stream"></div>
            <form class="omni-footer" id="omni-form-msg">
                <textarea id="omni-msg-input" class="omni-input" rows="1" placeholder="Type your message..." autocomplete="off"></textarea>
                <button type="submit" class="omni-send-btn" aria-label="Send">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        `;const n=document.getElementById("omni-form-msg"),t=document.getElementById("omni-msg-input");let i=!1;function s(){if(i)return;const r=document.getElementById("omni-msg-input");if(!r)return;const a=r.value.trim();!a||!d||(i=!0,r.value="",fetch(`${g}/api/v1/widget/messages`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({ticket_id:d,content:a,sender_name:y||"Visitor"})}).then(l=>l.json()).then(l=>{if(i=!1,l.message){const c=String(l.message.id);b.add(c),I(l.message)}else r.value=a,alert("[OmniHelp] Failed to send message. Please try again.")}).catch(l=>{i=!1,r.value=a,console.error("[OmniHelp] Error sending message:",l),alert("[OmniHelp] Network error while sending message. Please check your connection.")}))}t&&t.addEventListener("keydown",function(r){r.key==="Enter"&&!r.shiftKey&&!r.ctrlKey&&!r.metaKey&&(r.preventDefault(),s())}),n.addEventListener("submit",function(r){r.preventDefault(),s()})}function N(){const e=document.getElementById("omni-widget-content");e.innerHTML=`
            <div style="padding: 24px 20px; display:flex; flex-direction:column; gap:16px; align-items:center; justify-content:center; flex:1; text-align:center;">
                <div style="font-size: 15px; font-weight: 700;">Close Ticket & Rate Support</div>
                <div style="font-size: 12px; color: #94a3b8;">How was your experience with our support team?</div>
                
                <!-- 5-Star Rating Buttons -->
                <div id="omni-star-rating-box" style="display:flex; gap:8px; justify-content:center; margin: 8px 0;">
                    <button type="button" class="omni-star-btn" data-rating="1" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">\u2B50</button>
                    <button type="button" class="omni-star-btn" data-rating="2" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">\u2B50</button>
                    <button type="button" class="omni-star-btn" data-rating="3" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">\u2B50</button>
                    <button type="button" class="omni-star-btn" data-rating="4" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:0.3; transition: transform 0.2s;">\u2B50</button>
                    <button type="button" class="omni-star-btn" data-rating="5" style="font-size: 26px; background:none; border:none; cursor:pointer; opacity:1.0; transform:scale(1.2);">\u2B50</button>
                </div>

                <textarea id="omni-rating-comment" class="omni-input" rows="2" placeholder="Optional comments or feedback..." style="width:100%; box-sizing:border-box;"></textarea>

                <button type="button" id="omni-submit-rating-btn" class="omni-submit-btn" style="width:100%;">
                    Submit Rating & Close Ticket
                </button>
            </div>
        `;let n=5;const t=e.querySelectorAll(".omni-star-btn");t.forEach(i=>{i.addEventListener("click",function(){n=parseInt(this.getAttribute("data-rating")),t.forEach(s=>{parseInt(s.getAttribute("data-rating"))<=n?(s.style.opacity="1.0",s.style.transform="scale(1.1)"):(s.style.opacity="0.3",s.style.transform="scale(0.9)")})})}),document.getElementById("omni-submit-rating-btn").addEventListener("click",function(){const i=document.getElementById("omni-rating-comment"),s=i?i.value.trim():"";d&&fetch(`${g}/api/v1/widget/rating`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({ticket_id:d,rating:n,comment:s})}).catch(r=>console.error("[OmniHelp] Error submitting rating:",r)),A()})}function A(){localStorage.removeItem("omni_ticket_id"),d=null;const e=document.getElementById("omni-widget-content");e.innerHTML=`
            <div style="padding: 28px 20px; display:flex; flex-direction:column; gap:16px; align-items:center; justify-content:center; flex:1; text-align:center;">
                <div style="width:52px; height:52px; border-radius:50%; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:bold;">\u2713</div>
                <div style="font-size: 16px; font-weight: 700;">Ticket Closed & Feedback Saved</div>
                <div style="font-size: 12px; color: #94a3b8;">Your ticket has been marked closed. Thank you for your feedback!</div>
                
                <button type="button" id="omni-start-new-chat-btn" class="omni-submit-btn" style="width:100%; margin-top:12px;">
                    Start New Ticket / Chat
                </button>
            </div>
        `,document.getElementById("omni-start-new-chat-btn").addEventListener("click",function(){o.require_prechat?T():(C(),z())})}function z(){fetch(`${g}/api/v1/widget/init`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({channel_id:E,session_token:$,name:y,email:w,phone:v})}).then(e=>e.json()).then(e=>{if(e.session_token){if($=e.session_token,d=e.ticket_id,localStorage.setItem("omni_session_token",$),localStorage.setItem("omni_ticket_id",d),e.messages){const n=document.getElementById("omni-messages-stream");n&&(n.innerHTML=""),e.messages.forEach(t=>{const i=String(t.id);b.add(i),I(t)})}R()}}).catch(e=>console.error("[OmniHelp] Session initialization error:",e))}function R(){F()||B()}function F(){if(!d||!u||!u.key)return!1;try{const e=window.location.protocol==="https:"||u.scheme==="https",n=e?"wss://":"ws://";let t=u.host;if(!t||t==="127.0.0.1"||t==="localhost"||t==="0.0.0.0")try{t=new URL(g).hostname}catch{t=window.location.hostname}let i="";const s=parseInt(u.port);s&&s!==80&&s!==443&&!e&&(i=`:${s}`);const r=`${n}${t}${i}/app/${u.key}?protocol=7&client=js&version=8.4.0&flash=false`;return console.log("[OmniHelp Widget] Connecting WebSocket:",r),m&&(m.readyState===WebSocket.OPEN||m.readyState===WebSocket.CONNECTING)||(m=new WebSocket(r),m.onopen=function(){console.log("[OmniHelp Widget] Connected to Laravel Reverb WebSockets!"),h&&(clearInterval(h),h=null),m.send(JSON.stringify({event:"pusher:subscribe",data:{channel:`ticket.${d}`}}))},m.onmessage=function(a){try{const l=JSON.parse(a.data),c=l.event||"";if(c.includes("message.sent")||c.includes("MessageSent")){let O=typeof l.data=="string"?JSON.parse(l.data):l.data;const x=O.message||O;if(x&&x.id){const M=String(x.id);if(!b.has(M)&&(b.add(M),x.sender_type!=="customer"&&(I(x),x.sender_type==="agent"&&!f))){k++;const L=document.getElementById("omni-widget-badge");L&&(L.textContent=k,L.style.display="flex")}}}}catch(l){console.error("[OmniHelp Widget] Error parsing WS event:",l)}},m.onerror=function(a){console.warn("[OmniHelp Widget] WebSocket error, falling back to polling:",a),B()},m.onclose=function(){console.warn("[OmniHelp Widget] WebSocket closed, falling back to polling."),B()}),!0}catch(e){return console.error("[OmniHelp Widget] WebSocket initialization failed:",e),!1}}function B(){h&&clearInterval(h),h=setInterval(D,3500)}function D(){d&&fetch(`${g}/api/v1/widget/messages?ticket_id=${d}`).then(e=>e.json()).then(e=>{if(e.messages&&Array.isArray(e.messages)){let n=!1;if(e.messages.forEach(t=>{const i=String(t.id);b.has(i)||(b.add(i),t.sender_type!=="customer"&&(I(t),t.sender_type==="agent"&&(n=!0)))}),n&&!f){k++;const t=document.getElementById("omni-widget-badge");t&&(t.textContent=k,t.style.display="flex")}}}).catch(e=>console.error("[OmniHelp] Message polling error:",e))}function I(e){const n=document.getElementById("omni-messages-stream");if(!n)return;const t=e.id?String(e.id):"";if(t&&document.getElementById(`omni-msg-${t}`))return;const i=n.lastElementChild;if(i&&e.content&&(i.innerText||i.textContent||"").includes(e.content)&&(e.sender_type==="customer"||i.classList.contains("omni-msg-outgoing"))){t&&!i.id.includes(t)&&(i.id=`omni-msg-${t}`);return}const s=e.sender_type==="customer",r=e.sender_type==="system";let a=r?"omni-msg-system":s?"omni-msg-outgoing":"omni-msg-incoming";const l=new Date(e.created_at||Date.now()).toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"}),c=document.createElement("div");c.id=t?`omni-msg-${t}`:`omni-msg-temp_${Date.now()}`,c.className=`omni-msg ${a}`,r?c.textContent=e.content:c.innerHTML=`
                <div style="font-weight:600; font-size:10px; margin-bottom:2px; opacity:0.8;">${_(e.sender_name)}</div>
                <div>${_(e.content)}</div>
                <span class="omni-time">${l}</span>
            `,n.appendChild(c),j()}function j(){const e=document.getElementById("omni-messages-stream");e&&(e.scrollTop=e.scrollHeight)}function _(e){return e?e.replace(/[&<>"']/g,function(n){return{"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[n]}):""}function J(e,n){let t=!1;e[0]==="#"&&(e=e.slice(1),t=!0);let i=parseInt(e,16),s=(i>>16)+n;s>255?s=255:s<0&&(s=0);let r=(i>>8&255)+n;r>255?r=255:r<0&&(r=0);let a=(i&255)+n;return a>255?a=255:a<0&&(a=0),(t?"#":"")+(a|r<<8|s<<16).toString(16).padStart(6,"0")}})();
