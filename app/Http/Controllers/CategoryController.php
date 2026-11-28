<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('transactions')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
            'icons' => Category::ICONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'icon' => ['nullable', Rule::in(Category::ICONS)],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'type.required' => 'Tipe kategori wajib dipilih.',
        ]);

        $data['icon'] = $data['icon'] ?? 'tag';

        $category = Category::create($data);

        NotificationService::send(
            $request->user(),
            'Kategori ditambahkan',
            'Kategori "'.$category->name.'" berhasil dibuat.',
            'tag',
            $category->type === 'income' ? 'income' : 'expense',
            route('categories.index'),
        );

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'icon' => ['nullable', Rule::in(Category::ICONS)],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'type.required' => 'Tipe kategori wajib dipilih.',
        ]);

        if ($category->transactions()->exists() && $data['type'] !== $category->type) {
            return back()->with('error', 'Tipe kategori tidak dapat diubah karena sudah memiliki transaksi terkait.');
        }

        $data['icon'] = $data['icon'] ?? 'tag';

        $category->update($data);

        NotificationService::send(
            $request->user(),
            'Kategori diperbarui',
            'Kategori "'.$category->name.'" berhasil diperbarui.',
            'tag',
            'warning',
            route('categories.index'),
        );

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi terkait.');
        }

        $name = $category->name;
        $category->delete();

        NotificationService::send(
            $request->user(),
            'Kategori dihapus',
            'Kategori "'.$name.'" telah dihapus.',
            'trash',
            'expense',
            route('categories.index'),
        );

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
