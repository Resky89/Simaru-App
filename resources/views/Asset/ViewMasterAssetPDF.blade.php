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
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #28356B;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #28356B;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .asset-info {
            margin-bottom: 20px;
        }
        .asset-info h2 {
            font-size: 16px;
            color: #28356B;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
        }
        .info-value {
            flex: 1;
        }
        .asset-image {
            text-align: center;
            margin: 15px 0;
        }
        .asset-image img {
            max-width: 250px;
            max-height: 250px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #28356B;
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .status-badge-yes {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge-no {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Condition badge styles */
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

        /* Status badge styles */
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
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL LAPORAN ASSET MASTER</h1>
        <p>Dibuat pada: {{ $date_generated }}</p>
    </div>

    <!-- Master Asset Image Section -->
    <div class="asset-info">
        <h2>Gambar Aset Master</h2>
        <div class="image-container" style="text-align: center; margin: 10px 0; padding: 10px; background-color: white; border: 1px solid #eee;">
            @if(!empty($masterAsset['image_base64']))
                <img src="data:image/jpeg;base64,{{ $masterAsset['image_base64'] }}" alt="Master Asset Image" style="max-width: 300px; max-height: 300px;">
            @elseif(isset($masterAsset['reference_image_path']) && $masterAsset['reference_image_path'])
                <img src="data:image/jpeg;base64,{{ $masterAsset['reference_image_base64'] }}" alt="Master Asset Image" style="max-width: 300px; max-height: 300px;">
            @else
                <p style="color: #999; font-style: italic;">Tidak ada gambar tersedia</p>
            @endif
        </div>
    </div>

    <!-- Master Asset Information Section -->
    <div class="asset-info">
        <h2>Informasi Aset Master</h2>

        <!-- Basic Identification -->
        <div class="info-row">
            <div class="info-label">Kode Aset Master:</div>
            <div class="info-value">{{ $masterAsset['asset_master_code'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nama Aset:</div>
            <div class="info-value">{{ $masterAsset['asset_name'] ?? 'N/A' }}</div>
        </div>

        <!-- Classification -->
        <div class="info-row">
            <div class="info-label">Tipe Aset:</div>
            <div class="info-value">{{ ucfirst($masterAsset['asset_type'] ?? 'N/A') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Kategori:</div>
            <div class="info-value">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Merk:</div>
            <div class="info-value">{{ $masterAsset['brand_name'] ?? 'N/A' }}</div>
        </div>

        <!-- Asset Characteristics -->
        <div class="info-row">
            <div class="info-label">Penyusutan:</div>
            <div class="info-value">
                @if(isset($masterAsset['is_depreciable']) && $masterAsset['is_depreciable'])
                    <span class="status-badge status-badge-yes">Ya</span>
                @else
                    <span class="status-badge status-badge-no">No</span>
                @endif
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Memerlukan Kalibrasi:</div>
            <div class="info-value">
                @if(isset($masterAsset['needs_calibration']) && $masterAsset['needs_calibration'])
                    <span class="status-badge status-badge-yes">Ya</span>
                @else
                    <span class="status-badge status-badge-no">Tidak</span>
                @endif
            </div>
        </div>

        <!-- Additional Information -->
        <div class="info-row">
            <div class="info-label">Dibuat Pada:</div>
            <div class="info-value">{{ isset($masterAsset['created_at']) ? date('d M Y H:i', strtotime($masterAsset['created_at'])) : 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Deskripsi:</div>
            <div class="info-value">{{ $masterAsset['description'] ?? 'Tidak ada deskripsi tersedia' }}</div>
        </div>
    </div>

    <!-- Asset Units Section -->
    @if(isset($masterAsset['linked_assets']) && count($masterAsset['linked_assets']) > 0)
    <div class="asset-info">
        <h2>Unit Aset Terkait ({{ count($masterAsset['linked_assets']) }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode Aset</th>
                    <th>Nomor Seri</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masterAsset['linked_assets'] as $asset)
                <tr>
                    <td>{{ $asset['asset_code'] ?? 'N/A' }}</td>
                    <td>{{ $asset['serial_number'] ?? 'N/A' }}</td>
                    <td>
                        @php
                            $conditionClass = 'status-badge';

                            if(isset($asset['condition'])) {
                                switch(strtolower($asset['condition'])) {
                                    case 'good':
                                        $conditionClass .= ' Baik';
                                        break;
                                    case 'slighly damaged':
                                        $conditionClass .= ' Sedikit Rusak';
                                        break;
                                    case 'highly damaged':
                                        $conditionClass .= ' Rusak Berat';
                                        break;
                                }
                            }
                        @endphp
                        <span class="{{ $conditionClass }}">
                            {{ ucfirst($asset['condition'] ?? 'Unknown') }}
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
        <p>Sistem Monitoring Aset - Detail Laporan Aset Master</p>
    </div>
</body>
</html>
