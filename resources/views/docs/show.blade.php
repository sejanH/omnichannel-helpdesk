@extends('layouts.app')

@section('title', 'Documentation — OmniDesk')

@push('styles')
<!-- GitHub Markdown CSS for styling the raw HTML output -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.5.0/github-markdown-dark.min.css">
<style>
    .markdown-body {
        box-sizing: border-box;
        min-width: 200px;
        max-width: 900px;
        margin: 0 auto;
        padding: 45px;
        background-color: transparent;
    }
    @media (max-width: 767px) {
        .markdown-body {
            padding: 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="flex-1 flex overflow-hidden bg-slate-950">
    <!-- Docs Sidebar -->
    <div class="w-64 border-r border-slate-800 bg-slate-900/50 flex flex-col hidden md:flex">
        <div class="p-4 border-b border-slate-800">
            <h2 class="font-bold text-slate-200">Documentation</h2>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            @foreach($files as $f)
                <a href="{{ route('docs.show', $f) }}" 
                   class="block px-3 py-2 rounded-lg text-sm transition {{ $f === $page ? 'bg-indigo-500/20 text-indigo-400 font-bold border border-indigo-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                   {{ Str::headline($f) }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Docs Content -->
    <div class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="glass-panel border border-slate-800 rounded-2xl bg-slate-900/60 max-w-5xl mx-auto">
            <article class="markdown-body">
                {!! $html !!}
            </article>
        </div>
    </div>
</div>
@endsection
