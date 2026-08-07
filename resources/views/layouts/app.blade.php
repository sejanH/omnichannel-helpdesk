<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'OmniHelp — Omnichannel Support Agent Workspace')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Toastr Notifications CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-slate-100 text-slate-800 flex flex-col md:flex-row h-screen overflow-hidden antialiased font-sans selection:bg-indigo-500 selection:text-white">

    <!-- Mobile Top Navigation Header -->
    <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-30 shrink-0 shadow-2xs">
        <div class="flex items-center gap-3">
            <button type="button" id="mobile-menu-btn" title="Open Navigation" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">
                <x-icon name="menu-2" class="text-xl" />
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center shadow-xs">
                    <x-icon name="messages" class="text-base text-white" />
                </div>
                <span class="font-extrabold text-slate-900 text-sm tracking-tight">OmniHelp</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn-toggle-sound flex items-center gap-1 p-1 rounded-lg text-slate-500 hover:bg-slate-100 transition cursor-pointer" title="Toggle Notification Sounds">
                <x-icon name="volume" class="sound-icon-on text-lg text-emerald-600 shrink-0" />
                <x-icon name="volume-off" class="sound-icon-off text-lg text-rose-600 hidden shrink-0" />
                <span class="sound-status-text text-[11px] font-extrabold text-emerald-600 shrink-0">ON</span>
            </button>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                <span class="text-xs font-bold text-slate-800 max-w-[110px] truncate">{{ auth()->user()->name ?? 'Agent' }}</span>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar Drawer Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden md:hidden transition-opacity"></div>

    <!-- Unified Sidebar Component -->
    @include('layouts.sidebar')

    <!-- Main Workspace Page Content -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        @yield('content')
    </main>

    <!-- jQuery & Toastr JS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        function playNotificationSound() {
            if (localStorage.getItem('omnidesk_sound_muted') === 'true') {
                return;
            }
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                const now = ctx.currentTime;
                osc.frequency.setValueAtTime(783.99, now); // G5
                osc.frequency.setValueAtTime(1046.50, now + 0.1); // C6

                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.5);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now);
                osc.stop(now + 0.5);
            } catch (e) {
                console.warn('Audio playback prevented or unsupported:', e);
            }
        }

        function updateSoundToggleUI() {
            const isMuted = localStorage.getItem('omnidesk_sound_muted') === 'true';
            if (isMuted) {
                $('#sound-icon-on, .sound-icon-on').addClass('hidden');
                $('#sound-icon-off, .sound-icon-off').removeClass('hidden');
                $('#sound-status-text, .sound-status-text')
                    .text('OFF')
                    .attr('class', 'sound-status-text text-[11px] font-extrabold text-rose-600 shrink-0');
                $('#btn-toggle-sound, .btn-toggle-sound').attr('title', 'Notification Sounds Muted (Click to Turn ON)');
            } else {
                $('#sound-icon-on, .sound-icon-on').removeClass('hidden');
                $('#sound-icon-off, .sound-icon-off').addClass('hidden');
                $('#sound-status-text, .sound-status-text')
                    .text('ON')
                    .attr('class', 'sound-status-text text-[11px] font-extrabold text-emerald-600 shrink-0');
                $('#btn-toggle-sound, .btn-toggle-sound').attr('title', 'Notification Sounds Active (Click to Turn OFF)');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "timeOut": "3500",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                @if(session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                @if(session('error'))
                    toastr.error("{{ session('error') }}");
                @endif
                @if(session('info'))
                    toastr.info("{{ session('info') }}");
                @endif
                @if(session('warning'))
                    toastr.warning("{{ session('warning') }}");
                @endif
            }

            updateSoundToggleUI();

            $(document).on('click', '#btn-toggle-sound, .btn-toggle-sound', function() {
                const currentlyMuted = localStorage.getItem('omnidesk_sound_muted') === 'true';
                localStorage.setItem('omnidesk_sound_muted', currentlyMuted ? 'false' : 'true');
                updateSoundToggleUI();
                if (typeof toastr !== 'undefined') {
                    toastr.info(currentlyMuted ? 'Notification sounds unmuted 🔔' : 'Notification sounds muted 🔇');
                }
                if (currentlyMuted) {
                    playNotificationSound();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
