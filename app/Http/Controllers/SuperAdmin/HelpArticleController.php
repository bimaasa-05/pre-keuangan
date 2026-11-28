<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HelpArticleController extends Controller
{
    public function index(): View
    {
        $articles = HelpArticle::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('super-admin.help.index', compact('articles'));
    }

    public function create(): View
    {
        return view('super-admin.help.form', ['article' => new HelpArticle]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        $article = HelpArticle::create($data);

        NotificationService::send(
            $request->user(),
            'Artikel bantuan ditambahkan',
            'Artikel "'.$article->title.'" berhasil dibuat.',
            'book-open',
            'warning',
            route('super-admin.help.edit', $article),
            NotificationService::superAdmins(),
        );

        return redirect()
            ->route('super-admin.help.index')
            ->with('success', 'Artikel bantuan berhasil ditambahkan.');
    }

    public function edit(HelpArticle $helpArticle): View
    {
        return view('super-admin.help.form', ['article' => $helpArticle]);
    }

    public function update(Request $request, HelpArticle $helpArticle): RedirectResponse
    {
        $data = $this->validated($request);

        if ($helpArticle->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $helpArticle->id);
        }

        $helpArticle->update($data);

        NotificationService::send(
            $request->user(),
            'Artikel bantuan diperbarui',
            'Artikel "'.$helpArticle->title.'" berhasil diperbarui.',
            'book-open',
            'warning',
            route('super-admin.help.edit', $helpArticle),
            NotificationService::superAdmins(),
        );

        return redirect()
            ->route('super-admin.help.index')
            ->with('success', 'Artikel bantuan berhasil diperbarui.');
    }

    public function destroy(Request $request, HelpArticle $helpArticle): RedirectResponse
    {
        $title = $helpArticle->title;
        $helpArticle->delete();

        NotificationService::send(
            $request->user(),
            'Artikel bantuan dihapus',
            'Artikel "'.$title.'" telah dihapus.',
            'trash',
            'expense',
            route('super-admin.help.index'),
            NotificationService::superAdmins(),
        );

        return redirect()
            ->route('super-admin.help.index')
            ->with('success', 'Artikel bantuan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:300'],
            'content' => ['required', 'string', 'max:20000'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'category.required' => 'Kategori wajib diisi.',
            'content.required' => 'Isi artikel wajib diisi.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $counter = 2;

        while (HelpArticle::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
