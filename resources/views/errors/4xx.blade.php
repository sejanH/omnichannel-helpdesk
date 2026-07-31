@extends('errors.layout')

@section('title', 'Client Request Error')

@section('code', '4XX')
@section('icon', 'alert-triangle')
@section('subtitle', 'Client Error')
@section('system_status', 'Client Request Issue')
@section('status_ping', 'bg-amber-400')
@section('status_dot', 'bg-amber-500')

@section('glow_color', 'bg-amber-600/25')
@section('glow_color_secondary', 'bg-indigo-600/20')
@section('badge_gradient', 'from-amber-500/20 via-orange-500/10 to-transparent')
@section('code_badge_class', 'bg-amber-500/20 text-amber-300 border-amber-500/30')
@section('tag_class', 'bg-amber-950/80 text-amber-300 border-amber-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-amber-500/10 blur-md"></div>
    <div class="relative text-amber-400 flex flex-col items-center">
        <i class="ti ti-alert-triangle text-6xl md:text-7xl mb-2 text-amber-400 drop-shadow-[0_0_15px_rgba(245,158,11,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-amber-300 shadow-lg">
            <i class="ti ti-access-point-off text-amber-400"></i>
            <span>CLIENT ACTION ERROR</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Client Request Disruption')

@section('description')
The helpdesk server was unable to fulfill your action due to an unexpected client-side error or invalid parameters.
@endsection

@section('suggestions')
<li>Refresh the page and re-attempt your ticket operation.</li>
<li>Ensure your network connection is stable.</li>
@endsection
