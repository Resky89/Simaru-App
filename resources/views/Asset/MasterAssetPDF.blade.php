<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Aset Master</title>
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
        .filters {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .filters strong {
            font-weight: bold;
            display: inline-block;
            width: 100px;
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
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            margin-right: 4px;
            margin-bottom: 2px;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-primary {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN ASET MASTER</div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif

        @if(!empty($assetType))
        <p><strong>Tipe Aset:</strong>
            @if($assetType == 'medical')
                Medis
            @elseif($assetType == 'non_medical')
                Non Medis
            @else
                {{ ucfirst(str_replace('_', ' ', $assetType)) }}
            @endif
        </p>
        @endif

        <p><strong>Urutan:</strong>
            @switch($sort)
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
                    {{ ucfirst(str_replace('_', ' ', $sort)) }}
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
                <th>Merek</th>
                <th>Properti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($masterAssets as $asset)
                <tr>
                    <td>{{ $asset['asset_master_code'] ?? 'N/A' }}</td>
                    <td>{{ $asset['asset_name'] ?? '-' }}</td>
                    <td>
                        @if(isset($asset['asset_type']))
                            @if(strtolower($asset['asset_type']) == 'medical')
                                Medis
                            @elseif(strtolower($asset['asset_type']) == 'non_medical')
                                Non Medis
                            @else
                                {{ $asset['asset_type'] }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $asset['subcategory_name'] ?? '-' }}</td>
                    <td>{{ $asset['brand_name'] ?? '-' }}</td>
                    <td>
                        @if(isset($asset['is_depreciable']) && $asset['is_depreciable'])
                            <span class="badge badge-info">Dapat Disusutkan</span>
                        @endif
                        @if(isset($asset['needs_calibration']) && $asset['needs_calibration'])
                            <span class="badge badge-primary">Perlu Kalibrasi</span>
                        @endif
                        @if((!isset($asset['is_depreciable']) || !$asset['is_depreciable']) &&
                            (!isset($asset['needs_calibration']) || !$asset['needs_calibration']))
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada aset master ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Aset Master</p>
    </div>
</body>
</html>
