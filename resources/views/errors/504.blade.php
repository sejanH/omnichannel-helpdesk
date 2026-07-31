@extends('errors.layout')

@section('title', '504 - Gateway Timeout')

@section('code', '504')
@section('icon', 'clock-stop')
@section('subtitle', 'Channel Gateway Timeout')
@section('system_status', 'Response Timeout')
@section('status_ping', 'bg-yellow-400')
@section('status_dot', 'bg-yellow-500')

@section('glow_color', 'bg-yellow-600/30')
@section('glow_color_secondary', 'bg-amber-600/20')
@section('badge_gradient', 'from-yellow-500/20 via-amber-500/10 to-transparent')
@section('code_badge_class', 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30')
@section('tag_class', 'bg-yellow-950/80 text-yellow-300 border-yellow-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-yellow-500/10 blur-md"></div>
    <div class="relative text-yellow-400 flex flex-col items-center">
        <i class="ti ti-clock-stop text-6xl md:text-7xl mb-2 text-yellow-400 drop-shadow-[0_0_15px_rgba(234,179,8,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-yellow-300 shadow-lg">
            <i class="ti ti-loader-2 text-yellow-400 animate-spin"></i>
            <span>30,000ms TIMEOUT EXCEEDED</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Dispatch Gateway Timeout')

@section('description')
The server acting as a gateway or proxy did not receive a timely response from the underlying omnichannel messaging dispatcher.
@endsection

@section('suggestions')
<li>Retry sending the message or refreshing your inbox queue.</li>
<li>Check if large file attachments or heavy payload filters are slowing execution.</li>
@endsection
