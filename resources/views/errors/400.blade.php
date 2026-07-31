@extends('errors.layout')

@section('title', '400 - Invalid Request Payload')

@section('code', '400')
@section('icon', 'file-code-2')
@section('subtitle', 'Bad Request')
@section('system_status', 'Malformed Request Data')
@section('status_ping', 'bg-amber-400')
@section('status_dot', 'bg-amber-500')

@section('glow_color', 'bg-amber-600/25')
@section('glow_color_secondary', 'bg-yellow-600/20')
@section('badge_gradient', 'from-amber-500/20 via-yellow-500/10 to-transparent')
@section('code_badge_class', 'bg-amber-500/20 text-amber-300 border-amber-500/30')
@section('tag_class', 'bg-amber-950/80 text-amber-300 border-amber-800/80')

@section('illustration')
<div class="relative flex items-center justify-center animate-float-slow">
    <div class="absolute inset-0 rounded-full bg-amber-500/10 blur-md"></div>
    <div class="relative text-amber-400 flex flex-col items-center">
        <i class="ti ti-file-code-2 text-6xl md:text-7xl mb-2 text-amber-400 drop-shadow-[0_0_15px_rgba(245,158,11,0.5)]"></i>
        <div class="flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-md text-[11px] font-mono text-amber-300 shadow-lg">
            <i class="ti ti-braces text-amber-400"></i>
            <span>INVALID JSON / SYNTAX</span>
        </div>
    </div>
</div>
@endsection

@section('message', 'Malformed Ticket Request')

@section('description')
The helpdesk server could not understand your request because the submitted data payload, query parameter format, or header schema was invalid.
@endsection

@section('suggestions')
<li>Check submitted ticket form inputs or webhook parameters for formatting errors.</li>
<li>Refresh the ticket workspace page and re-submit your response.</li>
<li>Verify API request body parameters against OmniDesk documentation.</li>
@endsection
