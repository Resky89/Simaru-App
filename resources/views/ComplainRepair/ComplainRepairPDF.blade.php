<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keluhan & Perbaikan</title>
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
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
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
        <h1>LAPORAN KELUHAN & PERBAIKAN</h1>
        <p>Dibuat pada: {{ \Carbon\Carbon::parse($date_generated)->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
    </div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif
        <p><strong>Urutan Sortir:</strong> {{ ucfirst($sort) }}</p>
        @if(!empty($status))
        <p><strong>Filter Status:</strong> {{ ucfirst(str_replace('_', ' ', $status)) }}</p>
        @endif
    </div>

    <table class="striped">
        <thead>
            <tr>
                <th>Aset</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Tanggal Keluhan</th>
                <th>Tanggal Selesai</th>
                <th>Pelapor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $complaint)
                <tr>
                    <td>
                        <div>{{ $complaint['asset_name'] ?? '-' }}</div>
                        <div style="color: #666;">ID: {{ $complaint['asset_id'] ?? '-' }}</div>
                    </td>
                    <td>{{ $complaint['description'] ?? '-' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $status = $complaint['status'] ?? '';
                            $statusText = 'Tidak Diketahui';

                            if ($status == 'new') {
                                $statusClass = 'status-pending';
                                $statusText = 'Baru';
                            } elseif ($status == 'in progress') {
                                $statusClass = 'status-in-progress';
                                $statusText = 'Sedang Diproses';
                            } elseif ($status == 'finished') {
                                $statusClass = 'status-completed';
                                $statusText = 'Selesai';
                            } elseif ($status == 'approved') {
                                $statusClass = 'status-approved';
                                $statusText = 'Disetujui';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td>
                        {{ isset($complaint['complaint_date']) ? \Carbon\Carbon::parse($complaint['complaint_date'])->locale('id')->isoFormat('DD MMMM YYYY') : '-' }}
                    </td>
                    <td>
                        {{ isset($complaint['finished_date']) && $complaint['finished_date'] ? \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') : '-' }}
                    </td>
                    <td>
                        ID: {{ $complaint['reporter_number'] ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada keluhan ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Keluhan & Perbaikan</p>
    </div>
</body>
</html>
