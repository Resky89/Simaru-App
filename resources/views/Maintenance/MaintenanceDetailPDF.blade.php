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

        .content-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        h2 {
            color: #213268;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #EEF1F4;
            padding-bottom: 5px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .detail-table th {
            text-align: left;
            background-color: #213268;
            color: white;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #EEF1F4;
            width: 30%;
            font-size: 12px;
        }

        .detail-table td {
            padding: 8px;
            border: 1px solid #EEF1F4;
            background-color: #fff;
            font-size: 12px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #213268;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }

        table.data-table td {
            border-top: 1px solid #eef1f4;
            padding: 8px;
            font-size: 10px;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: normal;
            text-transform: uppercase;
        }

        .status-new {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-in-progress, .status-in_progress {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-completed, .status-finished {
            background-color: #DCFCE7;
            color: #166534;
        }

        .status-canceled {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .notes-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            padding: 10px;
            font-size: 11px;
            border-radius: 4px;
        }

        .image-container {
            height: 180px;
            text-align: center;
            border: 1px solid #eee;
            background-color: white;
            vertical-align: middle;
            margin-top: 10px;
            padding: 10px;
        }

        .image-container img {
            max-width: 85%;
            max-height: 160px;
            object-fit: contain;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .page-break {
            page-break-before: always;
        }

        .status-banner {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #333;
            font-weight: bold;
        }

        .status-banner-new {
            background-color: #DBEAFE;
            border: 1px solid #93C5FD;
        }

        .status-banner-in-progress {
            background-color: #FEF3C7;
            border: 1px solid #FCD34D;
        }

        .status-banner-finished {
            background-color: #DCFCE7;
            border: 1px solid #86EFAC;
        }

        .status-banner-canceled {
            background-color: #FEE2E2;
            border: 1px solid #FECACA;
        }

        .info-grid {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-bottom: 15px;
            padding: 10px;
        }

        .card-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .document-box {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        function translateMonth($date) {
            if (!$date) return 'N/A';

            $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            // Also handle abbreviated month names
            $englishAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $indonesianAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            $formattedDate = date('d F Y', strtotime($date));

            // Replace full month names
            foreach ($englishMonths as $index => $month) {
                $formattedDate = str_replace($month, $indonesianMonths[$index], $formattedDate);
            }

            // Replace abbreviated month names
            foreach ($englishAbbr as $index => $month) {
                $formattedDate = str_replace($month, $indonesianAbbr[$index], $formattedDate);
            }

            return $formattedDate;
        }

        function translateInterval($interval) {
            $intervalText = '-';
            if ($interval === 'ONCE') {
                $intervalText = 'Sekali';
            } elseif ($interval === 'DAILY') {
                $intervalText = 'Harian';
            } elseif ($interval === 'WEEKLY') {
                $intervalText = 'Mingguan';
            } elseif ($interval === '2 WEEKS') {
                $intervalText = '2 Minggu';
            } elseif ($interval === 'MONTHLY') {
                $intervalText = 'Bulanan';
            } elseif ($interval === '2 MONTHS') {
                $intervalText = '2 Bulan';
            } elseif ($interval === '3 MONTHS') {
                $intervalText = '3 Bulan';
            } elseif ($interval === '4 MONTHS') {
                $intervalText = '4 Bulan';
            } elseif ($interval === '6 MONTHS') {
                $intervalText = '6 Bulan';
            } elseif ($interval === 'YEARLY') {
                $intervalText = 'Tahunan';
            }
            return $intervalText;
        }
    @endphp

    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETAIL PEMELIHARAAN</div>

    <!-- Status Banner Section -->
    @php
        $statusClass = 'status-banner-new';
        $status = $maintenance['status'] ?? '';
        $statusText = 'TIDAK DIKETAHUI';
        $statusDescription = '';

        if ($status == 'new') {
            $statusClass = 'status-banner-new';
            $statusText = 'BARU';
            $statusDescription = 'Pemeliharaan baru dibuat dan belum dimulai';
        } elseif ($status == 'in_progress') {
            $statusClass = 'status-banner-in-progress';
            $statusText = 'DALAM PROSES';
            $statusDescription = 'Pemeliharaan sedang dalam proses pengerjaan';
        } elseif ($status == 'finished') {
            $statusClass = 'status-banner-finished';
            $statusText = 'SELESAI';
            $statusDescription = 'Pemeliharaan telah selesai dilakukan';
        } elseif ($status == 'canceled') {
            $statusClass = 'status-banner-canceled';
            $statusText = 'DIBATALKAN';
            $statusDescription = 'Pemeliharaan telah dibatalkan';
        }
    @endphp

    <div class="status-banner {{ $statusClass }}">
        <div>Status: {{ $statusText }}</div>
        <div style="font-weight: normal; font-size: 11px; margin-top: 3px;">{{ $statusDescription }}</div>
    </div>

    <!-- Main Content Grid -->
    <div class="info-grid">
        <!-- Asset Information Card -->
        <div class="card">
            <div class="card-title">Informasi Aset</div>
            <table class="detail-table">
                <tr>
                    <th>Nama Aset</th>
                    <td>{{ $maintenance['asset_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Kode Aset</th>
                    <td>{{ $maintenance['asset_code'] ?? 'N/A' }}</td>
                </tr>
                @if(isset($maintenance['asset_id']))
                <tr>
                    <th>ID Aset</th>
                    <td>{{ $maintenance['asset_id'] ?? 'N/A' }}</td>
                </tr>
                @endif
                @if(isset($maintenance['asset']) && isset($maintenance['asset']['location']))
                <tr>
                    <th>Gedung</th>
                    <td>{{ $maintenance['asset']['location']['building_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ruangan</th>
                    <td>{{ $maintenance['asset']['location']['room_name'] ?? 'N/A' }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Schedule Information Card -->
        <div class="card">
            <div class="card-title">Jadwal Pemeliharaan</div>
            <table class="detail-table">
                <tr>
                    <th>Interval</th>
                    <td>{{ translateInterval($maintenance['interval'] ?? '') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Mulai</th>
                    <td>{{ isset($maintenance['start_date']) ? translateMonth($maintenance['start_date']) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Selesai</th>
                    <td>{{ isset($maintenance['end_date']) ? translateMonth($maintenance['end_date']) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ isset($maintenance['created_at']) ? translateMonth($maintenance['created_at']) : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Assignment Information Card -->
        <div class="card">
            <div class="card-title">Informasi Penugasan</div>
            <table class="detail-table">
                <tr>
                    <th>Ditugaskan Kepada</th>
                    <td>{{ $maintenance['assigned_to_employee_number'] ?? $maintenance['assigned_to'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Dijadwalkan Oleh</th>
                    <td>{{ $maintenance['scheduled_by_employee_number'] ?? $maintenance['scheduled_by'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Vendor</th>
                    <td>{{ $maintenance['vendor_name'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Notes & Documents Section -->
    @if((isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available') ||
        (isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path'])))
    <div class="card">
        <div class="card-title">Catatan & Dokumen</div>

        @if(isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available')
        <div class="notes-box" style="margin-bottom: 10px;">
            <p style="margin: 0; white-space: pre-wrap;">{{ $maintenance['notes'] }}</p>
        </div>
        @endif

        @if(isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path']))
        <div>
            <h4 style="margin: 10px 0 5px 0; font-size: 12px;">Dokumen Terlampir</h4>
            <div class="document-box">
                @php
                    $filePath = $maintenance['document_file_path'];
                    $fileName = basename($filePath);
                    // Build the file URL using config
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $fileUrl = $backendUrl . '/public' . $filePath;
                @endphp
                <p style="margin: 0;">
                    <strong>Nama File:</strong> {{ $fileName }}
                </p>
                <p style="margin-top: 5px; word-break: break-all;">
                    <strong>URL Dokumen:</strong> {{ $fileUrl }}
                </p>
                <p style="margin-top: 5px;">
                    <em>Dokumen tersedia dan dapat diakses melalui URL di atas.</em>
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Maintenance Report Section -->
    @if(isset($maintenance['maintenance_report']))
    <div class="card" style="background-color: #F0F7FF;">
        <div class="card-title">Laporan Pemeliharaan</div>

        <table class="detail-table">
            <tr>
                <th>Tanggal Laporan</th>
                <td>{{ isset($maintenance['maintenance_report']['maintenance_date']) ? translateMonth($maintenance['maintenance_report']['maintenance_date']) : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Dilaporkan Oleh</th>
                <td>{{ $maintenance['maintenance_report']['reported_by_employee_number'] ?? $maintenance['maintenance_report']['reported_by'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Laporan Dibuat Pada</th>
                <td>{{ isset($maintenance['maintenance_report']['created_at']) ? translateMonth($maintenance['maintenance_report']['created_at']) : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td style="white-space: pre-wrap;">{{ $maintenance['maintenance_report']['description'] ?? 'Tidak ada deskripsi tersedia' }}</td>
            </tr>
        </table>

        @if(!empty($maintenance['maintenance_report']['attachment_path']) || !empty($maintenance['maintenance_report']['attachment_picture_base64']))
        <h4 style="margin: 15px 0 5px 0; font-size: 13px;">Lampiran Gambar:</h4>
        <div class="image-container">
            @if(!empty($maintenance['maintenance_report']['attachment_picture_base64']))
                <img src="data:image/jpeg;base64,{{ $maintenance['maintenance_report']['attachment_picture_base64'] }}" alt="Gambar Laporan Pemeliharaan">
            @elseif(!empty($maintenance['maintenance_report']['attachment_path']))
                <p style="margin: 0; color: #6B7280; font-style: italic;">Gambar tersedia melalui URL: {{ $maintenance['maintenance_report']['attachment_path'] }}</p>
            @else
                <p style="margin: 0; color: #6B7280; font-style: italic;">Tidak ada gambar tersedia</p>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- History Section -->
    @php
        $hasHistory = isset($maintenance['history']) && is_array($maintenance['history']) && !empty($maintenance['history']);
    @endphp

    @if($hasHistory)
    <div class="card">
        <div class="card-title">Riwayat Pemeliharaan</div>
        <table class="data-table striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Diubah Oleh</th>
                    <th>Bidang</th>
                    <th>Dari</th>
                    <th>Menjadi</th>
                </tr>
            </thead>
            <tbody>
                @foreach(is_array($maintenance['history']) ? $maintenance['history'] : [] as $historyItem)
                <tr>
                    <td>{{ isset($historyItem['created_at']) ? translateMonth($historyItem['created_at']) : 'N/A' }}</td>
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
        <p>Sistem Monitoring Aset - Laporan Detail Pemeliharaan RS UMMI</p>
        <p>Tanggal Cetak: {{ translateMonth(date('Y-m-d')) }}</p>
    </div>
</body>
</html>
