@extends('Layout.app')

@section('title', 'Aset Master')

@section('content')
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
                        <button id="importMasterAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                            </svg>
                            <span class="text-base">Impor Excel</span>
                        </button>
                        @endif
                        @if(hasPermission('asset-master:export'))
                        <button id="exportBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span class="text-base">Ekspor PDF</span>
                        </button>
                        @endif
                        @if(hasPermission('asset-master:create'))
                        <button id="addMasterAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Tambah Aset Master</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama aset, tipe, atau brand..."
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
                            <option value="" disabled selected>Pilih Urutan Pengurutan</option>
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Asset Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kode Aset Master</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tipe Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Subkategori</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Merk</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Penyusutan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Kalibrasi</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($masterAssets) && count($masterAssets) > 0)
                                @foreach($masterAssets as $asset)
                                <tr data-asset-id="{{ $asset['asset_master_id'] ?? '' }}">
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_master_code'] ?? 'N/A' }}</td>
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
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['subcategory_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['brand_name'] ?? '-' }}</td>
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
                                            <a href="{{ route('view-asset-master', $asset['asset_master_id'] ?? '') }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if(hasPermission('asset-master:edit'))
                                            <button class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-asset-btn"
                                                data-id="{{ $asset['asset_master_id'] ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            @endif
                                            @if(hasPermission('asset-master:delete'))
                                            <button class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-asset-btn"
                                                data-id="{{ $asset['asset_master_id'] ?? '' }}"
                                                data-name="{{ $asset['asset_name'] ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada aset master yang ditemukan</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Assets -->
                @if(isset($masterAssets_pagination) && $masterAssets_pagination)
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($masterAssets_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($masterAssets_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($masterAssets_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>

                        <div class="flex gap-2">
                            @php
                                $currentPage = $masterAssets_pagination['current_page'] ?? 1;
                                $lastPage = $masterAssets_pagination['last_page'] ?? 1;
                            @endphp

                            @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                   class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($masterAssets_pagination['current_page'] ?? 1) >= ($masterAssets_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($masterAssets_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($masterAssets_pagination['current_page'] ?? 1) >= ($masterAssets_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
                                Menampilkan 1 sampai {{ count($masterAssets ?? []) }} dari {{ count($masterAssets ?? []) }} data
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ isset($masterAssets_pagination['per_page']) && $masterAssets_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="createMasterAssetForm" action="{{ route('asset-master.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Aset</h3>

                            <!-- Image upload - Improved visibility -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#213268]">Gambar Aset <span class="text-sm font-normal text-[#666666]">(Unggah gambar referensi untuk aset ini)</span></label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Image preview -->
                                    <div id="image-preview" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Selected Image">
                                            <button type="button" id="remove-image" class="remove-image-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Seret gambar atau <span class="text-[#213268] font-semibold">pilih file</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini untuk memilih file</p>
                                    </div>
                                    <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Nama Aset <span class="text-red-500">*</span></label>
                                    <input type="text" name="asset_name"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Nama aset">
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus diisi</div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Tipe Aset <span class="text-red-500">*</span></label>
                                    <select name="asset_type" id="asset_type"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Pilih Tipe Aset</option>
                                        <option value="medical">Medis</option>
                                        <option value="non_medical">Non Medis</option>
                                    </select>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus dipilih</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Kategori <span class="text-red-500">*</span></label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Cari kategori...">
                                        <input type="hidden" name="subcategory_id" id="subcategory_id">
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500" id="subcategory-loading-message">Pilih tipe aset terlebih dahulu</div>
                                            <!-- Subcategories will be loaded dynamically based on asset_type -->
                                        </div>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih</div>
                                </div>

                                <!-- Brand Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Merk <span class="text-red-500">*</span></label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Cari merk...">
                                        <input type="hidden" name="brand_id" id="brand_id">
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500" id="brand-loading-message">Memuat data merk...</div>
                                            <!-- Brands will be loaded dynamically -->
                                        </div>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Merk harus dipilih</div>
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
                                    <label for="is_depreciable" class="text-base font-semibold text-[#666666]">Aktifkan Penyusutan Aset</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_depreciable" id="is_depreciable" class="sr-only peer depreciation-toggle" value="true">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">No</span>
                                    </label>
                                </div>

                                <!-- Calibration Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="needs_calibration" class="text-base font-semibold text-[#666666]">Kalibrasi</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="needs_calibration" id="needs_calibration" class="sr-only peer calibration-toggle" value="true">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="editMasterAssetForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <!-- Loading indicator -->
                        <div class="text-center" id="edit-loading">
                            <div class="inline-block w-8 h-8 border-4 border-[#213268] border-t-transparent rounded-full animate-spin"></div>
                            <p class="mt-2 text-gray-600">Memuat data aset...</p>
                        </div>

                        <div id="edit-form-content" class="space-y-4 hidden">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Aset</h3>

                            <!-- Image upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Gambar Aset</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Current image preview -->
                                    <div id="edit-image-container" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Asset Image">
                                            <button type="button" class="remove-image-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Seret gambar atau <span class="text-blue-600">pilih file</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                    </div>
                                    <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Nama Aset <span class="text-red-500">*</span></label>
                                    <input type="text" name="asset_name" id="edit_asset_name"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Nama aset">
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Nama aset harus diisi</div>
                                </div>

                                <!-- Asset Type Field -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Tipe Aset <span class="text-red-500">*</span></label>
                                    <select name="asset_type" id="edit_asset_type"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Pilih Tipe Aset</option>
                                        <option value="non_medical">Non Medis</option>
                                        <option value="medical">Medis</option>
                                    </select>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe aset harus dipilih</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Kategori <span class="text-red-500">*</span></label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Cari kategori...">
                                        <input type="hidden" name="subcategory_id" id="edit_subcategory_id">
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500" id="edit-subcategory-loading-message">Pilih tipe aset terlebih dahulu</div>
                                            <!-- Subcategories will be loaded dynamically based on asset_type -->
                                        </div>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih</div>
                                </div>

                                <!-- Brand Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Merk <span class="text-red-500">*</span></label>
                                    <div class="custom-select-container relative">
                                        <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Cari merk...">
                                        <input type="hidden" name="brand_id" id="edit_brand_id">
                                        <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500" id="edit-brand-loading-message">Memuat data merk...</div>
                                            <!-- Brands will be loaded dynamically -->
                                        </div>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Merk harus dipilih</div>
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
                                    <label for="edit_is_depreciable" class="text-base font-semibold text-[#666666]">Aktifkan Penyusutan Aset</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_depreciable" id="edit_is_depreciable" class="sr-only peer depreciation-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">Tidak</span>
                                    </label>
                                </div>

                                <!-- Calibration Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="edit_needs_calibration" class="text-base font-semibold text-[#666666]">Kalibrasi</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="needs_calibration" id="edit_needs_calibration" class="sr-only peer calibration-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="edit-submit-btn" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus aset master ini? Aksi ini tidak dapat dibatalkan.</p>
                                <p id="delete-asset-name" class="text-base font-semibold text-center mt-2"></p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    <li>Kolom yang dibutuhkan: Nama Aset, Tipe Aset, Kategori, Merk</li>
                                    <li>Maksimal 100 data per import</li>
                                    <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                </ul>
                                <div class="mt-3 flex justify-end">
                                    <a href="{{ asset('docs/ImportAsetTemplate.xlsx') }}" download class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Unduh Template
                                    </a>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#213268]">File Excel</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- File preview -->
                                    <div id="excel-file-name" class="mt-2 mb-4 w-full hidden">
                                        <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span id="file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                <button type="button" id="remove-excel" class="ml-auto text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Seret file Excel atau <span class="text-[#213268] font-semibold">pilih file</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini untuk memilih file</p>
                                    </div>
                                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="excel-error" class="hidden text-red-500 text-sm"></div>

                            <!-- Loading Indicator -->
                            <div id="excel-loading" class="hidden text-center py-2">
                                <div class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-sm text-gray-600">Memproses data Excel...</p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Batal
                                </button>
                                <button type="button" id="preview-btn" disabled class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
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
                                            <th class="p-3 text-left text-xs font-semibold">Merk</th>
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
                            <div id="preview-warnings" class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                <p class="font-medium mb-2">Peringatan:</p>
                                <ul class="list-disc pl-5" id="warning-list">
                                    <!-- Warning messages will be inserted here -->
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button type="button" id="back-to-upload-btn" class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Kembali
                                </button>
                                <form action="{{ route('asset-master.import') }}" method="POST" id="import-form" class="w-2/3" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="excel_data" id="excel_data">
                                    <button type="submit" id="import-btn" class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
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

<!-- All notifications are handled by JavaScript -->

@endsection

@push('scripts')
<!-- SheetJS library for Excel parsing -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Add JavaScript initialization here for permission awareness
        @if(!hasPermission('asset-master:create'))
        // Disable related elements if user doesn't have permission
        const addButtons = document.querySelectorAll('#addMasterAssetBtn');
        addButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-master:import'))
        // Disable related elements if user doesn't have permission
        const addButtons = document.querySelectorAll('#importMasterAssetBtn, #preview-btn');
        addButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-master:edit'))
        // Disable edit functionality if user doesn't have permission
            const editButtons = document.querySelectorAll('.edit-asset-btn');
        editButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-master:delete'))
        // Disable delete functionality if user doesn't have permission
        const deleteButtons = document.querySelectorAll('.delete-asset-btn');
        deleteButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        // Display session notifications using the showNotification function
        @if(session('success'))
            showNotification('success', '{{ session('success') }}');
        @endif

        @if(session('error') || isset($error))
            showNotification('error', '{!! session('error') ?? $error ?? 'An error occurred' !!}');
        @endif

        // Modal functionality
        const openModal = function(modal, content) {
            console.log('Opening modal', modal.id);
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        };

        const closeModal = function(modal, content) {
            console.log('Closing modal', modal.id);
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        // Debounce function to limit function calls
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Initialize custom select dropdowns
        function initCustomSelects() {
            document.querySelectorAll('.custom-select-container').forEach(container => {
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const optionsContainer = container.querySelector('.options-container');
                const options = container.querySelectorAll('.option');

                // Store all options in a variable for quick access
                const allOptions = Array.from(options);

                // Debug info about available options
                console.log(`Select for ${hiddenInput.id || 'unknown'} has ${allOptions.length} options`);

                // Lazy loading configuration
                const maxInitialOptions = 30; // Show fewer options initially for better performance
                const loadMoreIncrement = 50; // Load this many more options when "Load more" is clicked
                let isFullyLoaded = allOptions.length <= maxInitialOptions;
                let visibleCount = Math.min(maxInitialOptions, allOptions.length);

                // Initialize with limited options if there are many
                if (!isFullyLoaded) {
                    // Hide options beyond the initial limit
                    allOptions.forEach((option, index) => {
                        if (index >= maxInitialOptions) {
                            option.style.display = 'none';
                        }
                    });

                    // Add a "load more" option at the end
                    const loadMoreDiv = document.createElement('div');
                    loadMoreDiv.className = 'load-more p-3 text-center text-blue-600 hover:bg-gray-100 cursor-pointer';
                    loadMoreDiv.textContent = `Muat lebih banyak opsi... (${visibleCount} dari ${allOptions.length})`;
                    loadMoreDiv.addEventListener('click', function() {
                        // Calculate how many more to show
                        const newVisibleCount = Math.min(visibleCount + loadMoreIncrement, allOptions.length);

                        // Show the next batch of options
                        for (let i = visibleCount; i < newVisibleCount; i++) {
                            allOptions[i].style.display = '';
                        }

                        visibleCount = newVisibleCount;

                        // Update load more text or remove if all loaded
                        if (visibleCount >= allOptions.length) {
                            this.remove();
                            isFullyLoaded = true;
                        } else {
                            this.textContent = `Muat lebih banyak opsi... (${visibleCount} dari ${allOptions.length})`;
                        }

                        // Apply current search filter if there is one
                        const searchValue = searchInput.value.toLowerCase().trim();
                        if (searchValue) {
                            filterOptions(searchValue);
                        }
                    });
                    optionsContainer.appendChild(loadMoreDiv);
                }

                // Show options when input is focused
                searchInput.addEventListener('focus', () => {
                    optionsContainer.classList.remove('hidden');

                    // Reset search and show all loaded options
                    if (searchInput.value === '') {
                        if (isFullyLoaded) {
                            allOptions.forEach(option => {
                                option.style.display = '';
                            });
                        } else {
                            // Show only initial options
                            allOptions.forEach((option, index) => {
                                option.style.display = index < visibleCount ? '' : 'none';
                            });

                            // Make sure load more button is visible if needed
                            const loadMoreBtn = optionsContainer.querySelector('.load-more');
                            if (!loadMoreBtn && !isFullyLoaded) {
                                const loadMoreDiv = document.createElement('div');
                                loadMoreDiv.className = 'load-more p-3 text-center text-blue-600 hover:bg-gray-100 cursor-pointer';
                                loadMoreDiv.textContent = `Muat lebih banyak opsi... (${visibleCount} dari ${allOptions.length})`;
                                loadMoreDiv.addEventListener('click', function() {
                                    const newVisibleCount = Math.min(visibleCount + loadMoreIncrement, allOptions.length);
                                    for (let i = visibleCount; i < newVisibleCount; i++) {
                                        allOptions[i].style.display = '';
                                    }
                                    visibleCount = newVisibleCount;

                                    if (visibleCount >= allOptions.length) {
                                        this.remove();
                                        isFullyLoaded = true;
                                    } else {
                                        this.textContent = `Muat lebih banyak opsi... (${visibleCount} dari ${allOptions.length})`;
                                    }
                                });
                                optionsContainer.appendChild(loadMoreDiv);
                            }
                        }
                    } else {
                        // If there's already a search term, filter by it
                        filterOptions(searchInput.value.toLowerCase().trim());
                    }
                });

                // Hide options when clicking outside
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) {
                        optionsContainer.classList.add('hidden');
                    }
                });

                // Function to filter options by search term
                function filterOptions(searchValue) {
                    // For search operations, we'll search through ALL options, not just visible ones
                    isFullyLoaded = true; // When searching, ignore lazy loading limits

                    let hasResults = false;
                    const matchingOptions = [];

                    // Remove any existing no-results message
                    const existingNoResults = optionsContainer.querySelector('.no-results');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    // Remove load more button when filtering
                    const loadMoreBtn = optionsContainer.querySelector('.load-more');
                    if (loadMoreBtn) {
                        loadMoreBtn.remove();
                    }

                    // First find exact matches (start with)
                    allOptions.forEach(option => {
                        const text = option.textContent.trim().toLowerCase();
                        if (text.startsWith(searchValue)) {
                            matchingOptions.push(option);
                            option.style.display = '';
                            hasResults = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    // If no exact matches, look for contains matches
                    if (!hasResults) {
                        allOptions.forEach(option => {
                            const text = option.textContent.trim().toLowerCase();
                            if (text.includes(searchValue)) {
                                matchingOptions.push(option);
                                option.style.display = '';
                                hasResults = true;
                            }
                        });
                    }

                    // Show no results message if needed
                    if (!hasResults) {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'no-results p-3 text-center text-gray-500';
                        msgDiv.textContent = 'No results found';
                        optionsContainer.appendChild(msgDiv);
                    } else {
                        // Show the matched options and scroll to the first match
                        if (matchingOptions.length > 0) {
                            matchingOptions[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                            // Add result count if there are many matches
                            if (matchingOptions.length > 10) {
                                const countDiv = document.createElement('div');
                                countDiv.className = 'results-count p-2 text-center text-xs text-gray-500';
                                countDiv.textContent = `Ditemukan ${matchingOptions.length} data`;
                                optionsContainer.insertBefore(countDiv, optionsContainer.firstChild);
                            }
                        }
                    }
                }

                // Attach event handlers for dynamic search with dropdown
                searchInput.addEventListener('input', debounce(function() {
                    const searchValue = this.value.toLowerCase().trim();
                    const containerId = hiddenInput.id;
                    const containerLoadingId = optionsContainer.querySelector('[id$="-loading-message"]')?.id || `${containerId}-loading-message`;

                    // Identify what type of dropdown this is (subcategory or brand)
                    if (containerId.includes('subcategory')) {
                        // For subcategory, we need the asset type
                        const assetTypeSelect = document.getElementById(containerId === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type');
                        if (assetTypeSelect && assetTypeSelect.value) {
                            loadSubcategories(assetTypeSelect.value, containerId, containerLoadingId, searchValue);
                        } else {
                            // If no asset type is selected, show a message
                            Array.from(optionsContainer.querySelectorAll('.option, .no-results')).forEach(el => el.remove());
                            const msgDiv = document.createElement('div');
                            msgDiv.className = 'p-2 text-center text-gray-500';
                            msgDiv.textContent = 'Pilih tipe aset terlebih dahulu';
                            optionsContainer.classList.remove('hidden');
                            optionsContainer.appendChild(msgDiv);
                        }
                    } else if (containerId.includes('brand')) {
                        // For brand, just search directly
                        loadBrands(containerId, containerLoadingId, searchValue);
                    }
                }, 100)); // Reduced debounce time for more responsive search

                // Set selected option
                options.forEach(option => {
                    option.addEventListener('click', () => {
                        const value = option.dataset.value;
                        const text = option.textContent.trim();

                        hiddenInput.value = value;
                        searchInput.value = text;
                        optionsContainer.classList.add('hidden');

                        // Trigger change event to notify form of the selection
                        const event = new Event('change', { bubbles: true });
                        hiddenInput.dispatchEvent(event);
                    });
                });
            });
        }

        // Function to set select value and display text
        function setSelectValue(selectId, value, displayText = null) {
            if (!value) return;

            // Get the elements
            const hiddenInput = document.getElementById(selectId);
            if (!hiddenInput) return;

            const container = hiddenInput.closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');

            // Set the hidden input value
            hiddenInput.value = value.toString();

            // If we have display text, use it directly
            if (displayText) {
                searchInput.value = displayText;

                // Trigger change event to validate properly
                const event = new Event('change', { bubbles: true });
                hiddenInput.dispatchEvent(event);
                return;
            }

            // Otherwise, try to find the display text
            const options = container.querySelectorAll('.option');
            let foundOption = null;

            // Try to find the option in the DOM
            Array.from(options).forEach(option => {
                if (option.dataset.value === value.toString()) {
                    foundOption = option;
                }
            });

            // Update the display text if we found the option
            if (foundOption) {
                searchInput.value = foundOption.textContent.trim();

                // Make sure this option is visible
                foundOption.style.display = '';

                // Ensure we can see this option
                setTimeout(() => {
                    foundOption.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            } else {
                // For subcategory and brand, we need to fetch the display text from API
                if (selectId === 'edit_subcategory_id') {
                    // Fetch subcategory details to get the display name
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
                } else if (selectId === 'edit_brand_id') {
                    // Fetch brand details to get the display name
                    fetch(`/brands/${value}?json=true`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.brand_name) {
                                searchInput.value = data.brand_name;
                            } else {
                                searchInput.value = value.toString();
                                console.warn(`Could not find brand name for ID ${value}`);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching brand details:', error);
                            searchInput.value = value.toString();
                        });
                } else {
                    // For other fields, just use the value as display text
                    console.warn(`Option with value "${value}" not found for ${selectId}`);
                    searchInput.value = value.toString();
                }
            }

                        // Trigger change event to validate properly
                        const event = new Event('change', { bubbles: true });
                        hiddenInput.dispatchEvent(event);
        }

        // Initialize all custom selects
        initCustomSelects();

        // Load subcategories based on selected asset type
        function loadSubcategories(assetType, targetElementId, loadingMessageId, searchTerm = '', silentLoad = false) {
            // Get container elements
            const container = document.querySelector(`#${targetElementId}`).closest('.custom-select-container');
            const optionsContainer = container.querySelector('.options-container');
            const loadingMessage = document.getElementById(loadingMessageId);
            const searchInput = container.querySelector('.search-input');

            // Reset previous options
            Array.from(optionsContainer.querySelectorAll('.option')).forEach(option => option.remove());

            // Remove any existing no-results message
            const existingNoResults = optionsContainer.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            // Set loading message
            if (loadingMessage) {
                loadingMessage.textContent = searchTerm ? 'Mencari kategori...' : 'Memuat kategori...';
            } else {
                // Create a loading message if it doesn't exist
                const newLoadingMsg = document.createElement('div');
                newLoadingMsg.id = loadingMessageId;
                newLoadingMsg.className = 'p-2 text-center text-gray-500';
                newLoadingMsg.textContent = searchTerm ? 'Mencari kategori...' : 'Memuat kategori...';
                optionsContainer.appendChild(newLoadingMsg);
            }

            // Show options container only if not in silent mode
            if (!silentLoad && optionsContainer.classList.contains('hidden')) {
                optionsContainer.classList.remove('hidden');
            }

            if (!assetType) {
                if (loadingMessage) {
                    loadingMessage.textContent = 'Pilih tipe aset terlebih dahulu';
                }
                        return;
                    }

            // Build URL with search parameter if provided
            let url = `/categories?asset_type=${assetType}&json=true`;
            if (searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }

            // Fetch subcategories from API
            fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear the loading message
                    Array.from(optionsContainer.querySelectorAll('.p-2.text-center')).forEach(msg => msg.remove());

                    // Check if we have data
                    if (!Array.isArray(data) || data.length === 0) {
                        const noDataMsg = document.createElement('div');
                        noDataMsg.className = 'no-results p-2 text-center text-gray-500';
                        noDataMsg.textContent = searchTerm ? `Tidak ada hasil untuk "${searchTerm}"` : 'Tidak ada kategori tersedia';
                        optionsContainer.appendChild(noDataMsg);
                        return;
                    }

                    // Add search help message
                    const searchHelpMsg = document.createElement('div');
                    searchHelpMsg.className = 'p-2 text-center text-gray-500';
                    searchHelpMsg.textContent = 'Ketik untuk mencari...';
                    optionsContainer.appendChild(searchHelpMsg);

                    // Add options
                    data.forEach(subcategory => {
                        const option = document.createElement('div');
                        option.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                        option.setAttribute('data-value', subcategory.subcategory_id);
                        option.setAttribute('data-type', subcategory.asset_type);
                        option.textContent = subcategory.subcategory_name;

                        // Add click event
                        option.addEventListener('click', () => {
                            const hiddenInput = container.querySelector('input[type="hidden"]');
                            hiddenInput.value = subcategory.subcategory_id;
                            searchInput.value = subcategory.subcategory_name;
                            optionsContainer.classList.add('hidden');

                            // Trigger change event
                const event = new Event('change', { bubbles: true });
                hiddenInput.dispatchEvent(event);
                        });

                        optionsContainer.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                    Array.from(optionsContainer.querySelectorAll('.p-2.text-center')).forEach(msg => msg.remove());

                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'p-2 text-center text-red-500';
                    errorMsg.textContent = 'Error loading categories';
                    optionsContainer.appendChild(errorMsg);
                });
        }

                // Load brands
        function loadBrands(targetElementId, loadingMessageId, searchTerm = '', silentLoad = false) {
            // Get container elements
            const container = document.querySelector(`#${targetElementId}`).closest('.custom-select-container');
            const optionsContainer = container.querySelector('.options-container');
            const loadingMessage = document.getElementById(loadingMessageId);
            const searchInput = container.querySelector('.search-input');

            // Reset previous options
            Array.from(optionsContainer.querySelectorAll('.option')).forEach(option => option.remove());

            // Remove any existing no-results message
            const existingNoResults = optionsContainer.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            // Set loading message
            if (loadingMessage) {
                loadingMessage.textContent = searchTerm ? 'Mencari merk...' : 'Memuat data merk...';
            } else {
                // Create a loading message if it doesn't exist
                const newLoadingMsg = document.createElement('div');
                newLoadingMsg.id = loadingMessageId;
                newLoadingMsg.className = 'p-2 text-center text-gray-500';
                newLoadingMsg.textContent = searchTerm ? 'Mencari merk...' : 'Memuat data merk...';
                optionsContainer.appendChild(newLoadingMsg);
            }

            // Show options container only if not in silent mode
            if (!silentLoad && optionsContainer.classList.contains('hidden')) {
                optionsContainer.classList.remove('hidden');
            }

            // Build URL with search parameter if provided
            let url = '/brands?json=true';
            if (searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }

            // Fetch brands from API
            fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear the loading message
                    Array.from(optionsContainer.querySelectorAll('.p-2.text-center')).forEach(msg => msg.remove());

                    // Check if we have data
                    if (!Array.isArray(data) || data.length === 0) {
                        const noDataMsg = document.createElement('div');
                        noDataMsg.className = 'no-results p-2 text-center text-gray-500';
                        noDataMsg.textContent = searchTerm ? `Tidak ada hasil untuk "${searchTerm}"` : 'Tidak ada merk tersedia';
                        optionsContainer.appendChild(noDataMsg);
                        return;
                    }

                    // Add search help message
                    const searchHelpMsg = document.createElement('div');
                    searchHelpMsg.className = 'p-2 text-center text-gray-500';
                    searchHelpMsg.textContent = 'Ketik untuk mencari...';
                    optionsContainer.appendChild(searchHelpMsg);

                    // Add options with highlight for search term
                    data.forEach(brand => {
                        const option = document.createElement('div');
                        option.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                        option.setAttribute('data-value', brand.brand_id);

                        // Highlight search term if it exists
                        if (searchTerm && brand.brand_name.toLowerCase().includes(searchTerm.toLowerCase())) {
                            const regex = new RegExp(searchTerm, 'gi');
                            option.innerHTML = brand.brand_name.replace(regex, match =>
                                `<span class="bg-yellow-200">${match}</span>`
                            );
                } else {
                            option.textContent = brand.brand_name;
                        }

                        // Add click event
                        option.addEventListener('click', () => {
                            const hiddenInput = container.querySelector('input[type="hidden"]');
                            hiddenInput.value = brand.brand_id;
                            searchInput.value = brand.brand_name;
                            optionsContainer.classList.add('hidden');

                            // Trigger change event
                            const event = new Event('change', { bubbles: true });
                            hiddenInput.dispatchEvent(event);
                        });

                        optionsContainer.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching brands:', error);
                    Array.from(optionsContainer.querySelectorAll('.p-2.text-center')).forEach(msg => msg.remove());

                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'p-2 text-center text-red-500';
                    errorMsg.textContent = 'Error loading brands';
                    optionsContainer.appendChild(errorMsg);
                });
        }

        // Asset type change handlers
        document.getElementById('asset_type')?.addEventListener('change', function() {
            const selectedType = this.value;
            const container = document.querySelector('#subcategory_id').closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = container.querySelector('input[type="hidden"]');

            // Reset the selection when asset type changes
            hiddenInput.value = '';
            searchInput.value = '';

            // Load subcategories with empty search value
            loadSubcategories(selectedType, 'subcategory_id', 'subcategory-loading-message', '');
        });

        document.getElementById('edit_asset_type')?.addEventListener('change', function() {
            const selectedType = this.value;
            const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = container.querySelector('input[type="hidden"]');

            // Reset the selection when asset type changes
            hiddenInput.value = '';
            searchInput.value = '';

            // Load subcategories with empty search value
            loadSubcategories(selectedType, 'edit_subcategory_id', 'edit-subcategory-loading-message', '');
        });

        // Load brands when form opens
        document.getElementById('addMasterAssetBtn')?.addEventListener('click', function() {
            loadBrands('brand_id', 'brand-loading-message');
        });

        // Load brands for edit form
        document.querySelectorAll('.edit-asset-btn').forEach(button => {
            button.addEventListener('click', function() {
                // After asset data is loaded, we need to load brands
                const originalFetchComplete = function(asset) {
                    // Load brands in silent mode (don't show dropdown)
                    loadBrands('edit_brand_id', 'edit-brand-loading-message', '', true);

                    // If asset has asset_type, load subcategories filtered by that type in silent mode
                    if (asset.asset_type) {
                        loadSubcategories(asset.asset_type, 'edit_subcategory_id', 'edit-subcategory-loading-message', '', true);
                    }
                };

                // Store the original event for later use
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    const url = args[0];
                    if (typeof url === 'string' && url.includes('/asset-master/') && url.includes(button.dataset.id)) {
                        return originalFetch.apply(this, args)
                            .then(response => response.json())
                            .then(data => {
                                // Call our function after data is fetched
                                if (data && data.masterAsset) {
                                    originalFetchComplete(data.masterAsset);
                                }

                                // Return a new response with the same data
                                const newResponse = new Response(JSON.stringify(data), {
                                    status: 200,
                                    headers: { 'Content-Type': 'application/json' }
                                });
                                return newResponse;
                            });
                    }
                    return originalFetch.apply(this, args);
                };

                // Reset fetch after a short delay
                setTimeout(() => {
                    window.fetch = originalFetch;
                }, 5000);
            });
        });

        // Add master asset button
        document.getElementById('addMasterAssetBtn')?.addEventListener('click', function() {
            const modal = document.getElementById('addMasterAssetModal');
            const content = document.getElementById('addMasterAssetModalContent');
            if (modal && content) {
                openModal(modal, content);
            }
        });

        // Export PDF functionality
        document.getElementById('exportBtn')?.addEventListener('click', () => {
            // Get current URL parameters
            const url = new URL(window.location.href);
            const searchParams = url.searchParams;

            // Create the PDF export URL with the same parameters
            const exportUrl = "{{ route('export-asset-master-pdf') }}?" + searchParams.toString();

            // Redirect to the export URL
            window.open(exportUrl, '_blank');
        });

        // Edit asset functionality
        document.querySelectorAll('.edit-asset-btn').forEach(button => {
            button.addEventListener('click', function() {
                const assetId = this.dataset.id;
                const editModal = document.getElementById('editMasterAssetModal');
                const editContent = document.getElementById('editMasterAssetModalContent');
                const loadingIndicator = document.getElementById('edit-loading');
                const formContent = document.getElementById('edit-form-content');
                const submitBtn = document.getElementById('edit-submit-btn');

                if (editModal && editContent) {
                    // Reset form and show loading
                    document.getElementById('editMasterAssetForm').reset();
                    document.getElementById('editMasterAssetForm').action = `{{ url('asset-master') }}/${assetId}`;

                    // Show loading indicator
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    if (formContent) formContent.classList.add('hidden');
                    if (submitBtn) submitBtn.disabled = true;

                    // Open the modal while loading
                    openModal(editModal, editContent);

                    // Fetch asset data with proper error handling
                    console.log(`Fetching asset master data for ID: ${assetId}`);

                    fetch(`{{ url('asset-master') }}/${assetId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        // First check if response is ok (status in 200-299 range)
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        // Check Content-Type header
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error(`Expected JSON response but got ${contentType}`);
                        }

                        return response.json();
                    })
                    .then(data => {
                        console.log('Asset data received:', data);

                        if (!data || !data.masterAsset) {
                            throw new Error('Invalid response data structure');
                        }

                        const asset = data.masterAsset;
                        // Fill in form fields
                        document.getElementById('edit_asset_name').value = asset.asset_name || '';
                        document.getElementById('edit_description').value = asset.description || '';

                        // Set asset type
                        if (asset.asset_type) {
                            document.getElementById('edit_asset_type').value = asset.asset_type;

                            // Load subcategories based on asset type with silent mode (don't show dropdown)
                            loadSubcategories(asset.asset_type, 'edit_subcategory_id', 'edit-subcategory-loading-message', '', true);

                            // Set the selected subcategory value immediately
                            if (asset.subcategory_id && asset.subcategory_name) {
                                setSelectValue('edit_subcategory_id', asset.subcategory_id, asset.subcategory_name);
                            }
                        }

                        // Load brands with silent mode (don't show dropdown)
                        loadBrands('edit_brand_id', 'edit-brand-loading-message', '', true);

                        // Set the selected brand value immediately
                        if (asset.brand_id && asset.brand_name) {
                            setSelectValue('edit_brand_id', asset.brand_id, asset.brand_name);
                        }

                        // Handle checkboxes
                        document.getElementById('edit_is_depreciable').checked = Boolean(asset.is_depreciable);
                        document.getElementById('edit_needs_calibration').checked = Boolean(asset.needs_calibration);

                        // Update toggle status text
                        const depreciationStatus = document.querySelector('#editMasterAssetModal .depreciation-status');
                        if (depreciationStatus) {
                            depreciationStatus.textContent = asset.is_depreciable ? 'Yes' : 'No';
                        }

                        const calibrationStatus = document.querySelector('#editMasterAssetModal .calibration-status');
                        if (calibrationStatus) {
                            calibrationStatus.textContent = asset.needs_calibration ? 'Yes' : 'No';
                        }

                        // Handle image if present
                        if (asset.reference_image_path) {
                            const imgElement = document.querySelector('#edit-image-container img');
                            if (imgElement) {
                                try {
                                    // Define the base URL - using the confirmed server location
                                    const baseUrl = "http://localhost:5000/public";

                                    // Process the image URL
                                    let imageUrl = asset.reference_image_path;

                                    // If URL is not absolute, prepend the base URL
                                    if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('//')) {
                                        // Remove leading slash if present to avoid double slashes
                                        if (imageUrl.startsWith('/')) {
                                            imageUrl = imageUrl.substring(1);
                                        }
                                        imageUrl = `${baseUrl}/${imageUrl}`;
                                    }

                                    // Set the image source and display it
                                    imgElement.src = imageUrl;
                                    document.getElementById('edit-image-container').classList.remove('hidden');

                                    // Ensure new image preview is hidden
                                    document.getElementById('edit-image-preview').classList.add('hidden');

                                    // Reset file input
                                    const fileInput = document.getElementById('edit_image_file');
                                    if (fileInput) {
                                        fileInput.value = '';
                                    }

                                    // Add debug info
                                    imgElement.onerror = function() {
                                        console.error(`Failed to load image from URL: ${imageUrl}`);
                                        document.getElementById('edit-image-container').classList.add('hidden');
                                    };

                                    imgElement.onload = function() {
                                        console.log(`Berhasil memuat gambar dari URL: ${imageUrl}`);
                                    };
                                } catch (error) {
                                    console.error('Error setting image URL:', error);
                                }
                            }
                        } else {
                            const currentImage = document.getElementById('edit-image-container');
                            if (currentImage) currentImage.classList.add('hidden');
                        }

                        // Hide loading and show form
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        if (formContent) formContent.classList.remove('hidden');
                        if (submitBtn) submitBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching asset data:', error);

                        // Hide loading indicator
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');

                        // Show an error message in the modal
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

                        // Reattach close event listeners
                        formContent.querySelectorAll('.close-modal').forEach(btn => {
                            btn.addEventListener('click', function() {
                                closeModal(editModal, editContent);
                            });
                        });
                    });
                }
            });
        });

        // Delete asset functionality
        document.querySelectorAll('.delete-asset-btn').forEach(button => {
            button.addEventListener('click', function() {
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

        // Prevent multiple submissions for delete form
        document.getElementById('delete-form')?.addEventListener('submit', function(event) {
            // Prevent multiple submissions
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // Save original button text
                const originalText = submitBtn.innerHTML;

                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menghapus...</span></div>';

                // Set timeout to re-enable button after 10 seconds (in case of network issues)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });

        // Function to reset the add master asset form
        function resetAddMasterAssetForm() {
            const form = document.getElementById('createMasterAssetForm');
            if (form) {
                form.reset();

                // Reset image preview
                const imagePreview = document.getElementById('image-preview');
                if (imagePreview) {
                    imagePreview.classList.add('hidden');
                }

                // Reset custom dropdowns
                form.querySelectorAll('.custom-select-container').forEach(container => {
                    const searchInput = container.querySelector('.search-input');
                    const hiddenInput = container.querySelector('input[type="hidden"]');
                    if (searchInput) searchInput.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                });

                // Reset error messages
                form.querySelectorAll('.error-message').forEach(error => {
                    error.classList.add('hidden');
                });

                // Reset toggle switches
                form.querySelectorAll('.depreciation-status, .calibration-status').forEach(status => {
                    status.textContent = 'No';
                });
            }
        }

        // Function to reset the edit master asset form
        function resetEditMasterAssetForm() {
            const form = document.getElementById('editMasterAssetForm');
            if (form) {
                form.reset();

                // Reset image preview
                const imageContainer = document.getElementById('edit-image-container');
                if (imageContainer) {
                    imageContainer.classList.add('hidden');
                }

                // Remove any "remove_image" input
                const removeImageInput = form.querySelector('input[name="remove_image"]');
                if (removeImageInput) {
                    removeImageInput.remove();
                }

                // Reset custom dropdowns
                form.querySelectorAll('.custom-select-container').forEach(container => {
                    const searchInput = container.querySelector('.search-input');
                    const hiddenInput = container.querySelector('input[type="hidden"]');
                    if (searchInput) searchInput.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                });

                // Reset error messages
                form.querySelectorAll('.error-message').forEach(error => {
                    error.classList.add('hidden');
                });

                // Reset toggle switches
                form.querySelectorAll('.depreciation-status, .calibration-status').forEach(status => {
                    status.textContent = 'No';
                });

                // Reset loading state
                const loadingIndicator = document.getElementById('edit-loading');
                const formContent = document.getElementById('edit-form-content');
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                if (formContent) formContent.classList.add('hidden');
            }
        }

        // Function to reset the import modal
        function resetImportModal() {
            // Reset to step 1
            document.getElementById('import-step-1')?.classList.remove('hidden');
            document.getElementById('import-step-2')?.classList.add('hidden');

            // Reset file input and preview
            const fileInput = document.getElementById('excel_file');
            if (fileInput) fileInput.value = '';

            const previewContainer = document.getElementById('excel-file-name');
            if (previewContainer) previewContainer.classList.add('hidden');

            // Reset other elements
            document.getElementById('excel-error')?.classList.add('hidden');
            document.getElementById('excel-loading')?.classList.add('hidden');
            document.getElementById('preview-btn')?.setAttribute('disabled', 'disabled');
        }

        // Function to reset the delete modal
        function resetDeleteModal() {
            const form = document.getElementById('delete-form');
            if (form) {
                form.reset();
                document.getElementById('delete-asset-name').textContent = '';
            }
        }

        // Modal close buttons
        document.querySelectorAll('.close-modal').forEach(closeButton => {
            closeButton.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="Content"]');

                // Reset the appropriate form based on which modal is being closed
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

        // Close modal when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const content = this.querySelector('[id$="Content"]');

                    // Reset the appropriate form based on which modal is being closed
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

        // File upload preview for add modal
        document.getElementById('image_file')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.querySelector('#image-preview img');
                    imgElement.src = e.target.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // File upload preview for edit modal
        document.getElementById('edit_image_file')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.querySelector('#edit-image-container img');
                    imgElement.src = e.target.result;
                    document.getElementById('edit-image-container').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove image button for add modal
        document.getElementById('remove-image')?.addEventListener('click', function() {
            const fileInput = document.getElementById('image_file');
            const previewContainer = document.getElementById('image-preview');

            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }

            if (previewContainer) {
                previewContainer.classList.add('hidden'); // Hide the preview
            }
        });

        // Add remove image functionality for edit modal
        document.querySelector('#edit-image-container .remove-image-btn')?.addEventListener('click', function() {
            const fileInput = document.getElementById('edit_image_file');
            const imageContainer = document.getElementById('edit-image-container');

            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }

            if (imageContainer) {
                imageContainer.classList.add('hidden'); // Hide the preview
            }

            // Add a hidden input to signal that the image should be removed
            const removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.name = 'remove_image';
            removeImageInput.value = '1';

            // Add to form if not already present
            const form = document.getElementById('editMasterAssetForm');
            if (form && !form.querySelector('input[name="remove_image"]')) {
                form.appendChild(removeImageInput);
            }
        });

        // Toggle switches for add form
        document.getElementById('is_depreciable')?.addEventListener('change', function() {
            document.querySelectorAll('.depreciation-status')[0].textContent = this.checked ? 'Yes' : 'No';
        });

        document.getElementById('needs_calibration')?.addEventListener('change', function() {
            document.querySelectorAll('.calibration-status')[0].textContent = this.checked ? 'Yes' : 'No';
        });

        // Toggle switches for edit form
        document.getElementById('edit_is_depreciable')?.addEventListener('change', function() {
            document.querySelectorAll('.depreciation-status')[1].textContent = this.checked ? 'Yes' : 'No';
        });

        document.getElementById('edit_needs_calibration')?.addEventListener('change', function() {
            document.querySelectorAll('.calibration-status')[1].textContent = this.checked ? 'Yes' : 'No';
        });

        // Pagination helpers
        window.changePage = function(page) {
            if (page < 1) return;

            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        window.changePerPage = function(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', perPage);
            url.searchParams.set('page', 1); // Reset to page 1 when changing items per page
            window.location.href = url.toString();
        };

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = document.getElementById('searchInput')?.value.trim() || '';
            const typeValue = document.getElementById('assetTypeFilter')?.value || '';
            const sortValue = document.getElementById('sortOrder')?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'type', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (typeValue) url.searchParams.set('type', typeValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            // Reset to page 1 when filters change
            url.searchParams.set('page', 1);

            // Navigate to the new URL
            window.location.href = url.toString();
        }

        // Add event listeners with debounce for search
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Add event listeners for select filters
        document.getElementById('assetTypeFilter')?.addEventListener('change', applyFilters);
        document.getElementById('sortOrder')?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
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

        // Update pagination functions to preserve filters
        window.changePage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        window.changePerPage = function(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', perPage);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        };

        // Import master asset button
        document.getElementById('importMasterAssetBtn')?.addEventListener('click', function() {
            const modal = document.getElementById('importMasterAssetModal');
            const content = document.getElementById('importMasterAssetModalContent');
            if (modal && content) {
                openModal(modal, content);
            }
        });

        // Excel file upload preview
        document.getElementById('excel_file')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                document.getElementById('file-name-text').textContent = file.name;
                document.getElementById('excel-file-name').classList.remove('hidden');
            }
        });

        // Remove excel file button
        document.getElementById('remove-excel')?.addEventListener('click', function() {
            const fileInput = document.getElementById('excel_file');
            const previewContainer = document.getElementById('excel-file-name');

            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }

            if (previewContainer) {
                previewContainer.classList.add('hidden'); // Hide the preview
            }

            // Disable import button when file is removed
            document.getElementById('import-submit-btn').disabled = true;
            document.getElementById('import-submit-btn').classList.add('opacity-50', 'cursor-not-allowed');
        });

        // Variables to store parsed Excel data
        let excelData = [];
        let headers = [];
        let hasValidationErrors = false;

        // Function to open the preview modal
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

        // Function to close the preview modal
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

        // Excel file upload preview and validation
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

        // Enable/disable preview button based on file selection
        document.getElementById('excel_file')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                document.getElementById('file-name-text').textContent = file.name;
                document.getElementById('excel-file-name').classList.remove('hidden');
                previewBtn.disabled = false;
            } else {
                previewBtn.disabled = true;
            }
        });

        // Preview button functionality
        previewBtn?.addEventListener('click', function() {
            const file = excelFileInput.files[0];
            if (!file) {
                excelError.textContent = 'Silakan pilih file Excel terlebih dahulu.';
                excelError.classList.remove('hidden');
                return;
            }

            // Reset previous errors and show loading
            excelError.classList.add('hidden');
            excelLoading.classList.remove('hidden');
            previewBtn.disabled = true;

            // Parse the Excel file
            parseExcelFile(file).then(data => {
                console.log('Parsed Excel data:', data);

                // Hide loading and enable preview button
                excelLoading.classList.add('hidden');
                previewBtn.disabled = false;

                if (data.length === 0) {
                    excelError.textContent = 'File Excel tampaknya kosong atau tidak dapat diproses.';
                    excelError.classList.remove('hidden');
                    return;
                }

                // Store parsed data
                parsedExcelData = data;

                // Display the data in preview table
                populatePreviewTable(data);

                // Switch to preview step
                importStep1.classList.add('hidden');
                importStep2.classList.remove('hidden');

                // Prepare form data for submission
                excelDataInput.value = JSON.stringify(data);
            }).catch(error => {
                console.error('Gagal memproses file Excel:', error);
                excelLoading.classList.add('hidden');
                previewBtn.disabled = false;
                excelError.textContent = 'Gagal memproses file Excel: ' + error.message;
                excelError.classList.remove('hidden');
            });
        });

        // Back button from preview to upload
        backToUploadBtn?.addEventListener('click', function() {
            importStep2.classList.add('hidden');
            importStep1.classList.remove('hidden');
        });

        // Function to parse Excel file
        async function parseExcelFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    try {
                        const data = e.target.result;
                        const workbook = XLSX.read(data, { type: 'array' });

                        // Get the first sheet
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];

                        // Convert to JSON
                        const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                            header: 1,
                            defval: "" // Default value for empty cells
                        });

                        // Parse the data to extract headers and content
                        const result = processExcelData(jsonData);
                        resolve(result);
                    } catch (error) {
                        reject(error);
                    }
                };

                reader.onerror = function(error) {
                    reject(error);
                };

                reader.readAsArrayBuffer(file);
            });
        }

        // Process Excel data to match API expectations
        function processExcelData(data) {
            // Skip empty rows
            const nonEmptyRows = data.filter(row => row.some(cell => cell !== ""));

            if (nonEmptyRows.length < 2) {
                // Need at least headers and one data row
                return [];
            }

            // Get headers (first row)
            const headers = nonEmptyRows[0].map(header =>
                String(header).trim().toLowerCase().replace(/\s+/g, '_')
            );

            // Required headers and their mapping to API fields
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
                'brand': 'brand_name',
                'brand_name': 'brand_name',
                'nama_brand': 'brand_name',
                'nama brand': 'brand_name',
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

            // Find header indexes
            const headerIndexes = {};
            headers.forEach((header, index) => {
                // Check if this header matches any of our expected headers
                for (const [key, value] of Object.entries(headerMapping)) {
                    if (header === key || header.includes(key)) {
                        headerIndexes[value] = index;
                        break;
                    }
                }
            });

            // Process data rows
            const processedData = [];
            const warnings = [];

            // Skip header row, process data rows
            for (let i = 1; i < nonEmptyRows.length; i++) {
                const row = nonEmptyRows[i];
                const rowData = {
                    asset_name: '',
                    asset_type: '',
                    subcategory_name: '',
                    brand_name: '',
                    description: '',
                    is_depreciable: false,
                    needs_calibration: false
                };

                // Extract values based on header mapping
                for (const [key, index] of Object.entries(headerIndexes)) {
                    if (index !== undefined && index < row.length) {
                        let value = row[index];

                        // Handle boolean fields
                        if (key === 'is_depreciable' || key === 'needs_calibration') {
                            // Convert various formats to boolean
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

                // Validate required fields
                if (!rowData.asset_name) {
                    warnings.push(`Baris ${i+1}: Nama aset tidak ditemukan`);
                }

                if (!rowData.asset_type) {
                    warnings.push(`Baris ${i+1}: Tipe aset tidak ditemukan`);
                } else {
                    // Normalize asset_type
                    if (rowData.asset_type.toLowerCase().includes('medical')) {
                        rowData.asset_type = 'medical';
                    } else {
                        rowData.asset_type = 'non_medical';
                    }
                }

                if (!rowData.subcategory_name) {
                    warnings.push(`Baris ${i+1}: Kategori tidak ditemukan`);
                }

                if (!rowData.brand_name) {
                    warnings.push(`Baris ${i+1}: Merk tidak ditemukan`);
                }

                // Add row number for display
                rowData._rowNum = i;

                processedData.push(rowData);
            }

            // Store warnings for display
            if (warnings.length > 0) {
                showWarnings(warnings);
            } else {
                hideWarnings();
            }

            return processedData;
        }

        // Populate preview table with data
        function populatePreviewTable(data) {
            // Clear existing rows
            previewTableBody.innerHTML = '';

            // Update count
            previewCount.textContent = `${data.length} data ditemukan`;

            // Find duplicate entries if any
            const duplicates = findDuplicates(data);
            const hasDuplicates = Object.keys(duplicates).length > 0;

            // Add rows
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                row.innerHTML = `
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${index + 1}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.asset_name || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.asset_type ? formatAssetType(item.asset_type) : '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.subcategory_name || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${item.brand_name || '-'}</td>
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

            // Show duplicate warnings if any found
            if (hasDuplicates) {
                const warnings = [];
                for (const [key, indexes] of Object.entries(duplicates)) {
                    if (indexes.length > 1) {
                        const item = data[indexes[0]];
                        warnings.push(`Duplikat ditemukan: "${item.asset_name}" (${formatAssetType(item.asset_type)}, ${item.subcategory_name}, ${item.brand_name})`);
                    }
                }
                showWarnings(warnings);
            }
        }

        // Function to find duplicate entries in data
        function findDuplicates(data) {
            const duplicateMap = {};

            data.forEach((item, index) => {
                // Create a unique key from asset properties
                const key = `${item.asset_name}|${item.asset_type}|${item.subcategory_name}|${item.brand_name}`.toLowerCase();

                // Add to duplicates map
                if (!duplicateMap[key]) {
                    duplicateMap[key] = [];
                }
                duplicateMap[key].push(index);
            });

            // Filter out non-duplicates (entries with only one index)
            return Object.fromEntries(
                Object.entries(duplicateMap).filter(([key, indexes]) => indexes.length > 1)
            );
        }

        // Format asset type for display
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

        // Show warnings in the UI
        function showWarnings(warnings) {
            if (!warnings || warnings.length === 0) {
                hideWarnings();
                return;
            }

            // Clear previous warnings
            warningList.innerHTML = '';

            // Add new warnings
            warnings.forEach(warning => {
                const li = document.createElement('li');
                li.textContent = warning;
                warningList.appendChild(li);
            });

            // Show warnings container
            previewWarnings.classList.remove('hidden');
        }

        // Hide warnings
        function hideWarnings() {
            previewWarnings.classList.add('hidden');
        }

        // Remove excel file button
        document.getElementById('remove-excel')?.addEventListener('click', function() {
            const fileInput = document.getElementById('excel_file');
            const previewContainer = document.getElementById('excel-file-name');

            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }

            if (previewContainer) {
                previewContainer.classList.add('hidden'); // Hide the preview
            }

            // Disable preview button
            previewBtn.disabled = true;

            // Hide any error messages
            excelError.classList.add('hidden');
        });

        // Handle import form submission with AJAX
        importForm?.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent traditional form submission

            // Get form data
            const formData = new FormData(this);

            // Add the Excel file to the form data
            const originalFileInput = document.getElementById('excel_file');
            if (originalFileInput && originalFileInput.files.length > 0) {
                formData.append('excel_file_upload', originalFileInput.files[0]);
            }

            // Show loading state
            const importBtn = document.getElementById('import-btn');
            const originalBtnText = importBtn.innerHTML;
            importBtn.disabled = true;
            importBtn.innerHTML = `
                <div class="flex items-center justify-center">
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    <span>Mengimpor...</span>
                </div>
            `;

            // Send AJAX request
            fetch('{{ route('asset-master.import') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        // Add status to the data object
                        data.status = response.status;
                        return data;
                    });
                } else {
                    // If not JSON, it's likely an error page or redirect
                    throw new Error('Invalid response format');
                }
            })
            .then(data => {
                // Reset button state
                importBtn.disabled = false;
                importBtn.innerHTML = originalBtnText;

                if (data.status >= 200 && data.status < 300) {
                    // Success response
                    console.log('Import successful:', data);

                    // Close the modal
                    const modal = document.getElementById('importMasterAssetModal');
                    const content = document.getElementById('importMasterAssetModalContent');
                    closeModal(modal, content);

                    // Show success notification
                    showNotification('success', data.message || 'Assets imported successfully!');

                    // Reload the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Error response
                    console.error('Import error:', data);

                    // Show error notification toast (outside the modal)
                    let errorMessage = data.message || 'An error occurred during import.';
                    if (data.errors) {
                        const errorList = Object.values(data.errors);
                        if (errorList.length > 0) {
                            errorMessage += ': ' + errorList.join(', ');
                        }
                    }
                    showNotification('error', errorMessage);
                }
            })
            .catch(error => {
                // Reset button state
                importBtn.disabled = false;
                importBtn.innerHTML = originalBtnText;

                console.error('Import fetch error:', error);

                // Show error notification toast
                showNotification('error', 'An unexpected error occurred. Please try again.');
            });
        });

        // Helper function to show notifications
        function showNotification(type, message) {
            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.role = 'alert';

            // Check if message contains HTML
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

                // Structure for the notification
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-start';

                // Icon container
                const iconContainer = document.createElement('div');
                iconContainer.className = 'py-1 flex-shrink-0';
                iconContainer.innerHTML = `
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                `;

                // Content container
                const contentContainer = document.createElement('div');
                contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                // Title
                const title = document.createElement('p');
                title.className = 'font-bold';
                title.textContent = 'Error!';
                contentContainer.appendChild(title);

                // Message container
                const messageContainer = document.createElement('div');
                messageContainer.className = 'error-message';

                // Handle HTML content
                if (hasHTML) {
                    messageContainer.innerHTML = message;
                } else {
                    messageContainer.textContent = message;
                }

                contentContainer.appendChild(messageContainer);

                // Close button
                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
                    notification.remove();
                };

                // Assemble the notification
                wrapper.appendChild(iconContainer);
                wrapper.appendChild(contentContainer);
                wrapper.appendChild(closeBtn);
                notification.appendChild(wrapper);
            }

            // Add to document
            document.body.appendChild(notification);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            showNotification(type, message);
        }

        // Function to validate field and show/hide error message
        function validateField(field, customCheck = null) {
            if (!field) return true; // Skip if field doesn't exist

            let isValid = true;
            if (customCheck !== null) {
                isValid = customCheck;
            } else if (field.tagName.toLowerCase() === 'select') {
                isValid = field.value !== '';
            } else {
                isValid = field.value.trim() !== '';
            }

            // Find the error message element
            const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

            if (!isValid) {
                field.classList.add('border-red-500');
                if (errorElement) errorElement.classList.remove('hidden');
            } else {
                field.classList.remove('border-red-500');
                if (errorElement) errorElement.classList.add('hidden');
            }

            return isValid;
        }

        // Form validation for Add Master Asset
        document.getElementById('createMasterAssetForm')?.addEventListener('submit', function(event) {
            // Get all required fields from the form
            const assetName = document.querySelector('#createMasterAssetForm input[name="asset_name"]');
            const assetType = document.querySelector('#createMasterAssetForm select[name="asset_type"]');

            // For custom select dropdowns, get the hidden inputs
            const subcategoryInput = document.querySelector('#createMasterAssetForm input[name="subcategory_id"]');
            const brandInput = document.querySelector('#createMasterAssetForm input[name="brand_id"]');

            // Validate required fields
            const isAssetNameValid = validateField(assetName);
            const isAssetTypeValid = validateField(assetType);

            // For custom select dropdowns, check the hidden input value
            const isSubcategoryValid = validateField(
                subcategoryInput.closest('.custom-select-container').querySelector('.search-input'),
                subcategoryInput && subcategoryInput.value ? true : false
            );

            const isBrandValid = validateField(
                brandInput.closest('.custom-select-container').querySelector('.search-input'),
                brandInput && brandInput.value ? true : false
            );

            // If any validation fails, prevent form submission
            if (!isAssetNameValid || !isAssetTypeValid || !isSubcategoryValid || !isBrandValid) {
                event.preventDefault();
                showToast('Silakan isi semua field yang diperlukan', 'error');
                return false;
            }

            // Remove empty fields from form submission
            const formInputs = this.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                // Skip checkbox inputs and file inputs
                if (input.type === 'checkbox' || input.type === 'file' || input.type === 'hidden') {
                    return;
                }

                // If the input is empty, disable it so it won't be included in form submission
                if (input.value === '' || input.value === null) {
                    input.disabled = true;
                }
            });

            // Prevent multiple submissions
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // Save original button text
                const originalText = submitBtn.innerHTML;

                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menyimpan...</span></div>';

                // Set timeout to re-enable button after 10 seconds (in case of network issues)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalText;

                    // Re-enable all inputs that were disabled
                    formInputs.forEach(input => {
                        input.disabled = false;
                    });
                }, 10000);
            }
        });

        // Form validation for Edit Master Asset
        document.getElementById('editMasterAssetForm')?.addEventListener('submit', function(event) {
            const assetName = document.getElementById('edit_asset_name');
            const assetType = document.getElementById('edit_asset_type');
            const subcategoryId = document.getElementById('edit_subcategory_id');
            const brandId = document.getElementById('edit_brand_id');

            // Log values for debugging
            console.log("Edit Form Validation - Values:", {
                asset_name: assetName.value,
                asset_type: assetType.value,
                subcategory_id: subcategoryId.value,
                brand_id: brandId.value
            });

            // Validate required fields
            const isAssetNameValid = validateField(assetName);
            const isAssetTypeValid = validateField(assetType);

            // For custom select dropdowns, explicitly look at the hidden input value
            const subcategoryContainer = subcategoryId.closest('.custom-select-container');
            const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
            const isSubcategoryValid = subcategoryId.value ? true : false;

            // Show visual feedback if invalid
            if (!isSubcategoryValid) {
                subcategorySearchInput.classList.add('border-red-500');
                const errorElement = subcategoryContainer.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.remove('hidden');
            }

            const brandContainer = brandId.closest('.custom-select-container');
            const brandSearchInput = brandContainer.querySelector('.search-input');
            const isBrandValid = brandId.value ? true : false;

            // Show visual feedback if invalid
            if (!isBrandValid) {
                brandSearchInput.classList.add('border-red-500');
                const errorElement = brandContainer.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.remove('hidden');
            }

            // If any validation fails, prevent form submission
            if (!isAssetNameValid || !isAssetTypeValid || !isSubcategoryValid || !isBrandValid) {
                event.preventDefault();
                console.log("Validation failed:", {
                    isAssetNameValid,
                    isAssetTypeValid,
                    isSubcategoryValid,
                    isBrandValid
                });
                showToast('Silakan isi semua field yang diperlukan', 'error');
                return false;
            }

                        // Make sure hidden inputs for custom selects are properly included
            const hiddenInputs = ['edit_subcategory_id', 'edit_brand_id', 'edit_asset_type'];
            hiddenInputs.forEach(id => {
                const input = document.getElementById(id);
                if (input && input.value) {
                    // Ensure the field will be submitted with correct value
                    input.disabled = false;

                    // Also add a data-debug attribute to verify in browser inspection
                    input.setAttribute('data-included-in-submission', 'true');
                    console.log(`Including ${id} in submission with value: ${input.value}`);
                }
            });

            // Remove empty fields from form submission
            const formInputs = this.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                // Skip checkbox inputs, file inputs, and special inputs
                if (input.type === 'checkbox' || input.type === 'file' ||
                    input.name === '_token' || input.name === '_method' ||
                    hiddenInputs.includes(input.id)) {
                    return;
                }

                // If the input is empty, disable it so it won't be included in form submission
                if (input.value === '' || input.value === null) {
                    input.disabled = true;
                }
            });

            // Prevent multiple submissions
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // Save original button text
                const originalText = submitBtn.innerHTML;

                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menyimpan...</span></div>';

                // Set timeout to re-enable button after 10 seconds (in case of network issues)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalText;

                    // Re-enable all inputs that were disabled
                    formInputs.forEach(input => {
                        input.disabled = false;
                    });
                }, 10000);
            }
        });

        // Add input event listeners to clear error styling when typing in add form
        document.querySelector('#createMasterAssetForm input[name="asset_name"]')?.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        document.querySelector('#createMasterAssetForm select[name="asset_type"]')?.addEventListener('change', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        // Add input event listeners to clear error styling when typing in edit form
        document.getElementById('edit_asset_name')?.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        document.getElementById('edit_asset_type')?.addEventListener('change', function() {
            this.classList.remove('border-red-500');
            const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        });

        // Load categories and brands when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize custom select dropdowns
            initCustomSelects();

            // Enhance each search input for dynamic search
            document.querySelectorAll('.custom-select-container').forEach(container => {
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');

                if (searchInput && hiddenInput) {
                    // Show dropdown when clicking on the input (not just on focus)
                    searchInput.addEventListener('click', function(e) {
            const optionsContainer = container.querySelector('.options-container');
                        if (optionsContainer && optionsContainer.classList.contains('hidden')) {
                            // Trigger the appropriate load function based on input type
                            if (hiddenInput.id.includes('subcategory')) {
                                const assetTypeId = hiddenInput.id === 'edit_subcategory_id' ? 'edit_asset_type' : 'asset_type';
                                const assetTypeSelect = document.getElementById(assetTypeId);
                                if (assetTypeSelect && assetTypeSelect.value) {
                                    loadSubcategories(assetTypeSelect.value, hiddenInput.id, `${hiddenInput.id}-loading-message`, '');
                                }
                            } else if (hiddenInput.id.includes('brand')) {
                                loadBrands(hiddenInput.id, `${hiddenInput.id}-loading-message`, '');
                            }
                        }
                    });
                }
            });

            // For initial page load, pre-load brands (don't show dropdown)
            document.querySelectorAll('[id$="brand_id"]').forEach(brandInput => {
                // Create a loading indicator but don't display it initially
                const brandContainer = brandInput.closest('.custom-select-container');
                const optionsContainer = brandContainer.querySelector('.options-container');
                if (!optionsContainer.querySelector(`#${brandInput.id}-loading-message`)) {
                    const loadingMsg = document.createElement('div');
                    loadingMsg.id = `${brandInput.id}-loading-message`;
                    loadingMsg.className = 'p-2 text-center text-gray-500 hidden';
                    loadingMsg.textContent = 'Memuat data merk...';
                    optionsContainer.appendChild(loadingMsg);
                }

                // Preload the data in the background
                fetch('/brands?json=true', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .catch(error => console.error('Error preloading brands:', error));
            });

            // Same for subcategories if asset type is already selected
            const assetTypeSelect = document.getElementById('asset_type');
            if (assetTypeSelect && assetTypeSelect.value) {
                const subcategoryId = 'subcategory_id';
                // Create a loading indicator but don't display it
                const subcategoryContainer = document.getElementById(subcategoryId).closest('.custom-select-container');
                const optionsContainer = subcategoryContainer.querySelector('.options-container');
                if (!optionsContainer.querySelector(`#${subcategoryId}-loading-message`)) {
                    const loadingMsg = document.createElement('div');
                    loadingMsg.id = `${subcategoryId}-loading-message`;
                    loadingMsg.className = 'p-2 text-center text-gray-500 hidden';
                    loadingMsg.textContent = 'Memuat kategori...';
                    optionsContainer.appendChild(loadingMsg);
                }

                // Preload the data in the background
                fetch(`/categories?asset_type=${assetTypeSelect.value}&json=true`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .catch(error => console.error('Error preloading subcategories:', error));
            }
        });

        // For custom select dropdowns
        document.querySelectorAll('.custom-select-container .search-input').forEach(input => {
            input.addEventListener('input', function() {
                const container = this.closest('.custom-select-container');
                const selectElement = container.querySelector('select');
                if (selectElement) {
                    selectElement.classList.remove('border-red-500');
                    const errorElement = container.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                }
            });
        });

        // Add slide-in animation and styling for error messages to CSS
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                @keyframes slideInRight {
                    from { transform: translateX(100%); }
                    to { transform: translateX(0); }
                }
                .animate-slide-in-right {
                    animation: slideInRight 0.3s ease-out forwards;
                }
            </style>
        `);
    });
</script>
@endpush
