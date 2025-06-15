<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyusutan</title>
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

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .info-card {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-box {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            background-color: #F0F4FF;
            border: 1px solid #E9ECEF;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #213268;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">LAPORAN PENYUSUTAN ASET</div>
    <div class="subtitle">
        Tanggal Laporan:
        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $formattedDate = '';
            try {
                $dateStr = isset($as_of_date) ? $as_of_date : date('Y-m-d');
                $date = new DateTime($dateStr);
                $day = $date->format('d');
                $month = $months[(int)$date->format('m')];
                $year = $date->format('Y');
                $formattedDate = "$day $month $year";
            } catch (\Exception $e) {
                $now = new DateTime();
                $day = $now->format('d');
                $month = $months[(int)$now->format('m')];
                $year = $now->format('Y');
                $formattedDate = "$day $month $year";
            }
            echo $formattedDate;
        @endphp
        @if(!empty($asset_type))
            | Tipe Aset: {{ ucfirst($asset_type) == 'Medical' ? 'Medis' : (ucfirst($asset_type) == 'Non_medical' ? 'Non Medis' : ucfirst($asset_type)) }}
        @endif
        @if(isset($is_percentage))
            | Format: {{ $is_percentage ? 'Persentase' : 'Nilai' }}
        @endif
        @if(!empty($search))
            | Filter: {{ $search }}
        @endif
    </div>

    <h2>Ringkasan Penyusutan</h2>

    <table class="summary-grid">
        <tr>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Aset</div>
                    <div class="summary-value">{{ $summary['total_items'] ?? 0 }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Biaya Perolehan</div>
                    <div class="summary-value">
                        @if(isset($is_percentage) && $is_percentage)
                            100%
                        @else
                            Rp {{ number_format($summary['total_acquisition_cost'] ?? 0, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Nilai Buku</div>
                    <div class="summary-value">
                        @if(isset($is_percentage) && $is_percentage)
                            @php
                                $acquisitionCost = $summary['total_acquisition_cost'] ?? 0;
                                $bookValue = $summary['total_book_value'] ?? 0;
                                $percentage = $acquisitionCost > 0 ? ($bookValue / $acquisitionCost * 100) : 0;
                            @endphp
                            {{ number_format($percentage, 2) }}%
                        @else
                            Rp {{ number_format($summary['total_book_value'] ?? 0, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Penyusutan</div>
                    <div class="summary-value">
                        @if(isset($is_percentage) && $is_percentage)
                            @php
                                $acquisitionCost = $summary['total_acquisition_cost'] ?? 0;
                                $depreciation = $summary['total_depreciation'] ?? 0;
                                $percentage = $acquisitionCost > 0 ? ($depreciation / $acquisitionCost * 100) : 0;
                            @endphp
                            {{ number_format($percentage, 2) }}%
                        @else
                            Rp {{ number_format($summary['total_depreciation'] ?? 0, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <h2>Detail Aset</h2>
    @if(count($items) > 0)
        <table class="striped">
            <thead>
                <tr>
                    <th class="text-center" width="4%">ID Aset</th>
                    <th width="12%">Nama Aset</th>
                    <th width="8%">Tanggal Perolehan</th>
                    <th class="text-right" width="8%">Biaya Pembelian</th>
                    <th class="text-right" width="8%">Nilai Sisa</th>
                    <th class="text-center" width="5%">Masa Pakai (Bulan)</th>
                    <th width="8%">Metode Penyusutan</th>
                    <th width="7%">Bulan dan Tahun</th>
                    <th class="text-right" width="8%">Nilai Buku</th>
                    <th width="8%">Gedung</th>
                    <th width="8%">Ruangan</th>
                    <th width="8%">Tipe Aset</th>
                    <th width="8%">Subkategori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td class="text-center">{{ $item['asset_id'] }}</td>
                    <td>{{ $item['asset_name'] }}</td>
                    <td>
                        @if(isset($item['date_acquired']) && !empty($item['date_acquired']))
                            @php
                                try {
                                    $date = new DateTime($item['date_acquired']);
                                    $day = $date->format('d');
                                    $month = $months[(int)$date->format('m')];
                                    $year = $date->format('Y');
                                    echo "$day $month $year";
                                } catch (\Exception $e) {
                                    echo 'N/A';
                                }
                            @endphp
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($is_percentage) && $is_percentage)
                            100%
                        @else
                            Rp {{ number_format($item['purchase_cost'] ?? 0, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($is_percentage) && $is_percentage)
                            @php
                                $purchaseCost = $item['purchase_cost'] ?? 0;
                                $salvageValue = $item['salvage_value'] ?? 0;
                                $percentage = $purchaseCost > 0 ? ($salvageValue / $purchaseCost * 100) : 0;
                            @endphp
                            {{ number_format($percentage, 2) }}%
                        @else
                            Rp {{ number_format($item['salvage_value'] ?? 0, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-center">{{ $item['asset_life_months'] }}</td>
                    <td>{{ $item['depreciation_method'] }}</td>
                    <td>
                        @php
                            try {
                                if (isset($item['month_and_year']) && !empty($item['month_and_year'])) {
                                    $dateObj = DateTime::createFromFormat('F Y', $item['month_and_year']);
                                    if ($dateObj) {
                                        $monthNum = (int)$dateObj->format('m');
                                        $year = $dateObj->format('Y');
                                        echo $months[$monthNum] . ' ' . $year;
                                    } else {
                                        echo $item['month_and_year'] ?? 'N/A';
                                    }
                                } else {
                                    echo 'N/A';
                                }
                            } catch (\Exception $e) {
                                echo $item['month_and_year'] ?? 'N/A';
                            }
                        @endphp
                    </td>
                    <td class="text-right">
                        @if(isset($is_percentage) && $is_percentage)
                            @php
                                $purchaseCost = $item['purchase_cost'] ?? 0;
                                $bookValue = $item['book_value_at_month_end'] ?? 0;
                                $percentage = $purchaseCost > 0 ? ($bookValue / $purchaseCost * 100) : 0;
                            @endphp
                            {{ number_format($percentage, 2) }}%
                        @else
                            Rp {{ number_format($item['book_value_at_month_end'] ?? 0, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>{{ $item['building'] ?? 'N/A' }}</td>
                    <td>{{ $item['room'] ?? 'N/A' }}</td>
                    <td>{{ $item['asset_type'] == 'medical' ? 'Medis' : ($item['asset_type'] == 'non_medical' ? 'Non Medis' : ucfirst($item['asset_type'] ?? 'N/A')) }}</td>
                    <td>{{ $item['subcategory'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data penyusutan untuk laporan ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dibuat pada:
            @php
                try {
                    $now = new DateTime();
                    $day = $now->format('d');
                    $month = $months[(int)$now->format('m')] ?? '';
                    $year = $now->format('Y');
                    echo "$day $month $year";
                } catch (\Exception $e) {
                    echo date('d M Y');
                }
            @endphp
        </p>
        <p>Sistem Monitoring Aset - Laporan Penyusutan Aset RS UMMI</p>
    </div>
</body>
</html>
