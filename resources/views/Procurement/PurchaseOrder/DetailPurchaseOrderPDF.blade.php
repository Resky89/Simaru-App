<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Pemesanan</title>
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
        .subtitle {
            font-size: 14px;
            margin: 0 0 15px 0;
        }
        .date-generated {
            font-size: 11px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            vertical-align: top;
            padding-right: 10px;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #213268;
            color: white;
            text-align: left;
            padding: 6px;
            font-size: 11px;
        }
        .items-table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        .items-table tr.total-row td {
            font-weight: bold;
            border-top: 2px solid #213268;
        }
        .items-table tr.terms-row {
            background-color: #f0f5ff;
        }
        .items-table tr.terms-row td:first-child {
            font-weight: bold;
            color: #213268;
        }
        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 40px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .col-50 {
            width: 50%;
            float: left;
            padding: 0 5px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN DETAIL PEMESANAN</div>
    
    <div class="clearfix">
        <!-- Left Column -->
        <div class="col-50">
            <!-- Order Information -->
            <div class="section">
                <div class="section-title">Informasi Pemesanan</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nomor Pemesanan:</div>
                        <div class="info-value">{{ $purchaseOrder['purchase_order_code'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Penawaran:</div>
                        <div class="info-value">{{ $purchaseOrder['comparison_code'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Pemesanan:</div>
                        <div class="info-value">{{ isset($purchaseOrder['created_at']) ? \Carbon\Carbon::parse($purchaseOrder['created_at'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-50">
            <!-- Vendor Information -->
            <div class="section">
                <div class="section-title">Informasi Vendor</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Vendor:</div>
                        <div class="info-value">{{ $purchaseOrder['vendor']['vendor_name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Penanggung Jawab:</div>
                        <div class="info-value">{{ $purchaseOrder['vendor']['contact_person'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nomor Telepon:</div>
                        <div class="info-value">{{ $purchaseOrder['vendor']['phone_number'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Section - Full Width -->
    <div class="section">
        <div class="section-title">Daftar Aset</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Nama Aset</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @if(isset($purchaseOrder['items']) && is_array($purchaseOrder['items']) && count($purchaseOrder['items']) > 0)
                    @foreach($purchaseOrder['items'] as $item)
                        @php
                            $grandTotal += (float)($item['total_price'] ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $item['procurement_item_name'] ?? 'N/A' }}</td>
                            <td>{{ $item['quantity'] ?? 'N/A' }}</td>
                            <td>Rp {{ isset($item['unit_price']) ? number_format((float)$item['unit_price'], 0, ',', '.') : 'N/A' }}</td>
                            <td>Rp {{ isset($item['total_price']) ? number_format((float)$item['total_price'], 0, ',', '.') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada item yang ditemukan untuk pemesanan ini.</td>
                    </tr>
                @endif

                <!-- Grand Total Row -->
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total Keseluruhan</td>
                    <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>

                <!-- Payment Terms Row -->
                <tr class="terms-row">
                    <td>Syarat Pembayaran</td>
                    <td colspan="3">{{ $purchaseOrder['payment_terms'] ?? 'Tidak ada data' }}</td>
                </tr>

                <!-- Delivery Terms Row -->
                <tr class="terms-row">
                    <td>Syarat Pengiriman</td>
                    <td colspan="3">{{ $purchaseOrder['delivery_terms'] ?? 'Tidak ada data' }}</td>
                </tr>

                <!-- Notes Row -->
                <tr class="terms-row">
                    <td>Catatan</td>
                    <td colspan="3">{{ $purchaseOrder['notes'] ?? 'Tidak ada data' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Sistem Monitoring Aset - Laporan Pemesanan - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('Y') }}
    </div>
</body>
</html>
