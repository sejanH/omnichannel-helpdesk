@extends('layouts.app')

@section('title', $article->title . ' — Knowledge Base')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8 space-y-8">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <a href="{{ route('kb.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <x-icon name="arrow-left" class="text-sm" />
            <span>Back to Knowledge Base</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                {{ $article->category }}
            </span>
            <span class="text-xs text-slate-400 font-mono flex items-center gap-1">
                <x-icon name="eye" class="text-xs" />
                <span>{{ $article->views_count }} views</span>
            </span>
        </div>
    </div>

    <!-- Article Detail Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Article Content (8 Columns) -->
        <div class="lg:col-span-8 bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">{{ $article->title }}</h1>
                <div class="flex items-center gap-3 text-xs text-slate-400 mt-2">
                    <span>Published {{ $article->created_at->format('M d, Y') }}</span>
                    <span>•</span>
                    <span>By {{ $article->author->name ?? 'OmniHelp Support' }}</span>
                </div>
            </div>

            <div class="prose prose-slate max-w-none text-xs leading-relaxed text-slate-700 space-y-4 pt-2 border-t border-slate-100">
                {!! nl2br(e($article->content)) !!}
            </div>

            <!-- Helpful Feedback Rating -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Was this article helpful?</span>
                <div class="flex items-center gap-2">
                    <button onclick="alert('Thank you for your feedback!')" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                        👍 Yes
                    </button>
                    <button onclick="alert('Thank you for your feedback!')" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200 hover:bg-slate-200 transition cursor-pointer">
                        👎 No
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Related Articles (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <x-icon name="book" class="text-indigo-600" />
                    <span>Related Articles</span>
                </h3>

                <div class="space-y-3 pt-1">
                    @forelse($relatedArticles as $rel)
                        <a href="{{ route('kb.show', $rel->slug) }}" class="block p-3 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 transition group">
                            <div class="font-bold text-xs text-slate-900 group-hover:text-indigo-600 transition">{{ $rel->title }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-2">
                                <span>{{ $rel->category }}</span>
                                <span>•</span>
                                <span>{{ $rel->views_count }} views</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-xs text-slate-400 italic">No other related articles in this category.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
