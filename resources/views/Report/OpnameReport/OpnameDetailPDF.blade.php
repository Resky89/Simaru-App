<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Asset Opname Detail Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            color: #213268;
        }
        h2 {
            font-size: 14px;
            margin-top: 20px;
            color: #213268;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.info {
            margin-bottom: 20px;
        }
        table.info td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .label {
            font-weight: bold;
            width: 150px;
            background-color: #f5f5f5;
        }
        th {
            background-color: #213268;
            color: white;
            padding: 5px;
            text-align: left;
            font-size: 12px;
            border: 1px solid #213268;
        }
        td {
            padding: 5px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .found {
            color: green;
        }
        .missing {
            color: red;
        }
        .misplaced {
            color: orange;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Asset Opname Detail Report</h1>
    <p style="text-align: center;">{{ $opnameCode }}</p>

    <table class="info">
        <tr>
            <td class="label">Opname Code:</td>
            <td>{{ $opnameCode }}</td>
        </tr>
        <tr>
            <td class="label">Room:</td>
            <td>{{ isset($roomInfo['room_name']) ? $roomInfo['room_name'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Floor:</td>
            <td>{{ isset($roomInfo['floor_number']) ? $roomInfo['floor_number'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Building:</td>
            <td>{{ isset($roomInfo['building_name']) ? $roomInfo['building_name'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Date Created:</td>
            <td>{{ isset($roomInfo['created_at']) ? date('d M Y, H:i', strtotime($roomInfo['created_at'])) : date('d M Y, H:i') }}</td>
        </tr>
    </table>

    <h2>Asset Summary</h2>
    <table>
        <tr>
            <th>Total Assets</th>
            <th>Scanned Assets</th>
            <th>Found Assets</th>
            <th>Missing Assets</th>
            <th>Misplaced Assets</th>
        </tr>
        <tr>
            <td style="text-align: center;">{{ isset($summary['total_assets']) ? $summary['total_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['scanned_assets']) ? $summary['scanned_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['found_assets']) ? $summary['found_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['missing_assets']) ? $summary['missing_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['misplaced_assets']) ? $summary['misplaced_assets'] : '0' }}</td>
        </tr>
    </table>

    <h2>Asset Details</h2>
    @if(!empty($details))
    <table>
        <tr>
            <th>Asset Code</th>
            <th>Description</th>
            <th>Scan Date</th>
            <th>Status</th>
            <th>Expected Location</th>
            <th>Actual Location</th>
            <th>Scanned By</th>
        </tr>
        @foreach($details as $asset)
        <tr>
            <td>{{ isset($asset['asset_code']) ? $asset['asset_code'] : '-' }}</td>
            <td>{{ isset($asset['asset_description']) ? $asset['asset_description'] : '-' }}</td>
            <td>
                @if(isset($asset['scan_date']))
                    {{ date('d/m/Y', strtotime($asset['scan_date'])) }}
                @else
                    -
                @endif
            </td>
            <td>
                @if(isset($asset['scan_status']))
                    @if($asset['scan_status'] == 'found')
                        <span class="found">Found</span>
                    @elseif($asset['scan_status'] == 'missing')
                        <span class="missing">Missing</span>
                    @elseif($asset['scan_status'] == 'misplaced')
                        <span class="misplaced">Misplaced</span>
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
    </table>
    @else
    <p>No asset data available for this opname report.</p>
    @endif

    <div class="footer">
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        <p>This is an automatically generated report. Please verify all information with physical assets.</p>
    </div>
</body>
</html>
