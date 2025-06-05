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

        .asset-info {
            margin-bottom: 15px;
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
            width: 160px;
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
        }

        .image-container img {
            max-width: 85%;
            max-height: 160px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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

        .status-available {
            background-color: #659B09;
            color: white;
        }

        .status-deployed,
        .status-check-out {
            background-color: #F59E0B;
            color: white;
        }

        .status-maintenance,
        .status-under-repair {
            background-color: #25B1FF;
            color: white;
        }

        .status-lost {
            background-color: #EF4444;
            color: white;
        }

        .status-disposed,
        .status-dispose {
            background-color: #ACC3EF;
            color: white;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            margin-top: 12px;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }

        .asset-code-display {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            font-size: 13px;
        }

        .asset-name-display {
            text-align: center;
            margin: 5px 0;
            font-weight: bold;
            font-size: 13px;
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

        h2 {
            color: #213268;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #EEF1F4;
            padding-bottom: 5px;
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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETIL ASET</div>

    <!-- Asset Image and QR Side by Side using a table for better PDF rendering -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="49%" valign="top">
                <h2>Gambar Aset</h2>
                <div class="image-container">
                    @if(!empty($asset['image_base64']))
                        <img src="data:image/jpeg;base64,{{ $asset['image_base64'] }}" alt="Gambar Aset">
                    @elseif(isset($asset['asset_master']['reference_image_path']) && $asset['asset_master']['reference_image_path'])
                        <img src="{{ config('app.backend_url') }}/public{{ $asset['asset_master']['reference_image_path'] }}"
                            alt="Gambar Aset">
                    @else
                        <p style="color: #999; font-style: italic;">Tidak ada gambar tersedia</p>
                    @endif
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" valign="top">
                <h2>Kode QR</h2>
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

    <div class="asset-code-display">{{ $asset['asset_code'] ?? '-' }}</div>
    <div class="asset-name-display">{{ $asset['asset_master']['asset_name'] ?? $asset['asset_master_name'] ?? '-' }}
    </div>

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
    <div style="text-align: center; margin-bottom: 10px;">
        <span class="{{ $statusColor }}">{{ $statusText }}</span>
    </div>

    <!-- Asset Information Sections in Table Format -->
    <h2>Informasi Master Aset</h2>
    <table class="detail-table">
        <tr>
            <th>Kode Master Aset</th>
            <td>{{ $asset['asset_master']['asset_master_code'] ?? 'Tidak Tersedia' }}</td>
        </tr>
        <tr>
            <th>Tipe Aset</th>
            <td>
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
            </td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $asset['asset_master']['subcategory_name'] ?? $asset['asset_master']['subcategory']['subcategory_name'] ?? 'Tidak Tersedia' }}
            </td>
        </tr>
        <tr>
            <th>Merek</th>
            <td>{{ $asset['asset_master']['brand_name'] ?? $asset['asset_master']['brand']['brand_name'] ?? $asset['brand_name'] ?? 'Tidak Tersedia' }}
            </td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td>{{ $asset['asset_master']['description'] ?? 'Tidak ada deskripsi' }}</td>
        </tr>
    </table>

    <!-- Asset Details Section -->
    <h2>Informasi Aset</h2>
    <table class="detail-table">
        <tr>
            <th>Ruangan</th>
            <td>{{ $asset['room_name'] ?? $asset['room']['room_name'] ?? 'Tidak Tersedia' }}</td>
        </tr>
        <tr>
            <th>Gedung</th>
            <td>{{ $asset['building_name'] ?? $asset['room']['building']['building_name'] ?? 'Tidak Tersedia' }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
                @php
                    $condition = $asset['condition'] ?? '';
                    $conditionText = '';

                    switch (strtolower($condition)) {
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
            </td>
        </tr>
        <tr>
            <th>Garansi Berakhir</th>
            <td>{{ isset($asset['warranty_end_date']) && $asset['warranty_end_date'] ? date('d M Y', strtotime($asset['warranty_end_date'])) : 'Tidak Tersedia' }}
            </td>
        </tr>
        <tr>
            <th>Nomor Seri</th>
            <td>{{ $asset['serial_number'] ?? 'Tidak Tersedia' }}</td>
        </tr>
        <tr>
            <th>Penanggung Jawab</th>
            <td>
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
            </td>
        </tr>
        <tr>
            <th>Harga Beli</th>
            <td>{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Tanggal Beli</th>
            <td>{{ isset($asset['purchase_date']) && $asset['purchase_date'] ? date('d M Y', strtotime($asset['purchase_date'])) : 'Tidak Tersedia' }}
            </td>
        </tr>
        @if($asset['current_status'] === 'dispose' || $asset['current_status'] === 'disposed')
            <tr>
                <th>Tanggal Dimusnahkan</th>
                <td>{{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'Tidak Tersedia' }}
                </td>
            </tr>
        @endif
        @if($asset['current_status'] === 'lost')
            <tr>
                <th>Tanggal Hilang</th>
                <td>{{ isset($asset['updated_at']) ? date('d M Y', strtotime($asset['updated_at'])) : 'Tidak Tersedia' }}
                </td>
            </tr>
        @endif
    </table>

    <!-- Depreciation Information Section -->
    @if(isset($depreciation) && $depreciation)
        <div class="page-break"></div>
        <h2 class="text-center page-title" style="margin-top: 20px;">DATA PENYUSUTAN ASET</h2>

        <h2>Informasi Dasar Penyusutan</h2>
        <table class="detail-table">
            <tr>
                <th>Tanggal Pengadaan</th>
                <td>{{ $depreciation['date_acquired'] ?? '-' }}</td>
            </tr>
            <tr>
                <th>Total Biaya</th>
                <td>{{ isset($depreciation['total_cost']) ? number_format($depreciation['total_cost'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Nilai Sisa</th>
                <td>{{ isset($depreciation['salvage_value']) ? number_format($depreciation['salvage_value'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Usia Aset (Bulan)</th>
                <td>{{ $depreciation['asset_life_months'] ?? '-' }}</td>
            </tr>
            <tr>
                <th>Metode Penyusutan</th>
                <td>
                    @php
                        $method = $depreciation['depreciation_method'] ?? '-';
                        $methodMap = [
                            'Straight Line' => 'Garis Lurus (Straight Line)',
                            'Declining Balance' => 'Saldo Menurun (Declining Balance)',
                            'Double Declining Balance' => 'Saldo Menurun Ganda (Double Declining Balance)',
                            '150% Declining Balance' => 'Saldo Menurun 150% (150% Declining Balance)',
                            'Sum of the Year\'s Digits' => 'Jumlah Digit Tahun (Sum of Year\'s Digits)'
                        ];
                        echo isset($methodMap[$method]) ? $methodMap[$method] : $method;
                    @endphp
                </td>
            </tr>
            <tr>
                <th>Nilai Saat Ini</th>
                <td>
                    @php
                        // Get today's date from the device running the application
                        $today = new DateTime();
                        $currentValue = '-';

                        // Use today's date for calculation
                        if (isset($depreciation['monthly_data']) && is_array($depreciation['monthly_data']) && count($depreciation['monthly_data']) > 0) {
                            // Start with total cost as fallback
                            $currentValue = $depreciation['total_cost'] ?? 0;

                            // Find the most recent month that's not in the future
                            $mostRecentMonth = null;
                            $mostRecentDate = null;
                            $currentMonthMatch = null;

                            // Get current month and year for exact matching
                            $currentMonthNumber = (int)$today->format('n');
                            $currentYear = (int)$today->format('Y');
                            $currentMonthNames = [
                                // Full names
                                1 => ['January', 'Januari'],
                                2 => ['February', 'Februari'],
                                3 => ['March', 'Maret'],
                                4 => ['April', 'April'],
                                5 => ['May', 'Mei'],
                                6 => ['June', 'Juni'],
                                7 => ['July', 'Juli'],
                                8 => ['August', 'Agustus'],
                                9 => ['September', 'September'],
                                10 => ['October', 'Oktober'],
                                11 => ['November', 'November'],
                                12 => ['December', 'Desember']
                            ];
                            // Add abbreviated names
                            $currentMonthAbbr = [
                                1 => ['Jan'],
                                2 => ['Feb'],
                                3 => ['Mar'],
                                4 => ['Apr'],
                                5 => ['May'],
                                6 => ['Jun'],
                                7 => ['Jul'],
                                8 => ['Aug', 'Agu', 'Agt'],
                                9 => ['Sep', 'Sept'],
                                10 => ['Oct', 'Okt'],
                                11 => ['Nov'],
                                12 => ['Dec', 'Des']
                            ];
                            foreach ($currentMonthAbbr as $num => $abbrs) {
                                $currentMonthNames[$num] = array_merge($currentMonthNames[$num], $abbrs);
                            }

                            // First, try to find an exact match with the current month and year
                            foreach ($depreciation['monthly_data'] as $month) {
                                // Skip if no book value
                                if (!isset($month['book_value'])) continue;

                                if (isset($month['month_name'])) {
                                    // Check for an exact match with the current month/year
                                    if (preg_match('/(\w+)\s+(\d{4})/', $month['month_name'], $matches)) {
                                        $monthName = $matches[1];
                                        $year = (int)$matches[2];

                                        // Check if this is the current month and year
                                        if ($year === $currentYear) {
                                            foreach ($currentMonthNames[$currentMonthNumber] as $validMonthName) {
                                                if (strcasecmp($monthName, $validMonthName) === 0) {
                                                    // We found an exact match for the current month!
                                                    $currentMonthMatch = $month;
                                                    break 2; // Exit both loops
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // If we found an exact match for the current month, use it
                            if ($currentMonthMatch !== null) {
                                $currentValue = $currentMonthMatch['book_value'];
                            }
                            // Otherwise, look for the most recent applicable month
                            else {
                                foreach ($depreciation['monthly_data'] as $month) {
                                    // Skip if no book value
                                    if (!isset($month['book_value'])) continue;

                                    $monthDate = null;

                                    // Try to extract date information
                                    if (isset($month['month_name'])) {
                                        $monthNameStr = $month['month_name'];

                                        // Handle different month formats (Full: "January 2023", Abbreviated: "Jan 2023", etc.)
                                        if (preg_match('/(\w+)\s+(\d{4})/', $monthNameStr, $matches)) {
                                            $monthName = $matches[1];
                                            $year = (int)$matches[2];

                                            // Expanded month name mapping to include abbreviations
                                            $monthMap = [
                                                // Indonesian - full names
                                                'Januari' => 1,
                                                'Februari' => 2,
                                                'Maret' => 3,
                                                'April' => 4,
                                                'Mei' => 5,
                                                'Juni' => 6,
                                                'Juli' => 7,
                                                'Agustus' => 8,
                                                'September' => 9,
                                                'Oktober' => 10,
                                                'November' => 11,
                                                'Desember' => 12,
                                                // Indonesian - abbreviated
                                                'Jan' => 1,
                                                'Feb' => 2,
                                                'Mar' => 3,
                                                'Apr' => 4,
                                                'Jun' => 6,
                                                'Jul' => 7,
                                                'Agu' => 8,
                                                'Agt' => 8,
                                                'Sep' => 9,
                                                'Sept' => 9,
                                                'Okt' => 10,
                                                'Nov' => 11,
                                                'Des' => 12,
                                            ];

                                            // Add English month mappings
                                            $englishMonths = [
                                                'January' => 1, 'February' => 2, 'March' => 3,
                                                'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8,
                                                'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12,
                                                // Abbreviated
                                                'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
                                                'Jun' => 6, 'Jul' => 7, 'Aug' => 8,
                                                'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
                                            ];

                                            $monthMap = array_merge($monthMap, $englishMonths);

                                            // Case-insensitive month name lookup
                                            $monthNumber = null;
                                            foreach ($monthMap as $name => $num) {
                                                if (strcasecmp($monthName, $name) === 0) {
                                                    $monthNumber = $num;
                                                    break;
                                                }
                                            }

                                            // If we can identify the month number
                                            if ($monthNumber !== null) {
                                                // Create a date object for this month (end of month)
                                                try {
                                                    $monthDate = new DateTime();
                                                    $monthDate->setDate($year, $monthNumber, 1);
                                                    $monthDate->modify('last day of this month');
                                                } catch (\Exception $e) {
                                                    // Invalid date
                                                    continue;
                                                }
                                            }
                                        }
                                    }
                                    // If we have month_number and date_acquired, use those
                                    else if (isset($month['month_number']) && isset($depreciation['date_acquired'])) {
                                        try {
                                            $startDate = new DateTime($depreciation['date_acquired']);
                                            $monthDate = clone $startDate;
                                            $monthDate->modify('+' . ((int)$month['month_number'] - 1) . ' months');
                                            $monthDate->modify('last day of this month');
                                        } catch (\Exception $e) {
                                            // Invalid date
                                            continue;
                                        }
                                    }

                                    // If we have a valid date that's not in the future
                                    if ($monthDate && $monthDate <= $today) {
                                        // If this is our first valid month or it's more recent than what we have
                                        if ($mostRecentDate === null || $monthDate > $mostRecentDate) {
                                            $mostRecentDate = $monthDate;
                                            $mostRecentMonth = $month;
                                        }
                                    }
                                }

                                // Use the most recent month's book value if we found one
                                if ($mostRecentMonth !== null) {
                                    $currentValue = $mostRecentMonth['book_value'];
                                }
                            }
                        }

                        // Format for display
                        if (is_numeric($currentValue)) {
                            $currentValue = number_format($currentValue, 0, ',', '.');
                        }
                    @endphp
                    {{ $currentValue }}
                </td>
            </tr>
        </table>

        <!-- Monthly Depreciation Data -->
        @if(isset($depreciation['monthly_data']) && count($depreciation['monthly_data']) > 0)
            <h2>Data Penyusutan Bulanan</h2>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 20%;">Bulan</th>
                        <th style="width: 25%;">Pengeluaran Penyusutan</th>
                        <th style="width: 25%;">Penyusutan Akumulasi</th>
                        <th style="width: 20%;">Nilai Buku</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($depreciation['monthly_data'] as $month)
                        <tr>
                            <td>{{ $month['month_number'] ?? '-' }}</td>
                            <td>{{ $month['month_name'] ?? '-' }}</td>
                            <td>{{ isset($month['expense']) ? number_format($month['expense'], 0, ',', '.') : '-' }}</td>
                            <td>{{ isset($month['accumulated_depreciation']) ? number_format($month['accumulated_depreciation'], 0, ',', '.') : '-' }}
                            </td>
                            <td>{{ isset($month['book_value']) ? number_format($month['book_value'], 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    <!-- Finance Transaction Section -->
    @if(isset($finance) && isset($finance['transactions']) && count($finance['transactions']) > 0)
        <div class="page-break"></div>
        <h2 class="text-center page-title" style="margin-top: 20px;">DATA TRANSAKSI KEUANGAN</h2>

        <!-- Financial Summary -->
        @if(isset($finance['summary']))
            <div class="finance-summary" style="margin-bottom: 20px;">
                <table class="detail-table">
                    <tr>
                        <th style="width: 33%;">Total Pengeluaran</th>
                        <th style="width: 33%;">Total Pemasukan</th>
                        <th style="width: 33%;">Saldo</th>
                    </tr>
                    <tr>
                        <td style="text-align: center; color: #EF4444; font-weight: bold;">
                            {{ isset($finance['summary']['expense']['total']) ? number_format($finance['summary']['expense']['total'], 0, ',', '.') : '0' }}
                        </td>
                        <td style="text-align: center; color: #10B981; font-weight: bold;">
                            {{ isset($finance['summary']['income']['total']) ? number_format($finance['summary']['income']['total'], 0, ',', '.') : '0' }}
                        </td>
                        <td style="text-align: center; color: #213268; font-weight: bold;">
                            {{ isset($finance['summary']['balance']) ? number_format($finance['summary']['balance'], 0, ',', '.') : '0' }}
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <!-- Transactions List -->
        <h2>Daftar Transaksi</h2>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Tanggal</th>
                    <th style="width: 15%;">Tipe</th>
                    <th style="width: 25%;">Nominal</th>
                    <th style="width: 40%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finance['transactions'] as $transaction)
                    <tr>
                        <td>{{ isset($transaction['transaction_date']) ? date('d M Y', strtotime($transaction['transaction_date'])) : '-' }}
                        </td>
                        <td>
                            @if(isset($transaction['type']) && $transaction['type'] == 'income')
                                <span style="color: #10B981;">Pemasukan</span>
                            @else
                                <span style="color: #EF4444;">Pengeluaran</span>
                            @endif
                        </td>
                        <td>{{ isset($transaction['amount']) ? number_format($transaction['amount'], 0, ',', '.') : '-' }}</td>
                        <td>{{ $transaction['description'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Detail Aset RS UMMI</p>
    </div>
</body>

</html>