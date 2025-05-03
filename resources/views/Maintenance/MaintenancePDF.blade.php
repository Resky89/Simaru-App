<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maintenance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #213268;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #213268;
            font-weight: bold;
        }
        .header p {
            font-size: 12px;
            margin: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #EEF1F4;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #213268;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:nth-child(odd) {
            background-color: white;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #EEF1F4;
            color: #666;
        }
        .status-new {
            background-color: #cce5ff;
            color: #004085;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-scheduled {
            background-color: #e0cffc;
            color: #5a3b94;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-in_progress {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-canceled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .filter-info {
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .asset-name {
            font-weight: bold;
        }
        .asset-code {
            color: #666;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MAINTENANCE SCHEDULE REPORT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <div class="filter-info">
        @if(!empty($search))
            <strong>Search:</strong> {{ $search }} |
        @endif
        @if(!empty($status))
            <strong>Status:</strong> {{ ucfirst($status) }} |
        @endif
        <strong>Sort:</strong> {{ $sort_by ?? 'created_at' }} ({{ $sort_order ?? 'desc' }})
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset</th>
                <th>Interval</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Assigned To</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $maintenance)
                <tr>
                    <td>{{ $maintenance['id'] ?? '-' }}</td>
                    <td>
                        <div class="asset-name">{{ $maintenance['asset_name'] ?? '-' }}</div>
                        <div class="asset-code">Code: {{ $maintenance['asset_code'] ?? '-' }}</div>
                    </td>
                    <td>{{ $maintenance['interval'] ?? '-' }}</td>
                    <td>{{ isset($maintenance['start_date']) ? date('d M Y', strtotime($maintenance['start_date'])) : '-' }}</td>
                    <td>{{ isset($maintenance['end_date']) ? date('d M Y', strtotime($maintenance['end_date'])) : '-' }}</td>
                    <td>{{ $maintenance['assigned_to'] ?? '-' }}</td>
                    <td>{{ $maintenance['vendor_name'] ?? '-' }}</td>
                    <td>
                        <span class="status-{{ strtolower($maintenance['status'] ?? 'unknown') }}">
                            {{ ucfirst($maintenance['status'] ?? 'Unknown') }}
                        </span>
                    </td>
                    <td>{{ isset($maintenance['created_at']) ? date('d M Y', strtotime($maintenance['created_at'])) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No maintenance schedules found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated from the Asset Monitoring System.</p>
        <p>© {{ date('Y') }} Asset Monitoring System. All rights reserved.</p>
    </div>
</body>
</html>
