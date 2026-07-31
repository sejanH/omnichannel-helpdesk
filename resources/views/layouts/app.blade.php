<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'OmniDesk — Omnichannel Support Agent Workspace')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-slate-950 text-slate-100 flex h-screen overflow-hidden antialiased font-sans selection:bg-indigo-500 selection:text-white">

    <!-- Unified Sidebar Component -->
    @include('layouts.sidebar')

    <!-- Main Workspace Page Content -->
    <main class="flex-1 flex overflow-hidden">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
