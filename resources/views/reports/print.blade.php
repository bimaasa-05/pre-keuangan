<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Kas {{ $periodLabel }} · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .print-sheet {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-zinc-100 font-sans text-zinc-900">
    <div class="no-print sticky top-0 z-20 border-b border-zinc-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-[210mm] items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('reports.index', request()->only(['mode', 'month', 'year', 'date_from', 'date_to'])) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-600 hover:bg-zinc-100">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white hover:bg-zinc-800">
                <x-icon name="printer" class="h-4 w-4" />
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <main class="mx-auto my-6 max-w-[210mm] bg-white p-8 shadow-sm print-sheet">
        <header class="flex items-start justify-between gap-4 border-b-2 border-zinc-900 pb-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('icons/icon-mark.png') }}" alt="KasK1ta" class="h-12 w-12 rounded-xl">
                <div>
                    <p class="font-display text-xl font-bold tracking-tight">KasK1ta</p>
                    <p class="text-[11px] text-zinc-500">Sistem Pencatatan Kas Sekolah</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[11px] uppercase tracking-[0.2em] text-zinc-500">Laporan Kas Sekolah</p>
                <p class="mt-1 font-display text-base font-bold">{{ $periodLabel }}</p>
                <p class="mt-0.5 text-[10px] text-zinc-500">Dicetak {{ now()->translatedFormat('d F Y, H:i') }}</p>
            </div>
        </header>

        <section class="mt-4 grid grid-cols-4 gap-2 text-center">
            <div class="rounded-lg border border-zinc-300 px-2 py-2">
                <p class="text-[9px] uppercase tracking-wider text-zinc-500">Saldo Awal</p>
                <p class="mt-1 text-xs font-bold tabular-nums">{{ format_rupiah($openingBalance) }}</p>
            </div>
            <div class="rounded-lg border border-zinc-300 px-2 py-2">
                <p class="text-[9px] uppercase tracking-wider text-zinc-500">Pemasukan</p>
                <p class="mt-1 text-xs font-bold tabular-nums text-teal-700">{{ format_rupiah($totalIncome) }}</p>
            </div>
            <div class="rounded-lg border border-zinc-300 px-2 py-2">
                <p class="text-[9px] uppercase tracking-wider text-zinc-500">Pengeluaran</p>
                <p class="mt-1 text-xs font-bold tabular-nums text-orange-700">{{ format_rupiah($totalExpense) }}</p>
            </div>
            <div class="rounded-lg border border-zinc-900 bg-zinc-900 px-2 py-2 text-white">
                <p class="text-[9px] uppercase tracking-wider text-zinc-400">Saldo Akhir</p>
                <p class="mt-1 text-xs font-bold tabular-nums text-lime-300">{{ format_rupiah($closingBalance) }}</p>
            </div>
        </section>

        <table class="mt-4 w-full border-collapse text-[10px]">
            <thead>
                <tr class="bg-zinc-100 text-left">
                    <th class="border border-zinc-300 px-2 py-1.5 text-center">No</th>
                    <th class="border border-zinc-300 px-2 py-1.5 whitespace-nowrap">Tanggal</th>
                    <th class="border border-zinc-300 px-2 py-1.5">Uraian</th>
                    <th class="border border-zinc-300 px-2 py-1.5">Kategori</th>
                    <th class="border border-zinc-300 px-2 py-1.5 text-right whitespace-nowrap">Masuk</th>
                    <th class="border border-zinc-300 px-2 py-1.5 text-right whitespace-nowrap">Keluar</th>
                    <th class="border border-zinc-300 px-2 py-1.5 text-right whitespace-nowrap">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $running = $openingBalance; @endphp
                @forelse ($transactions as $index => $transaction)
                    @php $running += $transaction->signed_amount; @endphp
                    <tr class="align-top">
                        <td class="border border-zinc-300 px-2 py-1.5 text-center">{{ $index + 1 }}</td>
                        <td class="border border-zinc-300 px-2 py-1.5 whitespace-nowrap">{{ $transaction->transaction_date->translatedFormat('d/m/Y') }}</td>
                        <td class="border border-zinc-300 px-2 py-1.5">
                            {{ $transaction->description }}
                            @if ($transaction->notes)
                                <span class="block text-[9px] text-zinc-500">{{ $transaction->notes }}</span>
                            @endif
                        </td>
                        <td class="border border-zinc-300 px-2 py-1.5">{{ $transaction->category->name }}</td>
                        <td class="border border-zinc-300 px-2 py-1.5 text-right tabular-nums whitespace-nowrap">
                            {{ $transaction->type === 'income' ? format_rupiah($transaction->amount) : '—' }}
                        </td>
                        <td class="border border-zinc-300 px-2 py-1.5 text-right tabular-nums whitespace-nowrap">
                            {{ $transaction->type === 'expense' ? format_rupiah($transaction->amount) : '—' }}
                        </td>
                        <td class="border border-zinc-300 px-2 py-1.5 text-right font-semibold tabular-nums whitespace-nowrap">{{ format_rupiah($running) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-zinc-300 px-2 py-8 text-center text-zinc-500">Belum ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-zinc-100 font-bold">
                    <td colspan="4" class="border border-zinc-300 px-2 py-1.5 text-right uppercase tracking-wider">Total</td>
                    <td class="border border-zinc-300 px-2 py-1.5 text-right tabular-nums whitespace-nowrap">{{ format_rupiah($totalIncome) }}</td>
                    <td class="border border-zinc-300 px-2 py-1.5 text-right tabular-nums whitespace-nowrap">{{ format_rupiah($totalExpense) }}</td>
                    <td class="border border-zinc-300 px-2 py-1.5 text-right tabular-nums whitespace-nowrap">{{ format_rupiah($closingBalance) }}</td>
                </tr>
            </tfoot>
        </table>

        <section class="mt-10 grid grid-cols-2 gap-10 text-center text-[11px]">
            <div>
                <p>Mengetahui,</p>
                <p class="font-semibold">Kepala Sekolah</p>
                <div class="h-20"></div>
                <p class="border-t border-zinc-400 pt-1">Nama: ........................................</p>
                <p class="mt-0.5 text-[9px] text-zinc-500">NIP: ........................................</p>
            </div>
            <div>
                <p>{{ $end->translatedFormat('d F Y') }}</p>
                <p class="font-semibold">Bendahara Sekolah</p>
                <div class="h-20"></div>
                <p class="border-t border-zinc-400 pt-1">Nama: ........................................</p>
                <p class="mt-0.5 text-[9px] text-zinc-500">NIP: ........................................</p>
            </div>
        </section>

        <footer class="mt-6 border-t border-zinc-200 pt-2 text-[9px] text-zinc-400">
            Dokumen ini dicetak dari {{ config('app.name') }} · {{ $periodLabel }}
        </footer>
    </main>
</body>
</html>
