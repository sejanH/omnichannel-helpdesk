@extends('errors.layout')

@section('title', 'Server Exception Error')

@section('code', '5XX')
@section('icon', 'server-off')
@section('subtitle', 'Server Infrastructure Alert')
@section('system_status', 'Server Fault')
@section('status_ping', 'bg-rose-400')
@section('status_dot', 'bg-rose-500')

@section('glow_color', 'bg-rose-600/30')
@section('glow_color_secondary', 'bg-orange-600/20')
@section('badge_gradient', 'from-rose-500/20 via-purple-500/10 to-transparent')
@section('code_badge_class', 'bg-rose-500/20 text-rose-300 border-rose-500/30')
@section('tag_class', 'bg-rose-950/80 text-rose-300 border-rose-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-rose-500/10 blur-md"></div>
    <div class="relative text-rose-400 flex flex-col items-center">
        <i class="ti ti-server-off text-6xl md:text-7xl mb-2 text-rose-400 drop-shadow-[0_0_15px_rgba(244,63,94,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-rose-300 shadow-lg">
            <i class="ti ti-alert-circle text-rose-400"></i>
            <span>UNHANDLED SERVER EXCEPTION</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Server Processing Interrupted')

@section('description')
An unhandled server-side condition prevented OmniDesk from executing your request.
@endsection

@section('suggestions')
<li>Refresh the page to retry the server request.</li>
<li>Contact your system administrator if this error persists.</li>
@endsection
