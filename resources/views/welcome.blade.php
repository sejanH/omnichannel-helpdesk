<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OmniDesk — Omnichannel Support Helpdesk & Live Chat</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                    <x-icon name="messages" class="text-2xl text-white" />
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-white flex items-center gap-1.5">
                        OmniDesk
                        <span class="text-[10px] uppercase font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 px-1.5 py-0.5 rounded">v2.0</span>
                    </span>
                    <span class="block text-[11px] text-slate-400 font-medium leading-none">Omnichannel Support Engine</span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#features" class="hover:text-indigo-400 transition">Features</a>
                <a href="#channels" class="hover:text-indigo-400 transition">Channels</a>
                <a href="#pricing" class="hover:text-indigo-400 transition">Pricing</a>
                <a href="#faq" class="hover:text-indigo-400 transition">FAQ</a>
                <a href="{{ url('/docs/README.md') }}" target="_blank" class="hover:text-indigo-400 transition flex items-center gap-1">
                    Docs
                    <x-icon name="external-link" class="text-xs text-slate-500" />
                </a>
            </nav>

            <!-- Action CTAs & Auth Options -->
            <div class="flex items-center gap-3">
                <a href="{{ route('demo') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:text-white transition">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    Live Demo
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Go to Workspace</span>
                        <x-icon name="arrow-right" class="text-xs" />
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-200 hover:text-white transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        Start Free Trial
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-36 pb-24 overflow-hidden">
        <!-- Background Gradient Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-cyan-500/15 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Announcement Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-medium mb-8">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                <span>Next-Gen Omnichannel Support & Live Chat Engine</span>
                <x-icon name="chevron-right" class="text-xs text-indigo-400" />
            </div>

            <!-- Hero Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white max-w-4xl mx-auto leading-[1.15]">
                One Unified Inbox for <br>
                <span class="bg-gradient-to-r from-indigo-400 via-cyan-400 to-emerald-400 bg-clip-text text-transparent">All Customer Conversations.</span>
            </h1>

            <!-- Hero Subtitle -->
            <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto font-normal leading-relaxed">
                Connect Web Live Chat, WhatsApp, Email, Facebook Messenger & Telegram into a single real-time dashboard. Reply faster, collaborate internally, and automate SLAs.
            </p>

            <!-- Hero Buttons -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <span>Start 14-Day Free Trial</span>
                    <x-icon name="arrow-right" class="text-xs" />
                </a>
                <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-200 bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-slate-700 transition flex items-center gap-2">
                    <x-icon name="device-desktop" class="text-base text-indigo-400" />
                    <span>Launch Agent Dashboard</span>
                </a>
            </div>

            <!-- Sub CTA Trust Text -->
            <div class="mt-6 text-xs text-slate-500 flex items-center justify-center gap-6">
                <span class="flex items-center gap-1">
                    <x-icon name="check" class="text-sm text-emerald-400" />
                    No credit card required
                </span>
                <span class="flex items-center gap-1">
                    <x-icon name="check" class="text-sm text-emerald-400" />
                    Setup in 2 minutes
                </span>
            </div>

            <!-- Workspace Preview Mockup -->
            <div class="mt-16 relative max-w-5xl mx-auto">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/90 shadow-2xl overflow-hidden glass-panel p-2">
                    <div class="bg-slate-950 rounded-xl border border-slate-800/80 p-4 text-left">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="ml-2 text-xs font-mono text-slate-400">OmniDesk Agent Workspace — Active Ticket #TCK-1002</span>
                            </div>
                            <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded font-mono flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Reverb WebSocket Active
                            </span>
                        </div>
                        <!-- Mockup Inbox Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-slate-900 p-3 rounded-lg border border-slate-800 space-y-2">
                                <div class="text-xs font-bold text-slate-300">Incoming Stream</div>
                                <div class="p-2.5 rounded-lg bg-indigo-600/20 border border-indigo-500/30 text-xs space-y-1">
                                    <div class="flex justify-between text-indigo-300 font-bold">
                                        <span>WhatsApp Inquiry</span>
                                        <span>Just now</span>
                                    </div>
                                    <p class="text-slate-300 truncate">When will order #ORD-4491 ship?</p>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-950/80 border border-slate-800 text-xs space-y-1 opacity-70">
                                    <div class="flex justify-between text-cyan-300 font-bold">
                                        <span>Web Live Chat</span>
                                        <span>5m ago</span>
                                    </div>
                                    <p class="text-slate-400 truncate">Need help setting up custom domain</p>
                                </div>
                            </div>
                            <div class="col-span-2 bg-slate-900 p-3 rounded-lg border border-slate-800 flex flex-col justify-between h-48">
                                <div class="space-y-2 text-xs">
                                    <div class="p-2 bg-slate-950 rounded border border-slate-800 text-slate-300 max-w-[85%]">
                                        Hello, can I check when order #ORD-4491 will be shipped?
                                    </div>
                                    <div class="p-2 bg-indigo-600 text-white rounded ml-auto max-w-[85%]">
                                        Hi Pam, your order has been dispatched today via DHL tracking #99281726!
                                    </div>
                                    <div class="p-2 bg-amber-500/10 border border-amber-500/30 text-amber-300 rounded text-[11px] italic">
                                        🔒 Internal Note: Customer tracking updated in CRM.
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <input type="text" disabled value="Type your response here..." class="flex-1 bg-slate-950 border border-slate-800 text-xs rounded px-3 py-1.5 text-slate-500">
                                    <button class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded font-bold">Send Reply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Supported Channels Banner -->
    <section id="channels" class="py-12 bg-slate-900/60 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-8">
                Seamlessly Connect Your Favorite Channels
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 items-center">
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-indigo-400 font-semibold text-sm hover:border-indigo-500/50 transition">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span> Web Live Chat
                </div>
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-emerald-400 font-semibold text-sm hover:border-emerald-500/50 transition">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> WhatsApp Cloud
                </div>
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-amber-400 font-semibold text-sm hover:border-amber-500/50 transition">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span> Support Email
                </div>
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-blue-400 font-semibold text-sm hover:border-blue-500/50 transition">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span> Facebook Messenger
                </div>
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-cyan-400 font-semibold text-sm hover:border-cyan-500/50 transition col-span-2 sm:col-span-1">
                    <span class="w-3 h-3 rounded-full bg-cyan-500"></span> Telegram Bot
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-3">Everything You Need</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-white">Built for Speed, Collaboration, & Scale</p>
                <p class="mt-4 text-slate-400 text-base">Empower your customer success agents with powerful tools designed to resolve tickets 3x faster.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-indigo-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="message-dots" class="text-2xl text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Embeddable Live Chat Widget</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Customize colors, logo, theme, launcher position, and welcome greetings. Embed on any website with a single `<script>` tag.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="lock" class="text-2xl text-cyan-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Internal Team Collaboration</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Post private internal notes inside ticket streams. Discuss customer cases with teammates without exposing notes to customers.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="bolt" class="text-2xl text-emerald-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Canned Response Shortcuts</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Create pre-written templates for FAQs, greetings, and policies. Insert them instantly using simple `/shortcuts` to save time.</p>
                </div>

                <!-- Feature 4 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-amber-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="clock" class="text-2xl text-amber-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">SLA & Priority Escalations</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Track ticket urgency with color-coded priority badges (Urgent, High, Medium, Low) and automated response timers.</p>
                </div>

                <!-- Feature 5 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-purple-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="bell" class="text-2xl text-purple-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Real-Time WebSocket Engine</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Powered by Laravel Echo & Broadcasting. New messages and ticket updates stream instantly without page refreshes.</p>
                </div>

                <!-- Feature 6 -->
                <div class="glass-panel p-8 rounded-2xl border border-slate-800 hover:border-rose-500/40 transition group">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 mb-6 group-hover:scale-110 transition">
                        <x-icon name="code" class="text-2xl text-rose-400" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Developer-Friendly REST APIs</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Complete API endpoints for public widget integration, webhooks, contact synchronization, and ticket management.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Details Section -->
    <section id="pricing" class="py-24 bg-slate-900/40 border-t border-slate-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-3">Simple, Transparent Pricing</h2>
                <p class="text-3xl sm:text-5xl font-extrabold text-white">Choose the Plan That Fits Your Team</p>
                <p class="mt-4 text-slate-400 text-base">Scale your customer support with flexible monthly or annual billing. Cancel anytime.</p>

                <!-- Billing Toggle Button -->
                <div class="mt-8 inline-flex items-center gap-3 p-1.5 rounded-full bg-slate-900 border border-slate-800">
                    <button type="button" id="billing-monthly-btn" onclick="toggleBilling('monthly')" class="px-5 py-2 rounded-full text-xs font-bold transition text-white bg-indigo-600 shadow-md">
                        Monthly Billing
                    </button>
                    <button type="button" id="billing-annually-btn" onclick="toggleBilling('annually')" class="px-5 py-2 rounded-full text-xs font-bold transition text-slate-400 hover:text-white flex items-center gap-1.5">
                        Annual Billing
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] px-1.5 py-0.5 rounded-full uppercase">Save 20%</span>
                    </button>
                </div>
            </div>

            <!-- Pricing Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                
                <!-- Plan 1: Starter -->
                <div class="glass-panel rounded-3xl border border-slate-800 p-8 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Starter</div>
                        <h3 class="text-xl font-extrabold text-white mb-4">Small Teams</h3>
                        <div class="mb-6">
                            <span class="text-4xl sm:text-5xl font-black text-white price-val" data-monthly="$19" data-annually="$15">$19</span>
                            <span class="text-slate-400 text-sm">/ month</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-6 pb-6 border-b border-slate-800">Essential live chat widget and single web channel support for growing startups.</p>
                        
                        <ul class="space-y-3.5 text-xs text-slate-300 mb-8">
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>2 Agent Seats Included</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Web Live Chat Channel</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Up to 1,000 Tickets / month</span>
                            </li>
                            <li class="flex items-center gap-2.5 text-slate-500">
                                <x-icon name="x" class="text-sm text-slate-600 shrink-0" />
                                <span class="line-through">WhatsApp & Social Channels</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                        Get Started with Starter
                    </a>
                </div>

                <!-- Plan 2: Pro (Featured) -->
                <div class="glass-panel rounded-3xl border-2 border-indigo-500 p-8 flex flex-col justify-between relative shadow-2xl shadow-indigo-600/20 bg-slate-900/80">
                    <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-cyan-400 text-white text-[11px] font-extrabold uppercase px-4 py-1 rounded-full shadow-lg">
                        Most Popular
                    </span>
                    <div>
                        <div class="text-sm font-bold text-indigo-400 uppercase tracking-wider mb-2">Pro Business</div>
                        <h3 class="text-xl font-extrabold text-white mb-4">Growing Companies</h3>
                        <div class="mb-6">
                            <span class="text-4xl sm:text-5xl font-black text-white price-val" data-monthly="$49" data-annually="$39">$49</span>
                            <span class="text-slate-400 text-sm">/ month</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-6 pb-6 border-b border-slate-800">Complete omnichannel suite for teams handling high support volume across multi-channels.</p>
                        
                        <ul class="space-y-3.5 text-xs text-slate-300 mb-8">
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span class="font-semibold text-white">10 Agent Seats Included</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>All 5 Channels (WhatsApp, Web, Email, FB, Telegram)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Unlimited Tickets & Messages</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Canned Responses & Internal Notes</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>SLA Policy Automation & Priority Matrix</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-extrabold text-white shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                        Start 14-Day Free Trial
                    </a>
                </div>

                <!-- Plan 3: Enterprise -->
                <div class="glass-panel rounded-3xl border border-slate-800 p-8 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="text-sm font-bold text-cyan-400 uppercase tracking-wider mb-2">Enterprise</div>
                        <h3 class="text-xl font-extrabold text-white mb-4">Large Scale Operations</h3>
                        <div class="mb-6">
                            <span class="text-4xl sm:text-5xl font-black text-white price-val" data-monthly="$149" data-annually="$119">$149</span>
                            <span class="text-slate-400 text-sm">/ month</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-6 pb-6 border-b border-slate-800">Advanced compliance, custom webhooks, dedicated account manager, & guaranteed uptime SLA.</p>
                        
                        <ul class="space-y-3.5 text-xs text-slate-300 mb-8">
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span class="font-semibold text-white">Unlimited Agent Seats</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Custom Webhook Integrations</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>99.9% Financial Uptime Guarantee</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <x-icon name="check" class="text-sm text-emerald-400 shrink-0" />
                                <span>Dedicated Account Manager & 24/7 Phone Support</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                        Contact Enterprise Sales
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-3">Frequently Asked Questions</h2>
                <p class="text-3xl font-extrabold text-white">Got Questions? We Have Answers.</p>
            </div>

            <div class="space-y-4">
                <details class="group bg-slate-900 border border-slate-800 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer">
                    <summary class="flex items-center justify-between font-bold text-base text-slate-200">
                        <span>How easy is it to embed the live chat widget on my website?</span>
                        <span class="ml-4 shrink-0 transition group-open:-rotate-180">
                            <x-icon name="chevron-down" class="text-xl text-indigo-400" />
                        </span>
                    </summary>
                    <p class="mt-4 text-xs text-slate-400 leading-relaxed">
                        Embedding takes under 2 minutes! Copy the generated single-line `<script>` tag from your Admin Dashboard and paste it right before the closing `</body>` tag on WordPress, Shopify, Next.js, Laravel, or any custom website.
                    </p>
                </details>

                <details class="group bg-slate-900 border border-slate-800 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer">
                    <summary class="flex items-center justify-between font-bold text-base text-slate-200">
                        <span>Can customers see internal team notes?</span>
                        <span class="ml-4 shrink-0 transition group-open:-rotate-180">
                            <x-icon name="chevron-down" class="text-xl text-indigo-400" />
                        </span>
                    </summary>
                    <p class="mt-4 text-xs text-slate-400 leading-relaxed">
                        No! Internal notes (`is_internal_note: true`) are strictly filtered out by the public API and live chat widget. They are visible only to logged-in support team members in your Agent Workspace.
                    </p>
                </details>

                <details class="group bg-slate-900 border border-slate-800 rounded-2xl p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer">
                    <summary class="flex items-center justify-between font-bold text-base text-slate-200">
                        <span>Can I test the live agent dashboard without creating a database?</span>
                        <span class="ml-4 shrink-0 transition group-open:-rotate-180">
                            <x-icon name="chevron-down" class="text-xl text-indigo-400" />
                        </span>
                    </summary>
                    <p class="mt-4 text-xs text-slate-400 leading-relaxed">
                        Yes! Click the <strong>"Demo Dashboard"</strong> button or launch the Auth Modal to instantly log in with pre-seeded sample data (tickets, canned responses, agents, and contacts).
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800/80 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white text-sm">
                    OD
                </div>
                <span class="font-extrabold text-slate-200">OmniDesk Engine</span>
            </div>
            <p class="text-xs text-slate-500">© 2026 OmniDesk. Open-Source Omnichannel Helpdesk & Live Chat System.</p>
            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-white transition">Agent Workspace</a>
                <a href="{{ route('demo') }}" class="hover:text-white transition">Live Demo</a>
                <a href="{{ url('/docs/README.md') }}" target="_blank" class="hover:text-white transition">Documentation</a>
            </div>
        </div>
    </footer>

    <!-- Interactive Auth Options Modal -->
    <div id="auth-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-8 shadow-2xl relative">
            <button type="button" onclick="closeAuthModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg">✕</button>
            
            <!-- Auth Modal Tabs -->
            <div class="flex items-center justify-center gap-4 border-b border-slate-800 pb-4 mb-6">
                <button type="button" id="tab-login" onclick="switchAuthTab('login')" class="text-sm font-bold text-indigo-400 border-b-2 border-indigo-500 pb-1">Sign In</button>
                <button type="button" id="tab-register" onclick="switchAuthTab('register')" class="text-sm font-bold text-slate-400 hover:text-slate-200 pb-1">Create Account</button>
            </div>

            <!-- Login Form & Fast Demo Login -->
            <div id="auth-form-login" class="space-y-4">
                <!-- One Click Demo Login Button -->
                <div class="p-3 bg-indigo-600/15 border border-indigo-500/30 rounded-xl text-center space-y-2">
                    <span class="text-xs font-semibold text-indigo-300">Fast Demonstration Access</span>
                    <a href="{{ route('dashboard') }}" class="block w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                        ⚡ Enter Agent Workspace (Pre-Seeded Demo)
                    </a>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-800"></div>
                    <span class="flex-shrink mx-3 text-[10px] text-slate-500 uppercase font-mono">Or Sign In with Email</span>
                    <div class="flex-grow border-t border-slate-800"></div>
                </div>

                <form onsubmit="event.preventDefault(); window.location.href='{{ route('dashboard') }}';" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                        <input type="email" required value="admin@helpdesk.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                        <input type="password" required value="password" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition">
                        Sign In to Helpdesk
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="auth-form-register" class="space-y-3 hidden">
                <form onsubmit="event.preventDefault(); window.location.href='{{ route('dashboard') }}';" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                        <input type="text" required placeholder="Alex Rivera" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Company / Organization</label>
                        <input type="text" required placeholder="Acme Support Inc." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Work Email</label>
                        <input type="email" required placeholder="alex@acme.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                        <input type="password" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition">
                        Create Free Trial Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Client Floating Live Chat Widget Test Integration -->
    <script src="{{ url('/widget.js') }}" data-channel-id="1" async></script>

    <!-- Page Interaction Scripts -->
    <script>
        function toggleBilling(mode) {
            const monthlyBtn = document.getElementById('billing-monthly-btn');
            const annuallyBtn = document.getElementById('billing-annually-btn');
            const prices = document.querySelectorAll('.price-val');

            if (mode === 'annually') {
                annuallyBtn.classList.add('bg-indigo-600', 'text-white');
                annuallyBtn.classList.remove('text-slate-400');
                monthlyBtn.classList.remove('bg-indigo-600', 'text-white');
                monthlyBtn.classList.add('text-slate-400');

                prices.forEach(el => {
                    el.innerText = el.getAttribute('data-annually');
                });
            } else {
                monthlyBtn.classList.add('bg-indigo-600', 'text-white');
                monthlyBtn.classList.remove('text-slate-400');
                annuallyBtn.classList.remove('bg-indigo-600', 'text-white');
                annuallyBtn.classList.add('text-slate-400');

                prices.forEach(el => {
                    el.innerText = el.getAttribute('data-monthly');
                });
            }
        }

        function openAuthModal(tab = 'login') {
            switchAuthTab(tab);
            const modal = document.getElementById('auth-modal');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
        }

        function closeAuthModal() {
            const modal = document.getElementById('auth-modal');
            modal.classList.add('opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function switchAuthTab(tab) {
            const loginForm = document.getElementById('auth-form-login');
            const registerForm = document.getElementById('auth-form-register');
            const tabLogin = document.getElementById('tab-login');
            const tabRegister = document.getElementById('tab-register');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabLogin.className = 'text-sm font-bold text-indigo-400 border-b-2 border-indigo-500 pb-1';
                tabRegister.className = 'text-sm font-bold text-slate-400 hover:text-slate-200 pb-1';
            } else {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                tabRegister.className = 'text-sm font-bold text-indigo-400 border-b-2 border-indigo-500 pb-1';
                tabLogin.className = 'text-sm font-bold text-slate-400 hover:text-slate-200 pb-1';
            }
        }
    </script>
</body>
</html>