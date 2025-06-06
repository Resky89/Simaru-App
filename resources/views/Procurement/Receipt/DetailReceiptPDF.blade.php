<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Penerimaan</title>
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

    <div class="page-title">LAPORAN DETAIL PENERIMAAN</div>

    <div class="clearfix">
        <!-- Left Column -->
        <div class="col-50">
            <!-- Receipt Information -->
            <div class="section">
                <div class="section-title">Informasi Penerimaan</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nomor Penerimaan:</div>
                        <div class="info-value">{{ $receipt['receipt_code'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nomor Pemesanan:</div>
                        <div class="info-value">{{ $receipt['purchase_order_code'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Penerimaan:</div>
                        <div class="info-value">{{ isset($receipt['receipt_date']) ? \Carbon\Carbon::parse($receipt['receipt_date'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-50">
            <!-- Personnel Information -->
            <div class="section">
                <div class="section-title">Informasi Personil</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Dikirim Oleh:</div>
                        <div class="info-value">{{ $receipt['delivered_by'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Diterima Oleh:</div>
                        <div class="info-value">{{ $receipt['receiver_name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Diinput Oleh:</div>
                        <div class="info-value">{{ $receipt['creator_name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Input:</div>
                        <div class="info-value">{{ isset($receipt['created_at']) ? \Carbon\Carbon::parse($receipt['created_at'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}</div>
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
                    <th>Tanggal Penerimaan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($receipt['items']) && is_array($receipt['items']) && count($receipt['items']) > 0)
                    @foreach($receipt['items'] as $item)
                        <tr>
                            <td>{{ $item['procurement_item_name'] ?? 'N/A' }}</td>
                            <td>{{ isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}</td>
                            <td>{{ $item['notes'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada item yang ditemukan untuk penerimaan ini.</td>
                    </tr>
                @endif

                <!-- Notes Row -->
                @if(isset($receipt['notes']) && !empty($receipt['notes']))
                <tr class="terms-row">
                    <td>Catatan</td>
                    <td colspan="2">{{ $receipt['notes'] ?? 'Tidak ada data' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        Sistem Monitoring Aset - Laporan Penerimaan - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('Y') }}
    </div>
</body>
</html>
