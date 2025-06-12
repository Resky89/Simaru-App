<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keluhan & Perbaikan</title>
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: medium;
            text-align: center;
            min-width: 60px;
        }
        .status-new {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-in-progress {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-finished {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-approved {
            background-color: #DCFCE7;
            color: #166534;
        }
        .status-unknown {
            background-color: #F3F4F6;
            color: #4B5563;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN KELUHAN & PERBAIKAN</div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif

        <p><strong>Urutan:</strong>
            @switch($sort ?? 'default')
                @case('newest')
                    Terbaru
                    @break
                @case('oldest')
                    Terlama
                    @break
                @default
                    {{ ucfirst(str_replace('_', ' ', $sort ?? 'default')) }}
            @endswitch
        </p>

        @if(!empty($status))
        <p><strong>Filter Status:</strong>
            @switch($status)
                @case('new')
                    Baru
                    @break
                @case('in_progress')
                    Sedang Diproses
                    @break
                @case('finished')
                    Selesai
                    @break
                @case('approved')
                    Disetujui
                    @break
                @default
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
            @endswitch
        </p>
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
                        <div style="color: #666;">Kode: {{ $complaint['asset_code'] ?? '-' }}</div>
                    </td>
                    <td>{{ $complaint['description'] ?? '-' }}</td>
                    <td>
                        @php
                            $statusClass = 'status-unknown';
                            $status = $complaint['status'] ?? '';
                            $statusText = 'Tidak Diketahui';

                            if ($status == 'new') {
                                $statusClass = 'status-new';
                                $statusText = 'Baru';
                            } elseif ($status == 'in progress') {
                                $statusClass = 'status-in-progress';
                                $statusText = 'Sedang Diproses';
                            } elseif ($status == 'finished') {
                                $statusClass = 'status-finished';
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
                        @if(isset($complaint['finished_date']) && $complaint['finished_date'] && $complaint['finished_date'] != '-')
                            {{ \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') }}
                        @else
                            -
                        @endif
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
        <p>Sistem Monitoring Aset - Laporan Keluhan & Perbaikan RS UMMI</p>
    </div>
</body>
</html>
