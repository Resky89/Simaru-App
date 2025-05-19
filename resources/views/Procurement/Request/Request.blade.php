@extends('Layout.app')

@section('title', 'Permintaan Aset')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Asset Section -->
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
                            <option value="Approved">Disetujui</option>
                            <option value="Rejected">Ditolak</option>
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
                                    {{ $procurement['requester']['employee_number'] ?? '' }}
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
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if($procurement['status'] == 'Submitted') bg-blue-100 text-blue-800
                                        @elseif($procurement['status'] == 'Approved') bg-green-100 text-green-800
                                        @elseif($procurement['status'] == 'Rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($procurement['status'] == 'Submitted')
                                            Diajukan
                                        @elseif($procurement['status'] == 'Approved')
                                            Disetujui
                                        @elseif($procurement['status'] == 'Rejected')
                                            Ditolak
                                        @else
                                            {{ $procurement['status'] }}
                                        @endif
                                    </span>
                                </td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        @if($procurement['status'] == 'Submitted')
                                        @if(hasPermission('procurement:edit'))
                                        <button class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-request-btn"
                                                data-id="{{ $procurement['procurement_id'] }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @endif
                                        @else
                                        <span class="w-5 h-5 inline-block"></span>
                                        @endif
                                        @if($procurement['status'] == 'Submitted')
                                        @if(hasPermission('procurement:delete'))
                                        <button class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-request-btn"
                                                data-id="{{ $procurement['procurement_id'] }}"
                                                data-title="{{ $procurement['title'] }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @endif
                                        @else
                                        <span class="w-5 h-5 inline-block"></span>
                                        @endif
                                        <a href="{{ route('procurement.detail-request', ['id' => $procurement['procurement_id']]) }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
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
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($pagination) && is_array($pagination))
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) - 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>

                            <div class="flex gap-1">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $totalPages = $pagination['total_pages'] ?? 1;
                                    $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                                    $endPage = min($totalPages, max(5, $currentPage + 2));
                                @endphp

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                <button class="w-8 h-8 {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268]' }} rounded text-sm hover:bg-gray-50 {{ $i == $currentPage ? '' : 'hover:bg-gray-100' }}"
                                       onclick="changePage({{ $i }})">
                                    {{ $i }}
                                </button>
                                @endfor
                        </div>

                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                            Berikutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        @else
                            <!-- Default pagination when no data -->
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                                Sebelumnya
                    </button>
                            <button class="w-8 h-8 bg-[#213268] text-white rounded text-sm">1</button>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                Berikutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                        @endif
                </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($pagination) && is_array($pagination))
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $perPage = $pagination['limit'] ?? 10;
                                    $total = $pagination['total_items'] ?? count($procurements);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} entri
                            @else
                                Menampilkan 1 sampai {{ count($procurements) }} dari {{ count($procurements) }} entri
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['limit']) && $pagination['limit'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ isset($pagination['limit']) && $pagination['limit'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ isset($pagination['limit']) && $pagination['limit'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success and Error Notifications -->
@if(session('success'))
<div id="successNotification" class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Berhasil!</p>
            <p>{{ session('success') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('successNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

@if(session('error'))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Gagal!</p>
            <p>{{ session('error') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('errorNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

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
                <form id="deleteProcurementForm" method="POST">
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
        // Permission-based initialization
        @if(!hasPermission('procurement:create'))
        // Hide create button if user doesn't have permission
        const createButtons = document.querySelectorAll('a[href*="procurement.form-request"]');
        createButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        @if(!hasPermission('procurement:edit'))
        // Hide edit buttons if user doesn't have permission
        const editButtons = document.querySelectorAll('.edit-request-btn');
        editButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        @if(!hasPermission('procurement:delete'))
        // Hide delete buttons if user doesn't have permission
        const deleteButtons = document.querySelectorAll('.delete-request-btn');
        deleteButtons.forEach(btn => {
            if (btn) btn.style.display = 'none';
        });
        @endif

        // Add toast container to the body
        const toastContainer = document.createElement('div');
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
        document.body.appendChild(toastContainer);

        // Show toast notification
        window.showToast = function(message, type = 'info') {
            // Create toast element
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

            // Create toast content
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

            // Add to container
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);

            // Remove after 5 seconds
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

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortOrder = document.getElementById('sortOrder');

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const statusValue = statusFilter?.value || '';
            const sortValue = sortOrder?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'status', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (statusValue) url.searchParams.set('status', statusValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            // Reset to page 1 when filters change
            url.searchParams.set('page', 1);

            // Navigate to the new URL
            window.location.href = url.toString();
        }

        // Add event listeners with debounce for search
        let searchTimeout;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Add event listeners for select filters
        statusFilter?.addEventListener('change', applyFilters);
        sortOrder?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
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

        // Pagination functions
        window.changePage = function(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.href = '{{ route("procurement.request") }}?' + urlParams.toString();
        };

        window.changePerPage = function(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = '{{ route("procurement.request") }}?' + urlParams.toString();
        };

        // Modal functions
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

        // Add event listener for edit buttons
        const editButtons = document.querySelectorAll('.edit-request-btn');
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const procurementId = this.getAttribute('data-id');
                window.location.href = '{{ route("procurement.form-request") }}?id=' + procurementId;
            });
        });

        // Add event listener for delete buttons
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

        // Handle delete form submission
        const deleteForm = document.getElementById('deleteProcurementForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

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
                        // Store message in localStorage
                        localStorage.setItem('procurement_message', data.message || 'Permintaan pengadaan berhasil dihapus');
                        localStorage.setItem('procurement_action', 'success');

                        // Close the modal
                        const modal = document.getElementById('deleteProcurementModal');
                        const content = document.getElementById('deleteProcurementModalContent');
                        if (modal && content) {
                            closeModal(modal, content);
                        }

                        // Reload the page - toast will show after reload
                        window.location.reload();
                    } else {
                        // Store error message in localStorage
                        localStorage.setItem('procurement_message', data.message || 'Gagal menghapus permintaan pengadaan');
                        localStorage.setItem('procurement_action', 'error');

                        // Reload the page - toast will show after reload
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Store error message in localStorage
                    localStorage.setItem('procurement_message', 'Terjadi kesalahan saat menghapus permintaan pengadaan');
                    localStorage.setItem('procurement_action', 'error');

                    // Reload the page - toast will show after reload
                    window.location.reload();
                });
            });
        }

        // Close modal handlers
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        // Handle click outside modal
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

        // Check for procurement message in localStorage
        const message = localStorage.getItem('procurement_message');
        const action = localStorage.getItem('procurement_action');

        if (message && action) {
            // If the message contains "berhasil" but the action is "error",
            // correct the action to "success" to match the message
            if (message.toLowerCase().includes('berhasil') && action === 'error') {
                showToast(message, 'success');
            } else {
                showToast(message, action);
            }

            // Clear the message after showing
            localStorage.removeItem('procurement_message');
            localStorage.removeItem('procurement_action');
        }

        // Display session-based success/error messages if they exist
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endpush
@endsection
