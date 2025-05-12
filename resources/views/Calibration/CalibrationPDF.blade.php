<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kalibrasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #213268;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #213268;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .status-scheduled {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-cancelled {
            background-color: #e2e3e5;
            color: #383d41;
        }
        /* Result Badge Styles */
        .result-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .result-pass {
            background-color: #d4edda;
            color: #155724;
        }
        .result-fail {
            background-color: #f8d7da;
            color: #721c24;
        }
        .result-unknown {
            background-color: #fff3cd;
            color: #856404;
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
            display: inline-block;
            width: 100px;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KALIBRASI</h1>
        <p>Dibuat pada: {{ $date_generated }}</p>
    </div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif
        <p><strong>Urutan:</strong> {{ $sort_order == 'desc' ? 'Terbaru Terlebih Dahulu' : 'Terlama Terlebih Dahulu' }}</p>
        @if(!empty($status))
        <p><strong>Filter Status:</strong> {{ ucfirst(str_replace('_', ' ', $status)) }}</p>
        @endif
    </div>

    <table class="striped">
        <thead>
            <tr>
                <th>Aset</th>
                <th>Kode Tugas</th>
                <th>Status</th>
                <th>Tanggal Perencanaan</th>
                <th>Tanggal Aktual</th>
                <th>Tanggal Berikutnya</th>
                <th>Sertifikat</th>
                <th>Hasil</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calibrations as $calibration)
                <tr>
                    <td>
                        <div>{{ $calibration['asset_name'] ?? '-' }}</div>
                        <div style="color: #666;">{{ $calibration['asset_code'] ?? '-' }}</div>
                    </td>
                    <td>{{ $calibration['task_code'] ?? '-' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $status = $calibration['status_calibration'] ?? '';

                            if ($status == 'scheduled') {
                                $statusClass = 'status-scheduled';
                                $statusText = 'Dijadwalkan';
                            } elseif ($status == 'in_progress') {
                                $statusClass = 'status-in-progress';
                                $statusText = 'Dalam Pengerjaan';
                            } elseif ($status == 'completed') {
                                $statusClass = 'status-completed';
                                $statusText = 'Selesai';
                            } elseif ($status == 'overdue') {
                                $statusClass = 'status-overdue';
                                $statusText = 'Terlambat';
                            } elseif ($status == 'cancelled') {
                                $statusClass = 'status-cancelled';
                                $statusText = 'Dibatalkan';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $statusText ?: 'Unknown')) }}
                        </span>
                    </td>
                    <td>
                        {{ isset($calibration['planning_calibration_date']) ? \Carbon\Carbon::parse($calibration['planning_calibration_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}
                    </td>
                    <td>
                        {{ isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'] ? \Carbon\Carbon::parse($calibration['actual_calibration_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}
                    </td>
                    <td>
                        {{ isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'] ? \Carbon\Carbon::parse($calibration['next_calibration_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}
                    </td>
                    <td>{{ $calibration['certificate_number'] ?? '-' }}</td>
                    <td>
                        @php
                            $resultClass = '';
                            $result = $calibration['calibration_result'] ?? '';
                            $resultText = '-';

                            if ($result == 'pass') {
                                $resultClass = 'result-pass';
                                $resultText = 'Lulus';
                            } elseif ($result == 'fail') {
                                $resultClass = 'result-fail';
                                $resultText = 'Gagal';
                            } elseif ($result == 'unknown') {
                                $resultClass = 'result-unknown';
                                $resultText = 'Tidak Ditemukan';
                            }
                        @endphp
                        @if($resultText != '-')
                        <span class="result-badge {{ $resultClass }}">
                            {{ $resultText }}
                        </span>
                        @else
                            {{ $resultText }}
                        @endif
                    </td>
                    <td>{{ isset($calibration['calibration_price']) ? number_format($calibration['calibration_price'], 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada kalibrasi yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Kalibrasi</p>
    </div>
</body>
</html>
