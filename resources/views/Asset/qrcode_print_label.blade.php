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
            align-items: center;
        }

        /* Adjust content layout based on container width */
        @if($containerWidth == 80)
        .label-content {
            padding: 0 1mm;
        }
        @elseif($containerWidth == 100)
        .label-content {
            padding: 0 2mm;
        }
        @endif

        .qr-image {
            height: 100%;
            width: {{ $containerWidth / 2.2 }}mm; /* Slightly smaller to give more space to text */
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Size adjustments for different label sizes */
        @if($qrSize == 60)
        .qr-image {
            width: 22mm; /* Slightly smaller */
        }

        .qr-image img {
            max-height: 20mm;
            max-width: 20mm;
        }
        @elseif($containerWidth == 80)
        .qr-image {
            width: 34mm;
        }

        .qr-image img {
            max-height: 32mm;
            max-width: 32mm;
            object-fit: contain;
        }
        @elseif($containerWidth == 100)
        .qr-image {
            width: 40mm;
        }

        .qr-image img {
            max-height: 38mm;
            max-width: 38mm;
            object-fit: contain;
        }
        @else
        .qr-image img {
            max-height: 40mm;
            max-width: 40mm;
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
            max-width: {{ $containerWidth / 2 }}mm;
        }

        /* Adjust details section based on container width */
        @if($containerWidth == 80)
        .qr-details {
            padding-left: 1mm;
            max-width: 42mm;
        }
        @elseif($containerWidth == 100)
        .qr-details {
            padding-left: 3mm;
            max-width: 55mm;
        }
        @endif

        /* Adjust spacing between elements */
        .qr-details > div {
            margin-bottom: {{ $qrSize == 60 ? '0.5mm' : '1mm' }};
        }

        /* Ensure proper spacing for the last element */
        .qr-details > div:last-child {
            margin-bottom: 0;
        }

        .qr-code {
            font-weight: bold;
            font-size: {{ $qrSize == 60 ? '9' : '11' }}px;
            margin-bottom: 2mm;
            white-space: normal;
            word-break: break-word;
            overflow: visible;
            text-overflow: clip;
            max-height: {{ $qrSize == 60 ? '10mm' : '15mm' }};
            line-height: {{ $qrSize == 60 ? '10px' : '12px' }};
        }

        /* Adjust font sizes based on container width */
        @if($containerWidth == 80)
        .qr-code {
            font-size: 10px;
            line-height: 11px;
        }

        .qr-name {
            font-size: 9px;
        }

        .qr-category {
            font-size: 8px;
        }

        .qr-meta {
            font-size: 7px;
        }
        @elseif($containerWidth == 100)
        .qr-code {
            font-size: 12px;
            line-height: 13px;
        }

        .qr-name {
            font-size: 11px;
        }

        .qr-category {
            font-size: 10px;
        }

        .qr-meta {
            font-size: 9px;
        }
        @endif

        /* Special handling for asset code on small labels */
        @if($qrSize == 60)
        .qr-code {
            font-size: 9px;
            line-height: 10px;
            margin-bottom: 1mm;
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

        /* Special handling for very long asset codes */
        .long-code {
            font-size: {{ $qrSize == 60 ? '7px' : '9px' }};
            line-height: {{ $qrSize == 60 ? '8px' : '10px' }};
            display: inline-block;
            word-break: break-all;
        }

        /* Adjust long code handling based on container width */
        @if($containerWidth == 80)
        .long-code {
            font-size: 8px;
            line-height: 9px;
        }
        @elseif($containerWidth == 100)
        .long-code {
            font-size: 10px;
            line-height: 11px;
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
                <div class="qr-code">
                    @php
                        $assetCode = $asset['asset_code'] ?? 'No Code';
                        // If asset code is very long, add a small CSS class
                        $codeLength = strlen($assetCode);

                        // Adjust threshold based on container width
                        $longCodeThreshold = 20; // Default
                        if ($containerWidth == 80) {
                            $longCodeThreshold = 18;
                        } elseif ($containerWidth == 100) {
                            $longCodeThreshold = 22;
                        } elseif ($qrSize == 60) {
                            $longCodeThreshold = 16;
                        }

                        $longCodeClass = $codeLength > $longCodeThreshold ? 'long-code' : '';
                    @endphp
                    <span class="{{ $longCodeClass }}">{{ $assetCode }}</span>
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
