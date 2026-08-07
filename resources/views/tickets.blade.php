@extends('layouts.app')

@section('title', 'Agent Workspace — OmniDesk Helpdesk')

@section('content')
        <!-- VIEW 1: TICKETS & CHAT WORKSPACE -->
        <div id="view-tickets-workspace" class="flex-1 flex overflow-hidden">
                    <!-- Ticket Inbox List (Column 1) -->
            <section id="inbox-column" class="w-full md:w-96 border-r border-slate-200 bg-white flex flex-col shrink-0">
                <!-- Header Search & Filter -->
                <div class="p-4 border-b border-slate-200 bg-white space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-base text-slate-900">Conversations</h2>
                        <span
                            class="text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded border border-emerald-200 font-mono flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Sync Active
                        </span>
                    </div>
                    <input type="text" id="search-tickets" placeholder="Search customer, ticket # or text..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                    <select id="filter-tickets" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                        <option value="all">All Conversations</option>
                        <option value="mine">Assigned to Me</option>
                        <option value="unread">Unread / New</option>
                        <option value="open">Open / In Progress</option>
                        <option value="resolved">Resolved / Closed</option>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <option value="trash">Trash Bin (Deleted)</option>
                        @endif
                    </select>
                </div>

                <!-- Tickets Stream -->
                <div id="tickets-list" class="flex-1 overflow-y-auto divide-y divide-slate-100 bg-white">
                    @forelse($tickets as $ticket)
                        <div class="ticket-card p-4 hover:bg-slate-50/80 cursor-pointer transition border-l-4 border-transparent"
                            data-ticket-id="{{ $ticket->id }}"
                            data-status="{{ $ticket->status }}"
                            data-assigned-id="{{ $ticket->assigned_agent_id }}"
                            data-unread="{{ $ticket->unread_messages_count > 0 ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="badge-channel badge-{{ $ticket->channel->type ?? 'web' }}">
                                    {{ $ticket->channel->name ?? 'Web' }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @if($ticket->unread_messages_count > 0)
                                        <span class="unread-badge bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                                            {{ $ticket->unread_messages_count }} New
                                        </span>
                                    @endif
                                    @if($ticket->status === 'resolved')
                                        <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Resolved</span>
                                    @elseif($ticket->status === 'closed')
                                        <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-slate-100 text-slate-600 border border-slate-200">Closed</span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-blue-50 text-blue-700 border border-blue-200">In Progress</span>
                                    @elseif($ticket->status === 'pending')
                                        <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-purple-50 text-purple-700 border border-purple-200">Pending</span>
                                    @else
                                        <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200">Open</span>
                                    @endif
                                    <span class="badge-priority priority-{{ $ticket->priority }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </div>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 line-clamp-1 mb-1">{{ $ticket->subject }}</h3>
                            <p class="ticket-snippet-text text-xs text-slate-500 line-clamp-1 mb-2">
                                {{ $ticket->latestMessage->content ?? 'No messages yet.' }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span
                                    class="font-semibold text-slate-700">{{ $ticket->contact->name ?? 'Unknown Contact' }}</span>
                                <span class="ticket-time-text text-[11px] text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</span>
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
            <section id="chat-column" class="w-full md:flex-1 hidden md:flex flex-col bg-slate-100/70">
                <!-- Active Ticket Top Bar (Multi-Row Responsive Layout) -->
                <div id="chat-header" class="p-3.5 sm:p-4 border-b border-slate-200 bg-white space-y-3 shadow-2xs">
                    <!-- Row 1: Mobile Back Button, Contact Avatar & Action Controls -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="button" id="mobile-back-to-inbox" class="md:hidden p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition cursor-pointer" title="Back to Conversations">
                                <x-icon name="arrow-left" class="text-lg" />
                            </button>
                            <div class="w-9 h-9 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center font-bold text-indigo-600 text-xs shrink-0 shadow-2xs">
                                TCK
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    <span id="active-contact-name">Customer</span>
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-1.5">
                                    <span id="active-channel-name" class="text-indigo-600 font-semibold">Web Chat</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Group -->
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-slate-500 font-medium hidden sm:inline">Assignee:</span>
                                <select id="assign-agent-select" class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs text-slate-800 focus:outline-none focus:border-indigo-500 transition shadow-2xs font-medium max-w-[130px] sm:max-w-none">
                                    <option value="">Unassigned</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="btn-mark-resolved"
                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition cursor-pointer shrink-0">
                                Mark Resolved
                            </button>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <button type="button" id="btn-delete-ticket" class="hidden px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 transition cursor-pointer shrink-0" title="Move to Trash Bin">
                                    <x-icon name="trash" class="text-xs inline" />
                                    <span>Delete</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Row 2: Ticket Subject, Ticket #, SLA status & Ratings -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 id="active-ticket-subject" class="font-extrabold text-slate-900 text-base sm:text-lg tracking-tight">Select a conversation</h2>
                            <span id="active-ticket-number" class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">#TCK-1001</span>
                            <span id="active-sla-pill" class="hidden"></span>
                            <span id="active-ticket-rating" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"></span>
                        </div>
                    </div>
                </div>

                <!-- Trashed Ticket Alert Banner -->
                <div id="trashed-banner" class="hidden bg-rose-50 border-b border-rose-200 p-3 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-rose-800 font-medium">
                    <div class="flex items-center gap-2">
                        <x-icon name="alert-triangle" class="text-base text-rose-600 shrink-0" />
                        <span>This ticket is in the <strong>Trash Bin</strong>.</span>
                    </div>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" id="btn-restore-ticket" class="px-3 py-1 bg-white hover:bg-slate-50 text-indigo-700 text-xs font-bold rounded-lg border border-slate-200 transition cursor-pointer shadow-2xs">
                                Restore Ticket
                            </button>
                            <button type="button" id="btn-force-delete-ticket" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition cursor-pointer shadow-2xs">
                                Delete Permanently
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Messages Stream Area -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="text-center text-slate-500 text-xs py-8">
                        Select a ticket from the left panel to start chatting.
                    </div>
                </div>

                <!-- Chat Reply Input & Quick Responses -->
                <div class="p-4 border-t border-slate-200 bg-white space-y-3 shadow-sm">
                    <!-- Canned Responses Quick Toolbar -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                        <span class="text-slate-500 font-medium shrink-0">Canned:</span>
                        @foreach($cannedResponses as $response)
                            <button type="button"
                                class="btn-canned shrink-0 px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-indigo-700 font-semibold rounded-md border border-slate-200 transition cursor-pointer"
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
                                placeholder="Type your response... (Press Enter to send, Shift+Enter or Ctrl/Cmd+Enter for new line)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition resize-none shadow-2xs"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-amber-700 font-semibold cursor-pointer">
                                <input type="checkbox" id="is-internal-note"
                                    class="rounded bg-white border-slate-300 text-amber-600 focus:ring-amber-500">
                                Internal Note (Only visible to agents)
                            </label>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] text-slate-400 hidden md:inline">
                                    Press <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-600 font-mono text-[9px]">Enter</kbd> to send, <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-600 font-mono text-[9px]">Shift+Enter</kbd> for new line
                                </span>
                                <button type="submit"
                                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center gap-2 cursor-pointer">
                                    <span>Send Reply</span>
                                    <x-icon name="send" class="text-sm" />
                                </button>
                            </div>
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
            let currentEchoChannel = null;

            // Global Echo Listener for Workspace Sidebar & Live Ticket Updates
            if (window.Echo) {
                window.Echo.channel('omnichannel-dashboard')
                    .listen('.message.sent', function (e) {
                        const msg = e.message;
                        if (!msg) return;

                        // 1. Update snippet text and timestamp in left sidebar ticket list
                        const card = $('.ticket-card[data-ticket-id="' + msg.ticket_id + '"]');
                        if (card.length > 0) {
                            card.find('.ticket-snippet-text').text(msg.content);
                            card.find('.ticket-time-text').text('Just now');

                            // If message belongs to an unselected ticket, mark as unread in sidebar
                            if (String(msg.ticket_id) !== String(activeTicketId)) {
                                card.attr('data-unread', 'true');
                                if (card.find('.unread-badge').length === 0) {
                                    card.find('.badge-priority').before('<span class="unread-badge bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20">New</span>');
                                }
                            }
                        }

                        // 2. Only append message to main chat window IF it matches the active ticket!
                        if (String(msg.ticket_id) === String(activeTicketId)) {
                            if (msg.id && $('#msg-bubble-' + msg.id).length === 0) {
                                $('#messages-container').append(renderMessageBubble(msg));
                                scrollToBottom();
                            }
                        }
                    });
            }

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

            // Restore Filter & Search state from URL query parameters on page load
            const currentUrl = new URL(window.location.href);
            const initFilter = currentUrl.searchParams.get('filter');
            const initSearch = currentUrl.searchParams.get('search');

            if (initFilter) $('#filter-tickets').val(initFilter);
            if (initSearch) $('#search-tickets').val(initSearch);

            // Handle Ticket Selection
            $(document).on('click', '.ticket-card', function (e) {
                const ticketId = $(this).data('ticket-id');

                if (!ticketId) return;

                // Responsive mobile view switcher: hide inbox column and show chat column
                if (window.innerWidth < 768) {
                    $('#inbox-column').addClass('hidden');
                    $('#chat-column').removeClass('hidden').addClass('flex');
                }

                // If triggered by explicit click event, push state to browser URL
                if (e && e.originalEvent) {
                    const url = new URL(window.location.href);
                    url.pathname = '/tickets/' + ticketId;
                    window.history.pushState({}, '', url.pathname + url.search);
                }
                
                $('.ticket-card').removeClass('border-indigo-600 bg-indigo-50/70 shadow-2xs').addClass('border-transparent');
                $(this).removeClass('border-transparent').addClass('border-indigo-600 bg-indigo-50/70 shadow-2xs');

                // Hide unread badge when clicked and update data attribute
                $(this).attr('data-unread', 'false');
                $(this).find('.unread-badge').fadeOut(300, function() { $(this).remove(); });

                activeTicketId = ticketId;
                loadTicketMessages(activeTicketId);
            });

            // Client-side Search & Filtering Logic with URL State Sync
            const currentUserId = {{ Auth::id() ?? 0 }};

            function applyFilteringAndSyncUrl() {
                const searchTerm = $('#search-tickets').val().toLowerCase().trim();
                const filterVal = $('#filter-tickets').val();

                // 1. Sync filter and search state into URL query params
                const url = new URL(window.location.href);
                if (filterVal && filterVal !== 'all') {
                    url.searchParams.set('filter', filterVal);
                } else {
                    url.searchParams.delete('filter');
                }

                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }

                window.history.replaceState({}, '', url.pathname + url.search);

                // 2. Filter sidebar ticket cards
                $('.ticket-card').each(function () {
                    const textContent = $(this).text().toLowerCase();
                    const status = $(this).data('status');
                    const assignedId = $(this).attr('data-assigned-id');
                    const isUnread = $(this).attr('data-unread') === 'true';

                    let matchesSearch = textContent.includes(searchTerm);
                    let matchesFilter = true;

                    if (filterVal === 'mine' && String(assignedId) !== String(currentUserId)) matchesFilter = false;
                    if (filterVal === 'unread' && !isUnread) matchesFilter = false;
                    if (filterVal === 'open' && !['open', 'in_progress', 'pending'].includes(status)) matchesFilter = false;
                    if (filterVal === 'resolved' && !['resolved', 'closed'].includes(status)) matchesFilter = false;

                    if (matchesSearch && matchesFilter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            $('#search-tickets, #filter-tickets').on('input change', function () {
                applyFilteringAndSyncUrl();
            });

            // Handle Agent Ticket Assignment Change
            $('#assign-agent-select').on('change', function () {
                if (!activeTicketId) return;
                const agentId = $(this).val();

                $.ajax({
                    url: '/tickets/' + activeTicketId + '/assign',
                    method: 'PATCH',
                    data: {
                        assigned_agent_id: agentId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        // Update attribute on sidebar card
                        $('.ticket-card[data-ticket-id="' + activeTicketId + '"]').attr('data-assigned-id', agentId || '');
                        applyFilteringAndSyncUrl();
                    }
                });
            });

            // Initial filtering execution on load
            applyFilteringAndSyncUrl();

            // Auto-select initial ticket on page load (either specified in URL /tickets/{id} or the first visible card)
            const pathParts = window.location.pathname.split('/');
            let targetTicketId = null;
            if (pathParts.length >= 3 && pathParts[1] === 'tickets' && /^\d+$/.test(pathParts[2])) {
                targetTicketId = pathParts[2];
            } else {
                const firstCard = $('.ticket-card:visible').first();
                if (firstCard.length > 0) {
                    targetTicketId = firstCard.data('ticket-id');
                }
            }

            if (targetTicketId) {
                const targetCard = $('.ticket-card[data-ticket-id="' + targetTicketId + '"]');
                if (targetCard.length > 0) {
                    targetCard.trigger('click');
                }
            }

            // Load Ticket Messages via AJAX
            function loadTicketMessages(ticketId) {
                $('#messages-container').html('<div class="text-center text-slate-500 text-xs py-8">Loading messages...</div>');

                $.get('/tickets/' + ticketId + '/messages', function (data) {
                    $('#active-ticket-subject').text(data.ticket.subject);
                    $('#active-ticket-number').text('#' + data.ticket.ticket_number);
                    $('#active-contact-name').text(data.ticket.contact ? data.ticket.contact.name : 'Customer');
                    $('#active-channel-name').text(data.ticket.channel ? data.ticket.channel.name : 'Channel');
                    $('#assign-agent-select').val(data.ticket.assigned_agent_id || '');

                    if (data.ticket.rating) {
                        const stars = '⭐'.repeat(data.ticket.rating);
                        $('#active-ticket-rating').html(stars + ' ' + data.ticket.rating + '/5').removeClass('hidden');
                    } else {
                        $('#active-ticket-rating').addClass('hidden');
                    }

                    // Dynamic status pill & action button update
                    if (data.ticket.status === 'resolved' || data.ticket.status === 'closed') {
                        $('#btn-mark-resolved')
                            .html('<span>Re-open Ticket</span>')
                            .removeClass('bg-emerald-600/20 text-emerald-400 border-emerald-500/30 hover:bg-emerald-600/30')
                            .addClass('bg-amber-600/20 text-amber-400 border-amber-500/30 hover:bg-amber-600/30');

                        $('#active-ticket-status')
                            .html(data.ticket.status === 'resolved' ? '✓ RESOLVED' : 'CLOSED')
                            .attr('class', data.ticket.status === 'resolved'
                                ? 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase'
                                : 'px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700/50 text-slate-400 border border-slate-600/30 uppercase');
                    } else {
                        $('#btn-mark-resolved')
                            .html('<span>Mark Resolved</span>')
                            .removeClass('bg-amber-600/20 text-amber-400 border-amber-500/30 hover:bg-amber-600/30')
                            .addClass('bg-emerald-600/20 text-emerald-400 border-emerald-500/30 hover:bg-emerald-600/30');

                        const statusLabel = (data.ticket.status || 'open').replace('_', ' ').toUpperCase();
                        const statusClass = data.ticket.status === 'in_progress'
                            ? 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase'
                            : (data.ticket.status === 'pending'
                                ? 'px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase'
                                : 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase');

                        $('#active-ticket-status').html(statusLabel).attr('class', statusClass);
                    }

                    let messagesHtml = '';
                    data.messages.forEach(function (msg) {
                        messagesHtml += renderMessageBubble(msg);
                    });

                    $('#messages-container').html(messagesHtml);
                    scrollToBottom();

                    // Real-time Echo WebSockets (Leave previous ticket channel to prevent cross-ticket leakage)
                    if (window.Echo) {
                        if (currentEchoChannel && currentEchoChannel !== ('ticket.' + ticketId)) {
                            window.Echo.leave(currentEchoChannel);
                        }
                        currentEchoChannel = 'ticket.' + ticketId;

                        window.Echo.channel(currentEchoChannel)
                            .listen('.message.sent', function (e) {
                                // Strictly verify that message belongs to current active ticket
                                if (e.message && String(e.message.ticket_id) === String(activeTicketId)) {
                                    if ($('#msg-bubble-' + e.message.id).length === 0) {
                                        $('#messages-container').append(renderMessageBubble(e.message));
                                        scrollToBottom();
                                    }
                                }
                            });
                    }
                });
            }

            // Listen for Real-Time Global Omnichannel Events (New Ticket Arrival & Message Live Feed)
            if (window.Echo) {
                window.Echo.channel('omnichannel-dashboard')
                    .listen('.ticket.created', function (e) {
                        if (!e.ticket) return;

                        // Check if ticket card already exists in left list
                        if ($('.ticket-card[data-ticket-id="' + e.ticket.id + '"]').length === 0) {
                            const newCardHtml = renderTicketCardHtml(e.ticket);
                            $('#tickets-list').prepend(newCardHtml);
                            
                            // Re-apply active filters
                            applyFilteringAndSyncUrl();
                        }
                    })
                    .listen('.message.sent', function (e) {
                        if (!e.message) return;

                        // Update snippet & time on left list card
                        const card = $('.ticket-card[data-ticket-id="' + e.message.ticket_id + '"]');
                        if (card.length > 0) {
                            card.find('.ticket-snippet-text').text(e.message.content);
                            card.find('.ticket-time-text').text('Just now');
                            // Move updated ticket to top of list
                            $('#tickets-list').prepend(card);
                        }
                    });
            }

            function renderTicketCardHtml(t) {
                const channelName = t.channel ? t.channel.name : 'Web';
                const channelType = t.channel ? t.channel.type : 'web';
                const contactName = t.contact ? t.contact.name : 'Guest';
                const snippet = t.latest_message ? t.latest_message.content : 'New conversation started.';
                const priority = t.priority || 'medium';

                return `
                    <div class="ticket-card p-4 hover:bg-slate-50 cursor-pointer transition border-l-4 border-transparent bg-white border-b border-slate-100 shadow-2xs"
                        data-ticket-id="${t.id}"
                        data-status="${t.status}"
                        data-assigned-id="${t.assigned_agent_id || ''}"
                        data-unread="true">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="badge-channel badge-${channelType}">
                                ${channelName}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="unread-badge bg-rose-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-xs">
                                    New
                                </span>
                                <span class="ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                    Open
                                </span>
                                <span class="badge-priority priority-${priority}">
                                    ${priority}
                                </span>
                            </div>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 line-clamp-1 mb-1">${t.subject}</h3>
                        <p class="ticket-snippet-text text-xs text-slate-500 line-clamp-1 mb-2">
                            ${snippet}
                        </p>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="font-medium text-slate-700">${contactName}</span>
                            <span class="ticket-time-text">Just now</span>
                        </div>
                    </div>
                `;
            }

            // Render Message Bubble
            function renderMessageBubble(msg) {
                const isInternal = msg.is_internal_note;
                const isAgent = msg.sender_type === 'agent';

                let alignmentClass = isAgent ? 'outgoing ml-auto' : 'incoming';
                let bubbleClass = isInternal ? 'internal-note w-full' : alignmentClass;

                return `
                    <div id="msg-bubble-${msg.id || Date.now()}" class="flex flex-col mb-3">
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

            // Handle Keyboard Shortcuts for Textarea: Enter to Send, Shift/Ctrl/Cmd+Enter for Newline
            $('#message-input').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    if (e.shiftKey || e.ctrlKey || e.metaKey) {
                        return; // Allow native multiline newline insertion
                    }
                    e.preventDefault();
                    $('#message-form').submit();
                }
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

            // Toggle Ticket Status (Resolve / Re-open)
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
                        
                        const newStatus = response.ticket.status;
                        const card = $('.ticket-card[data-ticket-id="' + activeTicketId + '"]');
                        card.attr('data-status', newStatus);

                        const pill = card.find('.ticket-status-pill');
                        if (newStatus === 'resolved') {
                            pill.html('✓ Resolved').attr('class', 'ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200');
                        } else {
                            pill.html('Open').attr('class', 'ticket-status-pill px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200');
                        }

                        // Refresh active ticket header & button state
                        loadTicketMessages(activeTicketId);
                    }
                });
            });

            // Dynamic Canned Response Variable Replacement Engine
            function parseCannedResponseVariables(rawContent) {
                if (!rawContent) return '';

                const agentName = @json(auth()->user()->name ?? 'Agent');
                const agentEmail = @json(auth()->user()->email ?? '');
                const customerName = $('#active-contact-name').text().trim() || 'Customer';
                const ticketNumber = $('#active-ticket-number').text().trim() || '';
                const ticketSubject = $('#active-ticket-subject').text().trim() || '';
                const companyName = 'OmniHelp Desk';

                let parsed = rawContent;
                
                // Agent variables: {agent.name}, {agent_name}, {agent.email}
                parsed = parsed.replace(/\{agent\.name\}/gi, agentName)
                               .replace(/\{agent_name\}/gi, agentName)
                               .replace(/\{agent\.email\}/gi, agentEmail);

                // Customer / Contact variables: {customer.name}, {contact.name}, {customer_name}
                parsed = parsed.replace(/\{customer\.name\}/gi, customerName)
                               .replace(/\{contact\.name\}/gi, customerName)
                               .replace(/\{customer_name\}/gi, customerName);

                // Ticket variables: {ticket.number}, {ticket.id}, {ticket_number}, {ticket.subject}
                parsed = parsed.replace(/\{ticket\.number\}/gi, ticketNumber)
                               .replace(/\{ticket\.id\}/gi, ticketNumber)
                               .replace(/\{ticket_number\}/gi, ticketNumber)
                               .replace(/\{ticket\.subject\}/gi, ticketSubject);

                // Company variables: {company.name}, {company_name}
                parsed = parsed.replace(/\{company\.name\}/gi, companyName)
                               .replace(/\{company_name\}/gi, companyName);

                return parsed;
            }

            // Canned Response Shortcut Injector with Dynamic Variable Resolution
            $(document).on('click', '.btn-canned', function () {
                const rawContent = $(this).data('content');
                const parsedContent = parseCannedResponseVariables(rawContent);
                
                const currentVal = $('#message-input').val();
                if (currentVal && currentVal.trim().length > 0) {
                    $('#message-input').val(currentVal + '\n' + parsedContent);
                } else {
                    $('#message-input').val(parsedContent);
                }
                $('#message-input').focus();
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

            // Auto-select active ticket ONLY if explicitly specified in URL path (e.g. /tickets/15)
            const activeTicketIdFromServer = {{ (isset($activeTicket) && $activeTicket && $activeTicket->id) ? $activeTicket->id : 'null' }};
            if (activeTicketIdFromServer) {
                const targetTicket = $('.ticket-card[data-ticket-id="' + activeTicketIdFromServer + '"]');
                if (targetTicket.length) {
                    targetTicket.click();
                }
            }
        });
    </script>
@endpush
