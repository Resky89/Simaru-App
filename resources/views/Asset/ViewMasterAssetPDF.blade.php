<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Asset Detail Report</title>
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
            border-bottom: 1px solid #28356B;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #28356B;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .asset-info {
            margin-bottom: 20px;
        }
        .asset-info h2 {
            font-size: 16px;
            color: #28356B;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
        }
        .info-value {
            flex: 1;
        }
        .asset-image {
            text-align: center;
            margin: 15px 0;
        }
        .asset-image img {
            max-width: 250px;
            max-height: 250px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #28356B;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }
        td {
            border-top: 1px solid #eef1f4;
            padding: 8px;
            font-size: 10px;
            vertical-align: top;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }
        .status-badge-yes {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge-no {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Condition badge styles */
        .condition-good {
            background-color: #d4edda;
            color: #155724;
        }
        .condition-fair {
            background-color: #fff3cd;
            color: #856404;
        }
        .condition-poor {
            background-color: #ffe5d0;
            color: #ad4e00;
        }
        .condition-high-damage {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Status badge styles */
        .status-available {
            background-color: #d4edda;
            color: #155724;
        }
        .status-in-use {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-maintenance {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-broken {
            background-color: #ffe5d0;
            color: #ad4e00;
        }
        .status-dispose {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MASTER ASSET DETAIL REPORT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <!-- Master Asset Image Section -->
    <div class="asset-info">
        <h2>Master Asset Image</h2>
        <div class="image-container" style="text-align: center; margin: 10px 0; padding: 10px; background-color: white; border: 1px solid #eee;">
            @if(!empty($masterAsset['image_base64']))
                <img src="data:image/jpeg;base64,{{ $masterAsset['image_base64'] }}" alt="Master Asset Image" style="max-width: 300px; max-height: 300px;">
            @elseif(isset($masterAsset['reference_image_path']) && $masterAsset['reference_image_path'])
                <img src="data:image/jpeg;base64,{{ $masterAsset['reference_image_base64'] }}" alt="Master Asset Image" style="max-width: 300px; max-height: 300px;">
            @else
                <p style="color: #999; font-style: italic;">No image available</p>
            @endif
        </div>
    </div>

    <!-- Master Asset Information Section -->
    <div class="asset-info">
        <h2>Master Asset Information</h2>

        <!-- Basic Identification -->
        <div class="info-row">
            <div class="info-label">Master Asset Code:</div>
            <div class="info-value">{{ $masterAsset['asset_master_code'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Asset Name:</div>
            <div class="info-value">{{ $masterAsset['asset_name'] ?? 'N/A' }}</div>
        </div>

        <!-- Classification -->
        <div class="info-row">
            <div class="info-label">Asset Type:</div>
            <div class="info-value">{{ ucfirst($masterAsset['asset_type'] ?? 'N/A') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Subcategory:</div>
            <div class="info-value">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Brand:</div>
            <div class="info-value">{{ $masterAsset['brand_name'] ?? 'N/A' }}</div>
        </div>

        <!-- Asset Characteristics -->
        <div class="info-row">
            <div class="info-label">Depreciable:</div>
            <div class="info-value">
                @if(isset($masterAsset['is_depreciable']) && $masterAsset['is_depreciable'])
                    <span class="status-badge status-badge-yes">Yes</span>
                @else
                    <span class="status-badge status-badge-no">No</span>
                @endif
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Needs Calibration:</div>
            <div class="info-value">
                @if(isset($masterAsset['needs_calibration']) && $masterAsset['needs_calibration'])
                    <span class="status-badge status-badge-yes">Yes</span>
                @else
                    <span class="status-badge status-badge-no">No</span>
                @endif
            </div>
        </div>

        <!-- Additional Information -->
        <div class="info-row">
            <div class="info-label">Created At:</div>
            <div class="info-value">{{ isset($masterAsset['created_at']) ? date('d M Y H:i', strtotime($masterAsset['created_at'])) : 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value">{{ $masterAsset['description'] ?? 'No description available' }}</div>
        </div>
    </div>

    <!-- Asset Units Section -->
    @if(isset($masterAsset['linked_assets']) && count($masterAsset['linked_assets']) > 0)
    <div class="asset-info">
        <h2>Linked Asset Units ({{ count($masterAsset['linked_assets']) }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Serial Number</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masterAsset['linked_assets'] as $asset)
                <tr>
                    <td>{{ $asset['asset_code'] ?? 'N/A' }}</td>
                    <td>{{ $asset['serial_number'] ?? 'N/A' }}</td>
                    <td>
                        @php
                            $conditionClass = 'status-badge';

                            if(isset($asset['condition'])) {
                                switch(strtolower($asset['condition'])) {
                                    case 'good':
                                        $conditionClass .= ' condition-good';
                                        break;
                                    case 'fair':
                                        $conditionClass .= ' condition-fair';
                                        break;
                                    case 'poor':
                                        $conditionClass .= ' condition-poor';
                                        break;
                                    case 'high damage':
                                        $conditionClass .= ' condition-high-damage';
                                        break;
                                }
                            }
                        @endphp
                        <span class="{{ $conditionClass }}">
                            {{ ucfirst($asset['condition'] ?? 'Unknown') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = 'status-badge';

                            if(isset($asset['current_status'])) {
                                switch(strtolower($asset['current_status'])) {
                                    case 'available':
                                        $statusClass .= ' status-available';
                                        break;
                                    case 'in_use':
                                        $statusClass .= ' status-in-use';
                                        break;
                                    case 'maintenance':
                                        $statusClass .= ' status-maintenance';
                                        break;
                                    case 'broken':
                                        $statusClass .= ' status-broken';
                                        break;
                                    case 'dispose':
                                        $statusClass .= ' status-dispose';
                                        break;
                                }
                            }
                        @endphp
                        <span class="{{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $asset['current_status'] ?? 'Unknown')) }}
                        </span>
                    </td>
                    <td>
                        {{ $asset['room_name'] ?? 'N/A' }}
                        @if(isset($asset['building_name']))
                        <br><span style="font-size: 9px; color: #666;">{{ $asset['building_name'] }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="asset-info">
        <h2>Linked Asset Units</h2>
        <p style="color: #999; font-style: italic; text-align: center; padding: 20px;">No assets linked to this master asset.</p>
    </div>
    @endif

    <div class="footer">
        <p>Asset Monitoring System - Master Asset Detail Report</p>
    </div>
</body>
</html>
