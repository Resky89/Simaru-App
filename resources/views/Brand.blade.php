@extends('Layout.app')

@section('title', 'Manajemen Merk')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Brand Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DAFTAR MERK</h1>

                        <!-- Button Add Brand -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('brand:import'))
                                <button id="importBrandBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            @if(hasPermission('brand:create'))
                                <button id="addBrandBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Merk Baru</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama merk..."
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
                                <option value="" selected>Urutan Default</option>
                                <option value="id_asc">Terlama</option>
                                <option value="id_desc">Terbaru</option>
                                <option value="name_asc">Nama (A-Z)</option>
                                <option value="name_desc">Nama (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <div id="brandTableContainer">
                        <!-- Brand Table -->
                        <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByName">
                                            <span class="text-xs">Nama Merk</span>
                                            <span class="sort-icon">
                                                @php
                                                    $currentSort = request()->query('sort');
                                                    $sortIcon = 'none';

                                                    if ($currentSort === 'name_asc') {
                                                        $sortIcon = 'asc';
                                                    } elseif ($currentSort === 'name_desc') {
                                                        $sortIcon = 'desc';
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands ??[] as $brand)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $brand['brand_name'] }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('brand:edit'))
                                                    <button
                                                        class="edit-brand-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-brand-id="{{ $brand['brand_id'] }}"
                                                        data-brand-name="{{ $brand['brand_name'] }}"
                                                        title="Edit Merk">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if(hasPermission('brand:delete'))
                                                    <button
                                                        class="delete-brand-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-brand-id="{{ $brand['brand_id'] }}"
                                                        data-brand-name="{{ $brand['brand_name'] }}"
                                                        title="Hapus Merk">
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
                                        <td colspan="4" class="p-3 text-xs text-center border-t border-[#EEF1F4]">Tidak ada merk
                                            ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($brands_pagination))
                        <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($brands_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changePage({{ ($brands_pagination['current_page'] ?? 1) - 1 }})" {{ ($brands_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Sebelumnya
                                </button>

                                <div class="flex gap-2">
                                    @php
                                        $currentPage = $brands_pagination['current_page'] ?? 1;
                                        $lastPage = $brands_pagination['last_page'] ?? 1;
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
                                    <button
                                        onclick="changePage({{ $lastPage }})"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $lastPage }}
                                    </button>
                                @endif
                                </div>
                                <button
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($brands_pagination['current_page'] ?? 1) >= ($brands_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changePage({{ ($brands_pagination['current_page'] ?? 1) + 1 }})" {{ ($brands_pagination['current_page'] ?? 1) >= ($brands_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
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
                                    @php
                                        $currentPage = $brands_pagination['current_page'] ?? 1;
                                        $perPage = $brands_pagination['per_page'] ?? 10;
                                        $total = $brands_pagination['total'] ?? count($brands ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                </span>
                                <select id="perPageSelect"
                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                    onchange="changeItemPerPage(this.value)">
                                    <option value="10" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                    <option value="25" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                    <option value="50" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                    <option value="100" {{ isset($brands_pagination['per_page']) && $brands_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(hasPermission('brand:create'))
        @include('components.brand.add-modal')
    @endif

    @if(hasPermission('brand:edit'))
        @include('components.brand.edit-modal')
    @endif

    @if(hasPermission('brand:delete'))
        @include('components.brand.delete-modal')
    @endif

    @if(hasPermission('brand:import'))
        @include('components.brand.import-modal')
    @endif

    @push('scripts')
        @include('components.brand.script')
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    @endpush
@endsection
