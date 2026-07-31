<!-- Omnichannel Embedded Live Chat Widget -->
<div id="omni-chat-widget" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Floating Trigger Button -->
    <button id="omni-widget-toggle" class="chat-widget-trigger w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full flex items-center justify-center shadow-2xl shadow-indigo-500/50 transition transform hover:scale-105">
        <x-icon name="message-dots" class="text-3xl text-white" />
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
                <x-icon name="x" class="text-xl" />
            </button>
        </div>

        <!-- Messages Stream -->
        <div id="widget-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-xs">
        </div>

        <!-- Input Footer -->
        <div class="p-3 border-t border-slate-800 bg-slate-950 flex items-center gap-2">
            <input type="text" id="widget-input" placeholder="Type a message..." class="flex-1 bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-indigo-500">
            <button id="widget-send" class="p-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition">
                <x-icon name="send" class="text-lg" />
            </button>
        </div>
    </div>
</div>
