@extends('errors.layout')

@section('title', '429 - Rate Limit Exceeded')

@section('code', '429')
@section('icon', 'gauge-off')
@section('subtitle', 'Queue Throttled')
@section('system_status', 'Rate Limit Exceeded')
@section('status_ping', 'bg-orange-400')
@section('status_dot', 'bg-orange-500')

@section('glow_color', 'bg-orange-600/30')
@section('glow_color_secondary', 'bg-red-600/20')
@section('badge_gradient', 'from-orange-500/20 via-red-500/10 to-transparent')
@section('code_badge_class', 'bg-orange-500/20 text-orange-300 border-orange-500/30')
@section('tag_class', 'bg-orange-950/80 text-orange-300 border-orange-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-orange-500/10 blur-md"></div>
    <div class="relative text-orange-400 flex flex-col items-center">
        <i class="ti ti-gauge-off text-6xl md:text-7xl mb-2 text-orange-400 drop-shadow-[0_0_15px_rgba(249,115,22,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-orange-300 shadow-lg">
            <i class="ti ti-flame text-orange-400"></i>
            <span>DISPATCH THROTTLED</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Message Stream Throttled')

@section('description')
You or an automated channel script have sent too many requests in a short period. Dispatch throttled to prevent spam and preserve channel reliability.
@endsection

@section('suggestions')
<li>Wait 30-60 seconds before sending additional messages or refreshing.</li>
<li>Avoid batch submitting high volumes of tickets via single API endpoints.</li>
@endsection
