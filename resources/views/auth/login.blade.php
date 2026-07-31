<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — OmniDesk Helpdesk</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="p-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                    <x-icon name="messages" class="text-2xl text-white" />
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-white flex items-center gap-1.5">
                        OmniDesk
                    </span>
                    <span class="block text-[11px] text-slate-400 font-medium leading-none">Omnichannel Platform</span>
                </div>
            </a>
            <div class="text-xs text-slate-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Sign Up</a>
            </div>
        </div>
    </header>

    <!-- Login Container -->
    <main class="flex-1 flex items-center justify-center px-4 py-12 relative">
        <!-- Glowing background gradient -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-[130px] pointer-events-none"></div>

        <div class="w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl glass-panel relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Welcome Back</h1>
                <p class="text-xs text-slate-400 mt-1">Sign in to access your omnichannel agent workspace</p>
            </div>

            <!-- Fast Demonstration Quick Login -->
            <div class="mb-6 p-4 rounded-2xl bg-indigo-600/10 border border-indigo-500/30 space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold text-indigo-300">
                    <span>⚡ Quick Demo One-Click Login</span>
                    <span class="text-[10px] bg-indigo-500/20 px-1.5 py-0.5 rounded text-indigo-400">Instant</span>
                </div>
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" onclick="fillDemoCredentials('admin@helpdesk.com', 'password')" class="py-2 px-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-1.5">
                        <x-icon name="user" class="text-sm" />
                        <span>As Admin</span>
                    </button>
                    <button type="button" onclick="fillDemoCredentials('agent@helpdesk.com', 'password')" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold border border-slate-700 transition flex items-center justify-center gap-1.5">
                        <x-icon name="user-check" class="text-sm" />
                        <span>As Agent</span>
                    </button>
                </div>
            </div>

            <!-- Divider -->
            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-3 text-[10px] text-slate-500 uppercase font-mono tracking-wider">Or enter credentials</span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', 'admin@helpdesk.com') }}" placeholder="admin@helpdesk.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Password</label>
                        <a href="#" class="text-[11px] text-indigo-400 hover:text-indigo-300">Forgot?</a>
                    </div>
                    <input type="password" name="password" id="password" required value="password" placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span>Remember me for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                    Sign In to Agent Workspace
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="p-6 text-center text-xs text-slate-500">
        © 2026 OmniDesk Engine. All rights reserved.
    </footer>

    <script>
        function fillDemoCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
