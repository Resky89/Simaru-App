<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/png">
    <title>Print QR Codes</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Print-specific styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            @page {
                size: a4 portrait;
                margin: 10mm;
            }
        }

        /* Container styles */
        .container {
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        /* Header styles */
        .header {
            padding: 15px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #213268;
        }

        /* Button styles */
        .btn {
            display: inline-block;
            background: #213268;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #152451;
        }

        /* QR grid layout */
        .qr-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-row {
            page-break-inside: avoid;
            margin-bottom: 10mm;
        }

        .qr-cell {
            width: 100%;
            vertical-align: top;
            padding: 5mm;
            position: relative;
        }

        /* QR item container - size will be set by inline style */
        .qr-item {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 4mm;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            position: relative;
            height: 50mm; /* Fixed height for sticker paper */
            margin: 0 auto;
            display: flex;
            align-items: center;
        }

        /* Scissors guide for cutting */
        .cut-line-horizontal {
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px dashed #999;
        }

        .cut-line-top {
            top: 0;
        }

        .cut-line-bottom {
            bottom: 0;
        }

        /* QR image container */
        .qr-image {
            height: 40mm; /* Fixed height for QR image */
            width: 40mm; /* Fixed width for QR image */
            flex-shrink: 0;
        }

        /* Size-specific QR image adjustments */
        .qr-item.size-80 .qr-image {
            height: 35mm; /* Smaller height for 80mm width */
            width: 35mm; /* Smaller width for 80mm width */
        }

        .qr-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-details {
            flex-grow: 1;
            padding-left: 4mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .qr-code {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2mm;
            color: #213268;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-category {
            font-size: 10px;
            font-weight: 500;
            margin-bottom: 2mm;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-meta {
            font-size: 8px;
            margin-bottom: 0.5mm;
            color: #444;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .page-header {
            text-align: center;
            margin-bottom: 5mm;
            font-size: 16px;
            font-weight: bold;
            color: #213268;
            padding-bottom: 2mm;
            border-bottom: 1px solid #eee;
        }

        .page-footer {
            text-align: center;
            font-size: 8px;
            margin-top: 5mm;
            color: #666;
            padding-top: 2mm;
            border-top: 1px solid #eee;
        }

        .placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            border: 1px dashed #ccc;
            color: #666;
            font-size: 8px;
            text-align: center;
        }

        /* Size-specific adjustments to match sticker paper dimensions */
        .qr-item.size-80 {
            width: 80mm; /* Matches 80x50mm sticker paper */
        }

        .qr-item.size-100 {
            width: 100mm; /* Matches 100x50mm sticker paper */
        }

        /* Font size adjustments for different widths */
        .qr-item.size-80 .qr-code {
            font-size: 11px;
            margin-bottom: 1.5mm;
        }

        .qr-item.size-100 .qr-code {
            font-size: 13px;
        }

        .qr-item.size-80 .qr-category,
        .qr-item.size-80 .qr-name {
            font-size: 9px;
            margin-bottom: 1.5mm;
        }

        .qr-item.size-100 .qr-category,
        .qr-item.size-100 .qr-name {
            font-size: 11px;
        }

        .qr-item.size-80 .qr-meta {
            font-size: 7px;
            margin-bottom: 0.3mm;
            line-height: 1.1;
        }

        .qr-item.size-100 .qr-meta {
            font-size: 9px;
            line-height: 1.3;
        }

        /* Location details section */
        .location-details {
            margin-top: 1mm;
            border-top: 0.2mm dotted #eee;
            padding-top: 0.5mm;
        }

        .qr-item.size-80 .location-details {
            margin-top: 0.5mm;
            padding-top: 0.3mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with print button - will not be printed -->
        <div class="header no-print">
            <h1>Print QR Codes</h1>
            <div>
                <button onclick="window.print()" class="btn">Print</button>
                <button onclick="window.close()" class="btn" style="background: #666;">Close</button>
            </div>
        </div>

        <div class="page-header">
            QR CODE PRINT - UKURAN STIKER {{ $containerWidth }}x50mm
        </div>

        <table class="qr-grid">
            @foreach($qrData as $asset)
            <tr class="qr-row">
                <td class="qr-cell">
                    <div class="cut-line-horizontal cut-line-top"></div>
                    <div class="cut-line-horizontal cut-line-bottom"></div>

                    <div class="qr-item size-{{ $containerWidth }}">
                        <div class="qr-image">
                            @if(isset($asset['qr_base64']) && $asset['qr_base64'])
                                <img src="{{ $asset['qr_base64'] }}" alt="QR Code">
                            @else
                                <div class="placeholder">
                                    QR Not Available
                                </div>
                            @endif
                        </div>
                        <div class="qr-details">
                            <div class="qr-code">{{ $asset['asset_code'] ?? 'No Code' }}</div>
                            <div class="qr-category">
                                @php
                                    // Try to get subcategory name from different possible locations in the data structure
                                    $subcategoryName = null;
                                    if (isset($asset['subcategory']) && isset($asset['subcategory']['subcategory_name'])) {
                                        $subcategoryName = $asset['subcategory']['subcategory_name'];
                                    } elseif (isset($asset['category']) && isset($asset['category']['subcategory_name'])) {
                                        $subcategoryName = $asset['category']['subcategory_name'];
                                    } elseif (isset($asset['subcategory_name'])) {
                                        $subcategoryName = $asset['subcategory_name'];
                                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory']) && isset($asset['asset_master']['subcategory']['subcategory_name'])) {
                                        $subcategoryName = $asset['asset_master']['subcategory']['subcategory_name'];
                                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory_name'])) {
                                        $subcategoryName = $asset['asset_master']['subcategory_name'];
                                    }
                                @endphp
                                {{ $subcategoryName ?? 'N/A' }}
                            </div>
                            <div class="qr-name">
                                @php
                                    // Try to get asset name from different possible locations in the data structure
                                    $assetName = null;
                                    if (isset($asset['asset_name'])) {
                                        $assetName = $asset['asset_name'];
                                    } elseif (isset($asset['asset_master_name'])) {
                                        $assetName = $asset['asset_master_name'];
                                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['asset_name'])) {
                                        $assetName = $asset['asset_master']['asset_name'];
                                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['asset_master_name'])) {
                                        $assetName = $asset['asset_master']['asset_master_name'];
                                    }
                                @endphp
                                {{ $assetName ?? 'Unknown Asset' }}
                            </div>
                            <div class="qr-meta">Dibuat: {{ isset($asset['created_at']) ? date('d/m/Y', strtotime($asset['created_at'])) : date('d/m/Y') }}</div>

                            @php
                                // Try to get location information from different possible locations in the data structure
                                $buildingName = 'N/A';
                                $roomName = 'N/A';
                                $floorNumber = 'Lantai ?';

                                // Get room name
                                if (isset($asset['room']) && isset($asset['room']['room_name'])) {
                                    $roomName = $asset['room']['room_name'];
                                } elseif (isset($asset['location']) && isset($asset['location']['room_name'])) {
                                    $roomName = $asset['location']['room_name'];
                                } elseif (isset($asset['room_name'])) {
                                    $roomName = $asset['room_name'];
                                }

                                // Get building name
                                if (isset($asset['room']) && isset($asset['room']['building']) && isset($asset['room']['building']['building_name'])) {
                                    $buildingName = $asset['room']['building']['building_name'];
                                } elseif (isset($asset['room']) && isset($asset['room']['building_name'])) {
                                    $buildingName = $asset['room']['building_name'];
                                } elseif (isset($asset['location']) && isset($asset['location']['building_name'])) {
                                    $buildingName = $asset['location']['building_name'];
                                } elseif (isset($asset['building_name'])) {
                                    $buildingName = $asset['building_name'];
                                }

                                // Get floor number
                                if (isset($asset['room']) && isset($asset['room']['floor_number'])) {
                                    $floorNumber = $asset['room']['floor_number'];
                                } elseif (isset($asset['location']) && isset($asset['location']['floor_number'])) {
                                    $floorNumber = $asset['location']['floor_number'];
                                } elseif (isset($asset['floor_number'])) {
                                    $floorNumber = $asset['floor_number'];
                                }
                            @endphp
                            <div class="location-details">
                                <div class="qr-meta">Gedung: {{ $buildingName }}</div>
                                <div class="qr-meta">Lantai: {{ $floorNumber }}</div>
                                <div class="qr-meta">Ruangan: {{ $roomName }}</div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>

        <div class="page-footer">
            Generated on {{ date('Y-m-d H:i:s') }}
        </div>
    </div>

    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            // Short delay to ensure all images are loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
