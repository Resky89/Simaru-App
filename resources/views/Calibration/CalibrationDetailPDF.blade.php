<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Calibration Detail Report</title>
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
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-grid td {
            padding: 5px;
            vertical-align: top;
            font-size: 11px;
        }
        .info-grid .label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        .info-grid .value {
            color: #333;
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
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .status-scheduled {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        .result-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .result-pass {
            background-color: #d4edda;
            color: #155724;
        }
        .result-fail {
            background-color: #f8d7da;
            color: #721c24;
        }
        .result-unknown {
            background-color: #fff3cd;
            color: #856404;
        }
        .notes-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            padding: 10px;
            font-size: 11px;
            border-radius: 4px;
        }
        .certificate-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            padding: 10px;
            font-size: 11px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CALIBRATION DETAIL REPORT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <div class="content-section">
        <table class="info-grid">
            <tr>
                <td class="label">Asset:</td>
                <td class="value">
                    <div>{{ $calibration['asset_name'] ?? 'N/A' }}</div>
                    <div style="color: #666; font-size: 10px;">{{ $calibration['asset_code'] ?? 'N/A' }}</div>
                </td>
                <td class="label">Planning Date:</td>
                <td class="value">{{ isset($calibration['planning_calibration_date']) && $calibration['planning_calibration_date'] ? date('d M Y', strtotime($calibration['planning_calibration_date'])) : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td class="value">
                    @php
                        $statusClass = '';
                        $status = $calibration['status_calibration'] ?? '';

                        if ($status == 'scheduled') {
                            $statusClass = 'status-scheduled';
                        } elseif ($status == 'in_progress') {
                            $statusClass = 'status-in-progress';
                        } elseif ($status == 'completed') {
                            $statusClass = 'status-completed';
                        } elseif ($status == 'overdue') {
                            $statusClass = 'status-overdue';
                        }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $status ?: 'Unknown')) }}
                    </span>
                </td>
                <td class="label">Actual Calibration Date:</td>
                <td class="value">
                    @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                        <span style="color: #856404;">Belum dilakukan kalibrasi</span>
                    @else
                        {{ date('d M Y', strtotime($calibration['actual_calibration_date'])) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Brand:</td>
                <td class="value">{{ $calibration['brand_name'] ?? 'N/A' }}</td>
                @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                <td class="label">Next Calibration Date:</td>
                <td class="value">{{ isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'] ? date('d M Y', strtotime($calibration['next_calibration_date'])) : 'N/A' }}</td>
                @else
                <td></td>
                <td></td>
                @endif
            </tr>
            <tr>
                <td class="label">Serial Number:</td>
                <td class="value">{{ $calibration['serial_number'] ?? 'N/A' }}</td>
                @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                <td class="label">Certificate Number:</td>
                <td class="value">{{ $calibration['certificate_number'] ?? 'N/A' }}</td>
                @else
                <td></td>
                <td></td>
                @endif
            </tr>
            <tr>
                <td class="label">Location:</td>
                <td class="value">
                    @php
                        $locationText = 'N/A';
                        if(isset($calibration['location'])) {
                            $locationParts = [];
                            if(!empty($calibration['location']['room_name'])) $locationParts[] = $calibration['location']['room_name'];
                            if(!empty($calibration['location']['floor_number'])) $locationParts[] = $calibration['location']['floor_number'];
                            if(!empty($calibration['location']['building_name'])) $locationParts[] = $calibration['location']['building_name'];
                            if(count($locationParts) > 0) {
                                $locationText = implode(' | ', $locationParts);
                            }
                        }
                    @endphp
                    {{ $locationText }}
                </td>
                @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                <td class="label">Result:</td>
                <td class="value">
                    @php
                        $resultClass = '';
                        $result = $calibration['calibration_result'] ?? '';
                        $resultText = 'N/A';

                        if ($result == 'pass') {
                            $resultClass = 'result-pass';
                            $resultText = 'Pass';
                        } elseif ($result == 'fail') {
                            $resultClass = 'result-fail';
                            $resultText = 'Fail';
                        } elseif ($result == 'unknown') {
                            $resultClass = 'result-unknown';
                            $resultText = 'Unknown';
                        }
                    @endphp
                    <span class="result-badge {{ $resultClass }}">
                        {{ $resultText }}
                    </span>
                </td>
                @else
                <td></td>
                <td></td>
                @endif
            </tr>
        </table>
    </div>

    <!-- Notes section -->
    <div class="content-section">
        <div class="section-title">Notes</div>
        <div class="notes-box">
            @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                <p style="color: #856404; margin: 0;">Belum dilakukan kalibrasi</p>
            @else
                <p style="margin: 0;">{{ $calibration['notes'] ?? 'No notes available' }}</p>
            @endif
        </div>
    </div>

    @if(!empty($calibration['certificate_file_path']))
    <div class="content-section">
        <div class="section-title">Certificate File</div>
        <div class="certificate-box">
            @if(!empty($calibration['certificate_file_base64']))
            <!-- Certificate is an image, display it -->
            <p style="margin: 0;"><strong>Certificate File:</strong> {{ basename($calibration['certificate_file_path']) }}</p>
            <div style="margin-top: 10px; text-align: center;">
                <img src="data:image/jpeg;base64,{{ $calibration['certificate_file_base64'] }}"
                     style="max-width: 100%; max-height: 400px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;" />
            </div>
            @else
            <!-- Certificate is a document, make filename clickable -->
            <p style="margin: 0;">
                <strong>Certificate File:</strong>
                <a href="{{ $calibration['certificate_file_url'] ?? '#' }}" style="color: #0066cc; text-decoration: underline;">
                    {{ basename($calibration['certificate_file_path']) }}
                </a>
            </p>
            @endif
        </div>
    </div>
    @endif

    <!-- History section (if applicable) -->
    @if(!empty($calibration['history']) && count($calibration['history']) > 0)
    <div class="content-section">
        <div class="section-title">History</div>
        <table class="data-table striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calibration['history'] as $entry)
                <tr>
                    <td>{{ date('d M Y H:i', strtotime($entry['created_at'])) }}</td>
                    <td>{{ $entry['action'] }}</td>
                    <td>{{ $entry['user_name'] }}</td>
                    <td>{{ $entry['details'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Asset Monitoring System - Calibration Detail Report</p>
    </div>
</body>
</html>
