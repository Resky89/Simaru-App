@extends('Layout.app')

@section('title', 'Manajemen Kalibrasi')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Calibration Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">KALIBRASI</h1>

                        <div class="flex gap-4">
                            @if(hasPermission('calibration:export'))
                                <button id="exportBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-base">Ekspor PDF</span>
                                </button>
                            @endif

                            @if(hasPermission('calibration:create'))
                                <button id="addCalibrationBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Kalibrasi</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari kode tugas, nama aset, atau kode aset..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <select id="statusFilter"
                                class="w-[140px] h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Status</option>
                                <option value="">Semua</option>
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Dalam Proses</option>
                                <option value="completed">Selesai</option>
                            </select>
                            <select id="resultFilter"
                                class="w-[160px] h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Hasil</option>
                                <option value="">Semua Hasil</option>
                                <option value="pass">Lulus</option>
                                <option value="fail">Gagal</option>
                            </select>
                            <select id="sortOrder"
                                class="w-[150px] h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="newest" selected>Terbaru</option>
                                <option value="oldest">Terlama</option>
                                <option value="date_asc">Tanggal Rencana (A-Z)</option>
                                <option value="date_desc">Tanggal Rencana (Z-A)</option>
                                <option value="asset_asc">Nama Aset (A-Z)</option>
                                <option value="asset_desc">Nama Aset (Z-A)</option>
                            </select>
                            @if(hasPermission('calibration:delete'))
                                <button id="bulkDeleteBtn"
                                    class="hidden px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
                                    Hapus Terpilih
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Calibration Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                        <input type="checkbox" id="selectAllCalibrations" class="checkbox checkbox-sm" />
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kode Tugas</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Lokasi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Rencana</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Aktual</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Berikutnya
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Sertifikat</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left min-w-[90px]">Hasil
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Biaya</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left min-w-[120px]">Status
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calibrations ?? [] as $calibration)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                            <input type="checkbox" class="calibration-checkbox checkbox checkbox-sm"
                                                data-id="{{ $calibration['id'] }}" />
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $calibration['task_code'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $calibration['asset_name'] ?? '-' }}</span>
                                                <span class="text-gray-500">{{ $calibration['asset_code'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($calibration['location']))
                                                <div class="flex flex-col">
                                                    <span>{{ $calibration['location']['room_name'] ?? '-' }}</span>
                                                    <span
                                                        class="text-gray-500">{{ $calibration['location']['building_name'] ?? '-' }}</span>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if($calibration['planning_calibration_date'])
                                                @php
                                                    $date = \Carbon\Carbon::parse($calibration['planning_calibration_date']);
                                                    $indonesianMonths = [
                                                        'Jan',
                                                        'Feb',
                                                        'Mar',
                                                        'Apr',
                                                        'Mei',
                                                        'Jun',
                                                        'Jul',
                                                        'Agt',
                                                        'Sep',
                                                        'Okt',
                                                        'Nov',
                                                        'Des'
                                                    ];
                                                    echo $date->format('d') . ' ' . $indonesianMonths[$date->month - 1] . ' ' . $date->format('Y');
                                                @endphp
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($calibration['actual_calibration_date']) && $calibration['actual_calibration_date'])
                                                @php
                                                    $date = \Carbon\Carbon::parse($calibration['actual_calibration_date']);
                                                    $indonesianMonths = [
                                                        'Jan',
                                                        'Feb',
                                                        'Mar',
                                                        'Apr',
                                                        'Mei',
                                                        'Jun',
                                                        'Jul',
                                                        'Agt',
                                                        'Sep',
                                                        'Okt',
                                                        'Nov',
                                                        'Des'
                                                    ];
                                                    echo $date->format('d') . ' ' . $indonesianMonths[$date->month - 1] . ' ' . $date->format('Y');
                                                @endphp
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($calibration['next_calibration_date']) && $calibration['next_calibration_date'])
                                                @php
                                                    $date = \Carbon\Carbon::parse($calibration['next_calibration_date']);
                                                    $indonesianMonths = [
                                                        'Jan',
                                                        'Feb',
                                                        'Mar',
                                                        'Apr',
                                                        'Mei',
                                                        'Jun',
                                                        'Jul',
                                                        'Agt',
                                                        'Sep',
                                                        'Okt',
                                                        'Nov',
                                                        'Des'
                                                    ];
                                                    echo $date->format('d') . ' ' . $indonesianMonths[$date->month - 1] . ' ' . $date->format('Y');
                                                @endphp
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $calibration['certificate_number'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $resultClass = '';
                                                $resultText = $calibration['calibration_result'] ?? '-';

                                                if (strtolower($resultText) == 'pass') {
                                                    $resultClass = 'bg-green-100 text-green-800';
                                                    $resultText = 'Lulus';
                                                } elseif (strtolower($resultText) == 'fail') {
                                                    $resultClass = 'bg-red-100 text-red-800';
                                                    $resultText = 'Gagal';
                                                }
                                            @endphp
                                            @if($resultText != '-')
                                                <span
                                                    class="px-2 py-1 rounded text-xs inline-block w-full text-center whitespace-nowrap {{ $resultClass }}">
                                                    {{ $resultText }}
                                                </span>
                                            @else
                                                {{ $resultText }}
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ isset($calibration['calibration_price']) && $calibration['calibration_price'] ? number_format((float) $calibration['calibration_price'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $statusClass = '';
                                                $status = $calibration['status_calibration'] ?? '';

                                                if ($status == 'scheduled') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                } elseif ($status == 'in_progress') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                } elseif ($status == 'completed') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($status == 'overdue') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                } elseif ($status == 'cancelled') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                }
                                            @endphp
                                            <span
                                                class="px-2 py-1 rounded text-xs inline-block w-full text-center whitespace-nowrap {{ $statusClass }}">
                                                @if($status == 'scheduled')
                                                    Terjadwal
                                                @elseif($status == 'in_progress')
                                                    Dalam Proses
                                                @elseif($status == 'completed')
                                                    Selesai
                                                @elseif($status == 'overdue')
                                                    Terlambat
                                                @elseif($status == 'cancelled')
                                                    Dibatalkan
                                                @else
                                                    {{ ucfirst($status) ?: '-' }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <!-- View Details Icon (Eye) -->
                                                <a href="{{ route('calibration.detail', ['id' => $calibration['id']]) }}"
                                                    class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                <!-- Edit Schedule Icon (Calendar) -->
                                                @if(hasPermission('calibration:edit'))
                                                    @if(!in_array(strtolower($calibration['status_calibration'] ?? ''), ['completed', 'approved']))
                                                        <button
                                                            class="edit-schedule-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                            data-id="{{ $calibration['id'] }}"
                                                            data-asset-name="{{ $calibration['asset_name'] ?? '' }}"
                                                            data-asset-code="{{ $calibration['asset_code'] ?? '' }}"
                                                            title="Ubah Jadwal">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endif

                                                <!-- Perform Calibration Icon (Pencil) -->
                                                @if(hasPermission('calibration:report'))
                                                    @if(!in_array(strtolower($calibration['status_calibration'] ?? ''), ['completed', 'approved']))
                                                        <button
                                                            class="perform-calibration-btn p-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors"
                                                            data-id="{{ $calibration['id'] }}" title="Lakukan Kalibrasi">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endif

                                                <!-- Delete Icon (Trash) -->
                                                @if(hasPermission('calibration:delete'))
                                                    <button
                                                        class="delete-calibration-btn p-2 bg-[#F9D2D2] text-[#8E2121] rounded-md hover:bg-red-200 transition-colors"
                                                        data-id="{{ $calibration['id'] }}" title="Hapus Kalibrasi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Data
                                            kalibrasi tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $calibrations_pagination['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($calibrations_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $calibrations_pagination['current_page'] ?? 1;
                                    $lastPage = $calibrations_pagination['last_page'] ?? 1;
                                    $maxPagesShown = 5; // Show max 5 pages at once
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $startPage + $maxPagesShown - 1);

                                        if ($endPage - $startPage + 1 < $maxPagesShown) {
                                            $startPage = max(1, $endPage - $maxPagesShown + 1);
                                        }
                                    @endphp

                                    @if($startPage > 1)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                            class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                            1
                                        </a>
                                        @if($startPage > 2)
                                            <span class="flex items-center justify-center">
                                                ...
                                            </span>
                                        @endif
                                    @endif

                                    @for ($i = $startPage; $i <= $endPage; $i++)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                            class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                            {{ $i }}
                                        </a>
                                    @endfor

                                @if($endPage < $lastPage)
                                    @if($endPage < $lastPage - 1)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $lastPage }}
                                    </a>
                                @endif
                            </div>
                            <a href="{{ $calibrations_pagination['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($calibrations_pagination['current_page'] ?? 1) >= ($calibrations_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">
                                @if(isset($calibrations_pagination) && is_array($calibrations_pagination))
                                    Menampilkan
                                    {{ $calibrations_pagination['from'] ?? (($calibrations_pagination['current_page'] - 1) * ($calibrations_pagination['per_page'] ?? 10) + 1) }}
                                    sampai
                                    {{ $calibrations_pagination['to'] ?? min($calibrations_pagination['current_page'] * ($calibrations_pagination['per_page'] ?? 10), $calibrations_pagination['total'] ?? 0) }}
                                    dari
                                    {{ $calibrations_pagination['total'] ?? 0 }} data
                                @else
                                    Menampilkan 0 data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changePerPage(this.value)">
                                <option value="10" {{ isset($calibrations_pagination['per_page']) && $calibrations_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($calibrations_pagination['per_page']) && $calibrations_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($calibrations_pagination['per_page']) && $calibrations_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                <option value="100" {{ isset($calibrations_pagination['per_page']) && $calibrations_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Calibration Modal - Changed to Perform Calibration Modal -->
            @if(hasPermission('calibration:report'))
                    <div id="viewCalibrationModal" class="fixed inset-0 z-50 hidden">
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                        <div class="fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                                    id="viewCalibrationModalContent">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center p-6 pb-0">
                                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">LAKUKAN KALIBRASI</h2>
                                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                            data-modal="viewCalibrationModal">
                                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6">
                                        <form id="performCalibrationForm" class="space-y-6" data-no-loading
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" id="calibration_id" name="calibration_id">

                                            <!-- Required fields note -->
                                            <div class="text-sm text-gray-600 mb-4">
                                                Bidang dengan tanda <span class="text-red-500">*</span> wajib diisi
                                            </div>

                                            <!-- ASSET INFORMATION SECTION -->
                                            <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Informasi Aset</h3>

                                                <!-- Asset Image - Added similar to AssetDetail.blade.php -->
                                                <div
                                                    class="w-full h-40 bg-white mb-4 rounded-lg shadow-sm overflow-hidden relative flex items-center justify-center">
                                                    <img id="asset_image_display" src="{{ asset('images/placeholder.png') }}"
                                                        alt="Asset Image" class="w-full h-full object-contain p-2"
                                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Left Column -->
                                                    <div class="space-y-4">
                                                        <!-- Asset Code -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">KODE ASET</label>
                                                            <input type="text" id="asset_code_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Asset Name -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">NAMA ASET</label>
                                                            <input type="text" id="asset_name_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Serial Number -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">NOMOR SERI</label>
                                                            <input type="text" id="serial_number_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="space-y-4">
                                                        <!-- Brand (Merk) -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">MERK</label>
                                                            <input type="text" id="brand_name_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Model -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">MODEL</label>
                                                            <input type="text" id="model_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Location -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">LOKASI</label>
                                                            <input type="text" id="location_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CALIBRATION SCHEDULE SECTION -->
                                            <div class="bg-yellow-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Jadwal Kalibrasi</h3>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                    <!-- Planning Date -->
                                                    <div>
                                                        <label for="planning_calibration_date"
                                                            class="block text-sm font-medium text-gray-700">
                                                            TANGGAL RENCANA<span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="date" id="planning_date_display" name="planning_calibration_date"
                                                            class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                            readonly>
                                                    </div>

                                                    <!-- Work Date (Actual Calibration Date) -->
                                                    <div>
                                                        <label for="actual_calibration_date"
                                                            class="block text-sm font-medium text-gray-700">
                                                            TANGGAL KERJA<span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="date" id="actual_calibration_date" name="actual_calibration_date"
                                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                    </div>

                                                    <!-- Next Calibration Date -->
                                                    <div>
                                                        <label for="next_calibration_date"
                                                            class="block text-sm font-medium text-gray-700">
                                                            KALIBRASI BERIKUTNYA<span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="date" id="next_calibration_date" name="next_calibration_date"
                                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CALIBRATION DETAILS SECTION -->
                                            <div class="bg-green-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Detail Kalibrasi</h3>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Left Column -->
                                                    <div class="space-y-4">
                                                        <!-- Certificate Number -->
                                                        <div>
                                                            <label for="certificate_number"
                                                                class="block text-sm font-medium text-gray-700">
                                                                NOMOR SERTIFIKAT<span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text" id="certificate_number" name="certificate_number"
                                                                placeholder="Masukkan nomor sertifikat"
                                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                        </div>

                                                        <!-- Vendor -->
                                                        <div>
                                                            <label for="vendor_id" class="block text-sm font-medium text-gray-700">
                                                                VENDOR
                                                            </label>
                                                            <div class="relative">
                                                                <input type="text" id="vendor_search" placeholder="Cari vendor..."
                                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                                <input type="hidden" id="vendor_id" name="vendor_id">
                                                                <div id="vendor_results"
                                                                    class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto border border-gray-300">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="space-y-4">
                                                        <!-- Service Price -->
                                                        <div>
                                                            <label for="calibration_price"
                                                                class="block text-sm font-medium text-gray-700">
                                                                BIAYA LAYANAN<span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="relative mt-1">
                                                                <div
                                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                                                </div>
                                                                <input type="text" id="calibration_price" name="calibration_price"
                                                                    required
                                                                    class="block w-full pl-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                                    onkeyup="formatCurrency(this)"
                                                                    onblur="formatCurrency(this, 'blur')">
                                                            </div>
                                                        </div>

                                                        <!-- Result -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">
                                                                HASIL<span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="mt-2 flex flex-wrap gap-6">
                                                                <div class="flex items-center">
                                                                    <input type="radio" id="result_pass" name="calibration_result"
                                                                        value="pass"
                                                                        class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                                    <label for="result_pass"
                                                                        class="ml-2 text-sm text-gray-700">Lulus</label>
                                                                </div>
                                                                <div class="flex items-center">
                                                                    <input type="radio" id="result_fail" name="calibration_result"
                                                                        value="fail"
                                                                        class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                                    <label for="result_fail"
                                                                        class="ml-2 text-sm text-gray-700">Gagal</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- DOCUMENTATION SECTION -->
                                            <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Dokumentasi</h3>

                                                <!-- Document File -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">BERKAS TERUNGGAH</label>
                                                    <div
                                                        class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                        <!-- File preview container -->
                                                        <div id="file-preview" class="mt-2 mb-4 w-full hidden">
                                                            <div
                                                                class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                                <!-- Image preview -->
                                                                <img id="image-preview"
                                                                    class="w-full h-auto max-h-64 object-contain mx-auto rounded hidden"
                                                                    alt="Pratinjau file">

                                                                <!-- PDF/File preview -->
                                                                <div id="file-info" class="flex items-center">
                                                                    <svg class="w-6 h-6 text-red-600 mr-2"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                    <span id="file-name-text"
                                                                        class="text-sm text-gray-700 truncate"></span>
                                                                    <button type="button" id="remove-file"
                                                                        class="ml-auto text-red-500 hover:text-red-700">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="text-center">
                                                            <svg class="mx-auto h-12 w-12 text-[#213268]"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                            <p class="mt-1 text-sm text-gray-600">Seret berkas Anda atau <span
                                                                    class="text-[#213268] font-semibold">jelajahi berkas</span></p>
                                                            <p class="mt-1 text-xs text-gray-500">Format yang diterima: PDF, JPG, JPEG,
                                                                PNG (Maks: 5MB)</p>
                                                            <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area
                                                                ini untuk memilih berkas</p>
                                                        </div>
                                                        <input type="file" id="document_file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                    </div>
                                                </div>

                                                <!-- Calibration Notes - Full Width -->
                                                <div class="mt-4">
                                                    <label for="notes" class="block text-sm font-medium text-gray-700">CATATAN
                                                        KALIBRASI</label>
                                                    <textarea id="notes" name="notes" rows="3"
                                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                        placeholder="Tambahkan catatan atau keterangan tambahan tentang kalibrasi ini..."></textarea>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <input type="hidden" id="status_calibration" name="status_calibration" value="completed">

                                            <div class="pt-4">
                                                <button type="submit"
                                                    class="w-full py-3 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transition-colors duration-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Simpan Kalibrasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        <!-- Delete Confirmation Modal -->
        @if(hasPermission('calibration:delete'))
            <div id="deleteCalibrationModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="deleteCalibrationModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS KALIBRASI</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                    data-modal="deleteCalibrationModal">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <form id="deleteCalibrationForm" method="POST" data-no-loading>
                                @csrf
                                <div class="p-6">
                                    <div class="space-y-6 max-w-[400px] mx-auto">
                                        <div class="flex flex-col items-center">
                                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus data
                                                kalibrasi ini? Tindakan ini tidak dapat dibatalkan.</p>
                                            <p id="deleteCalibrationName" class="text-base font-semibold text-center mt-2"></p>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button"
                                                class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                                data-modal="deleteCalibrationModal">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        @endif

        <!-- Add Calibration Modal -->
        @if(hasPermission('calibration:create'))
            <div id="addCalibrationModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[850px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="addCalibrationModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Tambah Jadwal Kalibrasi Baru</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                    data-modal="addCalibrationModal">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6">
                                <form id="addCalibrationForm" class="space-y-6" data-no-loading>
                                    @csrf
                                    <!-- Required fields note -->
                                    <div class="text-sm text-gray-600">
                                        Bidang dengan tanda <span class="text-red-500">*</span> wajib diisi
                                    </div>

                                    <!-- Schedule Date -->
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                        <div class="flex items-center gap-4">
                                            <div class="min-w-[150px]">
                                                <label class="block text-base font-semibold text-[#213268]">
                                                    JADWAL MULAI<span class="text-red-500">*</span>
                                                </label>
                                            </div>
                                            <div class="flex-1">
                                                <input type="date" name="planning_calibration_date" id="planning_calibration_date"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                    required>
                                            </div>
                                            <div>
                                                <button type="button" id="addAssetsBtn"
                                                    class="bg-[#213268] hover:bg-[#152349] text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
                                                    title="Tambahkan aset yang perlu dikalibrasi">
                                                    <span class="text-xl mr-1">+</span>
                                                    Tambah Aset
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected Assets Table -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead>
                                                <tr>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                        No</th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                                        Kode Aset</th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset
                                                    </th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                                        Deskripsi</th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe Aset
                                                    </th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama
                                                        Kategori</th>
                                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedAssetsList">
                                                <tr>
                                                    <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak
                                                        ada data dalam tabel</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination for selected assets -->
                                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                                        <div class="flex items-center space-x-2" id="selectedAssetsPagination">
                                            <!-- Pagination controls will be inserted here -->
                                        </div>

                                        <div class="flex items-center gap-2 mt-4 md:mt-0">
                                            <span class="text-sm text-gray-600" id="selectedAssetsInfo">
                                                Menampilkan 0 sampai 0 dari 0 data
                                            </span>
                                            <select id="selectedAssetsPerPage"
                                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                                <option value="5" selected>5 per halaman</option>
                                                <option value="10">10 per halaman</option>
                                                <option value="20">20 per halaman</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-4 flex justify-end gap-4">
                                        <button type="submit"
                                            class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Asset Selection Modal -->
        @if(hasPermission('calibration:create'))
            <div id="assetSelectionModal" class="fixed inset-0 z-[60] hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[1200px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="assetSelectionModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Pilih Aset untuk Kalibrasi</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                    data-modal="assetSelectionModal">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <!-- Info Notice -->
                                <div class="p-4 mb-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p>Hanya aset dengan 'Perlu Kalibrasi' ditandai sebagai benar yang akan muncul dalam daftar
                                            ini.</p>
                                    </div>
                                </div>

                                <!-- Search and Filter -->
                                <div class="flex flex-col md:flex-row gap-4 mb-4">
                                    <div class="relative flex-grow">
                                        <input type="text" id="assetSearchInput"
                                            placeholder="Cari berdasarkan nama aset, kode, atau nomor seri..."
                                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assets Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                    <input type="checkbox" id="selectAllAssets" class="checkbox checkbox-sm">
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kode Aset
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Type
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Kategori
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="assetSelectionList">
                                            <tr>
                                                <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                    Memuat aset...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                                    <div class="flex items-center space-x-2" id="assetPaginationControls">
                                        <!-- Pagination will be inserted here -->
                                    </div>

                                    <div class="flex items-center gap-2 mt-4 md:mt-0">
                                        <span class="text-sm text-gray-600" id="assetPaginationInfo">
                                            Menampilkan 0 sampai 0 dari 0 data
                                        </span>
                                        <select id="assetPerPageSelect"
                                            class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                            <option value="10" selected>10 per halaman</option>
                                            <option value="25">25 per halaman</option>
                                            <option value="50">50 per halaman</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4">
                                    <button type="button" id="selectAssetsBtn"
                                        class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        </div>

        <!-- Edit Calibration Schedule Modal -->
        @if(hasPermission('calibration:edit'))
            <div id="editScheduleModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="editScheduleModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">UBAH JADWAL KALIBRASI</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                    data-modal="editScheduleModal">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <form id="editScheduleForm" method="POST" data-no-loading>
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" id="edit_schedule_calibration_id" name="calibration_id">
                                <div class="p-6">
                                    <div class="space-y-6 max-w-[450px] mx-auto">
                                        <!-- Asset Info -->
                                        <div class="bg-blue-50 p-4 rounded-lg mb-4 border border-blue-100">
                                            <div class="space-y-3">
                                                <div class="flex items-center">
                                                    <span class="font-semibold min-w-[120px] text-[#213268]">Kode Aset:</span>
                                                    <span id="edit_schedule_asset_code" class="text-gray-700"></span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="font-semibold min-w-[120px] text-[#213268]">Nama Aset:</span>
                                                    <span id="edit_schedule_asset_name" class="text-gray-700"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Planning Date -->
                                        <div class="space-y-2">
                                            <label for="planning_calibration_date"
                                                class="block text-base font-semibold text-[#666666]">
                                                Tanggal Rencana Kalibrasi<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="edit_planning_calibration_date" name="planning_calibration_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal rencana kalibrasi
                                                harus diisi</div>
                                            <div class="date-error-message text-red-500 text-sm mt-1 hidden">Tanggal tidak boleh
                                                kurang dari hari ini</div>
                                        </div>

                                        <div class="pt-2">
                                            <button type="submit"
                                                class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        @push('scripts')
            <script>
                const flashSuccess = @json(session('success') ?? null);
                const flashError = @json(session('error') ?? null);

                document.addEventListener('DOMContentLoaded', function () {
                    @if(!hasPermission('calibration:create'))
                        const createButtons = document.querySelectorAll('#addCalibrationBtn, #addAssetsBtn');
                        createButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                        @if(!hasPermission('calibration:edit'))
                            const editButtons = document.querySelectorAll('.edit-schedule-btn');
                            editButtons.forEach(btn => {
                                if (btn) {
                                    btn.style.display = 'none';
                                }
                            });
                        @endif

                        @if(!hasPermission('calibration:delete'))
                            const deleteButtons = document.querySelectorAll('.delete-calibration-btn, #bulkDeleteBtn');
                            deleteButtons.forEach(btn => {
                                if (btn) {
                                    btn.style.display = 'none';
                                }
                            });
                        @endif

                        @if(!hasPermission('calibration:export'))
                            const exportButtons = document.querySelectorAll('#exportBtn');
                            exportButtons.forEach(btn => {
                                if (btn) {
                                    btn.style.display = 'none';
                                }
                            });
                        @endif

                        @if(!hasPermission('calibration:report'))
                            const reportButtons = document.querySelectorAll('.perform-calibration-btn');
                            reportButtons.forEach(btn => {
                                if (btn) {
                                    btn.style.display = 'none';
                                }
                            });
                        @endif

                    // Currency formatter function
                    window.formatCurrency = function(input, blur) {
                        // Get input value
                        let input_val = input.value;

                        // Don't validate empty input
                        if (input_val === "") { return; }

                        // Check for decimal
                        if (input_val.indexOf(",") >= 0) {
                            // Get position of first decimal
                            var decimal_pos = input_val.indexOf(",");

                            // Split number by decimal point
                            var left_side = input_val.substring(0, decimal_pos);
                            var right_side = input_val.substring(decimal_pos);

                            // Remove all non-digits
                            left_side = left_side.replace(/\D/g, "");
                            right_side = right_side.replace(/\D/g, "");

                            // Limit decimal to only 2 digits
                            right_side = right_side.substring(0, 2);

                            // Add dots every 3 digits
                            left_side = left_side.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                            // Join number with comma for decimal
                            input_val = left_side + "," + right_side;
                        } else {
                            // Remove all non-digits
                            input_val = input_val.replace(/\D/g, "");

                            // Add dots every 3 digits
                            input_val = input_val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                            // Final formatting
                            if (blur === "blur") {
                                input_val += ",00";
                            }
                        }

                        // Send updated string to input
                        input.value = input_val;
                    };

                    // Format existing values on page load
                    const calibrationPriceInput = document.getElementById('calibration_price');
                    if (calibrationPriceInput && calibrationPriceInput.value) {
                        formatCurrency(calibrationPriceInput, 'blur');
                    }

                    if (typeof flashSuccess !== 'undefined' && flashSuccess) {
                        showToast(flashSuccess, 'success');
                    }
                    if (typeof flashError !== 'undefined' && flashError) {
                        showToast(flashError, 'error');
                    }

                    const addCalibrationForm = document.getElementById('addCalibrationForm');
                    const performCalibrationForm = document.getElementById('performCalibrationForm');
                    const deleteCalibrationForm = document.getElementById('deleteCalibrationForm');

                    function preventMultipleSubmits(form, buttonSelector) {
                        if (!form) return;

                        form.addEventListener('submit', function (e) {
                            if (this.checkValidity()) {
                                const submitBtn = this.querySelector(buttonSelector);
                                if (submitBtn && !submitBtn.disabled) {
                                    const originalText = submitBtn.innerHTML;

                                    submitBtn.disabled = true;
                                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                                    submitBtn.innerHTML = `
                                                                    <div class="flex items-center justify-center">
                                                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                                                        <span>Memproses...</span>
                                                                    </div>
                                                                `;

                                    setTimeout(() => {
                                        if (submitBtn) {
                                            submitBtn.disabled = false;
                                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                            submitBtn.innerHTML = originalText;
                                        }
                                    }, 10000);
                                }
                            }
                        });
                    }

                    preventMultipleSubmits(addCalibrationForm, 'button[type="submit"]');
                    preventMultipleSubmits(performCalibrationForm, 'button[type="submit"]');
                    preventMultipleSubmits(deleteCalibrationForm, 'button[type="submit"]');

                    function debounce(func, wait, immediate) {
                        let timeout;
                        return function () {
                            const context = this, args = arguments;
                            const later = function () {
                                timeout = null;
                                if (!immediate) func.apply(context, args);
                            };
                            const callNow = immediate && !timeout;
                            clearTimeout(timeout);
                            timeout = setTimeout(later, wait);
                            if (callNow) func.apply(context, args);
                        };
                    }

                    const today = new Date().toISOString().split('T')[0];

                    const planningDateInput = document.getElementById('planning_calibration_date');
                    if (planningDateInput) {
                        planningDateInput.setAttribute('min', today);
                    }

                    const nextCalibrationDateInput = document.getElementById('next_calibration_date');
                    if (nextCalibrationDateInput) {
                        nextCalibrationDateInput.setAttribute('min', today);
                    }

                    window.showToast = function (message, type = 'success') {
                        const notification = document.createElement('div');
                        notification.id = type + 'Notification' + Date.now();
                        notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                        notification.setAttribute('role', 'alert');

                        function processErrorObject(errorObj) {
                            // Handle {"success":"false","errors":"message"} format
                            if (errorObj.success === false && typeof errorObj.errors === 'string') {
                                return errorObj.errors;
                            }

                            if (errorObj.success === false && Array.isArray(errorObj.errors) && errorObj.errors.length > 0) {
                                const firstError = errorObj.errors[0];
                                if (typeof firstError === 'object' && firstError !== null &&
                                    firstError.path && firstError.message) {

                                    let errorList = '<ul>';
                                    errorObj.errors.forEach(err => {
                                        errorList += `<li><strong>${err.path}</strong>: ${err.message}</li>`;
                                    });
                                    errorList += '</ul>';
                                    return errorList;
                                }

                                return processErrorArray(errorObj.errors);
                            }

                            if (errorObj.errors && typeof errorObj.errors === 'object') {
                                let errorList = '<ul>';
                                Object.entries(errorObj.errors).forEach(([field, errors]) => {
                                    if (Array.isArray(errors)) {
                                        errors.forEach(error => {
                                            errorList += `<li><strong>${field}</strong>: ${error}</li>`;
                                        });
                                    } else if (typeof errors === 'string') {
                                        errorList += `<li><strong>${field}</strong>: ${errors}</li>`;
                                    }
                                });
                                errorList += '</ul>';
                                return errorList;
                            }

                            if (errorObj.message) {
                                return errorObj.message;
                            }

                            if (errorObj.error) {
                                return errorObj.error;
                            }

                            try {
                                return JSON.stringify(errorObj);
                            } catch (e) {
                                return "Error tidak dapat ditampilkan";
                            }
                        }

                        function processErrorArray(errArray) {
                            if (errArray.length > 0 && typeof errArray[0] === 'object' &&
                                errArray[0] !== null && errArray[0].path && errArray[0].message) {

                                let errorList = '<ul>';
                                errArray.forEach(err => {
                                    errorList += `<li><strong>${err.path}</strong>: ${err.message}</li>`;
                                });
                                errorList += '</ul>';
                                return errorList;
                            }

                            if (typeof errArray[0] === 'string') {
                                let errorList = '<ul>';
                                errArray.forEach(err => {
                                    errorList += `<li>${err}</li>`;
                                });
                                errorList += '</ul>';
                                return errorList;
                            }

                            return errArray.join(', ');
                        }

                        if (type === 'success') {
                            notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                            notification.innerHTML = `
                                                        <div class="flex items-start">
                                                            <div class="py-1">
                                                                <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="font-bold">Berhasil!</p>
                                                                <div>${message}</div>
                                                            </div>
                                                            <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                                                        </div>
                                                    `;
                        } else {
                            notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                            let processedMessage = '';

                            if (typeof message === 'string') {
                                processedMessage = message;
                            } else if (Array.isArray(message)) {
                                processedMessage = processErrorArray(message);
                            } else if (typeof message === 'object' && message !== null) {
                                processedMessage = processErrorObject(message);
                            } else {
                                processedMessage = "Terjadi kesalahan";
                            }

                            const wrapper = document.createElement('div');
                            wrapper.className = 'flex items-start';

                            const iconContainer = document.createElement('div');
                            iconContainer.className = 'py-1 flex-shrink-0';
                            iconContainer.innerHTML = `
                                                        <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                    `;

                            const contentContainer = document.createElement('div');
                            contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                            const title = document.createElement('p');
                            title.className = 'font-bold';
                            title.textContent = 'Gagal!';
                            contentContainer.appendChild(title);

                            const messageContainer = document.createElement('div');
                            messageContainer.className = 'error-message';
                            messageContainer.innerHTML = processedMessage;
                            contentContainer.appendChild(messageContainer);
                            const closeBtn = document.createElement('span');
                            closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                            closeBtn.textContent = '×';
                            closeBtn.onclick = function () {
                                notification.remove();
                            };

                            wrapper.appendChild(iconContainer);
                            wrapper.appendChild(contentContainer);
                            wrapper.appendChild(closeBtn);
                            notification.appendChild(wrapper);
                        }

                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                            setTimeout(() => notification.remove(), 500);
                        }, 5000);

                        return notification;
                    };

                    document.head.insertAdjacentHTML('beforeend', `
                                                <style>
                                                    @keyframes slideInRight {
                                                        from { transform: translateX(100%); }
                                                        to { transform: translateX(0); }
                                                    }
                                                    .animate-slide-in-right {
                                                        animation: slideInRight 0.3s ease-out forwards;
                                                    }

                                                    .error-message ul {
                                                        margin-top: 0.5rem;
                                                        padding-left: 1.5rem;
                                                        list-style-type: disc;
                                                    }
                                                    .error-message ul li {
                                                        margin-bottom: 0.5rem;
                                                        line-height: 1.4;
                                                    }
                                                    .error-message ul li:last-child {
                                                        margin-bottom: 0;
                                                    }
                                                    .error-message ul li strong {
                                                        font-weight: 600;
                                                        color: #991b1b;
                                                        display: inline-block;
                                                        min-width: 100px;
                                                    }
                                                    .error-message ul li::marker {
                                                        color: #991b1b;
                                                    }
                                                    .opacity-0 {
                                                        opacity: 0;
                                                    }
                                                    .transition-opacity {
                                                        transition-property: opacity;
                                                        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                                                    }
                                                    .duration-500 {
                                                        transition-duration: 500ms;
                                                    }
                                                </style>
                                            `);

                    @if(session('success'))
                        showToast("{{ session('success') }}", 'success');
                    @endif

                            @if(session('error'))
                                showToast("{{ session('error') }}", 'error');
                            @endif

                            const selectAllCalibrations = document.getElementById('selectAllCalibrations');
                    if (selectAllCalibrations) {
                        selectAllCalibrations.addEventListener('change', function () {
                            const isChecked = this.checked;
                            document.querySelectorAll('.calibration-checkbox').forEach(checkbox => {
                                checkbox.checked = isChecked;
                            });
                            updateBulkDeleteButtonVisibility();
                        });

                        document.addEventListener('change', function (e) {
                            if (e.target.classList.contains('calibration-checkbox')) {
                                const allCheckboxes = document.querySelectorAll('.calibration-checkbox');
                                const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                                selectAllCalibrations.checked = allCheckboxes.length === checkedCheckboxes.length;
                                selectAllCalibrations.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                                updateBulkDeleteButtonVisibility();
                            }
                        });
                    }

                    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                    function updateBulkDeleteButtonVisibility() {
                        const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                        if (checkedCheckboxes.length > 0) {
                            bulkDeleteBtn.classList.remove('hidden');
                        } else {
                            bulkDeleteBtn.classList.add('hidden');
                        }
                    }

                    if (bulkDeleteBtn) {
                        bulkDeleteBtn.addEventListener('click', function () {
                            const checkedCheckboxes = document.querySelectorAll('.calibration-checkbox:checked');
                            if (checkedCheckboxes.length === 0) {
                                showToast('Tidak ada kalibrasi yang dipilih', 'error');
                                return;
                            }

                            const selectedIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));
                            const deleteCalibrationName = document.getElementById('deleteCalibrationName');
                            if (deleteCalibrationName) {
                                deleteCalibrationName.textContent = `${selectedIds.length} selected calibration records`;
                            }

                            const calibrationForm = document.getElementById('deleteCalibrationForm');
                            if (calibrationForm) {
                                calibrationForm.action = "{{ route('calibrations.bulk.delete') }}";
                                let hiddenInput = document.getElementById('delete_calibration_id');
                                if (!hiddenInput) {
                                    hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.id = 'delete_calibration_id';
                                    calibrationForm.appendChild(hiddenInput);
                                }

                                hiddenInput.value = selectedIds.join(',');
                                openModal(modals.delete, modalContents.delete);
                            }
                        });
                    }

                    window.changePerPage = function (limit) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('limit', limit);
                        window.location.href = url.toString();
                    }

                    const statusFilterSelect = document.getElementById('statusFilter');
                    if (statusFilterSelect) {
                        statusFilterSelect.addEventListener('change', function () {
                            applyFilters();
                        });
                    }

                    const resultFilterSelect = document.getElementById('resultFilter');
                    if (resultFilterSelect) {
                        resultFilterSelect.addEventListener('change', function () {
                            applyFilters();
                        });
                    }

                    const sortOrderSelect = document.getElementById('sortOrder');
                    if (sortOrderSelect) {
                        sortOrderSelect.addEventListener('change', function () {
                            applyFilters();
                        });
                    }

                    function applyFilters() {
                        const searchTerm = document.getElementById('searchInput').value;
                        const statusFilter = document.getElementById('statusFilter').value;
                        const resultFilter = document.getElementById('resultFilter').value;
                        const sortOrder = document.getElementById('sortOrder').value;
                        const url = new URL(window.location.href);

                        if (searchTerm) url.searchParams.set('search', searchTerm);
                        else url.searchParams.delete('search');

                        if (statusFilter) url.searchParams.set('status', statusFilter);
                        else url.searchParams.delete('status');

                        if (resultFilter) url.searchParams.set('result', resultFilter);
                        else url.searchParams.delete('result');

                                if (sortOrder) {
                                    url.searchParams.set('sort', sortOrder);
                                } else {
                                    url.searchParams.delete('sort');
                                }

                        url.searchParams.set('page', 1);
                        window.location.href = url.toString();
                    }

                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('search')) {
                            searchInput.value = urlParams.get('search');
                        }

                        searchInput.addEventListener('input', debounce(function () {
                            applyFilters();
                        }, 500));

                        searchInput.addEventListener('keypress', function (e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                applyFilters();
                            }
                        });
                    }

                    const urlParams = new URLSearchParams(window.location.search);

                    const sortSelect = document.getElementById('sortOrder');
                    if (sortSelect) {
                        Array.from(sortSelect.options).forEach(option => {
                            option.removeAttribute('selected');
                        });

                                const sort = urlParams.get('sort');
                                if (sort) {
                                    sortSelect.value = sort;

                                    if (sortSelect.selectedIndex === -1) {
                                        sortSelect.selectedIndex = 0; // Default to first option if selected sort doesn't exist
                                    }
                                }
                            }

                    const statusSelect = document.getElementById('statusFilter');
                    if (statusSelect) {
                        Array.from(statusSelect.options).forEach(option => {
                            option.removeAttribute('selected');
                        });

                        if (urlParams.has('status') && urlParams.get('status')) {
                            statusSelect.value = urlParams.get('status');

                            if (statusSelect.selectedIndex === -1) {
                                statusSelect.selectedIndex = 1;
                            }
                        } else {
                            statusSelect.selectedIndex = 1;
                        }
                    }

                    const resultSelect = document.getElementById('resultFilter');
                    if (resultSelect) {
                        Array.from(resultSelect.options).forEach(option => {
                            option.removeAttribute('selected');
                        });

                        if (urlParams.has('result') && urlParams.get('result')) {
                            resultSelect.value = urlParams.get('result');

                            if (resultSelect.selectedIndex === -1) {
                                resultSelect.selectedIndex = 1;
                            }
                        } else {
                            resultSelect.selectedIndex = 1;
                        }
                    }

                    const modals = {
                        view: document.getElementById('viewCalibrationModal'),
                        add: document.getElementById('addCalibrationModal'),
                        asset: document.getElementById('assetSelectionModal'),
                        delete: document.getElementById('deleteCalibrationModal'),
                        schedule: document.getElementById('editScheduleModal')
                    };

                    const modalContents = {
                        view: document.getElementById('viewCalibrationModalContent'),
                        add: document.getElementById('addCalibrationModalContent'),
                        asset: document.getElementById('assetSelectionModalContent'),
                        delete: document.getElementById('deleteCalibrationModalContent'),
                        schedule: document.getElementById('editScheduleModalContent')
                    };

                    function openModal(modal, content) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                        }, 10);
                    }

                    function closeModal(modal, content) {
                        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                        setTimeout(() => {
                            modal.classList.add('hidden');

                            if (modal.id === 'addCalibrationModal') {
                                document.getElementById('addCalibrationForm')?.reset();
                                selectedAssets = [];
                                updateSelectedAssetsList();
                            } else if (modal.id === 'viewCalibrationModal') {
                                document.getElementById('performCalibrationForm')?.reset();
                                const filePreview = document.getElementById('file-preview');
                                if (filePreview) filePreview.classList.add('hidden');

                                const vendorSearchInput = document.getElementById('vendor_search');
                                if (vendorSearchInput) vendorSearchInput.value = '';
                                const vendorIdInput = document.getElementById('vendor_id');
                                if (vendorIdInput) vendorIdInput.value = '';
                                const vendorResults = document.getElementById('vendor_results');
                                if (vendorResults) vendorResults.style.display = 'none';
                                const infoMessage = document.getElementById('completed-info-message');
                                if (infoMessage) infoMessage.remove();
                            } else if (modal.id === 'assetSelectionModal') {
                                const assetSearchInput = document.getElementById('assetSearchInput');
                                if (assetSearchInput) assetSearchInput.value = '';
                            }
                        }, 300);
                    }

                    document.querySelectorAll('.close-modal').forEach(button => {
                        button.addEventListener('click', () => {
                            const modalId = button.getAttribute('data-modal');
                            const modal = document.getElementById(modalId);
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        });
                    });

                    document.getElementById('addCalibrationBtn')?.addEventListener('click', function () {
                        const today = new Date().toISOString().split('T')[0];
                        const planningDateInput = document.getElementById('planning_calibration_date');
                        if (planningDateInput) {
                            planningDateInput.setAttribute('min', today);
                        }

                        const selectedAssetsList = document.getElementById('selectedAssetsList');
                        if (selectedAssetsList) {
                            selectedAssetsList.setAttribute('data-current-page', '1');
                        }

                        openModal(modals.add, modalContents.add);
                    });

                    function loadVendors() {
                        if (allVendors && allVendors.length > 0) {
                            populateVendorDropdown(allVendors);
                        } else {
                            fetchVendors();
                        }
                    }

                    function fetchVendors(searchTerm = '', callback = null) {
                        const params = new URLSearchParams({
                            json: 'true',
                            limit: searchTerm ? '20' : '100'
                        });

                        if (searchTerm) {
                            params.append('search', searchTerm);
                        }

                        if (vendorResults && vendorResults.style.display === 'block') {
                            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                        }

                        fetch(`/vendors?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Server responded with status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                let vendors = [];

                                if (Array.isArray(data)) {
                                    vendors = data;
                                } else if (data.vendors && Array.isArray(data.vendors)) {
                                    vendors = data.vendors;
                                } else if (data.data && Array.isArray(data.data)) {
                                    vendors = data.data;
                                }

                                if (!searchTerm) {
                                    allVendors = vendors;
                                    try {
                                        localStorage.setItem('allVendors', JSON.stringify(allVendors));
                                    } catch (e) {
                                        console.error('Error caching vendors:', e);
                                    }

                                    populateVendorDropdown(vendors);
                                }

                                if (callback) {
                                    callback(vendors);
                                }

                                if (searchTerm && vendorResults && vendorResults.style.display === 'block') {
                                    displayVendorResults(vendors);
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching vendors:', error);

                                if (vendorResults && vendorResults.style.display === 'block') {
                                    vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';
                                }

                                if (typeof error === 'object' && error !== null) {
                                    showToast(error, 'error');
                                } else {
                                    showToast('Gagal memuat vendor: ' + error.message, 'error');
                                }

                                if (callback) {
                                    callback([]);
                                }
                            });
                    }

                    function populateVendorDropdown(vendors) {
                        const select = document.getElementById('vendor_id');
                        if (!select) return;

                        select.innerHTML = '<option value="">Select Vendor</option>';

                        vendors.forEach(vendor => {
                            const option = document.createElement('option');
                            option.value = vendor.vendor_id;
                            option.textContent = vendor.vendor_name;
                            select.appendChild(option);
                        });
                    }

                    document.querySelectorAll('.perform-calibration-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const calibrationId = this.getAttribute('data-id');
                            document.getElementById('calibration_id').value = calibrationId;

                            // Get the current button and row for updating later
                            const clickedButton = this;
                            const currentRow = clickedButton.closest('tr');
                            const statusBadge = currentRow.querySelector('td:nth-child(11) span');

                            // Show loading state on the button
                            const originalButtonHTML = clickedButton.innerHTML;
                            clickedButton.innerHTML = `
                                                                <svg class="animate-spin h-5 w-5 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            `;
                            clickedButton.disabled = true;

                            // First fetch the calibration details to check status
                            fetch(`/calibrations/${calibrationId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            throw data;
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        const calibration = data.data;
                                        const status = (calibration.status_calibration || '').toLowerCase();

                                        // Only call start endpoint if status is 'scheduled'
                                        if (status === 'scheduled') {
                                            // Call the start calibration endpoint
                                            return fetch(`/calibrations/${calibrationId}/start`, {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                }
                                            })
                                                .then(response => {
                                                    if (!response.ok) {
                                                        throw new Error('Failed to start calibration');
                                                    }
                                                    return response.json();
                                                })
                                                .then(startData => {
                                                    if (startData.success) {
                                                        if (statusBadge) {
                                                            statusBadge.textContent = 'Dalam Proses';
                                                            statusBadge.className = 'px-2 py-1 rounded text-xs inline-block w-full text-center whitespace-nowrap bg-yellow-100 text-yellow-800';
                                                        }
                                                        return fetch(`/calibrations/${calibrationId}`, {
                                                            headers: {
                                                                'Accept': 'application/json',
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        })
                                                            .then(response => response.json())
                                                            .then(updatedData => {
                                                                if (updatedData.success) {
                                                                    return fetchCalibrationDetails(calibrationId);
                                                                }
                                                                return fetchCalibrationDetails(calibrationId);
                                                            });
                                                    }
                                                    return fetchCalibrationDetails(calibrationId);
                                                })
                                                .catch(error => {
                                                    console.error('Error starting calibration:', error);
                                                    return fetchCalibrationDetails(calibrationId);
                                                });
                                        } else {
                                            return fetchCalibrationDetails(calibrationId);
                                        }
                                    } else {
                                        throw new Error(data.message || 'Failed to fetch calibration details');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching calibration details:', error);
                                    showToast('Error loading calibration: ' + (error.message || 'Unknown error'), 'error');
                                })
                                .finally(() => {
                                    clickedButton.innerHTML = originalButtonHTML;
                                    clickedButton.disabled = false;
                                });
                        });
                    });

                    function fetchCalibrationDetails(calibrationId) {
                            fetch(`/calibrations/${calibrationId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            throw data;
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        const calibration = data.data;

                                        const status = (calibration.status_calibration || '').toLowerCase();
                                        if (status === 'completed' || status === 'approved') {
                                        if (document.querySelector('.perform-calibration-btn path[d*="3.536"]')) {
                                                showToast('Kalibrasi telah selesai dan tidak dapat diubah', 'error');
                                                return;
                                            }
                                        }

                                        const today = new Date().toISOString().split('T')[0];
                                        const actualCalibrationDateInput = document.getElementById('actual_calibration_date');
                                        const nextCalibrationDateInput = document.getElementById('next_calibration_date');

                                        if (actualCalibrationDateInput) {
                                            actualCalibrationDateInput.value = today;
                                            actualCalibrationDateInput.setAttribute('min', today);
                                        }

                                        if (nextCalibrationDateInput) {
                                            nextCalibrationDateInput.setAttribute('min', today);
                                        }

                                        document.getElementById('planning_date_display').value = calibration.planning_calibration_date || '';
                                        document.getElementById('asset_code_display').value = calibration.asset_code || '-';
                                        document.getElementById('asset_name_display').value = calibration.asset_name || '-';
                                        document.getElementById('brand_name_display').value = calibration.brand_name || '-';
                                        document.getElementById('model_display').value = calibration.model || '-';
                                        document.getElementById('serial_number_display').value = calibration.serial_number || '-';

                                        const assetImageDisplay = document.getElementById('asset_image_display');
                                        if (assetImageDisplay) {
                                            if (calibration.asset_image_path) {
                                                assetImageDisplay.src = "{{ config('app.backend_url') }}/public" + calibration.asset_image_path;
                                            } else {
                                                assetImageDisplay.src = "{{ asset('images/placeholder.png') }}";
                                            }
                                        }

                                        let locationText = '-';
                                        if (calibration.location) {
                                            const locationParts = [];
                                            if (calibration.location.room_name) locationParts.push(calibration.location.room_name);
                                        if (calibration.location.floor_number) locationParts.push('Lantai ' + calibration.location.floor_number);
                                            if (calibration.location.building_name) locationParts.push(calibration.location.building_name);
                                            if (locationParts.length > 0) {
                                                locationText = locationParts.join(' | ');
                                            }
                                        }
                                        document.getElementById('location_display').value = locationText;

                                        if (calibration.vendor_id) {
                                            document.getElementById('vendor_id').value = calibration.vendor_id;
                                            document.getElementById('vendor_search').value = calibration.vendor_name || '';
                                        } else {
                                            document.getElementById('vendor_id').value = '';
                                            document.getElementById('vendor_search').value = '';
                                        }

                                        if (calibration.next_calibration_date) {
                                            document.getElementById('next_calibration_date').value = calibration.next_calibration_date;
                                        }

                                        if (calibration.actual_calibration_date) {
                                            document.getElementById('actual_calibration_date').value = calibration.actual_calibration_date;
                                        } else {
                                            const today = new Date().toISOString().split('T')[0];
                                            document.getElementById('actual_calibration_date').value = today;
                                        }

                                        document.getElementById('certificate_number').value = calibration.certificate_number || '';

                                        if (calibration.calibration_price) {
                                            const priceInput = document.getElementById('calibration_price');
                                            priceInput.value = calibration.calibration_price;
                                            formatCurrency(priceInput, 'blur');
                                        } else {
                                            document.getElementById('calibration_price').value = '';
                                        }

                                        document.getElementById('notes').value = calibration.notes || '';

                                        if (calibration.calibration_result === 'pass') {
                                            document.getElementById('result_pass').checked = true;
                                        } else if (calibration.calibration_result === 'fail') {
                                            document.getElementById('result_fail').checked = true;
                                        }

                                        const filePreview = document.getElementById('file-preview');
                                        if (filePreview) filePreview.classList.add('hidden');

                                        const imagePreview = document.getElementById('image-preview');
                                        if (imagePreview) {
                                            imagePreview.src = '';
                                            imagePreview.classList.add('hidden');
                                        }

                                        const fileInfo = document.getElementById('file-info');
                                        if (fileInfo) fileInfo.classList.remove('hidden');

                                        const isCompleted = status === 'completed' || status === 'approved';
                                        const form = document.getElementById('performCalibrationForm');
                                        const formElements = form.querySelectorAll('input, select, textarea, button[type="submit"]');

                                        formElements.forEach(element => {
                                            if (element.id !== 'planning_date_display' &&
                                                element.id !== 'asset_code_display' &&
                                                element.id !== 'asset_name_display' &&
                                                element.id !== 'brand_name_display' &&
                                            element.id !== 'model_display' &&
                                                element.id !== 'serial_number_display' &&
                                                element.id !== 'location_display') {

                                                if (isCompleted) {
                                                    element.setAttribute('disabled', 'disabled');
                                                    if (element.tagName === 'BUTTON' && element.type === 'submit') {
                                                        element.classList.add('bg-gray-500');
                                                        element.classList.remove('bg-[#213268]', 'hover:bg-[#152349]');
                                                    }
                                                } else {
                                                    element.removeAttribute('disabled');
                                                    if (element.tagName === 'BUTTON' && element.type === 'submit') {
                                                        element.classList.remove('bg-gray-500');
                                                        element.classList.add('bg-[#213268]', 'hover:bg-[#152349]');
                                                    }
                                                }
                                            }
                                        });

                                        const infoMessageContainer = document.querySelector('#viewCalibrationModalContent .p-6');
                                        const existingInfoMessage = document.getElementById('completed-info-message');

                                        if (existingInfoMessage) {
                                            existingInfoMessage.remove();
                                        }

                                        if (isCompleted) {
                                            const infoMessage = document.createElement('div');
                                            infoMessage.id = 'completed-info-message';
                                            infoMessage.className = 'bg-blue-50 border-l-4 border-blue-500 p-4 mb-4';
                                            infoMessage.innerHTML = `
                                                                                        <div class="flex items-center">
                                                                                            <div class="flex-shrink-0 text-blue-500">
                                                                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div class="ml-3">
                                                                                                <p class="text-sm text-blue-700">
                                                                                                    Kalibrasi ini ditandai sebagai ${status === 'completed' ? 'SELESAI' : 'DISETUJUI'}. Formulir dalam mode hanya-baca.
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;
                                            infoMessageContainer.insertAdjacentElement('afterbegin', infoMessage);
                                        }

                                        openModal(modals.view, modalContents.view);
                                    } else {
                                        showToast('Gagal memuat detail kalibrasi', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    if (typeof error === 'object' && error !== null) {
                                        showToast(error, 'error');
                                    } else {
                                        showToast('Terjadi kesalahan saat memuat detail kalibrasi', 'error');
                                    }
                                });
                    }

                    document.getElementById('document_file')?.addEventListener('change', function () {
                        const file = this.files[0];
                        if (file) {
                            const filePreview = document.getElementById('file-preview');
                            const imagePreview = document.getElementById('image-preview');
                            const fileInfo = document.getElementById('file-info');
                            const fileNameText = document.getElementById('file-name-text');

                            if (filePreview) filePreview.classList.remove('hidden');

                            if (fileNameText) fileNameText.textContent = file.name;

                            if (file.type.startsWith('image/')) {
                                if (imagePreview) {
                                    const objectUrl = URL.createObjectURL(file);
                                    imagePreview.src = objectUrl;
                                    imagePreview.classList.remove('hidden');
                                }
                                if (fileInfo) fileInfo.classList.add('hidden');
                            } else {
                                if (fileInfo) fileInfo.classList.remove('hidden');
                                if (imagePreview) imagePreview.classList.add('hidden');
                            }
                        }
                    });

                    document.getElementById('remove-file')?.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const fileInput = document.getElementById('document_file');
                        if (fileInput) fileInput.value = '';

                        const filePreview = document.getElementById('file-preview');
                        if (filePreview) filePreview.classList.add('hidden');

                        const imagePreview = document.getElementById('image-preview');
                        if (imagePreview && imagePreview.src) {
                            URL.revokeObjectURL(imagePreview.src);
                            imagePreview.src = '';
                        }
                    });

                    document.querySelectorAll('.delete-calibration-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const calibrationId = this.getAttribute('data-id');
                            const calibrationForm = document.getElementById('deleteCalibrationForm');
                            const deleteCalibrationName = document.getElementById('deleteCalibrationName');
                            const assetName = this.closest('tr').querySelector('td:nth-child(3) .font-medium').textContent;
                            const assetCode = this.closest('tr').querySelector('td:nth-child(3) .text-gray-500').textContent;
                            calibrationForm.action = "{{ route('calibrations.bulk.delete') }}";

                            let hiddenInput = document.getElementById('delete_calibration_id');
                            if (!hiddenInput) {
                                hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.id = 'delete_calibration_id';
                                calibrationForm.appendChild(hiddenInput);
                            }
                            hiddenInput.value = calibrationId;
                            deleteCalibrationName.textContent = `${assetName} (${assetCode})`;
                            openModal(modals.delete, modalContents.delete);
                        });
                    });

                    if (document.getElementById('deleteCalibrationForm')) {
                        document.getElementById('deleteCalibrationForm').addEventListener('submit', function (e) {
                            e.preventDefault();

                            const form = this;
                            const calibrationIdInput = document.getElementById('delete_calibration_id').value;

                            const isMultiple = calibrationIdInput.includes(',');
                            let ids = [];

                            if (isMultiple) {
                                ids = calibrationIdInput.split(',').map(id => parseInt(id.trim()));
                            } else {
                                ids = [parseInt(calibrationIdInput)];
                            }

                            fetch("{{ route('calibrations.bulk.delete') }}", {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ ids })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            console.error('Server error response:', data);
                                            throw new Error(data.message || `Server merespons dengan status ${response.status}`);
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    closeModal(modals.delete, modalContents.delete);

                                    if (data.success) {
                                        showToast(data.message || 'Kalibrasi berhasil dihapus', 'success');

                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    } else {
                                        showToast(data.message || data.error || 'Gagal menghapus kalibrasi', 'error');
                                        console.error('Delete error:', data.errors);
                                    }
                                })
                                .catch(error => {
                                    console.error('Delete request failed:', error);
                                    closeModal(modals.delete, modalContents.delete);

                                    if (typeof error === 'object' && error !== null) {
                                        if (error.errors) {
                                            if (typeof error.errors === 'string') {
                                                showToast(error.errors, 'error');
                                            }
                                            else if (typeof error.errors === 'object') {
                                                const errorMessages = [];
                                                Object.values(error.errors).forEach(err => {
                                                    if (Array.isArray(err)) {
                                                        errorMessages.push(...err);
                                                    } else {
                                                        errorMessages.push(err);
                                                    }
                                                });
                                                showToast(errorMessages.join(', '), 'error');
                                            }
                                        } else if (error.message) {
                                            showToast(error.message, 'error');
                                        } else {
                                            showToast('Gagal menghapus kalibrasi', 'error');
                                        }
                                    } else {
                                        showToast('Terjadi kesalahan saat menghapus kalibrasi', 'error');
                                    }
                                });
                        });
                    }

                    if (document.getElementById('performCalibrationForm')) {
                        document.getElementById('performCalibrationForm').addEventListener('submit', function (e) {
                            e.preventDefault();

                            const calibrationId = document.getElementById('calibration_id').value;
                            const formData = new FormData();
                            const formElements = this.elements;

                            // Process calibration price - convert from formatted to numeric value
                            const calibrationPrice = document.getElementById('calibration_price');
                            if (calibrationPrice && calibrationPrice.value) {
                                // Convert from formatted string (1.234,56) to numeric value (1234.56)
                                let numericValue = calibrationPrice.value
                                    .replace(/\./g, '')  // Remove thousand separators
                                    .replace(',', '.');  // Replace comma with dot for decimal

                                formData.append('calibration_price', numericValue);
                            }

                            for (let i = 0; i < formElements.length; i++) {
                                const element = formElements[i];

                                if (element.type === 'button' || element.type === 'submit' ||
                                    element.tagName === 'FIELDSET' || element.name === 'calibration_id' ||
                                    element.name === 'calibration_price') {
                                    continue;
                                }

                                if (element.type === 'radio' || element.type === 'checkbox') {
                                    if (element.checked) {
                                        formData.append(element.name, element.value);
                                    }
                                }
                                else if (element.type === 'file') {
                                    if (element.files && element.files.length > 0) {
                                        formData.append(element.name, element.files[0]);
                                    }
                                }
                                else if (element.value.trim() !== '') {
                                    formData.append(element.name, element.value.trim());
                                } else {
                                }
                            }

                            formData.append('_method', 'PUT');

                            fetch(`/calibrations/report/${calibrationId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            throw data;
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        closeModal(modals.view, modalContents.view);

                                        showToast(data.message || 'Kalibrasi berhasil diperbarui', 'success');

                                        setTimeout(() => {
                                            window.location.href = "{{ route('calibration') }}";
                                        }, 1000);
                                    } else {
                                        showToast(data.message || data.error || 'Gagal memperbarui kalibrasi', 'error');
                                    }
                                })
                                .catch(error => {
                                    // Enhanced error handling to support various error formats
                                    console.error('Form submission error:', error);

                                    try {
                                        // Check if error is a string that contains JSON
                                        if (typeof error === 'string' && (error.startsWith('{') || error.startsWith('['))) {
                                            try {
                                                const parsedError = JSON.parse(error);
                                                showToast(parsedError, 'error');
                                                return;
                                            } catch (e) {
                                                // If JSON parsing fails, continue with normal handling
                                            }
                                        }

                                        // Handle specific error format {"success":"false","errors":"message"}
                                        if (error && typeof error === 'object') {
                                            if (error.errors && typeof error.errors === 'string') {
                                                showToast(error.errors, 'error');
                                                return;
                                            }
                                            // Continue with existing error handling for other formats
                                        }

                                        // Fallback to original handler
                                        showToast(error, 'error');
                                    } catch (e) {
                                        showToast('Terjadi kesalahan saat memproses kalibrasi', 'error');
                                    }
                                });
                        });
                    }

                    let selectedAssets = [];

                    document.getElementById('addAssetsBtn')?.addEventListener('click', function () {
                        openModal(document.getElementById('assetSelectionModal'), document.getElementById('assetSelectionModalContent'));
                        loadAssets();
                    });

                    function debounce(func, wait) {
                        let timeout;
                        return function () {
                            const context = this;
                            const args = arguments;
                            clearTimeout(timeout);
                            timeout = setTimeout(() => {
                                func.apply(context, args);
                            }, wait);
                        };
                    }

                    document.getElementById('assetSearchInput')?.addEventListener('input', debounce(function () {
                        loadAssets(1);
                    }, 500));

                    function loadAssets(page = 1) {
                        const searchTerm = document.getElementById('assetSearchInput').value;
                        const limit = document.getElementById('assetPerPageSelect').value;

                        document.getElementById('assetSelectionList').innerHTML = `
                                                        <tr>
                                                            <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Memuat aset...</td>
                                                        </tr>
                                                    `;

                        fetch(`/calibrations/assets?page=${page}&limit=${limit}&search=${encodeURIComponent(searchTerm)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Server merespon dengan status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message || 'Failed to fetch assets for calibration');
                                }

                                const assets = data.data || [];

                                if (assets.length === 0) {
                                    document.getElementById('assetSelectionList').innerHTML = `
                                                                    <tr>
                                                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                            Tidak ditemukan aset yang memerlukan kalibrasi. Hanya aset dengan "Perlu Kalibrasi" diatur sebagai benar dan
                                                                            tanpa jadwal kalibrasi aktif yang akan muncul dalam daftar ini.
                                                                        </td>
                                                                    </tr>
                                                                `;

                                    const zeroPagination = {
                                        current_page: 1,
                                        total_items: 0,
                                        total_pages: 1
                                    };
                                    setupAssetPagination(zeroPagination);
                                    return;
                                }

                                renderAssets(assets, data);
                            })
                            .catch(error => {
                                document.getElementById('assetSelectionList').innerHTML = `
                                                              <tr>
                                                                  <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                                                                      Gagal memuat aset yang memerlukan kalibrasi. Silakan coba lagi atau hubungi dukungan jika masalah tetap berlanjut.
                                                                  </td>
                                                              </tr>
                                                          `;

                                showToast('Gagal memuat aset: ' + error.message, 'error');
                            });
                    }

                    function showErrorNotification(message) {
                        showToast(message, 'error');
                    }

                    function renderAssets(assets, data) {
                        if (!assets || assets.length === 0) {
                            document.getElementById('assetSelectionList').innerHTML = `
                                                              <tr>
                                                                  <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                      Tidak ditemukan aset yang perlu kalibrasi. Hanya aset dengan "Perlu Kalibrasi" diaktifkan yang akan muncul dalam daftar ini.
                                                                  </td>
                                                              </tr>
                                                          `;
                            return;
                        }

                        let html = '';
                        assets.forEach(asset => {
                            const isSelected = selectedAssets.some(selectedAsset => selectedAsset.asset_id === asset.asset_id);
                            const assetName = asset.asset_master_name ||
                                (asset.asset_master && asset.asset_master.asset_name) ||
                                '-';
                            const assetCode = asset.asset_code || '-';

                            let assetType = 'Non Medis';
                            if (asset.asset_master && asset.asset_master.asset_master_code) {
                                const code = asset.asset_master.asset_master_code;
                                if (code.startsWith('MED-')) {
                                    assetType = 'Medis';
                                }
                            }

                            const categoryName = asset.asset_master && asset.asset_master.subcategory_name ?
                                asset.asset_master.subcategory_name : '-';
                            const description = asset.asset_master && asset.asset_master.description ?
                                asset.asset_master.description : '-';

                            html += `
                                                              <tr>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                      <input type="checkbox" class="asset-checkbox" value="${asset.asset_id}"
                                                                          data-id="${asset.asset_id}"
                                                                          data-code="${assetCode}"
                                                                          data-name="${assetName}"
                                                                          data-description="${description}"
                                                                          data-type="${assetType}"
                                                                          data-category="${categoryName}"
                                                                          ${isSelected ? 'checked' : ''}>
                                                                  </td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${assetCode}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                      <div class="flex flex-col">
                                                                          <span class="font-medium">${assetName}</span>
                                                                      </div>
                                                                  </td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${description}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${assetType}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${categoryName}</td>
                                                              </tr>
                                                          `;
                        });

                        document.getElementById('assetSelectionList').innerHTML = html;

                        if (data) {
                            const limit = parseInt(document.getElementById('assetPerPageSelect').value, 10) || 10;
                            const paginationData = data.success ? data.pagination : data.assets_pagination;
                            if (paginationData) {
                                const serverTotalItems = paginationData.total_items || paginationData.total || 0;
                                const updatedPagination = {
                                    ...paginationData,
                                    total_items: serverTotalItems,
                                    total_pages: Math.max(1, Math.ceil(serverTotalItems / limit)),
                                    current_page: Math.min(
                                        paginationData.current_page || 1,
                                        Math.max(1, Math.ceil(serverTotalItems / limit))
                                    )
                                };
                                setupAssetPagination(updatedPagination);
                            }
                        }
                        attachCheckboxHandlers();
                    }

                    function attachCheckboxHandlers() {
                        const checkboxes = document.querySelectorAll('.asset-checkbox');
                        checkboxes.forEach(checkbox => {
                            const newCheckbox = checkbox.cloneNode(true);
                            checkbox.parentNode.replaceChild(newCheckbox, checkbox);
                        });
                        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                            checkbox.onclick = function () {
                                const assetId = parseInt(this.getAttribute('data-id'));
                                selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);
                                if (this.checked) {
                                    const assetName = this.getAttribute('data-name');
                                    const assetCode = this.getAttribute('data-code');
                                    const description = this.getAttribute('data-description');
                                    const assetType = this.getAttribute('data-type');
                                    const categoryName = this.getAttribute('data-category');

                                    selectedAssets.push({
                                        id: assetId,
                                        asset_id: assetId,
                                        asset_name: assetName,
                                        asset_code: assetCode,
                                        description: description,
                                        asset_type: assetType,
                                        category_name: categoryName,
                                        asset_master: {
                                            asset_name: assetName,
                                            asset_master_code: assetType === 'Medical' ? 'MED-' : 'NMED-',
                                            subcategory_name: categoryName
                                        }
                                    });
                                }

                                updateSelectedAssetsList();
                            };
                        });

                        const selectAllCheckbox = document.getElementById('selectAllAssets');
                        if (selectAllCheckbox) {
                            const newSelectAll = selectAllCheckbox.cloneNode(true);
                            selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
                            document.getElementById('selectAllAssets').onclick = function () {
                                const checkboxes = document.querySelectorAll('.asset-checkbox');
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = this.checked;
                                    if (checkbox.onclick) checkbox.onclick();
                                });
                            };
                        }
                    }

                    function setupAssetPagination(pagination) {
                        if (!pagination) return;

                        const paginationInfo = document.getElementById('assetPaginationInfo');
                        const paginationControls = document.getElementById('assetPaginationControls');
                        const currentPage = pagination.current_page || 1;
                        const totalPages = pagination.total_pages || pagination.last_page || 1;
                        const totalItems = pagination.total_items || pagination.total || 0;
                        const limit = pagination.limit || pagination.per_page || 10;
                        const from = pagination.from || ((currentPage - 1) * limit + 1);
                        const to = pagination.to || Math.min(currentPage * limit, totalItems);

                        if (paginationInfo) {
                            paginationInfo.textContent = `Menampilkan ${from} sampai ${to} dari ${totalItems} data`;
                        }

                        let controlsHtml = '';

                        controlsHtml += `
                                                          <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                                             ${currentPage > 1 ? 'data-page="' + (currentPage - 1) + '"' : ''}>
                                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                              </svg>
                                                              Sebelumnya
                                                          </a>
                                                      `;

                        if (totalItems > 0) {
                            controlsHtml += '<div class="flex gap-2">';

                            const maxPagesShown = 5;
                            let startPage = Math.max(1, currentPage - 2);
                            let endPage = Math.min(totalPages, startPage + maxPagesShown - 1);

                            if (endPage - startPage + 1 < maxPagesShown) {
                                startPage = Math.max(1, endPage - maxPagesShown + 1);
                            }

                            if (startPage > 1) {
                                controlsHtml += `
                                                                  <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                      data-page="1">1</a>
                                                              `;

                                if (startPage > 2) {
                                    controlsHtml += '<span class="flex items-center justify-center">...</span>';
                                }
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                controlsHtml += `
                                                                  <a href="#" class="h-8 w-8 flex items-center justify-center border ${i === currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]'} rounded"
                                                                     data-page="${i}">${i}</a>
                                                              `;
                            }

                            if (endPage < totalPages) {
                                if (endPage < totalPages - 1) {
                                    controlsHtml += '<span class="flex items-center justify-center">...</span>';
                                }

                                controlsHtml += `
                                                                  <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                      data-page="${totalPages}">${totalPages}</a>
                                                              `;
                            }

                            controlsHtml += '</div>';
                        }

                        controlsHtml += `
                                                          <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                                                             ${currentPage < totalPages ? 'data-page="' + (currentPage + 1) + '"' : ''}>
                                                              Selanjutnya
                                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                              </svg>
                                                          </a>
                                                      `;

                        if (paginationControls) {
                            paginationControls.innerHTML = controlsHtml;
                            paginationControls.querySelectorAll('a[data-page]').forEach(link => {
                                link.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    const page = parseInt(this.getAttribute('data-page'), 10);
                                    if (!isNaN(page)) {
                                        loadAssets(page);
                                    }
                                });
                            });
                        }
                    }

                    document.getElementById('selectAssetsBtn')?.addEventListener('click', function () {
                        const uniqueAssetIds = [...new Set(selectedAssets.map(asset => asset.id))];
                        if (uniqueAssetIds.length < selectedAssets.length) {
                            const uniqueAssets = [];
                            const seenIds = new Set();
                            selectedAssets.forEach(asset => {
                                if (!seenIds.has(asset.id)) {
                                    uniqueAssets.push(asset);
                                    seenIds.add(asset.id);
                                }
                            });

                            selectedAssets = uniqueAssets;
                        }

                        closeModal(document.getElementById('assetSelectionModal'), document.getElementById('assetSelectionModalContent'));

                        updateSelectedAssetsList();
                    });

                    function updateSelectedAssetsTable() {
                        if (selectedAssets.length === 0) {
                            document.getElementById('selectedAssetsList').innerHTML = `
                                                              <tr>
                                                                  <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada data yang tersedia</td>
                                                              </tr>
                                                          `;
                            return;
                        }

                        let html = '';
                        selectedAssets.forEach((asset, index) => {
                            const assetName = asset.asset_name ||
                                (asset.asset_master && asset.asset_master.asset_name) ||
                                '-';
                            const categoryName = asset.category_name ||
                                (asset.asset_master && asset.asset_master.subcategory_name) ||
                                '-';

                            html += `
                                                              <tr>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">${index + 1}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_code || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                      <div class="flex flex-col">
                                                                          <span class="font-medium">${assetName}</span>
                                                                      </div>
                                                                  </td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.description || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_type || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${categoryName}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                      <button type="button" class="text-red-500 hover:text-red-700" onclick="removeSelectedAsset(${asset.id})">
                                                                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                          </svg>
                                                                      </button>
                                                                  </td>
                                                              </tr>
                                                          `;
                        });

                        document.getElementById('selectedAssetsList').innerHTML = html;
                    }

                    document.getElementById('selectedAssetsPerPage')?.addEventListener('change', function () {
                        const selectedAssetsList = document.getElementById('selectedAssetsList');
                        if (selectedAssetsList) {
                            selectedAssetsList.setAttribute('data-current-page', '1');
                            updateSelectedAssetsList();
                        }
                    });

                    window.removeSelectedAsset = function (assetId) {
                        selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);

                        const selectedAssetsList = document.getElementById('selectedAssetsList');
                        const perPage = parseInt(document.getElementById('selectedAssetsPerPage').value, 10) || 5;
                        const currentPage = parseInt(selectedAssetsList.getAttribute('data-current-page') || '1', 10);
                        const newTotalPages = Math.ceil(selectedAssets.length / perPage);

                        if (currentPage > newTotalPages && newTotalPages > 0) {
                            selectedAssetsList.setAttribute('data-current-page', newTotalPages);
                        }

                        updateSelectedAssetsList();
                    }

                    document.getElementById('addCalibrationForm')?.addEventListener('submit', function (e) {
                        e.preventDefault();

                        if (selectedAssets.length === 0) {
                            showToast('Silakan pilih minimal satu aset untuk kalibrasi.', 'error');
                            return;
                        }

                        const planningDate = document.getElementById('planning_calibration_date').value;

                        if (!planningDate) {
                            showToast('Silakan pilih tanggal jadwal mulai.', 'error');
                            return;
                        }

                        console.clear();
                        const uniqueAssetIds = [...new Set(selectedAssets.map(asset => asset.asset_id))];

                        if (uniqueAssetIds.length < selectedAssets.length) {
                            const uniqueAssets = [];
                            const seenIds = new Set();

                            selectedAssets.forEach(asset => {
                                if (!seenIds.has(asset.asset_id)) {
                                    uniqueAssets.push(asset);
                                    seenIds.add(asset.asset_id);
                                }
                            });

                            selectedAssets = uniqueAssets;
                            updateSelectedAssetsTable();
                            showToast('Aset duplikat telah terdeteksi dan dihapus.', 'success');
                        }

                        const formData = {
                            asset_ids: uniqueAssetIds,
                            planning_calibration_date: planningDate
                        };

                        fetch('{{ route('calibrations.bulk.create') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        throw data;
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    closeModal(document.getElementById('addCalibrationModal'), document.getElementById('addCalibrationModalContent'));
                                    showToast(data.message || 'Kalibrasi berhasil dibuat', 'success');
                                    setTimeout(() => {
                                        window.location.href = "{{ route('calibration') }}";
                                    }, 1000);
                                } else {
                                    showToast(data.message || data.error || 'Gagal membuat kalibrasi', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error creating calibrations:', error);
                                if (typeof error === 'object' && error !== null) {
                                    if (error.errors) {
                                        if (typeof error.errors === 'string') {
                                            showToast(error.errors, 'error');
                                        }
                                        else if (typeof error.errors === 'object') {
                                            const errorMessages = [];
                                            Object.keys(error.errors).forEach(key => {
                                                const value = error.errors[key];
                                                if (Array.isArray(value)) {
                                                    errorMessages.push(...value);
                                                } else {
                                                    errorMessages.push(value);
                                                }
                                            });
                                            showToast(errorMessages.join(', '), 'error');
                                        }
                                    } else if (error.message) {
                                        showToast(error.message, 'error');
                                    } else {
                                        showToast('Terjadi kesalahan saat membuat kalibrasi.', 'error');
                                    }
                                } else {
                                    showToast('Terjadi kesalahan saat membuat kalibrasi.', 'error');
                                }
                            });
                    });

                    document.getElementById('assetPerPageSelect')?.addEventListener('change', function () {
                        loadAssets(1);
                    });

                    function debounce(func, wait) {
                        let timeout;
                        return function () {
                            const context = this;
                            const args = arguments;
                            clearTimeout(timeout);
                            timeout = setTimeout(() => {
                                func.apply(context, args);
                            }, wait);
                        };
                    }

                    let allVendors = [];
                    const vendorSearchInput = document.getElementById('vendor_search');
                    const vendorIdInput = document.getElementById('vendor_id');
                    const vendorResults = document.getElementById('vendor_results');

                    function loadAllVendors() {
                        const cachedVendors = localStorage.getItem('allVendors');
                        if (cachedVendors) {
                            try {
                                allVendors = JSON.parse(cachedVendors);
                                if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                    filterAndDisplayVendors(vendorSearchInput.value.trim());
                                } else if (vendorResults) {
                                    vendorResults.style.display = 'none';
                                }
                                fetchVendors();
                                return;
                            } catch (e) {
                                console.error('Error parsing cached vendors:', e);
                            }
                        }
                        fetchVendors();
                    }

                    function filterAndDisplayVendors(searchTerm) {
                        if (vendorResults) vendorResults.style.display = 'block';
                        if (searchTerm && searchTerm.length > 0) {
                            fetchVendors(searchTerm, displayVendorResults);
                        } else {
                            if (allVendors.length > 0) {
                                displayVendorResults(allVendors.slice(0, 20));
                            } else {
                                fetchVendors('', vendors => displayVendorResults(vendors.slice(0, 20)));
                            }
                        }
                    }

                    document.querySelectorAll('.edit-schedule-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const calibrationId = this.getAttribute('data-id');
                            const assetName = this.getAttribute('data-asset-name');
                            const assetCode = this.getAttribute('data-asset-code');

                            document.getElementById('edit_schedule_calibration_id').value = calibrationId;
                            document.getElementById('edit_schedule_asset_name').textContent = assetName;
                            document.getElementById('edit_schedule_asset_code').textContent = assetCode;

                            const today = new Date().toISOString().split('T')[0];
                            const planningDateInput = document.getElementById('edit_planning_calibration_date');
                            planningDateInput.setAttribute('min', today);
                            const errorMessage = document.querySelector('#editScheduleModal .error-message');
                            const dateErrorMessage = document.querySelector('#editScheduleModal .date-error-message');
                            if (errorMessage) errorMessage.classList.add('hidden');
                            if (dateErrorMessage) dateErrorMessage.classList.add('hidden');
                            planningDateInput.classList.remove('border-red-500');
                            fetch(`/calibrations/${calibrationId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Server responded with status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success && data.data) {
                                        const calibration = data.data;
                                        if (calibration.planning_calibration_date) {
                                            if (calibration.planning_calibration_date >= today) {
                                                planningDateInput.value = calibration.planning_calibration_date;
                                            } else {
                                                planningDateInput.value = today;
                                            }
                                        } else {
                                            planningDateInput.value = today;
                                        }
                                    } else {
                                        console.error('Failed to get calibration data:', data);
                                        planningDateInput.value = today;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching calibration data:', error);
                                    planningDateInput.value = today;
                                    showToast(`Error loading calibration data: ${error.message}`, 'error');
                                });

                            openModal(document.getElementById('editScheduleModal'), document.getElementById('editScheduleModalContent'));
                        });
                    });

                    document.getElementById('editScheduleForm')?.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const planningDateInput = document.getElementById('edit_planning_calibration_date');
                        const errorMessage = planningDateInput.closest('.space-y-2').querySelector('.error-message');
                        const dateErrorMessage = planningDateInput.closest('.space-y-2').querySelector('.date-error-message');
                        planningDateInput.classList.remove('border-red-500');
                        errorMessage.classList.add('hidden');
                        dateErrorMessage.classList.add('hidden');

                        if (!planningDateInput.value.trim()) {
                            planningDateInput.classList.add('border-red-500');
                            errorMessage.classList.remove('hidden');
                            return;
                        }

                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const selectedDate = new Date(planningDateInput.value);
                        selectedDate.setHours(0, 0, 0, 0);

                        if (selectedDate < today) {
                            planningDateInput.classList.add('border-red-500');
                            dateErrorMessage.classList.remove('hidden');
                            return;
                        }

                        const calibrationId = document.getElementById('edit_schedule_calibration_id').value;
                        const planningDate = planningDateInput.value;

                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                                                          <div class="flex items-center justify-center">
                                                              <div class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-white border-r-transparent mr-2"></div>
                                                              <span>Memproses...</span>
                                                          </div>
                                                      `;

                        fetch(`/calibrations/schedule/${calibrationId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                planning_calibration_date: planningDate
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                if (data.success) {
                                    closeModal(document.getElementById('editScheduleModal'), document.getElementById('editScheduleModalContent'));
                                    showToast(data.message || 'Jadwal kalibrasi berhasil diperbarui', 'success');
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    showToast(data.message || data.error || 'Gagal memperbarui jadwal kalibrasi', 'error');
                                }
                            })
                            .catch(error => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                                console.error('Error updating calibration schedule:', error);
                                showToast('Terjadi kesalahan saat memperbarui jadwal kalibrasi', 'error');
                            });
                    });

                    document.getElementById('edit_planning_calibration_date')?.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorMessage = this.closest('.space-y-2').querySelector('.error-message');
                        const dateErrorMessage = this.closest('.space-y-2').querySelector('.date-error-message');
                        if (errorMessage) errorMessage.classList.add('hidden');
                        if (dateErrorMessage) dateErrorMessage.classList.add('hidden');
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const selectedDate = new Date(this.value);
                        selectedDate.setHours(0, 0, 0, 0);

                        if (selectedDate < today) {
                            this.classList.add('border-red-500');
                            dateErrorMessage.classList.remove('hidden');
                        }
                    });

                    vendorSearchInput?.addEventListener('focus', function () {
                        filterAndDisplayVendors(this.value.trim());
                        vendorResults.style.display = 'block';
                    });

                    document.addEventListener('click', function (e) {
                        if (vendorResults && e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                            vendorResults.style.display = 'none';
                        }
                    });

                    vendorSearchInput?.addEventListener('input', debounce(function () {
                        const searchTerm = this.value.trim();
                        filterAndDisplayVendors(searchTerm);
                    }, 300));

                    function loadAllVendors() {
                        const cachedVendors = localStorage.getItem('allVendors');
                        if (cachedVendors) {
                            try {
                                allVendors = JSON.parse(cachedVendors);
                                if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                    filterAndDisplayVendors(vendorSearchInput.value.trim());
                                } else if (vendorResults) {
                                    vendorResults.style.display = 'none';
                                }
                                fetchVendors();
                                return;
                            } catch (e) {
                                console.error('Error parsing cached vendors:', e);
                            }
                        }
                        fetchVendors();
                    }
                    function filterAndDisplayVendors(searchTerm) {
                        if (vendorResults) vendorResults.style.display = 'block';
                        if (searchTerm && searchTerm.length > 0) {
                            fetchVendors(searchTerm, displayVendorResults);
                        } else {
                            if (allVendors.length > 0) {
                                displayVendorResults(allVendors.slice(0, 20));
                            } else {
                                fetchVendors('', vendors => displayVendorResults(vendors.slice(0, 20)));
                            }
                        }
                    }

                    function displayVendorResults(vendors) {
                        vendorResults.innerHTML = '';

                        if (vendors.length === 0) {
                            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Vendor tidak ditemukan</div>';
                            return;
                        }

                        vendors.forEach((vendor, index) => {
                            const div = document.createElement('div');
                            div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                            div.textContent = vendor.vendor_name;
                            div.setAttribute('data-id', vendor.vendor_id);
                            div.style.animationDelay = `${index * 30}ms`;

                            div.addEventListener('click', function () {
                                vendorIdInput.value = this.getAttribute('data-id');
                                vendorSearchInput.value = this.textContent;
                                vendorResults.style.display = 'none';
                            });

                            vendorResults.appendChild(div);
                        });

                        if (vendors.length === 20) {
                            const countDiv = document.createElement('div');
                            countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                            countDiv.textContent = `Menampilkan 20 hasil pertama`;
                            vendorResults.appendChild(countDiv);
                        }
                    }
                    document.getElementById('exportBtn')?.addEventListener('click', () => {
                        const url = new URL(window.location.href);
                        const searchParams = url.searchParams;
                        const exportUrl = "{{ route('calibrations.export.pdf') }}?" + searchParams.toString();
                        window.open(exportUrl, '_blank');
                    });

                    function updateSelectedAssetsList() {
                        const selectedAssetsList = document.getElementById('selectedAssetsList');

                        if (selectedAssets.length === 0) {
                            document.getElementById('selectedAssetsList').innerHTML = `
                                                              <tr>
                                                                  <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada data yang tersedia</td>
                                                              </tr>
                                                          `;
                            const paginationContainer = document.getElementById('selectedAssetsPagination');
                            if (paginationContainer) {
                                paginationContainer.innerHTML = '';
                            }

                            const infoContainer = document.getElementById('selectedAssetsInfo');
                            if (infoContainer) {
                                infoContainer.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
                            }

                            return;
                        }

                        const perPage = parseInt(document.getElementById('selectedAssetsPerPage').value, 10) || 5;
                        const currentPage = parseInt(selectedAssetsList.getAttribute('data-current-page') || '1', 10);
                        const totalPages = Math.ceil(selectedAssets.length / perPage);
                        const startIndex = (currentPage - 1) * perPage;
                        const endIndex = Math.min(startIndex + perPage, selectedAssets.length);

                        let html = '';
                        for (let i = startIndex; i < endIndex; i++) {
                            const asset = selectedAssets[i];
                            html += `
                                                              <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">${i + 1}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_code || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                                      <div class="flex flex-col">
                                                                          <span class="font-medium">${asset.asset_name || '-'}</span>
                                                                      </div>
                                                                  </td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.description || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.asset_type || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.category_name || '-'}</td>
                                                                  <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                                      <button type="button" class="text-red-500 hover:text-red-700" onclick="removeSelectedAsset(${asset.id})">
                                                                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                          </svg>
                                                                      </button>
                                                                  </td>
                                                              </tr>
                                                          `;
                        }
                        selectedAssetsList.setAttribute('data-current-page', currentPage);
                        selectedAssetsList.innerHTML = html;
                        updateSelectedAssetsPagination(currentPage, totalPages, selectedAssets.length);
                    }

                    function updateSelectedAssetsPagination(currentPage, totalPages, totalItems) {
                        const perPage = parseInt(document.getElementById('selectedAssetsPerPage').value, 10) || 5;
                        const paginationContainer = document.getElementById('selectedAssetsPagination');
                        const infoContainer = document.getElementById('selectedAssetsInfo');

                        if (!paginationContainer || !infoContainer) return;

                        const from = totalItems === 0 ? 0 : (currentPage - 1) * perPage + 1;
                        const to = Math.min(currentPage * perPage, totalItems);

                        infoContainer.textContent = `Menampilkan ${from} sampai ${to} dari ${totalItems} data`;

                        let html = '';

                        if (totalPages <= 1) {
                            paginationContainer.innerHTML = '';
                            return;
                        }

                        html += `
                                                          <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                                             ${currentPage > 1 ? 'data-page="' + (currentPage - 1) + '"' : ''}>
                                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                              </svg>
                                                              Sebelumnya
                                                          </a>
                                                      `;

                        html += '<div class="flex gap-2">';

                        const maxPagesShown = 3;
                        let startPage = Math.max(1, currentPage - 1);
                        let endPage = Math.min(totalPages, startPage + maxPagesShown - 1);

                        if (endPage - startPage + 1 < maxPagesShown) {
                            startPage = Math.max(1, endPage - maxPagesShown + 1);
                        }

                        if (startPage > 1) {
                            html += `
                                                              <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                 data-page="1">1</a>
                                                          `;

                            if (startPage > 2) {
                                html += '<span class="flex items-center justify-center">...</span>';
                            }
                        }

                        for (let i = startPage; i <= endPage; i++) {
                            html += `
                                                              <a href="#" class="h-8 w-8 flex items-center justify-center border ${i === currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]'} rounded"
                                                                 data-page="${i}">${i}</a>
                                                          `;
                        }

                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                html += '<span class="flex items-center justify-center">...</span>';
                            }

                            html += `
                                                              <a href="#" class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded"
                                                                  data-page="${totalPages}">${totalPages}</a>
                                                          `;
                        }

                        html += '</div>';

                        html += `
                                                          <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                                                             ${currentPage < totalPages ? 'data-page="' + (currentPage + 1) + '"' : ''}>
                                                              Selanjutnya
                                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                              </svg>
                                                          </a>
                                                      `;

                        paginationContainer.innerHTML = html;

                        paginationContainer.querySelectorAll('a[data-page]').forEach(link => {
                            link.addEventListener('click', function (e) {
                                e.preventDefault();
                                const page = parseInt(this.getAttribute('data-page'), 10);
                                if (!isNaN(page)) {
                                    changeSelectedAssetsPage(page);
                                }
                            });
                        });
                    }

                    function changeSelectedAssetsPage(page) {
                        const selectedAssetsList = document.getElementById('selectedAssetsList');
                        if (selectedAssetsList) {
                            selectedAssetsList.setAttribute('data-current-page', page);
                            updateSelectedAssetsList();
                        }
                    }

                    // Variables for vendor lazy loading
                    let vendorPage = 1;
                    let isLoadingVendors = false;
                    let hasMoreVendors = true;
                    let currentVendorSearch = '';

                    function fetchVendors(searchTerm = '', callback = null, page = 1, append = false) {
                        const params = new URLSearchParams({
                            json: 'true',
                            limit: '20',
                            page: page.toString()
                        });

                        if (searchTerm) {
                            params.append('search', searchTerm);
                        }

                        if (!append && vendorResults && vendorResults.style.display === 'block') {
                            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Loading vendors...</div>';
                        }

                        isLoadingVendors = true;

                        fetch(`/vendors?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Server responded with status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                let vendors = [];

                                if (Array.isArray(data)) {
                                    vendors = data;
                                } else if (data.vendors && Array.isArray(data.vendors)) {
                                    vendors = data.vendors;
                                } else if (data.data && Array.isArray(data.data)) {
                                    vendors = data.data;
                                }

                                if (!searchTerm && page === 1) {
                                    allVendors = vendors;
                                    try {
                                        localStorage.setItem('allVendors', JSON.stringify(allVendors));
                                    } catch (e) {
                                        console.error('Error caching vendors:', e);
                                    }
                                }

                                if (callback) {
                                    callback(vendors, append);
                                }

                                // Check if we have more pages to load
                                hasMoreVendors = vendors.length === 20; // Assuming 20 is the page size

                                isLoadingVendors = false;
                            })
                            .catch(error => {
                                console.error('Error fetching vendors:', error);

                                if (vendorResults && vendorResults.style.display === 'block' && !append) {
                                    vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';
                                }

                                if (typeof error === 'object' && error !== null) {
                                    showToast(error, 'error');
                                } else {
                                    showToast('Gagal memuat vendor: ' + error.message, 'error');
                                }

                                if (callback) {
                                    callback([]);
                                }

                                isLoadingVendors = false;
                            });
                    }

                    function filterAndDisplayVendors(searchTerm) {
                        if (vendorResults) vendorResults.style.display = 'block';

                        // Reset pagination variables when starting a new search
                        vendorPage = 1;
                        hasMoreVendors = true;
                        currentVendorSearch = searchTerm;

                        if (searchTerm && searchTerm.length > 0) {
                            fetchVendors(searchTerm, displayVendorResults, vendorPage, false);
                        } else {
                            if (allVendors.length > 0) {
                                displayVendorResults(allVendors.slice(0, 20), false);
                            } else {
                                fetchVendors('', vendors => displayVendorResults(vendors, false), vendorPage, false);
                            }
                        }
                    }

                    function displayVendorResults(vendors, append = false) {
                        if (!append) {
                            vendorResults.innerHTML = '';
                        } else {
                            // Remove loading indicator if it exists
                            const loadingIndicator = vendorResults.querySelector('.vendor-loading-indicator');
                            if (loadingIndicator) {
                                loadingIndicator.remove();
                            }
                        }

                        if (vendors.length === 0 && !append) {
                            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Vendor tidak ditemukan</div>';
                            return;
                        }

                        vendors.forEach((vendor, index) => {
                            const div = document.createElement('div');
                            div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                            div.textContent = vendor.vendor_name;
                            div.setAttribute('data-id', vendor.vendor_id);

                            if (!append) {
                                div.style.animationDelay = `${index * 30}ms`;
                            }

                            div.addEventListener('click', function () {
                                vendorIdInput.value = this.getAttribute('data-id');
                                vendorSearchInput.value = this.textContent;
                                vendorResults.style.display = 'none';
                            });

                            vendorResults.appendChild(div);
                        });

                        if (hasMoreVendors) {
                            const loadingDiv = document.createElement('div');
                            loadingDiv.className = 'p-2 text-xs text-gray-500 text-center border-t vendor-loading-indicator';
                            loadingDiv.textContent = 'memuat lebih lanjut...';
                            vendorResults.appendChild(loadingDiv);
                        }
                    }

                    // Setup infinite scrolling for vendor results
                    if (vendorResults) {
                        vendorResults.addEventListener('scroll', function () {
                            if (!hasMoreVendors || isLoadingVendors) return;

                            // Check if user scrolled to bottom
                            if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                                // Load next page
                                vendorPage++;
                                fetchVendors(currentVendorSearch, displayVendorResults, vendorPage, true);
                            }
                        });
                    }

                    vendorSearchInput?.addEventListener('focus', function () {
                        filterAndDisplayVendors(this.value.trim());
                        vendorResults.style.display = 'block';
                    });

                    document.addEventListener('click', function (e) {
                        if (vendorResults && e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                            vendorResults.style.display = 'none';
                        }
                    });

                    vendorSearchInput?.addEventListener('input', debounce(function () {
                        const searchTerm = this.value.trim();
                        filterAndDisplayVendors(searchTerm);
                    }, 300));
                });
            </script>
        @endpush
@endsection