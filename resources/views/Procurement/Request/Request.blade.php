@extends('Layout.app')

@section('title', 'Permintaan Aset')

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PERMINTAAN ASET</h1>

                    <!-- Button Request -->
                    @if(hasPermission('procurement:create'))
                    <a href="{{ route('procurement.form-request') }}" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Buat Permintaan</span>
                    </a>
                    @endif
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan judul, ID permintaan, atau justifikasi..."
                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <select id="statusFilter"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Status</option>
                            <option value="">Semua Status</option>
                            <option value="Submitted">Diajukan</option>
                            <option value="Under Review">Dalam Peninjauan</option>
                            <option value="Approved">Disetujui</option>
                            <option value="Rejected">Ditolak</option>
                            <option value="Procured">Diadakan</option>
                        </select>

                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Urutan</option>
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="title_asc">Judul (A-Z)</option>
                            <option value="title_desc">Judul (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Request Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ID Permintaan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Judul</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Justifikasi</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Jumlah</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Pemohon</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Permintaan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($procurements as $procurement)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $procurement['procurement_code'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $procurement['title'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $procurement['justification'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    {{ count($procurement['details'] ?? []) }}
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    {{ $procurement['requester']['employee_name'] ?? '' }}
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    @if(isset($procurement['request_date']))
                                        @php
                                            $date = \Carbon\Carbon::parse($procurement['request_date']);
                                            $indonesianMonths = [
                                                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                                                'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
                                            ];
                                            $month = $indonesianMonths[$date->month - 1];
                                            echo $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <span class="px-2 py-1 rounded text-xs inline-block w-full text-center whitespace-nowrap
                                        @if($procurement['status'] == 'Submitted') bg-blue-100 text-blue-800
                                        @elseif($procurement['status'] == 'Under Review') bg-yellow-100 text-yellow-800
                                        @elseif($procurement['status'] == 'Approved') bg-green-100 text-green-800
                                        @elseif($procurement['status'] == 'Rejected') bg-red-100 text-red-800
                                        @elseif($procurement['status'] == 'Procured') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($procurement['status'] == 'Submitted')
                                            Diajukan
                                        @elseif($procurement['status'] == 'Under Review')
                                            Dalam Peninjauan
                                        @elseif($procurement['status'] == 'Approved')
                                            Disetujui
                                        @elseif($procurement['status'] == 'Rejected')
                                            Ditolak
                                        @elseif($procurement['status'] == 'Procured')
                                            Diadakan
                                        @else
                                            {{ $procurement['status'] }}
                                        @endif
                                    </span>
                                </td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        @if(hasPermission('procurement:view'))
                                        <a href="{{ route('procurement.detail-request', ['id' => $procurement['procurement_id']]) }}"
                                           class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors detail-request-btn"
                                           data-id="{{ $procurement['procurement_id'] }}"
                                           data-title="{{ $procurement['title'] }}"
                                           data-status="{{ $procurement['status'] }}"
                                           data-can-start="{{ (hasPermission('procurement:approve:manager') || hasPermission('procurement:approve:director')) ? 'true' : 'false' }}"
                                           title="Lihat Detail Permintaan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @endif

                                        @if($procurement['status'] == 'Submitted' && hasPermission('procurement:edit'))
                                        <button class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-request-btn"
                                                data-id="{{ $procurement['procurement_id'] }}" title="Edit Permintaan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @endif

                                        @if($procurement['status'] == 'Submitted' && hasPermission('procurement:delete'))
                                        <button class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-request-btn"
                                                data-id="{{ $procurement['procurement_id'] }}"
                                                data-title="{{ $procurement['title'] }}" title="Hapus Permintaan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-3 text-center text-gray-500">Tidak ada permintaan pengadaan ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($procurements_pagination['prev_page_url'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($procurements_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($procurements_pagination['prev_page_url'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>
                        <div class="flex gap-2">
                            @php
                                $currentPage = $procurements_pagination['current_page'] ?? 1;
                                $lastPage = $procurements_pagination['last_page'] ?? 1;
                                $maxPagesShown = 5; // Show max 5 pages at once
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
                                    <span class="flex items-center justify-center">
                                        ...
                                    </span>
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
                                    <span class="flex items-center justify-center">
                                        ...
                                    </span>
                                @endif
                                <button onclick="changePage({{ $lastPage }})"
                                   class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                    {{ $lastPage }}
                                </button>
                            @endif
                        </div>
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($procurements_pagination['next_page_url'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($procurements_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($procurements_pagination['next_page_url'] ?? 1) <= 1 ? 'disabled' : '' }}>
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
                            @if(isset($procurements_pagination) && is_array($procurements_pagination))
                                Menampilkan {{ $procurements_pagination['from'] ?? 0 }} sampai {{ $procurements_pagination['to'] ?? 0 }} dari
                                {{ $procurements_pagination['total'] ?? 0 }} data
                            @else
                                Menampilkan 0 sampai 0 dari 0 data
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeRequestPerPage(this.value)">
                            <option value="10" {{ isset($procurements_pagination['per_page']) && $procurements_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ isset($procurements_pagination['per_page']) && $procurements_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ isset($procurements_pagination['per_page']) && $procurements_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ isset($procurements_pagination['per_page']) && $procurements_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Procurement Modal -->
<div id="deleteProcurementModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteProcurementModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS PERMINTAAN PENGADAAN</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="deleteProcurementForm" method="POST" data-no-loading>
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus permintaan pengadaan ini? Tindakan ini tidak dapat dibatalkan.</p>
                                <p id="deleteProcurementTitle" class="text-base font-semibold text-center mt-2"></p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!hasPermission('procurement:create'))
        const createButtons = document.querySelectorAll('a[href*="procurement.form-request"]');
        createButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        @if(!hasPermission('procurement:edit'))
        const editButtons = document.querySelectorAll('.edit-request-btn');
        editButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        @if(!hasPermission('procurement:delete'))
        const deleteButtons = document.querySelectorAll('.delete-request-btn');
        deleteButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        const toastContainer = document.createElement('div');
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
        document.body.appendChild(toastContainer);

        window.showToast = function(message, type = 'info') {
            const toast = document.createElement('div');
            let bgColor, borderColor, textColor, icon;

            if (type === 'success') {
                bgColor = 'bg-green-100';
                borderColor = 'border-green-500';
                textColor = 'text-green-700';
                icon = `<svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else if (type === 'error') {
                bgColor = 'bg-red-100';
                borderColor = 'border-red-500';
                textColor = 'text-red-700';
                icon = `<svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else {
                bgColor = 'bg-blue-100';
                borderColor = 'border-blue-500';
                textColor = 'text-blue-700';
                icon = `<svg class="h-6 w-6 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            }

            toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300`;
            toast.setAttribute('role', 'alert');

            toast.innerHTML = `
                <div class="flex items-center">
                    <div class="py-1">
                        ${icon}
                    </div>
                    <div>
                        <p class="font-bold">${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi!'}</p>
                        <p>${message}</p>
                    </div>
                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                </div>
            `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                setTimeout(() => {
                    if (toast.parentNode === toastContainer) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortOrder = document.getElementById('sortOrder');

        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const statusValue = statusFilter?.value || '';
            const sortValue = sortOrder?.value || '';
            const url = new URL(window.location.href);

            ['search', 'status', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            if (searchValue) url.searchParams.set('search', searchValue);
            if (statusValue) url.searchParams.set('status', statusValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        let searchTimeout;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        statusFilter?.addEventListener('change', applyFilters);
        sortOrder?.addEventListener('change', applyFilters);

        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (statusFilter) {
            const statusValue = urlParams.get('status');
            if (statusValue) {
                statusFilter.value = statusValue;
            }
        }
        if (sortOrder) {
            const sortValue = urlParams.get('sort');
            if (sortValue) {
                sortOrder.value = sortValue;
            }
        }

        window.changePage = function(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.href = '{{ route("procurement.request") }}?' + urlParams.toString();
        };

        window.changeRequestPerPage = function(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1);
            window.location.href = '{{ route("procurement.request") }}?' + urlParams.toString();
        };

        window.openModal = function(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        };

        window.closeModal = function(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        document.querySelectorAll('.edit-request-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const procurementId = this.getAttribute('data-id');
                window.location.href = '{{ route("procurement.form-request") }}?id=' + procurementId;
            });
        });

        document.addEventListener('click', function(event) {
            const deleteButton = event.target.closest('.delete-request-btn');
            if (!deleteButton) return;

            event.preventDefault();
            const procurementId = deleteButton.getAttribute('data-id');
            const procurementTitle = deleteButton.getAttribute('data-title');

            const deleteModal = document.getElementById('deleteProcurementModal');
            const deleteContent = document.getElementById('deleteProcurementModalContent');
            const deleteForm = document.getElementById('deleteProcurementForm');
            const deleteTitleEl = document.getElementById('deleteProcurementTitle');

            if (deleteModal && deleteContent && deleteForm && deleteTitleEl) {
                deleteForm.action = '{{ route("procurement.destroy", ["id" => ":id"]) }}'.replace(':id', procurementId);
                deleteTitleEl.textContent = procurementTitle;
                openModal(deleteModal, deleteContent);
            }
        });

        const deleteForm = document.getElementById('deleteProcurementForm');
        if (deleteForm) {
            let isSubmitting = false;

            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    Menghapus...
                `;

                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('procurement_message', data.message || 'Permintaan pengadaan berhasil dihapus');
                        localStorage.setItem('procurement_action', 'success');

                        const modal = document.getElementById('deleteProcurementModal');
                        const content = document.getElementById('deleteProcurementModalContent');
                        if (modal && content) {
                            closeModal(modal, content);
                        }

                        window.location.reload();
                    } else {
                        isSubmitting = false;
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        localStorage.setItem('procurement_message', data.message || 'Gagal menghapus permintaan pengadaan');
                        localStorage.setItem('procurement_action', 'error');
                        showToast(data.message || 'Gagal menghapus permintaan pengadaan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    localStorage.setItem('procurement_message', 'Terjadi kesalahan saat menghapus permintaan pengadaan');
                    localStorage.setItem('procurement_action', 'error');
                    showToast('Terjadi kesalahan saat menghapus permintaan pengadaan', 'error');
                });
            });
        }

        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const content = this.querySelector('[id$="ModalContent"]');
                    if (content) {
                        closeModal(this, content);
                    }
                }
            });
        });

        const message = localStorage.getItem('procurement_message');
        const action = localStorage.getItem('procurement_action');

        if (message && action) {
            if (message.toLowerCase().includes('berhasil') && action === 'error') {
                showToast(message, 'success');
            } else {
                showToast(message, action);
            }

            localStorage.removeItem('procurement_message');
            localStorage.removeItem('procurement_action');
        }

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

                // Handle detail request button clicks
        document.querySelectorAll('.detail-request-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                const procurementId = this.getAttribute('data-id');
                const procurementTitle = this.getAttribute('data-title');
                const status = this.getAttribute('data-status');
                const canStart = this.getAttribute('data-can-start') === 'true';
                const detailUrl = this.getAttribute('href');

                                // Only process start procurement if status is Submitted and user has permission
                if (status === 'Submitted' && canStart) {
                    e.preventDefault(); // Prevent default navigation

                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<div class="inline-block w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>';

                    fetch('{{ route("procurement.start", ["id" => ":id"]) }}'.replace(':id', procurementId), {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect to detail page after successful start without showing toast
                            window.location.href = detailUrl;
                        } else {
                            this.innerHTML = originalHTML;
                            showToast(data.errors?.exception?.[0] || 'Gagal memulai proses pengadaan', 'error');
                            // Navigate to detail page even if start fails
                            setTimeout(() => {
                                window.location.href = detailUrl;
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.innerHTML = originalHTML;
                        showToast('Terjadi kesalahan saat memulai proses pengadaan', 'error');
                        // Navigate to detail page even if start fails
                        setTimeout(() => {
                            window.location.href = detailUrl;
                        }, 2000);
                    });
                }
                // Otherwise, let the default navigation happen
            });
        });
    });
</script>
@endpush
@endsection
