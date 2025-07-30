@extends('Layout.app')

@section('title', 'Berita Acara')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Official Reports Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">BERITA ACARA</h1>

                        <!-- Button Add Official Report -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('official-report:create'))
                                <a href="{{ route('official-report.create') }}"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transition-colors">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Berita Acara</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan catatan atau tipe..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <select id="reportTypeFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Semua Tipe</option>
                                <option value="DISPOSAL">Dihapuskan</option>
                                <option value="LOSS">Hilang</option>
                                <option value="FOUND">Ditemukan</option>
                            </select>

                            <select id="statusFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Semua Status</option>
                                <option value="SUBMITTED">Diajukan</option>
                                <option value="APPROVED">Disetujui</option>
                                <option value="REJECTED">Ditolak</option>
                            </select>

                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Urutan Default</option>
                                <option value="newest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                            </select>
                        </div>
                    </div>

                    <!-- Official Reports Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[12%]">Dibuat Oleh</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Catatan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[12%]">
                                        <div class="flex items-center space-x-1 cursor-pointer" onclick="sortTable('release_date')">
                                            <span class="text-xs">Tanggal Rilis</span>
                                            <span class="sort-icon">
                                                @php
                                                    $currentSort = request()->query('sort_by');
                                                    $currentOrder = request()->query('sort_order');
                                                    $sortIcon = 'none';
                                                    if ($currentSort === 'release_date') {
                                                        $sortIcon = $currentOrder === 'asc' ? 'asc' : 'desc';
                                                    }
                                                @endphp

                                                @if($sortIcon === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($sortIcon === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[12%]">Tipe</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[10%]">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[120px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($official_reports ?? [] as $report)
                                    <tr>
                                        <!-- Dibuat Oleh -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $report['created_by']['employee_name'] ?? '-' }}
                                        </td>
                                        <!-- Catatan -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $report['notes'] ? \Illuminate\Support\Str::limit($report['notes'], 30) : '-' }}
                                        </td>
                                        <!-- Tanggal Rilis -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $report['release_date'] !== 'N/A' ? $report['release_date'] : '-' }}
                                        </td>
                                        <!-- Tipe -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $typeColor = 'bg-gray-500';
                                                $typeText = 'Tidak Diketahui';

                                                switch ($report['report_type']) {
                                                    case 'DISPOSAL':
                                                        $typeColor = 'bg-[#ACC3EF]';
                                                        $typeText = 'DIHAPUSKAN';
                                                        break;
                                                    case 'LOSS':
                                                        $typeColor = 'bg-[#EF4444]';
                                                        $typeText = 'HILANG';
                                                        break;
                                                    case 'FOUND':
                                                        $typeColor = 'bg-[#659B09]';
                                                        $typeText = 'DITEMUKAN';
                                                        break;
                                                    default:
                                                        $typeText = $report['report_type'];
                                                }
                                            @endphp
                                            <div class="{{ $typeColor }} py-0.5 px-3 rounded-md text-center">
                                                <p class="text-xs text-white font-medium">{{ $typeText }}</p>
                                            </div>
                                        </td>
                                        <!-- Status -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusText = 'Tidak Diketahui';

                                                switch ($report['status']) {
                                                    case 'SUBMITTED':
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        $statusText = 'Diajukan';
                                                        break;
                                                    case 'APPROVED':
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        $statusText = 'Disetujui';
                                                        break;
                                                    case 'REJECTED':
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        $statusText = 'Ditolak';
                                                        break;
                                                    case 'PENDING':
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        $statusText = 'Menunggu';
                                                        break;
                                                    default:
                                                        $statusText = $report['status'];
                                                }
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <!-- Aksi -->
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <!-- View Button -->
                                                <a href="{{ route('official-report.show', $report['official_report_id']) }}"
                                                    class="p-2 bg-blue-100 text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if(hasPermission('official-report:edit') &&
                                                $report['status'] !== 'APPROVED' &&
                                                (!isset($report['approval_1_status']) || $report['approval_1_status'] !== 'APPROVED') &&
                                                (!isset($report['approval_2_status']) || $report['approval_2_status'] !== 'APPROVED'))
                                                                <a href="{{ route('official-report.edit', $report['official_report_id']) }}"
                                                        class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        title="Edit Berita Acara">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(hasPermission('official-report:delete') &&
                                                $report['status'] !== 'APPROVED' &&
                                                (!isset($report['approval_1_status']) || $report['approval_1_status'] !== 'APPROVED') &&
                                                (!isset($report['approval_2_status']) || $report['approval_2_status'] !== 'APPROVED'))
                                                    <button
                                                        class="delete-report-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-report-id="{{ $report['official_report_id'] }}"
                                                        title="Hapus Berita Acara">
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
                                        <td colspan="6" class="p-3 text-xs text-center border-t border-[#EEF1F4]">Tidak ada
                                            berita acara ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-4">
                        <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($official_reports_pagination['prev_page_url'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($official_reports_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($official_reports_pagination['prev_page_url'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $official_reports_pagination['current_page'] ?? 1;
                                    $lastPage = $official_reports_pagination['last_page'] ?? 1;
                                    $maxPagesShown = 5;
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $startPage + $maxPagesShown - 1);

                                    if ($endPage - $startPage + 1 < $maxPagesShown) {
                                        $startPage = max(1, $endPage - $maxPagesShown + 1);
                                    }
                                @endphp

                            @if($startPage > 1)
                                <button onclick="changePage(1)"
                                   class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                    1
                                </button>
                                @if($startPage > 2)
                                    <span class="flex items-center justify-center">...</span>
                                @endif
                            @endif

                            @for ($i = $startPage; $i <= $endPage; $i++)
                                <button onclick="changePage({{ $i }})"
                                   class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </button>
                            @endfor

                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <span class="flex items-center justify-center">...</span>
                                @endif
                                <button onclick="changePage({{ $lastPage }})"
                                   class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                    {{ $lastPage }}
                                </button>
                            @endif
                            </div>
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($official_reports_pagination['next_page_url'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($official_reports_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($official_reports_pagination['next_page_url'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">
                                @if(isset($official_reports_pagination) && is_array($official_reports_pagination))
                                    Menampilkan {{ $official_reports_pagination['from'] }} sampai {{ $official_reports_pagination['to'] }} dari
                                    {{ $official_reports_pagination['total'] }} data
                                @else
                                    Menampilkan 0 data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changePerPage(this.value)">
                                <option value="10" {{ isset($official_reports_pagination['per_page']) && $official_reports_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($official_reports_pagination['per_page']) && $official_reports_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($official_reports_pagination['per_page']) && $official_reports_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                <option value="100" {{ isset($official_reports_pagination['per_page']) && $official_reports_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @if(hasPermission('official-report:delete'))
        <div id="deleteOfficialReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteOfficialReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">HAPUS BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus berita acara
                                        ini? Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <form id="deleteOfficialReportForm" action="" method="POST" data-no-loading class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="deleteReportId" name="report_id">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Show success/error messages
                @if(session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                // Search and filter functionality
                const searchInput = document.getElementById('searchInput');
                const reportTypeFilter = document.getElementById('reportTypeFilter');
                const statusFilter = document.getElementById('statusFilter');
                const sortOrder = document.getElementById('sortOrder');

                function applyFilters() {
                    const searchValue = searchInput?.value.trim() || '';
                    const reportTypeValue = reportTypeFilter?.value || '';
                    const statusValue = statusFilter?.value || '';
                    const sortValue = sortOrder?.value || '';

                    const url = new URL(window.location.href);

                    ['search', 'report_type', 'status', 'sort_by', 'sort_order', 'page'].forEach(param => {
                        url.searchParams.delete(param);
                    });

                    if (searchValue) url.searchParams.set('search', searchValue);
                    if (reportTypeValue) url.searchParams.set('report_type', reportTypeValue);
                    if (statusValue) url.searchParams.set('status', statusValue);

                    // Handle sort parameter mapping from old format to new format
                    if (sortValue) {
                        switch(sortValue) {
                            case 'newest':
                                url.searchParams.set('sort_by', 'created_at');
                                url.searchParams.set('sort_order', 'desc');
                                break;
                            case 'oldest':
                                url.searchParams.set('sort_by', 'created_at');
                                url.searchParams.set('sort_order', 'asc');
                                break;
                            case 'report_type_asc':
                                url.searchParams.set('sort_by', 'report_type');
                                url.searchParams.set('sort_order', 'asc');
                                break;
                            case 'report_type_desc':
                                url.searchParams.set('sort_by', 'report_type');
                                url.searchParams.set('sort_order', 'desc');
                                break;
                            case 'status_asc':
                                url.searchParams.set('sort_by', 'status');
                                url.searchParams.set('sort_order', 'asc');
                                break;
                            case 'status_desc':
                                url.searchParams.set('sort_by', 'status');
                                url.searchParams.set('sort_order', 'desc');
                                break;
                        }
                    }

                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                }

                // Sort table function for clickable headers
                window.sortTable = function(column) {
                    const url = new URL(window.location.href);
                    const currentSortBy = url.searchParams.get('sort_by');
                    const currentSortOrder = url.searchParams.get('sort_order');

                    let newSortOrder = 'asc';

                    // If clicking the same column, toggle the order
                    if (currentSortBy === column) {
                        newSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                    }

                    url.searchParams.set('sort_by', column);
                    url.searchParams.set('sort_order', newSortOrder);
                    url.searchParams.set('page', 1);

                    window.location.href = url.toString();
                };

                let searchTimeout;
                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });

                reportTypeFilter?.addEventListener('change', applyFilters);
                statusFilter?.addEventListener('change', applyFilters);
                sortOrder?.addEventListener('change', applyFilters);

                // Initialize filters from URL
                const urlParams = new URLSearchParams(window.location.search);
                if (searchInput) searchInput.value = urlParams.get('search') || '';
                if (reportTypeFilter) reportTypeFilter.value = urlParams.get('report_type') || '';
                if (statusFilter) statusFilter.value = urlParams.get('status') || '';

                // Initialize sort dropdown based on sort_by and sort_order parameters
                const sortBy = urlParams.get('sort_by');
                const sortOrderParam = urlParams.get('sort_order');

                if (sortBy && sortOrderParam) {
                    let sortValue = '';
                    if (sortBy === 'created_at' && sortOrderParam === 'desc') {
                        sortValue = 'newest';
                    } else if (sortBy === 'created_at' && sortOrderParam === 'asc') {
                        sortValue = 'oldest';
                    } else if (sortBy === 'report_type' && sortOrderParam === 'asc') {
                        sortValue = 'report_type_asc';
                    } else if (sortBy === 'report_type' && sortOrderParam === 'desc') {
                        sortValue = 'report_type_desc';
                    } else if (sortBy === 'status' && sortOrderParam === 'asc') {
                        sortValue = 'status_asc';
                    } else if (sortBy === 'status' && sortOrderParam === 'desc') {
                        sortValue = 'status_desc';
                    }

                    if (sortOrder && sortValue) {
                        sortOrder.value = sortValue;
                    }
                }

                // Pagination functions
                window.changePage = function (page) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);
                    window.location.href = url.toString();
                };

                window.changePerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', limit);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                };

                // Toast notification function
                function showToast(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

                    const hasHTML = /<[a-z][\s\S]*>/i.test(message);

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

                        if (hasHTML) {
                            messageContainer.innerHTML = message;
                        } else {
                            messageContainer.textContent = message;
                        }

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
                }

                // CSS for animations
                document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        @keyframes slideInRight {
                            from { transform: translateX(100%); }
                            to { transform: translateX(0); }
                        }
                        .animate-slide-in-right {
                            animation: slideInRight 0.3s ease-out forwards;
                        }

                        /* Styling for error messages with HTML content */
                        .error-message ul {
                            margin-top: 0.5rem;
                            padding-left: 1.5rem;
                        }
                        .error-message ul li {
                            margin-bottom: 0.25rem;
                        }
                        .error-message ul li:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                `);

                // Modal functionality
                const deleteOfficialReportModal = document.getElementById('deleteOfficialReportModal');
                const deleteOfficialReportModalContent = document.getElementById('deleteOfficialReportModalContent');

                // Function to open modal with animation
                function openModal(modal, modalContent) {
                    if (modal && modalContent) {
                        modal.classList.remove('hidden');
                        // Force reflow
                        modal.offsetHeight;
                        // Add show classes
                        modalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        modalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }
                }

                // Function to close modal with animation
                function closeModal(modal, modalContent) {
                    if (modal && modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        modalContent.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    }
                }

                // Delete buttons handler
                document.querySelectorAll('.delete-report-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const reportId = button.getAttribute('data-report-id');
                        const formAction = "{{ url('official-reports') }}/" + reportId;
                        document.getElementById('deleteOfficialReportForm').action = formAction;
                        document.getElementById('deleteReportId').value = reportId;
                        openModal(deleteOfficialReportModal, deleteOfficialReportModalContent);
                    });
                });

                // Close modal buttons
                document.querySelectorAll('.close-modal').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        closeModal(deleteOfficialReportModal, deleteOfficialReportModalContent);
                    });
                });

                // Close modal when clicking outside
                if (deleteOfficialReportModal) {
                    deleteOfficialReportModal.addEventListener('click', (e) => {
                        if (e.target === deleteOfficialReportModal) {
                            closeModal(deleteOfficialReportModal, deleteOfficialReportModalContent);
                        }
                    });
                }

                // Handle delete form submission with AJAX
                const deleteForm = document.getElementById('deleteOfficialReportForm');
                if (deleteForm) {
                    deleteForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;

                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;

                        const formData = new FormData(this);
                        formData.append('_method', 'DELETE');

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;

                            if (data.success) {
                                const modal = document.getElementById('deleteOfficialReportModal');
                                const modalContent = document.getElementById('deleteOfficialReportModalContent');
                                closeModal(modal, modalContent);
                                showToast(data.message || 'Berita acara berhasil dihapus', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // Parse error response with comprehensive error handling
                                const errorData = data.errors || [];
                                let errorMessage = data.message || 'Gagal menghapus berita acara';
                                let errorList = [];

                                // Handle different error response formats
                                if (Array.isArray(errorData)) {
                                    errorData.forEach(error => {
                                        if (error.path && error.message) {
                                            errorList.push(error.message);
                                        } else if (typeof error === 'string') {
                                            errorList.push(error);
                                        }
                                    });
                                }
                                else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                    Object.entries(errorData).forEach(([field, errors]) => {
                                        if (Array.isArray(errors)) {
                                            errors.forEach(err => {
                                                errorList.push(err);
                                            });
                                        } else if (typeof errors === 'string') {
                                            errorList.push(errors);
                                        }
                                    });
                                } else if (typeof errorData === 'string') {
                                    errorMessage = errorData;
                                }

                                // Use errorList if available, otherwise use errorMessage
                                let finalMessage;
                                if (errorList.length > 0) {
                                    // If only one error, show it directly without bullet points
                                    if (errorList.length === 1) {
                                        finalMessage = errorList[0];
                                    } else {
                                        // Multiple errors, use bullet points
                                        finalMessage = errorList.join('<br>• ');
                                        finalMessage = '• ' + finalMessage;
                                    }
                                } else {
                                    finalMessage = errorMessage;
                                }

                                showToast(finalMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;

                            let errorMessage = 'Terjadi kesalahan saat menghapus berita acara';

                            // Provide more specific error messages based on error type
                            if (error.message.includes('JSON')) {
                                errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi atau hubungi administrator.';
                            } else if (error.message.includes('HTTP')) {
                                errorMessage = `Kesalahan server: ${error.message}. Silakan coba lagi.`;
                            } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                                errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                            }

                            showToast(errorMessage, 'error');
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
