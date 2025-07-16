<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Keluhan & Perbaikan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin-bottom: 10px;
        }

        .header img {
            max-width: 100%;
            height: auto;
            max-height: 50px;
        }

        .header-line {
            border-bottom: 2px solid #213268;
            margin-top: 3px;
            margin-bottom: 15px;
            clear: both;
        }

        .page-title {
            color: #213268;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #213268;
            padding-bottom: 5px;
            border-bottom: 1px solid #eef1f4;
            margin-bottom: 10px;
        }

        .subsection-title {
            font-size: 12px;
            font-weight: bold;
            color: #666;
            margin: 10px 0 5px 0;
        }

        .box {
            background-color: #f5f5f5;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .detail-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .detail-row {
            display: table-row;
        }

        .detail-cell {
            display: table-cell;
            padding: 5px;
            border: 1px solid #eee;
            background-color: white;
        }

        .detail-cell-title {
            font-size: 10px;
            color: #666;
            display: block;
            margin-bottom: 3px;
        }

        .detail-cell-value {
            font-size: 11px;
        }

        .col-50 {
            width: 50%;
            float: left;
            padding: 0 5px;
            box-sizing: border-box;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .image-container {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border: 1px solid #eee;
        }

        img {
            max-width: 100%;
            max-height: 300px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: normal;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Status Banner Styles */
        .status-banner {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #333;
            font-weight: bold;
        }

        .status-banner-pending {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
        }

        .status-banner-approved {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .status-banner-rejected {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .status-banner-in-progress {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
        }

        .status-banner-completed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI">
    </div>
    <div class="header-line"></div>

    <div class="page-title">DETAIL KELUHAN & PERBAIKAN</div>

    <!-- Status Banner Section -->
    @php
        $statusClass = 'status-banner-pending';
        $status = $complaint['status'] ?? '';
        $statusText = 'TIDAK DIKETAHUI';
        $statusDescription = '';
        $statusIcon = '';

        if ($status == 'new') {
            $statusClass = 'status-banner-pending';
            $statusText = 'BARU';
            $statusDescription = 'Keluhan baru dibuat';
        } elseif ($status == 'in progress') {
            $statusClass = 'status-banner-in-progress';
            $statusText = 'SEDANG DIPROSES';
            $statusDescription = 'Keluhan sedang dalam proses perbaikan';
        } elseif ($status == 'finished') {
            $statusClass = 'status-banner-completed';
            $statusText = 'SELESAI';
            $statusDescription = 'Keluhan telah selesai diperbaiki pada ' .
                (isset($complaint['finished_date']) && $complaint['finished_date'] && $complaint['finished_date'] != '-' ?
                    \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') :
                    'tanggal tidak tersedia');
        } elseif ($status == 'approved') {
            $statusClass = 'status-banner-approved';
            $statusText = 'DISETUJUI';
            $statusDescription = 'Keluhan telah disetujui untuk diperbaiki';
        } else {
            $statusClass = 'status-banner-pending';
            $statusText = 'TIDAK DIKETAHUI';
            $statusDescription = 'Status keluhan tidak diketahui';
        }
    @endphp

    <div class="status-banner {{ $statusClass }}">
        <div>Status: {{ $statusText }}</div>
        <div style="font-weight: normal; font-size: 11px; margin-top: 3px;">{{ $statusDescription }}</div>
    </div>

    <!-- Complaint Section -->
    <div class="section">
        <h2 class="section-title">INFORMASI KELUHAN</h2>

        <div class="clearfix">
            <!-- Complaint Image (Left Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Gambar Keluhan</h3>
                <div class="image-container">
                    @if(!empty($complaint['complaint_picture_base64']))
                        <img src="data:image/jpeg;base64,{{ $complaint['complaint_picture_base64'] }}" alt="Gambar Keluhan">
                    @else
                        <p style="color: #999; font-style: italic;">Gambar tidak tersedia</p>
                    @endif
                </div>
            </div>

            <!-- Complaint Details (Right Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Detail Keluhan</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-cell" colspan="2">
                            <span class="detail-cell-title">Deskripsi</span>
                            <span class="detail-cell-value">{{ $complaint['description'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Keluhan Oleh</span>
                            <span class="detail-cell-value">{{ $complaint['reporter_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <h3 class="subsection-title">Waktu</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Tanggal Keluhan</span>
                            <span
                                class="detail-cell-value">{{ isset($complaint['complaint_date']) && $complaint['complaint_date'] && $complaint['complaint_date'] != '-' ? \Carbon\Carbon::parse($complaint['complaint_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Tanggal Selesai</span>
                            <span
                                class="detail-cell-value">{{ isset($complaint['finished_date']) && $complaint['finished_date'] && $complaint['finished_date'] != '-' ? \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Information Section -->
    <div class="section">
        <h2 class="section-title">INFORMASI ASET</h2>

        <div class="clearfix">
            <!-- Asset Image (Left Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Gambar Aset</h3>
                <div class="image-container">
                    @if(!empty($complaint['asset_image_base64']))
                        <img src="data:image/jpeg;base64,{{ $complaint['asset_image_base64'] }}" alt="Gambar Aset">
                    @else
                        <p style="color: #999; font-style: italic;">Gambar aset tidak tersedia</p>
                    @endif
                </div>
            </div>

            <!-- Asset Details (Right Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Detail Aset</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Nama Aset</span>
                            <span class="detail-cell-value">{{ $complaint['asset_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Kode Aset</span>
                            <span class="detail-cell-value">{{ $complaint['asset_code'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Nomor Seri</span>
                            <span class="detail-cell-value">{{ $complaint['serial_number'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Model</span>
                            <span class="detail-cell-value">{{ $complaint['model'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repair Section -->
    <div class="section">
        <h2 class="section-title">INFORMASI PERBAIKAN</h2>

        @if(!empty($complaint['repair']))
            <div class="clearfix">
                <!-- Repair Image (Left Half) -->
                <div class="col-50">
                    <h3 class="subsection-title">Gambar Perbaikan</h3>
                    <div class="image-container">
                        @if(!empty($complaint['repair']['repair_picture_base64']))
                            <img src="data:image/jpeg;base64,{{ $complaint['repair']['repair_picture_base64'] }}"
                                alt="Gambar Perbaikan">
                        @else
                            <p style="color: #999; font-style: italic;">Gambar perbaikan tidak tersedia</p>
                        @endif
                    </div>
                </div>

                <!-- Repair Details (Right Half) -->
                <div class="col-50">
                    <h3 class="subsection-title">Detail Perbaikan</h3>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Hasil</span>
                                <span class="detail-cell-value">
                                    @php
                                        $resultClass = '';
                                        $resultText = '';
                                        $finalResult = $complaint['repair']['final_result'] ?? '';

                                        $translations = [
                                            'Good' => 'Baik',
                                            'Slightly Damage' => 'Kerusakan Ringan',
                                            'Heavy Damage' => 'Kerusakan Berat',
                                            'Waiting for Part' => 'Menunggu Suku Cadang'
                                        ];

                                        $resultText = $translations[$finalResult] ?? $finalResult ?? 'N/A';
                                    @endphp
                                    <span class="detail-cell-value">{{ $resultText }}</span>
                                </span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Biaya</span>
                                <span
                                    class="detail-cell-value">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float) $complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell" colspan="2">
                                <span class="detail-cell-title">Deskripsi</span>
                                <span
                                    class="detail-cell-value">{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Teknisi</span>
                                <span class="detail-cell-value">
                                    {{ $complaint['repair']['technician_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Bagian yang Diganti</span>
                                <span class="detail-cell-value">{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="subsection-title">Waktu</h3>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Tanggal Perbaikan</span>
                                <span
                                    class="detail-cell-value">{{ isset($complaint['repair']['repair_date']) && $complaint['repair']['repair_date'] && $complaint['repair']['repair_date'] != '-' ? \Carbon\Carbon::parse($complaint['repair']['repair_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Tanggal Selesai</span>
                                <span
                                    class="detail-cell-value">{{ isset($complaint['repair']['completion_date']) && $complaint['repair']['completion_date'] && $complaint['repair']['completion_date'] != '-' ? \Carbon\Carbon::parse($complaint['repair']['completion_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Tanggal Disetujui</span>
                                <span
                                    class="detail-cell-value">{{ isset($complaint['repair']['approval_date']) && $complaint['repair']['approval_date'] && $complaint['repair']['approval_date'] != '-' ? \Carbon\Carbon::parse($complaint['repair']['approval_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Disetujui Oleh</span>
                                <span class="detail-cell-value">
                                    {{ $complaint['repair']['approver_name'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div style="text-align: center; padding: 30px; background-color: #f9f9f9; border-radius: 4px;">
                <p style="color: #666; font-size: 14px;">Belum ada informasi perbaikan.</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Sistem Monitoring Aset - Laporan Detail Keluhan & Perbaikan</p>
    </div>
</body>

</html>