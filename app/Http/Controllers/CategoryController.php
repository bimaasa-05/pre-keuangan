<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
            ->orderBy('type')
            ->orderBy('name')
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

        Category::create($data);

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

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi terkait.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
