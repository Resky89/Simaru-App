@extends('Layout.app')

@section('title', 'Manajemen Pemeliharaan')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Maintenance Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PEMELIHARAAN</h1>

                        <div class="flex gap-4">
                            <button id="exportBtn" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#213268] rounded-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="text-base">Ekspor PDF</span>
                            </button>

                            <button id="addMaintenanceBtn"
                                class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                        stroke-linecap="round" />
                                    <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                        stroke-linecap="round" />
                                </svg>
                                <span class="text-base">Tambah Pemeliharaan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama aset, interval, atau status..."
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
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Status</option>
                                <option value="">Semua Status</option>
                                <option value="new">Baru</option>
                                <option value="in_progress">Dalam Proses</option>
                                <option value="finished">Selesai</option>
                            </select>
                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Urutan</option>
                                <option value="newest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                            </select>
                        </div>
                    </div>

                    <!-- Maintenance Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Interval</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Mulai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Selesai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Ditugaskan Ke</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Vendor</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenances ?? [] as $maintenance)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $maintenance['asset_name'] ?? '-' }}</span>
                                                <span class="text-gray-500">Kode: {{ $maintenance['asset_code'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $intervalText = '-';
                                                $interval = $maintenance['interval'] ?? '';
                                                if ($interval === 'ONCE') {
                                                    $intervalText = 'Sekali';
                                                } elseif ($interval === 'DAILY') {
                                                    $intervalText = 'Harian';
                                                } elseif ($interval === 'WEEKLY') {
                                                    $intervalText = 'Mingguan';
                                                } elseif ($interval === '2 WEEKS') {
                                                    $intervalText = '2 Minggu';
                                                } elseif ($interval === 'MONTHLY') {
                                                    $intervalText = 'Bulanan';
                                                } elseif ($interval === '2 MONTHS') {
                                                    $intervalText = '2 Bulan';
                                                } elseif ($interval === '3 MONTHS') {
                                                    $intervalText = '3 Bulan';
                                                } elseif ($interval === '4 MONTHS') {
                                                    $intervalText = '4 Bulan';
                                                } elseif ($interval === '6 MONTHS') {
                                                    $intervalText = '6 Bulan';
                                                } elseif ($interval === 'YEARLY') {
                                                    $intervalText = 'Tahunan';
                                                }
                                            @endphp
                                            {{ $intervalText }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ isset($maintenance['start_date']) ? \Carbon\Carbon::parse($maintenance['start_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ isset($maintenance['end_date']) ? \Carbon\Carbon::parse($maintenance['end_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $maintenance['assigned_to'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $maintenance['vendor_name'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $statusClass = '';
                                                $status = $maintenance['status'] ?? '';
                                                $statusText = '-';

                                                if ($status == 'new') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'Baru';
                                                } elseif ($status == 'in_progress') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Dalam Proses';
                                                } elseif ($status == 'finished') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Selesai';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <!-- View Details Icon (Eye) -->
                                                <a href="{{ route('maintenance.detail', ['id' => $maintenance['id']]) }}"
                                                   class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                   title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                <!-- Edit Maintenance Icon (Pencil) -->
                                                @if(!in_array(strtolower($maintenance['status'] ?? ''), ['finished']))
                                                <button class="edit-maintenance-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                    data-id="{{ $maintenance['id'] }}"
                                                    title="Edit Pemeliharaan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                @endif

                                                <!-- Create Report Icon (Document) -->
                                                <button class="create-report-btn p-2 bg-green-100 text-green-500 rounded-md hover:bg-green-200 transition-colors"
                                                    data-id="{{ $maintenance['id'] }}"
                                                    data-asset-name="{{ $maintenance['asset_name'] ?? '' }}"
                                                    data-asset-code="{{ $maintenance['asset_code'] ?? '' }}"
                                                    title="Buat Laporan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Maintenance Icon (Trash) -->
                                                @if(!in_array(strtolower($maintenance['status'] ?? ''), ['finished']))
                                                <button class="delete-maintenance-btn p-2 bg-[#F9D2D2] text-[#8E2121] rounded-md hover:bg-red-200 transition-colors"
                                                    data-id="{{ $maintenance['id'] }}"
                                                    title="Hapus Pemeliharaan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak
                                            ditemukan jadwal pemeliharaan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ isset($pagination['has_prev']) && $pagination['has_prev'] ? request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) : '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($pagination['has_prev']) || !$pagination['has_prev'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $totalPages = $pagination['total_pages'] ?? 1;
                                    $maxPagesShown = 5; // Show max 5 pages at once
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $startPage + $maxPagesShown - 1);

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

                                @if($endPage < $totalPages)
                                    @if($endPage < $totalPages - 1)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $totalPages }}
                                    </a>
                                @endif
                            </div>
                            <a href="{{ isset($pagination['has_next']) && $pagination['has_next'] ? request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) : '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($pagination['has_next']) || !$pagination['has_next'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Berikutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="flex items-center gap-2 mt-4 md:mt-0">
                            <span class="text-sm text-gray-600">
                                @if(isset($pagination) && isset($pagination['total_items']))
                                    Menampilkan {{ ($pagination['current_page'] - 1) * $pagination['limit'] + 1 }}
                                    sampai {{ min($pagination['current_page'] * $pagination['limit'], $pagination['total_items']) }}
                                    dari {{ $pagination['total_items'] }} data
                                @else
                                    Menampilkan 0 sampai 0 dari 0 data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changePerPage(this.value)">
                                <option value="10" {{ (isset($pagination['limit']) && $pagination['limit'] == 10) ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ (isset($pagination['limit']) && $pagination['limit'] == 25) ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ (isset($pagination['limit']) && $pagination['limit'] == 50) ? 'selected' : '' }}>50 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Maintenance Modal -->
        <div id="addMaintenanceModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[850px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addMaintenanceModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Tambah Jadwal Pemeliharaan Baru</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addMaintenanceModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="addMaintenanceForm" class="space-y-6">
                                @csrf
                                <!-- Required fields note -->
                                <div class="text-sm text-gray-600">
                                    Kolom dengan tanda <span class="text-red-500">*</span> wajib diisi
                                </div>

                                <!-- Schedule Dates -->
                                <div class="bg-[#B0DAE5] p-4 rounded-lg space-y-4">
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label class="block text-base font-semibold">
                                                TANGGAL MULAI<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="date" name="start_date" id="start_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label class="block text-base font-semibold">
                                                TANGGAL SELESAI<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="date" name="end_date" id="end_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label class="block text-base font-semibold">
                                                INTERVAL<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <select name="interval" id="interval"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                                <option value="ONCE">Sekali</option>
                                                <option value="DAILY">Harian</option>
                                                <option value="WEEKLY">Mingguan</option>
                                                <option value="2 WEEKS">2 Minggu</option>
                                                <option value="MONTHLY" selected>Bulanan</option>
                                                <option value="2 MONTHS">2 Bulan</option>
                                                <option value="3 MONTHS">3 Bulan</option>
                                                <option value="4 MONTHS">4 Bulan</option>
                                                <option value="6 MONTHS">6 Bulan</option>
                                                <option value="YEARLY">Tahunan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label class="block text-base font-semibold">
                                                DITUGASKAN KE<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <div class="relative">
                                                <input type="text" id="user_search" placeholder="Cari karyawan (nomor karyawan)..."
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200" autocomplete="off">
                                                <input type="hidden" name="assigned_to" id="selected_user_id">
                                                <div id="user_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                                    <!-- Loading indicator -->
                                                    <div id="user_loading" class="flex justify-center py-2">
                                                        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                        </div>
                                                    <ul id="user_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label class="block text-base font-semibold">
                                                VENDOR
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <div class="relative">
                                                <input type="text" id="vendor_search" placeholder="Cari vendor..."
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                <input type="hidden" id="vendor_id" name="vendor_id">
                                                <div id="vendor_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" id="addAssetsBtn"
                                                class="bg-[#4299e1] hover:bg-[#3182ce] text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
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
                                                <th
                                                    class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                    No</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">
                                                    Kode Aset</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Nama
                                                    Aset</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">
                                                    Deskripsi</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Tipe
                                                    Aset</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-left">Nama
                                                    Kategori</th>
                                                <th class="bg-[#25B1FF] text-white p-3 font-bold text-xs text-center">Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedAssetsList">
                                            <tr>
                                                <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak
                                                    ada data tersedia dalam tabel</td>
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

        <!-- Asset Selection Modal -->
        <div id="assetSelectionModal" class="fixed inset-0 z-[60] hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[1200px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="assetSelectionModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Pilih Aset</h2>
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
                            <!-- Search and Filter -->
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <div class="relative flex-grow">
                                    <input type="text" id="assetSearchInput"
                                        placeholder="Search by asset name, code, or serial number..."
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
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe Aset
                                            </th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kategori
                                                Nama</th>
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
                                <div class="flex items-center space-x-2" id="assetModalPaginationControls">
                                    <!-- Pagination will be inserted here -->
                                </div>

                                <div class="flex items-center gap-2 mt-4 md:mt-0">
                                    <span class="text-sm text-gray-600" id="assetModalPaginationInfo">
                                        Menampilkan 0 sampai 0 dari 0 data
                                    </span>
                                    <select id="assetModalPerPageSelect"
                                        class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                        <option value="10">10 per halaman</option>
                                        <option value="25">25 per halaman</option>
                                        <option value="50">50 per halaman</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4 flex justify-end gap-4">
                                    <button type="button"
                                    class="close-modal px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200"
                                        data-modal="assetSelectionModal">
                                        Batal
                                    </button>
                                    <button type="button" id="selectAssetsBtn"
                                    class="px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Pilih
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteMaintenanceModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteMaintenanceModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS PEMELIHARAAN</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteMaintenanceModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="deleteMaintenanceForm">
                            @csrf
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus rekaman pemeliharaan ini? Tindakan ini tidak dapat dibatalkan.</p>
                                        <p id="deleteMaintenanceName" class="text-base font-semibold text-center mt-2"></p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteMaintenanceModal">
                                            Batal
                                        </button>
                                        <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Maintenance Modal -->
        <div id="editMaintenanceModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editMaintenanceModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Ubah Jadwal Pemeliharaan</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editMaintenanceModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editMaintenanceForm" method="POST">
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" id="edit_maintenance_id" name="maintenance_id">

                                <!-- Required fields note -->
                                <div class="text-sm text-gray-600 mb-6">
                                    Kolom yang di tandai <span class="text-red-500">*</span> adalah wajib diisi
                                </div>

                                <!-- Asset Info (Display Only) -->
                                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <span class="block text-base font-semibold text-gray-700">ASSET</span>
                                        </div>
                                        <div class="flex-1">
                                            <span id="edit_asset_name" class="font-medium text-gray-900"></span>
                                            <span id="edit_asset_code" class="text-sm text-gray-500 block"></span>
                                        </div>
                                    </div>
                          </div>

                                <!-- Schedule Information -->
                                <div class="bg-[#B0DAE5] p-4 rounded-lg space-y-4 mb-6">
                                    <!-- Start Date -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="edit_start_date" class="block text-base font-semibold">
                                                TANGGAL MULAI<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="date" id="edit_start_date" name="start_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="edit_end_date" class="block text-base font-semibold">
                                                TANGGAL SELESAI<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="date" id="edit_end_date" name="end_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Interval -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="edit_interval" class="block text-base font-semibold">
                                                INTERVAL<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <select id="edit_interval" name="interval"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                                <option value="ONCE">Sekali</option>
                                                <option value="DAILY">Harian</option>
                                                <option value="WEEKLY">Mingguan</option>
                                                <option value="2 WEEKS">2 Minggu</option>
                                                <option value="MONTHLY">Bulanan</option>
                                                <option value="2 MONTHS">2 Bulan</option>
                                                <option value="3 MONTHS">3 Bulan</option>
                                                <option value="4 MONTHS">4 Bulan</option>
                                                <option value="6 MONTHS">6 Bulan</option>
                                                <option value="YEARLY">Tahunan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Assigned To -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="edit_assigned_to" class="block text-base font-semibold">
                                                DITUGASKAN KE<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <select id="edit_assigned_to" name="assigned_to"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                                <option value="" disabled selected>Pilih Karyawan</option>
                                                @foreach($users ?? [] as $user)
                                                    <option value="{{ $user['user_id'] }}">
                                                        {{ $user['employee_number'] ?? '' }} {{ !empty($user['employee_number']) && !empty($user['name']) ? '-' : '' }} {{ $user['name'] ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Vendor -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="edit_vendor_search" class="block text-base font-semibold">
                                                VENDOR
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <div class="relative">
                                                <input type="text" id="edit_vendor_search" placeholder="Cari vendor..."
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                <input type="hidden" id="edit_vendor_id" name="vendor_id">
                                                <div id="edit_vendor_results" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4 flex justify-end gap-4">
                                    <button type="submit"
                                        class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Maintenance Report Modal -->
        <div id="createReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="createReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Buat Laporan Pemeliharaan</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="createReportModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="createReportForm" method="POST" enctype="multipart/form-data" action="{{ route('maintenance.reports.create') }}">
                                @csrf
                                <input type="hidden" id="report_maintenance_id" name="maintenance_id">

                                <!-- Required fields note z-->
                                <div class="text-sm text-gray-600 mb-6">
                                    Kolom yang di tandai <span class="text-red-500">*</span> adalah wajib diisi
                                </div>

                                <!-- Asset Info (Display Only) -->
                                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <span class="block text-base font-semibold text-gray-700">ASSET</span>
                                        </div>
                                        <div class="flex-1">
                                            <span id="report_asset_name" class="font-medium text-gray-900"></span>
                                            <span id="report_asset_code" class="text-sm text-gray-500 block"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Report Information -->
                                <div class="bg-[#B0DAE5] p-4 rounded-lg space-y-4 mb-6">
                                    <!-- Maintenance Date -->
                                    <div class="flex items-center gap-4">
                                        <div class="min-w-[150px]">
                                            <label for="maintenance_date" class="block text-base font-semibold">
                                                TANGGAL LAPORAN<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="date" id="maintenance_date" name="maintenance_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="flex items-start gap-4">
                                        <div class="min-w-[150px] pt-2">
                                            <label for="description" class="block text-base font-semibold">
                                                DESKRIPSI<span class="text-red-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <textarea id="description" name="description" rows="4"
                                                class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                                required placeholder="Masukkan detail laporan pemeliharaan..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Image Attachment -->
                                    <div class="flex items-start gap-4">
                                        <div class="min-w-[150px] pt-2">
                                            <label for="attachment" class="block text-base font-semibold">
                                                LAMPIRAN
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <div class="border-2 border-dashed border-[#213268] rounded-lg p-4 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                <!-- Image preview -->
                                                <div id="image-preview" class="mt-2 mb-4 w-full hidden">
                                                    <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                        <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Selected Image">
                                                        <button type="button" id="remove-image" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-1 text-sm text-gray-600">Seret gambar Anda atau <span class="text-[#213268] font-semibold">Cari file</span></p>
                                                    <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png</p>
                                                    <p class="mt-1 text-xs text-[#213268] font-medium">Klik di area ini untuk memilih file</p>
                                                </div>
                                                <input type="file" id="attachment" name="file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4 flex justify-end gap-4">
                                    <button type="submit"
                                        class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Kirim Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store user data in a global variable
        window.usersData = @json($users ?? []);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Prevent selecting past dates for date inputs
            const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD

            // Set min attribute for date inputs in add maintenance modal
            const startDateInput = document.getElementById('start_date');
            if (startDateInput) {
                startDateInput.setAttribute('min', today);
            }

            const endDateInput = document.getElementById('end_date');
            if (endDateInput) {
                endDateInput.setAttribute('min', today);
            }

            // Add event listener to ensure end date is not before start date
            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    // When start date changes, set it as minimum for end date
                    endDateInput.setAttribute('min', this.value);

                    // If end date is now less than start date, update it
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }

                    // If interval is DAILY or ONCE, also update end date to match start date
                    const currentInterval = document.getElementById('interval').value;
                    if (currentInterval === 'DAILY' || currentInterval === 'ONCE') {
                        endDateInput.value = this.value;
                    }
                });
            }

            // Set similar behavior for edit modal
            const editStartDateInput = document.getElementById('edit_start_date');
            const editEndDateInput = document.getElementById('edit_end_date');
            if (editStartDateInput && editEndDateInput) {
                editStartDateInput.addEventListener('change', function() {
                    // When start date changes, set it as minimum for end date
                    editEndDateInput.setAttribute('min', this.value);

                    // If end date is now less than start date, update it
                    if (editEndDateInput.value && editEndDateInput.value < this.value) {
                        editEndDateInput.value = this.value;
                    }

                    // If interval is DAILY or ONCE, also update end date to match start date
                    const currentEditInterval = document.getElementById('edit_interval').value;
                    if (currentEditInterval === 'DAILY' || currentEditInterval === 'ONCE') {
                        editEndDateInput.value = this.value;
                    }
                });
            }

            // Function to toggle end date field visibility based on interval
            function toggleEndDateVisibility(intervalValue, formType = 'add') {
                const endDateField = formType === 'add'
                    ? document.getElementById('end_date').closest('.flex.items-center.gap-4')
                    : document.getElementById('edit_end_date').closest('.flex.items-center.gap-4');
                const endDateInput = formType === 'add'
                    ? document.getElementById('end_date')
                    : document.getElementById('edit_end_date');

                                if (intervalValue === 'DAILY' || intervalValue === 'ONCE') {
                    // Hide end date field for daily and once intervals
                    endDateField.style.display = 'none';
                    // Remove required attribute when hidden
                    endDateInput.removeAttribute('required');

                    // Set end date equal to start date for data consistency
                    const startDateValue = formType === 'add'
                        ? document.getElementById('start_date').value
                        : document.getElementById('edit_start_date').value;
                    endDateInput.value = startDateValue;
                } else {
                    // Show end date field for other intervals
                    endDateField.style.display = 'flex';
                    // Add required attribute when visible
                    endDateInput.setAttribute('required', 'required');
                }
            }

            // Add interval change event listener for Add Maintenance modal
            const intervalSelect = document.getElementById('interval');
            if (intervalSelect) {
                intervalSelect.addEventListener('change', function() {
                    toggleEndDateVisibility(this.value, 'add');
                });

                // Set initial state
                if (intervalSelect.value === 'DAILY') {
                    toggleEndDateVisibility('DAILY', 'add');
                }
            }

            // Add interval change event listener for Edit Maintenance modal
            const editIntervalSelect = document.getElementById('edit_interval');
            if (editIntervalSelect) {
                editIntervalSelect.addEventListener('change', function() {
                    toggleEndDateVisibility(this.value, 'edit');
                });
            }

            // Debounce utility function to limit how often a function can be called
            function debounce(func, wait, immediate) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    const later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    const callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            }

            // Utility function to parse error responses from the server
            // This ensures error arrays are properly passed to the catch block
            function handleApiResponse(response) {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        if (!response.ok) {
                            console.error('Server error response:', data);
                            // Preserve the full error data structure
                            data.status = response.status;
                            return Promise.reject(data);
                        }
                        return data;
                    });
                }
                if (!response.ok) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
                return Promise.resolve({ success: true });
            }

            // Show toast notification function
            window.showToast = function(message, type = 'success') {
                // Remove existing notifications with the same type
                const existingNotification = document.getElementById(type === 'success' ? 'successNotification' : 'errorNotification');
                if (existingNotification) {
                    existingNotification.remove();
                }

                // Create the notification element
                const notification = document.createElement('div');
                notification.id = type === 'success' ? 'successNotification' : 'errorNotification';
                notification.className = `fixed top-4 right-4 bg-${type === 'success' ? 'green' : 'red'}-100 border-l-4 border-${type === 'success' ? 'green' : 'red'}-500 text-${type === 'success' ? 'green' : 'red'}-700 p-4 rounded shadow-md z-50`;
                notification.setAttribute('role', 'alert');

                // Check if message is an object or array (for detailed error messages)
                let messageContent = '';
                if (typeof message === 'object' && message !== null) {
                    // If it's an error object with nested errors
                    if (message.errors && typeof message.errors === 'object') {
                        messageContent = '<ul class="list-disc pl-5 mt-2">';
                        for (const field in message.errors) {
                            if (Array.isArray(message.errors[field])) {
                                message.errors[field].forEach(error => {
                                    messageContent += `<li>${error}</li>`;
                                });
                            } else if (typeof message.errors[field] === 'object') {
                                // Handle nested objects
                                for (const subField in message.errors[field]) {
                                    messageContent += `<li>${subField}: ${message.errors[field][subField]}</li>`;
                                }
                            } else {
                                messageContent += `<li>${field}: ${message.errors[field]}</li>`;
                            }
                        }
                        messageContent += '</ul>';
                    } else if (Array.isArray(message)) {
                        // If it's an array of error messages
                        messageContent = '<ul class="list-disc pl-5 mt-2">';

                        // First process and display general errors at the top
                        const generalErrors = message.filter(error =>
                            typeof error === 'object' && error !== null &&
                            error.path === 'general' && error.message
                        );

                        // Then process field-specific errors
                        const fieldErrors = message.filter(error =>
                            typeof error === 'object' && error !== null &&
                            error.path && error.path !== 'general' && error.message
                        );

                        // Handle string errors or other formats
                        const otherErrors = message.filter(error =>
                            !(typeof error === 'object' && error !== null && error.path && error.message)
                        );

                        // Display general errors first with stronger styling
                        generalErrors.forEach(error => {
                            messageContent += `<li class="font-medium text-red-800 mb-2">${error.message}</li>`;
                        });

                        // Display field errors with translated field names
                        fieldErrors.forEach(error => {
                            // Convert field names to readable format
                            let readableField = error.path;
                            if (error.path === 'start_date') readableField = 'Tanggal Mulai';
                            else if (error.path === 'end_date') readableField = 'Tanggal Selesai';
                            else if (error.path === 'interval') readableField = 'Interval';
                            else if (error.path === 'assigned_to') readableField = 'Ditugaskan Kepada';
                            else if (error.path === 'asset_ids') readableField = 'Aset';
                            else if (error.path === 'maintenance_date') readableField = 'Tanggal Laporan';
                            else if (error.path === 'description') readableField = 'Deskripsi';
                            else if (error.path === 'vendor_id') readableField = 'Vendor';
                            else if (error.path === 'file') readableField = 'File Lampiran';
                            else if (error.path === 'status') readableField = 'Status';

                            messageContent += `<li><strong>${readableField}:</strong> ${error.message}</li>`;
                        });

                        // Display other error formats
                        otherErrors.forEach(error => {
                            if (typeof error === 'string') {
                                messageContent += `<li>${error}</li>`;
                            } else {
                                // Generic object representation
                                messageContent += `<li>${JSON.stringify(error)}</li>`;
                            }
                        });

                        messageContent += '</ul>';
                    } else if (message.message) {
                        // If it has a message property (common in Error objects)
                        messageContent = message.message;
                    } else if (message.error) {
                        // If it has an error property
                        messageContent = message.error;
                    } else {
                        // Try to prettify the object for better readability
                        try {
                            // Create a formatted message showing each property
                            messageContent = '<ul class="list-disc pl-5 mt-2">';
                            Object.entries(message).forEach(([key, value]) => {
                                if (key !== 'stack' && key !== '__proto__') { // Skip non-helpful properties
                                    if (typeof value === 'object' && value !== null) {
                                        messageContent += `<li>${key}: ${JSON.stringify(value)}</li>`;
                                    } else {
                                        messageContent += `<li>${key}: ${value}</li>`;
                                    }
                                }
                            });
                            messageContent += '</ul>';

                            // If there were no properties to show, fallback to stringify
                            if (messageContent === '<ul class="list-disc pl-5 mt-2"></ul>') {
                                messageContent = JSON.stringify(message);
                            }
                        } catch (e) {
                            messageContent = "Error object could not be displayed";
                        }
                    }
                } else {
                    // Simple string message
                    messageContent = message;
                }

                // Set inner HTML
                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-${type === 'success' ? 'green' : 'red'}-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="${type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'}" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">${type === 'success' ? 'Berhasil!' : 'Gagal!'}</p>
                            <div class="error-message">${messageContent}</div>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;

                // Add to document
                document.body.appendChild(notification);

                // Auto-hide after 5 seconds
                setTimeout(function() {
                    if (document.getElementById(notification.id)) {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(function() {
                            if (document.getElementById(notification.id)) {
                                notification.remove();
                            }
                        }, 500);
                    }
                }, 5000);

                return notification;
            };

            // Show flash messages with the showToast function
            @if(session('success'))
            showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
            showToast("{{ session('error') }}", 'error');
            @endif

            // Function to change items per page
            window.changePerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', limit);
                window.location.href = url.toString();
            }

            // Status filter - apply immediately on change
            const statusFilterSelect = document.getElementById('statusFilter');
            if (statusFilterSelect) {
                statusFilterSelect.addEventListener('change', function() {
                    applyFilters();
                });
            }

            // Sort order - apply immediately on change
            const sortOrderSelect = document.getElementById('sortOrder');
            if (sortOrderSelect) {
                sortOrderSelect.addEventListener('change', function() {
                    applyFilters();
                });
            }

            // Function to apply all filters and sorting
            function applyFilters() {
                const searchTerm = document.getElementById('searchInput').value;
                const statusFilter = document.getElementById('statusFilter').value;
                const sortOrder = document.getElementById('sortOrder').value;

                // For AJAX-based filtering
                if (document.getElementById('ajaxFilterButton')) {
                    // If we have an AJAX filter button, use it to filter without page reload
                    fetchMaintenanceData(1, searchTerm, statusFilter, sortOrder);
                    return;
                }

                // Fall back to traditional page reload for the main list
                const url = new URL(window.location.href);

                // Set search parameter
                if (searchTerm) url.searchParams.set('search', searchTerm);
                else url.searchParams.delete('search');

                // Set status parameter
                if (statusFilter) url.searchParams.set('status', statusFilter);
                else url.searchParams.delete('status');

                // Set sort parameter based on selected option
                if (sortOrder) {
                    // Map front-end sort values to backend expected values
                    let sortBy, sortDirection;

                    switch(sortOrder) {
                        case 'newest':
                            sortBy = 'created_at';
                            sortDirection = 'desc';
                            break;
                        case 'oldest':
                            sortBy = 'created_at';
                            sortDirection = 'asc';
                            break;
                        default:
                            sortBy = 'created_at';
                            sortDirection = 'desc';
                    }

                    url.searchParams.set('sort_by', sortBy);
                    url.searchParams.set('sort_order', sortDirection);

                    // Keep the frontend sort value for the select element
                    url.searchParams.set('sort', sortOrder);
                } else {
                    url.searchParams.delete('sort_by');
                    url.searchParams.delete('sort_order');
                    url.searchParams.delete('sort');
                }

                // Reset to first page on filter change
                url.searchParams.set('page', 1);

                // Redirect to new URL with filters
                window.location.href = url.toString();
            }

            // Function to fetch maintenance data with AJAX (for future use)
            function fetchMaintenanceData(page = 1, search = '', status = '', sort = '') {
                // This is a placeholder for future AJAX implementation
                // You would implement this to fetch data without page reload
                console.log('AJAX fetch maintenance data:', {page, search, status, sort});

                // Show loading state
                const tableBody = document.querySelector('table tbody');
                if (tableBody) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="10" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                <div class="flex justify-center">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#213268]"></div>
                                </div>
                                <div class="mt-2">Memuat data...</div>
                            </td>
                        </tr>
                    `;
                }
            }

            // Search input - apply filters on debounce
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                // Fill the search input with the value from URL if it exists
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('search')) {
                    searchInput.value = urlParams.get('search');
                }

                // Add debounced event listener for input
                searchInput.addEventListener('input', debounce(function() {
                    applyFilters();
                }, 500));

                // Also handle Enter key press
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyFilters();
                    }
                });
            }

            // Set existing values from URL for filters
            const urlParams = new URLSearchParams(window.location.search);

            // Set sort value
            const sortSelect = document.getElementById('sortOrder');
            if (sortSelect) {
                // Remove disabled and selected from all options first
                Array.from(sortSelect.options).forEach(option => {
                    option.removeAttribute('selected');
                });

                // Get the sort value from URL
                if (urlParams.has('sort') && urlParams.get('sort')) {
                    sortSelect.value = urlParams.get('sort');

                    // If no matching option found, set to first non-placeholder option
                    if (sortSelect.selectedIndex === -1) {
                        sortSelect.selectedIndex = 1; // Index 1 is "Newest First"
                    }
                } else {
                    // If there's no sort value but there is sort_by/sort_order, try to map back
                    const sortBy = urlParams.get('sort_by');
                    const sortOrder = urlParams.get('sort_order');

                    if (sortBy && sortOrder) {
                        if (sortBy === 'created_at' && sortOrder === 'desc') {
                            sortSelect.value = 'newest';
                        } else if (sortBy === 'created_at' && sortOrder === 'asc') {
                            sortSelect.value = 'oldest';
                        }
                    } else {
                        // Default to "Newest First" if no sort specified
                        sortSelect.selectedIndex = 1;
                    }
                }
            }

            // Set status filter value
            const statusSelect = document.getElementById('statusFilter');
            if (statusSelect) {
                // Remove selected from all options first
                Array.from(statusSelect.options).forEach(option => {
                    option.removeAttribute('selected');
                });

                if (urlParams.has('status') && urlParams.get('status')) {
                    statusSelect.value = urlParams.get('status');

                    // If no matching option found, set to first non-placeholder option
                    if (statusSelect.selectedIndex === -1) {
                        statusSelect.selectedIndex = 1; // Index 1 is "All Status"
                    }
                } else {
                    // Default to "All Status" if no status specified
                    statusSelect.selectedIndex = 1;
                }
            }

            // Modal handling
            const modals = {
                add: document.getElementById('addMaintenanceModal'),
                assetSelection: document.getElementById('assetSelectionModal'),
                delete: document.getElementById('deleteMaintenanceModal'),
                edit: document.getElementById('editMaintenanceModal'),
                report: document.getElementById('createReportModal')
            };
            const modalContents = {
                add: document.getElementById('addMaintenanceModalContent'),
                assetSelection: document.getElementById('assetSelectionModalContent'),
                delete: document.getElementById('deleteMaintenanceModalContent'),
                edit: document.getElementById('editMaintenanceModalContent'),
                report: document.getElementById('createReportModalContent')
            };

            // Function to open modal
            function openModal(modal, modalContent) {
                if (modal && modalContent) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modalContent.classList.add('opacity-100', 'scale-100');
                        modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4', 'sm:translate-y-0');
                    }, 10);
                    document.body.classList.add('overflow-hidden');
                }
            }

            // Function to close modal
            function closeModal(modal, modalContent) {
                if (modal && modalContent) {
                    modalContent.classList.remove('opacity-100', 'scale-100');
                    modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4', 'sm:translate-y-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');

                        // Reset asset search input when asset selection modal is closed
                        if (modal.id === 'assetSelectionModal') {
                            const assetSearchInput = document.getElementById('assetSearchInput');
                            if (assetSearchInput) {
                                assetSearchInput.value = '';
                            }
                        }
                    }, 300);
                }
            }

            // Close modal buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    const modalContent = document.getElementById(modalId + 'Content');
                    closeModal(modal, modalContent);
                });
            });

            // Add maintenance click handler
            document.getElementById('addMaintenanceBtn')?.addEventListener('click', function() {
                openModal(modals.add, modalContents.add);
            });

            // Add Assets button click handler - opens the asset selection modal
            document.getElementById('addAssetsBtn')?.addEventListener('click', function() {
                openModal(modals.assetSelection, modalContents.assetSelection);
                loadAssets(1); // Load the first page of assets
            });

            // Handle asset search with debounce
            document.getElementById('assetSearchInput')?.addEventListener('input', debounce(function() {
                loadAssets(1);
            }, 500));

            // Handle asset per page change
            document.getElementById('assetModalPerPageSelect')?.addEventListener('change', function() {
                loadAssets(1);
            });

            // Select button click handler
            document.getElementById('selectAssetsBtn')?.addEventListener('click', function() {
                updateSelectedAssetsList();
                closeModal(modals.assetSelection, modalContents.assetSelection);
            });

            // Handle selected assets per page change
            document.getElementById('selectedAssetsPerPage')?.addEventListener('change', function() {
                // Reset to page 1 when changing items per page
                const selectedAssetsList = document.getElementById('selectedAssetsList');
                if (selectedAssetsList) {
                    selectedAssetsList.setAttribute('data-current-page', '1');
                    updateSelectedAssetsList();
                }
            });

            // Asset selection handling
            let selectedAssets = [];

            // Function to validate asset before adding to selection
            function validateAsset(asset) {
                // Check if asset has id
                if (!asset.id) {
                    console.error('Asset is missing ID:', asset);
                    return false;
                }

                // Make sure ID is valid
                const id = parseInt(asset.id, 10);
                if (isNaN(id)) {
                    console.error('Asset has invalid ID:', asset.id);
                    return false;
                }

                return true;
            }

            // Load assets for selection
            function loadAssets(page = 1) {
                const searchTerm = document.getElementById('assetSearchInput').value;
                const limit = document.getElementById('assetModalPerPageSelect').value;

                // Show loading state
                document.getElementById('assetSelectionList').innerHTML = `
                    <tr>
                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Memuat aset...</td>
                    </tr>
                `;

                // Fetch assets from API
                fetch(`/assets/data?page=${page}&limit=${limit}&search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Jaringan tidak berfungsi');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('API response for assets:', data); // Log the complete API response for debugging

                        const assets = data.assets || [];
                        if (assets.length === 0) {
                            document.getElementById('assetSelectionList').innerHTML = `
                                <tr>
                                    <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada aset    </td>
                                </tr>
                            `;
                            return;
                        }

                        // Render assets
                        let html = '';
                        assets.forEach(asset => {
                            const isSelected = selectedAssets.some(selectedAsset => selectedAsset.id === asset.asset_id);

                            // Get asset name - check both direct property and nested structure
                            const assetName = asset.asset_master_name ||
                                           (asset.asset_master && asset.asset_master.asset_name) ||
                                           '-';

                            // Get asset code
                            const assetCode = asset.asset_code || '-';

                            // Get asset type based on asset_master_code pattern
                            let assetType = 'Non Medical';
                            if (asset.asset_master && asset.asset_master.asset_master_code) {
                                const code = asset.asset_master.asset_master_code;
                                if (code.startsWith('MED-')) {
                                    assetType = 'Medical';
                                }
                            }

                            // Get category name from asset_master if it exists
                            const categoryName = asset.asset_master && asset.asset_master.subcategory_name ?
                                                asset.asset_master.subcategory_name : '-';

                            // Get description
                            const description = asset.description || '-';

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

                        // Setup pagination with proper data
                        const paginationData = data.assets_pagination || data.pagination || {};
                        console.log('Pagination data:', paginationData); // Log pagination data for debugging

                        setupAssetPagination(paginationData);

                        // Attach checkbox event handlers
                        attachCheckboxHandlers();
                    })
                    .catch(error => {
                        console.error('Error loading assets:', error);
                        document.getElementById('assetSelectionList').innerHTML = `
                            <tr>
                                <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">Gagal memuat aset</td>
                            </tr>
                        `;
                    });
            }

            // Function to handle checkbox events for asset selection
            function attachCheckboxHandlers() {
                const checkboxes = document.querySelectorAll('.asset-checkbox');

                // First remove any existing event listeners by cloning and replacing
                checkboxes.forEach(checkbox => {
                    const newCheckbox = checkbox.cloneNode(true);
                    checkbox.parentNode.replaceChild(newCheckbox, checkbox);
                });

                // Now add fresh event listeners
                document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const assetId = this.getAttribute('data-id');
                        if (this.checked) {
                            // Create asset object
                            const asset = {
                                id: assetId,
                                code: this.getAttribute('data-code'),
                                name: this.getAttribute('data-name'),
                                description: this.getAttribute('data-description'),
                                type: this.getAttribute('data-type'),
                                category: this.getAttribute('data-category')
                            };

                            // Validate and add to selected assets if not already there
                            if (validateAsset(asset) && !selectedAssets.some(a => a.id === assetId)) {
                                selectedAssets.push(asset);
                            }
                        } else {
                            // Remove from selected assets
                            selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);
                        }
                    });
                });

                // Setup Select All checkbox
                const selectAllAssets = document.getElementById('selectAllAssets');
                if (selectAllAssets) {
                    // Remove existing event listeners
                    const newSelectAll = selectAllAssets.cloneNode(true);
                    selectAllAssets.parentNode.replaceChild(newSelectAll, selectAllAssets);

                    // Add fresh event listener
                    document.getElementById('selectAllAssets').addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.asset-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        });
                    });
                }
            }

            // Function to setup asset pagination
            function setupAssetPagination(pagination) {
                if (!pagination) return;

                const paginationInfo = document.getElementById('assetModalPaginationInfo');
                const paginationControls = document.getElementById('assetModalPaginationControls');

                // Handle different pagination data structures (from maintenance vs calibration API)
                const currentPage = pagination.current_page || 1;
                const totalPages = pagination.total_pages || pagination.last_page || 1;
                const totalItems = pagination.total_items || pagination.total || 0;
                const limit = pagination.limit || pagination.per_page || 10;
                const from = pagination.from || ((currentPage - 1) * limit + 1);
                const to = pagination.to || Math.min(currentPage * limit, totalItems);

                // Update pagination info
                if (paginationInfo) {
                    paginationInfo.textContent = `Menampilkan ${from} sampai ${to} dari ${totalItems} data`;
                }

                // Generate pagination controls
                let controlsHtml = '';

                // Previous button
                controlsHtml += `
                    <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                       ${currentPage > 1 ? 'data-page="' + (currentPage - 1) + '"' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Prev
                    </a>
                `;

                // Only show pagination if there are items
                if (totalItems > 0) {
                    // Page numbers
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

                // Next button
                controlsHtml += `
                    <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                       ${currentPage < totalPages ? 'data-page="' + (currentPage + 1) + '"' : ''}>
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                `;

                if (paginationControls) {
                    paginationControls.innerHTML = controlsHtml;

                    // Add event listeners to pagination links
                    paginationControls.querySelectorAll('a[data-page]').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const page = parseInt(this.getAttribute('data-page'), 10);
                            if (!isNaN(page)) {
                                loadAssets(page);
                            }
                        });
                    });
                }
            }

            // Function to update the selected assets list in the add maintenance form
            function updateSelectedAssetsList() {
                const selectedAssetsList = document.getElementById('selectedAssetsList');

                if (selectedAssets.length > 0) {
                    // Get current page and per page settings
                    const perPage = parseInt(document.getElementById('selectedAssetsPerPage').value, 10) || 5;
                    const currentPage = parseInt(selectedAssetsList.getAttribute('data-current-page') || '1', 10);
                    const totalPages = Math.ceil(selectedAssets.length / perPage);

                    // Calculate indices for current page
                    const startIndex = (currentPage - 1) * perPage;
                    const endIndex = Math.min(startIndex + perPage, selectedAssets.length);

                    // Generate table rows for current page
                    let html = '';
                    for (let i = startIndex; i < endIndex; i++) {
                        const asset = selectedAssets[i];
                        html += `
                        <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">${i + 1}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.code || '-'}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.name || '-'}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.description || '-'}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.type || '-'}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4]">${asset.category || '-'}</td>
                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                <input type="hidden" name="asset_ids[]" value="${asset.id}">
                                <button type="button" class="remove-asset text-red-500 hover:text-red-700" data-id="${asset.id}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>`;
                    }

                    // Store current page in the table element
                    selectedAssetsList.setAttribute('data-current-page', currentPage);
                    selectedAssetsList.innerHTML = html;

                    // Add hidden inputs for all assets (so form submission includes all assets)
                    let hiddenInputsHtml = '';
                    selectedAssets.forEach(asset => {
                        if (!html.includes(`name="asset_ids[]" value="${asset.id}"`)) {
                            hiddenInputsHtml += `<input type="hidden" name="asset_ids[]" value="${asset.id}">`;
                        }
                    });

                    // Append hidden inputs after the table
                    const hiddenInputsContainer = document.getElementById('hiddenAssetInputs') || document.createElement('div');
                    hiddenInputsContainer.id = 'hiddenAssetInputs';
                    hiddenInputsContainer.innerHTML = hiddenInputsHtml;
                    hiddenInputsContainer.style.display = 'none';

                    if (!document.getElementById('hiddenAssetInputs')) {
                        selectedAssetsList.parentNode.appendChild(hiddenInputsContainer);
                    }

                    // Update pagination controls
                    updateSelectedAssetsPagination(currentPage, totalPages, selectedAssets.length);

                    // Add event listeners to remove buttons
                    document.querySelectorAll('.remove-asset').forEach(button => {
                        button.addEventListener('click', function() {
                            const assetId = this.getAttribute('data-id');
                            selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);

                            // If removing an item from the last page and that page would be empty,
                            // go to the previous page
                            const newTotalPages = Math.ceil(selectedAssets.length / perPage);
                            if (currentPage > newTotalPages && newTotalPages > 0) {
                                selectedAssetsList.setAttribute('data-current-page', newTotalPages);
                            }

                            updateSelectedAssetsList();
                        });
                    });
                } else {
                    selectedAssetsList.innerHTML = '<tr><td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada data yang tersedia dalam tabel</td></tr>';

                    // Reset pagination
                    const paginationContainer = document.getElementById('selectedAssetsPagination');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = '';
                    }

                    const infoContainer = document.getElementById('selectedAssetsInfo');
                    if (infoContainer) {
                        infoContainer.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
                    }

                    // Clear hidden inputs
                    const hiddenInputsContainer = document.getElementById('hiddenAssetInputs');
                    if (hiddenInputsContainer) {
                        hiddenInputsContainer.innerHTML = '';
                    }
                }
            }

            // Function to update pagination for selected assets
            function updateSelectedAssetsPagination(currentPage, totalPages, totalItems) {
                const perPage = parseInt(document.getElementById('selectedAssetsPerPage').value, 10) || 5;
                const paginationContainer = document.getElementById('selectedAssetsPagination');
                const infoContainer = document.getElementById('selectedAssetsInfo');

                if (!paginationContainer || !infoContainer) return;

                // Calculate from and to numbers
                const from = totalItems === 0 ? 0 : (currentPage - 1) * perPage + 1;
                const to = Math.min(currentPage * perPage, totalItems);

                // Update info text
                infoContainer.textContent = `Menampilkan ${from} sampai ${to} dari ${totalItems} data`;

                // Generate pagination controls
                let html = '';

                // Only show pagination if there are multiple pages
                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                // Previous button
                html += `
                    <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                       ${currentPage > 1 ? 'data-page="' + (currentPage - 1) + '"' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Prev
                    </a>
                `;

                // Page numbers
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

                // Next button
                html += `
                    <a href="#" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm ${currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                       ${currentPage < totalPages ? 'data-page="' + (currentPage + 1) + '"' : ''}>
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                `;

                paginationContainer.innerHTML = html;

                // Add event listeners to pagination links
                paginationContainer.querySelectorAll('a[data-page]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.getAttribute('data-page'), 10);
                        if (!isNaN(page)) {
                            changeSelectedAssetsPage(page);
                        }
                    });
                });
            }

            // Function to change page for selected assets
            function changeSelectedAssetsPage(page) {
                const selectedAssetsList = document.getElementById('selectedAssetsList');
                if (selectedAssetsList) {
                    selectedAssetsList.setAttribute('data-current-page', page);
                    updateSelectedAssetsList();
                }
            }

            // Vendor search functionality with debounce
            let allVendors = []; // Store all vendors for client-side filtering
            const vendorSearchInput = document.getElementById('vendor_search');
            const vendorIdInput = document.getElementById('vendor_id');
            const vendorResults = document.getElementById('vendor_results');

            // Edit modal vendor search
            const editVendorSearchInput = document.getElementById('edit_vendor_search');
            const editVendorIdInput = document.getElementById('edit_vendor_id');
            const editVendorResults = document.getElementById('edit_vendor_results');

            // Initial load of vendors
            loadAllVendors();

            // Show/hide vendor results
            vendorSearchInput?.addEventListener('focus', function() {
                filterAndDisplayVendors(this.value.trim(), 'add');
                vendorResults.style.display = 'block';
            });

            // Show/hide edit vendor results
            editVendorSearchInput?.addEventListener('focus', function() {
                filterAndDisplayVendors(this.value.trim(), 'edit');
                editVendorResults.style.display = 'block';
            });

            // Hide vendor results when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                    vendorResults.style.display = 'none';
                }
                if (e.target !== editVendorSearchInput && !editVendorResults.contains(e.target)) {
                    editVendorResults.style.display = 'none';
                }
            });

            // Search vendors with debounce
            vendorSearchInput?.addEventListener('input', debounce(function() {
                const searchTerm = this.value.trim();
                filterAndDisplayVendors(searchTerm, 'add');
            }, 300));

            // Search vendors in edit modal with debounce
            editVendorSearchInput?.addEventListener('input', debounce(function() {
                const searchTerm = this.value.trim();
                filterAndDisplayVendors(searchTerm, 'edit');
            }, 300));

            // Load all vendors
            function loadAllVendors() {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                vendorResults.style.display = 'block';

                // First try to get from localStorage to avoid delay
                const cachedVendors = localStorage.getItem('allVendors');
                if (cachedVendors) {
                    try {
                        allVendors = JSON.parse(cachedVendors);

                        // Still load fresh data in the background
                        fetchAllVendors();

                        return; // Exit early with cached data
                    } catch (e) {
                        console.error('Error parsing cached vendors:', e);
                    }
                }

                // If no cache, fetch from API
                fetchAllVendors();
            }

            // Fetch all vendors with pagination
            function fetchAllVendors() {
                let page = 1;
                allVendors = []; // Reset array

                function fetchPage(page) {
                    if (page === 1) {
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    } else {
                        // Update loading message for subsequent pages
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor (halaman ' + page + ')...</div>';
                    }

                    fetch(`/vendor?json=true&page=${page}&limit=1000`, {
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
                        let pagination = null;

                        // Handle different response formats
                        if (Array.isArray(data)) {
                            vendors = data;
                        } else if (data.vendors && Array.isArray(data.vendors)) {
                            vendors = data.vendors;
                            pagination = data.pagination;
                        } else if (data.data && Array.isArray(data.data)) {
                            vendors = data.data;
                            pagination = data.pagination;
                        }

                        // Add to our collection
                        allVendors = [...allVendors, ...vendors];

                        // Check if there are more pages
                        const hasNextPage = pagination && pagination.has_next;

                        if (hasNextPage) {
                            // Fetch next page
                            fetchPage(page + 1);
                        } else {
                            // Cache for future use
                            try {
                                localStorage.setItem('allVendors', JSON.stringify(allVendors));
                            } catch (e) {
                                console.error('Error caching vendors:', e);
                            }

                            // If the input has a value, filter and display
                            if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                filterAndDisplayVendors(vendorSearchInput.value.trim());
                            } else {
                                vendorResults.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error(`Error fetching vendors page ${page}:`, error);
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';

                        // If we got some vendors, still show them
                        if (allVendors.length > 0) {
                            filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');
                        }
                    });
                }

                // Start fetching from page 1
                fetchPage(page);
            }

            // Filter and display vendors based on search term
            function filterAndDisplayVendors(searchTerm, mode = 'add') {
                // Determine which elements to use based on mode
                const resultsElem = mode === 'add' ? vendorResults : editVendorResults;
                const searchInputElem = mode === 'add' ? vendorSearchInput : editVendorSearchInput;
                const idInputElem = mode === 'add' ? vendorIdInput : editVendorIdInput;

                // Make sure dropdown is visible
                resultsElem.style.display = 'block';

                // Show loading message during search
                if (searchTerm && searchTerm.length > 0) {
                    resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Mencari vendor...</div>';
                }

                // If we have no vendors yet
                if (allVendors.length === 0) {
                    resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                    return;
                }

                // Filter vendors
                let filteredVendors = allVendors;
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    filteredVendors = allVendors.filter(vendor =>
                        vendor.vendor_name?.toLowerCase().includes(term)
                    );
                }

                // Sort by relevance if we have a search term
                if (searchTerm) {
                    filteredVendors.sort((a, b) => {
                        // Exact matches first
                        if (a.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return -1;
                        if (b.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return 1;

                        // Then starts-with matches
                        const aStarts = a.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        const bStarts = b.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                        if (aStarts && !bStarts) return -1;
                        if (bStarts && !aStarts) return 1;

                        // Then alphabetical
                        return a.vendor_name.localeCompare(b.vendor_name);
                    });
                }

                // Limit to first 20 for performance
                const displayVendors = filteredVendors.slice(0, 20);

                // Update DOM with animation delay
                resultsElem.innerHTML = '';

                if (displayVendors.length === 0) {
                    resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada vendor yang ditemukan</div>';
                    return;
                }

                // Add vendor items with staggered animation
                displayVendors.forEach((vendor, index) => {
                    const div = document.createElement('div');
                    div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                    div.textContent = vendor.vendor_name;
                    div.setAttribute('data-id', vendor.vendor_id);
                    div.style.animationDelay = `${index * 30}ms`; // Staggered animation

                    div.addEventListener('click', function() {
                        idInputElem.value = this.getAttribute('data-id');
                        searchInputElem.value = this.textContent;
                        resultsElem.style.display = 'none';
                    });

                    resultsElem.appendChild(div);
                });

                // Show count if limited
                if (filteredVendors.length > 20) {
                    const countDiv = document.createElement('div');
                    countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                    countDiv.textContent = `Menampilkan 20 dari ${filteredVendors.length} vendor`;
                    resultsElem.appendChild(countDiv);
                }
            }

            // Initialize user search functionality
            initUserSearch();

            // Function to initialize user search
            function initUserSearch() {
                const searchInput = document.getElementById('user_search');
                const dropdown = document.getElementById('user_dropdown');
                const userList = document.getElementById('user_list');
                const loadingIndicator = document.getElementById('user_loading');
                const selectedUserId = document.getElementById('selected_user_id');

                if (!searchInput || !dropdown || !userList) return;

                // Toggle dropdown visibility
                searchInput.addEventListener('focus', function() {
                    dropdown.classList.remove('hidden');
                    if (userList.children.length === 0) {
                        loadUsers(''); // Initial load on focus
                    }
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Search input handler with debounce
                const debouncedSearch = debounce(function(e) {
                    loadUsers(e.target.value);
                }, 300);

                searchInput.addEventListener('input', debouncedSearch);

                // Function to load users
                async function loadUsers(searchTerm) {
                    // Show loading indicator
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    userList.innerHTML = '';

                    try {
                        // Use locally available data instead of fetching from server
                        let users = window.usersData || [];

                        // Filter users based on search term
                        if (searchTerm) {
                            searchTerm = searchTerm.toLowerCase();
                            users = users.filter(user => {
                                return (user.employee_number && user.employee_number.toLowerCase().includes(searchTerm)) ||
                                       (user.name && user.name.toLowerCase().includes(searchTerm)) ||
                                       (user.user_id && user.user_id.toString().includes(searchTerm));
                            });
                        }

                        // Sort by relevance if search term exists
                        if (searchTerm) {
                            users.sort((a, b) => {
                                const aStartsWithEmp = a.employee_number && a.employee_number.toLowerCase().startsWith(searchTerm);
                                const bStartsWithEmp = b.employee_number && b.employee_number.toLowerCase().startsWith(searchTerm);
                                if (aStartsWithEmp && !bStartsWithEmp) return -1;
                                if (!aStartsWithEmp && bStartsWithEmp) return 1;

                                const aStartsWithName = a.name && a.name.toLowerCase().startsWith(searchTerm);
                                const bStartsWithName = b.name && b.name.toLowerCase().startsWith(searchTerm);
                                if (aStartsWithName && !bStartsWithName) return -1;
                                if (!aStartsWithName && bStartsWithName) return 1;

                                return 0;
                            });
                        }

                        // Populate dropdown
                        userList.innerHTML = '';

                        if (users.length === 0) {
                            const noResults = document.createElement('li');
                            noResults.className = 'px-4 py-2 text-gray-500 italic';
                            noResults.textContent = 'No users found';
                            userList.appendChild(noResults);
                        } else {
                            // Limit to first 10 results for performance
                            const limitedUsers = users.slice(0, 10);

                            limitedUsers.forEach(user => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                // Display employee_number with user's name if available
                                let displayText = '';
                                if (user.employee_number) {
                                    displayText = user.employee_number;
                                    if (user.name) {
                                        displayText += ` - ${user.name}`;
                                    }
                                } else {
                                    displayText = user.name || `User ID: ${user.user_id}`;
                                }

                                li.textContent = displayText;
                                li.setAttribute('data-id', user.user_id);
                                li.setAttribute('data-employee-number', user.employee_number || '');

                                li.addEventListener('click', function() {
                                    // Set the selected user ID
                                    selectedUserId.value = this.getAttribute('data-id');

                                    // Update the search input with employee number or name
                                    const employeeNumber = this.getAttribute('data-employee-number');
                                    if (employeeNumber) {
                                        searchInput.value = employeeNumber;
                                    } else {
                                        searchInput.value = this.textContent;
                                    }

                                    // Hide dropdown
                                    dropdown.classList.add('hidden');
                                });

                                userList.appendChild(li);
                            });

                            // Show count if limited
                            if (users.length > 10) {
                                const countDiv = document.createElement('li');
                                countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t';
                                countDiv.textContent = `Menampilkan 10 dari ${users.length} pengguna`;
                                userList.appendChild(countDiv);
                            }
                        }
                    } catch (error) {
                        console.error('Error loading users:', error);
                        const errorItem = document.createElement('li');
                        errorItem.className = 'px-4 py-2 text-red-500';
                        errorItem.textContent = 'Gagal memproses data pengguna';
                        userList.appendChild(errorItem);
                    } finally {
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    }
                }
            }

            // Form submission handling
            document.getElementById('addMaintenanceForm')?.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const interval = document.getElementById('interval').value;
                const assignedTo = document.getElementById('selected_user_id').value;

                let errorMessages = [];

                if (!startDate) {
                    errorMessages.push('Tanggal mulai diperlukan');
                }

                if (!endDate) {
                    errorMessages.push('Tanggal akhir diperlukan');
                }

                if (!interval) {
                    errorMessages.push('Interval diperlukan');
                }

                if (!assignedTo) {
                    errorMessages.push('Bidang yang ditugaskan diperlukan');
                }

                if (selectedAssets.length === 0) {
                    errorMessages.push('Silakan pilih setidaknya satu aset');
                }

                if (errorMessages.length > 0) {
                    showToast(errorMessages, 'error');
                    return;
                }

                // Create a JSON object instead of FormData to properly control types
                const formData = new FormData(this);
                const jsonData = {};

                // Process regular form fields
                for (const [key, value] of formData.entries()) {
                    if (key !== 'asset_ids[]') {
                        jsonData[key] = value;
                    }
                }

                // Convert assigned_to to number
                if (jsonData.assigned_to) {
                    jsonData.assigned_to = parseInt(jsonData.assigned_to, 10);
                }

                // Convert vendor_id to number or remove if empty
                if (jsonData.vendor_id) {
                    jsonData.vendor_id = parseInt(jsonData.vendor_id, 10);
                } else {
                    delete jsonData.vendor_id;
                }

                // Don't send end_date for ONCE or DAILY intervals
                if (jsonData.interval === 'ONCE' || jsonData.interval === 'DAILY') {
                    delete jsonData.end_date;
                }

                // Convert asset_ids to array of numbers
                jsonData.asset_ids = selectedAssets.map(asset => {
                    // Make sure each asset ID is a valid number
                    const assetId = parseInt(asset.id, 10);
                    if (isNaN(assetId)) {
                        console.error('Invalid asset ID:', asset.id);
                        throw new Error('Invalid asset ID: ' + asset.id);
                    }
                    return assetId;
                });

                // Log the final data before sending
                console.log('Sending maintenance data:', jsonData);

                // Validation check for asset_ids format
                if (!Array.isArray(jsonData.asset_ids) || jsonData.asset_ids.length === 0) {
                    showToast('Error: Tidak ada ID aset yang valid untuk dikirim', 'error');
                    return;
                }

                try {
                    // Send the JSON data to the server
                fetch('/maintenance', {
                    method: 'POST',
                        body: JSON.stringify(jsonData),
                    headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                .then(handleApiResponse)
                .then(data => {
                        console.log('Maintenance creation response:', data);
                    if (data.success) {
                        showToast(data.message || 'Jadwal pemeliharaan berhasil dibuat', 'success');
                        closeModal(modals.add, modalContents.add);

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                            // Handle unsuccessful response - check for error message
                            if (data.error) {
                                showToast(data.error, 'error');
                            } else if (data.message) {
                                showToast(data.message, 'error');
                            } else if (data.errors) {
                            showToast({ errors: data.errors }, 'error');
                            } else {
                            showToast('Gagal membuat jadwal pemeliharaan', 'error');
                            }
                    }
                })
                .catch(error => {
                    console.error('Error creating maintenance schedule:', error);

                    // Handle different error formats
                    if (error && error.errors) {
                        if (Array.isArray(error.errors)) {
                            // If we have an array of errors with path and message properties
                            showToast(error.errors, 'error');
                        } else {
                            // If we have structured validation errors in object format
                            showToast({ errors: error.errors }, 'error');
                        }
                    } else if (error && error.status === 422) {
                        // If it's a validation error but no structured data
                        showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                    } else {
                        // Generic error message
                        showToast(error.message || 'Gagal membuat jadwal pemeliharaan', 'error');
                    }
                });
                } catch (error) {
                    console.error('Error handling maintenance form submission:', error);
                    showToast('Gagal memproses formulir', 'error');
                }
            });

            // Export PDF functionality
            document.getElementById('exportBtn')?.addEventListener('click', () => {
                // Get current URL parameters
                const url = new URL(window.location.href);
                const searchParams = url.searchParams;

                // Create the PDF export URL with the same parameters
                const exportUrl = "{{ route('maintenance.export.pdf') }}?" + searchParams.toString();

                // Redirect to the export URL
                window.open(exportUrl, '_blank');
            });

            // Delete maintenance buttons
            document.querySelectorAll('.delete-maintenance-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const maintenanceId = this.getAttribute('data-id');
                    const deleteMaintenanceName = document.getElementById('deleteMaintenanceName');

                    // Store the maintenance ID for later use
                    document.getElementById('deleteMaintenanceForm').setAttribute('data-id', maintenanceId);

                    // Get maintenance details to show in the confirmation modal
                    const assetName = this.closest('tr').querySelector('td:nth-child(1) .font-medium').textContent;
                    const assetCode = this.closest('tr').querySelector('td:nth-child(1) .text-gray-500').textContent;

                    // Set maintenance name in the modal
                    deleteMaintenanceName.textContent = `${assetName} (${assetCode.replace('Kode: ', '')})`;

                    // Open delete confirmation modal
                    openModal(modals.delete, modalContents.delete);
                });
            });

            // Form submission handler for delete
            document.getElementById('deleteMaintenanceForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Get the maintenance ID from the data attribute
                const maintenanceId = this.getAttribute('data-id');

                // Make the DELETE request directly to the ID-specific endpoint
                fetch(`/maintenance/${maintenanceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(handleApiResponse)
                .then(data => {
                    // Close the modal
                    closeModal(modals.delete, modalContents.delete);

                    if (data.success) {
                        // Show toast notification first
                        showToast(data.message || 'Rekaman pemeliharaan berhasil dihapus', 'success');

                        // Delay the redirect slightly to allow the toast to be seen
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Gagal menghapus rekaman pemeliharaan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete request failed:', error);
                    closeModal(modals.delete, modalContents.delete);

                    // Handle different error formats
                    if (error && error.errors) {
                        if (Array.isArray(error.errors)) {
                            // If we have an array of errors with path and message properties
                            showToast(error.errors, 'error');
                        } else {
                            // If we have structured validation errors in object format
                            showToast({ errors: error.errors }, 'error');
                        }
                    } else if (error && error.status === 422) {
                        // If it's a validation error but no structured data
                        showToast(`Validasi gagal: ${error.message || 'Silakan periksa form Anda'}`, 'error');
                    } else {
                        // Generic error message
                        showToast(error.message || 'Gagal menghapus rekaman pemeliharaan', 'error');
                    }
                });
            });

            // Edit maintenance buttons
            document.querySelectorAll('.edit-maintenance-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const maintenanceId = this.getAttribute('data-id');
                    const assetName = this.closest('tr').querySelector('td:nth-child(1) .font-medium').textContent;
                    const assetCode = this.closest('tr').querySelector('td:nth-child(1) .text-gray-500').textContent.replace('Kode: ', '');

                    // Show loading state in edit form
                    document.getElementById('edit_asset_name').textContent = assetName;
                    document.getElementById('edit_asset_code').textContent = assetCode;

                    // Reset form values
                    document.getElementById('editMaintenanceForm').reset();

                    // Fetch maintenance details
                    fetch(`/maintenance/${maintenanceId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil detail pemeliharaan');
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (!result.success) {
                            throw new Error(result.message || 'Gagal mengambil detail pemeliharaan');
                        }

                        const maintenance = result.data;

                        // Populate form with maintenance data
                        document.getElementById('edit_maintenance_id').value = maintenance.id;

                        // Format dates (API returns ISO format, input requires YYYY-MM-DD)
                        if (maintenance.start_date) {
                            const startDate = new Date(maintenance.start_date);
                            document.getElementById('edit_start_date').value = startDate.toISOString().split('T')[0];
                        }

                        if (maintenance.end_date) {
                            const endDate = new Date(maintenance.end_date);
                            document.getElementById('edit_end_date').value = endDate.toISOString().split('T')[0];
                        }

                        // Set interval
                        if (maintenance.interval) {
                            document.getElementById('edit_interval').value = maintenance.interval;
                            // Apply end date visibility based on interval
                            toggleEndDateVisibility(maintenance.interval, 'edit');
                        }

                        // Set assigned_to
                        if (maintenance.assigned_to) {
                            document.getElementById('edit_assigned_to').value = maintenance.assigned_to;
                        }

                        // Set vendor_id and vendor_name (if exists)
                        if (maintenance.vendor_id && maintenance.vendor_name) {
                            document.getElementById('edit_vendor_id').value = maintenance.vendor_id;
                            document.getElementById('edit_vendor_search').value = maintenance.vendor_name;
                        } else {
                            document.getElementById('edit_vendor_id').value = '';
                            document.getElementById('edit_vendor_search').value = '';
                        }

                        // Open the edit modal
                        openModal(modals.edit, modalContents.edit);
                    })
                    .catch(error => {
                        console.error('Error fetching maintenance details:', error);
                        showToast(error.message || 'Gagal mengambil detail pemeliharaan', 'error');
                    });
                });
            });

            // Form submission handler for edit
            document.getElementById('editMaintenanceForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const maintenanceId = document.getElementById('edit_maintenance_id').value;
                if (!maintenanceId) {
                    showToast('ID pemeliharaan tidak ada', 'error');
                    return;
                }

                // Collect form data into JSON
                const formData = {
                    interval: document.getElementById('edit_interval').value,
                    start_date: document.getElementById('edit_start_date').value,
                    assigned_to: parseInt(document.getElementById('edit_assigned_to').value, 10),
                    vendor_id: document.getElementById('edit_vendor_id').value ? parseInt(document.getElementById('edit_vendor_id').value, 10) : null
                };

                // Only add end_date if interval is not ONCE or DAILY
                if (formData.interval !== 'ONCE' && formData.interval !== 'DAILY') {
                    formData.end_date = document.getElementById('edit_end_date').value;
                }

                // Make the PUT request to update the maintenance
                fetch(`/maintenance/${maintenanceId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(handleApiResponse)
                .then(data => {
                    // Close the modal
                    closeModal(modals.edit, modalContents.edit);

                    if (data.success) {
                        // Show toast notification
                        showToast(data.message || 'Rekaman pemeliharaan berhasil diperbarui', 'success');

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Gagal memperbarui rekaman pemeliharaan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Update request failed:', error);

                    // Handle different error formats
                    if (error && error.errors) {
                        if (Array.isArray(error.errors)) {
                            // If we have an array of errors with path and message properties
                            showToast(error.errors, 'error');
                        } else {
                            // If we have structured validation errors in object format
                            showToast({ errors: error.errors }, 'error');
                        }
                    } else if (error && error.status === 422) {
                        // If it's a validation error but no structured data
                        showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                    } else {
                        // Generic error message
                        showToast(error.message || 'Gagal memperbarui rekaman pemeliharaan', 'error');
                    }
                });
            });

            // Create Report buttons
            document.querySelectorAll('.create-report-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const maintenanceId = this.getAttribute('data-id');
                    // Coba ambil dari atribut data dulu
                    let assetName = this.getAttribute('data-asset-name');
                    let assetCode = this.getAttribute('data-asset-code');

                    // Jika tidak ada di data attributes, ambil dari row
                    if (!assetName || !assetCode) {
                        assetName = this.closest('tr').querySelector('td:nth-child(1) .font-medium').textContent;
                        assetCode = this.closest('tr').querySelector('td:nth-child(1) .text-gray-500').textContent.replace('Kode: ', '');
                    }

                    // Set the form data
                    document.getElementById('report_maintenance_id').value = maintenanceId;
                    document.getElementById('report_asset_name').textContent = assetName;
                    document.getElementById('report_asset_code').textContent = assetCode;

                    // Set default date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('maintenance_date').value = today;

                    // Reset form fields but preserve ID and date
                    document.getElementById('createReportForm').reset();
                    document.getElementById('report_maintenance_id').value = maintenanceId; // Re-set ID after reset
                    document.getElementById('maintenance_date').value = today; // Re-set date after reset

                    // Reset image preview
                    document.getElementById('image-preview').classList.add('hidden');

                    // Open the modal
                    openModal(modals.report, modalContents.report);
                });
            });

            // Form submission handler for create report
            document.getElementById('createReportForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const maintenanceId = document.getElementById('report_maintenance_id').value;
                const description = document.getElementById('description').value;
                const maintenanceDate = document.getElementById('maintenance_date').value;

                if (!maintenanceId) {
                    showToast('ID pemeliharaan tidak ada', 'error');
                    return;
                }

                if (!description) {
                    showToast('Deskripsi diperlukan', 'error');
                    return;
                }

                if (!maintenanceDate) {
                    showToast('Tanggal laporan diperlukan', 'error');
                    return;
                }

                // Create FormData object for file upload
                const formData = new FormData(this);

                // Make sure reporter_number is included
                if (!formData.has('reporter_number')) {
                    formData.append('reporter_number', '1234'); // Default value
                }

                // Log formData for debugging
                console.log('Submitting maintenance report with data:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + (pair[0] === 'attachment' ? 'FILE' : pair[1]));
                }

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        <span>Membuat...</span>
                    </div>
                `;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Make the POST request to create the report
                fetch('/maintenance-reports', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin' // Important for CSRF
                })
                .then(handleApiResponse)
                .then(data => {
                    console.log('Server response:', data);

                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    // Close the modal
                    closeModal(modals.report, modalContents.report);

                    if (data.success) {
                        // Show toast notification
                        showToast(data.message || 'Laporan pemeliharaan berhasil dibuat', 'success');

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Gagal membuat laporan pemeliharaan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Gagal membuat laporan pemeliharaan:', error);

                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    // Handle different error formats
                    if (error && error.errors) {
                        if (Array.isArray(error.errors)) {
                            // If we have an array of errors with path and message properties
                            showToast(error.errors, 'error');
                        } else {
                            // If we have structured validation errors in object format
                            showToast({ errors: error.errors }, 'error');
                        }
                    } else if (error && error.status === 422) {
                        // If it's a validation error but no structured data
                        showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                    } else {
                        // Generic error message
                        showToast(error.message || 'Gagal membuat laporan pemeliharaan', 'error');
                    }
                });
            });

            // File upload preview for attachment
            document.getElementById('attachment')?.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgElement = document.querySelector('#image-preview img');
                        imgElement.src = e.target.result;
                        document.getElementById('image-preview').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Remove image button for attachment
            document.getElementById('remove-image')?.addEventListener('click', function(e) {
                e.preventDefault();
                const fileInput = document.getElementById('attachment');
                if (fileInput) {
                    fileInput.value = ''; // Clear the file input
                }
                document.getElementById('image-preview').classList.add('hidden');
            });

            // Initialize datepicker for maintenance date in report form
            if (document.getElementById('maintenance_date')) {
                // Set max attribute to today
                const todayDate = new Date().toISOString().split('T')[0];
                document.getElementById('maintenance_date').value = todayDate;
                document.getElementById('maintenance_date').setAttribute('max', todayDate);
            }
        });
    </script>
    @endpush

    <style>
        /* Animation for the vendor search dropdown items */
        .vendor-item {
            opacity: 0;
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out forwards;
        }
    </style>
@endsection
