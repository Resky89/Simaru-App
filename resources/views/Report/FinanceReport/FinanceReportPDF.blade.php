<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #213268;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 18pt;
            margin: 5px 0;
            color: #213268;
        }
        h2 {
            font-size: 14pt;
            margin: 8px 0;
            color: #213268;
        }
        .sub-header {
            font-size: 11pt;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #EEF1F4;
        }
        th {
            background-color: #213268;
            padding: 8px;
            font-weight: bold;
            text-align: left;
            color: white;
        }
        td {
            padding: 6px 8px;
            font-size: 9pt;
        }
        .footer {
            text-align: center;
            font-size: 9pt;
            margin-top: 20px;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .income {
            color: green;
        }
        .expense {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <div class="sub-header">
            <strong>Dibuat pada:</strong> {{ date('d M Y') }}
            @if(!empty($search))
                <br><strong>Filter Pencarian:</strong> {{ $search }}
            @endif
            <br><strong>Urutan Sortir:</strong> {{ ucfirst($sort) }}
        </div>
    </div>

    <div class="summary">
        <h2>Ringkasan Transaksi Aset</h2>
        <p>Total transaksi: {{ count($transactions) }}</p>
    </div>

    @if(count($transactions) > 0)
        <table class="striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Aset</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalIncome = 0;
                    $totalExpense = 0;
                @endphp
                @foreach($transactions as $index => $transaction)
                    @php
                        $amountClass = ($transaction['type'] ?? '') == 'income' ? 'income' : 'expense'; // tetap menggunakan 'income' dan 'expense' untuk class CSS
                        if(($transaction['type'] ?? '') == 'income') {
                            $totalIncome += $transaction['amount'] ?? 0;
                        } else {
                            $totalExpense += $transaction['amount'] ?? 0;
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $transaction['asset_name'] ?? 'N/A' }}<br>
                            <small>ID: {{ $transaction['asset_id'] ?? 'N/A' }}</small>
                        </td>
                        <td>{{ ucfirst($transaction['type'] ?? 'N/A') == 'Income' ? 'Pendapatan' : (ucfirst($transaction['type'] ?? 'N/A') == 'Expense' ? 'Pengeluaran' : ucfirst($transaction['type'] ?? 'N/A')) }}</td>
                        <td class="text-right {{ $amountClass }}">Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ isset($transaction['transaction_date']) ? date('d M Y', strtotime($transaction['transaction_date'])) : 'N/A' }}</td>
                        <td>{{ $transaction['description'] ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><strong>Total Pendapatan</strong></td>
                    <td class="text-right income"><strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Total Pengeluaran</strong></td>
                    <td class="text-right expense"><strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Saldo Bersih</strong></td>
                    <td class="text-right {{ ($totalIncome - $totalExpense) >= 0 ? 'income' : 'expense' }}"><strong>Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data transaksi untuk laporan ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis dari Sistem Monitoring Aset.</p>
        <p>© {{ date('Y') }} Sistem Monitoring Aset</p>
    </div>
</body>
</html>
