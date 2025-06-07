@extends('Layout.app')

@section('title', 'Perbandingan Harga')

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PERBANDINGAN HARGA</h1>

                        <!-- Add Comparison Button -->
                        @if(hasPermission('price-comparison:create'))
                        <a href="{{ route('procurement.form-comparison') }}" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Buat Baru</span>
                        </a>
                        @endif
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan judul, nomor penawaran..."
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
                            <option value="" selected>Semua Status</option>
                            <option value="Draft">Draft</option>
                            <option value="In Progress">Dalam Proses</option>
                            <option value="Completed">Selesai</option>
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

                <!-- Price Comparison Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Penawaran</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Permintaan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Judul</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Dibuat Oleh</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Penawaran</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comparisons as $comparison)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['comparison_code'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['procurement_code'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['title'] ?? 'N/A' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $comparison['creator']['employee_number'] ?? ($comparison['created_by']['name'] ?? 'N/A') }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    @if(isset($comparison['created_at']))
                                        @php
                                            $date = \Carbon\Carbon::parse($comparison['created_at']);
                                            $indonesianMonths = [
                                                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                                                'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
                                            ];
                                            $month = $indonesianMonths[$date->month - 1];
                                            echo $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
                                        @endphp
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if(isset($comparison['status']) && $comparison['status'] == 'Completed') bg-green-100 text-green-800
                                        @elseif(isset($comparison['status']) && $comparison['status'] == 'In Progress') bg-blue-100 text-blue-800
                                        @elseif(isset($comparison['status']) && $comparison['status'] == 'Draft') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if(isset($comparison['status']))
                                            @if($comparison['status'] == 'Completed')
                                                Selesai
                                            @elseif($comparison['status'] == 'In Progress')
                                                Dalam Proses
                                            @elseif($comparison['status'] == 'Draft')
                                                Draft
                                            @else
                                                {{ $comparison['status'] }}
                                            @endif
                                        @else
                                            Tidak Ada
                                        @endif
                                    </span>
                                </td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('procurement.detail-comparison', ['id' => $comparison['comparison_id'] ?? '']) }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if(hasPermission('price-comparison:edit') && (!isset($comparison['status']) || strtolower($comparison['status']) != 'completed'))
                                        <a href="{{ route('procurement.edit-comparison', ['id' => $comparison['comparison_id'] ?? '']) }}" class="p-2 bg-yellow-100 text-yellow-800 rounded-md hover:bg-yellow-200 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @endif
                                        <span class="w-5 h-5 inline-block"></span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-3 text-center text-gray-500">Tidak ada perbandingan harga ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>
                        <div class="flex gap-2">
                            @php
                                $currentPage = $pagination['current_page'] ?? 1;
                                $lastPage = $pagination['total_pages'] ?? 1;
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
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
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
                            @if(isset($pagination) && is_array($pagination))
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $perPage = $pagination['limit'] ?? 10;
                                    $total = $pagination['total_items'] ?? count($comparisons);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                            @else
                                Menampilkan 1 sampai {{ count($comparisons) }} dari {{ count($comparisons) }} data
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeComparisonPerPage(this.value)">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Permission handling
        @if(!hasPermission('price-comparison:create'))
        // Hide create button if user doesn't have permission
        const createButtons = document.querySelectorAll('a[href="{{ route('procurement.form-comparison') }}"]');
        createButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('price-comparison:edit'))
        // Hide edit buttons if user doesn't have permission
        const editButtons = document.querySelectorAll('a[href^="{{ url('procurement/price-comparison') }}/"]');
        editButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

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
            window.location.href = '{{ route("procurement.price-comparison") }}?' + urlParams.toString();
        };

        window.changeComparisonPerPage = function(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = '{{ route("procurement.price-comparison") }}?' + urlParams.toString();
        };
    });
</script>
@endpush
@endsection
