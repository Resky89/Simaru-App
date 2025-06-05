<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: left;
            margin-bottom: 10px;
        }
        .header img {
            max-width: 100%;
            height: auto;
            max-height: 50px;
        }
        .header-line {
            border-bottom: 2px solid #213268;
            margin-top: 3px;
            margin-bottom: 15px;
            clear: both;
        }
        .page-title {
            color: #213268;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #213268;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #EEF1F4;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #213268;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            font-size: 11px;
            border: 1px solid #213268;
        }
        td {
            border: 1px solid #EEF1F4;
            padding: 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .summary-box {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-weight: bold;
            color: #213268;
            margin-bottom: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            text-align: right;
        }
        .income {
            color: #16A34A;
        }
        .expense {
            color: #DC2626;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }
        .small-text {
            font-size: 8px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: normal;
            text-transform: uppercase;
        }
        .badge-income {
            background-color: #DCFCE7;
            color: #15803D;
        }
        .badge-expense {
            background-color: #FEE2E2;
            color: #B91C1C;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN KEUANGAN ASET</div>
    <div class="subtitle">
        Data transaksi keuangan aset
            @if(!empty($search))
        | Filter: {{ $search }}
            @endif
        | Urutan: {{ ucfirst($sort) }}
    </div>

    <div class="summary-box">
        <div class="summary-title">Ringkasan Transaksi</div>
        <p>Total transaksi: {{ count($transactions) }}</p>

        @if(count($transactions) > 0)
        @php
            $totalIncome = 0;
            $totalExpense = 0;
            foreach($transactions as $transaction) {
                if(($transaction['type'] ?? '') == 'income') {
                    $totalIncome += $transaction['amount'] ?? 0;
                } else {
                    $totalExpense += $transaction['amount'] ?? 0;
                }
            }
            $balance = $totalIncome - $totalExpense;
        @endphp

        <div class="summary-row">
            <div class="summary-label">Total Pendapatan:</div>
            <div class="summary-value income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Pengeluaran:</div>
            <div class="summary-value expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Saldo:</div>
            <div class="summary-value {{ $balance >= 0 ? 'income' : 'expense' }}">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
        @endif
    </div>

    <h2>Detil Transaksi</h2>
    @if(count($transactions) > 0)
        <table class="striped">
            <thead>
                <tr>
                <th width="5%">No</th>
                <th width="20%">Aset</th>
                <th width="10%">Tipe</th>
                <th width="15%">Jumlah</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $transaction['asset_name'] ?? 'N/A' }}</strong>
                    <div class="small-text">ID: {{ $transaction['asset_id'] ?? 'N/A' }}</div>
                </td>
                <td>
                    @if(($transaction['type'] ?? '') == 'income')
                    <span class="badge badge-income">Pendapatan</span>
                    @else
                    <span class="badge badge-expense">Pengeluaran</span>
                    @endif
                </td>
                <td class="text-right">
                    <strong class="{{ ($transaction['type'] ?? '') == 'income' ? 'income' : 'expense' }}">
                        Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                    </strong>
                </td>
                <td>
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];

                        if(isset($transaction['transaction_date'])) {
                            $date = new DateTime($transaction['transaction_date']);
                            $day = $date->format('d');
                            $month = $months[(int)$date->format('m')];
                            $year = $date->format('Y');
                            $formattedDate = "$day $month $year";
                            echo $formattedDate;
                        } else {
                            echo 'N/A';
                        }
                    @endphp
                        </td>
                        <td>{{ $transaction['description'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data transaksi untuk laporan ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dibuat pada:
            @php
                $now = new DateTime();
                $day = $now->format('d');
                $month = $months[(int)$now->format('m')];
                $year = $now->format('Y');
                echo "$day $month $year";
            @endphp
        </p>
        <p>Sistem Monitoring Aset - Laporan Keuangan Aset RS UMMI</p>
    </div>
</body>
</html>
