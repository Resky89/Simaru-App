<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depreciation Report</title>
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
        <h1>DEPRECIATION REPORT</h1>
        <div class="sub-header">
            <strong>Generated on:</strong> {{ date('d M Y H:i:s') }}<br>
            <strong>As of Date:</strong> {{ date('d M Y', strtotime($as_of_date)) }}
            @if(!empty($search))
                <br><strong>Search Filter:</strong> {{ $search }}
            @endif
            @if(!empty($asset_type))
                <br><strong>Asset Type:</strong> {{ ucfirst($asset_type) }}
            @endif
        </div>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Assets</div>
                    <div class="summary-value">{{ $summary['total_items'] ?? 0 }}</div>
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Acquisition Cost</div>
                    <div class="summary-value">Rp {{ number_format($summary['total_acquisition_cost'] ?? 0, 0, ',', '.') }}</div>
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Book Value</div>
                    <div class="summary-value">Rp {{ number_format($summary['total_book_value'] ?? 0, 0, ',', '.') }}</div>
                </td>
                <td width="25%" style="background-color: #f0f4ff; border: none;">
                    <div class="summary-label">Total Depreciation</div>
                    <div class="summary-value">Rp {{ number_format($summary['total_depreciation'] ?? 0, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(count($items) > 0)
        <table class="striped">
            <thead>
                <tr>
                    <th class="text-center" width="4%">Asset ID</th>
                    <th width="12%">Asset Name</th>
                    <th width="8%">Date Acquired</th>
                    <th class="text-right" width="8%">Purchase Cost</th>
                    <th class="text-right" width="8%">Salvage Value</th>
                    <th class="text-center" width="5%">Asset Life (Month)</th>
                    <th width="8%">Depreciation Method</th>
                    <th width="7%">Month and Year</th>
                    <th class="text-right" width="8%">Book Value</th>
                    <th width="8%">Building</th>
                    <th width="8%">Room</th>
                    <th width="8%">Asset Type</th>
                    <th width="8%">Subcategory</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td class="text-center">{{ $item['asset_id'] }}</td>
                    <td>{{ $item['asset_name'] }}</td>
                    <td>{{ isset($item['date_acquired']) ? date('d M Y', strtotime($item['date_acquired'])) : 'N/A' }}</td>
                    <td class="text-right">Rp {{ number_format($item['purchase_cost'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['salvage_value'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['asset_life_months'] }}</td>
                    <td>{{ $item['depreciation_method'] }}</td>
                    <td>{{ $item['month_and_year'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['book_value_at_month_end'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $item['building'] ?? 'N/A' }}</td>
                    <td>{{ $item['room'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item['asset_type'] ?? 'N/A') }}</td>
                    <td>{{ $item['subcategory'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No depreciation data available for this report.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report is automatically generated from the Asset Monitoring System.</p>
        <p>© {{ date('Y') }} Asset Monitoring System</p>
    </div>
</body>
</html>
