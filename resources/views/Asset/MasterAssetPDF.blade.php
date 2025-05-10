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
        .asset-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ASET MASTER</h1>
        <p>Dibuat pada: {{ $date_generated }}</p>
    </div>

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

        @if(!empty($brandId) && isset($brandMap[$brandId]))
        <p><strong>Merek:</strong> {{ $brandMap[$brandId]['brand_name'] ?? 'Tidak Diketahui' }}</p>
        @endif

        @if(!empty($subcategoryId) && isset($subcategoryMap[$subcategoryId]))
        <p><strong>Kategori:</strong> {{ $subcategoryMap[$subcategoryId]['subcategory_name'] ?? 'Tidak Diketahui' }}</p>
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
                <th>Merek</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Properti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($masterAssets as $asset)
                <tr>
                    <td>{{ $asset['asset_master_code'] ?? '-' }}</td>
                    <td>{{ $asset['asset_name'] ?? '-' }}</td>
                    <td>
                        @if(isset($asset['brand_id']) && isset($brandMap[$asset['brand_id']]))
                            {{ $brandMap[$asset['brand_id']]['brand_name'] ?? '-' }}
                        @else
                            {{ $asset['brand_name'] ?? '-' }}
                        @endif
                    </td>
                    <td>
                        @if(isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']]))
                            {{ $subcategoryMap[$asset['subcategory_id']]['subcategory_name'] ?? '-' }}
                        @else
                            {{ $asset['subcategory_name'] ?? '-' }}
                        @endif
                    </td>
                    <td>
                        <span class="asset-type">
                            @if(isset($asset['asset_type']))
                                @if($asset['asset_type'] == 'medical')
                                    Medis
                                @elseif($asset['asset_type'] == 'non_medical')
                                    Non Medis
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $asset['asset_type'])) }}
                                @endif
                            @elseif(isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']]['asset_type']))
                                @if($subcategoryMap[$asset['subcategory_id']]['asset_type'] == 'medical')
                                    Medis
                                @elseif($subcategoryMap[$asset['subcategory_id']]['asset_type'] == 'non_medical')
                                    Non Medis
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $subcategoryMap[$asset['subcategory_id']]['asset_type'])) }}
                                @endif
                            @else
                                -
                            @endif
                        </span>
                    </td>
                    <td>
                        @if(isset($asset['is_depreciable']) || isset($asset['needs_calibration']))
                            @if(isset($asset['is_depreciable']) && $asset['is_depreciable'])
                                <div>Dapat Disusutkan</div>
                            @endif
                            @if(isset($asset['needs_calibration']) && $asset['needs_calibration'])
                                <div>Perlu Kalibrasi</div>
                            @endif
                        @else
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
