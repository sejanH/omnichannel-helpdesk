@extends('layouts.app')

@section('title', 'Knowledge Base & FAQ Portal — OmniHelp')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8 space-y-8">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <x-icon name="help" class="text-indigo-600" />
                <span>Knowledge Base & Self-Service FAQ</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Browse help articles, integration guides, and troubleshooting documentation.</p>
        </div>

        @auth
            <button onclick="openPublishArticleModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <x-icon name="plus" class="text-sm" />
                <span>Publish New Article</span>
            </button>
        @endauth
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <x-icon name="circle-check" class="text-base text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><x-icon name="x" class="text-sm" /></button>
        </div>
    @endif

    <!-- Search Hero Box -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs text-center relative overflow-hidden space-y-4">
        <div class="max-w-xl mx-auto space-y-3">
            <h2 class="text-xl font-extrabold text-slate-900">How can we help you today?</h2>
            <form action="{{ route('kb.index') }}" method="GET" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles, setup guides, FAQs..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 shadow-2xs">
                    <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base" />
                </div>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Search
                </button>
            </form>
        </div>
    </div>

    <!-- Category Pill Filters -->
    @if($categories->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <a href="{{ route('kb.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ !request('category') ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('kb.index', ['category' => $cat]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ request('category') === $cat ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $article->category }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                            <x-icon name="eye" class="text-xs" />
                            <span>{{ $article->views_count }} views</span>
                        </span>
                    </div>

                    <a href="{{ route('kb.show', $article->slug) }}" class="block font-bold text-slate-900 text-base group-hover:text-indigo-600 transition">
                        {{ $article->title }}
                    </a>

                    <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('kb.show', $article->slug) }}" class="font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <span>Read Article</span>
                        <x-icon name="arrow-right" class="text-xs" />
                    </a>

                    @auth
                        <form action="{{ route('kb.destroy', $article) }}" method="POST" onsubmit="return confirm('Delete article \'{{ $article->title }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-600 transition cursor-pointer">
                                <x-icon name="trash" class="text-sm" />
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 rounded-3xl border border-slate-200/80 text-center text-slate-500 text-xs">
                No Knowledge Base articles found matching your criteria.
            </div>
        @endforelse
    </div>

</div>

@auth
<!-- Modal: Publish New KB Article -->
<div id="publish-article-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-lg bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <x-icon name="plus" class="text-indigo-600" />
                <span>Publish New FAQ Article</span>
            </h3>
            <button onclick="closePublishArticleModal()" class="text-slate-400 hover:text-slate-600"><x-icon name="x" class="text-lg" /></button>
        </div>

        <form action="{{ route('kb.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Article Title *</label>
                <input type="text" name="title" required placeholder="e.g. How to Connect Webhooks" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                <input type="text" name="category" required placeholder="e.g. Integrations, Billing, Widget Setup" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Article Content *</label>
                <textarea name="content" rows="6" required placeholder="Provide clear step-by-step instructions..." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closePublishArticleModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Publish Article</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPublishArticleModal() {
        document.getElementById('publish-article-modal').classList.remove('hidden');
    }
    function closePublishArticleModal() {
        document.getElementById('publish-article-modal').classList.add('hidden');
    }
</script>
@endauth
@endsection
