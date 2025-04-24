<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaint & Repair Detail</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>COMPLAINT & REPAIR DETAIL</h1>
        <p>Generated on: {{ date('d M Y H:i:s') }}</p>
    </div>

    <!-- Complaint Section -->
    <div class="section">
        <h2 class="section-title">COMPLAINT INFORMATION</h2>

        <div class="clearfix">
            <!-- Complaint Image (Left Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Complaint Image</h3>
                <div class="image-container">
                    @if(!empty($complaint['complaint_picture_base64']))
                        <img src="data:image/jpeg;base64,{{ $complaint['complaint_picture_base64'] }}" alt="Complaint Image">
                    @else
                        <p style="color: #999; font-style: italic;">No image available</p>
                    @endif
                </div>
            </div>

            <!-- Complaint Details (Right Half) -->
            <div class="col-50">
                <h3 class="subsection-title">Basic Details</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Asset Name</span>
                            <span class="detail-cell-value">{{ $complaint['asset_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Asset ID</span>
                            <span class="detail-cell-value">{{ $complaint['asset_id'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-cell" colspan="2">
                            <span class="detail-cell-title">Description</span>
                            <span class="detail-cell-value">{{ $complaint['description'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Status</span>
                            <span class="detail-cell-value">
                                @php
                                    $statusClass = '';
                                    $status = $complaint['status'] ?? '';

                                    if ($status == 'approved' || $status == 'completed') {
                                        $statusClass = 'status-approved';
                                    } elseif ($status == 'pending') {
                                        $statusClass = 'status-pending';
                                    } elseif ($status == 'rejected') {
                                        $statusClass = 'status-rejected';
                                    } elseif ($status == 'in_progress') {
                                        $statusClass = 'status-in-progress';
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status ?: 'Unknown')) }}
                                </span>
                            </span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Reported By</span>
                            <span class="detail-cell-value">ID: {{ $complaint['reporter_number'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <h3 class="subsection-title">Timeline</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-cell">
                            <span class="detail-cell-title">Complaint Date</span>
                            <span class="detail-cell-value">{{ isset($complaint['complaint_date']) ? date('d M Y H:i', strtotime($complaint['complaint_date'])) : 'N/A' }}</span>
                        </div>
                        <div class="detail-cell">
                            <span class="detail-cell-title">Finished Date</span>
                            <span class="detail-cell-value">{{ isset($complaint['finished_date']) && $complaint['finished_date'] ? date('d M Y H:i', strtotime($complaint['finished_date'])) : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repair Section -->
    <div class="section">
        <h2 class="section-title">REPAIR INFORMATION</h2>

        @if(!empty($complaint['repair']))
            <div class="clearfix">
                <!-- Repair Image (Left Half) -->
                <div class="col-50">
                    <h3 class="subsection-title">Repair Image</h3>
                    <div class="image-container">
                        @if(!empty($complaint['repair']['repair_picture_base64']))
                            <img src="data:image/jpeg;base64,{{ $complaint['repair']['repair_picture_base64'] }}" alt="Repair Image">
                        @else
                            <p style="color: #999; font-style: italic;">No repair image available</p>
                        @endif
                    </div>
                </div>

                <!-- Repair Details (Right Half) -->
                <div class="col-50">
                    <h3 class="subsection-title">Repair Details</h3>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Result</span>
                                <span class="detail-cell-value">{{ $complaint['repair']['final_result'] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Cost</span>
                                <span class="detail-cell-value">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float)$complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell" colspan="2">
                                <span class="detail-cell-title">Description</span>
                                <span class="detail-cell-value">{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Technician</span>
                                <span class="detail-cell-value">ID: {{ $complaint['repair']['technician_number'] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Parts Replaced</span>
                                <span class="detail-cell-value">{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="subsection-title">Timeline</h3>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Repair Date</span>
                                <span class="detail-cell-value">{{ isset($complaint['repair']['repair_date']) ? date('d M Y H:i', strtotime($complaint['repair']['repair_date'])) : 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Completion Date</span>
                                <span class="detail-cell-value">{{ isset($complaint['repair']['completion_date']) ? date('d M Y H:i', strtotime($complaint['repair']['completion_date'])) : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell">
                                <span class="detail-cell-title">Approval Date</span>
                                <span class="detail-cell-value">{{ isset($complaint['repair']['approval_date']) ? date('d M Y H:i', strtotime($complaint['repair']['approval_date'])) : 'N/A' }}</span>
                            </div>
                            <div class="detail-cell">
                                <span class="detail-cell-title">Approved By</span>
                                <span class="detail-cell-value">ID: {{ $complaint['repair']['approver_number'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div style="text-align: center; padding: 30px; background-color: #f9f9f9; border-radius: 4px;">
                <p style="color: #666; font-size: 14px;">No repair information available yet.</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Asset Monitoring System - Complaint & Repair Detail Report</p>
    </div>
</body>
</html>
