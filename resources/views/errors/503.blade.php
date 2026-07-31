@extends('errors.layout')

@section('title', '503 - Scheduled Maintenance')

@section('code', '503')
@section('icon', 'tools')
@section('subtitle', 'Maintenance In Progress')
@section('system_status', 'Scheduled Upgrade Mode')
@section('status_ping', 'bg-cyan-400')
@section('status_dot', 'bg-cyan-500')

@section('glow_color', 'bg-cyan-600/30')
@section('glow_color_secondary', 'bg-indigo-600/20')
@section('badge_gradient', 'from-cyan-500/20 via-blue-500/10 to-transparent')
@section('code_badge_class', 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30')
@section('tag_class', 'bg-cyan-950/80 text-cyan-300 border-cyan-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-cyan-500/10 blur-md"></div>
    <div class="relative text-cyan-400 flex flex-col items-center">
        <i class="ti ti-tools text-6xl md:text-7xl mb-2 text-cyan-400 drop-shadow-[0_0_15px_rgba(6,182,212,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-cyan-300 shadow-lg">
            <i class="ti ti-refresh animate-spin text-cyan-400"></i>
            <span>SYSTEM UPGRADE IN PROGRESS</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Workspace Maintenance Mode')

@section('description')
OmniDesk is currently undergoing scheduled infrastructure enhancements and database indexing to improve message routing performance. We will be back online shortly.
@endsection

@section('suggestions')
<li>Incoming messages across WhatsApp, Email, and Telegram are queued safely.</li>
<li>System auto-restoration is estimated within 5–10 minutes.</li>
<li>Check back soon or refresh your browser workspace.</li>
@endsection
