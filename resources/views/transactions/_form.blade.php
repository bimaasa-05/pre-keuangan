@php
    $isEdit = isset($transaction);
    $currentType = old('type', $isEdit ? $transaction->type : 'income');
    $currentCategory = old('category_id', $isEdit ? $transaction->category_id : '');
    $currentAmount = old('amount', $isEdit ? $transaction->amount : '');
    $currentDate = old('transaction_date', $isEdit ? $transaction->transaction_date->toDateString() : now()->toDateString());
    $currentDescription = old('description', $isEdit ? $transaction->description : '');
    $currentNotes = old('notes', $isEdit ? $transaction->notes : '');
    $incomeCategoryOptions = [['value' => '', 'label' => 'Pilih kategori pemasukan']];
    $expenseCategoryOptions = [['value' => '', 'label' => 'Pilih kategori pengeluaran']];

    foreach ($categories->where('type', 'income') as $category) {
        $incomeCategoryOptions[] = ['value' => (string) $category->id, 'label' => $category->name];
    }

    foreach ($categories->where('type', 'expense') as $category) {
        $expenseCategoryOptions[] = ['value' => (string) $category->id, 'label' => $category->name];
    }
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
          setType(nextType) {
              if (this.type === nextType) return;
              this.type = nextType;
              this.$dispatch('reset-category');
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

    <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Tipe Transaksi</p>
        <div class="mt-2 grid grid-cols-2 gap-1.5 rounded-2xl bg-zinc-100 p-1.5 dark:bg-zinc-800">
            <button type="button" @click="setType('income')"
                    :class="type === 'income' ? 'bg-emerald-600 text-white shadow-sm dark:bg-emerald-500 dark:text-zinc-950' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <x-icon name="arrow-down-left" class="h-4 w-4" />
                Pemasukan
            </button>
            <button type="button" @click="setType('expense')"
                    :class="type === 'expense' ? 'bg-rose-600 text-white shadow-sm dark:bg-rose-500 dark:text-zinc-950' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <x-icon name="arrow-up-right" class="h-4 w-4" />
                Pengeluaran
            </button>
        </div>
        @error('type')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <label for="amount_display" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Nominal</label>
        <div class="mt-2 flex items-center gap-2 border-b border-zinc-200 pb-3 dark:border-zinc-700">
            <span class="font-display text-2xl font-bold text-zinc-400 dark:text-zinc-500">Rp</span>
            <input id="amount_display" type="text" inputmode="numeric" autocomplete="off"
                   :value="amountDisplay" @input="onAmountInput($event)" placeholder="0"
                   class="w-full border-0 bg-transparent p-0 font-display text-3xl font-bold tabular-nums tracking-tight text-zinc-900 placeholder:text-zinc-300 focus:ring-0 dark:text-zinc-100 dark:placeholder:text-zinc-600">
            <input type="hidden" name="amount" value="{{ $currentAmount }}" x-model="amountRaw">
        </div>
        @error('amount')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <label for="category_income" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Kategori</label>
            <div class="mt-2">
                <x-ui.select name="category_id" id="category_income" :options="$incomeCategoryOptions"
                             :selected="$currentType === 'income' ? $currentCategory : ''"
                             placeholder="Pilih kategori pemasukan"
                             disabled-expr="type !== 'income'"
                             x-show="type === 'income'" x-cloak />
                <x-ui.select name="category_id" id="category_expense" :options="$expenseCategoryOptions"
                             :selected="$currentType === 'expense' ? $currentCategory : ''"
                             placeholder="Pilih kategori pengeluaran"
                             disabled-expr="type !== 'expense'"
                             x-show="type === 'expense'" x-cloak />
            </div>
            @error('category_id')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
            <label for="transaction_date" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Tanggal</label>
            <div class="mt-2">
                <x-ui.date-picker name="transaction_date" :value="$currentDate" id="transaction_date" placeholder="Pilih tanggal transaksi" required />
            </div>
            @error('transaction_date')
                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <label for="description" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Keterangan</label>
        <input id="description" name="description" type="text" value="{{ $currentDescription }}" required
               placeholder="Contoh: Iuran Kas Kelas X-1"
               class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 @error('description') border-rose-400 @enderror">
        @error('description')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <label for="notes" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Catatan <span class="font-normal text-zinc-400">(opsional)</span></label>
        <textarea id="notes" name="notes" rows="3" placeholder="Detail tambahan, nama penyetor, atau nomor nota..."
                  class="mt-2 w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-lime-400 dark:focus:bg-zinc-900 dark:focus:ring-lime-400/20 @error('notes') border-rose-400 @enderror">{{ $currentNotes }}</textarea>
        @error('notes')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm shadow-zinc-900/5 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/20">
        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Bukti Nota <span class="font-normal text-zinc-400">(opsional, maks. 2 MB)</span></p>

        <label for="receipt"
               class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-zinc-400 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:border-zinc-600 dark:hover:bg-zinc-800">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-zinc-500 shadow-sm dark:bg-zinc-900 dark:text-zinc-400">
                <x-icon name="photo" class="h-5 w-5" />
            </span>
            <span class="mt-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Ambil atau pilih foto nota</span>
            <span class="mt-0.5 text-[11px] text-zinc-500 dark:text-zinc-400">JPG, PNG, atau WEBP · maksimal 2 MB</span>
        </label>
        <input id="receipt" name="receipt" type="file" accept="image/*" class="hidden" @change="previewReceipt($event)">

        <div x-show="receiptPreview" x-cloak class="mt-3">
            <img :src="receiptPreview" alt="Pratinjau bukti nota" class="max-h-64 w-full rounded-2xl bg-zinc-100 object-contain dark:bg-zinc-800">
        </div>

        @if ($isEdit && $transaction->receipt_path)
            <div class="mt-3 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <x-icon name="receipt" class="h-4 w-4" />
                Bukti saat ini:
                <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="font-semibold text-zinc-700 underline decoration-zinc-300 underline-offset-2 hover:text-zinc-900 dark:text-zinc-200 dark:decoration-zinc-600 dark:hover:text-white">
                    Lihat nota
                </a>
            </div>
        @endif

        @error('receipt')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ $isEdit ? route('transactions.show', $transaction) : route('transactions.index') }}"
           class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 focus-visible:ring-offset-2 dark:bg-lime-400 dark:text-zinc-950 dark:hover:bg-lime-300 dark:focus-visible:ring-lime-400 dark:focus-visible:ring-offset-zinc-950">
            <x-icon name="check" class="h-4 w-4" />
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
        </button>
    </div>
</form>
