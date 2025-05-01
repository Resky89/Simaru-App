@extends('Layout.app')

@section('title', 'Calibration Details')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <!-- Header with back button -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <a href="{{ route('calibration') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-[#213268]">CALIBRATION DETAILS</h1>
        </div>
        <div>
            <a href="{{ route('calibration.detail.export.pdf', ['id' => $calibration['id'] ?? 0]) }}" target="_blank"
               class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Calibration information -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left column -->
            <div class="space-y-4">
                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Asset</span>
                    <span class="font-medium">{{ $calibration['asset_name'] ?? 'N/A' }}</span>
                    <span class="text-sm text-gray-600">{{ $calibration['asset_code'] ?? 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Status</span>
                    <div class="flex items-center">
                        @if(isset($calibration['status_calibration']) && $calibration['status_calibration'] == 'scheduled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Scheduled
                            </span>
                        @elseif(isset($calibration['status_calibration']) && $calibration['status_calibration'] == 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                        @elseif(isset($calibration['status_calibration']) && $calibration['status_calibration'] == 'overdue')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Overdue
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ isset($calibration['status_calibration']) ? ucfirst($calibration['status_calibration']) : 'Unknown' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Brand</span>
                    <span class="font-medium">{{ $calibration['brand_name'] ?? 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Serial Number</span>
                    <span class="font-medium">{{ $calibration['serial_number'] ?? 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Location</span>
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
                    <span class="font-medium">{{ $locationText }}</span>
                </div>
            </div>

            <!-- Right column -->
            <div class="space-y-4">
                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Planning Date</span>
                    <span class="font-medium">{{ isset($calibration['planning_calibration_date']) && $calibration['planning_calibration_date'] ? date('d M Y', strtotime($calibration['planning_calibration_date'])) : 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Actual Calibration Date</span>
                    @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                        <span class="font-medium text-amber-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Belum dilakukan kalibrasi
                        </span>
                    @else
                        <span class="font-medium">{{ date('d M Y', strtotime($calibration['actual_calibration_date'])) }}</span>
                    @endif
                </div>

                @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Next Calibration Date</span>
                    <span class="font-medium">{{ isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'] ? date('d M Y', strtotime($calibration['next_calibration_date'])) : 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Vendor</span>
                    <span class="font-medium">{{ $calibration['vendor_name'] ?? 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Certificate Number</span>
                    <span class="font-medium">{{ $calibration['certificate_number'] ?? 'N/A' }}</span>
                </div>

                <div class="flex flex-col space-y-1">
                    <span class="text-sm text-gray-500">Result</span>
                    <div>
                        @if(isset($calibration['calibration_result']) && $calibration['calibration_result'] == 'pass')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Pass
                            </span>
                        @elseif(isset($calibration['calibration_result']) && $calibration['calibration_result'] == 'fail')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Fail
                            </span>
                        @elseif(isset($calibration['calibration_result']) && $calibration['calibration_result'] == 'unknown')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Unknown
                            </span>
                        @else
                            <span class="font-medium">{{ isset($calibration['calibration_result']) ? ucfirst($calibration['calibration_result']) : 'N/A' }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes section -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-2 text-[#213268]">Notes</h2>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                <p class="text-amber-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Belum dilakukan kalibrasi
                </p>
            @else
                <p class="text-gray-700 whitespace-pre-line">{{ $calibration['notes'] ?? 'No notes available' }}</p>
            @endif
        </div>
    </div>

    <!-- Certificate file section -->
    @if(!empty($calibration['certificate_file_path']))
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-2 text-[#213268]">Certificate File</h2>
        <div class="flex flex-col p-4 bg-gray-50 rounded-lg border border-gray-200">
            @php
                $fileName = basename($calibration['certificate_file_path']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
            @endphp

            @if($isImage)
                <div class="mb-4 w-full flex justify-center">
                    <img src="http://localhost:5000/public/images/{{ $fileName }}"
                         alt="Certificate"
                         class="max-w-md w-full object-contain rounded-lg shadow-md"
                         style="max-height: 350px;"
                         onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                </div>
            @else
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div>
                        <p class="font-medium">{{ $fileName }}</p>
                        <a href="http://localhost:5000/public/documents/{{ $fileName }}"
                            target="_blank"
                            class="text-blue-600 hover:underline text-sm">
                            View Document
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Print-only styles -->
    <style>
        @media print {
            .no-print, .no-print * {
                display: none !important;
            }
            body {
                background-color: white;
            }
        }
    </style>
</div>
@endsection
