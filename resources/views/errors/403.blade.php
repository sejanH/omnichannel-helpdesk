@extends('errors.layout')

@section('title', '403 - Access Restricted')

@section('code', '403')
@section('icon', 'shield-x')
@section('subtitle', 'Access Restricted')
@section('system_status', 'Clearance Required')
@section('status_ping', 'bg-amber-400')
@section('status_dot', 'bg-amber-500')

@section('glow_color', 'bg-amber-600/25')
@section('glow_color_secondary', 'bg-rose-600/20')
@section('badge_gradient', 'from-amber-500/20 via-orange-500/10 to-transparent')
@section('code_badge_class', 'bg-amber-500/20 text-amber-300 border-amber-500/30')
@section('tag_class', 'bg-amber-950/80 text-amber-300 border-amber-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-amber-500/10 blur-md"></div>
    <div class="relative text-amber-400 flex flex-col items-center">
        <i class="ti ti-shield-lock text-6xl md:text-7xl mb-2 text-amber-400 drop-shadow-[0_0_15px_rgba(245,158,11,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-amber-950/90 border border-amber-800/80 px-3 py-1 rounded-md text-[11px] font-mono text-amber-200 shadow-lg">
            <i class="ti ti-lock-access text-amber-400"></i>
            <span>AGENT LEVEL 3 REQUIRED</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Agent Clearance Restricted')

@section('description')
You do not have administrative permission to view or manipulate this ticket queue, audit record, or system settings section.
@endsection

@section('suggestions')
<li>Contact your Helpdesk Supervisor to request elevated role privileges.</li>
<li>Switch to an authorized agent profile if you manage multiple team accounts.</li>
<li>Return to your assigned queue inbox to continue handling active tickets.</li>
@endsection
