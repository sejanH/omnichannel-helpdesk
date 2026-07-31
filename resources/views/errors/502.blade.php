@extends('errors.layout')

@section('title', '502 - Bad Gateway')

@section('code', '502')
@section('icon', 'plug-connected-x')
@section('subtitle', 'Upstream Disconnection')
@section('system_status', 'Gateway Link Down')
@section('status_ping', 'bg-rose-400')
@section('status_dot', 'bg-rose-500')

@section('glow_color', 'bg-purple-600/30')
@section('glow_color_secondary', 'bg-rose-600/20')
@section('badge_gradient', 'from-purple-500/20 via-rose-500/10 to-transparent')
@section('code_badge_class', 'bg-purple-500/20 text-purple-300 border-purple-500/30')
@section('tag_class', 'bg-purple-950/80 text-purple-300 border-purple-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-purple-500/10 blur-md"></div>
    <div class="relative text-purple-400 flex flex-col items-center">
        <i class="ti ti-plug-connected-x text-6xl md:text-7xl mb-2 text-purple-400 drop-shadow-[0_0_15px_rgba(168,85,247,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-purple-300 shadow-lg">
            <i class="ti ti-brand-whatsapp text-emerald-400"></i>
            <i class="ti ti-arrows-right-left text-slate-500"></i>
            <i class="ti ti-server-off text-rose-400"></i>
        </div>
    </div>
</div>
@endsection

@section('message', 'Upstream Channel Disconnected')

@section('description')
OmniDesk proxy received an invalid response from an upstream channel service (such as Meta WhatsApp Business API, Telegram Bot Gateway, or SMTP Mail Server).
@endsection

@section('suggestions')
<li>Verify third-party channel API status and access keys in Channel Settings.</li>
<li>Retry the operation to establish a fresh bridge handshake.</li>
@endsection
