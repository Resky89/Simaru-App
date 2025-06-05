<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemeliharaan</title>
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
            padding: 8px;
            font-size: 11px;
        }
        td {
            border-top: 1px solid #eef1f4;
            padding: 8px;
            font-size: 10px;
            vertical-align: top;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .filters {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .filters strong {
            font-weight: bold;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
        }
        .status-new {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-in_progress {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-finished {
            background-color: #DCFCE7;
            color: #166534;
        }
        .status-canceled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .asset-name {
            font-weight: bold;
        }
        .asset-code {
            color: #666;
            font-size: 9px;
        }
        .interval-label {
            font-size: 10px;
        }
    </style>
</head>
<body>
    @php
        function translateMonth($date) {
            if (!$date) return 'N/A';

            $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            // Also handle abbreviated month names
            $englishAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $indonesianAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            $formattedDate = date('d F Y', strtotime($date));

            // Replace full month names
            foreach ($englishMonths as $index => $month) {
                $formattedDate = str_replace($month, $indonesianMonths[$index], $formattedDate);
            }

            // Replace abbreviated month names
            foreach ($englishAbbr as $index => $month) {
                $formattedDate = str_replace($month, $indonesianAbbr[$index], $formattedDate);
            }

            return $formattedDate;
        }
    @endphp

    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN JADWAL PEMELIHARAAN</div>

    <div class="filters">
        @if(!empty($search))
            <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif

        @if(!empty($status))
            <p><strong>Status:</strong>
            @switch($status)
                @case('new')
                    Baru
                    @break
                @case('in_progress')
                    Dalam Proses
                    @break
                @case('finished')
                    Selesai
                    @break
                @default
                    {{ ucfirst($status) }}
            @endswitch
            </p>
        @endif

        <p><strong>Urutan:</strong>
            @if(isset($sort_by) && isset($sort_order))
                @if($sort_by == 'created_at' && $sort_order == 'desc')
                    Terbaru
                @elseif($sort_by == 'created_at' && $sort_order == 'asc')
                    Terlama
                @else
                    {{ $sort_by }} ({{ $sort_order == 'asc' ? 'naik' : 'turun' }})
                @endif
            @else
                Terbaru
            @endif
        </p>

        <p><strong>Tanggal Laporan:</strong> {{ translateMonth(date('Y-m-d')) }}</p>
    </div>

    <table class="striped">
        <thead>
            <tr>
                <th>Aset</th>
                <th>Interval</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Ditugaskan Kepada</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $maintenance)
                <tr>
                    <td>
                        <div class="asset-name">{{ $maintenance['asset_name'] ?? '-' }}</div>
                        <div class="asset-code">Kode: {{ $maintenance['asset_code'] ?? '-' }}</div>
                    </td>
                    <td>
                        @php
                            $intervalText = '-';
                            $interval = $maintenance['interval'] ?? '';
                            if ($interval === 'ONCE') {
                                $intervalText = 'Sekali';
                            } elseif ($interval === 'DAILY') {
                                $intervalText = 'Harian';
                            } elseif ($interval === 'WEEKLY') {
                                $intervalText = 'Mingguan';
                            } elseif ($interval === '2 WEEKS') {
                                $intervalText = '2 Minggu';
                            } elseif ($interval === 'MONTHLY') {
                                $intervalText = 'Bulanan';
                            } elseif ($interval === '2 MONTHS') {
                                $intervalText = '2 Bulan';
                            } elseif ($interval === '3 MONTHS') {
                                $intervalText = '3 Bulan';
                            } elseif ($interval === '4 MONTHS') {
                                $intervalText = '4 Bulan';
                            } elseif ($interval === '6 MONTHS') {
                                $intervalText = '6 Bulan';
                            } elseif ($interval === 'YEARLY') {
                                $intervalText = 'Tahunan';
                            }
                        @endphp
                        {{ $intervalText }}
                    </td>
                    <td>{{ isset($maintenance['start_date']) ? translateMonth($maintenance['start_date']) : '-' }}</td>
                    <td>{{ isset($maintenance['end_date']) ? translateMonth($maintenance['end_date']) : '-' }}</td>
                    <td>{{ $maintenance['assigned_to_employee_number'] ?? $maintenance['assigned_to'] ?? '-' }}</td>
                    <td>{{ $maintenance['vendor_name'] ?? '-' }}</td>
                    <td style="text-align: center;">
                        @php
                            $statusClass = '';
                            $status = $maintenance['status'] ?? '';
                            $statusText = 'Tidak Diketahui';

                            if ($status == 'new') {
                                $statusClass = 'status-new';
                                $statusText = 'Baru';
                            } elseif ($status == 'in_progress') {
                                $statusClass = 'status-in_progress';
                                $statusText = 'Dalam Proses';
                            } elseif ($status == 'finished') {
                                $statusClass = 'status-finished';
                                $statusText = 'Selesai';
                            } elseif ($status == 'canceled') {
                                $statusClass = 'status-canceled';
                                $statusText = 'Dibatalkan';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td>{{ isset($maintenance['created_at']) ? translateMonth($maintenance['created_at']) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ditemukan jadwal pemeliharaan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Jadwal Pemeliharaan RS UMMI</p>
    </div>
</body>
</html>
