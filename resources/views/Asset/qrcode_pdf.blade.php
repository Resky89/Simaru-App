    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Asset QR Codes</title>
        <style>
            @page {
                size: a4 portrait;
                margin: 10mm;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }

            .qr-grid {
                width: 100%;
                border-collapse: collapse;
            }

            .qr-row {
                page-break-inside: avoid;
            }

            .qr-cell {
                width: 50%;
                vertical-align: top;
                padding: 5mm;
                position: relative;
            }

            .qr-item {
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 4mm;
                background: white;
                box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                height: 30mm;
                position: relative;
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

            .qr-image {
                width: 25mm;
                height: 25mm;
                position: absolute;
                left: 4mm;
                top: 4mm;
            }

            .qr-image img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .qr-details {
                position: absolute;
                left: 32mm; /* QR width + left + margin */
                top: 4mm;
                right: 4mm;
            }

            .qr-code {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 1mm;
                color: #213268;
            }

            .qr-category {
                font-size: 10px;
                font-weight: 500;
                margin-bottom: 1mm;
                color: #555;
            }

            .qr-name {
                font-size: 10px;
                font-weight: bold;
                margin-bottom: 1mm;
            }

            .qr-meta {
                font-size: 8px;
                margin-bottom: 0.5mm;
                color: #444;
            }

            .location-info {
                position: absolute;
                bottom: 4mm;
                left: 4mm;
                font-size: 8px;
                color: #444;
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
        </style>
    </head>
    <body>
        <div class="page-header">
            QR CODE PRINT
        </div>

        <table class="qr-grid">
            @php
                $rows = array_chunk($qrData, 2);
            @endphp

            @foreach($rows as $row)
            <tr class="qr-row">
                @foreach($row as $asset)
                <td class="qr-cell">
                    <div class="cut-line-horizontal cut-line-top"></div>
                    <div class="cut-line-horizontal cut-line-bottom"></div>

                    <div class="qr-item">
                        <div class="qr-image">
                            @if(isset($asset['qr_base64']))
                                <img src="{{ $asset['qr_base64'] }}">
                            @else
                                <div class="placeholder">
                                    QR Not Available
                                </div>
                            @endif
                        </div>
                        <div class="qr-details">
                            <div class="qr-code">{{ $asset['asset_code'] }}</div>
                            <div class="qr-category">{{ isset($asset['category']['subcategory_name']) ? $asset['category']['subcategory_name'] : 'N/A' }}</div>
                            <div class="qr-name">{{ $asset['asset_name'] }}</div>
                            <div class="qr-meta">Dibuat: {{ date('d/m/Y', strtotime($asset['created_at'] ?? now())) }}</div>
                            <div class="qr-meta">Lokasi: {{ isset($asset['location']['room_name']) ? $asset['location']['room_name'] : 'N/A' }}</div>
                            <div class="qr-meta">{{ isset($asset['location']['floor_number']) ? $asset['location']['floor_number'] : 'Lantai ?' }}</div>
                        </div>
                    </div>
                </td>
                @endforeach

                @if(count($row) < 2)
                <td class="qr-cell"></td>
                @endif
            </tr>
            @endforeach
        </table>

        <div class="page-footer">
            Generated on {{ date('Y-m-d H:i:s') }}
        </div>
    </body>
    </html>
