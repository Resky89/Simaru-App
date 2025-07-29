@extends('Layout.app')

@section('title', 'Buildings')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Building Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">Gedung</h1>

                        <!-- Button Add Building -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('building:import'))
                                <button id="importBuildingBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            @if(hasPermission('building:create'))
                                <button id="addBuildingBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Gedung</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="relative flex-grow">
                            <input type="text" id="searchBuildingInput"
                                placeholder="Cari berdasarkan nama gedung atau alamat..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <select id="sortBuildingOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Urutan Default</option>
                                <option value="id_asc">Terlama</option>
                                <option value="id_desc">Terbaru</option>
                                <option value="name_asc">Nama (A-Z)</option>
                                <option value="name_desc">Nama (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Building Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByName">
                                            <span class="text-xs">Nama Gedung</span>
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($sortIcon === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByAddress">
                                            <span class="text-xs">Alamat</span>
                                            <span class="sort-icon">
                                                @php
                                                    $addressSortIcon = 'none';

                                                    if ($currentSort === 'address_asc') {
                                                        $addressSortIcon = 'asc';
                                                    } elseif ($currentSort === 'address_desc') {
                                                        $addressSortIcon = 'desc';
                                                    }
                                                @endphp

                                                @if($addressSortIcon === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($addressSortIcon === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buildings ?? [] as $building)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $building['building_name'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $building['address'] }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('building:edit'))
                                                    <button
                                                        class="edit-building-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-id="{{ $building['building_id'] }}"
                                                        data-name="{{ $building['building_name'] }}"
                                                        data-address="{{ $building['address'] }}" title="Edit Gedung">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if(hasPermission('building:delete'))
                                                    <button
                                                        class="delete-building-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-id="{{ $building['building_id'] }}" title="Hapus Gedung">
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
                                        <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada gedung yang ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <button onclick="window.location.href='{{ $buildings_pagination['prev_page_url'] ?? '#' }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($buildings_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ ($buildings_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </button>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $buildings_pagination['current_page'] ?? 1;
                                    $lastPage = $buildings_pagination['last_page'] ?? 1;
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
                                    <button
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}'"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $lastPage }}
                                    </button>
                                @endif
                            </div>
                            <button
                                onclick="window.location.href='{{ ($buildings_pagination['current_page'] ?? 1) >= ($buildings_pagination['last_page'] ?? 1) ? '#' : request()->fullUrlWithQuery(['page' => ($buildings_pagination['current_page'] ?? 1) + 1]) }}'"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($buildings_pagination['current_page'] ?? 1) >= ($buildings_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ ($buildings_pagination['current_page'] ?? 1) >= ($buildings_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
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
                                @if(isset($buildings_pagination) && is_array($buildings_pagination))
                                    Menampilkan {{ $buildings_pagination['from'] }} sampai {{ $buildings_pagination['to'] }} dari
                                    {{ $buildings_pagination['total'] }} data
                                @else
                                    Menampilkan 0 data
                                @endif
                            </span>
                            <select id="buildingPerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeBuildingPerPage(this.value)">
                                <option value="10" {{ isset($buildings_pagination['per_page']) && $buildings_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($buildings_pagination['per_page']) && $buildings_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($buildings_pagination['per_page']) && $buildings_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                <option value="50" {{ isset($buildings_pagination['per_page']) && $buildings_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Building Modal -->
        @if(hasPermission('building:create'))
            <div id="addBuildingModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="buildingModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">Tambah Gedung</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6">
                                <form id="addBuildingForm" action="{{ route('buildings.store') }}" method="POST" data-no-loading
                                    novalidate>
                                    @csrf
                                    <div class="space-y-4 max-w-[400px] mx-auto">
                                        <!-- Building Name Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nama Gedung <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="building_name" id="add_building_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik nama gedung" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Gedung harus diisi
                                            </div>
                                        </div>

                                        <!-- Address Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Alamat <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="address" id="add_building_address"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik alamat gedung" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Alamat harus diisi</div>
                                        </div>

                                        <!-- Button Group -->
                                        <div class="pt-4">
                                            <button type="submit"
                                                class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Building Modal -->
        @if(hasPermission('building:edit'))
            <div id="editBuildingModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="editBuildingModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">Edit Gedung</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6">
                                <form id="editBuildingForm" action="" method="POST" data-no-loading novalidate>
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="editBuildingId" name="building_id">
                                    <div class="space-y-4 max-w-[400px] mx-auto">
                                        <!-- Building Name Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nama Gedung <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="editBuildingName" name="building_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik nama gedung" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Gedung harus diisi
                                            </div>
                                        </div>

                                        <!-- Address Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Alamat <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="editAddress" name="address"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik alamat gedung" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Alamat harus diisi</div>
                                        </div>

                                        <!-- Button Group -->
                                        <div class="pt-4">
                                            <button type="submit"
                                                class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Building Confirmation Modal -->
        @if(hasPermission('building:delete'))
            <div id="deleteBuildingModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="deleteBuildingModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Hapus Gedung</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Content -->
                            <form id="deleteBuildingForm" action="" method="POST" data-no-loading>
                                @csrf
                                @method('DELETE')
                                <input type="hidden" id="deleteBuildingId" name="building_id">
                                <div class="p-6">
                                    <div class="space-y-6 max-w-[400px] mx-auto">
                                        <div class="flex flex-col items-center">
                                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base text-gray-600 text-center">Apakah anda yakin ingin menghapus
                                                gedung
                                                ini? Aksi ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button"
                                                class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
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

        <!-- Success and Error Notifications -->
        @if(session('success'))
            <div id="successNotification"
                class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50"
                role="alert">
                <div class="flex items-start">
                    <div class="py-1">
                        <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Berhasil!</p>
                        <div>{{ session('success') }}</div>
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
                }, 5000); // Hide after 5 seconds
            </script>
        @endif

        @if(session('error'))
            <div id="errorNotification"
                class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50"
                role="alert">
                <div class="flex items-start">
                    <div class="py-1">
                        <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Gagal!</p>
                        <div>{{ session('error') }}</div>
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
                }, 5000); // Hide after 5 seconds
            </script>
        @endif

        <!-- Import Building Modal -->
        @if(hasPermission('building:import'))
            <div id="importBuildingModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="importBuildingModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPOR GEDUNG</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Step 1: File Selection -->
                            <div id="import-building-step-1" class="block">
                                <div class="p-6">
                                    <div class="space-y-6">
                                        <!-- Import Instructions -->
                                        <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                            <p class="font-medium text-blue-600 mb-2">Petunjuk Impor:</p>
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>Gunakan format template Excel untuk mengimpor</li>
                                                <li>Kolom yang diperlukan: Nama Gedung, Alamat</li>
                                                <li>Maksimal 100 data per impor</li>
                                                <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                            </ul>
                                            <div class="mt-3 flex justify-end">
                                                <a href="{{ asset('docs/ImportGedungTemplate.xlsx') }}" download
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                        </path>
                                                    </svg>
                                                    Unduh Template
                                                </a>
                                            </div>
                                        </div>

                                        <!-- File Upload -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#213268]">File Excel</label>
                                            <div
                                                class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                <!-- File preview -->
                                                <div id="building-excel-file-name" class="mt-2 mb-4 w-full hidden">
                                                    <div
                                                        class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                        <div class="flex items-center">
                                                            <svg class="w-6 h-6 text-green-600 mr-2"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span id="building-file-name-text"
                                                                class="text-sm text-gray-700 truncate"></span>
                                                            <button type="button" id="remove-building-excel"
                                                                class="ml-auto text-red-500 hover:text-red-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <svg class="mx-auto h-12 w-12 text-[#213268]"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span
                                                            class="text-[#213268] font-semibold">telusuri file</span></p>
                                                    <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv
                                                    </p>
                                                    <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area
                                                        ini untuk memilih file</p>
                                                </div>
                                                <input type="file" id="building_excel_file" name="excel_file"
                                                    accept=".xlsx,.xls,.csv"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            </div>
                                        </div>

                                        <!-- Error Message -->
                                        <div id="building-excel-error" class="hidden text-red-500 text-sm"></div>

                                        <!-- Loading Indicator -->
                                        <div id="building-excel-loading" class="hidden text-center py-2">
                                            <div
                                                class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin">
                                            </div>
                                            <p class="mt-2 text-sm text-gray-600">Memproses data Excel...</p>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="flex gap-3">
                                            <button type="button"
                                                class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Batal
                                            </button>
                                            <button type="button" id="building-preview-btn" disabled
                                                class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Pratinjau Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Import Progress -->
                            <div id="import-building-step-2" class="hidden">
                                <div class="p-6">
                                    <div class="space-y-6">
                                        <!-- Preview Header -->
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                            <span class="text-sm text-gray-500" id="building-preview-count">0 item
                                                ditemukan</span>
                                        </div>

                                        <!-- Preview Table -->
                                        <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                            <table class="w-full">
                                                <thead class="sticky top-0 bg-[#213268] text-white">
                                                    <tr>
                                                        <th class="p-3 text-left text-xs font-semibold">No</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Nama Gedung</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Alamat</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="building-preview-table-body">
                                                    <!-- Preview data will be inserted here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Warning/Error Messages -->
                                        <div id="building-preview-warnings"
                                            class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                            <p class="font-medium mb-2">Peringatan:</p>
                                            <ul class="list-disc pl-5" id="building-warning-list">
                                                <!-- Warning messages will be inserted here -->
                                            </ul>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-3">
                                            <button type="button" id="building-back-to-upload-btn"
                                                class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Kembali
                                            </button>
                                            <form action="{{ route('buildings.import') }}" method="POST"
                                                id="building-import-form" class="w-2/3" data-no-loading
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="excel_data" id="building_excel_data">
                                                <button type="submit" id="building-import-btn"
                                                    class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                                    Impor Data
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(!hasPermission('building:create'))
                    const addButtons = document.querySelectorAll('#addBuildingBtn');
                    addButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                    @if(!hasPermission('building:edit'))
                        const editButtons = document.querySelectorAll('.edit-building-btn');
                        editButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('building:delete'))
                        const deleteButtons = document.querySelectorAll('.delete-building-btn');
                        deleteButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('building:import'))
                        const importButtons = document.querySelectorAll('#importBuildingBtn');
                        importButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                window.changeBuildingPerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', limit);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                }

                const searchBuildingInput = document.getElementById('searchBuildingInput');
                const sortBuildingOrder = document.getElementById('sortBuildingOrder');

                function applyBuildingFilters() {
                    const searchValue = searchBuildingInput?.value.trim() || '';
                    const sortValue = sortBuildingOrder?.value || '';

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
                searchBuildingInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyBuildingFilters, 500);
                });

                sortBuildingOrder?.addEventListener('change', applyBuildingFilters);

                const urlParams = new URLSearchParams(window.location.search);
                if (searchBuildingInput) {
                    searchBuildingInput.value = urlParams.get('search') || '';
                }
                if (sortBuildingOrder) {
                    const sortValue = urlParams.get('sort');
                    if (sortValue) {
                        sortBuildingOrder.value = sortValue;
                    }
                }

                const toastContainer = document.createElement('div');
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
                document.body.appendChild(toastContainer);

                const addBuildingModal = document.getElementById('addBuildingModal');
                const editBuildingModal = document.getElementById('editBuildingModal');
                const deleteBuildingModal = document.getElementById('deleteBuildingModal');
                const closeButtons = document.querySelectorAll('.close-modal');

                function openModal(modal, content) {
                    if (!modal || !content) {
                        console.error('Modal or content element not found');
                        return;
                    }
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                }

                function closeModal(modal, content) {
                    if (!modal || !content) {
                        console.error('Modal or content element not found');
                        return;
                    }
                    content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                    content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }

                const addBuildingBtn = document.getElementById('addBuildingBtn');
                if (addBuildingBtn) {
                    addBuildingBtn.addEventListener('click', () => {
                        openModal(addBuildingModal, addBuildingModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                document.querySelectorAll('.edit-building-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const buildingId = button.getAttribute('data-id');

                        const formAction = `{{ url('buildings/update') }}/${buildingId}`;
                        const editForm = document.getElementById('editBuildingForm');
                        if (editForm) {
                            editForm.action = formAction;
                        }

                        const idField = document.getElementById('editBuildingId');
                        const nameField = document.getElementById('editBuildingName');
                        const addressField = document.getElementById('editAddress');

                        if (idField) idField.value = buildingId;
                        if (nameField) nameField.value = button.getAttribute('data-name');
                        if (addressField) addressField.value = button.getAttribute('data-address');

                        if (editBuildingModal) {
                            const modalContent = editBuildingModal.querySelector('[id$="ModalContent"]');
                            if (modalContent) {
                                openModal(editBuildingModal, modalContent);
                            }
                        }
                    });
                });

                document.querySelectorAll('.delete-building-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const buildingId = button.getAttribute('data-id');

                        const formAction = `{{ url('buildings/delete') }}/${buildingId}`;
                        const deleteForm = document.getElementById('deleteBuildingForm');
                        if (deleteForm) {
                            deleteForm.action = formAction;
                        }

                        const idField = document.getElementById('deleteBuildingId');
                        if (idField) {
                            idField.value = buildingId;
                        }

                        if (deleteBuildingModal) {
                            const modalContent = deleteBuildingModal.querySelector('[id$="ModalContent"]');
                            if (modalContent) {
                                openModal(deleteBuildingModal, modalContent);
                            }
                        }
                    });
                });

                function clearModalForms(modal) {
                    if (!modal) return;

                    const forms = modal.querySelectorAll('form');

                    forms.forEach(form => {
                        form.reset();

                        const inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.classList.remove('border-red-500');
                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        });
                    });

                    if (modal.id === 'importBuildingModal') {
                        const fileInput = modal.querySelector('#building_excel_file');
                        if (fileInput) fileInput.value = '';

                        const fileNameContainer = modal.querySelector('#building-excel-file-name');
                        if (fileNameContainer) fileNameContainer.classList.add('hidden');

                        const previewBtn = modal.querySelector('#building-preview-btn');
                        if (previewBtn) previewBtn.disabled = true;

                        const errorDiv = modal.querySelector('#building-excel-error');
                        if (errorDiv) errorDiv.classList.add('hidden');

                        document.getElementById('import-building-step-1')?.classList.remove('hidden');
                        document.getElementById('import-building-step-2')?.classList.add('hidden');
                    }
                }

                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const modal = button.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                        clearModalForms(modal);
                    });
                });

                [addBuildingModal, editBuildingModal, deleteBuildingModal].forEach(modal => {
                    if (modal) {
                        modal.addEventListener('click', function (e) {
                            if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                                e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                                const content = this.querySelector('[id$="ModalContent"]');
                                if (content) {
                                    closeModal(this, content);
                                    clearModalForms(this);
                                }
                            }
                        });
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        [addBuildingModal, editBuildingModal, deleteBuildingModal].forEach(modal => {
                            if (modal && !modal.classList.contains('hidden')) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                if (content) {
                                    closeModal(modal, content);
                                    clearModalForms(modal);
                                }
                            }
                        });
                    }
                });

                window.showToast = function (message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.id = type + 'Notification' + Date.now();
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

                    const hasHTML = /<[a-z][\s\S]*>/i.test(message);
                    const isArray = Array.isArray(message);

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

                        if (isArray) {
                            let htmlContent = '<ul class="mt-2 ml-4 list-disc">';
                            message.forEach(item => {
                                htmlContent += `<li>${item}</li>`;
                            });
                            htmlContent += '</ul>';
                            messageContainer.innerHTML = htmlContent;
                        } else if (hasHTML) {
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

                document.getElementById('addBuildingForm')?.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const buildingNameInput = document.getElementById('add_building_name');
                    const buildingAddressInput = document.getElementById('add_building_address');

                    const isNameValid = validateField(buildingNameInput);
                    const isAddressValid = validateField(buildingAddressInput);

                    if (!isNameValid || !isAddressValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;

                        // Create FormData and send via AJAX
                        const formData = new FormData(this);

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Success
                                showToast(result.message || 'Gedung berhasil dibuat', 'success');

                                // Close modal
                                const modal = document.getElementById('addBuildingModal');
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                                clearModalForms(modal);

                                // Reload page after a short delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // Error
                                let errorMessage = result.message || 'Terjadi kesalahan saat membuat gedung';

                                // Handle validation errors
                                if (result.errors) {
                                    const errors = result.errors;
                                    Object.keys(errors).forEach(key => {
                                        const input = document.getElementById(`add_${key}`);
                                        if (input) {
                                            input.classList.add('border-red-500');
                                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                                            if (errorElement) {
                                                errorElement.textContent = errors[key][0];
                                                errorElement.classList.remove('hidden');
                                            }
                                        }
                                    });
                                }

                                showToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Terjadi kesalahan saat mengirim permintaan', 'error');
                        })
                        .finally(() => {
                            // Re-enable submit button
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        });
                    }
                });

                document.getElementById('editBuildingForm')?.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const buildingNameInput = document.getElementById('editBuildingName');
                    const buildingAddressInput = document.getElementById('editAddress');

                    const isNameValid = validateField(buildingNameInput);
                    const isAddressValid = validateField(buildingAddressInput);

                    if (!isNameValid || !isAddressValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;

                        // Create FormData and send via AJAX
                        const formData = new FormData(this);
                        formData.append('_method', 'PUT'); // For Laravel method spoofing

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Success
                                showToast(result.message || 'Gedung berhasil diubah', 'success');

                                // Close modal
                                const modal = document.getElementById('editBuildingModal');
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                                clearModalForms(modal);

                                // Reload page after a short delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // Error
                                let errorMessage = result.message || 'Terjadi kesalahan saat mengubah gedung';

                                // Handle validation errors
                                if (result.errors) {
                                    const errors = result.errors;
                                    Object.keys(errors).forEach(key => {
                                        let inputId = key;
                                        if (key === 'building_name') inputId = 'editBuildingName';
                                        else if (key === 'address') inputId = 'editAddress';

                                        const input = document.getElementById(inputId);
                                        if (input) {
                                            input.classList.add('border-red-500');
                                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                                            if (errorElement) {
                                                errorElement.textContent = errors[key][0];
                                                errorElement.classList.remove('hidden');
                                            }
                                        }
                                    });
                                }

                                showToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Terjadi kesalahan saat mengirim permintaan', 'error');
                        })
                        .finally(() => {
                            // Re-enable submit button
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        });
                    }
                });

                const deleteBuildingForm = document.getElementById('deleteBuildingForm');
                if (deleteBuildingForm) {
                    deleteBuildingForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn && !submitBtn.disabled) {
                            const originalText = submitBtn.innerHTML;

                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Menghapus...</span>
                                </div>
                            `;

                            // Create FormData and send via AJAX
                            const formData = new FormData(this);
                            formData.append('_method', 'DELETE'); // For Laravel method spoofing

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    // Success
                                    showToast(result.message || 'Gedung berhasil dihapus', 'success');

                                    // Close modal
                                    const modal = document.getElementById('deleteBuildingModal');
                                    const content = modal.querySelector('[id$="ModalContent"]');
                                    closeModal(modal, content);

                                    // Reload page after a short delay
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    // Error
                                    let errorMessage = result.message || 'Terjadi kesalahan saat menghapus gedung';
                                    showToast(errorMessage, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showToast('Terjadi kesalahan saat mengirim permintaan', 'error');
                            })
                            .finally(() => {
                                // Re-enable submit button
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            });
                        }
                    });
                }

                function validateField(field) {
                    if (!field) return true;

                    let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        if (errorElement) errorElement.classList.remove('hidden');
                        return false;
                    } else {
                        field.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                }

                const add_building_name = document.getElementById('add_building_name');
                if (add_building_name) {
                    add_building_name.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }

                const add_building_address = document.getElementById('add_building_address');
                if (add_building_address) {
                    add_building_address.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }

                const editBuildingName = document.getElementById('editBuildingName');
                if (editBuildingName) {
                    editBuildingName.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }

                const editAddress = document.getElementById('editAddress');
                if (editAddress) {
                    editAddress.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }

                // ===== IMPORT BUILDING FUNCTIONALITY =====
                const importBuildingBtn = document.getElementById('importBuildingBtn');
                const importBuildingModal = document.getElementById('importBuildingModal');

                if (importBuildingBtn) {
                    importBuildingBtn.addEventListener('click', () => {
                        openModal(importBuildingModal, importBuildingModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                const buildingExcelFile = document.getElementById('building_excel_file');
                const buildingFileNameContainer = document.getElementById('building-excel-file-name');
                const buildingFileNameText = document.getElementById('building-file-name-text');
                const removeBuildingExcel = document.getElementById('remove-building-excel');
                const buildingPreviewBtn = document.getElementById('building-preview-btn');
                const buildingExcelError = document.getElementById('building-excel-error');
                const buildingExcelLoading = document.getElementById('building-excel-loading');

                if (buildingExcelFile) {
                    buildingExcelFile.addEventListener('change', function (e) {
                        if (buildingExcelError) buildingExcelError.classList.add('hidden');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const fileExt = file.name.split('.').pop().toLowerCase();

                            if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                                if (buildingExcelError) {
                                    buildingExcelError.textContent = 'Tipe file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                                    buildingExcelError.classList.remove('hidden');
                                }
                                this.value = '';
                                if (buildingFileNameContainer) buildingFileNameContainer.classList.add('hidden');
                                if (buildingPreviewBtn) buildingPreviewBtn.disabled = true;
                                return;
                            }

                            if (buildingFileNameText) buildingFileNameText.textContent = file.name;
                            if (buildingFileNameContainer) buildingFileNameContainer.classList.remove('hidden');
                            if (buildingPreviewBtn) buildingPreviewBtn.disabled = false;
                        } else {
                            if (buildingFileNameContainer) buildingFileNameContainer.classList.add('hidden');
                            if (buildingPreviewBtn) buildingPreviewBtn.disabled = true;
                        }
                    });
                }

                if (removeBuildingExcel) {
                    removeBuildingExcel.addEventListener('click', function () {
                        if (buildingExcelFile) buildingExcelFile.value = '';
                        if (buildingFileNameContainer) buildingFileNameContainer.classList.add('hidden');
                        if (buildingPreviewBtn) buildingPreviewBtn.disabled = true;
                        if (buildingExcelError) buildingExcelError.classList.add('hidden');
                    });
                }

                if (buildingPreviewBtn) {
                    buildingPreviewBtn.addEventListener('click', function () {
                        if (!buildingExcelFile || !buildingExcelFile.files || !buildingExcelFile.files[0]) {
                            if (buildingExcelError) {
                                buildingExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                                buildingExcelError.classList.remove('hidden');
                            }
                            return;
                        }

                        const file = buildingExcelFile.files[0];

                        if (buildingExcelLoading) buildingExcelLoading.classList.remove('hidden');
                        if (buildingExcelError) buildingExcelError.classList.add('hidden');

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            try {
                                const data = new Uint8Array(e.target.result);
                                const workbook = XLSX.read(data, { type: 'array' });

                                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                                const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                                if (rows.length < 2) {
                                    throw new Error('File tidak memiliki data atau header yang hilang.');
                                }

                                processBuildingExcelData(rows);

                                if (buildingExcelLoading) buildingExcelLoading.classList.add('hidden');

                                document.getElementById('import-building-step-1').classList.add('hidden');
                                document.getElementById('import-building-step-2').classList.remove('hidden');
                            } catch (error) {
                                console.error('Excel parsing error:', error);
                                if (buildingExcelLoading) buildingExcelLoading.classList.add('hidden');
                                if (buildingExcelError) {
                                    buildingExcelError.textContent = 'Gagal memproses file: ' + error.message;
                                    buildingExcelError.classList.remove('hidden');
                                }
                            }
                        };

                        reader.onerror = function () {
                            console.error('FileReader error:', reader.error);
                            if (buildingExcelLoading) buildingExcelLoading.classList.add('hidden');
                            if (buildingExcelError) {
                                buildingExcelError.textContent = 'Gagal membaca file. Silakan coba file lainnya.';
                                buildingExcelError.classList.remove('hidden');
                            }
                        };

                        reader.readAsArrayBuffer(file);
                    });
                }

                const buildingBackBtn = document.getElementById('building-back-to-upload-btn');
                if (buildingBackBtn) {
                    buildingBackBtn.addEventListener('click', function () {
                        document.getElementById('import-building-step-2').classList.add('hidden');
                        document.getElementById('import-building-step-1').classList.remove('hidden');
                    });
                }

                function processBuildingExcelData(data) {
                    const headers = data[0];
                    const rows = data.slice(1).filter(row => row.length > 0 && row.some(cell => cell !== null && cell !== ''));

                    const headerMap = {};
                    headers.forEach((header, index) => {
                        if (header) {
                            const normalizedHeader = String(header).toLowerCase().trim()
                                .replace(/\s+/g, '_')
                                .replace(/[^a-z0-9_]/g, '');
                            headerMap[normalizedHeader] = index;
                        }
                    });

                    const previewData = [];
                    const warnings = [];

                    rows.forEach((row, rowIndex) => {
                        const item = {};

                        const getValue = (possibleNames) => {
                            for (const name of possibleNames) {
                                const normalizedName = name.toLowerCase().trim()
                                    .replace(/\s+/g, '_')
                                    .replace(/[^a-z0-9_]/g, '');

                                if (headerMap[normalizedName] !== undefined) {
                                    return row[headerMap[normalizedName]];
                                }
                            }
                            return null;
                        };

                        item.building_name = getValue(['building_name', 'buildingname', 'name', 'nama', 'nama_gedung', 'nama gedung']);
                        item.address = getValue(['address', 'alamat', 'alamat_gedung', 'location', 'lokasi']);

                        if (!item.building_name) {
                            warnings.push(`Row ${rowIndex + 2}: Nama Gedung tidak boleh kosong`);
                        }

                        item._rowNum = rowIndex + 2;

                        previewData.push(item);
                    });

                    const buildingNameMap = {};
                    previewData.forEach(item => {
                        if (item.building_name) {
                            const key = item.building_name.toLowerCase();
                            if (!buildingNameMap[key]) {
                                buildingNameMap[key] = [];
                            }
                            buildingNameMap[key].push(item._rowNum);
                        }
                    });

                    Object.entries(buildingNameMap).forEach(([key, rows]) => {
                        if (rows.length > 1) {
                            warnings.push(`Nama Gedung duplikat "${key}" ditemukan di baris: ${rows.join(', ')}`);
                        }
                    });

                    document.getElementById('building_excel_data').value = JSON.stringify(previewData);

                    showBuildingDataPreview(previewData, warnings);
                }

                function showBuildingDataPreview(data, warnings) {
                    const previewTableBody = document.getElementById('building-preview-table-body');
                    const previewCount = document.getElementById('building-preview-count');
                    const warningsContainer = document.getElementById('building-preview-warnings');
                    const warningsList = document.getElementById('building-warning-list');

                    if (!previewTableBody || !previewCount) return;

                    previewTableBody.innerHTML = '';
                    if (warningsList) warningsList.innerHTML = '';
                    if (warningsContainer) warningsContainer.classList.add('hidden');

                    previewCount.textContent = `${data.length} item ditemukan`;

                    data.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                        const indexCell = document.createElement('td');
                        indexCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                        indexCell.textContent = index + 1;
                        row.appendChild(indexCell);

                        const fields = ['building_name', 'address'];

                        fields.forEach(field => {
                            const cell = document.createElement('td');
                            cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                            cell.textContent = item[field] || '-';
                            row.appendChild(cell);
                        });

                        previewTableBody.appendChild(row);
                    });

                    if (warnings && warnings.length > 0 && warningsList && warningsContainer) {
                        warnings.forEach(warning => {
                            const li = document.createElement('li');
                            li.textContent = warning;
                            warningsList.appendChild(li);
                        });
                        warningsContainer.classList.remove('hidden');

                        const importBtn = document.getElementById('building-import-btn');
                        const hasCriticalWarnings = warnings.some(warning =>
                            warning.includes('Nama Gedung tidak boleh kosong')
                        );

                        if (importBtn && hasCriticalWarnings) {
                            importBtn.disabled = true;
                            importBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else if (importBtn) {
                            importBtn.disabled = false;
                            importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                }

                const buildingImportForm = document.getElementById('building-import-form');
                buildingImportForm?.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    const originalFileInput = document.getElementById('building_excel_file');
                    if (originalFileInput && originalFileInput.files.length > 0) {
                        formData.append('excel_file', originalFileInput.files[0]);
                    }

                    const importBtn = document.getElementById('building-import-btn');
                    const originalBtnText = importBtn.innerHTML;
                    importBtn.disabled = true;
                    importBtn.innerHTML = `
                                        <div class="flex items-center justify-center">
                                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                            <span>Memproses...</span>
                                        </div>
                                    `;

                    fetch('{{ route('buildings.import') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(data => {
                                    data.status = response.status;
                                    return data;
                                });
                            } else {
                                throw new Error('Invalid response format');
                            }
                        })
                        .then(data => {
                            importBtn.disabled = false;
                            importBtn.innerHTML = originalBtnText;

                            if (data.success === true || (data.status >= 200 && data.status < 300)) {

                                const modal = document.getElementById('importBuildingModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                showToast('Gedung berhasil diimpor!', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                console.error('Import error:', data);

                                let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                                let errorDetails = [];

                                if (data.data && data.data.errors) {

                                    if (Array.isArray(data.data.errors)) {
                                        data.data.errors.forEach(error => {
                                            if (typeof error === 'string') {
                                                errorDetails.push(error);
                                            } else if (error.message) {
                                                errorDetails.push(error.message);
                                            } else if (error.building_name && error.reason) {
                                                errorDetails.push(`"${error.building_name}" - ${error.reason}`);
                                            } else if (error.row && error.reason) {
                                                errorDetails.push(`${error.reason}`);
                                            } else if (error.reason) {
                                                errorDetails.push(error.reason);
                                            }
                                        });
                                    }
                                }

                                if (errorDetails.length > 0) {
                                    errorMessage = `${errorMessage}<ul class="mt-2 ml-4 list-disc">`;
                                    errorDetails.forEach(detail => {
                                        errorMessage += `<li>${detail}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                }

                                showToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            importBtn.disabled = false;
                            importBtn.innerHTML = originalBtnText;

                            console.error('Import fetch error:', error);

                            showToast('Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.', 'error');
                        });
                });

                document.head.insertAdjacentHTML('beforeend', `
                                    <style>
                                        @keyframes slideInRight {
                                            from { transform: translateX(100%); }
                                            to { transform: translateX(0); }
                                        }
                                        .animate-slide-in-right {
                                            animation: slideInRight 0.3s ease-out forwards;
                                        }

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

                // Column header sorting
                const sortByNameHeader = document.getElementById('sortByName');
                if (sortByNameHeader) {
                    sortByNameHeader.addEventListener('click', function () {
                        const currentSort = '{{ request()->query("sort") }}';
                        let newSort;

                        if (currentSort === 'name_asc') {
                            newSort = 'name_desc';
                        } else {
                            newSort = 'name_asc';
                        }

                        const url = new URL(window.location.href);
                        url.searchParams.set('sort', newSort);
                        url.searchParams.set('page', 1);
                        window.location.href = url.toString();
                    });
                }

                const sortByAddressHeader = document.getElementById('sortByAddress');
                if (sortByAddressHeader) {
                    sortByAddressHeader.addEventListener('click', function () {
                        const currentSort = '{{ request()->query("sort") }}';
                        let newSort;

                        if (currentSort === 'address_asc') {
                            newSort = 'address_desc';
                        } else {
                            newSort = 'address_asc';
                        }

                        const url = new URL(window.location.href);
                        url.searchParams.set('sort', newSort);
                        url.searchParams.set('page', 1);
                        window.location.href = url.toString();
                    });
                }
            });
        </script>
    @endpush

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endsection
