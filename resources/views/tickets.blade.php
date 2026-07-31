@extends('layouts.app')

@section('title', 'Agent Workspace — OmniDesk Helpdesk')

@section('content')
        <!-- VIEW 1: TICKETS & CHAT WORKSPACE -->
        <div id="view-tickets-workspace" class="flex-1 flex overflow-hidden">
            <!-- Ticket Inbox List (Column 1) -->
            <section class="w-96 border-r border-slate-800 bg-slate-900/50 flex flex-col shrink-0">
                <!-- Header Search & Filter -->
                <div class="p-4 border-b border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-base text-slate-100">Conversations</h2>
                        <span
                            class="text-xs text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20 font-mono flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Reverb Active
                        </span>
                    </div>
                    <input type="text" id="search-tickets" placeholder="Search customer, ticket # or text..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                    <select id="filter-tickets" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        <option value="all">All Conversations</option>
                        <option value="unread">Unread / New</option>
                        <option value="open">Open / In Progress</option>
                        <option value="resolved">Resolved / Closed</option>
                    </select>
                </div>

                <!-- Tickets Stream -->
                <div id="tickets-list" class="flex-1 overflow-y-auto divide-y divide-slate-800/60">
                    @forelse($tickets as $ticket)
                        <div class="ticket-card p-4 hover:bg-slate-800/40 cursor-pointer transition border-l-4 {{ $loop->first ? 'border-indigo-500 bg-slate-800/30' : 'border-transparent' }}"
                            data-ticket-id="{{ $ticket->id }}"
                            data-status="{{ $ticket->status }}"
                            data-unread="{{ $ticket->unread_messages_count > 0 ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="badge-channel badge-{{ $ticket->channel->type ?? 'web' }}">
                                    {{ $ticket->channel->name ?? 'Web' }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @if($ticket->unread_messages_count > 0)
                                        <span class="unread-badge bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20">
                                            {{ $ticket->unread_messages_count }} New
                                        </span>
                                    @endif
                                    <span class="badge-priority priority-{{ $ticket->priority }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </div>
                            </div>
                            <h3 class="font-semibold text-sm text-slate-200 line-clamp-1 mb-1">{{ $ticket->subject }}</h3>
                            <p class="text-xs text-slate-400 line-clamp-1 mb-2">
                                {{ $ticket->latestMessage->content ?? 'No messages yet.' }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span
                                    class="font-medium text-slate-400">{{ $ticket->contact->name ?? 'Unknown Contact' }}</span>
                                <span>{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500 text-sm">
                            No active tickets available.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Active Chat Window (Column 2) -->
            <section class="flex-1 flex flex-col bg-slate-950">
                <!-- Active Ticket Top Bar -->
                <div id="chat-header"
                    class="p-4 border-b border-slate-800 bg-slate-900/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-indigo-400">
                            TCK
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 id="active-ticket-subject" class="font-bold text-slate-100 text-base">Select a
                                    conversation</h2>
                                <span id="active-ticket-number"
                                    class="text-xs font-mono text-slate-400">#TCK-1001</span>
                            </div>
                            <div class="text-xs text-slate-400 flex items-center gap-2">
                                <span id="active-contact-name">Customer</span>
                                <span>•</span>
                                <span id="active-channel-name" class="text-indigo-400">Web Chat</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="btn-mark-resolved"
                            class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-xs font-semibold rounded-lg border border-emerald-500/30 transition">
                            Mark Resolved
                        </button>
                    </div>
                </div>

                <!-- Messages Stream Area -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="text-center text-slate-500 text-xs py-8">
                        Select a ticket from the left panel to start chatting.
                    </div>
                </div>

                <!-- Chat Reply Input & Quick Responses -->
                <div class="p-4 border-t border-slate-800 bg-slate-900/90 space-y-3">
                    <!-- Canned Responses Quick Toolbar -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                        <span class="text-slate-400 font-medium shrink-0">Canned:</span>
                        @foreach($cannedResponses as $response)
                            <button type="button"
                                class="btn-canned shrink-0 px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 rounded-md border border-slate-700 transition"
                                data-content="{{ $response->content }}">
                                {{ $response->shortcut }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Input Box -->
                    <form id="message-form" class="space-y-2">
                        @csrf
                        <div class="relative">
                            <textarea id="message-input" rows="3"
                                placeholder="Type your response here... (or type internal note)"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-amber-400 font-medium cursor-pointer">
                                <input type="checkbox" id="is-internal-note"
                                    class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500">
                                Internal Note (Only visible to agents)
                            </label>
                            <button type="submit"
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                                <span>Send Reply</span>
                                <x-icon name="send" class="text-sm" />
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- VIEW 2: FLOATING WIDGET BUILDER & EMBED CONFIGURATION -->
        <div id="view-widget-studio" class="flex-1 flex overflow-hidden hidden bg-slate-950">
            <!-- Settings Form Column -->
            <div class="w-1/2 border-r border-slate-800 p-6 overflow-y-auto space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-100">Floating Live Chat Widget Configurator</h2>
                        <p class="text-xs text-slate-400 mt-1">Customize position, colors, title, logo, and pre-chat
                            requirements like Tawk.to</p>
                    </div>
                    <button type="button" id="save-widget-config"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                        <x-icon name="check" class="text-sm" />
                        <span>Save Configuration</span>
                    </button>
                </div>

                <div id="config-alert"
                    class="hidden p-3 rounded-xl text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    Configuration saved successfully!
                </div>

                <form id="widget-settings-form" class="space-y-5">
                    <!-- Brand / Theme Color -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2">Widget Brand Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="cfg-color" value="#6366f1"
                                class="w-10 h-10 rounded-lg cursor-pointer bg-slate-900 border border-slate-700 p-1">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#6366f1] ring-2 ring-white/20"
                                    data-color="#6366f1"></button>
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#10b981] ring-2 ring-white/20"
                                    data-color="#10b981"></button>
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#f43f5e] ring-2 ring-white/20"
                                    data-color="#f43f5e"></button>
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#f59e0b] ring-2 ring-white/20"
                                    data-color="#f59e0b"></button>
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#06b6d4] ring-2 ring-white/20"
                                    data-color="#06b6d4"></button>
                                <button type="button"
                                    class="btn-color-preset w-7 h-7 rounded-full bg-[#475569] ring-2 ring-white/20"
                                    data-color="#475569"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Screen Position Location -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2">Widget Position on
                            Website</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="bottom-right" checked
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-200">Bottom Right</div>
                                    <div class="text-[10px] text-slate-500">Standard floating position</div>
                                </div>
                            </label>
                            <label
                                class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="bottom-left"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-200">Bottom Left</div>
                                    <div class="text-[10px] text-slate-500">Left edge alignment</div>
                                </div>
                            </label>
                            <label
                                class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="top-right"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-200">Top Right</div>
                                    <div class="text-[10px] text-slate-500">Header top right position</div>
                                </div>
                            </label>
                            <label
                                class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-900 border border-slate-800 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="top-left"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-200">Top Left</div>
                                    <div class="text-[10px] text-slate-500">Header top left position</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Header Title</label>
                            <input type="text" id="cfg-title" value="Customer Support"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Header Subtitle</label>
                            <input type="text" id="cfg-subtitle" value="We typically reply in under 5 minutes"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Welcome Greeting -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Welcome Message /
                            Greeting</label>
                        <textarea id="cfg-welcome" rows="2" placeholder="Optional greeting message..."
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Logo / Avatar URL -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Logo / Avatar URL</label>
                        <input type="url" id="cfg-logo" value="https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>

                    <!-- Theme & Icon Options -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Widget UI Theme</label>
                            <select id="cfg-theme"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="dark" selected>Dark Mode (Default)</option>
                                <option value="light">Light Mode</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Floating Button
                                Icon</label>
                            <select id="cfg-icon"
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="chat" selected>Chat Bubble Icon</option>
                                <option value="sparkles">Sparkles Icon</option>
                                <option value="help">Question Mark Icon</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pre-chat Form Toggle -->
                    <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Require Pre-Chat Form</div>
                            <div class="text-[10px] text-slate-400">Collect visitor Name and Email before starting chat
                            </div>
                        </div>
                        <input type="checkbox" id="cfg-prechat"
                            class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    </div>
                </form>
            </div>

            <!-- Preview & Embed Code Column -->
            <div class="w-1/2 p-6 flex flex-col justify-between overflow-y-auto space-y-6">
                <!-- Interactive Live Preview Card -->
                <div
                    class="flex-1 border border-slate-800 rounded-2xl bg-slate-900/60 p-5 flex flex-col relative overflow-hidden glass-panel">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="ml-2 text-xs text-slate-400 font-mono">Live Interactive Preview</span>
                        </div>
                        <span class="text-[10px] bg-slate-800 text-indigo-300 px-2 py-0.5 rounded font-mono">Simulated
                            Website</span>
                    </div>

                    <!-- Mock website container with widget floating live -->
                    <div
                        class="flex-1 bg-slate-950 border border-slate-800/80 rounded-xl p-4 relative flex flex-col justify-center items-center overflow-hidden">
                        <div class="text-center max-w-sm space-y-2 opacity-40">
                            <div class="h-4 bg-slate-800 rounded w-3/4 mx-auto"></div>
                            <div class="h-3 bg-slate-800 rounded w-1/2 mx-auto"></div>
                            <div class="h-3 bg-slate-800 rounded w-5/6 mx-auto"></div>
                        </div>

                        <!-- Live Interactive Mock Widget Floating Element -->
                        <div id="prev-widget-box"
                            class="absolute bottom-16 right-4 w-72 h-[340px] bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden transition-all duration-300">
                            <!-- Header -->
                            <div id="prev-header" class="p-3 text-white flex items-center justify-between"
                                style="background-color: #6366f1;">
                                <div class="flex items-center gap-2.5">
                                    <img id="prev-logo-img" src="https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk"
                                        class="w-7 h-7 rounded-full bg-white/20 object-cover border border-white/30"
                                        alt="Logo">
                                    <div>
                                        <h4 id="prev-header-title" class="font-bold text-xs leading-tight">Customer
                                            Support</h4>
                                        <p id="prev-header-subtitle" class="text-[10px] opacity-80 leading-tight">We
                                            reply in 5m</p>
                                    </div>
                                </div>
                                <span class="text-xs opacity-70">✕</span>
                            </div>

                            <!-- Stream -->
                            <div class="flex-1 p-3 space-y-2 text-[11px] overflow-y-auto bg-slate-950">
                                <div id="prev-welcome-bubble"
                                    class="p-2.5 rounded-xl bg-slate-800 text-slate-200 border border-slate-700/60 max-w-[88%] leading-relaxed">
                                    👋 Hello! How can our support team help you today?
                                </div>
                                <div
                                    class="p-2.5 rounded-xl bg-indigo-600 text-white ml-auto max-w-[80%] leading-relaxed">
                                    Hi! I have a question about pricing.
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="p-2.5 border-t border-slate-800 bg-slate-900 flex items-center gap-2">
                                <input type="text" disabled placeholder="Type a message..."
                                    class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-300">
                                <button type="button" id="prev-send-btn" class="p-1.5 text-white rounded-lg transition flex items-center justify-center"
                                    style="background-color: #6366f1;">
                                    <x-icon name="send" class="text-xs" />
                                </button>
                            </div>
                        </div>

                        <!-- Floating Launcher Button Mock -->
                        <button id="prev-launcher-btn" type="button"
                            class="absolute bottom-3 right-4 w-11 h-11 text-white rounded-full flex items-center justify-center shadow-xl transition transform hover:scale-105"
                            style="background-color: #6366f1;">
                            <x-icon name="message-dots" class="text-lg" />
                        </button>
                    </div>
                </div>

                <!-- Embed Script Code Snippet Box -->
                <div class="border border-slate-800 rounded-2xl bg-slate-900 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-slate-200">Client Embed Script Snippet</h3>
                            <p class="text-xs text-slate-400">Copy & paste this script tag before the closing
                                <code>&lt;/body&gt;</code> tag on your site.
                            </p>
                        </div>
                        <button type="button" id="copy-snippet-btn"
                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-indigo-300 font-semibold text-xs rounded-lg border border-slate-700 transition flex items-center gap-1.5">
                            <x-icon name="copy" class="text-sm" />
                            <span id="copy-btn-text">Copy Code</span>
                        </button>
                    </div>
                    <pre
                        class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80 text-xs font-mono text-cyan-300 overflow-x-auto"><code>&lt;!-- OmniDesk Floating Live Chat Widget --&gt;
&lt;script src="{{ url('/') }}/widget.js" data-channel-id="1" async&gt;&lt;/script&gt;</code></pre>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <!-- jQuery & Workspace View Logic -->
    <script type="module">
        $(document).ready(function () {
            let activeTicketId = null;

            // View Switching Navigation
            $('#nav-tickets').on('click', function () {
                $('#nav-widget-config').removeClass('bg-indigo-600/20 text-indigo-400 border border-indigo-500/30').addClass('text-slate-400');
                $(this).addClass('bg-indigo-600/20 text-indigo-400 border border-indigo-500/30').removeClass('text-slate-400');
                $('#view-widget-studio').addClass('hidden');
                $('#view-tickets-workspace').removeClass('hidden');
            });

            $('#nav-widget-config').on('click', function () {
                $('#nav-tickets').removeClass('bg-indigo-600/20 text-indigo-400 border border-indigo-500/30').addClass('text-slate-400');
                $(this).addClass('bg-indigo-600/20 text-indigo-400 border border-indigo-500/30').removeClass('text-slate-400');
                $('#view-tickets-workspace').addClass('hidden');
                $('#view-widget-studio').removeClass('hidden');
                loadWidgetConfig();
            });

            // Handle Ticket Selection
            $(document).on('click', '.ticket-card', function (e) {
                // If it was a real click (not triggered programmatically), update URL
                if (e.originalEvent) {
                    const ticketId = $(this).data('ticket-id');
                    window.history.pushState({}, '', '/tickets/' + ticketId);
                }
                
                $('.ticket-card').removeClass('border-indigo-500 bg-slate-800/30').addClass('border-transparent');
                $(this).removeClass('border-transparent').addClass('border-indigo-500 bg-slate-800/30');

                // Hide unread badge when clicked and update data attribute
                $(this).attr('data-unread', 'false');
                $(this).find('.unread-badge').fadeOut(300, function() { $(this).remove(); });

                activeTicketId = $(this).data('ticket-id');
                loadTicketMessages(activeTicketId);
            });

            // Client-side Search & Filtering Logic
            $('#search-tickets, #filter-tickets').on('input change', function () {
                const searchTerm = $('#search-tickets').val().toLowerCase();
                const filterVal = $('#filter-tickets').val();

                $('.ticket-card').each(function () {
                    const textContent = $(this).text().toLowerCase();
                    const status = $(this).data('status');
                    const isUnread = $(this).attr('data-unread') === 'true'; // use attr to pick up dynamic updates

                    let matchesSearch = textContent.includes(searchTerm);
                    let matchesFilter = true;

                    if (filterVal === 'unread' && !isUnread) matchesFilter = false;
                    if (filterVal === 'open' && !['open', 'in_progress', 'pending'].includes(status)) matchesFilter = false;
                    if (filterVal === 'resolved' && !['resolved', 'closed'].includes(status)) matchesFilter = false;

                    if (matchesSearch && matchesFilter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Load Ticket Messages via AJAX
            function loadTicketMessages(ticketId) {
                $('#messages-container').html('<div class="text-center text-slate-500 text-xs py-8">Loading messages...</div>');

                $.get('/tickets/' + ticketId + '/messages', function (data) {
                    $('#active-ticket-subject').text(data.ticket.subject);
                    $('#active-ticket-number').text('#' + data.ticket.ticket_number);
                    $('#active-contact-name').text(data.ticket.contact ? data.ticket.contact.name : 'Customer');
                    $('#active-channel-name').text(data.ticket.channel ? data.ticket.channel.name : 'Channel');

                    let messagesHtml = '';
                    data.messages.forEach(function (msg) {
                        messagesHtml += renderMessageBubble(msg);
                    });

                    $('#messages-container').html(messagesHtml);
                    scrollToBottom();

                    // Real-time Echo WebSockets
                    if (window.Echo) {
                        window.Echo.channel('ticket.' + ticketId)
                            .listen('.message.sent', function (e) {
                                $('#messages-container').append(renderMessageBubble(e.message));
                                scrollToBottom();
                            });
                    }
                });
            }

            // Render Message Bubble
            function renderMessageBubble(msg) {
                const isInternal = msg.is_internal_note;
                const isAgent = msg.sender_type === 'agent';

                let alignmentClass = isAgent ? 'outgoing ml-auto' : 'incoming';
                let bubbleClass = isInternal ? 'internal-note w-full' : alignmentClass;

                return `
                    <div class="flex flex-col mb-3">
                        <div class="flex items-center gap-2 mb-1 text-xs text-slate-400 ${isAgent ? 'justify-end' : ''}">
                            <span class="font-semibold text-slate-300">${msg.sender_name}</span>
                            <span>•</span>
                            <span class="text-[10px]">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                        <div class="chat-bubble ${bubbleClass}">
                            ${msg.content}
                        </div>
                    </div>
                `;
            }

            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            }

            // Canned Response Shortcut Injector
            $(document).on('click', '.btn-canned', function () {
                const content = $(this).data('content');
                $('#message-input').val(content);
            });

            // Send Message Form Submit
            $('#message-form').on('submit', function (e) {
                e.preventDefault();
                const content = $('#message-input').val().trim();
                const isInternal = $('#is-internal-note').is(':checked');

                if (!content || !activeTicketId) return;

                $.ajax({
                    url: '/tickets/' + activeTicketId + '/messages',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        content: content,
                        is_internal_note: isInternal ? 1 : 0
                    },
                    success: function (response) {
                        $('#message-input').val('');
                        $('#messages-container').append(renderMessageBubble(response.message));
                        scrollToBottom();
                    }
                });
            });

            // Mark Ticket as Resolved
            $('#btn-mark-resolved').on('click', function () {
                if (!activeTicketId) return;

                $.ajax({
                    url: '/tickets/' + activeTicketId + '/resolve',
                    type: 'PATCH',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#messages-container').append(renderMessageBubble(response.message));
                        scrollToBottom();
                        
                        // Update ticket card UI
                        const card = $('.ticket-card[data-ticket-id="' + activeTicketId + '"]');
                        card.attr('data-status', 'resolved');
                        // Show visual indicator in list
                        if (card.find('.resolved-label').length === 0) {
                            card.find('.badge-priority').after('<span class="resolved-label ml-1 text-[9px] uppercase font-bold text-emerald-500">Resolved</span>');
                        }
                    }
                });
            });

            // --- WIDGET BUILDER STUDIO LOGIC ---

            // Load Existing Widget Config
            function loadWidgetConfig() {
                $.get('/api/v1/widget/config?channel_id=1', function (data) {
                    if (data.configuration) {
                        const cfg = data.configuration;
                        $('#cfg-color').val(cfg.widget_color || '#6366f1');
                        $(`input[name="position"][value="${cfg.position || 'bottom-right'}"]`).prop('checked', true);
                        $('#cfg-title').val(cfg.title || 'Customer Support');
                        $('#cfg-subtitle').val(cfg.subtitle || 'We typically reply in under 5 minutes');
                        $('#cfg-welcome').val(cfg.welcome_message || '');
                        $('#cfg-logo').val(cfg.logo_url || 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk');
                        $('#cfg-theme').val(cfg.theme || 'dark');
                        $('#cfg-icon').val(cfg.launcher_icon || 'chat');
                        $('#cfg-prechat').prop('checked', !!cfg.require_prechat);

                        updateLivePreview();
                    }
                });
            }

            // Preset Color Buttons
            $('.btn-color-preset').on('click', function () {
                const color = $(this).data('color');
                $('#cfg-color').val(color).trigger('input');
            });

            // Real-Time Live Preview Updater
            $('#cfg-color, #cfg-title, #cfg-subtitle, #cfg-welcome, #cfg-logo, #cfg-theme, #cfg-icon, input[name="position"]').on('input change', function () {
                updateLivePreview();
            });

            function updateLivePreview() {
                const color = $('#cfg-color').val();
                const pos = $('input[name="position"]:checked').val();
                const title = $('#cfg-title').val() || 'Customer Support';
                const subtitle = $('#cfg-subtitle').val() || '';
                const welcome = $('#cfg-welcome').val() || '';
                const logo = $('#cfg-logo').val() || 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk';

                // Apply color
                $('#prev-header').css('background-color', color);
                $('#prev-launcher-btn').css('background-color', color);
                $('#prev-send-btn').css('background-color', color);

                // Apply content
                $('#prev-header-title').text(title);
                $('#prev-header-subtitle').text(subtitle);
                $('#prev-welcome-bubble').text(welcome);
                $('#prev-logo-img').attr('src', logo);

                // Position preview
                const box = $('#prev-widget-box');
                const btn = $('#prev-launcher-btn');

                box.removeClass('bottom-16 top-12 right-4 left-4');
                btn.removeClass('bottom-3 top-3 right-4 left-4');

                if (pos === 'bottom-right') {
                    box.addClass('bottom-16 right-4');
                    btn.addClass('bottom-3 right-4');
                } else if (pos === 'bottom-left') {
                    box.addClass('bottom-16 left-4');
                    btn.addClass('bottom-3 left-4');
                } else if (pos === 'top-right') {
                    box.addClass('top-12 right-4');
                    btn.addClass('top-3 right-4');
                } else if (pos === 'top-left') {
                    box.addClass('top-12 left-4');
                    btn.addClass('top-3 left-4');
                }
            }

            // Save Widget Configuration to Database
            $('#save-widget-config').on('click', function () {
                const payload = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    widget_color: $('#cfg-color').val(),
                    position: $('input[name="position"]:checked').val(),
                    title: $('#cfg-title').val(),
                    subtitle: $('#cfg-subtitle').val(),
                    welcome_message: $('#cfg-welcome').val(),
                    logo_url: $('#cfg-logo').val(),
                    theme: $('#cfg-theme').val(),
                    launcher_icon: $('#cfg-icon').val(),
                    require_prechat: $('#cfg-prechat').is(':checked') ? 1 : 0
                };

                $.post('/api/v1/widget/config/1', payload, function (response) {
                    $('#config-alert').removeClass('hidden').fadeIn();
                    setTimeout(() => {
                        $('#config-alert').fadeOut();
                    }, 3000);
                }).fail(function (xhr) {
                    alert('Error saving configuration: ' + (xhr.responseJSON?.message || 'Check form inputs'));
                });
            });

            // Copy Snippet Code
            $('#copy-snippet-btn').on('click', function () {
                const code = `<script src="${window.location.origin}/widget.js" data-channel-id="1" async><\/script>`;
                navigator.clipboard.writeText(code).then(() => {
                    $('#copy-btn-text').text('Copied!');
                    setTimeout(() => {
                        $('#copy-btn-text').text('Copy Code');
                    }, 2000);
                });
            });

            // Auto-select active ticket or first ticket on load
            const activeTicketIdFromServer = {{ (isset($activeTicket) && $activeTicket && $activeTicket->id) ? $activeTicket->id : 'null' }};
            if (activeTicketIdFromServer) {
                const targetTicket = $('.ticket-card[data-ticket-id="' + activeTicketIdFromServer + '"]');
                if (targetTicket.length) {
                    targetTicket.click();
                }
            } else {
                const firstTicket = $('.ticket-card').first();
                if (firstTicket.length) {
                    // Update URL for the first selected ticket if it wasn't specified in URL
                    const firstId = firstTicket.data('ticket-id');
                    window.history.replaceState({}, '', '/tickets/' + firstId);
                    firstTicket.click();
                }
            }
        });
    </script>
@endpush
