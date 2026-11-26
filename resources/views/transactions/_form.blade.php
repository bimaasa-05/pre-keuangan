@php
    $isEdit = isset($transaction);
    $currentType = old('type', $isEdit ? $transaction->type : 'income');
    $currentCategory = old('category_id', $isEdit ? $transaction->category_id : '');
    $currentAmount = old('amount', $isEdit ? $transaction->amount : '');
    $currentDate = old('transaction_date', $isEdit ? $transaction->transaction_date->toDateString() : now()->toDateString());
    $currentDescription = old('description', $isEdit ? $transaction->description : '');
    $currentNotes = old('notes', $isEdit ? $transaction->notes : '');
@endphp

<form method="POST"
      action="{{ $isEdit ? route('transactions.update', $transaction) : route('transactions.store') }}"
      enctype="multipart/form-data"
      class="space-y-4"
      x-data="{
          type: @js($currentType),
          amountRaw: @js((string) $currentAmount),
          amountDisplay: '',
          receiptPreview: null,
          init() {
              this.amountDisplay = this.formatAmount(this.amountRaw);
          },
          formatAmount(value) {
              const digits = String(value).replace(/\D/g, '');
              return digits ? new Intl.NumberFormat('id-ID').format(digits) : '';
          },
          onAmountInput(event) {
              const digits = event.target.value.replace(/\D/g, '');
              this.amountRaw = digits;
              this.amountDisplay = this.formatAmount(digits);
              event.target.value = this.amountDisplay;
          },
          previewReceipt(event) {
              const file = event.target.files[0];
              this.receiptPreview = file ? URL.createObjectURL(file) : null;
          }
      }">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <input type="hidden" name="type" x-model="type">

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <p class="text-xs font-semibold text-zinc-700">Tipe Transaksi</p>
        <div class="mt-2 grid grid-cols-2 gap-1.5 rounded-2xl bg-zinc-100 p-1.5">
            <button type="button" @click="type = 'income'"
                    :class="type === 'income' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <x-icon name="arrow-down-left" class="h-4 w-4" />
                Pemasukan
            </button>
            <button type="button" @click="type = 'expense'"
                    :class="type === 'expense' ? 'bg-zinc-900 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <x-icon name="arrow-up-right" class="h-4 w-4" />
                Pengeluaran
            </button>
        </div>
        @error('type')
            <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <label for="amount_display" class="text-xs font-semibold text-zinc-700">Nominal</label>
        <div class="mt-2 flex items-center gap-2 border-b border-zinc-200 pb-3">
            <span class="font-display text-2xl font-bold text-zinc-400">Rp</span>
            <input id="amount_display" type="text" inputmode="numeric" autocomplete="off"
                   :value="amountDisplay" @input="onAmountInput($event)" placeholder="0"
                   class="w-full border-0 bg-transparent p-0 font-display text-3xl font-bold tabular-nums tracking-tight text-zinc-900 placeholder:text-zinc-300 focus:ring-0">
            <input type="hidden" name="amount" value="{{ $currentAmount }}" x-model="amountRaw">
        </div>
        @error('amount')
            <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
            <label for="category_income" class="text-xs font-semibold text-zinc-700">Kategori</label>
            <div class="relative mt-2">
                <select id="category_income" name="category_id" x-show="type === 'income'" :disabled="type !== 'income'" x-cloak
                        class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-9 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    <option value="">Pilih kategori pemasukan</option>
                    @foreach ($categories->where('type', 'income') as $category)
                        <option value="{{ $category->id }}" @selected((string) $currentCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select id="category_expense" name="category_id" x-show="type === 'expense'" :disabled="type !== 'expense'" x-cloak
                        class="w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 pr-9 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                    <option value="">Pilih kategori pengeluaran</option>
                    @foreach ($categories->where('type', 'expense') as $category)
                        <option value="{{ $category->id }}" @selected((string) $currentCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
            </div>
            @error('category_id')
                <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
            <label for="transaction_date" class="text-xs font-semibold text-zinc-700">Tanggal</label>
            <input id="transaction_date" name="transaction_date" type="date" value="{{ $currentDate }}" required
                   class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('transaction_date') border-orange-400 @enderror">
            @error('transaction_date')
                <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <label for="description" class="text-xs font-semibold text-zinc-700">Keterangan</label>
        <input id="description" name="description" type="text" value="{{ $currentDescription }}" required
               placeholder="Contoh: Iuran Kas Kelas X-1"
               class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('description') border-orange-400 @enderror">
        @error('description')
            <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <label for="notes" class="text-xs font-semibold text-zinc-700">Catatan <span class="font-normal text-zinc-400">(opsional)</span></label>
        <textarea id="notes" name="notes" rows="3" placeholder="Detail tambahan, nama penyetor, atau nomor nota..."
                  class="mt-2 w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 @error('notes') border-orange-400 @enderror">{{ $currentNotes }}</textarea>
        @error('notes')
            <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 sm:p-5">
        <p class="text-xs font-semibold text-zinc-700">Bukti Nota <span class="font-normal text-zinc-400">(opsional, maks. 2 MB)</span></p>

        <label for="receipt"
               class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-zinc-400 hover:bg-zinc-100">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-zinc-500 shadow-sm">
                <x-icon name="photo" class="h-5 w-5" />
            </span>
            <span class="mt-2 text-sm font-semibold text-zinc-700">Ambil atau pilih foto nota</span>
            <span class="mt-0.5 text-[11px] text-zinc-500">JPG, PNG, atau WEBP · maksimal 2 MB</span>
        </label>
        <input id="receipt" name="receipt" type="file" accept="image/*" class="hidden" @change="previewReceipt($event)">

        <div x-show="receiptPreview" x-cloak class="mt-3">
            <img :src="receiptPreview" alt="Pratinjau bukti nota" class="max-h-64 w-full rounded-2xl bg-zinc-100 object-contain">
        </div>

        @if ($isEdit && $transaction->receipt_path)
            <div class="mt-3 flex items-center gap-2 text-xs text-zinc-500">
                <x-icon name="receipt" class="h-4 w-4" />
                Bukti saat ini:
                <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="font-semibold text-zinc-700 underline decoration-zinc-300 underline-offset-2 hover:text-zinc-900">
                    Lihat nota
                </a>
            </div>
        @endif

        @error('receipt')
            <p class="mt-2 text-xs text-orange-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ $isEdit ? route('transactions.show', $transaction) : route('transactions.index') }}"
           class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2">
            <x-icon name="check" class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
        </button>
    </div>
</form>
