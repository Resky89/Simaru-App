<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Detail Opname Aset</title>
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
    <h1>Laporan Detail Opname Aset</h1>
    <p style="text-align: center;">{{ $opnameCode }}</p>

    <table class="info">
        <tr>
            <td class="label">Kode Opname:</td>
            <td>{{ $opnameCode }}</td>
        </tr>
        <tr>
            <td class="label">Ruangan:</td>
            <td>{{ isset($roomInfo['room_name']) ? $roomInfo['room_name'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lantai:</td>
            <td>{{ isset($roomInfo['floor_number']) ? $roomInfo['floor_number'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Gedung:</td>
            <td>{{ isset($roomInfo['building_name']) ? $roomInfo['building_name'] : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Dibuat:</td>
            <td>{{ isset($roomInfo['created_at']) ? date('d M Y, H:i', strtotime($roomInfo['created_at'])) : date('d M Y, H:i') }}</td>
        </tr>
    </table>

    <h2>Ringkasan Aset</h2>
    <table>
        <tr>
            <th>Total Aset</th>
            <th>Terscan</th>
            <th>Ditemukan</th>
            <th>Hilang</th>
            <th>Salah Tempat</th>
        </tr>
        <tr>
            <td style="text-align: center;">{{ isset($summary['total_assets']) ? $summary['total_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['scanned_assets']) ? $summary['scanned_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['found_assets']) ? $summary['found_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['missing_assets']) ? $summary['missing_assets'] : '0' }}</td>
            <td style="text-align: center;">{{ isset($summary['misplaced_assets']) ? $summary['misplaced_assets'] : '0' }}</td>
        </tr>
    </table>

    <h2>Detail Aset</h2>
    @if(!empty($details))
    <table>
        <tr>
            <th>Kode Aset</th>
            <th>Deskripsi</th>
            <th>Tanggal Scan</th>
            <th>Status</th>
            <th>Lokasi Seharusnya</th>
            <th>Lokasi Aktual</th>
            <th>Discan Oleh</th>
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
                        <span class="found">Ditemukan</span>
                    @elseif($asset['scan_status'] == 'missing')
                        <span class="missing">Hilang</span>
                    @elseif($asset['scan_status'] == 'misplaced')
                        <span class="misplaced">Salah Tempat</span>
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
    <p>Tidak ada data aset tersedia untuk laporan opname ini.</p>
    @endif

    <div class="footer">
        <p>Dibuat pada: {{ date('Y-m-d H:i:s') }}</p>
        <p>Ini adalah laporan yang dibuat secara otomatis. Harap verifikasi semua informasi dengan aset fisik.</p>
    </div>
</body>
</html>
