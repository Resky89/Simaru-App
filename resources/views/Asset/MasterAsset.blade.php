@extends('Layout.app')

@section('title', 'Aset Master')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Asset Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ASET MASTER</h1>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('asset-master:import'))
                                <button id="importMasterAssetBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            @if(hasPermission('asset-master:export'))
                                <button id="exportBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-base">Ekspor PDF</span>
                                </button>
                            @endif
                            @if(hasPermission('asset-master:create'))
                                <button id="addMasterAssetBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-base">Tambah Aset Master</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput"
                                placeholder="Cari berdasarkan nama aset..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <select id="assetTypeFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Pilih Tipe</option>
                                <option value="">Semua Tipe</option>
                                <option value="medical">Medis</option>
                                <option value="non_medical">Non Medis</option>
                            </select>

                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled>Pilih Urutan Pengurutan</option>
                                <option value="newest" {{ request()->query('sort') === 'newest' || !request()->query('sort') ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request()->query('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ request()->query('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="name_desc" {{ request()->query('sort') === 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                                <option value="code_asc" {{ request()->query('sort') === 'code_asc' ? 'selected' : '' }}>Kode (A-Z)</option>
                                <option value="code_desc" {{ request()->query('sort') === 'code_desc' ? 'selected' : '' }}>Kode (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Asset Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByCode">
                                            <span class="text-xs">Kode Aset Master</span>
                                            <span class="sort-icon">
                                                @php
                                                    $currentSort = request()->query('sort');
                                                    $codeIconType = 'none';

                                                    if ($currentSort === 'code_asc') {
                                                        $codeIconType = 'asc';
                                                    } elseif ($currentSort === 'code_desc') {
                                                        $codeIconType = 'desc';
                                                    }
                                                @endphp

                                                @if($codeIconType === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($codeIconType === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByName">
                                            <span class="text-xs">Nama Aset</span>
                                            <span class="sort-icon">
                                                @php
                                                    $nameIconType = 'none';

                                                    if ($currentSort === 'name_asc') {
                                                        $nameIconType = 'asc';
                                                    } elseif ($currentSort === 'name_desc') {
                                                        $nameIconType = 'desc';
                                                    }
                                                @endphp

                                                @if($nameIconType === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($nameIconType === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kategori</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Penyusutan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Kalibrasi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($masterAssets) && count($masterAssets) > 0)
                                    @foreach($masterAssets ?? [] as $asset)
                                        <tr data-asset-id="{{ $asset['asset_master_id'] ?? '' }}">
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                {{ $asset['asset_master_code'] ?? 'N/A' }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_name'] ?? '-' }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                @if(isset($asset['asset_type']))
                                                    @if(strtolower($asset['asset_type']) == 'medical')
                                                        Medis
                                                    @elseif(strtolower($asset['asset_type']) == 'non_medical')
                                                        Non Medis
                                                    @else
                                                        {{ $asset['asset_type'] }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                {{ $asset['subcategory_name'] ?? '-' }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['description'] ?? '-' }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                @if(isset($asset['is_depreciable']) && $asset['is_depreciable'])
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ya</span>
                                                @else
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                @if(isset($asset['needs_calibration']) && $asset['needs_calibration'])
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Ya</span>
                                                @else
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="p-3 border-t border-[#EEF1F4] text-center">
                                                <div class="flex justify-center items-center space-x-2">
                                                    <a href="{{ route('view-asset-master', $asset['asset_master_id'] ?? '') }}"
                                                        class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                        title="Lihat Detail Aset Master">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    @if(hasPermission('asset-master:edit'))
                                                        <button
                                                            class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-asset-btn"
                                                            data-id="{{ $asset['asset_master_id'] ?? '' }}"
                                                            title="Edit Aset Master">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if(hasPermission('asset-master:delete'))
                                                        <button
                                                            class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-asset-btn"
                                                            data-id="{{ $asset['asset_master_id'] ?? '' }}"
                                                            data-name="{{ $asset['asset_name'] ?? '' }}"
                                                            title="Hapus Aset Master">
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
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada aset
                                            master yang ditemukan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for Assets -->
                    @if(isset($masterAssets_pagination) && $masterAssets_pagination)
                        <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($masterAssets_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changePage({{ ($masterAssets_pagination['current_page'] ?? 1) - 1 }})" {{ ($masterAssets_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Sebelumnya
                                </button>

                                <!-- Replace with improved pagination that shows first/last pages with ellipses -->
                                <div class="flex gap-2">
                                    @php
                                        $currentPage = $masterAssets_pagination['current_page'] ?? 1;
                                        $lastPage = $masterAssets_pagination['last_page'] ?? 1;
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
                                    class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($masterAssets_pagination['current_page'] ?? 1) >= ($masterAssets_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changePage({{ ($masterAssets_pagination['current_page'] ?? 1) + 1 }})" {{ ($masterAssets_pagination['current_page'] ?? 1) >= ($masterAssets_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
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
                                    @if(isset($masterAssets_pagination) && is_array($masterAssets_pagination))
                                        @php
                                            $currentPage = $masterAssets_pagination['current_page'] ?? 1;
                                            $perPage = $masterAssets_pagination['per_page'] ?? 10;
                                            $total = $masterAssets_pagination['total'] ?? count($masterAssets ?? []);
                                            $from = ($currentPage - 1) * $perPage + 1;
                                            $to = min($currentPage * $perPage, $total);
                                        @endphp
                                        Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                    @else
                                        Menampilkan 1 sampai {{ count($masterAssets ?? []) }} dari {{ count($masterAssets ?? []) }}
                                        data
                                    @endif
                                </span>
                                <select id="perPageSelect"
                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                    onchange="changeMasterAssetPerPage(this.value)">
                                    <option value="10" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                    <option value="25" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                    <option value="50" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                    <option value="100" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Master Asset Modal -->
    @if(hasPermission('asset-master:create'))
        <div id="addMasterAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addMasterAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH ASSET MASTER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="createMasterAssetForm" action="{{ route('asset-master.store') }}" method="POST"
                            data-no-loading enctype="multipart/form-data">
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Asset Information Section -->
                                    <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Aset</h3>

                                    <!-- Image upload - Improved visibility -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#213268]">Gambar Aset <span
                                                class="text-sm font-normal text-[#666666]">(Unggah gambar referensi untuk aset
                                                ini)</span></label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- Image preview -->
                                            <div id="image-preview" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded"
                                                        alt="Selected Image">
                                                    <button type="button" id="remove-image"
                                                        class="remove-image-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Seret gambar atau <span
                                                        class="text-[#213268] font-semibold">pilih file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png
                                                    (Ukuran maks: 5MB)
                                                </p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                                    untuk memilih file</p>
                                            </div>
                                            <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Basic Asset Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Nama Aset <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="asset_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Nama aset">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus diisi
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Tipe Aset <span
                                                    class="text-red-500">*</span></label>
                                            <select name="asset_type" id="asset_type"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <option value="" disabled selected>Pilih Tipe Aset</option>
                                                <option value="medical">Medis</option>
                                                <option value="non_medical">Non Medis</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus dipilih
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Kategori <span
                                                class="text-red-500">*</span> <span class="text-xs text-blue-600">(Pilih
                                                tipe aset terlebih dahulu)</span></label>
                                        <div class="custom-select-container relative">
                                            <input type="text"
                                                class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] disabled:bg-gray-100 disabled:cursor-not-allowed"
                                                placeholder="Pilih tipe aset terlebih dahulu" disabled
                                                id="subcategory_search">
                                            <input type="hidden" name="subcategory_id" id="subcategory_id">
                                            <div
                                                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer dropdown-icon">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                            <div
                                                class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 text-center text-gray-500" id="subcategory-loading-message">
                                                    Pilih tipe aset terlebih dahulu</div>
                                                <!-- Subcategories will be loaded dynamically based on asset_type -->
                                            </div>
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                        <textarea name="description"
                                            class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                            placeholder="Deskripsi aset"></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Depreciation Toggle Switch -->
                                        <div class="flex items-center justify-between">
                                            <label for="is_depreciable" class="text-base font-semibold text-[#666666]">Aktifkan
                                                Penyusutan Aset</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_depreciable" id="is_depreciable"
                                                    class="sr-only peer depreciation-toggle" value="true">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                                    peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                                    after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-[#213268]"></div>
                                                <span
                                                    class="ml-2 text-sm font-medium text-gray-900 depreciation-status">Tidak</span>
                                            </label>
                                        </div>

                                        <!-- Calibration Toggle Switch -->
                                        <div class="flex items-center justify-between">
                                            <label for="needs_calibration"
                                                class="text-base font-semibold text-[#666666]">Kalibrasi</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="needs_calibration" id="needs_calibration"
                                                    class="sr-only peer calibration-toggle" value="true">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                                    peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                                    after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-[#213268]"></div>
                                                <span
                                                    class="ml-2 text-sm font-medium text-gray-900 calibration-status">Tidak</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Master Asset Modal -->
    @if(hasPermission('asset-master:edit'))
        <div id="editMasterAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editMasterAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ASSET MASTER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="editMasterAssetForm" method="POST" data-no-loading enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <!-- Loading indicator -->
                                <div class="text-center" id="edit-loading">
                                    <div
                                        class="inline-block w-8 h-8 border-4 border-[#213268] border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <p class="mt-2 text-gray-600">Memuat data aset...</p>
                                </div>

                                <div id="edit-form-content" class="space-y-4 hidden">
                                    <!-- Asset Information Section -->
                                    <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Aset</h3>

                                    <!-- Image upload -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Gambar Aset</label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- Current image preview -->
                                            <div id="edit-image-container" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded"
                                                        alt="Asset Image">
                                                    <button type="button"
                                                        class="remove-image-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Seret gambar atau <span
                                                        class="text-blue-600">pilih file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png
                                                    (Ukuran maks: 5MB)
                                                </p>
                                            </div>
                                            <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Basic Asset Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Nama Aset <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="asset_name" id="edit_asset_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Nama aset">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus diisi
                                            </div>
                                        </div>

                                        <!-- Asset Type Field -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Tipe Aset <span
                                                    class="text-red-500">*</span></label>
                                            <select name="asset_type" id="edit_asset_type"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <option value="" disabled selected>Pilih Tipe Aset</option>
                                                <option value="non_medical">Non Medis</option>
                                                <option value="medical">Medis</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus dipilih
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subcategory Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Kategori <span
                                                class="text-red-500">*</span> <span class="text-xs text-blue-600">(Pilih
                                                tipe aset terlebih dahulu)</span></label>
                                        <div class="custom-select-container relative">
                                            <input type="text"
                                                class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] disabled:bg-gray-100 disabled:cursor-not-allowed"
                                                placeholder="Pilih tipe aset terlebih dahulu" disabled
                                                id="edit_subcategory_search">
                                            <input type="hidden" name="subcategory_id" id="edit_subcategory_id">
                                            <div
                                                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer dropdown-icon">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                            <div
                                                class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 text-center text-gray-500"
                                                    id="edit-subcategory-loading-message">Pilih tipe aset terlebih dahulu
                                                </div>
                                                <!-- Subcategories will be loaded dynamically based on asset_type -->
                                            </div>
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                        <textarea name="description" id="edit_description"
                                            class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                            placeholder="Deskripsi aset"></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Depreciation Toggle Switch -->
                                        <div class="flex items-center justify-between">
                                            <label for="edit_is_depreciable"
                                                class="text-base font-semibold text-[#666666]">Aktifkan Penyusutan Aset</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_depreciable" id="edit_is_depreciable"
                                                    class="sr-only peer depreciation-toggle" value="1">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                                    peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                                    after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-[#213268]"></div>
                                                <span
                                                    class="ml-2 text-sm font-medium text-gray-900 depreciation-status">Tidak</span>
                                            </label>
                                        </div>

                                        <!-- Calibration Toggle Switch -->
                                        <div class="flex items-center justify-between">
                                            <label for="edit_needs_calibration"
                                                class="text-base font-semibold text-[#666666]">Kalibrasi</label>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="needs_calibration" id="edit_needs_calibration"
                                                    class="sr-only peer calibration-toggle" value="1">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                                    peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                                    after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                    peer-checked:bg-[#213268]"></div>
                                                <span
                                                    class="ml-2 text-sm font-medium text-gray-900 calibration-status">Tidak</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="edit-submit-btn"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Perbarui
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if(hasPermission('asset-master:delete'))
        <div id="deleteModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS ASSET MASTER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="delete-form" method="POST" data-no-loading>
                            @csrf
                            @method('DELETE')
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus aset
                                            master ini? Aksi ini tidak dapat dibatalkan.</p>
                                        <p id="delete-asset-name" class="text-base font-semibold text-center mt-2"></p>
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

    <!-- Import Master Asset Modal -->
    @if(hasPermission('asset-master:import'))
        <div id="importMasterAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="importMasterAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPORT ASSET MASTER</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Step 1: File Selection -->
                        <div id="import-step-1" class="block">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Import Instructions -->
                                    <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                        <p class="font-medium text-blue-600 mb-2">Instruksi Import:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Gunakan format Excel template untuk mengimpor</li>
                                            <li>Kolom yang dibutuhkan: Nama Aset, Tipe Aset, Kategori, Deskripsi</li>
                                            <li>Maksimal 100 data per import</li>
                                            <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                        </ul>
                                        <div class="mt-3 flex justify-end">
                                            <a href="{{ asset('docs/ImportAssetMasterTemplate.xlsx') }}" download
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
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
                                            <div id="excel-file-name" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <div class="flex items-center">
                                                        <svg class="w-6 h-6 text-green-600 mr-2"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span id="file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="remove-excel"
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
                                                <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Seret file Excel atau <span
                                                        class="text-[#213268] font-semibold">pilih file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                                    untuk memilih file</p>
                                            </div>
                                            <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Error Message -->
                                    <div id="excel-error" class="hidden text-red-500 text-sm"></div>

                                    <!-- Loading Indicator -->
                                    <div id="excel-loading" class="hidden text-center py-2">
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
                                        <button type="button" id="preview-btn" disabled
                                            class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Pratinjau Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Data Preview -->
                        <div id="import-step-2" class="hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Preview Header -->
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                        <span class="text-sm text-gray-500" id="preview-count">0 data ditemukan</span>
                                    </div>

                                    <!-- Preview Table -->
                                    <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                        <table class="w-full">
                                            <thead class="sticky top-0 bg-[#213268] text-white">
                                                <tr>
                                                    <th class="p-3 text-left text-xs font-semibold">No</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Nama Aset</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Tipe Aset</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Kategori</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Deskripsi</th>
                                                    <th class="p-3 text-center text-xs font-semibold">Penyusutan</th>
                                                    <th class="p-3 text-center text-xs font-semibold">Kalibrasi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="preview-table-body">
                                                <!-- Preview data will be inserted here -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Warning/Error Messages -->
                                    <div id="preview-warnings"
                                        class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                        <p class="font-medium mb-2">Peringatan:</p>
                                        <ul class="list-disc pl-5" id="warning-list">
                                            <!-- Warning messages will be inserted here -->
                                        </ul>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <button type="button" id="back-to-upload-btn"
                                            class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                            Kembali
                                        </button>
                                        <form action="{{ route('asset-master.import') }}" method="POST" id="import-form"
                                            class="w-2/3" data-no-loading enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="excel_data" id="excel_data">
                                            <button type="submit" id="import-btn"
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('asset-master:create'))
                const addButtons = document.querySelectorAll('#addMasterAssetBtn');
                addButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('asset-master:import'))
                    const importButtons = document.querySelectorAll('#importMasterAssetBtn, #preview-btn');
                    importButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('asset-master:edit'))
                    const editButtons = document.querySelectorAll('.edit-asset-btn');
                    editButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('asset-master:delete'))
                    const deleteButtons = document.querySelectorAll('.delete-asset-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif

            @if(session('error') || isset($error))
                showToast('{!! session('error') ?? $error ?? 'Terjadi kesalahan' !!}', 'error');
            @endif

            const openModal = function (modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            };

            const closeModal = function (modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };

            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            function initCustomSelects() {
                document.querySelectorAll('.custom-select-container').forEach(container => {
                    const searchInput = container.querySelector('.search-input');
                    const hiddenInput = container.querySelector('input[type="hidden"]');
                    const optionsContainer = container.querySelector('.options-container');
                    const dropdownIcon = container.querySelector('.dropdown-icon');

                    if (!searchInput || !hiddenInput || !optionsContainer) return;

                    const toggleDropdown = (e) => {
                        e.stopPropagation();

                        if (searchInput.disabled) {
                            const formId = container.closest('form').id;
                            const assetTypeId = formId === 'editMasterAssetForm' ? 'edit_asset_type' : 'asset_type';
                            const assetTypeSelect = document.getElementById(assetTypeId);

                            if (assetTypeSelect) {
                                assetTypeSelect.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                                setTimeout(() => {
                                    assetTypeSelect.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                                }, 1000);
                            }
                            return;
                        }

                        const isDropdownVisible = !optionsContainer.classList.contains('hidden');

                        if (isDropdownVisible) {
                            optionsContainer.classList.add('hidden');
                        } else {
                            if (hiddenInput.id.includes('subcategory')) {
                                const assetTypeId = hiddenInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                                const assetTypeSelect = document.getElementById(assetTypeId);

                                if (assetTypeSelect && assetTypeSelect.value) {
                                    fetchCategories(assetTypeSelect.value, searchInput.value.trim() || " ", hiddenInput.id);
                                } else {
                                    optionsContainer.innerHTML = '<div class="p-2 text-center text-gray-500">Pilih tipe aset terlebih dahulu</div>';
                                }
                            }

                            optionsContainer.classList.remove('hidden');
                        }
                    };

                    searchInput.addEventListener('click', toggleDropdown);
                    if (dropdownIcon) {
                        dropdownIcon.addEventListener('click', toggleDropdown);
                    }

                    searchInput.addEventListener('input', debounce(function () {
                        const searchValue = this.value.trim();

                        if (this.disabled) return;

                        if (hiddenInput.id.includes('subcategory')) {
                            const assetTypeId = hiddenInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                            const assetTypeSelect = document.getElementById(assetTypeId);

                            if (assetTypeSelect && assetTypeSelect.value) {
                                fetchCategories(assetTypeSelect.value, searchValue || " ", hiddenInput.id);
                                optionsContainer.classList.remove('hidden');
                            }
                        }
                    }, 300));

                    document.addEventListener('click', function (e) {
                        if (!container.contains(e.target)) {
                            optionsContainer.classList.add('hidden');
                        }
                    });

                    searchInput.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                });
            }

            function setSelectValue(selectId, value, displayText = null) {
                if (!value) return;

                const hiddenInput = document.getElementById(selectId);
                if (!hiddenInput) return;

                const container = hiddenInput.closest('.custom-select-container');
                const searchInput = container.querySelector('.search-input');

                hiddenInput.value = value.toString();

                if (displayText) {
                    searchInput.value = displayText;

                    searchInput.classList.remove('border-red-500');
                    const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');

                    const event = new Event('change', { bubbles: true });
                    hiddenInput.dispatchEvent(event);
                    return;
                }

                const options = container.querySelectorAll('.option');
                let foundOption = null;

                Array.from(options).forEach(option => {
                    if (option.dataset.value === value.toString()) {
                        foundOption = option;
                    }
                });

                if (foundOption) {
                    searchInput.value = foundOption.textContent.trim();

                    foundOption.style.display = '';

                    setTimeout(() => {
                        foundOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                } else {
                    if (selectId === 'edit_subcategory_id') {
                        fetch(`/categories/${value}?json=true`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.subcategory_name) {
                                    searchInput.value = data.subcategory_name;
                                } else {
                                    searchInput.value = value.toString();
                                    console.warn(`Could not find subcategory name for ID ${value}`);
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching subcategory details:', error);
                                searchInput.value = value.toString();
                            });
                    } else {
                        console.warn(`Option with value "${value}" not found for ${selectId}`);
                        searchInput.value = value.toString();
                    }
                }

                searchInput.classList.remove('border-red-500');
                const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');

                const event = new Event('change', { bubbles: true });
                hiddenInput.dispatchEvent(event);
            }

            initCustomSelects();

            function loadSubcategories(assetType, targetElementId, loadingMessageId, searchTerm = '', silentLoad = false) {
                if (typeof fetchCategories === 'function') {
                    fetchCategories(assetType, searchTerm, targetElementId);
                    return;
                }

                console.warn('fetchCategories not available, using legacy loadSubcategories');

                const container = document.querySelector(`#${targetElementId}`).closest('.custom-select-container');
                const optionsContainer = container.querySelector('.options-container');

                optionsContainer.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat kategori...</div>';

                if (!silentLoad) {
                    optionsContainer.style.display = 'block';
                }

                if (!assetType) {
                    optionsContainer.innerHTML = '<div class="p-2 text-sm text-gray-500">Pilih tipe aset terlebih dahulu</div>';
                    return;
                }
            }

            document.getElementById('asset_type')?.addEventListener('change', function () {
                const selectedType = this.value;
                const container = document.querySelector('#subcategory_id').closest('.custom-select-container');
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');

                hiddenInput.value = '';
                searchInput.value = '';

                if (selectedType) {
                    searchInput.disabled = false;
                    searchInput.placeholder = "Cari kategori...";

                    fetchCategories(selectedType, '', 'subcategory_id');
                } else {
                    searchInput.disabled = true;
                    searchInput.placeholder = "Pilih tipe aset terlebih dahulu";
                }
            });

            document.getElementById('edit_asset_type')?.addEventListener('change', function () {
                const selectedType = this.value;
                const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');

                hiddenInput.value = '';
                searchInput.value = '';

                if (selectedType) {
                    searchInput.disabled = false;
                    searchInput.placeholder = "Cari kategori...";

                    fetchCategories(selectedType, '', 'edit_subcategory_id');
                } else {
                    searchInput.disabled = true;
                    searchInput.placeholder = "Pilih tipe aset terlebih dahulu";
                }
            });

            document.getElementById('addMasterAssetBtn')?.addEventListener('click', function () {
                const modal = document.getElementById('addMasterAssetModal');
                const content = document.getElementById('addMasterAssetModalContent');
                if (modal && content) {
                    openModal(modal, content);
                }
            });

            document.querySelectorAll('.edit-asset-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const originalFetchComplete = function (asset) {
                        if (asset.asset_type) {
                            const subcategoryContainer = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                            const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                            subcategorySearchInput.disabled = false;
                            subcategorySearchInput.placeholder = "Cari kategori...";

                            fetchCategories(asset.asset_type, '', 'edit_subcategory_id');
                        } else {
                            const subcategoryContainer = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                            const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                            subcategorySearchInput.disabled = true;
                            subcategorySearchInput.placeholder = "Pilih tipe aset terlebih dahulu";
                        }
                    };

                    const originalFetch = window.fetch;
                    window.fetch = function (...args) {
                        const url = args[0];
                        if (typeof url === 'string' && url.includes('/asset-master/') && url.includes(button.dataset.id)) {
                            return originalFetch.apply(this, args)
                                .then(response => response.json())
                                .then(data => {
                                    if (data && data.masterAsset) {
                                        originalFetchComplete(data.masterAsset);
                                    }

                                    const newResponse = new Response(JSON.stringify(data), {
                                        status: 200,
                                        headers: { 'Content-Type': 'application/json' }
                                    });
                                    return newResponse;
                                });
                        }
                        return originalFetch.apply(this, args);
                    };

                    setTimeout(() => {
                        window.fetch = originalFetch;
                    }, 5000);
                });
            });

            document.getElementById('exportBtn')?.addEventListener('click', () => {
                const url = new URL(window.location.href);
                const searchParams = url.searchParams;

                const exportUrl = "{{ route('export-asset-master-pdf') }}?" + searchParams.toString();

                window.open(exportUrl, '_blank');
            });

            document.querySelectorAll('.edit-asset-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const assetId = this.dataset.id;
                    const editModal = document.getElementById('editMasterAssetModal');
                    const editContent = document.getElementById('editMasterAssetModalContent');
                    const loadingIndicator = document.getElementById('edit-loading');
                    const formContent = document.getElementById('edit-form-content');
                    const submitBtn = document.getElementById('edit-submit-btn');

                    if (editModal && editContent) {
                        document.getElementById('editMasterAssetForm').reset();
                        document.getElementById('editMasterAssetForm').action = `{{ url('asset-master') }}/${assetId}`;

                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        if (formContent) formContent.classList.add('hidden');
                        if (submitBtn) submitBtn.disabled = true;

                        openModal(editModal, editContent);

                        fetch(`{{ url('asset-master') }}/${assetId}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }

                                const contentType = response.headers.get('content-type');
                                if (!contentType || !contentType.includes('application/json')) {
                                    throw new Error(`Expected JSON response but got ${contentType}`);
                                }

                                return response.json();
                            })
                            .then(data => {
                                if (!data || !data.masterAsset) {
                                    throw new Error('Invalid response data structure');
                                }

                                const asset = data.masterAsset;
                                document.getElementById('edit_asset_name').value = asset.asset_name || '';
                                document.getElementById('edit_description').value = asset.description || '';

                                if (asset.asset_type) {
                                    document.getElementById('edit_asset_type').value = asset.asset_type;

                                    const subcategoryContainer = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                                    const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                                    subcategorySearchInput.disabled = false;
                                    subcategorySearchInput.placeholder = "Cari kategori...";

                                    fetchCategories(asset.asset_type, '', 'edit_subcategory_id');

                                    if (asset.subcategory_id && asset.subcategory_name) {
                                        setSelectValue('edit_subcategory_id', asset.subcategory_id, asset.subcategory_name);
                                    }
                                }

                                document.getElementById('edit_is_depreciable').checked = Boolean(asset.is_depreciable);
                                document.getElementById('edit_needs_calibration').checked = Boolean(asset.needs_calibration);

                                const depreciationStatus = document.querySelector('#editMasterAssetModal .depreciation-status');
                                if (depreciationStatus) {
                                    depreciationStatus.textContent = asset.is_depreciable ? 'Ya' : 'Tidak';
                                }

                                const calibrationStatus = document.querySelector('#editMasterAssetModal .calibration-status');
                                if (calibrationStatus) {
                                    calibrationStatus.textContent = asset.needs_calibration ? 'Ya' : 'Tidak';
                                }

                                if (asset.reference_image_path) {
                                    const imgElement = document.querySelector('#edit-image-container img');
                                    if (imgElement) {
                                        try {
                                            const baseUrl = "https://web-magangunbin2025.rsummi.co.id/api/public";

                                            let imageUrl = asset.reference_image_path;

                                            if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('//')) {
                                                if (imageUrl.startsWith('/')) {
                                                    imageUrl = imageUrl.substring(1);
                                                }
                                                imageUrl = `${baseUrl}/${imageUrl}`;
                                            }

                                            imgElement.src = imageUrl;
                                            document.getElementById('edit-image-container').classList.remove('hidden');

                                            document.getElementById('edit-image-preview').classList.add('hidden');

                                            const fileInput = document.getElementById('edit_image_file');
                                            if (fileInput) {
                                                fileInput.value = '';
                                            }

                                            imgElement.onerror = function () {
                                                console.error(`Failed to load image from URL: ${imageUrl}`);
                                                document.getElementById('edit-image-container').classList.add('hidden');
                                            };
                                        } catch (error) {
                                            console.error('Error setting image URL:', error);
                                        }
                                    }
                                } else {
                                    const currentImage = document.getElementById('edit-image-container');
                                    if (currentImage) currentImage.classList.add('hidden');
                                }

                                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                                if (formContent) formContent.classList.remove('hidden');
                                if (submitBtn) submitBtn.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error fetching asset data:', error);

                                if (loadingIndicator) loadingIndicator.classList.add('hidden');

                                if (formContent) {
                                    formContent.innerHTML = `
                                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                        <p class="font-medium">Gagal memuat data aset</p>
                                        <p>${error.message}</p>
                                        <p class="mt-2">Silakan coba lagi atau hubungi dukungan jika masalah tetap terjadi.</p>
                                    </div>
                                    <button type="button" class="close-modal w-full h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300">
                                        Tutup
                                    </button>
                                `;
                                    formContent.classList.remove('hidden');
                                }

                                formContent.querySelectorAll('.close-modal').forEach(btn => {
                                    btn.addEventListener('click', function () {
                                        closeModal(editModal, editContent);
                                    });
                                });
                            });
                    }
                });
            });

            document.querySelectorAll('.delete-asset-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const assetId = this.dataset.id;
                    const assetName = this.dataset.name;
                    const deleteModal = document.getElementById('deleteModal');
                    const deleteContent = document.getElementById('deleteModalContent');

                    if (deleteModal && deleteContent) {
                        document.getElementById('delete-asset-name').textContent = assetName;
                        document.getElementById('delete-form').action = `{{ url('asset-master') }}/${assetId}`;
                        openModal(deleteModal, deleteContent);
                    }
                });
            });

            document.getElementById('delete-form')?.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.action;

                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menghapus...</span></div>';

                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw errorData;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            const deleteModal = document.getElementById('deleteModal');
                            const deleteContent = document.getElementById('deleteModalContent');
                            if (deleteModal && deleteContent) {
                                closeModal(deleteModal, deleteContent);
                            }

                            showToast(data.message || 'Aset master berhasil dihapus!', 'success');

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        })
                        .catch(error => {
                            console.error('Error deleting asset:', error);

                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;

                            showToast(error, 'error');
                        });
                }
            });

            function resetAddMasterAssetForm() {
                const form = document.getElementById('createMasterAssetForm');
                if (form) {
                    form.reset();

                    const imagePreview = document.getElementById('image-preview');
                    if (imagePreview) {
                        imagePreview.classList.add('hidden');
                    }

                    form.querySelectorAll('.custom-select-container').forEach(container => {
                        const searchInput = container.querySelector('.search-input');
                        const hiddenInput = container.querySelector('input[type="hidden"]');
                        if (searchInput) searchInput.value = '';
                        if (hiddenInput) hiddenInput.value = '';
                    });

                    form.querySelectorAll('.error-message').forEach(error => {
                        error.classList.add('hidden');
                    });

                    form.querySelectorAll('.depreciation-status, .calibration-status').forEach(status => {
                        status.textContent = 'No';
                    });
                }
            }

            function resetEditMasterAssetForm() {
                const form = document.getElementById('editMasterAssetForm');
                if (form) {
                    form.reset();

                    const imageContainer = document.getElementById('edit-image-container');
                    if (imageContainer) {
                        imageContainer.classList.add('hidden');
                    }

                    const removeImageInput = form.querySelector('input[name="remove_image"]');
                    if (removeImageInput) {
                        removeImageInput.remove();
                    }

                    form.querySelectorAll('.custom-select-container').forEach(container => {
                        const searchInput = container.querySelector('.search-input');
                        const hiddenInput = container.querySelector('input[type="hidden"]');
                        if (searchInput) searchInput.value = '';
                        if (hiddenInput) hiddenInput.value = '';
                    });

                    form.querySelectorAll('.error-message').forEach(error => {
                        error.classList.add('hidden');
                    });

                    form.querySelectorAll('.depreciation-status, .calibration-status').forEach(status => {
                        status.textContent = 'No';
                    });

                    const loadingIndicator = document.getElementById('edit-loading');
                    const formContent = document.getElementById('edit-form-content');
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    if (formContent) formContent.classList.add('hidden');
                }
            }

            function resetImportModal() {
                document.getElementById('import-step-1')?.classList.remove('hidden');
                document.getElementById('import-step-2')?.classList.add('hidden');

                const fileInput = document.getElementById('excel_file');
                if (fileInput) fileInput.value = '';

                const previewContainer = document.getElementById('excel-file-name');
                if (previewContainer) previewContainer.classList.add('hidden');

                document.getElementById('excel-error')?.classList.add('hidden');
                document.getElementById('excel-loading')?.classList.add('hidden');
                document.getElementById('preview-btn')?.setAttribute('disabled', 'disabled');
            }

            function resetDeleteModal() {
                const form = document.getElementById('delete-form');
                if (form) {
                    form.reset();
                    document.getElementById('delete-asset-name').textContent = '';
                }
            }

            document.querySelectorAll('.close-modal').forEach(closeButton => {
                closeButton.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="Content"]');

                    if (modal.id === 'addMasterAssetModal') {
                        resetAddMasterAssetForm();
                    } else if (modal.id === 'editMasterAssetModal') {
                        resetEditMasterAssetForm();
                    } else if (modal.id === 'importMasterAssetModal') {
                        resetImportModal();
                    } else if (modal.id === 'deleteModal') {
                        resetDeleteModal();
                    }

                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        const content = this.querySelector('[id$="Content"]');

                        if (this.id === 'addMasterAssetModal') {
                            resetAddMasterAssetForm();
                        } else if (this.id === 'editMasterAssetModal') {
                            resetEditMasterAssetForm();
                        } else if (this.id === 'importMasterAssetModal') {
                            resetImportModal();
                        } else if (this.id === 'deleteModal') {
                            resetDeleteModal();
                        }

                        if (content) {
                            closeModal(this, content);
                        }
                    }
                });
            });

            document.getElementById('image_file')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgElement = document.querySelector('#image-preview img');
                        imgElement.src = e.target.result;
                        document.getElementById('image-preview').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.getElementById('edit_image_file')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgElement = document.querySelector('#edit-image-container img');
                        imgElement.src = e.target.result;
                        document.getElementById('edit-image-container').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.getElementById('remove-image')?.addEventListener('click', function () {
                const fileInput = document.getElementById('image_file');
                const previewContainer = document.getElementById('image-preview');

                if (fileInput) {
                    fileInput.value = '';
                }

                if (previewContainer) {
                    previewContainer.classList.add('hidden');
                }
            });

            document.querySelector('#edit-image-container .remove-image-btn')?.addEventListener('click', function () {
                const fileInput = document.getElementById('edit_image_file');
                const imageContainer = document.getElementById('edit-image-container');

                if (fileInput) {
                    fileInput.value = '';
                }

                if (imageContainer) {
                    imageContainer.classList.add('hidden');
                }

                const removeImageInput = document.createElement('input');
                removeImageInput.type = 'hidden';
                removeImageInput.name = 'remove_image';
                removeImageInput.value = '1';

                const form = document.getElementById('editMasterAssetForm');
                if (form && !form.querySelector('input[name="remove_image"]')) {
                    form.appendChild(removeImageInput);
                }
            });

            document.getElementById('is_depreciable')?.addEventListener('change', function () {
                document.querySelectorAll('.depreciation-status')[0].textContent = this.checked ? 'Ya' : 'Tidak';
            });

            document.getElementById('needs_calibration')?.addEventListener('change', function () {
                document.querySelectorAll('.calibration-status')[0].textContent = this.checked ? 'Ya' : 'Tidak';
            });

            document.getElementById('edit_is_depreciable')?.addEventListener('change', function () {
                document.querySelectorAll('.depreciation-status')[1].textContent = this.checked ? 'Ya' : 'Tidak';
            });

            document.getElementById('edit_needs_calibration')?.addEventListener('change', function () {
                document.querySelectorAll('.calibration-status')[1].textContent = this.checked ? 'Ya' : 'Tidak';
            });

            window.changePage = function (page) {
                if (page < 1) return;

                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            };

            window.changePerPage = function (perPage) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', perPage);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            };

            function applyFilters() {
                const searchValue = document.getElementById('searchInput')?.value.trim() || '';
                const typeValue = document.getElementById('assetTypeFilter')?.value || '';
                const sortValue = document.getElementById('sortOrder')?.value || '';

                const url = new URL(window.location.href);

                ['search', 'type', 'sort', 'page'].forEach(param => {
                    url.searchParams.delete(param);
                });

                if (searchValue) url.searchParams.set('search', searchValue);
                if (typeValue) url.searchParams.set('type', typeValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                url.searchParams.set('page', 1);

                window.location.href = url.toString();
            }

            let searchTimeout;
            document.getElementById('searchInput')?.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            document.getElementById('assetTypeFilter')?.addEventListener('change', applyFilters);
            document.getElementById('sortOrder')?.addEventListener('change', applyFilters);

            const urlParams = new URLSearchParams(window.location.search);
            if (document.getElementById('searchInput')) {
                document.getElementById('searchInput').value = urlParams.get('search') || '';
            }
            if (document.getElementById('assetTypeFilter')) {
                const typeValue = urlParams.get('type');
                if (typeValue) {
                    document.getElementById('assetTypeFilter').value = typeValue;
                }
            }
            if (document.getElementById('sortOrder')) {
                const sortValue = urlParams.get('sort');
                if (sortValue) {
                    document.getElementById('sortOrder').value = sortValue;
                }
            }

            window.changePage = function (page) {
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            };

            window.changePerPage = function (perPage) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', perPage);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            };

            document.getElementById('importMasterAssetBtn')?.addEventListener('click', function () {
                const modal = document.getElementById('importMasterAssetModal');
                const content = document.getElementById('importMasterAssetModalContent');
                if (modal && content) {
                    openModal(modal, content);
                }
            });

            document.getElementById('excel_file')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    document.getElementById('file-name-text').textContent = file.name;
                    document.getElementById('excel-file-name').classList.remove('hidden');
                }
            });

            document.getElementById('remove-excel')?.addEventListener('click', function () {
                const fileInput = document.getElementById('excel_file');
                const previewContainer = document.getElementById('excel-file-name');

                if (fileInput) {
                    fileInput.value = '';
                }

                if (previewContainer) {
                    previewContainer.classList.add('hidden');
                }

                document.getElementById('import-submit-btn').disabled = true;
                document.getElementById('import-submit-btn').classList.add('opacity-50', 'cursor-not-allowed');
            });

            let excelData = [];
            let headers = [];
            let hasValidationErrors = false;

            function openPreviewModal() {
                const modal = document.getElementById('excelPreviewModal');
                const content = document.getElementById('excelPreviewModalContent');

                if (modal && content) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                }
            }

            function closePreviewModal() {
                const modal = document.getElementById('excelPreviewModal');
                const content = document.getElementById('excelPreviewModalContent');

                if (modal && content) {
                    content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                    content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }
            }

            const excelFileInput = document.getElementById('excel_file');
            const previewBtn = document.getElementById('preview-btn');
            const excelError = document.getElementById('excel-error');
            const excelLoading = document.getElementById('excel-loading');
            const importStep1 = document.getElementById('import-step-1');
            const importStep2 = document.getElementById('import-step-2');
            const backToUploadBtn = document.getElementById('back-to-upload-btn');
            const importForm = document.getElementById('import-form');
            const excelDataInput = document.getElementById('excel_data');
            const previewTableBody = document.getElementById('preview-table-body');
            const previewCount = document.getElementById('preview-count');
            const previewWarnings = document.getElementById('preview-warnings');
            const warningList = document.getElementById('warning-list');

            let parsedExcelData = null;

            document.getElementById('excel_file')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    document.getElementById('file-name-text').textContent = file.name;
                    document.getElementById('excel-file-name').classList.remove('hidden');
                    previewBtn.disabled = false;
                } else {
                    previewBtn.disabled = true;
                }
            });

            previewBtn?.addEventListener('click', function () {
                const file = excelFileInput.files[0];
                if (!file) {
                    excelError.textContent = 'Silakan pilih file Excel terlebih dahulu.';
                    excelError.classList.remove('hidden');
                    return;
                }

                excelError.classList.add('hidden');
                excelLoading.classList.remove('hidden');
                previewBtn.disabled = true;

                parseExcelFile(file).then(data => {
                    excelLoading.classList.add('hidden');
                    previewBtn.disabled = false;

                    if (data.length === 0) {
                        excelError.textContent = 'File Excel tampaknya kosong atau tidak dapat diproses.';
                        excelError.classList.remove('hidden');
                        return;
                    }

                    parsedExcelData = data;

                    populatePreviewTable(data);

                    importStep1.classList.add('hidden');
                    importStep2.classList.remove('hidden');

                    excelDataInput.value = JSON.stringify(data);
                }).catch(error => {
                    console.error('Gagal memproses file Excel:', error);
                    excelLoading.classList.add('hidden');
                    previewBtn.disabled = false;
                    excelError.textContent = 'Gagal memproses file Excel: ' + error.message;
                    excelError.classList.remove('hidden');
                });
            });

            backToUploadBtn?.addEventListener('click', function () {
                importStep2.classList.add('hidden');
                importStep1.classList.remove('hidden');
            });

            async function parseExcelFile(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        try {
                            const data = e.target.result;
                            const workbook = XLSX.read(data, { type: 'array' });

                            const firstSheetName = workbook.SheetNames[0];
                            const worksheet = workbook.Sheets[firstSheetName];

                            const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                                header: 1,
                                defval: ""
                            });

                            const result = processExcelData(jsonData);
                            resolve(result);
                        } catch (error) {
                            reject(error);
                        }
                    };

                    reader.onerror = function (error) {
                        reject(error);
                    };

                    reader.readAsArrayBuffer(file);
                });
            }

            function processExcelData(data) {
                const nonEmptyRows = data.filter(row => row.some(cell => cell !== ""));

                if (nonEmptyRows.length < 2) {
                    return [];
                }

                const headers = nonEmptyRows[0].map(header =>
                    String(header).trim().toLowerCase().replace(/\s+/g, '_')
                );

                const headerMapping = {
                    'asset_name': 'asset_name',
                    'name': 'asset_name',
                    'asset': 'asset_name',
                    'nama_aset': 'asset_name',
                    'nama_asset': 'asset_name',
                    'nama aset': 'asset_name',
                    'asset_type': 'asset_type',
                    'type': 'asset_type',
                    'tipe_aset': 'asset_type',
                    'tipe aset': 'asset_type',
                    'subcategory': 'subcategory_name',
                    'subcategory_name': 'subcategory_name',
                    'sub_category': 'subcategory_name',
                    'nama_kategori': 'subcategory_name',
                    'nama kategori': 'subcategory_name',
                    'kategori': 'subcategory_name',
                    'category': 'subcategory_name',
                    'description': 'description',
                    'deskripsi': 'description',
                    'is_depreciable': 'is_depreciable',
                    'depreciable': 'is_depreciable',
                    'dapat_didepresiasi': 'is_depreciable',
                    'dapat didepresiasi': 'is_depreciable',
                    'needs_calibration': 'needs_calibration',
                    'calibration': 'needs_calibration',
                    'perlu_kalibrasi': 'needs_calibration',
                    'perlu kalibrasi': 'needs_calibration'
                };

                const headerIndexes = {};
                headers.forEach((header, index) => {
                    for (const [key, value] of Object.entries(headerMapping)) {
                        if (header === key || header.includes(key)) {
                            headerIndexes[value] = index;
                            break;
                        }
                    }
                });

                const requiredFields = ['asset_name', 'asset_type', 'subcategory_name', 'description'];
                const missingHeaders = [];
                requiredFields.forEach(field => {
                    if (headerIndexes[field] === undefined) {
                        const readableField = field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        missingHeaders.push(`Kolom ${readableField} tidak ditemukan di file Excel`);
                    }
                });

                if (missingHeaders.length > 0) {
                    showWarnings(missingHeaders);
                    return [];
                }

                const processedData = [];
                const warnings = [];
                const validAssetTypes = ['medical', 'medis', 'non_medical', 'non medis'];

                for (let i = 1; i < nonEmptyRows.length; i++) {
                    const row = nonEmptyRows[i];
                    const rowData = {
                        asset_name: '',
                        asset_type: '',
                        subcategory_name: '',
                        description: '',
                        is_depreciable: false,
                        needs_calibration: false
                    };

                    for (const [key, index] of Object.entries(headerIndexes)) {
                        if (index !== undefined && index < row.length) {
                            let value = row[index];

                            if (key === 'is_depreciable' || key === 'needs_calibration') {
                                if (typeof value === 'string') {
                                    value = value.toLowerCase();
                                    rowData[key] = value === 'yes' || value === 'true' || value === '1' || value === 'y' || value === 'ya';
                                } else if (typeof value === 'number') {
                                    rowData[key] = value === 1;
                                } else {
                                    rowData[key] = Boolean(value);
                                }
                            } else {
                                rowData[key] = String(value).trim();
                            }
                        }
                    }

                    let hasErrors = false;

                    if (!rowData.asset_name) {
                        warnings.push(`Baris ${i + 1}: Missing Asset Name`);
                        hasErrors = true;
                    }

                    if (!rowData.asset_type) {
                        warnings.push(`Baris ${i + 1}: Missing Asset Type`);
                        hasErrors = true;
                    } else {
                        const lowerType = rowData.asset_type.toLowerCase();
                        if (!validAssetTypes.some(type => lowerType.includes(type))) {
                            warnings.push(`Baris ${i + 1}: Invalid Asset Type "${rowData.asset_type}" (harus Medical/Medis atau Non-Medical/Non-Medis)`);
                            hasErrors = true;
                        } else {
                            if (lowerType.includes('medical') || lowerType.includes('medis')) {
                                rowData.asset_type = 'medical';
                            } else {
                                rowData.asset_type = 'non_medical';
                            }
                        }
                    }

                    if (!rowData.subcategory_name) {
                        warnings.push(`Baris ${i + 1}: Missing Subcategory`);
                        hasErrors = true;
                    }

                    rowData._rowNum = i;

                    if (!hasErrors) {
                        processedData.push(rowData);
                    }
                }

                if (warnings.length > 0) {
                    showWarnings(warnings);
                } else {
                    hideWarnings();
                }

                return processedData;
            }

            function populatePreviewTable(data) {
                previewTableBody.innerHTML = '';

                previewCount.textContent = `${data.length} data ditemukan`;

                const duplicates = findDuplicates(data);
                const hasDuplicates = Object.keys(duplicates).length > 0;

                const warnings = [];

                if (hasDuplicates) {
                    for (const [key, indexes] of Object.entries(duplicates)) {
                        if (indexes.length > 1) {
                            const item = data[indexes[0]];
                            warnings.push(`Duplikat ditemukan: "${item.asset_name}" (${formatAssetType(item.asset_type)}, ${item.subcategory_name}, ${item.description})`);
                        }
                    }
                }

                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                    row.innerHTML = `
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${index + 1}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.asset_name || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.asset_type ? formatAssetType(item.asset_type) : '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.subcategory_name || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.description || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                            ${item.is_depreciable ?
                            '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ya</span>' :
                            '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Tidak</span>'
                        }
                        </td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                            ${item.needs_calibration ?
                            '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Ya</span>' :
                            '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Tidak</span>'
                        }
                        </td>
                    `;

                    previewTableBody.appendChild(row);
                });

                if (warnings.length > 0) {
                    showWarnings(warnings);
                }
            }

            function findDuplicates(data) {
                const duplicateMap = {};

                data.forEach((item, index) => {
                    const key = `${item.asset_name}|${item.asset_type}|${item.subcategory_name}|${item.description}`.toLowerCase();

                    if (!duplicateMap[key]) {
                        duplicateMap[key] = [];
                    }
                    duplicateMap[key].push(index);
                });

                return Object.fromEntries(
                    Object.entries(duplicateMap).filter(([key, indexes]) => indexes.length > 1)
                );
            }

            function formatAssetType(type) {
                if (typeof type !== 'string') return '-';

                const lowerType = type.toLowerCase();
                if (lowerType === 'medical') {
                    return 'Medis';
                } else if (lowerType === 'non_medical') {
                    return 'Non Medis';
                } else {
                    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
            }

            function showWarnings(warnings) {
                if (!warnings || warnings.length === 0) {
                    hideWarnings();
                    return;
                }

                warningList.innerHTML = '';

                warnings.forEach(warning => {
                    const li = document.createElement('li');
                    li.textContent = warning;
                    warningList.appendChild(li);
                });

                previewWarnings.classList.remove('hidden');

                const importBtn = document.getElementById('import-btn');
                const hasCriticalWarnings = warnings.some(warning =>
                    warning.includes('Missing Asset Type') ||
                    warning.includes('Missing Asset Name') ||
                    warning.includes('Invalid Asset Type') ||
                    warning.includes('Missing Subcategory') ||
                    warning.includes('Missing Description')
                );

                if (importBtn && hasCriticalWarnings) {
                    importBtn.disabled = true;
                    importBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else if (importBtn) {
                    importBtn.disabled = false;
                    importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            function hideWarnings() {
                previewWarnings.classList.add('hidden');
            }

            document.getElementById('remove-excel')?.addEventListener('click', function () {
                const fileInput = document.getElementById('excel_file');
                const previewContainer = document.getElementById('excel-file-name');

                if (fileInput) {
                    fileInput.value = '';
                }

                if (previewContainer) {
                    previewContainer.classList.add('hidden');
                }

                previewBtn.disabled = true;
                excelError.classList.add('hidden');
            });

            importForm?.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                const originalFileInput = document.getElementById('excel_file');
                if (originalFileInput && originalFileInput.files.length > 0) {
                    formData.append('excel_file_upload', originalFileInput.files[0]);
                }

                const importBtn = document.getElementById('import-btn');
                const originalBtnText = importBtn.innerHTML;
                importBtn.disabled = true;
                importBtn.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        <span>Mengimpor...</span>
                    </div>
                `;

                fetch('{{ route('asset-master.import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw errorData;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalBtnText;

                        const modal = document.getElementById('importMasterAssetModal');
                        const content = document.getElementById('importMasterAssetModalContent');
                        closeModal(modal, content);

                        showToast(data.message || 'Aset master berhasil diimpor!', 'success');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalBtnText;

                        console.error('Import error:', error);

                        showToast(error, 'error');
                    });
            });

            function handleImportErrorResponse(errorData) {
                let errorMessage = errorData.message || 'Terjadi kesalahan selama pengimporan.';
                let errorDetails = [];

                if (errorData.errors) {
                    if (typeof errorData.errors === 'object' && !Array.isArray(errorData.errors)) {
                        Object.entries(errorData.errors).forEach(([field, messages]) => {
                            if (Array.isArray(messages)) {
                                messages.forEach(msg => errorDetails.push(`${field}: ${msg}`));
                            } else if (typeof messages === 'string') {
                                errorDetails.push(`${field}: ${messages}`);
                            }
                        });
                    }
                    else if (Array.isArray(errorData.errors)) {
                        errorData.errors.forEach(error => {
                            if (typeof error === 'string') {
                                errorDetails.push(error);
                            } else if (typeof error === 'object') {
                                if (error.message) {
                                    errorDetails.push(error.message);
                                } else if (error.row && error.reason) {
                                    errorDetails.push(`Baris ${error.row}: ${error.reason}`);
                                } else if (error.reason) {
                                    errorDetails.push(error.reason);
                                }
                            }
                        });
                    }
                }

                if (errorData.data && errorData.data.errors) {
                    if (Array.isArray(errorData.data.errors)) {
                        errorData.data.errors.forEach(error => {
                            if (typeof error === 'string') {
                                errorDetails.push(error);
                            } else if (error.message) {
                                errorDetails.push(error.message);
                            } else if (error.asset_name && error.reason) {
                                errorDetails.push(`"${error.asset_name}" - ${error.reason}`);
                            } else if (error.row && error.reason) {
                                errorDetails.push(`Baris ${error.row}: ${error.reason}`);
                            } else if (error.reason) {
                                errorDetails.push(error.reason);
                            }
                        });
                    } else if (typeof errorData.data.errors === 'object') {
                        Object.entries(errorData.data.errors).forEach(([field, messages]) => {
                            if (Array.isArray(messages)) {
                                messages.forEach(msg => errorDetails.push(`${field}: ${msg}`));
                            } else if (typeof messages === 'string') {
                                errorDetails.push(`${field}: ${messages}`);
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

                showNotification('error', errorMessage);
            }

            function showNotification(type, message) {
                showToast(message, type);
            }

            function showToast(message, type = 'success') {
                const notification = document.createElement('div');
                notification.id = type + 'Notification' + Date.now();
                notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                notification.role = 'alert';

                const hasHTML = typeof message === 'string' && /<[a-z][\s\S]*>/i.test(message);
                const isObject = typeof message === 'object' && message !== null;

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
                                <div>${isObject ? message.message || 'Operasi berhasil' : message}</div>
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

                    if (isObject) {
                        let errorContent = '';

                        if (message.message) {
                            errorContent = `<p>${message.message}</p>`;
                        } else {
                            errorContent = '<p>Terjadi kesalahan</p>';
                        }

                        if (message.errors) {
                            errorContent += '<ul class="mt-2 ml-4 list-disc">';

                            if (Array.isArray(message.errors)) {
                                message.errors.forEach(err => {
                                    if (typeof err === 'string') {
                                        errorContent += `<li>${err}</li>`;
                                    } else if (typeof err === 'object' && err !== null) {
                                        if (err.path && err.message) {
                                            errorContent += `<li>${err.path}: ${err.message}</li>`;
                                        } else if (err.message) {
                                            errorContent += `<li>${err.message}</li>`;
                                        } else {
                                            const values = Object.values(err).filter(v => typeof v === 'string');
                                            if (values.length > 0) {
                                                errorContent += `<li>${values.join(': ')}</li>`;
                                            }
                                        }
                                    }
                                });
                            } else if (typeof message.errors === 'object') {
                                Object.entries(message.errors).forEach(([field, fieldErrors]) => {
                                    if (Array.isArray(fieldErrors)) {
                                        fieldErrors.forEach(err => errorContent += `<li>${field}: ${err}</li>`);
                                    } else if (typeof fieldErrors === 'string') {
                                        errorContent += `<li>${field}: ${fieldErrors}</li>`;
                                    }
                                });
                            }

                            errorContent += '</ul>';
                        }

                        if (message.data && message.data.errors) {
                            errorContent += '<ul class="mt-2 ml-4 list-disc">';

                            if (Array.isArray(message.data.errors)) {
                                message.data.errors.forEach(err => {
                                    if (typeof err === 'string') {
                                        errorContent += `<li>${err}</li>`;
                                    } else if (typeof err === 'object' && err !== null) {
                                        if (err.path && err.message) {
                                            errorContent += `<li>${err.path}: ${err.message}</li>`;
                                        } else if (err.message) {
                                            errorContent += `<li>${err.message}</li>`;
                                        } else if (err.reason) {
                                            errorContent += `<li>${err.reason}</li>`;
                                        }
                                    }
                                });
                            } else if (typeof message.data.errors === 'object') {
                                Object.entries(message.data.errors).forEach(([field, fieldErrors]) => {
                                    if (Array.isArray(fieldErrors)) {
                                        fieldErrors.forEach(err => errorContent += `<li>${field}: ${err}</li>`);
                                    } else if (typeof fieldErrors === 'string') {
                                        errorContent += `<li>${field}: ${fieldErrors}</li>`;
                                    }
                                });
                            }

                            errorContent += '</ul>';
                        }

                        messageContainer.innerHTML = errorContent;
                    } else {
                        if (hasHTML) {
                            messageContainer.innerHTML = message;
                        } else {
                            messageContainer.textContent = message;
                        }
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

            function validateField(field, customCheck = null) {
                if (!field) return true;

                let isValid = true;
                if (customCheck !== null) {
                    isValid = customCheck;
                } else if (field.tagName.toLowerCase() === 'select') {
                    isValid = field.value !== '';
                } else {
                    isValid = field.value.trim() !== '';
                }

                const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

                if (!isValid) {
                    field.classList.add('border-red-500');
                    if (errorElement) {
                        errorElement.textContent = errorElement.getAttribute('data-error-message') || 'Field ini wajib diisi';
                        errorElement.classList.remove('hidden');
                    }
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                }

                return isValid;
            }

            document.getElementById('createMasterAssetForm')?.addEventListener('submit', function (event) {
                const assetName = this.querySelector('input[name="asset_name"]');
                const assetType = document.getElementById('asset_type');
                const subcategoryId = document.getElementById('subcategory_id');

                let isValid = true;

                if (!assetName.value.trim()) {
                    assetName.classList.add('border-red-500');
                    const errorElement = assetName.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Nama aset harus diisi';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                if (!assetType.value) {
                    assetType.classList.add('border-red-500');
                    const errorElement = assetType.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Tipe aset harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                const subcategoryContainer = subcategoryId.closest('.custom-select-container');
                const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                if (!subcategoryId.value) {
                    subcategorySearchInput.classList.add('border-red-500');
                    const errorElement = subcategoryContainer.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Kategori harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault();
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    `;
                }

            });

            document.getElementById('editMasterAssetForm')?.addEventListener('submit', function (event) {
                event.preventDefault();
                const assetName = document.getElementById('edit_asset_name');
                const assetType = document.getElementById('edit_asset_type');
                const subcategoryId = document.getElementById('edit_subcategory_id');


                let isValid = true;

                if (!assetName.value.trim()) {
                    assetName.classList.add('border-red-500');
                    const errorElement = assetName.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Nama aset harus diisi';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                if (!assetType.value) {
                    assetType.classList.add('border-red-500');
                    const errorElement = assetType.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Tipe aset harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                const subcategoryContainer = subcategoryId.closest('.custom-select-container');
                const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                if (!subcategoryId.value) {
                    subcategorySearchInput.classList.add('border-red-500');
                    const errorElement = subcategoryContainer.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Kategori harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                    isValid = false;
                }

                if (!isValid) {
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const formData = new FormData(this);
                const actionUrl = this.action;

                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    `;

                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw errorData;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;

                        // Close the modal
                        const editModal = document.getElementById('editMasterAssetModal');
                        const editContent = document.getElementById('editMasterAssetModalContent');
                        if (editModal && editContent) {
                            closeModal(editModal, editContent);
                        }

                        showToast(data.message || 'Aset master berhasil diperbarui', 'success');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error('Error updating asset:', error);

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;

                        showToast(error, 'error');
                    });
                }
            });

            document.querySelector('#createMasterAssetForm input[name="asset_name"]')?.addEventListener('input', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.querySelector('#createMasterAssetForm select[name="asset_type"]')?.addEventListener('change', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.getElementById('edit_asset_name')?.addEventListener('input', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.getElementById('edit_asset_type')?.addEventListener('change', function () {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.querySelectorAll('[id$="subcategory_id"]').forEach(subcategoryInput => {
                const container = subcategoryInput.closest('.custom-select-container');
                const searchInput = container.querySelector('.search-input');
                const optionsContainer = container.querySelector('.options-container');

                const dropdownIcon = document.createElement('div');
                dropdownIcon.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer';
                dropdownIcon.innerHTML = '<svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
                searchInput.parentNode.style.position = 'relative';
                searchInput.parentNode.appendChild(dropdownIcon);
                if (searchInput) {
                    searchInput.addEventListener('focus', (e) => {
                        e.stopPropagation();
                    });

                    const showDropdown = function () {
                        if (searchInput.disabled) {
                            const assetTypeId = subcategoryInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                            const assetTypeSelect = document.getElementById(assetTypeId);
                            assetTypeSelect.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');

                            setTimeout(() => {
                                assetTypeSelect.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                            }, 1000);
                            return;
                        }

                        const assetTypeId = subcategoryInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                        const assetTypeSelect = document.getElementById(assetTypeId);

                        if (assetTypeSelect && assetTypeSelect.value) {
                            const searchTerm = searchInput.value.trim() || " ";
                            fetchCategories(assetTypeSelect.value, searchTerm, subcategoryInput.id);
                            optionsContainer.classList.remove('hidden');
                        } else {
                            optionsContainer.innerHTML = '<div class="p-2 text-sm text-red-500">Pilih tipe aset terlebih dahulu</div>';
                            optionsContainer.classList.remove('hidden');

                            assetTypeSelect.classList.add('border-red-500', 'ring-2', 'ring-red-200');

                            setTimeout(() => {
                                assetTypeSelect.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                            }, 1500);
                        }
                    };

                    searchInput.addEventListener('click', showDropdown);
                    dropdownIcon.addEventListener('click', function (e) {
                        e.stopPropagation();
                        showDropdown();
                    });

                    document.addEventListener('click', function (e) {
                        if (e.target !== searchInput && e.target !== dropdownIcon && !dropdownIcon.contains(e.target) && !optionsContainer.contains(e.target)) {
                            optionsContainer.classList.add('hidden');
                        }
                    });

                    searchInput.addEventListener('input', debounce(function () {
                        if (this.disabled) return;

                        const searchTerm = this.value.trim();
                        const assetTypeId = subcategoryInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                        const assetTypeSelect = document.getElementById(assetTypeId);

                        if (assetTypeSelect && assetTypeSelect.value) {
                            if (searchTerm.length > 0) {
                                fetchCategories(assetTypeSelect.value, searchTerm, subcategoryInput.id);
                                optionsContainer.classList.remove('hidden');
                            } else {
                                fetchCategories(assetTypeSelect.value, " ", subcategoryInput.id);
                                optionsContainer.classList.remove('hidden');
                            }
                        }
                    }, 300));
                }
            });

            document.querySelectorAll('.custom-select-container .search-input').forEach(input => {
                input.addEventListener('input', function () {
                    const container = this.closest('.custom-select-container');
                    const selectElement = container.querySelector('select');
                    if (selectElement) {
                        selectElement.classList.remove('border-red-500');
                        const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
                });
            });

            function updateSubcategoryInputState(subcategoryId, assetTypeId) {
                const subcategoryInput = document.getElementById(subcategoryId);
                if (!subcategoryInput) return;

                const container = subcategoryInput.closest('.custom-select-container');
                const searchInput = container.querySelector('.search-input');
                const assetTypeSelect = document.getElementById(assetTypeId);

                if (assetTypeSelect && assetTypeSelect.value) {
                    searchInput.disabled = false;
                    searchInput.placeholder = "Cari kategori...";
                } else {
                    searchInput.disabled = true;
                    searchInput.placeholder = "Pilih tipe aset terlebih dahulu";
                }
            }

            updateSubcategoryInputState('subcategory_id', 'asset_type');
            updateSubcategoryInputState('edit_subcategory_id', 'edit_asset_type');

            function fetchCategories(assetType, searchTerm = '', targetId = '') {
                if (!assetType || !targetId) return;

                const container = document.getElementById(targetId).closest('.custom-select-container');
                const optionsContainer = container.querySelector('.options-container');
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const isShowAll = searchTerm === " ";

                if (!searchTerm.trim() && !isShowAll) {
                    optionsContainer.classList.add('hidden');
                    return;
                }

                optionsContainer.innerHTML = '<div class="p-2 text-center text-gray-500">Memuat kategori...</div>';
                optionsContainer.classList.remove('hidden');

                // Add these variables for lazy loading
                let page = 1;
                const perPage = 15;
                let isLoading = false;
                let hasMoreData = true;
                let allCategories = [];

                // Function to load categories with pagination
                function loadCategories(page, searchValue) {
                    if (isLoading || !hasMoreData) return;

                    isLoading = true;

                let queryParams = new URLSearchParams();
                queryParams.append('json', 'true');
                queryParams.append('asset_type', assetType);
                    queryParams.append('page', page);
                    queryParams.append('limit', perPage);

                    if (searchValue.trim() && !isShowAll) {
                        queryParams.append('search', searchValue.trim());
                }

                fetch(`/categories?${queryParams.toString()}`, {
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
                        let categories = [];
                            let pagination = null;

                        if (Array.isArray(data)) {
                            categories = data;
                        } else if (data.subcategories && Array.isArray(data.subcategories)) {
                            categories = data.subcategories;
                                pagination = data.pagination || null;
                        } else if (data.data && Array.isArray(data.data)) {
                            categories = data.data;
                                pagination = data.pagination || data.meta || null;
                            }

                            allCategories = [...allCategories, ...categories];

                            // Check if we have more data to load
                            if (pagination) {
                                hasMoreData = pagination.current_page < pagination.last_page;
                            } else {
                                hasMoreData = categories.length >= perPage;
                            }

                            displayCategoryResults(allCategories, optionsContainer, hiddenInput, searchInput, assetType, true);

                            isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching categories:', error);
                            if (page === 1) {
                        optionsContainer.innerHTML = `
                        <div class="p-3 text-sm text-red-500 text-center">
                            <p>Gagal memuat kategori</p>
                            <p class="text-xs mt-1 text-red-400">${error.message}</p>
                            <button class="mt-2 px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 text-xs" onclick="this.closest('.options-container').classList.add('hidden')">Tutup</button>
                                </div>`;
                            }
                            isLoading = false;
                        });
                }

                // Initial load
                loadCategories(page, searchTerm);

                // Remove any existing scroll event listeners
                optionsContainer.removeEventListener('scroll', categoryScrollHandler);

                // Scroll event handler for lazy loading
                function categoryScrollHandler() {
                    const { scrollTop, scrollHeight, clientHeight } = optionsContainer;

                    // Load more data when user scrolls to 80% of the container
                    if (scrollTop + clientHeight >= scrollHeight * 0.8 && hasMoreData && !isLoading) {
                        page++;
                        loadCategories(page, searchTerm);
                    }
                }

                // Add scroll event listener
                optionsContainer.addEventListener('scroll', categoryScrollHandler);
            }

            function displayCategoryResults(categories, resultsElem, idInputElem, searchInputElem, assetType, isLazyLoad = false) {
                // Always clear the initial loading message
                const initialLoadingMessage = resultsElem.querySelector('div:not(.option):not(.loading-indicator):not(.dropdown-header)');
                if (initialLoadingMessage && initialLoadingMessage.textContent.includes('Memuat kategori')) {
                    initialLoadingMessage.remove();
                }

                if (!isLazyLoad) {
                resultsElem.innerHTML = '';
                } else {
                    // Remove loading indicator if it exists
                    const loadingIndicator = resultsElem.querySelector('.loading-indicator');
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                }

                if (categories.length === 0 && !isLazyLoad) {
                    resultsElem.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-gray-500 mb-2">Tidak ada kategori yang ditemukan</p>
                            <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 text-xs" onclick="this.closest('.options-container').classList.add('hidden')">Tutup</button>
                        </div>
                    `;
                    return;
                }

                // Only add the header if it doesn't exist yet
                const existingHeader = resultsElem.querySelector('.dropdown-header');
                if (!existingHeader && categories.length > 0) {
                    const typeTitle = document.createElement('div');
                    typeTitle.className = 'dropdown-header p-2 text-sm font-medium text-gray-600 border-b sticky top-0 bg-white z-10';
                    typeTitle.textContent = `Kategori ${assetType === 'medical' ? 'Medis' : 'Non-Medis'}`;
                    resultsElem.insertBefore(typeTitle, resultsElem.firstChild);
                }

                if (searchInputElem && searchInputElem.value.trim() && !isLazyLoad) {
                    const searchTerm = searchInputElem.value.trim().toLowerCase();
                    categories.sort((a, b) => {
                        const aName = a.subcategory_name?.toLowerCase() || '';
                        const bName = b.subcategory_name?.toLowerCase() || '';

                        if (aName === searchTerm) return -1;
                        if (bName === searchTerm) return 1;

                        const aStarts = aName.startsWith(searchTerm);
                        const bStarts = bName.startsWith(searchTerm);
                        if (aStarts && !bStarts) return -1;
                        if (bStarts && !aStarts) return 1;

                        return aName.localeCompare(bName);
                    });
                }

                const existingOptions = new Set();
                const existingOptionElements = resultsElem.querySelectorAll('.option');

                existingOptionElements.forEach(element => {
                    existingOptions.add(element.getAttribute('data-value'));
                });

                categories.forEach((category) => {
                    // Skip duplicates that might occur during lazy loading
                    if (existingOptions.has(category.subcategory_id?.toString())) {
                        return;
                    }

                    const div = document.createElement('div');
                    div.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                    div.textContent = category.subcategory_name || 'Unknown Category';
                    div.setAttribute('data-value', category.subcategory_id || '');
                    div.setAttribute('data-type', category.asset_type || assetType);

                    div.addEventListener('click', function () {
                        idInputElem.value = this.getAttribute('data-value');
                        searchInputElem.value = this.textContent;

                        searchInputElem.classList.remove('border-red-500');
                        const container = searchInputElem.closest('.custom-select-container');
                        const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');

                        resultsElem.classList.add('hidden');

                        const event = new Event('change', { bubbles: true });
                        idInputElem.dispatchEvent(event);
                    });

                    resultsElem.appendChild(div);
                    existingOptions.add(category.subcategory_id?.toString());
                });

                // Add loading indicator at the bottom for lazy loading
                if (isLazyLoad) {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'loading-indicator p-2 text-xs text-gray-500 text-center';
                    loadingDiv.textContent = 'Memuat kategori lainnya...';
                    resultsElem.appendChild(loadingDiv);
                }

                // Add max height and scrollable class if not already set
                if (!resultsElem.classList.contains('scrollable-dropdown')) {
                    resultsElem.classList.add('scrollable-dropdown');
                    resultsElem.style.maxHeight = '250px';
                    resultsElem.style.overflowY = 'auto';
                }
            }

            window.changeMasterAssetPerPage = function (perPage) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', perPage);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            };

            // Column header sorting
            const sortByNameHeader = document.getElementById('sortByName');
            if (sortByNameHeader) {
                sortByNameHeader.addEventListener('click', function() {
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

            const sortByCodeHeader = document.getElementById('sortByCode');
            if (sortByCodeHeader) {
                sortByCodeHeader.addEventListener('click', function() {
                    const currentSort = '{{ request()->query("sort") }}';
                    let newSort;

                    if (currentSort === 'code_asc') {
                        newSort = 'code_desc';
                    } else {
                        newSort = 'code_asc';
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', newSort);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                });
            }
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

                /* Add these styles for dropdown */
                .scrollable-dropdown {
                    max-height: 250px;
                    overflow-y: auto;
                    scrollbar-width: thin;
                }
                .scrollable-dropdown::-webkit-scrollbar {
                    width: 6px;
                }
                .scrollable-dropdown::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                .scrollable-dropdown::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 3px;
                }
                .scrollable-dropdown::-webkit-scrollbar-thumb:hover {
                    background: #555;
                }
                .loading-indicator {
                    border-top: 1px solid #eee;
                    padding-top: 8px;
                }
            </style>
        `);
    </script>
@endpush
