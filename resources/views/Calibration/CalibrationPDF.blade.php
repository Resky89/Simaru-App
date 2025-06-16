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
            display: inline-block;
            width: 100px;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            background-color: #e9ecef;
            color: #495057;
        }

        /* Calibration Status Badge Styles */
        .status-scheduled {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-in-progress {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-completed {
            background-color: #DCFCE7;
            color: #166534;
        }

        /* Result Badge Styles */
        .result-pass {
            background-color: #DCFCE7;
            color: #166534;
        }
        .result-fail {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .result-unknown {
            background-color: #FEF3C7;
            color: #92400E;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN KALIBRASI</div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif

        <p><strong>Urutan:</strong> {{ $sort_order == 'desc' ? 'Terbaru Terlebih Dahulu' : 'Terlama Terlebih Dahulu' }}</p>

        @if(!empty($status))
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $status)) }}</p>
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
                        <div><strong>{{ $calibration['asset_name'] ?? '-' }}</strong></div>
                        <div style="color: #666; font-size: 8px;">{{ $calibration['asset_code'] ?? '-' }}</div>
                    </td>
                    <td>{{ $calibration['task_code'] ?? '-' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $status = $calibration['status_calibration'] ?? '';

                            if ($status == 'scheduled') {
                                $statusClass = 'status-scheduled';
                                $statusText = 'DIJADWALKAN';
                            } elseif ($status == 'in_progress') {
                                $statusClass = 'status-in-progress';
                                $statusText = 'DALAM PENGERJAAN';
                            } elseif ($status == 'completed') {
                                $statusClass = 'status-completed';
                                $statusText = 'SELESAI';
                            } else {
                                $statusText = 'TIDAK DIKETAHUI';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td>
                        {{ isset($calibration['planning_calibration_date']) ? date('d M Y', strtotime($calibration['planning_calibration_date'])) : '-' }}
                    </td>
                    <td>
                        {{ isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'] ? date('d M Y', strtotime($calibration['actual_calibration_date'])) : '-' }}
                    </td>
                    <td>
                        {{ isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'] ? date('d M Y', strtotime($calibration['next_calibration_date'])) : '-' }}
                    </td>
                    <td>{{ $calibration['certificate_number'] ?? '-' }}</td>
                    <td>
                        @php
                            $resultClass = '';
                            $result = $calibration['calibration_result'] ?? '';
                            $resultText = '-';

                            if ($result == 'pass') {
                                $resultClass = 'result-pass';
                                $resultText = 'LULUS';
                            } elseif ($result == 'fail') {
                                $resultClass = 'result-fail';
                                $resultText = 'GAGAL';
                            } elseif ($result == 'unknown') {
                                $resultClass = 'result-unknown';
                                $resultText = 'TIDAK DITEMUKAN';
                            }
                        @endphp
                        @if($resultText != '-')
                        <span class="status-badge {{ $resultClass }}">
                            {{ $resultText }}
                        </span>
                        @else
                            {{ $resultText }}
                        @endif
                    </td>
                    <td>{{ isset($calibration['calibration_price']) ? 'Rp ' . number_format($calibration['calibration_price'], 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada kalibrasi yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Kalibrasi RS UMMI</p>
    </div>
</body>
</html>
