@extends('errors.layout')

@section('title', '419 - Workspace Session Expired')

@section('code', '419')
@section('icon', 'hourglass-empty')
@section('subtitle', 'Session Expired')
@section('system_status', 'CSRF Security Timeout')
@section('status_ping', 'bg-sky-400')
@section('status_dot', 'bg-sky-500')

@section('glow_color', 'bg-sky-600/30')
@section('glow_color_secondary', 'bg-indigo-600/20')
@section('badge_gradient', 'from-sky-500/20 via-indigo-500/10 to-transparent')
@section('code_badge_class', 'bg-sky-500/20 text-sky-300 border-sky-500/30')
@section('tag_class', 'bg-sky-950/80 text-sky-300 border-sky-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-sky-500/10 blur-md"></div>
    <div class="relative text-sky-400 flex flex-col items-center">
        <i class="ti ti-hourglass-empty text-6xl md:text-7xl mb-2 text-sky-400 drop-shadow-[0_0_15px_rgba(56,189,248,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-sky-300 shadow-lg">
            <i class="ti ti-clock-pause text-sky-400"></i>
            <span>CSRF TOKEN TIMEOUT</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Workspace Session Expired')

@section('description')
Your agent workspace session was idle for too long and the security CSRF validation token expired to protect your customer conversations.
@endsection

@section('suggestions')
<li>Click <strong>Retry Connection</strong> or refresh your browser tab to generate a new CSRF token.</li>
<li>Your ticket drafts are auto-saved in local cache.</li>
@endsection
