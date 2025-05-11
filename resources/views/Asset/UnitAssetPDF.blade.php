<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Aset Unit</title>
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
            background-color: #e9ecef;
            color: #495057;
        }
        .available {
            background-color: #659B09;
            color: white;
        }
        .check-out {
            background-color: #F59E0B;
            color: white;
        }
        .lost {
            background-color: #EF4444;
            color: white;
        }
        .dispose {
            background-color: #ACC3EF;
            color: white;
        }
        .under-repair {
            background-color: #6B7280;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ASSET UNIT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Search:</strong> {{ $search }}</p>
        @endif

        @if(!empty($typeFilter))
        <p><strong>Tipe Aset:</strong> {{ ucfirst(str_replace('_', ' ', $typeFilter)) }}</p>
        @endif

        @if(!empty($statusFilter))
        <p><strong>Status:</strong> {{ ucfirst($statusFilter) }}</p>
        @endif

        <p><strong>Urutan Pengurutan:</strong>
            @switch($sortOrder)
                @case('newest')
                    Terbaru
                    @break
                @case('oldest')
                    Terlama
                    @break
                @case('name_asc')
                    Nama (A-Z)
                    @break
                @case('name_desc')
                    Nama (Z-A)
                    @break
                @default
                    {{ ucfirst(str_replace('_', ' ', $sortOrder)) }}
            @endswitch
        </p>
    </div>

    <table class="striped">
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Nomor Seri</th>
                <th>Tanggal Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr>
                    <td>{{ $asset['asset_code'] ?? '-' }}</td>
                    <td>{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '-' }}</td>
                    <td>
                        @if(isset($asset['asset_master']) && isset($asset['asset_master']['asset_master_code']))
                            @php
                                $code = $asset['asset_master']['asset_master_code'];
                                $assetType = 'Non Medis';
                                if (strpos($code, 'MED-') === 0) {
                                    $assetType = 'Medis';
                                } elseif (strpos($code, 'NMED-') === 0) {
                                    $assetType = 'Non Medis';
                                }
                            @endphp
                            {{ $assetType }}
                        @else
                            Non Medis
                        @endif
                    </td>
                    <td>
                        {{ $asset['asset_master']['subcategory_name'] ?? '-' }}
                    </td>
                    <td>
                        @if(isset($asset['room']) && isset($asset['room']['room_name']))
                            {{ $asset['room']['room_name'] }}
                            @if(isset($asset['room']['building']) && isset($asset['room']['building']['building_name']))
                                ({{ $asset['room']['building']['building_name'] }})
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @php
                            $statusText = 'UNKNOWN';
                            $statusClass = '';

                            if(isset($asset['current_status'])) {
                                switch(strtolower($asset['current_status'])) {
                                    case 'available':
                                        $statusText = 'TERSEDIA';
                                        $statusClass = 'available';
                                        break;
                                    case 'check out':
                                        $statusText = 'DIPINJAM';
                                        $statusClass = 'check-out';
                                        break;
                                    case 'lost':
                                        $statusText = 'HILANG';
                                        $statusClass = 'lost';
                                        break;
                                    case 'dispose':
                                        $statusText = 'DIHAPUSKAN';
                                        $statusClass = 'dispose';
                                        break;
                                    case 'under repair':
                                        $statusText = 'PERBAIKAN';
                                        $statusClass = 'under-repair';
                                        break;
                                    default:
                                        $statusText = strtoupper($asset['current_status']);
                                }
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td>{{ $asset['serial_number'] ?? '-' }}</td>
                    <td>{{ $asset['purchase_date'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada aset yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Pengawasan Aset - Laporan Aset Unit</p>
    </div>
</body>
</html>
