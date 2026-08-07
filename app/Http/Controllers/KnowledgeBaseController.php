<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    /**
     * Display listing of Knowledge Base articles (Public / Helpdesk view).
     */
    public function index(Request $request)
    {
        $query = KnowledgeBaseArticle::where('is_published', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $articles = $query->orderBy('views_count', 'desc')->get();
        $categories = KnowledgeBaseArticle::where('is_published', true)->distinct()->pluck('category');

        return view('kb.index', compact('articles', 'categories'));
    }

    /**
     * Show a single KB Article.
     */
    public function show($slug)
    {
        $article = KnowledgeBaseArticle::where('slug', $slug)->firstOrFail();
        $article->increment('views_count');

        $relatedArticles = KnowledgeBaseArticle::where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->take(4)
            ->get();

        return view('kb.show', compact('article', 'relatedArticles'));
    }

    /**
     * Store new KB Article (Admin / Agent Only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $article = KnowledgeBaseArticle::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'category' => $validated['category'],
            'content' => $validated['content'],
            'author_id' => auth()->id(),
            'is_published' => true,
        ]);

        return redirect()->back()->with('success', "Knowledge Base article '{$article->title}' published successfully!");
    }

    /**
     * Delete KB Article.
     */
    public function destroy(KnowledgeBaseArticle $article)
    {
        $article->delete();
        return redirect()->route('kb.index')->with('success', 'Article deleted successfully.');
    }
}
