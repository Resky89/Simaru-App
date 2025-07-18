@extends('Layout.app')

@section('title', 'Laporan Opname')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">LAPORAN OPNAME</h1>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan kode opname..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                value="{{ $search ?? '' }}">
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
                                <option value="desc" {{ isset($sort_order) && $sort_order == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ isset($sort_order) && $sort_order == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opname Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kode Opname</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Ruangan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Konduktor</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Total Asets</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Aset Scan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Dibuat</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($opnames as $opname)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $opname['opname_code'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $opname['room_name'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $opname['conductor_name'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $opname['total_assets'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $opname['scanned_assets'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ \Carbon\Carbon::parse($opname['created_at'])->locale('id')->isoFormat('D MMMM Y') }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <a href="{{ route('opnames.detail', $opname['opname_id']) }}"
                                                    class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
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
                                        <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada
                                            laporan opname ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <button onclick="window.location.href='{{ $pagination['prev_page_url'] ?? '#' }}'"
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
                                    $lastPage = $pagination['last_page'] ?? 1;
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
                            <button onclick="window.location.href='{{ $pagination['next_page_url'] ?? '#' }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
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
                                        $perPage = $pagination['per_page'] ?? 10;
                                        $total = $pagination['total'] ?? count($opnames ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                @else
                                    Menampilkan 0 sampai 0 dari 0 data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeOpnamePerPage(this.value)">
                                <option value="10" {{ isset($pagination['per_page']) && $pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($pagination['per_page']) && $pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($pagination['per_page']) && $pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.changePerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', limit);
                    window.location.href = url.toString();
                }

                window.changeOpnamePerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', limit);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                }

                const searchInput = document.getElementById('searchInput');
                const sortOrder = document.getElementById('sortOrder');

                let searchTimeout;
                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });

                sortOrder?.addEventListener('change', applyFilters);

                function applyFilters() {
                    const searchValue = searchInput?.value.trim() || '';
                    const sortValue = sortOrder?.value || '';

                    const url = new URL(window.location.href);

                    ['search', 'sort_order', 'page'].forEach(param => {
                        url.searchParams.delete(param);
                    });

                    if (searchValue) url.searchParams.set('search', searchValue);
                    if (sortValue) url.searchParams.set('sort_order', sortValue);

                    url.searchParams.set('page', 1);

                    window.location.href = url.toString();
                }
            });
        </script>
    @endpush
@endsection
