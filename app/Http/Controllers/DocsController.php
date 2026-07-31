<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocsController extends Controller
{
    public function show($page = 'README')
    {
        // Prevent directory traversal
        $page = str_replace(['../', '..\\'], '', $page);
        
        // Remove .md extension if it was included in the URL
        $page = preg_replace('/\.md$/', '', $page);

        if ($page === 'deployment') {
            abort(404, 'Documentation page not found.');
        }

        $path = base_path("docs/{$page}.md");

        if (!File::exists($path)) {
            abort(404, 'Documentation page not found.');
        }

        $content = File::get($path);
        
        // Parse markdown to HTML
        $html = Str::markdown($content);

        // Get list of all documentation files for the sidebar navigation
        $files = collect(File::files(base_path('docs')))
            ->map(function ($file) {
                return $file->getFilenameWithoutExtension();
            })
            ->filter(function ($filename) {
                return $filename !== 'deployment';
            })
            ->sort()
            ->values();

        return view('docs.show', [
            'html' => $html,
            'page' => $page,
            'files' => $files
        ]);
    }
}
