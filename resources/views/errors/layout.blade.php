<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Error') — OmniDesk Helpdesk</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden relative selection:bg-indigo-500 selection:text-white">

    <!-- Glowing Background Ambient Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="error-bg-glow w-[500px] h-[500px] -top-32 -left-32 @yield('glow_color', 'bg-indigo-600/30')"></div>
        <div class="error-bg-glow w-[600px] h-[600px] -bottom-40 -right-40 @yield('glow_color_secondary', 'bg-cyan-600/20')"></div>
        <div class="absolute inset-0 error-grid-overlay opacity-40"></div>
    </div>

    <!-- Header Navigation -->
    <header class="relative z-20 border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 p-0.5 shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-300">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i class="ti ti-headset text-indigo-400 text-xl group-hover:text-cyan-300 transition-colors"></i>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-lg tracking-tight text-white group-hover:text-indigo-200 transition-colors">Omni<span class="text-indigo-400">Desk</span></span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 rounded-full border border-slate-700">Helpdesk</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Omnichannel Support Workspace</p>
                </div>
            </a>

            <!-- System Status Badge & Actions -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/80 border border-slate-800 text-xs text-slate-300">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full @yield('status_ping', 'bg-amber-400') opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 @yield('status_dot', 'bg-amber-500')"></span>
                    </span>
                    <span class="font-medium">@yield('system_status', 'Workspace Alert')</span>
                </div>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700/80 hover:border-slate-600 transition-all shadow-sm">
                    <i class="ti ti-layout-dashboard text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Error Showcase / Preview Bar (If in Preview mode) -->
    @if(request()->is('errors*') || request()->has('preview'))
    <div class="relative z-20 bg-indigo-950/60 border-b border-indigo-500/20 py-2.5 px-4 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-indigo-200 font-medium">
                <i class="ti ti-eye text-indigo-400 text-base animate-pulse"></i>
                <span><strong>Error Theme Preview Gallery:</strong> Switch status codes below to test layout designs</span>
            </div>
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-slate-400 text-[11px] font-semibold uppercase mr-1">4xx:</span>
                @foreach(['400', '401', '403', '404', '419', '429', '4xx'] as $code)
                    <a href="{{ url('/errors/'.$code) }}" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition-all {{ request()->segment(2) == $code ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'bg-slate-900/80 text-slate-300 border border-slate-800 hover:border-amber-500/50 hover:text-amber-300' }}">
                        {{ $code }}
                    </a>
                @endforeach

                <span class="text-slate-400 text-[11px] font-semibold uppercase ml-2 mr-1">5xx:</span>
                @foreach(['500', '502', '503', '504', '5xx'] as $code)
                    <a href="{{ url('/errors/'.$code) }}" class="px-2.5 py-1 rounded-md text-[11px] font-bold transition-all {{ request()->segment(2) == $code ? 'bg-rose-500 text-white shadow-md shadow-rose-500/30' : 'bg-slate-900/80 text-slate-300 border border-slate-800 hover:border-rose-500/50 hover:text-rose-300' }}">
                        {{ $code }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-6 md:p-12">
        <div class="max-w-4xl w-full">
            <div class="glass-panel rounded-3xl border border-slate-800/90 bg-slate-900/70 p-8 md:p-12 shadow-2xl shadow-slate-950/80 relative overflow-hidden backdrop-blur-xl">
                
                <!-- Background Accent Glow Card -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full opacity-10 blur-3xl @yield('glow_color', 'bg-indigo-500')"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-center">
                    
                    <!-- Visual Illustration Column -->
                    <div class="lg:col-span-5 flex flex-col items-center justify-center text-center">
                        <div class="relative">
                            <!-- Code Badge Backdrop -->
                            <div class="absolute -inset-4 rounded-3xl bg-gradient-to-tr @yield('badge_gradient', 'from-indigo-500/20 via-purple-500/10 to-transparent') blur-xl opacity-70"></div>
                            
                            <!-- Custom Illustration / Graphic Slot -->
                            <div class="relative z-10 w-48 h-48 md:w-56 md:h-56 rounded-3xl bg-slate-950/80 border border-slate-800/90 flex flex-col items-center justify-center p-6 shadow-inner group">
                                @yield('illustration')

                                <!-- Status Badge Pill -->
                                <div class="mt-4 px-3 py-1 rounded-full text-xs font-black tracking-widest uppercase border shadow-lg @yield('code_badge_class', 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30')">
                                    HTTP @yield('code', '500')
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details & Guidance Column -->
                    <div class="lg:col-span-7 flex flex-col">
                        <!-- Category Tag -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg w-fit text-xs font-bold tracking-wide uppercase mb-3 border @yield('tag_class', 'bg-slate-800 text-slate-300 border-slate-700')">
                            <i class="ti ti-@yield('icon', 'alert-circle') text-sm"></i>
                            <span>@yield('subtitle', 'Omnichannel Dispatch Notice')</span>
                        </div>

                        <!-- Big Headline -->
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight mb-3">
                            @yield('message', 'An unexpected event occurred.')
                        </h1>

                        <!-- Detailed Description -->
                        <p class="text-slate-400 text-sm sm:text-base leading-relaxed mb-6">
                            @yield('description', 'The helpdesk system encountered a problem processing your request. Please check your workspace parameters or contact support.')
                        </p>

                        <!-- Suggested Solutions List -->
                        @hasSection('suggestions')
                            <div class="mb-6 p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 text-xs sm:text-sm text-slate-300">
                                <div class="font-semibold text-slate-200 mb-2 flex items-center gap-2">
                                    <i class="ti ti-bulb text-amber-400 text-base"></i>
                                    <span>Recommended Troubleshooting Steps:</span>
                                </div>
                                <ul class="space-y-1.5 list-disc list-inside text-slate-400">
                                    @yield('suggestions')
                                </ul>
                            </div>
                        @endif

                        <!-- Action Controls -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <!-- Primary Button -->
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all duration-200 active:scale-95">
                                <i class="ti ti-arrow-left text-lg"></i>
                                <span>Return to Workspace</span>
                            </a>

                            <!-- Secondary Button -->
                            <button onclick="window.location.reload()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700/90 hover:border-slate-600 transition-all active:scale-95">
                                <i class="ti ti-refresh text-lg text-slate-400"></i>
                                <span>Retry Connection</span>
                            </button>

                            <!-- Diagnostic Info Toggle Button -->
                            <button onclick="toggleDiagnostics()" class="inline-flex items-center gap-1.5 px-3 py-3 rounded-xl text-xs font-semibold bg-slate-900/90 hover:bg-slate-800 text-slate-400 hover:text-slate-200 border border-slate-800 transition-colors ml-auto">
                                <i class="ti ti-terminal-2 text-base"></i>
                                <span class="hidden sm:inline">Diagnostics</span>
                                <i id="diag-chevron" class="ti ti-chevron-down text-xs transition-transform"></i>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Expandable Diagnostic Terminal Card -->
                <div id="diagnostic-panel" class="hidden mt-8 pt-6 border-t border-slate-800/80 transition-all duration-300">
                    <div class="rounded-2xl diagnostic-terminal p-4 text-xs">
                        <div class="flex items-center justify-between mb-3 border-b border-slate-800 pb-2">
                            <div class="flex items-center gap-2 font-mono text-indigo-400 font-semibold">
                                <i class="ti ti-terminal text-sm"></i>
                                <span>SYSTEM DIAGNOSTIC LOG</span>
                            </div>
                            <button onclick="copyDiagnostics()" id="copy-btn" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-[11px] font-medium flex items-center gap-1.5 transition-colors">
                                <i class="ti ti-copy text-xs"></i>
                                <span id="copy-text">Copy Logs</span>
                            </button>
                        </div>
                        <div class="space-y-1 font-mono text-slate-400 overflow-x-auto text-[11px]">
                            <div><span class="text-slate-500">Status Code:</span> <span class="text-amber-400 font-bold">@yield('code', '500')</span></div>
                            <div><span class="text-slate-500">Request ID:</span> <span class="text-cyan-400">req_omni_{{ substr(md5(request()->fullUrl() . time()), 0, 10) }}</span></div>
                            <div><span class="text-slate-500">Timestamp:</span> <span class="text-slate-300">{{ now()->toIso8601String() }}</span></div>
                            <div><span class="text-slate-500">Method & Path:</span> <span class="text-indigo-300">{{ request()->method() }} {{ request()->path() }}</span></div>
                            <div><span class="text-slate-500">User Agent:</span> <span class="text-slate-400 truncate inline-block max-w-xl align-bottom">{{ request()->userAgent() }}</span></div>
                            @if(isset($exception) && $exception->getMessage())
                            <div class="mt-2 pt-2 border-t border-slate-800/80 text-rose-300">
                                <span class="text-rose-500 font-bold">Exception Details:</span> {{ $exception->getMessage() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Bottom Omnichannel Channel Health Footer -->
    <footer class="relative z-20 border-t border-slate-800/80 bg-slate-950/80 backdrop-blur-md py-4 px-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <!-- Left: Copyright -->
            <div class="flex items-center gap-2">
                <i class="ti ti-shield-check text-indigo-400 text-base"></i>
                <span>&copy; {{ date('Y') }} OmniDesk Omnichannel Platform. All Systems Monitored.</span>
            </div>

            <!-- Right: Live Channel Connectivity Matrix -->
            <div class="flex flex-wrap items-center gap-4 text-[11px]">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-300">Live Chat</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-300">WhatsApp API</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-300">Email Pipeline</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-300">Telegram Bot</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        function toggleDiagnostics() {
            const panel = document.getElementById('diagnostic-panel');
            const chevron = document.getElementById('diag-chevron');
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                panel.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        function copyDiagnostics() {
            const code = "@yield('code', '500')";
            const path = "{{ request()->path() }}";
            const text = `OmniDesk Error Log\nCode: ${code}\nPath: ${path}\nTime: ${new Date().toISOString()}`;
            
            navigator.clipboard.writeText(text).then(() => {
                const btnText = document.getElementById('copy-text');
                const orig = btnText.innerText;
                btnText.innerText = "Copied!";
                btnText.classList.add('text-emerald-400');
                setTimeout(() => {
                    btnText.innerText = orig;
                    btnText.classList.remove('text-emerald-400');
                }, 2000);
            });
        }
    </script>
</body>
</html>
