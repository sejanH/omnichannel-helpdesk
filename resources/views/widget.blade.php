<!-- Omnichannel Embedded Live Chat Widget -->
<div id="omni-chat-widget" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Floating Trigger Button -->
    <button id="omni-widget-toggle" class="chat-widget-trigger w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full flex items-center justify-center shadow-2xl shadow-indigo-500/50 transition transform hover:scale-105">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
    </button>

    <!-- Chat Popup Box -->
    <div id="omni-widget-box" class="hidden absolute bottom-18 right-0 w-96 h-[500px] bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden glass-panel">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-indigo-600 to-indigo-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></div>
                <div>
                    <h3 class="font-bold text-sm">Live Support</h3>
                    <p class="text-[11px] text-indigo-200">We typically reply in under 5 minutes</p>
                </div>
            </div>
            <button id="omni-widget-close" class="text-indigo-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Messages Stream -->
        <div id="widget-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-xs">
            <div class="chat-bubble incoming">
                👋 Hello! How can our support team help you today?
            </div>
        </div>

        <!-- Input Footer -->
        <div class="p-3 border-t border-slate-800 bg-slate-950 flex items-center gap-2">
            <input type="text" id="widget-input" placeholder="Type a message..." class="flex-1 bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-indigo-500">
            <button id="widget-send" class="p-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>
</div>
