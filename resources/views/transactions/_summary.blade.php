<div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
    <x-ui.stat-card
        label="Pemasukan (hasil filter)"
        :value="format_rupiah($totalIncome)"
        icon="arrow-down-left"
        tone="income"
        hint="Masuk"
        compact
        data-summary="income"
    />
    <x-ui.stat-card
        label="Pengeluaran (hasil filter)"
        :value="format_rupiah($totalExpense)"
        icon="arrow-up-right"
        tone="expense"
        hint="Keluar"
        compact
        data-summary="expense"
    />
    <div class="col-span-2 sm:col-span-1">
        <x-ui.stat-card
            label="Jumlah Transaksi"
            :value="number_format($transactions->total(), 0, ',', '.')"
            icon="receipt"
            tone="neutral"
            hint="Transaksi"
            compact
            data-summary="count"
        />
    </div>
</div>
