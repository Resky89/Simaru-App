<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Laporan Aset Master</title>
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
        .asset-info {
            margin-bottom: 20px;
            clear: both;
        }
        .asset-info h2 {
            font-size: 14px;
            color: #213268;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
        }
        .info-value {
            flex: 1;
        }
        .image-container {
            height: 180px;
            text-align: center;
            border: 1px solid #eee;
            background-color: white;
            vertical-align: middle;
            margin: 10px 0;
            padding: 10px;
        }
        .image-container img {
            max-width: 85%;
            max-height: 160px;
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
        }
        td {
            border-top: 1px solid #eef1f4;
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: normal;
            text-transform: uppercase;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-purple {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        .status-available {
            background-color: #659B09;
            color: white;
        }
        .status-in-use, .status-check-out {
            background-color: #F59E0B;
            color: white;
        }
        .status-maintenance, .status-under-repair {
            background-color: #25B1FF;
            color: white;
        }
        .status-broken {
            background-color: #ffe5d0;
            color: #ad4e00;
        }
        .status-dispose {
            background-color: #ACC3EF;
            color: white;
        }
        .status-lost {
            background-color: #EF4444;
            color: white;
        }
        .condition-good {
            background-color: #d4edda;
            color: #155724;
        }
        .condition-fair, .condition-slightly-damage {
            background-color: #fff3cd;
            color: #856404;
        }
        .condition-poor {
            background-color: #ffe5d0;
            color: #ad4e00;
        }
        .condition-high-damage {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETAIL ASET MASTER</div>

    <!-- Master Asset Image Section -->
    <div class="asset-info">
        <h2>Gambar Aset Master</h2>
        <div class="image-container">
            @if(!empty($masterAsset['image_base64']))
                <img src="data:image/jpeg;base64,{{ $masterAsset['image_base64'] }}" alt="Master Asset Image">
            @elseif(isset($masterAsset['reference_image_path']) && $masterAsset['reference_image_path'])
                <img src="data:image/jpeg;base64,{{ $masterAsset['reference_image_base64'] }}" alt="Master Asset Image">
            @else
                <p style="color: #999; font-style: italic;">Tidak ada gambar tersedia</p>
            @endif
        </div>
    </div>

    <!-- Master Asset Information Section -->
    <div class="asset-info">
        <h2>Informasi Aset Master</h2>

        <table class="detail-table" style="border-collapse: collapse; width: 100%;">
            <tr>
                <th style="width: 30%; border: 1px solid #EEF1F4;">Kode Aset Master</th>
                <td style="border: 1px solid #EEF1F4;">{{ $masterAsset['asset_master_code'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Nama Aset</th>
                <td style="border: 1px solid #EEF1F4;">{{ $masterAsset['asset_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Tipe Aset</th>
                <td style="border: 1px solid #EEF1F4;">
                    @php
                        $assetType = $masterAsset['asset_type'] ?? 'N/A';
                        if (strtolower($assetType) === 'medical') {
                            echo 'Medis';
                        } elseif (strtolower($assetType) === 'non_medical') {
                            echo 'Non Medis';
                        } else {
                            echo ucfirst($assetType);
                        }
                    @endphp
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Kategori</th>
                <td style="border: 1px solid #EEF1F4;">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Merk</th>
                <td style="border: 1px solid #EEF1F4;">{{ $masterAsset['brand_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Karakteristik</th>
                <td style="border: 1px solid #EEF1F4;">
                    @if(isset($masterAsset['is_depreciable']) && $masterAsset['is_depreciable'])
                        <span class="status-badge badge-blue">Aset Dapat Mengalami Depresiasi</span>
                    @endif

                    @if(isset($masterAsset['needs_calibration']) && $masterAsset['needs_calibration'])
                        <span class="status-badge badge-purple">Aset Memerlukan Kalibrasi</span>
                    @endif

                    @if((!isset($masterAsset['is_depreciable']) || !$masterAsset['is_depreciable']) &&
                        (!isset($masterAsset['needs_calibration']) || !$masterAsset['needs_calibration']))
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Dibuat Pada</th>
                <td style="border: 1px solid #EEF1F4;">{{ isset($masterAsset['created_at']) ? date('d M Y H:i', strtotime($masterAsset['created_at'])) : 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #EEF1F4;">Deskripsi</th>
                <td style="border: 1px solid #EEF1F4;">{{ $masterAsset['description'] ?? 'Tidak ada deskripsi tersedia' }}</td>
            </tr>
        </table>
    </div>

    <!-- Asset Units Section -->
    @if(isset($masterAsset['linked_assets']) && count($masterAsset['linked_assets']) > 0)
    <div class="asset-info">
        <h2>Unit Aset Terkait ({{ count($masterAsset['linked_assets']) }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode Aset</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masterAsset['linked_assets'] as $asset)
                <tr>
                    <td>{{ $asset['asset_code'] ?? 'N/A' }}</td>
                    <td>
                        @php
                            $conditionClass = 'status-badge';

                            if(isset($asset['condition'])) {
                                switch(strtolower($asset['condition'])) {
                                    case 'good':
                                        $conditionClass .= ' condition-good';
                                        $conditionText = 'Baik';
                                        break;
                                    case 'slightly damage':
                                    case 'slighly damaged':
                                        $conditionClass .= ' condition-slightly-damage';
                                        $conditionText = 'Sedikit Rusak';
                                        break;
                                    case 'high damage':
                                    case 'highly damaged':
                                        $conditionClass .= ' condition-high-damage';
                                        $conditionText = 'Rusak Berat';
                                        break;
                                    default:
                                        $conditionText = ucfirst($asset['condition']);
                                }
                            } else {
                                $conditionText = 'Unknown';
                            }
                        @endphp
                        <span class="{{ $conditionClass }}">
                            {{ $conditionText }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = 'status-badge';

                            if(isset($asset['current_status'])) {
                                switch(strtolower($asset['current_status'])) {
                                    case 'available':
                                        $statusClass .= ' status-available';
                                        $statusText = 'TERSEDIA';
                                        break;
                                    case 'in_use':
                                    case 'check out':
                                        $statusClass .= ' status-check-out';
                                        $statusText = 'DIPINJAM';
                                        break;
                                    case 'maintenance':
                                    case 'under repair':
                                        $statusClass .= ' status-under-repair';
                                        $statusText = 'PERBAIKAN';
                                        break;
                                    case 'lost':
                                        $statusClass .= ' status-lost';
                                        $statusText = 'HILANG';
                                        break;
                                    case 'dispose':
                                        $statusClass .= ' status-dispose';
                                        $statusText = 'DIHAPUSKAN';
                                        break;
                                    default:
                                        $statusText = strtoupper($asset['current_status']);
                                }
                            } else {
                                $statusText = 'UNKNOWN';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td>
                        {{ $asset['room_name'] ?? 'N/A' }}
                        @if(isset($asset['building_name']))
                        <br><span style="font-size: 9px; color: #666;">{{ $asset['building_name'] }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="asset-info">
        <h2>Unit Aset Terkait</h2>
        <p style="color: #999; font-style: italic; text-align: center; padding: 20px;">Tidak ada aset terkait dengan aset master ini.</p>
    </div>
    @endif

    <div class="footer">
        <p>Sistem Monitoring Aset - Detail Aset Master RS UMMI</p>
    </div>
</body>
</html>
