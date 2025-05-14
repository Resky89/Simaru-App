<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Pemeliharaan</title>
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
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
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
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-new {
            background-color: #E6F0FF;
            color: #0066CC;
        }
        .status-scheduled {
            background-color: #F0E6FF;
            color: #6600CC;
        }
        .status-in-progress {
            background-color: #FFF6E6;
            color: #CC6600;
        }
        .status-completed {
            background-color: #E6FFE6;
            color: #00CC00;
        }
        .status-canceled {
            background-color: #FFE6E6;
            color: #CC0000;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .history-table th {
            background-color: #213268;
            color: white;
            text-align: left;
            padding: 6px;
            font-size: 11px;
        }
        .history-table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 40px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
        }
        .image-container {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        img {
            max-width: 100%;
            max-height: 200px;
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
        .report-section {
            background-color: #f0f5ff;
            border: 1px solid #cce0ff;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL PEMELIHARAAN</h1>
        <p>ID: {{ $maintenance['id'] ?? 'N/A' }} | Status:
        @php
            $status = $maintenance['status'] ?? '';
            echo ucfirst($status) ?: 'Tidak Diketahui';
        @endphp
        </p>
        <p>Dibuat pada: {{ date('d M Y H:i:s') }}</p>
    </div>

    <div class="clearfix">
        <!-- Left Column -->
        <div class="col-50">
            <!-- Asset Information -->
            <div class="section">
                <div class="section-title">Informasi Aset</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Aset:</div>
                        <div class="info-value">{{ $maintenance['asset_name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ID Aset:</div>
                        <div class="info-value">{{ $maintenance['asset_id'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kode Aset:</div>
                        <div class="info-value">{{ $maintenance['asset_code'] ?? 'N/A' }}</div>
                    </div>
                    @if(isset($maintenance['asset']) && isset($maintenance['asset']['location']))
                    <div class="info-row">
                        <div class="info-label">Lokasi:</div>
                        <div class="info-value">{{ $maintenance['asset']['location']['room_name'] ?? 'N/A' }}, {{ $maintenance['asset']['location']['building_name'] ?? '' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="section">
                <div class="section-title">Informasi Jadwal</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Interval:</div>
                        <div class="info-value">{{ $maintenance['interval'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Mulai:</div>
                        <div class="info-value">{{ isset($maintenance['start_date']) ? date('d M Y', strtotime($maintenance['start_date'])) : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Selesai:</div>
                        <div class="info-value">{{ isset($maintenance['end_date']) ? date('d M Y', strtotime($maintenance['end_date'])) : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dibuat Pada:</div>
                        <div class="info-value">{{ isset($maintenance['created_at']) ? date('d M Y H:i', strtotime($maintenance['created_at'])) : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Diperbarui Pada:</div>
                        <div class="info-value">{{ isset($maintenance['updated_at']) ? date('d M Y H:i', strtotime($maintenance['updated_at'])) : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-50">
            <!-- Assignment Information -->
            <div class="section">
                <div class="section-title">Informasi Penugasan</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Ditugaskan Kepada:</div>
                        <div class="info-value">{{ $maintenance['assigned_to'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dijadwalkan Oleh:</div>
                        <div class="info-value">{{ $maintenance['scheduled_by'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Vendor:</div>
                        <div class="info-value">{{ $maintenance['vendor_name'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Notes & Documents -->
            @if((isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available') ||
                (isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path'])))
            <div class="section">
                <div class="section-title">Catatan & Dokumen</div>
                <div class="info-grid">
                    @if(isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available')
                    <div class="info-row">
                        <div class="info-label">Catatan:</div>
                        <div class="info-value">{{ $maintenance['notes'] }}</div>
                    </div>
                    @endif

                    @if(isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path']))
                    <div class="info-row">
                        <div class="info-label">Dokumen Terlampir:</div>
                        <div class="info-value">Dokumen tersedia (tidak dapat dilihat di PDF)</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Maintenance Report Section - Full Width -->
    @if(isset($maintenance['maintenance_report']))
    <div class="report-section">
        <div class="section-title">Laporan Pemeliharaan</div>

        <div class="clearfix">
            <div class="col-50">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Tanggal Laporan:</div>
                        <div class="info-value">{{ isset($maintenance['maintenance_report']['maintenance_date']) ? date('d M Y H:i', strtotime($maintenance['maintenance_report']['maintenance_date'])) : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dilaporkan Oleh:</div>
                        <div class="info-value">ID: {{ $maintenance['maintenance_report']['reported_by'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Laporan Dibuat Pada:</div>
                        <div class="info-value">{{ isset($maintenance['maintenance_report']['created_at']) ? date('d M Y H:i', strtotime($maintenance['maintenance_report']['created_at'])) : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-50">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Deskripsi:</div>
                        <div class="info-value">{{ $maintenance['maintenance_report']['description'] ?? 'Tidak ada deskripsi tersedia' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Report Image -->
        <div class="image-container">
            @if(!empty($maintenance['maintenance_report']['attachment_picture_base64']))
                <img src="data:image/jpeg;base64,{{ $maintenance['maintenance_report']['attachment_picture_base64'] }}" alt="Gambar Laporan Pemeliharaan">
            @else
                <p style="color: #999; font-style: italic;">Tidak ada gambar tersedia</p>
            @endif
        </div>
    </div>
    @endif

    <!-- History Section -->
    @php
        $hasHistory = isset($maintenance['history']) && is_array($maintenance['history']) && !empty($maintenance['history']);
    @endphp

    @if($hasHistory)
    <div class="section">
        <div class="section-title">Riwayat Pemeliharaan</div>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Diubah Oleh</th>
                    <th>Bidang</th>
                    <th>Dari</th>
                    <th>Ke</th>
                </tr>
            </thead>
            <tbody>
                @foreach(is_array($maintenance['history']) ? $maintenance['history'] : [] as $historyItem)
                <tr>
                    <td>{{ isset($historyItem['created_at']) ? date('d M Y H:i', strtotime($historyItem['created_at'])) : 'N/A' }}</td>
                    <td>{{ $historyItem['user_name'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $historyItem['field_name'] ?? 'N/A')) }}</td>
                    <td>{{ $historyItem['old_value'] ?? 'N/A' }}</td>
                    <td>{{ $historyItem['new_value'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Sistem Monitoring Aset - Laporan Pemeliharaan - {{ date('Y') }}
    </div>
</body>
</html>
