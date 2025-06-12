<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Kalibrasi</title>
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

        .status-scheduled {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-in-progress {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-completed {
            background-color: #DCFCE7;
            color: #166534;
        }

        .status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .result-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: normal;
            text-transform: uppercase;
        }

        .result-pass {
            background-color: #DCFCE7;
            color: #166534;
        }

        .result-fail {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .result-unknown {
            background-color: #FEF3C7;
            color: #92400E;
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

        .status-banner-scheduled {
            background-color: #DBEAFE;
            border: 1px solid #93C5FD;
        }

        .status-banner-in-progress {
            background-color: #FEF3C7;
            border: 1px solid #FCD34D;
        }

        .status-banner-completed {
            background-color: #DCFCE7;
            border: 1px solid #86EFAC;
        }

        .status-banner-overdue {
            background-color: #FEE2E2;
            border: 1px solid #FECACA;
        }

        .status-banner-cancelled {
            background-color: #F3F4F6;
            border: 1px solid #D1D5DB;
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
    @endphp

    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETAIL KALIBRASI</div>

    <!-- Status Banner Section -->
    @php
        $statusClass = 'status-banner-scheduled';
        $status = $calibration['status_calibration'] ?? '';
        $statusText = 'TIDAK DIKETAHUI';
        $statusDescription = '';

        if ($status == 'scheduled') {
            $statusClass = 'status-banner-scheduled';
            $statusText = 'TERJADWAL';
            $statusDescription = 'Kalibrasi dijadwalkan pada ' . (isset($calibration['planning_calibration_date']) ? translateMonth($calibration['planning_calibration_date']) : 'tanggal tidak tersedia');
        } elseif ($status == 'in_progress') {
            $statusClass = 'status-banner-in-progress';
            $statusText = 'DALAM PROSES';
            $statusDescription = 'Kalibrasi sedang dalam proses';
        } elseif ($status == 'completed') {
            $statusClass = 'status-banner-completed';
            $statusText = 'SELESAI';
            $statusDescription = 'Kalibrasi telah selesai pada ' . (isset($calibration['actual_calibration_date']) ? translateMonth($calibration['actual_calibration_date']) : 'tanggal tidak tersedia');
        } elseif ($status == 'overdue') {
            $statusClass = 'status-banner-overdue';
            $statusText = 'TERLAMBAT';
            $statusDescription = 'Kalibrasi terlambat dari jadwal';
        } elseif ($status == 'cancelled') {
            $statusClass = 'status-banner-cancelled';
            $statusText = 'DIBATALKAN';
            $statusDescription = 'Kalibrasi telah dibatalkan';
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
                    <th>Kode Aset</th>
                    <td>{{ $calibration['asset_code'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Nama Aset</th>
                    <td>{{ $calibration['asset_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Merek</th>
                    <td>{{ $calibration['brand_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Nomor Seri</th>
                    <td>{{ $calibration['serial_number'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Location Information Card -->
        <div class="card">
            <div class="card-title">Lokasi</div>
            <table class="detail-table">
                <tr>
                    <th>Gedung</th>
                    <td>{{ $calibration['location']['building_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Lantai</th>
                    <td>{{ $calibration['location']['floor_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ruangan</th>
                    <td>{{ $calibration['location']['room_name'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Calibration Dates Card -->
        <div class="card">
            <div class="card-title">Jadwal Kalibrasi</div>
            <table class="detail-table">
                <tr>
                    <th>Tanggal Rencana</th>
                    <td>{{ isset($calibration['planning_calibration_date']) && $calibration['planning_calibration_date'] ? translateMonth($calibration['planning_calibration_date']) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Aktual</th>
                    <td>
                        @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                            <span style="color: #856404;">Belum dilakukan kalibrasi</span>
                        @else
                            {{ translateMonth($calibration['actual_calibration_date']) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Kalibrasi Berikutnya</th>
                    <td>{{ isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'] ? translateMonth($calibration['next_calibration_date']) : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
        <!-- Calibration Results Card -->
        <div class="card">
            <div class="card-title">Hasil Kalibrasi</div>
            <table class="detail-table">
                <tr>
                    <th>Vendor</th>
                    <td>{{ $calibration['vendor_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Nomor Sertifikat</th>
                    <td>{{ $calibration['certificate_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Biaya Kalibrasi</th>
                    <td>{{ isset($calibration['calibration_price']) ? 'Rp ' . number_format($calibration['calibration_price'], 0, ',', '.') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Hasil Kalibrasi</th>
                    <td>
                        @php
                            $resultClass = '';
                            $result = $calibration['calibration_result'] ?? '';
                            $resultText = 'N/A';

                            if ($result == 'pass') {
                                $resultClass = 'result-pass';
                                $resultText = 'LULUS';
                            } elseif ($result == 'fail') {
                                $resultClass = 'result-fail';
                                $resultText = 'GAGAL';
                            } elseif ($result == 'unknown') {
                                $resultClass = 'result-unknown';
                                $resultText = 'TIDAK DITEMUKAN';
                            }
                        @endphp
                        @if($resultText != 'N/A')
                        <span class="result-badge {{ $resultClass }}">{{ $resultText }}</span>
                        @else
                        {{ $resultText }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endif
    </div>

    <!-- Notes section -->
    <div class="card">
        <div class="card-title">Catatan Kalibrasi</div>
        <div class="notes-box">
            @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                <p style="color: #856404; margin: 0;">Belum dilakukan kalibrasi</p>
            @else
                <p style="margin: 0;">{{ $calibration['notes'] ?? 'Tidak ada catatan' }}</p>
            @endif
        </div>
    </div>

    @if(!empty($calibration['certificate_file_path']))
    <!-- Certificate Document Section -->
    <div class="card">
        <div class="card-title">Sertifikat Kalibrasi</div>

        @php
            $filePath = $calibration['certificate_file_path'];
            $fileName = basename($filePath);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);

            // Build the file URL using config
            $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
            $fileUrl = $backendUrl . '/public' . $filePath;
        @endphp

        @if($isImage && !empty($calibration['certificate_file_base64']))
        <!-- Certificate is an image, display it -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="100%" valign="top">
                    <div class="image-container">
                        <img src="data:image/{{ strtolower($fileExtension) }};base64,{{ $calibration['certificate_file_base64'] }}" alt="Sertifikat Kalibrasi">
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 5px; font-size: 11px;">
            <strong>{{ $fileName }}</strong>
        </div>
        @else
        <!-- Certificate is a document, show info -->
        <div class="notes-box">
            <p style="margin: 0;">
                <strong>Nama File:</strong> {{ $fileName }}
            </p>
            <p style="margin-top: 5px; word-break: break-all;">
                <strong>URL Dokumen:</strong> {{ $fileUrl }}
            </p>
            <p style="margin-top: 5px;">
                <em>Sertifikat tersedia dalam format dokumen dan tidak dapat ditampilkan dalam PDF ini. Silakan salin URL di atas untuk mengakses file.</em>
            </p>
        </div>
        @endif
    </div>
    @endif

    <!-- History section (if applicable) -->
    @if(!empty($calibration['history']) && count($calibration['history']) > 0)
    <div class="card">
        <div class="card-title">Riwayat Kalibrasi</div>
        <table class="data-table striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                    <th>Pengguna</th>
                    <th>Rincian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calibration['history'] as $entry)
                <tr>
                    <td>{{ isset($entry['created_at']) ? translateMonth($entry['created_at']) . ' ' . date('H:i', strtotime($entry['created_at'])) : 'N/A' }}</td>
                    <td>
                    @php
                        $action = $entry['action'] ?? '';
                        if($action == 'created') {
                            echo 'Dibuat';
                        } elseif($action == 'updated') {
                            echo 'Diperbarui';
                        } elseif($action == 'deleted') {
                            echo 'Dihapus';
                        } elseif($action == 'completed') {
                            echo 'Diselesaikan';
                        } else {
                            echo ucfirst($action);
                        }
                    @endphp
                    </td>
                    <td>{{ $entry['user_name'] ?? 'N/A' }}</td>
                    <td>{{ $entry['details'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Detail Kalibrasi RS UMMI</p>
    </div>
</body>
</html>
