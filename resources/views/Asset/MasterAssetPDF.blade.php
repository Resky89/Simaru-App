<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Assets Report</title>
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
        .filters {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .filters strong {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .asset-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MASTER ASSETS REPORT</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    <div class="filters">
        @if(!empty($search))
        <p><strong>Search:</strong> {{ $search }}</p>
        @endif

        @if(!empty($assetType))
        <p><strong>Asset Type:</strong> {{ ucfirst(str_replace('_', ' ', $assetType)) }}</p>
        @endif

        @if(!empty($brandId) && isset($brandMap[$brandId]))
        <p><strong>Brand:</strong> {{ $brandMap[$brandId]['brand_name'] ?? 'Unknown' }}</p>
        @endif

        @if(!empty($subcategoryId) && isset($subcategoryMap[$subcategoryId]))
        <p><strong>Category:</strong> {{ $subcategoryMap[$subcategoryId]['subcategory_name'] ?? 'Unknown' }}</p>
        @endif

        <p><strong>Sort Order:</strong>
            @switch($sort)
                @case('newest')
                    Newest First
                    @break
                @case('oldest')
                    Oldest First
                    @break
                @case('name_asc')
                    Name (A-Z)
                    @break
                @case('name_desc')
                    Name (Z-A)
                    @break
                @default
                    {{ ucfirst(str_replace('_', ' ', $sort)) }}
            @endswitch
        </p>
    </div>

    <table class="striped">
        <thead>
            <tr>
                <th>Asset Code</th>
                <th>Asset Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Type</th>
                <th>Properties</th>
            </tr>
        </thead>
        <tbody>
            @forelse($masterAssets as $asset)
                <tr>
                    <td>{{ $asset['asset_master_code'] ?? '-' }}</td>
                    <td>{{ $asset['asset_name'] ?? '-' }}</td>
                    <td>
                        @if(isset($asset['brand_id']) && isset($brandMap[$asset['brand_id']]))
                            {{ $brandMap[$asset['brand_id']]['brand_name'] ?? '-' }}
                        @else
                            {{ $asset['brand_name'] ?? '-' }}
                        @endif
                    </td>
                    <td>
                        @if(isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']]))
                            {{ $subcategoryMap[$asset['subcategory_id']]['subcategory_name'] ?? '-' }}
                        @else
                            {{ $asset['subcategory_name'] ?? '-' }}
                        @endif
                    </td>
                    <td>
                        <span class="asset-type">
                            @if(isset($asset['asset_type']))
                                {{ ucfirst(str_replace('_', ' ', $asset['asset_type'])) }}
                            @elseif(isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']]['asset_type']))
                                {{ ucfirst(str_replace('_', ' ', $subcategoryMap[$asset['subcategory_id']]['asset_type'])) }}
                            @else
                                -
                            @endif
                        </span>
                    </td>
                    <td>
                        @if(isset($asset['is_depreciable']) || isset($asset['needs_calibration']))
                            @if(isset($asset['is_depreciable']) && $asset['is_depreciable'])
                                <div>Depreciable</div>
                            @endif
                            @if(isset($asset['needs_calibration']) && $asset['needs_calibration'])
                                <div>Needs Calibration</div>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No master assets found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Asset Monitoring System - Master Assets Report</p>
    </div>
</body>
</html>
