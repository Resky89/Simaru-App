<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyusutan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #213268;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 16pt;
            margin: 5px 0;
            color: #213268;
        }
        h2 {
            font-size: 13pt;
            margin: 8px 0;
            color: #213268;
        }
        .sub-header {
            font-size: 10pt;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8pt;
        }
        table, th, td {
            border: 1px solid #EEF1F4;
        }
        th {
            background-color: #213268;
            padding: 5px 3px;
            font-weight: bold;
            text-align: left;
            color: white;
            font-size: 8pt;
        }
        td {
            padding: 4px 3px;
            font-size: 8pt;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 15px;
            color: #666;
        }
        .summary {
            margin-bottom: 15px;
        }
        .summary-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
        }
        .summary-box {
            background-color: #f0f4ff;
            border-radius: 5px;
            padding: 10px;
            flex: 1;
            min-width: 160px;
            margin-bottom: 10px;
        }
        .summary-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 10pt;
            font-weight: bold;
            color: #213268;
        }
        .page-break {
            page-break-after: always;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENYUSUTAN</h1>
        <div class="sub-header">
            <strong>Dibuat pada:</strong> {{ date('d M Y H:i:s') }}<br>
            <strong>Tanggal:</strong> {{ date('d M Y', strtotime($as_of_date)) }}
            @if(!empty($search))
                <br><strong>Filter Pencarian:</strong> {{ $search }}
            @endif
            @if(!empty($asset_type))
                <br><strong>Tipe Aset:</strong> {{ ucfirst($asset_type) == 'Medical' ? 'Medis' : (ucfirst($asset_type) == 'Non_medical' ? 'Non Medis' : ucfirst($asset_type)) }}
            @endif
            @if(isset($is_percentage))
                <br><strong>Format:</strong> {{ $is_percentage ? 'Persentase' : 'Nilai' }}
            @endif
        </div>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Aset</div>
                    <div class="summary-value">{{ $summary['total_items'] ?? 0 }}</div>
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Biaya Perolehan</div>
                    <div class="summary-value">
                        @if(isset($is_percentage) && $is_percentage)
                            100%
                        @else
                            Rp {{ number_format($summary['total_acquisition_cost'] ?? 0, 0, ',', '.') }}
                        @endif
                    </div>
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
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
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
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
                </td>
            </tr>
        </table>
    </div>

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
                    <td>{{ isset($item['date_acquired']) ? \Carbon\Carbon::parse($item['date_acquired'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</td>
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
                                // Try to parse and format the month_and_year
                                echo \Carbon\Carbon::createFromFormat('F Y', $item['month_and_year'])->locale('id')->isoFormat('MMMM YYYY');
                            } catch (\Exception $e) {
                                // If parsing fails, show original value
                                echo $item['month_and_year'];
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
        <p>Laporan ini dibuat secara otomatis dari Sistem Monitoring Aset.</p>
        <p>© {{ date('Y') }} Sistem Monitoring Aset</p>
    </div>
</body>
</html>
