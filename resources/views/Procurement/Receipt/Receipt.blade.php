@extends('Layout.app')

@section('title', 'Penerimaan')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PENERIMAAN</h1>

                        <div class="flex gap-3">
                            @if(hasPermission('receipt:create'))
                                <!-- Add Receipt Button -->
                                <a href="{{ route('procurement.form-receipt') }}"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-base">Tambah Penerimaan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <form action="{{ route('procurement.receipt') }}" method="GET" id="searchForm" data-no-loading>
                                <input type="text" id="searchInput" name="search"
                                    placeholder="Cari penerimaan berdasarkan nomor, penerima..."
                                    value="{{ request('search') }}"
                                    class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="searchBtn">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <input type="hidden" name="page" value="1" id="page">
                                <input type="hidden" name="limit" value="{{ request('limit', 10) }}" id="limit">
                            </form>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <select id="sortOrder" name="sort" form="searchForm"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled {{ request('sort') ? '' : 'selected' }}>Urutan</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Kode Penerimaan
                                    (A-Z)</option>
                                <option value="code_desc" {{ request('sort') == 'code_desc' ? 'selected' : '' }}>Kode
                                    Penerimaan (Z-A)</option>
                                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal (Naik)
                                </option>
                                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Tanggal
                                    (Turun)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Receipt Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Penerimaan
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Pemesanan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Dikirim Oleh</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Diterima Oleh</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Penerimaan
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Dibuat Oleh</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Item</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $receipt['receipt_code'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $receipt['purchase_order_code'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $receipt['delivered_by'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $receipt['receiver_name'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if($receipt['receipt_date'])
                                                @php
                                                    $date = \Carbon\Carbon::parse($receipt['receipt_date']);
                                                    $monthsIndonesian = [
                                                        1 => 'Januari',
                                                        'Februari',
                                                        'Maret',
                                                        'April',
                                                        'Mei',
                                                        'Juni',
                                                        'Juli',
                                                        'Agustus',
                                                        'September',
                                                        'Oktober',
                                                        'November',
                                                        'Desember'
                                                    ];
                                                    echo $date->format('d') . ' ' . $monthsIndonesian[$date->format('n')] . ' ' . $date->format('Y');
                                                @endphp
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $receipt['creator_name'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                            {{ isset($receipt['items']) ? count($receipt['items']) : 0 }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex justify-center">
                                                <a href="{{ route('procurement.receipt.show', $receipt['receipt_id']) }}"
                                                    class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors" title="Lihat Detail Penerimaan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-3 text-center text-gray-500">Tidak ada data penerimaan yang
                                            tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <button onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => max(1, ($pagination['current_page'] ?? 1) - 1)]) }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
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
                                    $maxPagesShown = 5;
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
                            <button onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => min($lastPage, ($pagination['current_page'] ?? 1) + 1)]) }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) >= ($pagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
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
                                        $total = $pagination['total_items'] ?? count($receipts ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                @else
                                    Menampilkan 1 sampai {{ count($receipts ?? []) }} dari {{ count($receipts ?? []) }} data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeItemPerPage(this.value)">
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
        <div id="successNotification"
            class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50"
            role="alert">
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
            setTimeout(function () {
                const notification = document.getElementById('successNotification');
                if (notification) {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(function () {
                        notification.remove();
                    }, 500);
                }
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div id="errorNotification"
            class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50"
            role="alert">
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
            setTimeout(function () {
                const notification = document.getElementById('errorNotification');
                if (notification) {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(function () {
                        notification.remove();
                    }, 500);
                }
            }, 5000);
        </script>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(!hasPermission('receipt:create'))
                    const addButtons = document.querySelectorAll('a[href="{{ route('procurement.form-receipt') }}"]');
                    addButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif
                const searchForm = document.getElementById('searchForm');
                const searchInput = document.getElementById('searchInput');
                const searchBtn = document.getElementById('searchBtn');
                const sortOrder = document.getElementById('sortOrder');
                const perPageSelect = document.getElementById('perPageSelect');

                searchBtn?.addEventListener('click', function () {
                    searchForm?.submit();
                });

                searchInput?.addEventListener('keypress', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        searchForm?.submit();
                    }
                });

                sortOrder?.addEventListener('change', function () {
                    searchForm?.submit();
                });

                window.changePage = function (page) {
                    document.getElementById('page').value = page;
                    searchForm?.submit();
                };

                window.changeReceiptPerPage = function (limit) {
                    document.getElementById('limit').value = limit;
                    document.getElementById('page').value = 1;
                    searchForm?.submit();
                };

                perPageSelect?.removeEventListener('change', function () {
                    document.getElementById('limit').value = this.value;
                    document.getElementById('page').value = 1;
                    searchForm?.submit();
                });
            });
        </script>
    @endpush
@endsection
