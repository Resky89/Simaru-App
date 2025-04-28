<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Asset Detail Report</title>
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
        .asset-info {
            margin-bottom: 20px;
        }
        .asset-info h2 {
            font-size: 16px;
            color: #213268;
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
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code {
            max-width: 150px;
            max-height: 150px;
            margin: 0 auto;
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .status-available {
            background-color: #d4edda;
            color: #155724;
        }
        .status-deployed, .status-check-out {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-maintenance {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-lost {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-disposed, .status-dispose {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .asset-image {
            text-align: center;
            margin: 15px 0;
        }
        .asset-image img {
            max-width: 250px;
            max-height: 250px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ASSET DETAIL REPORT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <!-- Asset Image Section -->
    <div class="asset-info">
        <h2>Asset Image</h2>
        <div class="image-container" style="text-align: center; margin: 10px 0; padding: 10px; background-color: white; border: 1px solid #eee;">
            @if(!empty($asset['image_base64']))
                <img src="data:image/jpeg;base64,{{ $asset['image_base64'] }}" alt="Asset Image" style="max-width: 300px; max-height: 300px;">
            @elseif(isset($asset['asset_master']['reference_image_path']) && $asset['asset_master']['reference_image_path'])
                <!-- This likely won't work in PDF, but keeping as fallback -->
                <img src="{{ config('app.backend_url') }}/public{{ $asset['asset_master']['reference_image_path'] }}" alt="Asset Image" style="max-width: 300px; max-height: 300px;">
            @else
                <p style="color: #999; font-style: italic;">No image available</p>
            @endif
        </div>
    </div>

    <!-- Asset Master Information Section -->
    <div class="asset-info">
        <h2>Informasi Master Asset</h2>

        <div class="info-row">
            <div class="info-label">Asset ID:</div>
            <div class="info-value">{{ $asset['asset_id'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Asset Code:</div>
            <div class="info-value">{{ $asset['asset_code'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Asset Name:</div>
            <div class="info-value">{{ $asset['asset_master']['asset_name'] ?? $asset['asset_master_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Asset Master Code:</div>
            <div class="info-value">{{ $asset['asset_master']['asset_master_code'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Kategori:</div>
            <div class="info-value">{{ $asset['asset_master']['asset_type'] ?? $asset['asset_master']['category']['category_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Sub Kategori:</div>
            <div class="info-value">{{ $asset['asset_master']['subcategory_name'] ?? $asset['asset_master']['subcategory']['subcategory_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Merek:</div>
            <div class="info-value">{{ $asset['asset_master']['brand_name'] ?? $asset['asset_master']['brand']['brand_name'] ?? $asset['brand_name'] ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Asset Information Section -->
    <div class="asset-info">
        <h2>Informasi Asset</h2>

        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                @php
                    $statusClass = '';
                    $status = $asset['current_status'] ?? '';
                    $statusText = 'UNKNOWN';

                    switch(strtolower($status)) {
                        case 'available':
                            $statusClass = 'status-available';
                            $statusText = 'TERSEDIA';
                            break;
                        case 'check out':
                        case 'deployed':
                            $statusClass = 'status-check-out';
                            $statusText = 'DIPINJAM';
                            break;
                        case 'maintenance':
                            $statusClass = 'status-maintenance';
                            $statusText = 'PERAWATAN';
                            break;
                        case 'lost':
                            $statusClass = 'status-lost';
                            $statusText = 'HILANG';
                            break;
                        case 'dispose':
                        case 'disposed':
                            $statusClass = 'status-dispose';
                            $statusText = 'DIHAPUSKAN';
                            break;
                        default:
                            $statusText = strtoupper($status);
                    }
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Ruangan:</div>
            <div class="info-value">{{ $asset['room_name'] ?? $asset['room']['room_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Gedung:</div>
            <div class="info-value">{{ $asset['building_name'] ?? $asset['room']['building']['building_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nomor Seri:</div>
            <div class="info-value">{{ $asset['serial_number'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Kondisi:</div>
            <div class="info-value">{{ ucfirst($asset['condition'] ?? 'N/A') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Karyawan Penanggung Jawab:</div>
            <div class="info-value">
                @if(isset($asset['user_id']) && $asset['user_id'])
                    @if(isset($asset['user']['employee_number']) && $asset['user']['employee_number'])
                        {{ $asset['user']['employee_number'] }}
                        @if(isset($asset['user']['name']) && $asset['user']['name'])
                            - {{ $asset['user']['name'] }}
                        @endif
                    @elseif(isset($asset['user']['name']) && $asset['user']['name'])
                        {{ $asset['user']['name'] }}
                    @else
                        User ID: {{ $asset['user_id'] }}
                    @endif
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <!-- Purchase Information Section -->
    <div class="asset-info">
        <h2>Informasi Pembelian</h2>

        <div class="info-row">
            <div class="info-label">Tanggal Beli:</div>
            <div class="info-value">
                {{ isset($asset['purchase_date']) ? date('d M Y', strtotime($asset['purchase_date'])) : 'N/A' }}
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Harga Beli:</div>
            <div class="info-value">
                {{ isset($asset['purchase_cost']) ? number_format($asset['purchase_cost'], 2) : 'N/A' }}
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Tanggal Berakhir Garansi:</div>
            <div class="info-value">
                {{ isset($asset['warranty_end_date']) ? date('d M Y', strtotime($asset['warranty_end_date'])) : 'N/A' }}
            </div>
        </div>

        @if($asset['current_status'] === 'disposed' || $asset['current_status'] === 'dispose')
        <div class="info-row">
            <div class="info-label">Tanggal Dimusnahkan:</div>
            <div class="info-value">
                {{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'N/A' }}
            </div>
        </div>
        @endif

        @if($asset['current_status'] === 'lost')
        <div class="info-row">
            <div class="info-label">Tanggal Hilang:</div>
            <div class="info-value">
                {{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'N/A' }}
            </div>
        </div>
        @endif
    </div>

    <!-- Depreciation Information Section -->
    @if(isset($asset['depreciation']))
    <div class="asset-info">
        <h2>Informasi Penyusutan</h2>

        <div class="info-row">
            <div class="info-label">Metode:</div>
            <div class="info-value">{{ $asset['depreciation']['depreciation_method'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Biaya Akuisisi:</div>
            <div class="info-value">
                {{ isset($asset['depreciation']['acquisition_cost']) ? number_format($asset['depreciation']['acquisition_cost'], 2) : 'N/A' }}
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Nilai Sisa:</div>
            <div class="info-value">
                {{ isset($asset['depreciation']['salvage_value']) ? number_format($asset['depreciation']['salvage_value'], 2) : 'N/A' }}
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Umur Asset:</div>
            <div class="info-value">
                {{ $asset['depreciation']['asset_life_months'] ?? 'N/A' }} bulan
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Nilai Saat Ini:</div>
            <div class="info-value">
                {{ isset($asset['depreciation']['current_value']) ? number_format($asset['depreciation']['current_value'], 2) : 'N/A' }}
            </div>
        </div>
    </div>
    @endif

    <!-- QR Code Section -->
    @if(isset($asset['qr_base64']))
    <div class="asset-info">
        <h2>Asset QR Code</h2>
        <div class="image-container" style="text-align: center; margin: 10px 0; padding: 10px; background-color: white; border: 1px solid #eee;">
            <img src="data:image/png;base64,{{ $asset['qr_base64'] }}" alt="Asset QR Code" style="max-width: 200px; max-height: 200px;">
        </div>
    </div>
    @elseif(isset($asset['qr_code']))
    <div class="asset-info">
        <h2>Asset QR Code</h2>
        <div class="image-container" style="text-align: center; margin: 10px 0; padding: 10px; background-color: white; border: 1px solid #eee;">
            @php
                $backendUrl = rtrim(config('app.backend_url'), '/');
                $qrImageUrl = $backendUrl . '/public' . $asset['qr_code'];
            @endphp
            <img src="{{ $qrImageUrl }}" alt="Asset QR Code" style="max-width: 200px; max-height: 200px;">
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Asset Monitoring System - Asset Detail Report</p>
    </div>
</body>
</html>