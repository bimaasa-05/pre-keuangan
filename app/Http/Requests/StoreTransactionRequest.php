<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'receipt' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $category = Category::query()->find($this->input('category_id'));

            if ($category && $category->type !== $this->input('type')) {
                $validator->errors()->add('category_id', 'Kategori yang dipilih tidak sesuai dengan tipe transaksi.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Pilih tipe transaksi terlebih dahulu.',
            'type.in' => 'Tipe transaksi tidak valid.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.integer' => 'Nominal transaksi harus berupa angka.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'amount.max' => 'Nominal transaksi terlalu besar.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'description.max' => 'Keterangan transaksi maksimal 255 karakter.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'receipt.image' => 'Bukti nota harus berupa gambar.',
            'receipt.mimes' => 'Format bukti nota harus JPG, JPEG, PNG, atau WEBP.',
            'receipt.max' => 'Ukuran bukti nota maksimal 2 MB.',
        ];
    }
}
