@extends('Layout.app')

@section('title', 'Manajemen Pemeliharaan')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Maintenance Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PEMELIHARAAN</h1>

                        <div class="flex gap-4">
                            @if(hasPermission('maintenance:export'))
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

                            @if(hasPermission('maintenance:create'))
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
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput"
                                placeholder="Cari berdasarkan nama aset, interval, atau status..."
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
                                <option value="in progress">Dalam Proses</option>
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[120px]">Status</th>
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
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $maintenance['assigned_to_employee_name'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $maintenance['vendor_name'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] w-[120px]">
                                            @php
                                                $statusClass = '';
                                                $status = $maintenance['status'] ?? '';
                                                $statusText = '-';

                                                if ($status == 'new') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'Baru';
                                                } elseif ($status == 'in progress') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Dalam Proses';
                                                } elseif ($status == 'finished') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Selesai';
                                                }
                                            @endphp
                                            <div class="flex justify-center">
                                                <span class="px-2 py-1 rounded text-xs {{ $statusClass }} whitespace-nowrap">
                                                    {{ $statusText }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <!-- View Details Icon (Eye) -->
                                                <a href="{{ route('maintenance.detail', ['id' => $maintenance['id']]) }}"
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

                                                @if(!in_array(strtolower($maintenance['status'] ?? ''), ['finished', 'selesai']))
                                                    <!-- Edit Maintenance Icon (Pencil) -->
                                                    @if(hasPermission('maintenance:edit'))
                                                        <button
                                                            class="edit-maintenance-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                            data-id="{{ $maintenance['id'] }}" title="Edit Pemeliharaan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    @endif

                                                    <!-- Create Report Icon (Document) -->
                                                    @php
                                                        $canCreateReport = false;
                                                        $loggedInUserId = session('user_id');
                                                        $assignedUserId = $maintenance['assigned_to'] ?? null;

                                                        $canCreateReport = ($loggedInUserId && $assignedUserId && $loggedInUserId == $assignedUserId);

                                                        $hasReportPermission = hasPermission('maintenance-report:medical') ||
                                                            hasPermission('maintenance-report:non-medical');
                                                    @endphp

                                                    @if($canCreateReport && $hasReportPermission)
                                                        <button
                                                            class="create-report-btn p-2 bg-green-100 text-green-500 rounded-md hover:bg-green-200 transition-colors"
                                                            data-id="{{ $maintenance['id'] }}"
                                                            data-asset-name="{{ $maintenance['asset_name'] ?? '' }}"
                                                            data-asset-code="{{ $maintenance['asset_code'] ?? '' }}"
                                                            data-assigned-to="{{ $maintenance['assigned_to'] ?? '' }}"
                                                            data-status="{{ $maintenance['status'] ?? 'new' }}"
                                                            title="Buat Laporan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </button>
                                                    @endif

                                                    <!-- Delete Maintenance Icon (Trash) -->
                                                    @if(hasPermission('maintenance:delete'))
                                                        <button
                                                            class="delete-maintenance-btn p-2 bg-[#F9D2D2] text-[#8E2121] rounded-md hover:bg-red-200 transition-colors"
                                                            data-id="{{ $maintenance['id'] }}" title="Hapus Pemeliharaan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    @endif
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
                            <button onclick="window.location.href='{{ $maintenances_pagination['prev_page_url'] ?? '#' }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($maintenances_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ ($maintenances_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </button>
                            <div class="flex gap-2">
                                @php
                                        $currentPage = $maintenances_pagination['current_page'] ?? 1;
                                        $lastPage = $maintenances_pagination['last_page'] ?? 1;
                                        $maxPagesShown = 5; // Show max 5 pages at once
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $startPage + $maxPagesShown - 1);

                                        if ($endPage - $startPage + 1 < $maxPagesShown) {
                                            $startPage = max(1, $endPage - $maxPagesShown + 1);
                                        }
                                    @endphp

                                    @if($startPage > 1)
                                        <button onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => 1]) }}'"
                                            class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                            1
                                        </button>
                                        @if($startPage > 2)
                                            <span class="flex items-center justify-center">
                                                ...
                                            </span>
                                        @endif
                                    @endif

                                    @for ($i = $startPage; $i <= $endPage; $i++)
                                        <button onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => $i]) }}'"
                                            class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <span class="flex items-center justify-center">
                                                ...
                                            </span>
                                        @endif
                                        <button onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}'"
                                            class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                            {{ $lastPage }}
                                        </button>
                                    @endif
                                </div>
                                <button onclick="window.location.href='{{ $maintenances_pagination['next_page_url'] ?? '#' }}'"
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($maintenances_pagination['current_page'] ?? 1) >= ($maintenances_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ ($maintenances_pagination['current_page'] ?? 1) >= ($maintenances_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                                    Selanjutnya
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 mt-4 md:mt-0">
                                <span class="text-sm text-gray-600">
                                        @if(isset($maintenances_pagination) && is_array($maintenances_pagination))
                                            Menampilkan {{ $maintenances_pagination['from'] ?? 0 }} sampai {{ $maintenances_pagination['to'] ?? 0 }} dari
                                            {{ $maintenances_pagination['total'] ?? 0 }} data
                                        @else
                                        Menampilkan 0 sampai 0 dari 0 data
                                    @endif
                                </span>
                                <select id="perPageSelect"
                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                    onchange="changePerPage(this.value)">
                                        <option value="10" {{ isset($maintenances_pagination['per_page']) && $maintenances_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                        <option value="25" {{ isset($maintenances_pagination['per_page']) && $maintenances_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                        <option value="50" {{ isset($maintenances_pagination['per_page']) && $maintenances_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Maintenance Modal -->
            @if(hasPermission('maintenance:create'))
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
                                    <form id="addMaintenanceForm" class="space-y-6" data-no-loading>
                                        @csrf
                                        <!-- Required fields note -->
                                        <div class="text-sm text-gray-600">
                                            Kolom dengan tanda <span class="text-red-500">*</span> wajib diisi
                                        </div>

                                        <!-- Schedule Dates -->
                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 space-y-4">
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label class="block text-base font-semibold text-[#213268]">
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
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Interval harus dipilih</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label class="block text-base font-semibold text-[#213268]">
                                                        TANGGAL MULAI<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="date" name="start_date" id="start_date"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                        >
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal mulai diperlukan</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label class="block text-base font-semibold text-[#213268]">
                                                        TANGGAL SELESAI<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="date" name="end_date" id="end_date"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                        >
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal selesai diperlukan</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label class="block text-base font-semibold text-[#213268]">
                                                        DITUGASKAN KE<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <input type="text" id="user_search" placeholder="Cari karyawan (nomor karyawan)..."
                                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200" autocomplete="off">
                                                        <input type="hidden" name="assigned_to" id="selected_user_id">
                                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Karyawan harus dipilih</div>
                                                        <!-- Add radio buttons for permission selection -->
                                                        <div class="mt-2 flex items-center gap-4">
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="permission_filter" value="maintenance-report:medical" class="permission-radio text-[#213268]" data-asset-type="medical" checked>
                                                                <span class="ml-2 text-sm text-gray-700">User Medis</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="permission_filter" value="maintenance-report:non-medical" class="permission-radio text-[#213268]" data-asset-type="non_medical">
                                                                <span class="ml-2 text-sm text-gray-700">User Non-Medis</span>
                                                            </label>
                                                        </div>
                                                        <div id="user_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                                            <!-- Loading indicator -->
                                                            <div id="user_loading" class="flex justify-center py-2">
                                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <ul id="user_list" class="max-h-56 overflow-y-auto"></ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label class="block text-base font-semibold text-[#213268]">
                                                        VENDOR
                                                        </label>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <input type="text" id="vendor_search" placeholder="Cari vendor..."
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                        <input type="hidden" id="vendor_id" name="vendor_id">
                                                        <div id="vendor_results" class="absolute z-[100] w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300"></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" id="addAssetsBtn"
                                                        class="bg-[#213268] hover:bg-[#152349] text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
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
                                                            class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                            No</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                                            Kode Aset</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama
                                                            Aset</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                                            Deskripsi</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe
                                                            Aset</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama
                                                            Kategori</th>
                                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi
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
            @endif

            <!-- Asset Selection Modal -->
            @if(hasPermission('maintenance:create'))
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
                                                <option value="100">100 per halaman</option>
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

            <!-- Delete Confirmation Modal -->
            @if(hasPermission('maintenance:delete'))
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
                                <form id="deleteMaintenanceForm" data-no-loading>
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
            @endif

            <!-- Edit Maintenance Modal -->
            @if(hasPermission('maintenance:edit'))
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
                                    <form id="editMaintenanceForm" method="POST" data-no-loading>
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
                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 space-y-4 mb-6">
                                            <!-- Interval -->
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label for="edit_interval" class="block text-base font-semibold text-[#213268]">
                                                        INTERVAL<span class="text-red-500">*</span>
                                                        </label>
                                                    </div>
                                                <div class="flex-1">
                                                    <select id="edit_interval" name="interval"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
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
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Interval harus dipilih</div>
                                                </div>
                                            </div>

                                            <!-- Start Date -->
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label for="edit_start_date" class="block text-base font-semibold text-[#213268]">
                                                        TANGGAL MULAI<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="date" id="edit_start_date" name="start_date"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal mulai diperlukan</div>
                                                </div>
                                            </div>

                                            <!-- End Date -->
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label for="edit_end_date" class="block text-base font-semibold text-[#213268]">
                                                        TANGGAL SELESAI<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="date" id="edit_end_date" name="end_date"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal selesai diperlukan</div>
                                                </div>
                                            </div>

                                            <!-- Assigned To -->
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label for="edit_assigned_to" class="block text-base font-semibold text-[#213268]">
                                                        DITUGASKAN KE<span class="text-red-500">*</span>
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <input type="text" id="edit_user_search" placeholder="Cari karyawan (nomor karyawan)..."
                                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200" autocomplete="off">
                                                        <input type="hidden" id="edit_assigned_to" name="assigned_to">
                                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Karyawan harus dipilih</div>
                                                        <!-- Add radio buttons for permission selection in edit mode -->
                                                        <div class="mt-2 flex items-center gap-4">
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="edit_permission_filter" value="maintenance-report:medical" class="edit-permission-radio text-[#213268]" data-asset-type="medical" checked>
                                                                <span class="ml-2 text-sm text-gray-700">User Medis</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="edit_permission_filter" value="maintenance-report:non-medical" class="edit-permission-radio text-[#213268]" data-asset-type="non_medical">
                                                                <span class="ml-2 text-sm text-gray-700">User Non-Medis</span>
                                                            </label>
                                                        </div>
                                                        <div id="edit_user_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                                            <!-- Loading indicator -->
                                                            <div id="edit_user_loading" class="flex justify-center py-2">
                                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </div>
                                                            <ul id="edit_user_list" class="max-h-56 overflow-y-auto"></ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Vendor -->
                                            <div class="flex items-center gap-4">
                                                <div class="min-w-[150px]">
                                                    <label for="edit_vendor_search" class="block text-base font-semibold text-[#213268]">
                                                        VENDOR
                                                    </label>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <input type="text" id="edit_vendor_search" placeholder="Cari vendor..."
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                                        <input type="hidden" id="edit_vendor_id" name="vendor_id">
                                                        <div id="edit_vendor_results" class="absolute z-[100] w-full mt-1 bg-white shadow-lg max-h-60 rounded-md overflow-y-auto border border-gray-300"></div>
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
            @endif

            @if(hasPermission('maintenance-report:medical') || hasPermission('maintenance-report:non-medical'))
                    <!-- Create Maintenance Report Modal -->
                    <div id="createReportModal" class="fixed inset-0 z-50 hidden">
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                        <div class="fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                                    id="createReportModalContent">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center p-6 pb-0">
                                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">BUAT LAPORAN PEMELIHARAAN</h2>
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
                                        <form id="createReportForm" method="POST" enctype="multipart/form-data" data-no-loading>
                                            @csrf
                                            <input type="hidden" id="report_maintenance_id" name="maintenance_id">

                                            <!-- Required fields note -->
                                            <div class="text-sm text-gray-600 mb-4">
                                                Bidang dengan tanda <span class="text-red-500">*</span> wajib diisi
                                            </div>

                                            <!-- ASSET INFORMATION SECTION -->
                                            <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Informasi Aset</h3>

                                                <!-- Asset Image -->
                                                <div class="w-full h-40 bg-white mb-4 rounded-lg shadow-sm overflow-hidden relative flex items-center justify-center">
                                                    <img id="maintenance_asset_image" src="{{ asset('images/placeholder.png') }}"
                                                        alt="Asset Image" class="w-full h-full object-contain p-2"
                                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                                                    </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Left Column -->
                                                    <div class="space-y-4">
                                                        <!-- Asset Code -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">KODE ASET</label>
                                                            <input type="text" id="report_asset_code_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                    </div>

                                                        <!-- Asset Name -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">NAMA ASET</label>
                                                            <input type="text" id="report_asset_name_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Serial Number -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">NOMOR SERI</label>
                                                            <input type="text" id="report_serial_number_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                </div>
                                            </div>

                                                    <!-- Right Column -->
                                                    <div class="space-y-4">
                                                        <!-- Brand (Merk) -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">MERK</label>
                                                            <input type="text" id="report_brand_name_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                            </div>

                                                        <!-- Model -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">MODEL</label>
                                                            <input type="text" id="report_model_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>

                                                        <!-- Location -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">LOKASI</label>
                                                            <input type="text" id="report_location_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- MAINTENANCE SCHEDULE SECTION -->
                                            <div class="bg-yellow-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Jadwal Pemeliharaan</h3>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                    <!-- Start Date -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">TANGGAL MULAI</label>
                                                        <input type="text" id="report_start_date_display"
                                                            class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                            readonly>
                                                    </div>

                                                    <!-- End Date -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">TANGGAL SELESAI</label>
                                                        <input type="text" id="report_end_date_display"
                                                            class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                            readonly>
                                                    </div>

                                                    <!-- Interval -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">INTERVAL</label>
                                                        <input type="text" id="report_interval_display"
                                                            class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- MAINTENANCE REPORT DETAILS SECTION -->
                                            <div class="bg-green-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Detail Pemeliharaan</h3>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Left Column -->
                                                    <div>
                                                <!-- Maintenance Date -->
                                                        <div>
                                                            <label for="maintenance_date" class="block text-sm font-medium text-gray-700">
                                                            TANGGAL LAPORAN<span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="date" id="maintenance_date" name="maintenance_date"
                                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal laporan diperlukan</div>
                                                    </div>
                                                </div>

                                                    <!-- Right Column -->
                                                    <div>
                                                        <!-- Vendor -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">VENDOR</label>
                                                            <input type="text" id="report_vendor_display"
                                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Description - Full Width -->
                                                <div class="mt-4">
                                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                                            DESKRIPSI<span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea id="description" name="description" rows="4"
                                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                        placeholder="Tambahkan detail laporan pemeliharaan..."></textarea>
                                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Deskripsi diperlukan</div>
                                                    </div>
                                                </div>

                                            <!-- DOCUMENTATION SECTION -->
                                            <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                                <h3 class="text-[#213268] font-semibold text-lg mb-4">Dokumentasi</h3>

                                                <!-- Attachment -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">BERKAS TERUNGGAH</label>
                                                    <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                            <!-- Image preview -->
                                                            <div id="image-preview" class="mt-2 mb-4 w-full hidden">
                                                            <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                                <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Pratinjau file">
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
                                                            <p class="mt-1 text-sm text-gray-600">Seret gambar Anda atau <span class="text-[#213268] font-semibold">jelajahi berkas</span></p>
                                                            <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png (Maks: 5MB)</p>
                                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di area ini untuk memilih file</p>
                                                            </div>
                                                            <input type="file" id="attachment" name="file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Button Group -->
                                            <div class="pt-4">
                                                <button type="submit"
                                                    class="w-full px-6 py-3 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
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
            @endif

        @push('scripts')
        <!-- Tambahkan di bagian head atau sebelum </body> -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const indonesianLocale = {
                        weekdays: {
                            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                            longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                        },
                        months: {
                            shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                            longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                        },
                        firstDayOfWeek: 1,
                        ordinal: () => {
                            return "";
                        },
                        rangeSeparator: " sampai ",
                        weekAbbreviation: "Minggu",
                        scrollTitle: "Gulir untuk menambah",
                        toggleTitle: "Klik untuk beralih",
                        time_24hr: true,
                    };

                    function loadFlatpickr() {
                        if (typeof flatpickr === 'undefined') {
                            const cssLink = document.createElement('link');
                            cssLink.rel = 'stylesheet';
                            cssLink.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
                            document.head.appendChild(cssLink);

                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                            script.onload = function() {
                                if (flatpickr && flatpickr.l10ns) {
                                    flatpickr.l10ns.id = indonesianLocale;
                                    initAllDatepickers();
                                }
                            };
                            document.head.appendChild(script);
                        } else {
                            if (flatpickr.l10ns) {
                                flatpickr.l10ns.id = indonesianLocale;
                            }
                            initAllDatepickers();
                        }
                    }

                    function initAllDatepickers() {
                        const dateFields = [
                            'start_date',
                            'end_date',
                            'edit_start_date',
                            'edit_end_date',
                            'maintenance_date'
                        ];
                        dateFields.forEach(fieldId => {
                            const dateField = document.getElementById(fieldId);
                            if (dateField) {
                                initFlatpickr(dateField, false);
                            }
                        });
                    }

                    function initFlatpickr(dateInput, isReadonly) {
                        if (!dateInput) return;
                        // Check if this is an edit field
                        const isEditField = dateInput.id.startsWith('edit_');

                        const fpInstance = flatpickr(dateInput, {
                            locale: 'id',
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "j F Y",
                            static: true,
                            disableMobile: true,
                            allowInput: false,
                            clickOpens: !isReadonly,
                            // Only apply minDate: today for new entries, not for edit fields
                            minDate: isEditField ? null : "today",
                            onReady: function(selectedDates, dateStr, instance) {
                                if (instance.altInput) {
                                    instance.altInput.style.width = "100%";
                                    instance.altInput.style.display = "block";
                                    const parentWrapper = instance.altInput.closest('.flatpickr-wrapper');
                                    if (parentWrapper) {
                                        parentWrapper.style.width = "100%";
                                        parentWrapper.style.display = "block";
                                    }
                                    instance.altInput.className = dateInput.className;
                                }
                                if (selectedDates && selectedDates.length > 0) {
                                    const date = selectedDates[0];
                                    const day = date.getDate();
                                    const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();
                                    if (instance.altInput) {
                                        instance.altInput.value = `${day} ${month} ${year}`;
                                    }
                                }
                            },
                            onChange: function(selectedDates, dateStr, instance) {
                                if (selectedDates && selectedDates.length > 0) {
                                    const date = selectedDates[0];
                                    const day = date.getDate();
                                    const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();
                                    if (instance.altInput) {
                                        instance.altInput.value = `${day} ${month} ${year}`;
                                    }
                                }
                            },
                            formatDate: (date, format) => {
                                if (format === "Y-m-d") {
                                    const localDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                                    const year = localDate.getFullYear();
                                    const month = String(localDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(localDate.getDate()).padStart(2, '0');
                                    return `${year}-${month}-${day}`;
                                }
                                if (format === "j F Y") {
                                    const day = date.getDate();
                                    const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                    const month = monthsInIndonesian[date.getMonth()];
                                    const year = date.getFullYear();
                                    return `${day} ${month} ${year}`;
                                }
                                return flatpickr.formatDate(date, format);
                            },
                            parseDate: (datestr, format) => {
                                if (format === "Y-m-d") {
                                    const [year, month, day] = datestr.split("-").map(Number);
                                    return new Date(year, month - 1, day);
                                }
                                return flatpickr.parseDate(datestr, format);
                            }
                        });
                        return fpInstance;
                    }

                    loadFlatpickr();
                    @if(!hasPermission('maintenance:create'))
                        const createButtons = document.querySelectorAll('#addMaintenanceBtn, #addAssetsBtn');
                        createButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('maintenance:edit'))
                        const editButtons = document.querySelectorAll('.edit-maintenance-btn');
                        editButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('maintenance:delete'))
                        const deleteButtons = document.querySelectorAll('.delete-maintenance-btn');
                        deleteButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('maintenance:export'))
                        const exportButtons = document.querySelectorAll('#exportBtn');
                        exportButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    function validateField(field, isValid = null) {
                        let isFieldValid = isValid;
                        let fieldParent, errorElement;

                        if (!field) return false;

                        if (field.id === 'user_search') {
                            fieldParent = field.closest('.relative');
                            errorElement = fieldParent?.querySelector('.error-message');

                            if (isFieldValid === null) {
                                const selectedUserId = document.getElementById('selected_user_id');
                                isFieldValid = selectedUserId ? selectedUserId.value !== '' : false;
                            }
                        } else if (field.id === 'vendor_search') {
                            return true;
                        } else if (field.tagName?.toLowerCase() === 'select' || field.type === 'date') {
                            fieldParent = field.parentElement;
                            errorElement = fieldParent?.querySelector('.error-message');

                            if (isFieldValid === null) {
                                isFieldValid = field.value !== '';
                            }
                        } else {
                            fieldParent = field.parentElement;
                            errorElement = fieldParent?.querySelector('.error-message');

                            if (isFieldValid === null) {
                                isFieldValid = field.value.trim() !== '';
                            }
                        }

                        if (!isFieldValid) {
                            field.classList.add('border-red-500');
                            if (errorElement) errorElement.classList.remove('hidden');
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorElement) errorElement.classList.add('hidden');
                            return true;
                        }
                    }

                    const today = new Date().toISOString().split('T')[0];
                    const startDateInput = document.getElementById('start_date');
                    const endDateInput = document.getElementById('end_date');
                    const maintenanceDateInput = document.getElementById('maintenance_date');

                    function preventMultipleSubmits(form, buttonSelector) {
                        if (!form) return;

                        form.addEventListener('submit', function(e) {
                            const submitBtn = this.querySelector(buttonSelector);
                            if (submitBtn && !submitBtn.disabled) {
                                // Store original button text
                                submitBtn.dataset.originalText = submitBtn.innerHTML;

                                // Disable button and show loading state
                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Memproses...</span>
                                    </div>
                                `;

                                // Set safety timeout to restore button after 10 seconds
                                // This prevents stuck UI if something unexpected happens
                                const safetyTimeout = setTimeout(() => {
                                    resetButton(submitBtn);
                                }, 10000);

                                // Store the safety timeout ID on the form for later clearing
                                form.dataset.safetyTimeoutId = safetyTimeout;
                            }
                        });
                    }

                    // Function to reset button to original state
                    function resetButton(button) {
                        if (!button) return;

                        // Check if we stored the original text
                        const originalText = button.dataset.originalText || 'Simpan';

                        // Reset button state
                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-not-allowed');
                        button.innerHTML = originalText;
                    }

                    preventMultipleSubmits(document.getElementById('addMaintenanceForm'), 'button[type="submit"]');
                    preventMultipleSubmits(document.getElementById('editMaintenanceForm'), 'button[type="submit"]');
                    preventMultipleSubmits(document.getElementById('deleteMaintenanceForm'), 'button[type="submit"]');
                    preventMultipleSubmits(document.getElementById('createReportForm'), 'button[type="submit"]');

                    // Initialize flatpickr for start date with linked end date
                    if (startDateInput) {
                        const startDatePicker = initFlatpickr(startDateInput, false);
                        startDatePicker.set('minDate', today);
                        startDatePicker.config.onChange = function(selectedDates, dateStr, instance) {
                            if (endDatePicker && selectedDates[0]) {
                                endDatePicker.set('minDate', selectedDates[0]);
                                const currentInterval = document.getElementById('interval').value;
                                if (currentInterval === 'DAILY' || currentInterval === 'ONCE') {
                                    endDatePicker.setDate(selectedDates[0]);
                                }
                            }
                        };
                    }

                    // Initialize flatpickr for end date
                    let endDatePicker;
                    if (endDateInput) {
                        endDatePicker = initFlatpickr(endDateInput, false);
                        endDatePicker.set('minDate', today);
                    }

                    // Initialize flatpickr for maintenance date
                    if (maintenanceDateInput) {
                        const maintenanceDatePicker = initFlatpickr(maintenanceDateInput, false);
                        maintenanceDatePicker.set('minDate', today);
                    }

                    // Initialize flatpickr for edit start date with linked end date
                    const editStartDateInput = document.getElementById('edit_start_date');
                    const editEndDateInput = document.getElementById('edit_end_date');
                    if (editStartDateInput) {
                        const editStartDatePicker = initFlatpickr(editStartDateInput, false);
                        editStartDatePicker.set('minDate', today);
                        editStartDatePicker.config.onChange = function(selectedDates, dateStr, instance) {
                            if (editEndDatePicker && selectedDates[0]) {
                                editEndDatePicker.set('minDate', selectedDates[0]);
                                const currentEditInterval = document.getElementById('edit_interval').value;
                                if (currentEditInterval === 'DAILY' || currentEditInterval === 'ONCE') {
                                    editEndDatePicker.setDate(selectedDates[0]);
                                }
                            }
                        };
                    }

                    // Initialize flatpickr for edit end date
                    let editEndDatePicker;
                    if (editEndDateInput) {
                        editEndDatePicker = initFlatpickr(editEndDateInput, false);
                        editEndDatePicker.set('minDate', today);
                    }

                    function toggleEndDateVisibility(intervalValue, formType = 'add') {
                        const endDateField = formType === 'add'
                            ? document.getElementById('end_date').closest('.flex.items-center.gap-4')
                            : document.getElementById('edit_end_date').closest('.flex.items-center.gap-4');
                        const endDateInput = formType === 'add'
                            ? document.getElementById('end_date')
                            : document.getElementById('edit_end_date');

                        if (intervalValue === 'DAILY' || intervalValue === 'ONCE') {
                            endDateField.style.display = 'none';
                            endDateInput.removeAttribute('required');

                            const startDatePicker = formType === 'add' ? flatpickr('#start_date') : flatpickr('#edit_start_date');
                            const endDatePicker = formType === 'add' ? endDatePicker : editEndDatePicker;

                            if (startDatePicker.selectedDates[0]) {
                                endDatePicker.setDate(startDatePicker.selectedDates[0]);
                            }
                        } else {
                            endDateField.style.display = 'flex';
                            endDateInput.setAttribute('required', 'required');
                        }
                    }

                    const intervalSelect = document.getElementById('interval');
                    if (intervalSelect) {
                        intervalSelect.addEventListener('change', function() {
                            toggleEndDateVisibility(this.value, 'add');
                        });

                        if (intervalSelect.value === 'DAILY') {
                            toggleEndDateVisibility('DAILY', 'add');
                        }
                    }

                    const editIntervalSelect = document.getElementById('edit_interval');
                    if (editIntervalSelect) {
                        editIntervalSelect.addEventListener('change', function() {
                            toggleEndDateVisibility(this.value, 'edit');
                        });
                    }

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

                    function handleApiResponse(response) {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                if (!response.ok) {
                                    console.error('Server error response:', data);
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

                    window.showToast = function(message, type = 'success') {
                        const existingNotification = document.getElementById(type === 'success' ? 'successNotification' : 'errorNotification');
                        if (existingNotification) {
                            existingNotification.remove();
                        }

                        const notification = document.createElement('div');
                        notification.id = type === 'success' ? 'successNotification' : 'errorNotification';
                        notification.className = `fixed top-4 right-4 bg-${type === 'success' ? 'green' : 'red'}-100 border-l-4 border-${type === 'success' ? 'green' : 'red'}-500 text-${type === 'success' ? 'green' : 'red'}-700 p-4 rounded shadow-md z-50`;
                        notification.setAttribute('role', 'alert');

                        if (typeof message === 'object' && message !== null) {
                            if (message.errors && typeof message.errors === 'object') {
                                messageContent = '<ul class="list-disc pl-5 mt-2">';
                                for (const field in message.errors) {
                                    if (Array.isArray(message.errors[field])) {
                                        message.errors[field].forEach(error => {
                                            messageContent += `<li>${error}</li>`;
                                        });
                                    } else if (typeof message.errors[field] === 'object') {
                                        for (const subField in message.errors[field]) {
                                            messageContent += `<li>${subField}: ${message.errors[field][subField]}</li>`;
                                        }
                                    } else {
                                        messageContent += `<li>${field}: ${message.errors[field]}</li>`;
                                    }
                                }
                                messageContent += '</ul>';
                            } else if (Array.isArray(message)) {
                                messageContent = '<ul class="list-disc pl-5 mt-2">';

                                const generalErrors = message.filter(error =>
                                    typeof error === 'object' && error !== null &&
                                    error.path === 'general' && error.message
                                );

                                const fieldErrors = message.filter(error =>
                                    typeof error === 'object' && error !== null &&
                                    error.path && error.path !== 'general' && error.message
                                );

                                const otherErrors = message.filter(error =>
                                    !(typeof error === 'object' && error !== null && error.path && error.message)
                                );

                                generalErrors.forEach(error => {
                                    messageContent += `<li class="font-medium text-red-800 mb-2">${error.message}</li>`;
                                });

                                fieldErrors.forEach(error => {
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

                                otherErrors.forEach(error => {
                                    if (typeof error === 'string') {
                                        messageContent += `<li>${error}</li>`;
                                    } else {
                                        messageContent += `<li>${JSON.stringify(error)}</li>`;
                                    }
                                });

                                messageContent += '</ul>';
                            } else if (message.message) {
                                messageContent = message.message;
                            } else if (message.error) {
                                messageContent = message.error;
                            } else {
                                try {
                                    messageContent = '<ul class="list-disc pl-5 mt-2">';
                                    Object.entries(message).forEach(([key, value]) => {
                                        if (key !== 'stack' && key !== '__proto__') {
                                            if (typeof value === 'object' && value !== null) {
                                                messageContent += `<li>${key}: ${JSON.stringify(value)}</li>`;
                                            } else {
                                                messageContent += `<li>${key}: ${value}</li>`;
                                            }
                                        }
                                    });
                                    messageContent += '</ul>';

                                    if (messageContent === '<ul class="list-disc pl-5 mt-2"></ul>') {
                                        messageContent = JSON.stringify(message);
                                    }
                                } catch (e) {
                                    messageContent = "Error object could not be displayed";
                                }
                            }
                        } else {
                            messageContent = message;
                        }

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

                        document.body.appendChild(notification);

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

                    @if(session('success'))
                        showToast("{{ session('success') }}", 'success');
                    @endif

                    @if(session('error'))
                        showToast("{{ session('error') }}", 'error');
                    @endif

                    window.changePerPage = function (limit) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('limit', limit);
                        window.location.href = url.toString();
                    }

                    const statusFilterSelect = document.getElementById('statusFilter');
                    if (statusFilterSelect) {
                        statusFilterSelect.addEventListener('change', function() {
                            applyFilters();
                        });
                    }

                    const sortOrderSelect = document.getElementById('sortOrder');
                    if (sortOrderSelect) {
                        sortOrderSelect.addEventListener('change', function() {
                            applyFilters();
                        });
                    }

                    function applyFilters() {
                        const searchTerm = document.getElementById('searchInput').value;
                        const statusFilter = document.getElementById('statusFilter').value;
                        const sortOrder = document.getElementById('sortOrder').value;

                        if (document.getElementById('ajaxFilterButton')) {
                            fetchMaintenanceData(1, searchTerm, statusFilter, sortOrder);
                            return;
                        }

                        const url = new URL(window.location.href);

                        if (searchTerm) url.searchParams.set('search', searchTerm);
                        else url.searchParams.delete('search');

                        if (statusFilter) url.searchParams.set('status', statusFilter);
                        else url.searchParams.delete('status');

                        if (sortOrder) {
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
                            url.searchParams.set('sort', sortOrder);
                        } else {
                            url.searchParams.delete('sort_by');
                            url.searchParams.delete('sort_order');
                            url.searchParams.delete('sort');
                        }

                        url.searchParams.set('page', 1);
                        window.location.href = url.toString();
                    }

                    function fetchMaintenanceData(page = 1, search = '', status = '', sort = '') {
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

                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('search')) {
                            searchInput.value = urlParams.get('search');
                        }

                        searchInput.addEventListener('input', debounce(function() {
                            applyFilters();
                        }, 500));

                        searchInput.addEventListener('keypress', function(e) {
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

                        if (urlParams.has('sort') && urlParams.get('sort')) {
                            sortSelect.value = urlParams.get('sort');

                            if (sortSelect.selectedIndex === -1) {
                                sortSelect.selectedIndex = 1;
                            }
                        } else {
                            const sortBy = urlParams.get('sort_by');
                            const sortOrder = urlParams.get('sort_order');

                            if (sortBy && sortOrder) {
                                if (sortBy === 'created_at' && sortOrder === 'desc') {
                                    sortSelect.value = 'newest';
                                } else if (sortBy === 'created_at' && sortOrder === 'asc') {
                                    sortSelect.value = 'oldest';
                                }
                            } else {
                                sortSelect.selectedIndex = 1;
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

                    function closeModal(modal, modalContent) {
                        if (modal && modalContent) {
                            modalContent.classList.remove('opacity-100', 'scale-100');
                            modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4', 'sm:translate-y-0');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                document.body.classList.remove('overflow-hidden');
                                const modalId = modal.id;

                                if (modalId === 'addMaintenanceModal') {
                                    const form = document.getElementById('addMaintenanceForm');
                                    if (form) {
                                        form.reset();

                                        selectedAssets = [];
                                        updateSelectedAssetsList();

                                        form.querySelectorAll('input, select, textarea').forEach(field => {
                                            field.classList.remove('border-red-500');
                                        });
                                        form.querySelectorAll('.error-message').forEach(error => {
                                            error.classList.add('hidden');
                                        });

                                        const endDateField = document.getElementById('end_date').closest('.flex.items-center.gap-4');
                                        if (endDateField) endDateField.style.display = 'flex';
                                    }
                                }
                                else if (modalId === 'assetSelectionModal') {
                                    const assetSearchInput = document.getElementById('assetSearchInput');
                                    if (assetSearchInput) {
                                        assetSearchInput.value = '';
                                    }

                                    const selectAllCheckbox = document.getElementById('selectAllAssets');
                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.checked = false;
                                    }
                                }
                                else if (modalId === 'editMaintenanceModal') {
                                    const form = document.getElementById('editMaintenanceForm');
                                    if (form) {
                                        form.reset();

                                        form.querySelectorAll('input, select, textarea').forEach(field => {
                                            field.classList.remove('border-red-500');
                                        });
                                        form.querySelectorAll('.error-message').forEach(error => {
                                            error.classList.add('hidden');
                                        });

                                        const endDateField = document.getElementById('edit_end_date').closest('.flex.items-center.gap-4');
                                        if (endDateField) endDateField.style.display = 'flex';
                                    }
                                }
                                else if (modalId === 'createReportModal') {
                                    const form = document.getElementById('createReportForm');
                                    if (form) {
                                        form.reset();

                                        const imagePreview = document.getElementById('image-preview');
                                        if (imagePreview) imagePreview.classList.add('hidden');

                                        form.querySelectorAll('input, textarea').forEach(field => {
                                            field.classList.remove('border-red-500');
                                        });
                                        form.querySelectorAll('.error-message').forEach(error => {
                                            error.classList.add('hidden');
                                        });

                                        const todayDate = new Date().toISOString().split('T')[0];
                                        const maintenanceDate = document.getElementById('maintenance_date');
                                        if (maintenanceDate) maintenanceDate.value = todayDate;
                                    }
                                }
                                else if (modalId === 'deleteMaintenanceModal') {
                                    const form = document.getElementById('deleteMaintenanceForm');
                                    if (form) {
                                        form.reset();
                                        form.removeAttribute('data-id');
                                    }
                                }
                            }, 300);
                        }
                    }

                    document.querySelectorAll('.close-modal').forEach(button => {
                        button.addEventListener('click', function() {
                            const modalId = this.getAttribute('data-modal');
                            const modal = document.getElementById(modalId);
                            const modalContent = document.getElementById(modalId + 'Content');
                            closeModal(modal, modalContent);
                        });
                    });

                    const addMaintenanceBtn = document.getElementById('addMaintenanceBtn');
                    if (addMaintenanceBtn) {
                        addMaintenanceBtn.addEventListener('click', function() {
                        openModal(modals.add, modalContents.add);
                    });
                    }

                    const addAssetsBtn = document.getElementById('addAssetsBtn');
                    if (addAssetsBtn) {
                        addAssetsBtn.addEventListener('click', function() {
                        openModal(modals.assetSelection, modalContents.assetSelection);
                        loadAssets(1);
                    });
                    }

                    const assetSearchInput = document.getElementById('assetSearchInput');
                    if (assetSearchInput) {
                        assetSearchInput.addEventListener('input', debounce(function() {
                        loadAssets(1);
                    }, 500));
                    }

                    const assetModalPerPageSelect = document.getElementById('assetModalPerPageSelect');
                    if (assetModalPerPageSelect) {
                        assetModalPerPageSelect.addEventListener('change', function() {
                        loadAssets(1);
                    });
                    }

                    const selectAssetsBtn = document.getElementById('selectAssetsBtn');
                    if (selectAssetsBtn) {
                        selectAssetsBtn.addEventListener('click', function() {
                        updateSelectedAssetsList();
                        closeModal(modals.assetSelection, modalContents.assetSelection);
                    });
                    }

                    const selectedAssetsPerPage = document.getElementById('selectedAssetsPerPage');
                    if (selectedAssetsPerPage) {
                        selectedAssetsPerPage.addEventListener('change', function() {
                        const selectedAssetsList = document.getElementById('selectedAssetsList');
                        if (selectedAssetsList) {
                            selectedAssetsList.setAttribute('data-current-page', '1');
                            updateSelectedAssetsList();
                        }
                    });
                    }

                    let selectedAssets = [];

                    function validateAsset(asset) {
                        if (!asset.id) {
                            console.error('Asset is missing ID:', asset);
                            return false;
                        }

                        const id = parseInt(asset.id, 10);
                        if (isNaN(id)) {
                            console.error('Asset has invalid ID:', asset.id);
                            return false;
                        }

                        return true;
                    }

                    function loadAssets(page = 1) {
                        const searchTerm = document.getElementById('assetSearchInput').value;
                        const limit = document.getElementById('assetModalPerPageSelect').value;
                        const selectedRadio = document.querySelector('input[name="permission_filter"]:checked');
                        const assetType = selectedRadio ? selectedRadio.getAttribute('data-asset-type') : 'medical';

                        document.getElementById('assetSelectionList').innerHTML = `
                            <tr>
                                <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Memuat aset...</td>
                            </tr>
                        `;

                        let url = `/assets?page=${page}&limit=${limit}&search=${encodeURIComponent(searchTerm)}`;

                        if (assetType) {
                            url += `&asset_type=${assetType}`;
                        }

                        fetch(url, {
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
                                const assets = data.assets || [];
                                if (assets.length === 0) {
                                    document.getElementById('assetSelectionList').innerHTML = `
                                        <tr>
                                            <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada aset    </td>
                                        </tr>
                                    `;
                                    return;
                                }

                                let html = '';
                                assets.forEach(asset => {
                                    if (asset.current_status === "dispose") {
                                        return;
                                    }

                                    const isSelected = selectedAssets.some(selectedAsset => selectedAsset.id === asset.asset_id);
                                    const assetName = asset.asset_master_name ||
                                                   (asset.asset_master && asset.asset_master.asset_name) ||
                                                   '-';
                                    const assetCode = asset.asset_code || '-';

                                    let assetType = 'Non Medical';
                                    if (asset.asset_master && asset.asset_master.asset_master_code) {
                                        const code = asset.asset_master.asset_master_code;
                                        if (code.startsWith('MED-')) {
                                            assetType = 'Medical';
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

                                const paginationData = data.assets_pagination || data.pagination || {};

                                setupAssetPagination(paginationData);

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

                    function attachCheckboxHandlers() {
                        const checkboxes = document.querySelectorAll('.asset-checkbox');

                        checkboxes.forEach(checkbox => {
                            const newCheckbox = checkbox.cloneNode(true);
                            checkbox.parentNode.replaceChild(newCheckbox, checkbox);
                        });

                        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const assetId = this.getAttribute('data-id');
                                if (this.checked) {
                                    const asset = {
                                        id: assetId,
                                        code: this.getAttribute('data-code'),
                                        name: this.getAttribute('data-name'),
                                        description: this.getAttribute('data-description'),
                                        type: this.getAttribute('data-type'),
                                        category: this.getAttribute('data-category')
                                    };

                                    if (validateAsset(asset) && !selectedAssets.some(a => a.id === assetId)) {
                                        selectedAssets.push(asset);
                                    }
                                } else {
                                    selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);
                                }
                            });
                        });

                        const selectAllAssets = document.getElementById('selectAllAssets');
                        if (selectAllAssets) {
                            const newSelectAll = selectAllAssets.cloneNode(true);
                            selectAllAssets.parentNode.replaceChild(newSelectAll, selectAllAssets);

                            document.getElementById('selectAllAssets').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('.asset-checkbox');
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = this.checked;
                                    checkbox.dispatchEvent(new Event('change'));
                                });
                            });
                        }
                    }

                    function setupAssetPagination(pagination) {
                        if (!pagination) return;

                        const paginationInfo = document.getElementById('assetModalPaginationInfo');
                        const paginationControls = document.getElementById('assetModalPaginationControls');

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

                    function updateSelectedAssetsList() {
                        const selectedAssetsList = document.getElementById('selectedAssetsList');

                        if (selectedAssets.length > 0) {
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

                            selectedAssetsList.setAttribute('data-current-page', currentPage);
                            selectedAssetsList.innerHTML = html;

                            let hiddenInputsHtml = '';
                            selectedAssets.forEach(asset => {
                                if (!html.includes(`name="asset_ids[]" value="${asset.id}"`)) {
                                    hiddenInputsHtml += `<input type="hidden" name="asset_ids[]" value="${asset.id}">`;
                                }
                            });

                            const hiddenInputsContainer = document.getElementById('hiddenAssetInputs') || document.createElement('div');
                            hiddenInputsContainer.id = 'hiddenAssetInputs';
                            hiddenInputsContainer.innerHTML = hiddenInputsHtml;
                            hiddenInputsContainer.style.display = 'none';

                            if (!document.getElementById('hiddenAssetInputs')) {
                                selectedAssetsList.parentNode.appendChild(hiddenInputsContainer);
                            }

                            updateSelectedAssetsPagination(currentPage, totalPages, selectedAssets.length);

                            document.querySelectorAll('.remove-asset').forEach(button => {
                                button.addEventListener('click', function() {
                                    const assetId = this.getAttribute('data-id');
                                    selectedAssets = selectedAssets.filter(asset => asset.id !== assetId);

                                    const newTotalPages = Math.ceil(selectedAssets.length / perPage);
                                    if (currentPage > newTotalPages && newTotalPages > 0) {
                                        selectedAssetsList.setAttribute('data-current-page', newTotalPages);
                                    }

                                    updateSelectedAssetsList();
                                });
                            });
                        } else {
                            selectedAssetsList.innerHTML = '<tr><td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada data yang tersedia dalam tabel</td></tr>';

                            const paginationContainer = document.getElementById('selectedAssetsPagination');
                            if (paginationContainer) {
                                paginationContainer.innerHTML = '';
                            }

                            const infoContainer = document.getElementById('selectedAssetsInfo');
                            if (infoContainer) {
                                infoContainer.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
                            }

                            const hiddenInputsContainer = document.getElementById('hiddenAssetInputs');
                            if (hiddenInputsContainer) {
                                hiddenInputsContainer.innerHTML = '';
                            }
                        }
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
                            link.addEventListener('click', function(e) {
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

                    let allVendors = [];
                    const vendorSearchInput = document.getElementById('vendor_search');
                    const vendorIdInput = document.getElementById('vendor_id');
                    const vendorResults = document.getElementById('vendor_results');

                    const editVendorSearchInput = document.getElementById('edit_vendor_search');
                    const editVendorIdInput = document.getElementById('edit_vendor_id');
                    const editVendorResults = document.getElementById('edit_vendor_results');

                    // Variables for vendor lazy loading
                    let vendorPage = 1;
                    let isLoadingVendors = false;
                    let hasMoreVendors = true;
                    let currentVendorSearch = '';

                    // Variables for edit vendor lazy loading
                    let editVendorPage = 1;
                    let isLoadingEditVendors = false;
                    let hasMoreEditVendors = true;
                    let currentEditVendorSearch = '';

                    vendorSearchInput?.addEventListener('focus', function() {
                        filterAndDisplayVendors(this.value.trim(), 'add');
                        vendorResults.style.display = 'block';
                    });

                    editVendorSearchInput?.addEventListener('focus', function() {
                        filterAndDisplayVendors(this.value.trim(), 'edit');
                        editVendorResults.style.display = 'block';
                    });

                    document.addEventListener('click', function(e) {
                        if (vendorSearchInput && vendorResults) {
                        if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                            vendorResults.style.display = 'none';
                        }
                        }

                        if (editVendorSearchInput && editVendorResults) {
                        if (e.target !== editVendorSearchInput && !editVendorResults.contains(e.target)) {
                            editVendorResults.style.display = 'none';
                            }
                        }
                    });

                    vendorSearchInput?.addEventListener('input', debounce(function() {
                        const searchTerm = this.value.trim();
                        filterAndDisplayVendors(searchTerm, 'add');
                    }, 300));

                    editVendorSearchInput?.addEventListener('input', debounce(function() {
                        const searchTerm = this.value.trim();
                        filterAndDisplayVendors(searchTerm, 'edit');
                    }, 300));

                    function filterAndDisplayVendors(searchTerm, mode = 'add') {
                        const resultsElem = mode === 'add' ? vendorResults : editVendorResults;

                        if (resultsElem) resultsElem.style.display = 'block';

                        if (mode === 'add') {
                            vendorPage = 1;
                            hasMoreVendors = true;
                            currentVendorSearch = searchTerm;
                        } else {
                            editVendorPage = 1;
                            hasMoreEditVendors = true;
                            currentEditVendorSearch = searchTerm;
                        }

                        fetchVendors(searchTerm, mode, mode === 'add' ? vendorPage : editVendorPage, false);
                    }

                    function fetchVendors(searchTerm = '', mode = 'add', page = 1, append = false) {
                        const resultsElem = mode === 'add' ? vendorResults : editVendorResults;
                        const searchInputElem = mode === 'add' ? vendorSearchInput : editVendorSearchInput;
                        const idInputElem = mode === 'add' ? vendorIdInput : editVendorIdInput;

                        if (mode === 'add') {
                            if (isLoadingVendors) return;
                            isLoadingVendors = true;
                        } else {
                            if (isLoadingEditVendors) return;
                            isLoadingEditVendors = true;
                        }

                        if (!append && resultsElem && resultsElem.style.display === 'block') {
                        resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                        }

                        let queryParams = new URLSearchParams();
                        queryParams.append('json', 'true');
                        queryParams.append('limit', '20');
                        queryParams.append('page', page.toString());

                        if (searchTerm) {
                            queryParams.append('search', searchTerm);
                        }

                        const url = `/vendors?${queryParams.toString()}`;

                        fetch(url, {
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

                            // Cache all vendors on first load
                            if (!searchTerm && page === 1 && mode === 'add') {
                                allVendors = vendors;
                                try {
                                    localStorage.setItem('allVendors', JSON.stringify(allVendors));
                                } catch (e) {
                                    console.error('Error caching vendors:', e);
                                }
                            }

                            if (mode === 'add') {
                                hasMoreVendors = vendors.length === 20;
                                isLoadingVendors = false;
                            } else {
                                hasMoreEditVendors = vendors.length === 20;
                                isLoadingEditVendors = false;
                            }

                            displayVendorResults(vendors, resultsElem, idInputElem, searchInputElem, append);
                        })
                        .catch(error => {
                            console.error('Error fetching vendors:', error);

                            if (resultsElem && resultsElem.style.display === 'block' && !append) {
                            resultsElem.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';
                            }

                            if (typeof error === 'object' && error !== null) {
                                showToast(error, 'error');
                            } else {
                                showToast('Gagal memuat vendor: ' + error.message, 'error');
                            }

                            if (mode === 'add') {
                                isLoadingVendors = false;
                            } else {
                                isLoadingEditVendors = false;
                            }
                        });
                    }

                    function displayVendorResults(vendors, resultsElem, idInputElem, searchInputElem, append = false) {
                        if (!append) {
                        resultsElem.innerHTML = '';
                        } else {
                            const loadingIndicator = resultsElem.querySelector('.vendor-loading-indicator');
                            if (loadingIndicator) {
                                loadingIndicator.remove();
                            }
                        }

                        if (vendors.length === 0 && !append) {
                            resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Vendor tidak ditemukan</div>';
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

                            div.addEventListener('click', function() {
                                idInputElem.value = this.getAttribute('data-id');
                                searchInputElem.value = this.textContent;
                                resultsElem.style.display = 'none';
                            });

                            resultsElem.appendChild(div);
                        });

                        const isMainForm = resultsElem === vendorResults;
                        const hasMore = isMainForm ? hasMoreVendors : hasMoreEditVendors;

                        if (hasMore) {
                            const loadingDiv = document.createElement('div');
                            loadingDiv.className = 'p-2 text-xs text-gray-500 text-center border-t vendor-loading-indicator';
                            loadingDiv.textContent = 'memuat lebih lanjut...';
                            resultsElem.appendChild(loadingDiv);
                        }
                    }

                    if (vendorResults) {
                        vendorResults.addEventListener('scroll', function() {
                            if (!hasMoreVendors || isLoadingVendors) return;

                            if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                                vendorPage++;
                                fetchVendors(currentVendorSearch, 'add', vendorPage, true);
                            }
                        });
                    }

                    if (editVendorResults) {
                        editVendorResults.addEventListener('scroll', function() {
                            if (!hasMoreEditVendors || isLoadingEditVendors) return;

                            if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                                editVendorPage++;
                                fetchVendors(currentEditVendorSearch, 'edit', editVendorPage, true);
                            }
                        });
                    }

                    function loadAllVendors() {
                        const cachedVendors = localStorage.getItem('allVendors');
                        if (cachedVendors) {
                            try {
                                allVendors = JSON.parse(cachedVendors);
                            } catch (e) {
                                console.error('Error parsing cached vendors:', e);
                            }
                        }
                    }

                    loadAllVendors();

                    let userPage = 1;
                    let isLoadingUsers = false;
                    let hasMoreUsers = true;
                    let currentUserSearch = '';

                    let editUserPage = 1;
                    let isLoadingEditUsers = false;
                    let hasMoreEditUsers = true;
                    let currentEditUserSearch = '';

                    initUserSearch('user_search', 'user_dropdown', 'user_list', 'user_loading', 'selected_user_id');
                    initUserSearch('edit_user_search', 'edit_user_dropdown', 'edit_user_list', 'edit_user_loading', 'edit_assigned_to');

                    function initUserSearch(searchInputId, dropdownId, userListId, loadingIndicatorId, selectedUserIdId) {
                        const searchInput = document.getElementById(searchInputId);
                        const dropdown = document.getElementById(dropdownId);
                        const userList = document.getElementById(userListId);
                        const loadingIndicator = document.getElementById(loadingIndicatorId);
                        const selectedUserId = document.getElementById(selectedUserIdId);

                        if (!searchInput || !dropdown || !userList) return;

                        searchInput.addEventListener('focus', function() {
                            dropdown.classList.remove('hidden');

                            if (searchInputId === 'user_search') {
                                userPage = 1;
                                hasMoreUsers = true;
                                currentUserSearch = '';
                            } else {
                                editUserPage = 1;
                                hasMoreEditUsers = true;
                                currentEditUserSearch = '';
                            }

                            loadUsers('', searchInputId === 'user_search' ? 1 : 1, false);
                        });

                        document.addEventListener('click', function(e) {
                            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                                dropdown.classList.add('hidden');
                            }
                        });

                        const debouncedSearch = debounce(function(e) {
                            const searchTerm = e.target.value;
                            const isEditMode = searchInputId === 'edit_user_search';

                            if (isEditMode) {
                                editUserPage = 1;
                                hasMoreEditUsers = true;
                                currentEditUserSearch = searchTerm;
                            } else {
                                userPage = 1;
                                hasMoreUsers = true;
                                currentUserSearch = searchTerm;
                            }

                            loadUsers(searchTerm, isEditMode ? editUserPage : userPage, false);
                        }, 300);

                        searchInput.addEventListener('input', debouncedSearch);

                        const isEditMode = searchInputId === 'edit_user_search';
                        const permissionRadios = document.querySelectorAll(isEditMode ? '.edit-permission-radio' : '.permission-radio');
                        permissionRadios.forEach(radio => {
                            radio.addEventListener('change', function() {
                                if (isEditMode) {
                                    editUserPage = 1;
                                    hasMoreEditUsers = true;
                                } else {
                                    userPage = 1;
                                    hasMoreUsers = true;
                                }

                                userList.innerHTML = '';
                                loadUsers(searchInput.value, isEditMode ? editUserPage : userPage, false);
                            });
                        });

                        dropdown.addEventListener('scroll', function() {
                            const isEditMode = searchInputId === 'edit_user_search';
                            const hasMore = isEditMode ? hasMoreEditUsers : hasMoreUsers;
                            const isLoading = isEditMode ? isLoadingEditUsers : isLoadingUsers;

                            if (!hasMore || isLoading) return;

                            if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                                if (isEditMode) {
                                    editUserPage++;
                                    loadUsers(currentEditUserSearch, editUserPage, true);
                                } else {
                                    userPage++;
                                    loadUsers(currentUserSearch, userPage, true);
                                }
                            }
                        });

                    function loadUsers(searchTerm, page = 1, append = false) {
                        const isEditMode = searchInputId === 'edit_user_search';

                        if (isEditMode) {
                            if (isLoadingEditUsers) return;
                            isLoadingEditUsers = true;
                        } else {
                            if (isLoadingUsers) return;
                            isLoadingUsers = true;
                        }

                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');

                        if (!append) {
                            userList.innerHTML = '';
                        const searchingMsg = document.createElement('li');
                        searchingMsg.className = 'px-4 py-2 text-blue-500 text-center';
                        searchingMsg.textContent = searchTerm ? `Mencari "${searchTerm}"...` : 'Memuat pengguna...';
                        userList.appendChild(searchingMsg);
                        } else {
                            const loadingItem = document.createElement('li');
                            loadingItem.className = 'px-4 py-2 text-blue-500 text-center user-loading-indicator';
                            loadingItem.textContent = 'Memuat lebih banyak...';
                            userList.appendChild(loadingItem);
                        }

                        let queryParams = new URLSearchParams();
                        if (searchTerm) {
                            queryParams.append('search', searchTerm);
                        }
                        queryParams.append('limit', 10);
                        queryParams.append('page', page);

                            const selectedPermission = document.querySelector(
                                isEditMode ? 'input[name="edit_permission_filter"]:checked' : 'input[name="permission_filter"]:checked'
                            )?.value;

                            if (!selectedPermission) {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                            userList.innerHTML = '';
                            const noPermission = document.createElement('li');
                            noPermission.className = 'px-4 py-2 text-red-500';
                                noPermission.textContent = 'Silakan pilih jenis izin pengguna terlebih dahulu';
                            userList.appendChild(noPermission);

                            if (isEditMode) {
                                isLoadingEditUsers = false;
                            } else {
                                isLoadingUsers = false;
                            }
                            return;
                        }

                            fetch(`/user/by-permission/${selectedPermission}?${queryParams.toString()}`, {
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
                                let users = [];
                                if (Array.isArray(data)) {
                                    users = data;
                                } else if (data.users && Array.isArray(data.users)) {
                                    users = data.users;
                                } else if (data.data && Array.isArray(data.data)) {
                                    users = data.data;
                                }

                            if (append) {
                                const loadingIndicator = userList.querySelector('.user-loading-indicator');
                                if (loadingIndicator) {
                                    loadingIndicator.remove();
                                }
                            } else {
                            userList.innerHTML = '';
                            }

                            if (isEditMode) {
                                hasMoreEditUsers = users.length === 10;
                            } else {
                                hasMoreUsers = users.length === 10;
                            }

                            if (users.length === 0 && !append) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                    noResults.textContent = 'Tidak ada pengguna ditemukan dengan izin yang dipilih';
                                userList.appendChild(noResults);
                            } else {
                                    users.sort((a, b) => {
                                    if (a.employee_name && b.employee_name) {
                                        return a.employee_name.localeCompare(b.employee_name);
                                    } else if (a.name && b.name) {
                                        return a.name.localeCompare(b.name);
                                    }
                                    return 0;
                                });

                                    users.forEach(user => {
                                    const li = document.createElement('li');
                                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                    let displayText = '';
                                    if (user.employee_name) {
                                        displayText = user.employee_name;
                                        if (user.name) {
                                            displayText += ` - ${user.name}`;
                                        }
                                    } else {
                                        displayText = user.name || `User ID: ${user.user_id}`;
                                    }

                                    li.textContent = displayText;
                                    li.setAttribute('data-id', user.user_id);
                                    li.setAttribute('data-employee-number', user.employee_name || '');

                                    li.addEventListener('click', function() {
                                        selectedUserId.value = this.getAttribute('data-id');

                                        const employeeNumber = this.getAttribute('data-employee-number');
                                        if (employeeNumber) {
                                            searchInput.value = employeeNumber;
                                        } else {
                                            searchInput.value = this.textContent;
                                        }

                                        dropdown.classList.add('hidden');
                                    });

                                    userList.appendChild(li);
                                });

                                if ((isEditMode && hasMoreEditUsers) || (!isEditMode && hasMoreUsers)) {
                                const countDiv = document.createElement('li');
                                countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t';
                                    let permissionText = selectedPermission === 'maintenance-report:medical' ?
                                        'izin pemeliharaan medis' : 'izin pemeliharaan non-medis';

                                    countDiv.textContent = `Scroll untuk memuat lebih banyak pengguna dengan ${permissionText}`;
                                userList.appendChild(countDiv);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading users with permissions:', error);

                            if (append) {
                                const loadingIndicator = userList.querySelector('.user-loading-indicator');
                                if (loadingIndicator) {
                                    loadingIndicator.remove();
                                }
                            } else {
                                userList.innerHTML = '';
                            }

                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';

                                const permissionText = document.querySelector(
                                    isEditMode ? 'input[name="edit_permission_filter"]:checked' : 'input[name="permission_filter"]:checked'
                                )?.value === 'maintenance-report:medical' ?
                                    'izin pemeliharaan medis' : 'izin pemeliharaan non-medis';

                                errorItem.textContent = `Gagal memuat data pengguna dengan ${permissionText}`;
                            userList.appendChild(errorItem);
                        })
                        .finally(() => {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                            if (isEditMode) {
                                isLoadingEditUsers = false;
                            } else {
                                isLoadingUsers = false;
                            }
                        });
                        }
                    }

                    document.getElementById('addMaintenanceForm')?.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const intervalField = document.getElementById('interval');
                        const startDateField = document.getElementById('start_date');
                        const endDateField = document.getElementById('end_date');
                        const userSearchField = document.getElementById('user_search');

                        const isIntervalValid = validateField(intervalField);
                        const isStartDateValid = validateField(startDateField);

                        let isEndDateValid = true;
                        if (intervalField.value !== 'ONCE' && intervalField.value !== 'DAILY') {
                            isEndDateValid = validateField(endDateField);
                        }

                        const isUserValid = validateField(userSearchField);

                        let isAssetsValid = true;
                        if (selectedAssets.length === 0) {
                            isAssetsValid = false;
                            showToast('Silakan pilih setidaknya satu aset', 'error');
                        }

                        if (!isIntervalValid || !isStartDateValid || !isEndDateValid || !isUserValid || !isAssetsValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');

                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                resetButton(submitBtn);
                            }

                            return;
                        }

                        const formData = new FormData(this);
                        const jsonData = {};

                        for (const [key, value] of formData.entries()) {
                            if (key !== 'asset_ids[]') {
                                jsonData[key] = value;
                            }
                        }

                        if (jsonData.assigned_to) {
                            jsonData.assigned_to = parseInt(jsonData.assigned_to, 10);
                        }

                        if (jsonData.vendor_id) {
                            jsonData.vendor_id = parseInt(jsonData.vendor_id, 10);
                        } else {
                            delete jsonData.vendor_id;
                        }

                        if (jsonData.interval === 'ONCE' || jsonData.interval === 'DAILY') {
                            delete jsonData.end_date;
                        }

                        jsonData.asset_ids = selectedAssets.map(asset => {
                            const assetId = parseInt(asset.id, 10);
                            if (isNaN(assetId)) {
                                console.error('Invalid asset ID:', asset.id);
                                throw new Error('Invalid asset ID: ' + asset.id);
                            }
                            return assetId;
                        });

                        if (!Array.isArray(jsonData.asset_ids) || jsonData.asset_ids.length === 0) {
                            showToast('Error: Tidak ada ID aset yang valid untuk dikirim', 'error');
                            return;
                        }

                        try {
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
                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            if (data.success) {
                                showToast(data.message || 'Jadwal pemeliharaan berhasil dibuat', 'success');
                                closeModal(modals.add, modalContents.add);

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                const submitBtn = this.querySelector('button[type="submit"]');
                                resetButton(submitBtn);

                                if (data.errors) {
                                    showToast(data.errors, 'error');
                                } else {
                                    showToast('Gagal membuat jadwal pemeliharaan', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error creating maintenance schedule:', error);

                            const submitBtn = this.querySelector('button[type="submit"]');
                            resetButton(submitBtn);

                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            if (error && error.errors) {
                                if (Array.isArray(error.errors)) {
                                    showToast(error.errors, 'error');
                                } else {
                                    showToast( error.errors, 'error');
                                }
                            } else if (error && error.status === 422) {
                                showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                            } else {
                                showToast(error.message || 'Gagal membuat jadwal pemeliharaan', 'error');
                            }
                        });
                        } catch (error) {
                            console.error('Error handling maintenance form submission:', error);
                            showToast('Gagal memproses formulir', 'error');
                        }
                    });

                    const exportBtn = document.getElementById('exportBtn');
                    if (exportBtn) {
                        exportBtn.addEventListener('click', () => {
                        const url = new URL(window.location.href);
                        const searchParams = url.searchParams;

                        const exportUrl = "{{ route('maintenance.export.pdf') }}?" + searchParams.toString();

                        window.open(exportUrl, '_blank');
                    });
                    }

                    document.querySelectorAll('.delete-maintenance-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const maintenanceId = this.getAttribute('data-id');
                            const deleteMaintenanceName = document.getElementById('deleteMaintenanceName');

                            if (deleteMaintenanceName && document.getElementById('deleteMaintenanceForm')) {
                            document.getElementById('deleteMaintenanceForm').setAttribute('data-id', maintenanceId);

                            const assetName = this.closest('tr').querySelector('td:nth-child(1) .font-medium').textContent;
                            const assetCode = this.closest('tr').querySelector('td:nth-child(1) .text-gray-500').textContent;

                            deleteMaintenanceName.textContent = `${assetName} (${assetCode.replace('Kode: ', '')})`;

                            openModal(modals.delete, modalContents.delete);
                            }
                        });
                    });

                    const deleteMaintenanceForm = document.getElementById('deleteMaintenanceForm');
                    if (deleteMaintenanceForm) {
                        deleteMaintenanceForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const maintenanceId = this.getAttribute('data-id');

                        if (!maintenanceId) {
                            showToast('ID pemeliharaan tidak ada', 'error');

                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = 'Hapus';
                            }

                            return;
                        }

                        fetch(`/maintenance/${maintenanceId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(handleApiResponse)
                        .then(data => {
                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            if (data.success) {
                                closeModal(modals.delete, modalContents.delete);
                                showToast(data.message || 'Rekaman pemeliharaan berhasil dihapus', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                const submitBtn = this.querySelector('button[type="submit"]');
                                resetButton(submitBtn);

                                showToast(data.message || 'Gagal menghapus rekaman pemeliharaan', 'error');

                                return;
                            }
                        })
                        .catch(error => {
                            console.error('Delete request failed:', error);

                            const submitBtn = this.querySelector('button[type="submit"]');
                            resetButton(submitBtn);

                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            if (error && error.errors) {
                                if (Array.isArray(error.errors)) {
                                    showToast(error.errors, 'error');
                                } else {
                                    showToast(error.errors, 'error');
                                }
                            } else if (error && error.status === 422) {
                                showToast(`Validasi gagal: ${error.message || 'Silakan periksa form Anda'}`, 'error');
                            } else {
                                showToast(error.message || 'Gagal menghapus rekaman pemeliharaan', 'error');
                            }
                        });
                    });
                    }

                    document.querySelectorAll('.edit-maintenance-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const maintenanceId = this.getAttribute('data-id');
                            const assetName = this.closest('tr').querySelector('td:nth-child(1) .font-medium').textContent;
                            const assetCode = this.closest('tr').querySelector('td:nth-child(1) .text-gray-500').textContent.replace('Kode: ', '');

                            const editAssetName = document.getElementById('edit_asset_name');
                            const editAssetCode = document.getElementById('edit_asset_code');
                            const editMaintenanceForm = document.getElementById('editMaintenanceForm');

                            if (editAssetName && editAssetCode && editMaintenanceForm) {
                                editAssetName.textContent = assetName;
                                editAssetCode.textContent = assetCode;
                                editMaintenanceForm.reset();

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

                                    editMaintenanceForm.dataset.originalData = JSON.stringify(maintenance);

                                    const editMaintenanceId = document.getElementById('edit_maintenance_id');
                                    const editStartDate = document.getElementById('edit_start_date');
                                    const editEndDate = document.getElementById('edit_end_date');
                                    const editInterval = document.getElementById('edit_interval');
                                    const editAssignedTo = document.getElementById('edit_assigned_to');
                                    const editUserSearch = document.getElementById('edit_user_search');
                                    const editVendorId = document.getElementById('edit_vendor_id');
                                    const editVendorSearch = document.getElementById('edit_vendor_search');

                                    if (editMaintenanceId) editMaintenanceId.value = maintenance.id;

                                    if (maintenance.start_date && editStartDate) {
                                        // Get the flatpickr instance for start date
                                        const startDatePicker = editStartDate._flatpickr;
                                        if (startDatePicker) {
                                            try {
                                                // Parse the date string from the server
                                                const startDate = new Date(maintenance.start_date);
                                                // Format as YYYY-MM-DD
                                                const formattedStartDate = startDate.getFullYear() + '-' +
                                                    String(startDate.getMonth() + 1).padStart(2, '0') + '-' +
                                                    String(startDate.getDate()).padStart(2, '0');
                                                startDatePicker.setDate(formattedStartDate);
                                            } catch (error) {
                                                console.error('Error setting start date:', error);
                                            }
                                        }
                                    }

                                    if (maintenance.end_date && editEndDate) {
                                        // Get the flatpickr instance for end date
                                        const endDatePicker = editEndDate._flatpickr;
                                        if (endDatePicker) {
                                            try {
                                                // Parse the date string from the server
                                                const endDate = new Date(maintenance.end_date);
                                                // Format as YYYY-MM-DD
                                                const formattedEndDate = endDate.getFullYear() + '-' +
                                                    String(endDate.getMonth() + 1).padStart(2, '0') + '-' +
                                                    String(endDate.getDate()).padStart(2, '0');
                                                endDatePicker.setDate(formattedEndDate);
                                            } catch (error) {
                                                console.error('Error setting end date:', error);
                                                endDatePicker.setDate(formattedEndDate, true, "Y-m-d");
                                            }
                                        }
                                    }

                                    if (maintenance.interval && editInterval) {
                                        editInterval.value = maintenance.interval;
                                    toggleEndDateVisibility(maintenance.interval, 'edit');
                                }

                                    if (maintenance.assigned_to && editAssignedTo && editUserSearch) {
                                        editAssignedTo.value = maintenance.assigned_to;

                                    if (maintenance.assigned_to_employee_name) {
                                            editUserSearch.value = maintenance.assigned_to_employee_name;
                                    } else if (maintenance.employee_name) {
                                            editUserSearch.value = maintenance.employee_name;
                                    } else if (maintenance.employee_name) {
                                            editUserSearch.value = maintenance.employee_name;
                                    } else {
                                            editUserSearch.value = `User ID: ${maintenance.assigned_to}`;
                                        }
                                    }

                                    if (maintenance.vendor_id && maintenance.vendor_name && editVendorId && editVendorSearch) {
                                        editVendorId.value = maintenance.vendor_id;
                                        editVendorSearch.value = maintenance.vendor_name;
                                    } else if (editVendorId && editVendorSearch) {
                                        editVendorId.value = '';
                                        editVendorSearch.value = '';
                                }

                                openModal(modals.edit, modalContents.edit);
                            })
                            .catch(error => {
                                console.error('Error fetching maintenance details:', error);
                                showToast(error.message || 'Gagal mengambil detail pemeliharaan', 'error');
                            });
                        }
                        });
                    });

                    const editMaintenanceForm = document.getElementById('editMaintenanceForm');
                    if (editMaintenanceForm) {
                        editMaintenanceForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const maintenanceId = document.getElementById('edit_maintenance_id').value;
                        if (!maintenanceId) {
                            showToast('ID pemeliharaan tidak ada', 'error');

                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = 'Simpan Perubahan';
                            }

                            return;
                        }

                        const intervalField = document.getElementById('edit_interval');
                        const startDateField = document.getElementById('edit_start_date');
                        const endDateField = document.getElementById('edit_end_date');
                        const userSearchField = document.getElementById('edit_user_search');

                        const isIntervalValid = validateField(intervalField);
                        const isStartDateValid = validateField(startDateField);

                        let isEndDateValid = true;
                        if (intervalField.value !== 'ONCE' && intervalField.value !== 'DAILY') {
                            isEndDateValid = validateField(endDateField);
                        }

                        const isUserValid = validateField(userSearchField);

                        if (!isIntervalValid || !isStartDateValid || !isEndDateValid || !isUserValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');

                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = 'Simpan Perubahan';
                            }

                            return;
                        }

                        const formData = {};

                        const originalData = JSON.parse(this.dataset.originalData || '{}');

                        const formatDate = (dateString) => {
                            if (!dateString) return '';
                            const date = new Date(dateString);
                            return date.toISOString().split('T')[0];
                        };

                        if (intervalField.value !== originalData.interval) {
                            formData.interval = intervalField.value;
                        }

                        const originalStartDate = formatDate(originalData.start_date);
                        if (startDateField.value !== originalStartDate) {
                            formData.start_date = startDateField.value;
                        }

                        if (intervalField.value !== 'ONCE' && intervalField.value !== 'DAILY') {
                            const originalEndDate = formatDate(originalData.end_date);
                            if (endDateField.value !== originalEndDate) {
                                formData.end_date = endDateField.value;
                            }
                        }

                        const assignedTo = parseInt(document.getElementById('edit_assigned_to').value, 10);
                        if (assignedTo !== originalData.assigned_to) {
                            formData.assigned_to = assignedTo;
                        }

                        const vendorId = document.getElementById('edit_vendor_id').value ?
                            parseInt(document.getElementById('edit_vendor_id').value, 10) : null;
                        const originalVendorId = originalData.vendor_id || null;

                        if (vendorId !== originalVendorId) {
                            formData.vendor_id = vendorId;
                        }

                        if (Object.keys(formData).length === 0) {
                            closeModal(modals.edit, modalContents.edit);
                            showToast('Tidak ada perubahan yang dilakukan', 'info');

                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = 'Simpan Perubahan';
                            }
                            return;
                        }

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
                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            closeModal(modals.edit, modalContents.edit);

                            if (data.success) {
                                showToast(data.message || 'Rekaman pemeliharaan berhasil diperbarui', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                const submitBtn = this.querySelector('button[type="submit"]');
                                resetButton(submitBtn);

                                showToast(data.message || 'Gagal memperbarui rekaman pemeliharaan', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Update request failed:', error);

                            const submitBtn = this.querySelector('button[type="submit"]');
                            resetButton(submitBtn);

                            if (this.dataset.safetyTimeoutId) {
                                clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                            }

                            if (error && error.errors) {
                                if (Array.isArray(error.errors)) {
                                    showToast(error.errors, 'error');
                                } else {
                                    showToast(error.errors, 'error');
                                }
                            } else if (error && error.status === 422) {
                                showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                            } else {
                                showToast(error.message || 'Gagal memperbarui rekaman pemeliharaan', 'error');
                            }
                        });
                    });
                    }

                    @php
                        echo "const currentLoggedInUserId = " . json_encode(session('user_id')) . ";";
                    @endphp

                    document.querySelectorAll('.create-report-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const maintenanceId = this.getAttribute('data-id');
                            let assetName = this.getAttribute('data-asset-name');
                            let assetCode = this.getAttribute('data-asset-code');
                            const status = this.getAttribute('data-status') || 'new';

                            const assignedUserId = this.getAttribute('data-assigned-to');

                            if (!currentLoggedInUserId || !assignedUserId || currentLoggedInUserId != assignedUserId) {
                                showToast('Akses ditolak. Hanya petugas yang ditugaskan yang dapat membuat laporan pemeliharaan.', 'error');
                                return;
                            }

                            button.setAttribute('data-original-html', button.innerHTML);
                            button.disabled = true;
                            button.innerHTML = `<div class="inline-block w-4 h-4 border-2 border-green-500 border-t-transparent rounded-full animate-spin mr-1"></div>`;

                            const safetyTimeout = setTimeout(() => {
                                resetReportButton();
                            }, 10000);

                            const resetReportButton = () => {
                                const originalHtml = button.getAttribute('data-original-html');
                                if (originalHtml) {
                                    button.innerHTML = originalHtml;
                                }
                                button.disabled = false;
                                clearTimeout(safetyTimeout);
                            };

                            if (status === 'new') {
                                fetch(`/maintenance/${maintenanceId}/start`, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Gagal memulai pemeliharaan');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        const statusCell = button.closest('tr').querySelector('td:nth-child(7) span');
                                        if (statusCell) {
                                            statusCell.textContent = 'Dalam Proses';
                                            statusCell.className = 'px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800';
                                        }

                                        button.setAttribute('data-status', 'in progress');
                                        fetchMaintenanceDetails();
                                    } else {
                                        throw new Error(data.message || 'Gagal memulai pemeliharaan');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error starting maintenance:', error);
                                    resetReportButton();
                                    showToast(error.message || 'Gagal memulai pemeliharaan', 'error');
                                });
                            } else {
                                fetchMaintenanceDetails();
                            }

                            function fetchMaintenanceDetails() {

                            fetch(`/maintenance/${maintenanceId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    resetReportButton();
                                    throw new Error('Gagal mengambil detail pemeliharaan');
                                }
                                return response.json();
                            })
                            .then(result => {
                                resetReportButton();

                                if (!result.success) {
                                    throw new Error(result.message || 'Gagal mengambil detail pemeliharaan');
                                }

                                const maintenance = result.data;
                                if (status === 'new') {
                                    maintenance.status = 'in progress';
                                }

                                const asset = maintenance.asset || {};

                                assetName = maintenance.asset_name || asset.asset_name || asset.name ||
                                          assetName || this.closest('tr')?.querySelector('td:nth-child(1) .font-medium')?.textContent || '-';

                                assetCode = maintenance.asset_code || asset.asset_code || asset.code ||
                                          assetCode || this.closest('tr')?.querySelector('td:nth-child(1) .text-gray-500')?.textContent.replace('Kode: ', '') || '-';

                                const serialNumber = maintenance.serial_number || asset.serial_number || '-';
                                const brandName = maintenance.brand_name || asset.brand_name || asset.brand || '-';
                                const modelName = maintenance.model || asset.model || '-';

                                const locationData = maintenance.location || asset.location || {};

                                const reportMaintenanceId = document.getElementById('report_maintenance_id');
                                const maintenanceDate = document.getElementById('maintenance_date');
                                const createReportForm = document.getElementById('createReportForm');
                                const imagePreview = document.getElementById('image-preview');
                                const assetImage = document.getElementById('maintenance_asset_image');

                                if (reportMaintenanceId && maintenanceDate && createReportForm && imagePreview) {
                                    reportMaintenanceId.value = maintenanceId;

                                    createReportForm.reset();

                                    const today = new Date().toISOString().split('T')[0];
                                    maintenanceDate.value = today;

                                    imagePreview.classList.add('hidden');

                                    reportMaintenanceId.value = maintenanceId;

                                    document.getElementById('report_asset_code_display').value = assetCode;
                                    document.getElementById('report_asset_name_display').value = assetName;
                                    document.getElementById('report_serial_number_display').value = serialNumber;
                                    document.getElementById('report_brand_name_display').value = brandName;
                                    document.getElementById('report_model_display').value = modelName;

                                    let locationText = '-';
                                    if (locationData && typeof locationData === 'object') {
                                        const locationParts = [];
                                        if (locationData.room_name) locationParts.push(locationData.room_name);
                                        if (locationData.floor_number) locationParts.push('Lantai ' + locationData.floor_number);
                                        if (locationData.building_name) locationParts.push(locationData.building_name);
                                        if (locationParts.length > 0) {
                                            locationText = locationParts.join(' | ');
                                        }
                                    }
                                    document.getElementById('report_location_display').value = locationText;

                                    const formatDate = (dateString) => {
                                        if (!dateString) return '-';
                                        const date = new Date(dateString);
                                        return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                                    };

                                    document.getElementById('report_start_date_display').value = formatDate(maintenance.start_date);
                                    document.getElementById('report_end_date_display').value = formatDate(maintenance.end_date);

                                    let intervalText = '-';
                                    const interval = maintenance.interval;
                                    if (interval === 'ONCE') intervalText = 'Sekali';
                                    else if (interval === 'DAILY') intervalText = 'Harian';
                                    else if (interval === 'WEEKLY') intervalText = 'Mingguan';
                                    else if (interval === '2 WEEKS') intervalText = '2 Minggu';
                                    else if (interval === 'MONTHLY') intervalText = 'Bulanan';
                                    else if (interval === '2 MONTHS') intervalText = '2 Bulan';
                                    else if (interval === '3 MONTHS') intervalText = '3 Bulan';
                                    else if (interval === '4 MONTHS') intervalText = '4 Bulan';
                                    else if (interval === '6 MONTHS') intervalText = '6 Bulan';
                                    else if (interval === 'YEARLY') intervalText = 'Tahunan';

                                    document.getElementById('report_interval_display').value = intervalText;

                                    document.getElementById('report_vendor_display').value = maintenance.vendor_name || '-';

                                    if (assetImage) {
                                        const imagePath = maintenance.asset_image_path ||
                                                        asset.image_path ||
                                                        asset.asset_image_path ||
                                                        maintenance.image_path || null;

                                        if (imagePath) {
                                            assetImage.src = imagePath.startsWith('http')
                                                ? imagePath
                                                : "{{ config('app.backend_url') }}/public" + imagePath;

                                            assetImage.onerror = function() {
                                                this.onerror = null;
                                                this.src = "{{ asset('images/placeholder.png') }}";
                                            };
                                        } else {
                                            assetImage.src = "{{ asset('images/placeholder.png') }}";
                                        }
                                    }

                                    openModal(modals.report, modalContents.report);
                                } else {
                                    showToast('Beberapa elemen form tidak ditemukan', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching maintenance details:', error);

                                resetReportButton();

                                showToast(error.message || 'Gagal mengambil detail pemeliharaan', 'error');
                            });
                        }
                        });
                    });

                    const createReportForm = document.getElementById('createReportForm');
                    if (createReportForm) {
                        createReportForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const maintenanceId = document.getElementById('report_maintenance_id').value;
                            const description = document.getElementById('description');
                            const maintenanceDate = document.getElementById('maintenance_date');

                            if (!maintenanceId) {
                                showToast('ID pemeliharaan tidak ada', 'error');

                                const submitBtn = this.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                    submitBtn.innerHTML = 'Kirim Laporan';
                                }

                                return;
                            }

                            const isDateValid = validateField(maintenanceDate);
                            const isDescriptionValid = validateField(description);

                            if (!isDateValid || !isDescriptionValid) {
                                showToast('Silakan isi semua field yang diperlukan', 'error');

                                const submitBtn = this.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                    submitBtn.innerHTML = 'Kirim Laporan';
                                }

                                return;
                            }

                            const formData = new FormData(this);

                            fetch('/maintenance/reports', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                }
                            })
                            .then(handleApiResponse)
                            .then(data => {
                                if (this.dataset.safetyTimeoutId) {
                                    clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                                }

                                closeModal(modals.report, modalContents.report);

                                if (data.success) {
                                    showToast(data.message || 'Laporan pemeliharaan berhasil dibuat', 'success');

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1500);
                                } else {
                                    const submitBtn = this.querySelector('button[type="submit"]');
                                    resetButton(submitBtn);

                                    showToast(data.message || 'Gagal membuat laporan pemeliharaan', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error creating maintenance report:', error);

                                const submitBtn = this.querySelector('button[type="submit"]');
                                resetButton(submitBtn);

                                if (this.dataset.safetyTimeoutId) {
                                    clearTimeout(parseInt(this.dataset.safetyTimeoutId));
                                }

                                if (error && error.errors) {
                                    if (Array.isArray(error.errors)) {
                                        showToast(error.errors, 'error');
                                    } else {
                                        showToast(error.errors, 'error');
                                    }
                                } else if (error && error.status === 422) {
                                    showToast(`Validasi gagal: ${error.message || 'Silakan periksa isian form Anda'}`, 'error');
                                } else {
                                    showToast(error.message || 'Gagal membuat laporan pemeliharaan', 'error');
                                }
                            });
                        });
                    }

                    const attachment = document.getElementById('attachment');
                    if (attachment) {
                        attachment.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imgElement = document.querySelector('#image-preview img');
                                    if (imgElement) {
                                imgElement.src = e.target.result;
                                        const imagePreview = document.getElementById('image-preview');
                                        if (imagePreview) {
                                            imagePreview.classList.remove('hidden');
                                        }
                                    }
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                    }

                    const removeImage = document.getElementById('remove-image');
                    if (removeImage) {
                        removeImage.addEventListener('click', function(e) {
                        e.preventDefault();
                        const fileInput = document.getElementById('attachment');
                        if (fileInput) {
                            fileInput.value = '';
                        }
                            const imagePreview = document.getElementById('image-preview');
                            if (imagePreview) {
                                imagePreview.classList.add('hidden');
                            }
                        });
                    }

                    const intervalElement = document.getElementById('interval');
                    if (intervalElement) {
                        intervalElement.addEventListener('change', function() {
                        validateField(this, true);
                    });
                    }

                    const startDateElement = document.getElementById('start_date');
                    if (startDateElement) {
                        startDateElement.addEventListener('input', function() {
                        validateField(this, true);

                        const endDateInput = document.getElementById('end_date');
                        if (endDateInput && endDateInput.value && endDateInput.value < this.value) {
                            validateField(endDateInput, false);
                        }
                    });
                    }

                    const endDateElement = document.getElementById('end_date');
                    if (endDateElement) {
                        endDateElement.addEventListener('input', function() {
                        validateField(this, true);
                    });
                    }

                    const userSearchElement = document.getElementById('user_search');
                    if (userSearchElement) {
                        userSearchElement.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.classList.remove('border-red-500');
                            const errorElement = this.closest('.relative').querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        }
                    });
                    }

                    const editIntervalElement = document.getElementById('edit_interval');
                    if (editIntervalElement) {
                        editIntervalElement.addEventListener('change', function() {
                        validateField(this, true);
                    });
                    }

                    const editStartDateElement = document.getElementById('edit_start_date');
                    if (editStartDateElement) {
                        editStartDateElement.addEventListener('input', function() {
                        validateField(this, true);

                        const endDateInput = document.getElementById('edit_end_date');
                        if (endDateInput && endDateInput.value && endDateInput.value < this.value) {
                            validateField(endDateInput, false);
                        }
                    });
                    }

                    const editEndDateElement = document.getElementById('edit_end_date');
                    if (editEndDateElement) {
                        editEndDateElement.addEventListener('input', function() {
                        validateField(this, true);
                    });
                    }

                    const editUserSearchElement = document.getElementById('edit_user_search');
                    if (editUserSearchElement) {
                        editUserSearchElement.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.classList.remove('border-red-500');
                            const errorElement = this.closest('.relative').querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        }
                    });
                    }

                    const maintenanceDateElement = document.getElementById('maintenance_date');
                    if (maintenanceDateElement) {
                        maintenanceDateElement.addEventListener('input', function() {
                        validateField(this, true);
                    });
                    }

                    const descriptionElement = document.getElementById('description');
                    if (descriptionElement) {
                        descriptionElement.addEventListener('input', function() {
                        validateField(this, true);
                    });
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelectorAll('.permission-radio, .edit-permission-radio').forEach(radio => {
                            radio.addEventListener('change', function() {
                                localStorage.setItem('selectedAssetType', this.getAttribute('data-asset-type'));
                            });
                        });

                        const addAssetsBtn = document.getElementById('addAssetsBtn');
                        if (addAssetsBtn) {
                            const originalAddAssetsClick = addAssetsBtn.onclick;
                            addAssetsBtn.addEventListener('click', function() {
                                openModal(modals.assetSelection, modalContents.assetSelection);
                                loadAssets(1);
                            });
                        }
                    });
                });
            </script>
        @endpush

        <style>
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

            #edit_vendor_results, #vendor_results {
                position: absolute;
                z-index: 9999;
            }

            .relative {
                position: relative;
                overflow: visible;
            }

            table td:nth-child(7) {
                width: 120px;
                text-align: center;
            }

            table th:nth-child(7) {
                width: 120px;
            }
        </style>
@endsection
