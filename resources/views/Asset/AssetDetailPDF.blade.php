<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Aset</title>
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
            margin-bottom: 30px;
        }
        .header-content {
            width: 100%;
            margin-bottom: 15px;
        }
        .letterhead {
            width: 100%;
        }
        .logo {
            text-align: center;
            width: 20%;
        }
        .logo img {
            max-height: 100px;
        }
        .institution-info {
            text-align: center;
            width: 80%;
        }
        .institution-name {
            font-size: 20px;
            font-weight: bold;
            color: #213268;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .institution-address {
            font-size: 11px;
            margin: 4px 0;
        }
        .institution-contact {
            font-size: 11px;
            margin: 2px 0;
        }
        .institution-tagline {
            font-style: italic;
            font-size: 11px;
            margin: 4px 0;
            color: #213268;
        }
        .header-border {
            border-bottom: 3px solid #000;
            margin-top: 5px;
            margin-bottom: 0;
        }
        .header-border-thin {
            border-bottom: 1px solid #000;
            margin-top: 1px;
            margin-bottom: 0;
        }
        .asset-info {
            margin-bottom: 20px;
            clear: both;
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
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: normal;
            text-transform: uppercase;
        }
        .status-available {
            background-color: #659B09;
            color: white;
        }
        .status-deployed, .status-check-out {
            background-color: #F59E0B;
            color: white;
        }
        .status-maintenance, .status-under-repair {
            background-color: #25B1FF;
            color: white;
        }
        .status-lost {
            background-color: #EF4444;
            color: white;
        }
        .status-disposed, .status-dispose {
            background-color: #ACC3EF;
            color: white;
        }
        .asset-image {
            text-align: center;
            margin: 15px 0;
        }
        .asset-image img {
            max-width: 250px;
            max-height: 250px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            margin-top: 15px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 15px 0;
        }
        .image-container {
            height: 250px;
            text-align: center;
            border: 1px solid #eee;
            background-color: white;
            vertical-align: middle;
        }
        .image-container img {
            max-width: 90%;
            max-height: 230px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0 10px;
            text-decoration: underline;
        }
        .report-number {
            font-size: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="letterhead" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo" valign="top">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo RS UMMI">
                </td>
                <td class="institution-info" valign="top">
                    <div class="institution-name">RUMAH SAKIT UMMI</div>
                    <div class="institution-tagline">We Care & Cure with Heart</div>
                    <div class="institution-address">Jl. Empang II No.2 16132 Bogor Jawa Barat</div>
                    <div class="institution-contact">Telp: +62 251 8341600 | Website: www.rsummi.co.id</div>
                </td>
            </tr>
        </table>
        <div class="header-border"></div>
        <div class="header-border-thin"></div>
    </div>

    <!-- Asset Image and QR Side by Side using a table for better PDF rendering -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="49%" valign="top">
                <h2 style="font-size: 16px; color: #213268; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">Gambar Aset</h2>
                <div class="image-container">
                    @if(!empty($asset['image_base64']))
                        <img src="data:image/jpeg;base64,{{ $asset['image_base64'] }}" alt="Gambar Aset">
                    @elseif(isset($asset['asset_master']['reference_image_path']) && $asset['asset_master']['reference_image_path'])
                        <img src="{{ config('app.backend_url') }}/public{{ $asset['asset_master']['reference_image_path'] }}" alt="Gambar Aset">
                    @else
                        <p style="color: #999; font-style: italic;">Tidak ada gambar tersedia</p>
                    @endif
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" valign="top">
                <h2 style="font-size: 16px; color: #213268; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">Kode QR</h2>
                <div class="image-container">
                    @if(isset($asset['qr_base64']))
                        <img src="data:image/png;base64,{{ $asset['qr_base64'] }}" alt="Kode QR Aset">
                    @elseif(isset($asset['qr_code']))
                        @php
                            $backendUrl = rtrim(config('app.backend_url'), '/');
                            $qrImageUrl = $backendUrl . '/public' . $asset['qr_code'];
                        @endphp
                        <img src="{{ $qrImageUrl }}" alt="Kode QR Aset">
                    @else
                        <p style="color: #999; font-style: italic;">Kode QR tidak tersedia</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="asset-info">
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="display: inline-block; font-weight: bold; margin: 0 auto;">
                <p style="font-size: 14px; margin: 0;">{{ $asset['asset_code'] ?? '-' }}</p>
                <p style="font-size: 14px; margin-top: 5px;">{{ $asset['asset_master']['asset_name'] ?? $asset['asset_master_name'] ?? '-' }}</p>

                @php
                    $statusColor = 'status-badge';
                    $statusText = 'TIDAK DIKETAHUI';
                    if (isset($asset['current_status'])) {
                        switch (strtolower($asset['current_status'])) {
                            case 'available':
                                $statusColor .= ' status-available';
                                $statusText = 'TERSEDIA';
                                break;
                            case 'check out':
                                $statusColor .= ' status-check-out';
                                $statusText = 'DIPINJAM';
                                break;
                            case 'lost':
                                $statusColor .= ' status-lost';
                                $statusText = 'HILANG';
                                break;
                            case 'dispose':
                                $statusColor .= ' status-dispose';
                                $statusText = 'DIHAPUSKAN';
                                break;
                            case 'under repair':
                                $statusColor .= ' status-under-repair';
                                $statusText = 'PERBAIKAN';
                                break;
                            default:
                                $statusText = strtoupper($asset['current_status']);
                        }
                    }
                @endphp
                <div style="margin-top: 8px;">
                    <span class="{{ $statusColor }}">{{ $statusText }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Information Sections -->
    <div class="asset-info">
        <h2>Informasi Master Aset</h2>
        <div class="info-row">
            <div class="info-label">Kode Master Aset:</div>
            <div class="info-value">{{ $asset['asset_master']['asset_master_code'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tipe Aset:</div>
            <div class="info-value">
                @php
                    $assetType = $asset['asset_master']['asset_type'] ?? $asset['asset_master']['category']['category_name'] ?? 'Tidak Tersedia';
                    if (strtolower($assetType) === 'medical') {
                        echo 'Medis';
                    } elseif (strtolower($assetType) === 'non_medical') {
                        echo 'Non Medis';
                    } else {
                        echo $assetType;
                    }
                @endphp
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Kategori:</div>
            <div class="info-value">{{ $asset['asset_master']['subcategory_name'] ?? $asset['asset_master']['subcategory']['subcategory_name'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Merek:</div>
            <div class="info-value">{{ $asset['asset_master']['brand_name'] ?? $asset['asset_master']['brand']['brand_name'] ?? $asset['brand_name'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Deskripsi:</div>
            <div class="info-value">{{ $asset['asset_master']['description'] ?? 'Tidak ada deskripsi' }}</div>
        </div>
    </div>

    <!-- Asset Details Section -->
    <div class="asset-info">
        <h2>Informasi Aset</h2>
        <div class="info-row">
            <div class="info-label">Ruangan:</div>
            <div class="info-value">{{ $asset['room_name'] ?? $asset['room']['room_name'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Gedung:</div>
            <div class="info-value">{{ $asset['building_name'] ?? $asset['room']['building']['building_name'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kondisi:</div>
            <div class="info-value">
                @php
                    $condition = $asset['condition'] ?? '';
                    $conditionText = '';

                    switch(strtolower($condition)) {
                        case 'good':
                            $conditionText = 'Baik';
                            break;
                        case 'slighly damage':
                            $conditionText = 'Sedikit Rusak';
                            break;
                        case 'high damage':
                            $conditionText = 'Sangat Rusak';
                            break;
                        default:
                            $conditionText = ucfirst($condition);
                    }
                @endphp
                {{ $conditionText }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Garansi Berakhir:</div>
            <div class="info-value">
                {{ isset($asset['warranty_end_date']) && $asset['warranty_end_date'] ? date('d M Y', strtotime($asset['warranty_end_date'])) : 'Tidak Tersedia' }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Nomor Seri:</div>
            <div class="info-value">{{ $asset['serial_number'] ?? 'Tidak Tersedia' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Penanggung Jawab:</div>
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
                        ID Pengguna: {{ $asset['user_id'] }}
                    @endif
                @else
                    -
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Harga Beli:</div>
            <div class="info-value">{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Beli:</div>
            <div class="info-value">
                {{ isset($asset['purchase_date']) && $asset['purchase_date'] ? date('d M Y', strtotime($asset['purchase_date'])) : 'Tidak Tersedia' }}
            </div>
        </div>

        @if($asset['current_status'] === 'dispose' || $asset['current_status'] === 'disposed')
        <div class="info-row">
            <div class="info-label">Tanggal Dimusnahkan:</div>
            <div class="info-value">
                {{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'Tidak Tersedia' }}
            </div>
        </div>
        @endif

        @if($asset['current_status'] === 'lost')
        <div class="info-row">
            <div class="info-label">Tanggal Hilang:</div>
            <div class="info-value">
                {{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'Tidak Tersedia' }}
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
            <div class="info-label">Umur Aset:</div>
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

    <!-- Footer -->
    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Detail Aset RS UMMI</p>
    </div>
</body>
</html>
