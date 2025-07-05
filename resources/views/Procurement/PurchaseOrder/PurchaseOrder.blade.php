@extends('Layout.app')

@section('title', 'Pemesanan')

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PEMESANAN</h1>

                    <div class="flex gap-3">
                        <!-- Add Create Button -->
                        @if(hasPermission('purchase-order:vendor-offers:select'))
                        <a href="{{ route('procurement.form-purchase-order') }}" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Buat Baru</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Cari Pemesanan berdasarkan nomor, vendor..."
                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Urutkan</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Kode PO (A-Z)</option>
                            <option value="code_desc" {{ request('sort') == 'code_desc' ? 'selected' : '' }}>Kode PO (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Purchase Order Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Pemesanan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Penawaran</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Vendor</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PIC</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Pengguna Input</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Pemesanan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders ?? [] as $po)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $po['purchase_order_code'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $po['comparison_code'] ?? '-' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $po['vendor']['vendor_name'] ?? '-' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $po['vendor']['contact_person'] ?? '-' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $po['creator_employee_number'] ?? '-' }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    @if(isset($po['created_at']))
                                        @php
                                            $date = \Carbon\Carbon::parse($po['created_at']);
                                            $indonesianMonths = [
                                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                            ];
                                            echo $date->format('d') . ' ' . $indonesianMonths[$date->month - 1] . ' ' . $date->format('Y');
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center">
                                        <a href="{{ route('procurement.detail-purchase-order', ['id' => $po['purchase_order_id']]) }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors" title="Lihat Detail Pemesanan">
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
                                <td colspan="7" class="p-3 text-center text-gray-500">Tidak ada data pesanan pembelian yang tersedia.</td>
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
                                    $total = $pagination['total_items'] ?? count($purchaseOrders ?? []);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                            @else
                                Menampilkan 1 sampai {{ count($purchaseOrders ?? []) }} dari {{ count($purchaseOrders ?? []) }} data
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePurchaseOrderPerPage(this.value)">
                            <option value="10" {{ isset($pagination['limit']) && $pagination['limit'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ isset($pagination['limit']) && $pagination['limit'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ isset($pagination['limit']) && $pagination['limit'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ isset($pagination['limit']) && $pagination['limit'] == 100 ? 'selected' : '' }}>100 per halaman</option>
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
    }, 5000);
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
    }, 5000);
</script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!hasPermission('purchase-order:vendor-offers:select'))
        const createButtons = document.querySelectorAll('a[href="{{ route("procurement.form-purchase-order") }}"]');
        createButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif
        const searchInput = document.getElementById('searchInput');
        const sortOrder = document.getElementById('sortOrder');

        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const sortValue = sortOrder?.value || '';

            const url = new URL(window.location.href);

            ['search', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            if (searchValue) url.searchParams.set('search', searchValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            url.searchParams.set('page', 1);

            window.location.href = url.toString();
        }

        let searchTimeout;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        sortOrder?.addEventListener('change', applyFilters);

        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (sortOrder) {
            const sortValue = urlParams.get('sort');
            if (sortValue) {
                sortOrder.value = sortValue;
            }
        }

        window.changePage = function(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.href = '{{ route("procurement.purchase-order") }}?' + urlParams.toString();
        };

        window.changePurchaseOrderPerPage = function(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1);
            window.location.href = '{{ route("procurement.purchase-order") }}?' + urlParams.toString();
        };
    });
</script>
@endpush
@endsection
