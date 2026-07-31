@extends('errors.layout')

@section('title', '500 - Internal Server Error')

@section('code', '500')
@section('icon', 'cpu-off')
@section('subtitle', 'Internal System Failure')
@section('system_status', 'Core Server Alert')
@section('status_ping', 'bg-rose-400')
@section('status_dot', 'bg-rose-500')

@section('glow_color', 'bg-rose-600/30')
@section('glow_color_secondary', 'bg-purple-600/20')
@section('badge_gradient', 'from-rose-500/20 via-red-500/10 to-transparent')
@section('code_badge_class', 'bg-rose-500/20 text-rose-300 border-rose-500/30')
@section('tag_class', 'bg-rose-950/80 text-rose-300 border-rose-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-rose-500/10 blur-md"></div>
    <div class="relative text-rose-400 flex flex-col items-center">
        <i class="ti ti-cpu-off text-6xl md:text-7xl mb-2 text-rose-400 drop-shadow-[0_0_15px_rgba(244,63,94,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-rose-300 shadow-lg">
            <i class="ti ti-bolt text-rose-400 animate-pulse"></i>
            <span>OMNI CORE ENGINE CRASH</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Internal Engine Exception')

@section('description')
OmniDesk backend encountered an unexpected internal server exception while processing ticket dispatches or database queries. Our engineering team has been automatically notified.
@endsection

@section('suggestions')
<li>Refresh the page in a few moments; server processes may auto-recover.</li>
<li>Check the expandable <strong>Diagnostics</strong> panel below for trace logs.</li>
<li>If the issue persists, contact system administration with the Request ID.</li>
@endsection
