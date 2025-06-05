<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Detail Opname Aset</title>
    @php
        // Function to format dates in Indonesian
        function formatDateIndonesian($date, $includeTime = true) {
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            // If date is null or empty, use current date and time
            if ($date === null || $date === '') {
                $date = new DateTime();
            } elseif (is_string($date)) {
                $date = new DateTime($date);
            }

            $day = $date->format('d');
            $month = $months[(int)$date->format('m')];
            $year = $date->format('Y');

            if ($includeTime) {
                $time = $date->format('H:i');
                return "$day $month $year, $time";
            } else {
                return "$day $month $year";
            }
        }
    @endphp
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
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            color: #213268;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #EEF1F4;
            padding-bottom: 5px;
        }

        .info-card {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-grid {
            display: block;
            margin-bottom: 10px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            color: #666;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .info-value {
            color: #213268;
            font-size: 14px;
            font-weight: bold;
        }

        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }

        .summary-card.found {
            background-color: #F0FDF4;
            border-color: #DCFCE7;
        }

        .summary-card.missing {
            background-color: #FEF2F2;
            border-color: #FEE2E2;
        }

        .summary-card.misplaced {
            background-color: #FFFBEB;
            border-color: #FEF3C7;
        }

        .summary-label {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .summary-label.found {
            color: #15803D;
        }

        .summary-label.missing {
            color: #B91C1C;
        }

        .summary-label.misplaced {
            color: #B45309;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }

        .summary-value.found {
            color: #16A34A;
        }

        .summary-value.missing {
            color: #DC2626;
        }

        .summary-value.misplaced {
            color: #F59E0B;
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
            border: 1px solid #213268;
        }

        td {
            border: 1px solid #EEF1F4;
            padding: 6px;
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

        .status-found {
            background-color: #DCFCE7;
            color: #15803D;
        }

        .status-missing {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        .status-misplaced {
            background-color: #FEF3C7;
            color: #B45309;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETAIL OPNAME ASET</div>
    <div class="subtitle">Laporan detail dan hasil pemeriksaan inventaris aset</div>

    <!-- Opname Info Card -->
    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Kode Opname</div>
                <div class="info-value">{{ $opnameCode }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Gedung</div>
                <div class="info-value">{{ isset($roomInfo['building']) ? $roomInfo['building'] : '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Ruangan</div>
                <div class="info-value">{{ isset($roomInfo['room_name']) ? $roomInfo['room_name'] : '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Lantai</div>
                <div class="info-value">{{ isset($roomInfo['floor']) ? $roomInfo['floor'] : '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Dibuat</div>
                <div class="info-value">
                    @php
                        $formattedDate = formatDateIndonesian($roomInfo['created_at'] ?? null);
                    @endphp
                    {{ $formattedDate }}
                </div>
            </div>
        </div>
    </div>

    <h2>Ringkasan Aset</h2>

    <!-- Summary Cards -->
    <table class="summary-grid">
        <tr>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-label">Total Aset</div>
                    <div class="summary-value">{{ isset($summary['total_assets']) ? $summary['total_assets'] : '0' }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card">
                    <div class="summary-label">Terscan</div>
                    <div class="summary-value">{{ isset($summary['scanned_assets']) ? $summary['scanned_assets'] : '0' }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card found">
                    <div class="summary-label found">Ditemukan</div>
                    <div class="summary-value found">{{ isset($summary['found_assets']) ? $summary['found_assets'] : '0' }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card missing">
                    <div class="summary-label missing">Hilang</div>
                    <div class="summary-value missing">{{ isset($summary['missing_assets']) ? $summary['missing_assets'] : '0' }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="summary-card misplaced">
                    <div class="summary-label misplaced">Salah Tempat</div>
                    <div class="summary-value misplaced">{{ isset($summary['misplaced_assets']) ? $summary['misplaced_assets'] : '0' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h2>Detail Aset</h2>
    @if(!empty($details))
    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Deskripsi</th>
                <th>Tanggal Scan</th>
                <th>Status</th>
                <th>Lokasi Seharusnya</th>
                <th>Lokasi Aktual</th>
                <th>Discan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $asset)
            <tr>
                <td><strong>{{ isset($asset['asset_code']) ? $asset['asset_code'] : '-' }}</strong></td>
                <td>{{ isset($asset['asset_description']) ? $asset['asset_description'] : '-' }}</td>
                <td>
                    @if(isset($asset['scan_date']))
                    {{ formatDateIndonesian($asset['scan_date'], false) }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if(isset($asset['scan_status']))
                    @if($asset['scan_status'] == 'found')
                    <span class="status-badge status-found">Ditemukan</span>
                    @elseif($asset['scan_status'] == 'missing')
                    <span class="status-badge status-missing">Hilang</span>
                    @elseif($asset['scan_status'] == 'misplaced')
                    <span class="status-badge status-misplaced">Salah Tempat</span>
                    @else
                    {{ $asset['scan_status'] }}
                    @endif
                    @else
                    -
                    @endif
                </td>
                <td>{{ isset($asset['expected_location_name']) ? $asset['expected_location_name'] : '-' }}</td>
                <td>{{ isset($asset['actual_location_name']) ? $asset['actual_location_name'] : '-' }}</td>
                <td>{{ isset($asset['scanner_name']) ? $asset['scanner_name'] : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px; color: #666; font-style: italic; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px;">
        <p>Tidak ada data aset tersedia untuk laporan opname ini.</p>
    </div>
    @endif

    <div class="footer">
        <p>Dibuat pada: {{ formatDateIndonesian(new DateTime(), true) }}</p>
        <p>Sistem Monitoring Aset - Laporan Detail Opname Aset RS UMMI</p>
    </div>
</body>

</html>
