@extends('errors.layout')

@section('title', '401 - Agent Authentication Required')

@section('code', '401')
@section('icon', 'lock-access')
@section('subtitle', 'Authentication Required')
@section('system_status', 'Session Unauthenticated')
@section('status_ping', 'bg-violet-400')
@section('status_dot', 'bg-violet-500')

@section('glow_color', 'bg-violet-600/30')
@section('glow_color_secondary', 'bg-indigo-600/20')
@section('badge_gradient', 'from-violet-500/20 via-purple-500/10 to-transparent')
@section('code_badge_class', 'bg-violet-500/20 text-violet-300 border-violet-500/30')
@section('tag_class', 'bg-violet-950/80 text-violet-300 border-violet-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-violet-500/10 blur-md"></div>
    <div class="relative text-violet-400 flex flex-col items-center">
        <i class="ti ti-keyframe text-6xl md:text-7xl mb-2 text-violet-400 drop-shadow-[0_0_15px_rgba(139,92,246,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-slate-300 shadow-lg">
            <i class="ti ti-user-exclamation text-violet-400"></i>
            <span>AGENT SIGN-IN NEEDED</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Agent Authentication Required')

@section('description')
Your agent authentication token has expired or you are not signed into the OmniDesk Workspace. Authentication is required to access support channels.
@endsection

@section('suggestions')
<li>Sign in using your OmniDesk agent credentials or SSO provider.</li>
<li>Ensure cookies and session tokens are allowed in your browser settings.</li>
@endsection
