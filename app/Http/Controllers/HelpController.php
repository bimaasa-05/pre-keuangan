<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $query = HelpArticle::query()
            ->where('is_published', true)
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('category', $category));

        $articles = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $categories = HelpArticle::query()
            ->where('is_published', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('help.index', compact('articles', 'categories', 'filters'));
    }

    public function show(HelpArticle $helpArticle): View
    {
        abort_unless($helpArticle->is_published, 404);

        $related = HelpArticle::query()
            ->where('is_published', true)
            ->where('id', '!=', $helpArticle->id)
            ->where('category', $helpArticle->category)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('help.show', compact('helpArticle', 'related'));
    }
}
