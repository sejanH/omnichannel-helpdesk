@extends('errors.layout')

@section('title', '404 - Ticket or Page Not Found')

@section('code', '404')
@section('icon', 'compass-off')
@section('subtitle', 'Resource Not Found')
@section('system_status', 'Resource Misplaced')
@section('status_ping', 'bg-indigo-400')
@section('status_dot', 'bg-indigo-500')

@section('glow_color', 'bg-indigo-600/30')
@section('glow_color_secondary', 'bg-cyan-600/20')
@section('badge_gradient', 'from-indigo-500/20 via-blue-500/10 to-transparent')
@section('code_badge_class', 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30')
@section('tag_class', 'bg-indigo-950/80 text-indigo-300 border-indigo-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-indigo-500/10 blur-md"></div>
    <div class="relative text-indigo-400 flex flex-col items-center">
        <i class="ti ti-compass-off text-6xl md:text-7xl mb-2 text-indigo-400 drop-shadow-[0_0_15px_rgba(99,102,241,0.5)]"></i>
        <div class="flex items-center gap-1 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-slate-400 shadow-lg">
            <i class="ti ti-ticket-off text-rose-400"></i>
            <span>#TK-????</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Ticket or Page Not Found')

@section('description')
The ticket ID, customer record, or workspace path you are looking for does not exist, has been transferred, or was permanently archived.
@endsection

@section('suggestions')
<li>Verify the ticket ID or URL string for typos.</li>
<li>Search for the ticket by customer email in your workspace search bar.</li>
<li>Ensure your agent account has access to the requested department inbox.</li>
@endsection
