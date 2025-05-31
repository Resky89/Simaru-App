<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Label Print</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        /* Label printer optimized styles */
        @page {
            size: {{ $containerWidth }}mm 50mm;
            margin: 0;
        }

        .label-container {
            width: {{ $containerWidth }}mm;
            height: 50mm;
            padding: 2mm;
            overflow: hidden;
            page-break-after: always;
            position: relative;
        }

        /* Special adjustments for 60mm width labels */
        @if($qrSize == 60)
        @page {
            size: 60mm 40mm;
            margin: 0;
        }

        .label-container {
            width: 60mm;
            height: 40mm;
        }
        @endif

        .label-content {
            display: flex;
            height: 100%;
            width: 100%;
        }

        .qr-image {
            height: 100%;
            width: {{ $containerWidth / 2 }}mm;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Size adjustments for smaller labels */
        @if($qrSize == 60)
        .qr-image {
            width: 24mm;
        }

        .qr-image img {
            max-height: 22mm;
            max-width: 22mm;
        }
        @else
        .qr-image img {
            max-height: 42mm;
            max-width: 42mm;
            object-fit: contain;
        }
        @endif

        .qr-details {
            flex: 1;
            padding-left: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .qr-code {
            font-weight: bold;
            font-size: {{ $qrSize == 60 ? '9' : '11' }}px;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Special handling for asset code on small labels */
        @if($qrSize == 60)
        .qr-code {
            font-size: 9px;
            line-height: 10px;
            margin-bottom: 1mm;
            white-space: normal; /* Allow wrapping if needed */
            word-break: break-all; /* Break words at any character */
            overflow: visible; /* Don't hide overflowing content */
            text-overflow: clip;
            max-height: 10mm;
        }
        @endif

        .qr-name {
            font-weight: bold;
            font-size: {{ $qrSize == 60 ? '8' : '10' }}px;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-category {
            font-size: {{ $qrSize == 60 ? '7' : '9' }}px;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-meta {
            font-size: {{ $qrSize == 60 ? '6' : '8' }}px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.5mm;
        }

        /* For 60mm labels, adjust the location details */
        @if($qrSize == 60)
        .location-details {
            margin-top: 0.5mm;
            padding-top: 0.5mm;
        }

        .qr-meta {
            line-height: 7px;
            margin-bottom: 0.3mm;
        }
        @else
        .location-details {
            margin-top: 1mm;
            border-top: 0.2mm dotted #eee;
            padding-top: 0.5mm;
        }
        @endif

        /* Controls for on-screen (not printed) */
        .controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #213268;
            padding: 10px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .btn {
            background: white;
            color: #213268;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
            font-weight: bold;
        }

        @media print {
            .controls {
                display: none;
            }
        }

        /* Printer-specific optimizations */
        @if($printerType == 'zebra')
        body {
            font-family: 'Arial Narrow', Arial, sans-serif; /* Better for Zebra printers */
        }
        @endif

        @if($printerType == 'dymo')
        .label-container {
            padding: 1mm; /* Less padding for Dymo */
        }
        .qr-image img {
            max-height: 95%; /* Slightly smaller QR for Dymo */
        }
        @endif
    </style>
</head>
<body>
    <!-- Controls (visible only on screen) -->
    <div class="controls">
        <span>{{ $containerWidth }}x50mm {{ ucfirst($printerType) }} Label</span>
        <button class="btn" onclick="window.print()">Print</button>
        <button class="btn" onclick="window.close()">Close</button>
    </div>

    <!-- Generate one label per asset -->
    @foreach($qrData as $asset)
    <div class="label-container">
        <div class="label-content">
            <div class="qr-image">
                @if(isset($asset['qr_base64']) && $asset['qr_base64'])
                    <img src="{{ $asset['qr_base64'] }}" alt="QR Code">
                @else
                    <div style="width: 90%; height: 90%; display: flex; justify-content: center; align-items: center; background: #f0f0f0; border: 1px dashed #ccc;">
                        QR Not Available
                    </div>
                @endif
            </div>
            <div class="qr-details">
                <div class="qr-code">{{ $asset['asset_code'] ?? 'No Code' }}</div>
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
    </div>
    @endforeach

    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            // Short delay to ensure all images are loaded
            setTimeout(function() {
                window.print();
            }, 800);
        };
    </script>
</body>
</html>
