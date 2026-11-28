<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-ui.stat-card label="Saldo Awal" :value="format_rupiah($openingBalance)" icon="wallet" tone="neutral" compact />
    <x-ui.stat-card label="Total Pemasukan" :value="format_rupiah($totalIncome)" icon="arrow-down-left" tone="income" compact />
    <x-ui.stat-card label="Total Pengeluaran" :value="format_rupiah($totalExpense)" icon="arrow-up-right" tone="expense" compact />
    <x-ui.stat-card label="Saldo Akhir" :value="format_rupiah($closingBalance)" icon="banknotes" tone="balance" compact />
</div>
