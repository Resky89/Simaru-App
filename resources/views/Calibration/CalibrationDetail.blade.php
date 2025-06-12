@extends('Layout.app')

@section('title', 'Detail Kalibrasi')

@section('content')
    @include('Layout.loading')
    <div class="h-full">
        <!-- Calibration Details Section -->
        <div class="flex flex-col gap-6 p-4 md:p-7 bg-base-100 rounded-xl">
            <!-- Header with status banner -->
            <div class="relative">
                <!-- Status banner at top -->
                @php
                    $statusClass = 'bg-gray-100 text-gray-800';
                    $statusText = 'Tidak Diketahui';
                    $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';

                    if(isset($calibration['status_calibration'])) {
                        switch($calibration['status_calibration']) {
                            case 'scheduled':
                                $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                $statusText = 'Terjadwal';
                                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />';
                                break;
                            case 'in_progress':
                                $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                $statusText = 'Dalam Proses';
                                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                break;
                            case 'completed':
                                $statusClass = 'bg-green-100 text-green-800 border-green-200';
                                $statusText = 'Selesai';
                                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                break;
                            case 'overdue':
                                $statusClass = 'bg-red-100 text-red-800 border-red-200';
                                $statusText = 'Terlambat';
                                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                                break;
                            case 'cancelled':
                                $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                $statusText = 'Dibatalkan';
                                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                                break;
                        }
                    }
                @endphp

                <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <a href="{{ route('calibration') }}"
                        class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL KALIBRASI</h1>
                </div>
                <div>
                    @if(hasPermission('calibration:export'))
                    <a href="{{ route('calibration.detail.export.pdf', ['id' => $calibration['id'] ?? 0]) }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Ekspor PDF
                    </a>
                    @endif
                </div>
            </div>

                <!-- Status banner -->
                <div class="w-full rounded-lg p-4 mb-6 {{ $statusClass }} border flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $statusIcon !!}
                    </svg>
                    <div>
                        <div class="font-semibold">Status: {{ $statusText }}</div>
                        <div class="text-sm">
                            @php
                                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            @endphp
                            @if($calibration['status_calibration'] == 'scheduled')
                                Kalibrasi dijadwalkan pada
                                @php
                                    if (isset($calibration['planning_calibration_date']) && $calibration['planning_calibration_date']) {
                                        $date = new DateTime($calibration['planning_calibration_date']);
                                        echo $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                    } else {
                                        echo 'tanggal tidak tersedia';
                                    }
                                @endphp
                            @elseif($calibration['status_calibration'] == 'in_progress')
                                Kalibrasi sedang dalam proses
                            @elseif($calibration['status_calibration'] == 'completed')
                                Kalibrasi telah selesai pada
                                @php
                                    if (isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date']) {
                                        $date = new DateTime($calibration['actual_calibration_date']);
                                        echo $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                    } else {
                                        echo 'tanggal tidak tersedia';
                                    }
                                @endphp
                            @elseif($calibration['status_calibration'] == 'overdue')
                                Kalibrasi terlambat dari jadwal
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Asset Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        Informasi Aset
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Aset</span>
                            <span class="font-medium text-lg">{{ $calibration['asset_name'] ?? 'N/A' }}</span>
                            <span class="text-sm text-gray-600">{{ $calibration['asset_code'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Merek</span>
                            <span class="font-medium">{{ $calibration['brand_name'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Nomor Seri</span>
                            <span class="font-medium">{{ $calibration['serial_number'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Location Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lokasi
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Gedung</span>
                            <span class="font-medium">{{ $calibration['location']['building_name'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Lantai</span>
                            <span class="font-medium">{{ $calibration['location']['floor_number'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Ruangan</span>
                            <span class="font-medium">{{ $calibration['location']['room_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Calibration Dates Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Jadwal Kalibrasi
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Tanggal Rencana</span>
                            <span class="font-medium">
                                @php
                                    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    if (isset($calibration['planning_calibration_date']) && $calibration['planning_calibration_date']) {
                                        $date = new DateTime($calibration['planning_calibration_date']);
                                        echo $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                    } else {
                                        echo 'N/A';
                                    }
                                @endphp
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Tanggal Kalibrasi Aktual</span>
                            @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                                <span class="font-medium text-amber-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Belum dilakukan kalibrasi
                                </span>
                            @else
                                <span class="font-medium">
                                    @php
                                        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        $date = new DateTime($calibration['actual_calibration_date']);
                                        echo $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                    @endphp
                                </span>
                            @endif
                        </div>

                        @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500">Tanggal Kalibrasi Berikutnya</span>
                                <span class="font-medium">
                                    @php
                                        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        if (isset($calibration['next_calibration_date']) && $calibration['next_calibration_date']) {
                                            $date = new DateTime($calibration['next_calibration_date']);
                                            echo $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                        } else {
                                            echo 'N/A';
                                        }
                                    @endphp
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Section Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-2">
                <!-- Calibration Results Card -->
                @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Hasil Kalibrasi
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col">
                                <span class="text-sm text-gray-500">Vendor</span>
                                <span class="font-medium">{{ $calibration['vendor_name'] ?? 'N/A' }}</span>
                            </div>

                        <div class="flex flex-col">
                                <span class="text-sm text-gray-500">Nomor Sertifikat</span>
                                <span class="font-medium">{{ $calibration['certificate_number'] ?? 'N/A' }}</span>
                            </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Biaya Kalibrasi</span>
                            <span class="font-medium">{{ isset($calibration['calibration_price']) ? 'Rp ' . number_format($calibration['calibration_price'], 0, ',', '.') : 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                                <span class="text-sm text-gray-500">Hasil</span>
                                <div>
                                @if(isset($calibration['calibration_result']))
                                    @if($calibration['calibration_result'] == 'pass')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Lulus
                                        </span>
                                    @elseif($calibration['calibration_result'] == 'fail')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Gagal
                                        </span>
                                    @elseif($calibration['calibration_result'] == 'unknown')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Tidak Ditemukan
                                        </span>
                                    @else
                                        <span class="font-medium">{{ ucfirst($calibration['calibration_result']) }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            <!-- Notes section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Catatan
                    </h2>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px]">
                    @if(!isset($calibration['actual_calibration_date']) || !$calibration['actual_calibration_date'])
                        <p class="text-amber-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Belum dilakukan kalibrasi
                        </p>
                    @else
                        <p class="text-gray-700 whitespace-pre-line">{{ $calibration['notes'] ?? 'Tidak ada catatan' }}</p>
                    @endif
                    </div>
                </div>
            </div>

            <!-- Certificate file section -->
            @if(!empty($calibration['certificate_file_path']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mt-2">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Berkas Sertifikat
                    </h2>

                    <div class="flex flex-col p-4 bg-gray-50 rounded-lg border border-gray-200">
                        @php
                            $filePath = $calibration['certificate_file_path'];
                            $fileName = basename($filePath);
                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                        @endphp

                        @if($isImage)
                            <div class="mb-4 w-full flex justify-center">
                                <img src="http://localhost:5000/public{{ $filePath }}" alt="Sertifikat"
                                    class="max-w-md w-full object-contain rounded-lg shadow-md" style="max-height: 350px;"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                            </div>
                        @else
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium">{{ $fileName }}</p>
                                    <a href="http://localhost:5000/public{{ $filePath }}" target="_blank"
                                        class="text-blue-600 hover:underline text-sm">
                                        Lihat Dokumen
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
                    .no-print,
                    .no-print * {
                        display: none !important;
                    }

                    body {
                        background-color: white;
                    }
                }
            </style>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Export PDF functionality
            const exportBtn = document.querySelector('a[href*="calibration.detail.export.pdf"]');
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const calibrationId = {{ $calibration['id'] ?? 0 }};
                    if (!calibrationId) {
                        console.error('Calibration ID not available');
                        return;
                    }

                    // Create the PDF export URL
                    const exportUrl = "{{ route('calibration.detail.export.pdf', ['id' => $calibration['id'] ?? 0]) }}";

                    // Open in new tab
                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>
    @endpush
@endsection
