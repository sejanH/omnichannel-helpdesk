<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Website - Live Chat Widget Embed Demo</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex flex-col justify-between p-8">

    <div class="max-w-4xl mx-auto w-full space-y-8">
        <!-- Client Header -->
        <header class="flex items-center justify-between border-b border-slate-800 pb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 flex items-center justify-center font-bold text-white shadow-lg">
                    AC
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-100">Acme Store Client Website</h1>
                    <p class="text-xs text-slate-400">Demonstrating embedded OmniDesk live chat widget</p>
                </div>
            </div>
            <a href="/" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg transition">
                ← Back to Agent Dashboard
            </a>
        </header>

        <!-- Product Preview Mock -->
        <main class="space-y-6">
            <div class="p-8 rounded-2xl bg-slate-800/60 border border-slate-700/60 shadow-xl space-y-4">
                <span class="px-3 py-1 bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xs font-mono rounded-full">
                    Third-Party Client Integration
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight text-white">Embeddable Tawk.to Style Live Chat</h2>
                <p class="text-slate-300 text-sm leading-relaxed max-w-2xl">
                    This page simulates an external customer site (e.g., e-commerce, SaaS homepage). Notice the floating live chat trigger button appearing according to the dashboard configuration (position, color, logo, and title).
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <button onclick="alert('Demo Button Clicked')" class="px-6 py-3 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs rounded-xl transition">
                        Explore Features
                    </button>
                    <span class="text-xs text-slate-400">← Look for the floating chat button in the corner!</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1">
                    <div class="font-bold text-indigo-400">1. Instant Connection</div>
                    <div class="text-slate-400">Click the launcher icon to initialize a real live chat ticket.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1">
                    <div class="font-bold text-emerald-400">2. Dashboard Sync</div>
                    <div class="text-slate-400">Messages sent here appear immediately in the OmniDesk agent workspace.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1">
                    <div class="font-bold text-amber-400">3. Custom Configuration</div>
                    <div class="text-slate-400">Change colors, position, logo, and greetings dynamically in the dashboard.</div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="text-center text-xs text-slate-500 pt-8 border-t border-slate-800">
        Acme Demo Store • Powered by OmniDesk Embeddable Chat Widget
    </footer>

    <!-- OmniDesk Floating Live Chat Widget Script Tag -->
    <script src="{{ url('/widget.js') }}" data-channel-id="1" async></script>
</body>
</html>
