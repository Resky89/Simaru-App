@extends('Layout.app')

@section('title', 'Kategori Aset')

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Sub Categories Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">KATEGORI</h1>

                    <!-- Button Add Sub Categories -->
                    <div class="flex flex-wrap gap-3">
                        @if(hasPermission('asset-subcategory:import'))
                        <button id="importCategoryBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                            </svg>
                            <span class="text-base">Impor Excel</span>
                        </button>
                        @endif
                        @if(hasPermission('asset-subcategory:create'))
                        <button id="addSubCategoryBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            <span class="text-base">Tambah Kategori</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama kategori..."
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
                            <option value="" selected>Semua Tipe</option>
                            <option value="medical">Medis</option>
                            <option value="non_medical">Non Medis</option>
                        </select>

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

                <!-- Sub Categories Table will go here -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[25%]">Tipe Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kategori</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $subcategory)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if($subcategory['asset_type'] == 'medical')
                                            Medis
                                        @elseif($subcategory['asset_type'] == 'non_medical')
                                            Non Medis
                                        @else
                                            {{ $subcategory['asset_type'] }}
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $subcategory['subcategory_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $subcategory['description'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="flex items-center space-x-2 justify-center">
                                            @if(hasPermission('asset-subcategory:edit'))
                                            <button class="edit-subcategory-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                    data-subcategory-id="{{ $subcategory['subcategory_id'] }}"
                                                    data-asset-type="{{ $subcategory['asset_type'] }}"
                                                    data-subcategory-name="{{ $subcategory['subcategory_name'] }}"
                                                    data-description="{{ $subcategory['description'] ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            @endif
                                            @if(hasPermission('asset-subcategory:delete'))
                                            <button class="delete-subcategory-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                    data-subcategory-id="{{ $subcategory['subcategory_id'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-3 text-xs text-center border-t border-[#EEF1F4]">Tidak ada kategori ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ $pagination['prev_page_url'] ?? '#' }}"
                            class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>
                    <div class="flex gap-2">
                            @php
                                $currentPage = $pagination['current_page'] ?? 1;
                                $lastPage = $pagination['last_page'] ?? 1;
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
                        <a href="{{ $pagination['next_page_url'] ?? '#' }}"
                            class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                                </svg>
                        </a>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($pagination) && is_array($pagination))
                                Menampilkan {{ $pagination['from'] }} sampai {{ $pagination['to'] }} dari {{ $pagination['total'] }} data
                            @else
                                Menampilkan 0 data
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['per_page']) && $pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ isset($pagination['per_page']) && $pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ isset($pagination['per_page']) && $pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ isset($pagination['per_page']) && $pagination['per_page'] == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Sub Categories -->
@if(hasPermission('asset-subcategory:create'))
<div id="addSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="subCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">TAMBAH KATEGORI</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="createSubCategoryForm" action="{{ route('categories.store') }}" method="POST" data-no-loading novalidate>
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Category Dropdown -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Tipe Aset <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="asset_type" id="add_asset_type" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="medical">Medis</option>
                                        <option value="non_medical">Non Medis</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe harus dipilih</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subcategory_name" id="add_subcategory_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Ketik di sini" required>
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus diisi</div>
                            </div>

                            <!-- Description Field -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Deskripsi
                                </label>
                                <textarea name="description" id="add_description"
                                    class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Ketik deskripsi di sini" rows="3"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Edit Sub Category -->
@if(hasPermission('asset-subcategory:edit'))
<div id="editSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editSubCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT KATEGORI</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="editSubCategoryForm" action="" method="POST" data-no-loading novalidate>
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Hidden subcategory ID -->
                            <input type="hidden" id="editSubCategoryId" name="subcategory_id">

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Tipe Aset <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="editAssetType" name="asset_type" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="medical">Medis</option>
                                        <option value="non_medical">Non Medis</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe harus dipilih</div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editSubCategoryName" name="subcategory_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Ketik di sini" required>
                                <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus diisi</div>
                            </div>

                            <!-- Description Field -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Deskripsi
                                </label>
                                <textarea id="editDescription" name="description"
                                    class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Ketik deskripsi di sini" rows="3"></textarea>
                            </div>

                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Delete Sub Category -->
@if(hasPermission('asset-subcategory:delete'))
<div id="deleteSubCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteSubCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">HAPUS KATEGORI</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Batal
                            </button>
                            <form id="deleteSubCategoryForm" action="" method="POST" data-no-loading class="w-1/2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" id="deleteSubCategoryId" name="subcategory_id">
                                <button type="submit" class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Import Category Modal -->
@if(hasPermission('asset-subcategory:import'))
<div id="importCategoryModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="importCategoryModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPOR KATEGORI</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Step 1: File Selection -->
                <div id="import-category-step-1" class="block">
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Import Instructions -->
                            <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                <p class="font-medium text-blue-600 mb-2">Petunjuk Impor:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Gunakan format template Excel untuk mengimpor</li>
                                    <li>Kolom yang diperlukan: Nama Kategori, Tipe Aset</li>
                                    <li>Maksimal 100 data per impor</li>
                                    <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                </ul>
                                <div class="mt-3 flex justify-end">
                                    <a href="{{ asset('docs/ImportKategoriTemplate.xlsx') }}" download class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                                    <div id="category-excel-file-name" class="mt-2 mb-4 w-full hidden">
                                        <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span id="category-file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                <button type="button" id="remove-category-excel" class="ml-auto text-red-500 hover:text-red-700">
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
                                        <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span class="text-[#213268] font-semibold">telusuri file</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini untuk memilih file</p>
                                    </div>
                                    <input type="file" id="category_excel_file" name="excel_file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="category-excel-error" class="hidden text-red-500 text-sm"></div>

                            <!-- Loading Indicator -->
                            <div id="category-excel-loading" class="hidden text-center py-2">
                                <div class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-sm text-gray-600">Memproses data Excel...</p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Batal
                                </button>
                                <button type="button" id="category-preview-btn" disabled class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Pratinjau Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Import Progress -->
                <div id="import-category-step-2" class="hidden">
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Preview Header -->
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                <span class="text-sm text-gray-500" id="category-preview-count">0 item ditemukan</span>
                            </div>

                            <!-- Preview Table -->
                            <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="sticky top-0 bg-[#213268] text-white">
                                        <tr>
                                            <th class="p-3 text-left text-xs font-semibold">No</th>
                                            <th class="p-3 text-left text-xs font-semibold">Tipe Aset</th>
                                            <th class="p-3 text-left text-xs font-semibold">Nama Sub Kategori</th>
                                            <th class="p-3 text-left text-xs font-semibold">Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="category-preview-table-body">
                                        <!-- Preview data will be inserted here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Warning/Error Messages -->
                            <div id="category-preview-warnings" class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                <p class="font-medium mb-2">Peringatan:</p>
                                <ul class="list-disc pl-5" id="category-warning-list">
                                    <!-- Warning messages will be inserted here -->
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button type="button" id="category-back-to-upload-btn" class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Kembali
                                </button>
                                <form action="{{ route('categories.import') }}" method="POST" id="category-import-form" class="w-2/3" data-no-loading enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="excel_data" id="category_excel_data">
                                    <button type="submit" id="category-import-btn" class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                        Impor Data
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Import Result -->
                <div id="import-category-step-3" class="hidden">
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg font-semibold text-[#213268]">Impor Berhasil!</p>
                                <p class="mt-2 text-sm text-gray-600">Sub kategori Anda telah berhasil diimpor.</p>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="close-modal px-6 py-2 bg-[#213268] text-white rounded-lg hover:bg-[#152451] transition-colors duration-200">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add JavaScript initialization here for permission awareness
        @if(!hasPermission('asset-subcategory:create'))
        // Disable related elements if user doesn't have permission
        const addButtons = document.querySelectorAll('#addSubCategoryBtn');
        addButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-subcategory:import'))
        // Disable related elements if user doesn't have permission
        const importButtons = document.querySelectorAll('#importCategoryBtn, #preview-btn');
        importButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-subcategory:edit'))
        // Disable edit functionality if user doesn't have permission
        const editButtons = document.querySelectorAll('.edit-subcategory-btn');
        editButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        @if(!hasPermission('asset-subcategory:delete'))
        // Disable delete functionality if user doesn't have permission
        const deleteButtons = document.querySelectorAll('.delete-subcategory-btn');
        deleteButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        // Initialize all modal references - regardless of permissions
        const addSubCategoryBtn = document.getElementById('addSubCategoryBtn');
        const addSubCategoryModal = document.getElementById('addSubCategoryModal');
        const editSubCategoryModal = document.getElementById('editSubCategoryModal');
        const deleteSubCategoryModal = document.getElementById('deleteSubCategoryModal');
        const importCategoryModal = document.getElementById('importCategoryModal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Prevent multiple form submissions
        const createSubCategoryForm = document.getElementById('createSubCategoryForm');
        const editSubCategoryForm = document.getElementById('editSubCategoryForm');
        const deleteSubCategoryForm = document.getElementById('deleteSubCategoryForm');

        // Helper function to prevent multiple submissions
        function preventMultipleSubmits(form, buttonSelector) {
            if (!form) return;

            form.addEventListener('submit', function(e) {
                // Only proceed if validation passes
                if (this.checkValidity()) {
                    // Find the submit button
                    const submitBtn = this.querySelector(buttonSelector);
                    if (submitBtn && !submitBtn.disabled) {
                        // Save original button text
                        const originalText = submitBtn.innerHTML;

                        // Disable the button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;

                        // Re-enable button after 10 seconds as a failsafe
                        setTimeout(() => {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                }
            });
        }

        // Apply to all forms
        preventMultipleSubmits(createSubCategoryForm, 'button[type="submit"]');
        preventMultipleSubmits(editSubCategoryForm, 'button[type="submit"]');
        preventMultipleSubmits(deleteSubCategoryForm, 'button[type="submit"]');

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = url.toString();
        }

        // Modal handling functions - define before use
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

        // Add Sub Category Modal
        if (addSubCategoryBtn) {
            addSubCategoryBtn.addEventListener('click', () => {
                openModal(addSubCategoryModal, addSubCategoryModal.querySelector('[id$="ModalContent"]'));
            });
        }

        // Import Category Modal
        const importCategoryBtn = document.getElementById('importCategoryBtn');
        if (importCategoryBtn) {
            importCategoryBtn.addEventListener('click', () => {
                openModal(importCategoryModal, importCategoryModal.querySelector('[id$="ModalContent"]'));
            });
        }

        // Edit Sub Category Modal
        document.querySelectorAll('.edit-subcategory-btn').forEach(button => {
            button.addEventListener('click', () => {
                const subcategoryId = button.getAttribute('data-subcategory-id');
                const assetType = button.getAttribute('data-asset-type');
                const subcategoryName = button.getAttribute('data-subcategory-name');
                const description = button.getAttribute('data-description');

                // Update form action with the correct route and log it
                const formAction = "{{ url('categories/update') }}/" + subcategoryId;
                const editForm = document.getElementById('editSubCategoryForm');
                if (editForm) {
                    editForm.action = formAction;
                    console.log('Edit form action set to:', formAction);
                }

                // Set form values
                const idField = document.getElementById('editSubCategoryId');
                const typeField = document.getElementById('editAssetType');
                const nameField = document.getElementById('editSubCategoryName');
                const descField = document.getElementById('editDescription');

                if (idField) idField.value = subcategoryId;
                if (typeField) typeField.value = assetType;
                if (nameField) nameField.value = subcategoryName;
                if (descField) descField.value = description || '';

                if (editSubCategoryModal) {
                    const modalContent = editSubCategoryModal.querySelector('[id$="ModalContent"]');
                    if (modalContent) {
                        openModal(editSubCategoryModal, modalContent);
                    }
                }
            });
        });

        // Delete Sub Category Modal
        document.querySelectorAll('.delete-subcategory-btn').forEach(button => {
            button.addEventListener('click', () => {
                const subcategoryId = button.getAttribute('data-subcategory-id');

                // Update form action with the correct route and log it
                const formAction = "{{ url('categories/delete') }}/" + subcategoryId;
                document.getElementById('deleteSubCategoryForm').action = formAction;
                console.log('Delete form action set to:', formAction);

                // Set subcategory ID in hidden input
                document.getElementById('deleteSubCategoryId').value = subcategoryId;

                openModal(deleteSubCategoryModal, deleteSubCategoryModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Function to clear form inputs and error states when a modal is closed
        function clearModalForms(modal) {
            if (!modal) return;

            // Get forms in the modal
            const forms = modal.querySelectorAll('form');

            forms.forEach(form => {
                // Reset the form
                form.reset();

                // Clear validation styling and error messages
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                    const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            });

            // Additional cleanup for specific modals
            if (modal.id === 'importCategoryModal') {
                // Reset file upload
                const fileInput = modal.querySelector('#category_excel_file');
                if (fileInput) fileInput.value = '';

                const fileNameContainer = modal.querySelector('#category-excel-file-name');
                if (fileNameContainer) fileNameContainer.classList.add('hidden');

                const previewBtn = modal.querySelector('#category-preview-btn');
                if (previewBtn) previewBtn.disabled = true;

                const errorDiv = modal.querySelector('#category-excel-error');
                if (errorDiv) errorDiv.classList.add('hidden');

                // Reset to step 1 if on any other step
                document.getElementById('import-category-step-1')?.classList.remove('hidden');
                document.getElementById('import-category-step-2')?.classList.add('hidden');
                document.getElementById('import-category-step-3')?.classList.add('hidden');
            }
        }

        // Close Modal Handlers
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
                clearModalForms(modal);
            });
        });

        // Close on outside click
        [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal, importCategoryModal].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    // Check if the click is directly on the modal's overlay area
                    if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                        e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                        const content = this.querySelector('[id$="ModalContent"]');
                        closeModal(this, content);
                        clearModalForms(this);
                    }
                });
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal, importCategoryModal].forEach(modal => {
                    if (modal && !modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                        clearModalForms(modal);
                    }
                });
            }
        });

        // Form validation for Add Sub Category
        if (createSubCategoryForm) {
            createSubCategoryForm.addEventListener('submit', function(event) {
                const assetTypeInput = document.getElementById('add_asset_type');
                const subcategoryNameInput = document.getElementById('add_subcategory_name');
                const descriptionInput = document.getElementById('add_description');

                const isAssetTypeValid = validateField(assetTypeInput);
                const isSubcategoryNameValid = validateField(subcategoryNameInput);

                // Ensure description is never NULL
                if (descriptionInput && descriptionInput.value === null) {
                    descriptionInput.value = '';
                }

                if (!isAssetTypeValid || !isSubcategoryNameValid) {
                    event.preventDefault();
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                }
            });
        }

        // Form validation for Edit Sub Category
        if (editSubCategoryForm) {
            editSubCategoryForm.addEventListener('submit', function(event) {
                const assetTypeInput = document.getElementById('editAssetType');
                const subcategoryNameInput = document.getElementById('editSubCategoryName');
                const descriptionInput = document.getElementById('editDescription');

                const isAssetTypeValid = validateField(assetTypeInput);
                const isSubcategoryNameValid = validateField(subcategoryNameInput);

                // Ensure description is never NULL
                if (descriptionInput && descriptionInput.value === null) {
                    descriptionInput.value = '';
                }

                if (!isAssetTypeValid || !isSubcategoryNameValid) {
                    event.preventDefault();
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                }
            });
        }

        // Function to validate field and show error styling
        function validateField(field) {
            if (!field) return true; // Skip validation if element doesn't exist

            let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

            if (field.tagName.toLowerCase() === 'select') {
                if (!field.value) {
                    field.classList.add('border-red-500');
                    if (errorElement) errorElement.classList.remove('hidden');
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                    return true;
                }
            } else {
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
        }

        // Add input event listeners to clear error styling when typing
        const add_asset_type = document.getElementById('add_asset_type');
        if (add_asset_type) {
            add_asset_type.addEventListener('change', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        const add_subcategory_name = document.getElementById('add_subcategory_name');
        if (add_subcategory_name) {
            add_subcategory_name.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        const editAssetType = document.getElementById('editAssetType');
        if (editAssetType) {
            editAssetType.addEventListener('change', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        const editSubCategoryName = document.getElementById('editSubCategoryName');
        if (editSubCategoryName) {
            editSubCategoryName.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const assetTypeFilter = document.getElementById('assetTypeFilter');
        const sortOrder = document.getElementById('sortOrder');

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const typeValue = assetTypeFilter?.value || '';
            const sortValue = sortOrder?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'asset_type', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (typeValue) url.searchParams.set('asset_type', typeValue);
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
        assetTypeFilter?.addEventListener('change', applyFilters);
        sortOrder?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (assetTypeFilter) {
            const typeValue = urlParams.get('asset_type');
            if (typeValue) {
                assetTypeFilter.value = typeValue;
            }
        }
        if (sortOrder) {
            const sortValue = urlParams.get('sort');
            if (sortValue) {
                sortOrder.value = sortValue;
            }
        }

        // Update pagination functions to preserve filters
        window.changePage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        };

        // Function to show toast notifications
        function showToast(message, type = 'success') {
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

                /* Styling for error messages with HTML content */
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

        // ===== IMPORT CATEGORY FUNCTIONALITY =====
        // Utility function to debounce
        function debounce(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        // File input handling for categories
        const categoryExcelFile = document.getElementById('category_excel_file');
        const categoryFileNameContainer = document.getElementById('category-excel-file-name');
        const categoryFileNameText = document.getElementById('category-file-name-text');
        const removeCategoryExcel = document.getElementById('remove-category-excel');
        const categoryPreviewBtn = document.getElementById('category-preview-btn');
        const categoryExcelError = document.getElementById('category-excel-error');
        const categoryExcelLoading = document.getElementById('category-excel-loading');

        if (categoryExcelFile) {
            categoryExcelFile.addEventListener('change', function(e) {
                if (categoryExcelError) categoryExcelError.classList.add('hidden');

                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileExt = file.name.split('.').pop().toLowerCase();

                    if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                        if (categoryExcelError) {
                            categoryExcelError.textContent = 'Tipe file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                            categoryExcelError.classList.remove('hidden');
                        }
                        this.value = '';
                        if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                        if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                        return;
                    }

                    if (categoryFileNameText) categoryFileNameText.textContent = file.name;
                    if (categoryFileNameContainer) categoryFileNameContainer.classList.remove('hidden');
                    if (categoryPreviewBtn) categoryPreviewBtn.disabled = false;
                } else {
                    if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                    if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                }
            });
        }

        if (removeCategoryExcel) {
            removeCategoryExcel.addEventListener('click', function() {
                if (categoryExcelFile) categoryExcelFile.value = '';
                if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                if (categoryExcelError) categoryExcelError.classList.add('hidden');
            });
        }

        // Preview button for categories
        if (categoryPreviewBtn) {
            categoryPreviewBtn.addEventListener('click', function() {
                if (!categoryExcelFile || !categoryExcelFile.files || !categoryExcelFile.files[0]) {
                    if (categoryExcelError) {
                        categoryExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                        categoryExcelError.classList.remove('hidden');
                    }
                    return;
                }

                const file = categoryExcelFile.files[0];

                if (categoryExcelLoading) categoryExcelLoading.classList.remove('hidden');
                if (categoryExcelError) categoryExcelError.classList.add('hidden');

                const reader = new FileReader();

                reader.onload = function(e) {
                    try {
                        // Use XLSX.js to parse Excel data
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });

                        // Get first sheet
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                        // Convert to JSON
                        const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                        // Process data
                        if (rows.length < 2) {
                            throw new Error('File tidak memiliki data atau header yang hilang.');
                        }

                        // Process the Excel data for categories
                        processCategoryExcelData(rows);

                        if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');

                        // Show step 2
                        document.getElementById('import-category-step-1').classList.add('hidden');
                        document.getElementById('import-category-step-2').classList.remove('hidden');
                    } catch (error) {
                        console.error('Excel parsing error:', error);
                        if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');
                        if (categoryExcelError) {
                            categoryExcelError.textContent = 'Gagal memproses file: ' + error.message;
                            categoryExcelError.classList.remove('hidden');
                        }
                    }
                };

                reader.onerror = function() {
                    console.error('FileReader error:', reader.error);
                    if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');
                    if (categoryExcelError) {
                        categoryExcelError.textContent = 'Gagal membaca file. Silakan coba file lainnya.';
                        categoryExcelError.classList.remove('hidden');
                    }
                };

                reader.readAsArrayBuffer(file);
            });
        }

        // Back button for category import
        const categoryBackBtn = document.getElementById('category-back-to-upload-btn');
        if (categoryBackBtn) {
            categoryBackBtn.addEventListener('click', function() {
                document.getElementById('import-category-step-2').classList.add('hidden');
                document.getElementById('import-category-step-1').classList.remove('hidden');
            });
        }

        // Function to process Excel data for categories
        function processCategoryExcelData(data) {
            // Get headers (first row)
            const headers = data[0];
            // Remove empty rows
            const rows = data.slice(1).filter(row => row.length > 0 && row.some(cell => cell !== null && cell !== ''));

            // Map headers to normalized names
            const headerMap = {};
            headers.forEach((header, index) => {
                if (header) {
                    const normalizedHeader = String(header).toLowerCase().trim()
                        .replace(/\s+/g, '_')
                        .replace(/[^a-z0-9_]/g, '');
                    headerMap[normalizedHeader] = index;
                }
            });

            // Transform data for preview
            const previewData = [];
            const warnings = [];

            rows.forEach((row, rowIndex) => {
                const item = {};

                // Helper function to get value by possible header names
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

                // Map values to normalized fields
                item.asset_type = getValue(['asset_type', 'asset type', 'assettype', 'type', 'category', 'tipe aset', 'tipe_aset']);
                item.subcategory_name = getValue(['subcategory_name', 'subcategory name', 'sub category name', 'sub_category_name', 'name', 'nama kategori', 'nama_kategori']);
                item.description = getValue(['description', 'desc', 'notes', 'deskripsi']);

                // Normalize asset_type values
                if (item.asset_type) {
                    const typeStr = item.asset_type.toString().toLowerCase().trim();
                    if (typeStr === 'medical' || typeStr === 'med') {
                        item.asset_type = 'medical';
                    } else if (typeStr === 'non medical' || typeStr === 'non-medical' || typeStr === 'nonmedical' || typeStr === 'nmed' || typeStr === 'non_medis') {
                        item.asset_type = 'non_medical';
                    }
                }

                // Validate required fields
                if (!item.asset_type) {
                    warnings.push(`Row ${rowIndex + 2}: Tipe Aset tidak boleh kosong`);
                } else if (item.asset_type !== 'medical' && item.asset_type !== 'non_medical') {
                    warnings.push(`Row ${rowIndex + 2}: Tipe Aset tidak valid. Harus "medical" atau "non_medical"`);
                }

                if (!item.subcategory_name) {
                    warnings.push(`Row ${rowIndex + 2}: Nama Sub Kategori tidak boleh kosong`);
                }

                // Add row index for reference
                item._rowNum = rowIndex + 2; // +2 because we've removed the header row and arrays are 0-indexed

                previewData.push(item);
            });

            // Check for duplicate subcategory names
            const subcategoryNameMap = {};
            previewData.forEach(item => {
                if (item.subcategory_name && item.asset_type) {
                    const key = `${item.asset_type}|${item.subcategory_name.toLowerCase()}`;
                    if (!subcategoryNameMap[key]) {
                        subcategoryNameMap[key] = [];
                    }
                    subcategoryNameMap[key].push(item._rowNum);
                }
            });

            // Add duplicate warnings
            Object.entries(subcategoryNameMap).forEach(([key, rows]) => {
                if (rows.length > 1) {
                    const [assetType, subcategoryName] = key.split('|');
                    warnings.push(`Sub Kategori duplikat "${subcategoryName}" untuk Tipe Aset "${assetType}" ditemukan di baris: ${rows.join(', ')}`);
                }
            });

            // Update hidden field with JSON data for form submission
            document.getElementById('category_excel_data').value = JSON.stringify(previewData);

            // Show preview with warnings
            showCategoryDataPreview(previewData, warnings);
        }

        // Function to show data preview for categories
        function showCategoryDataPreview(data, warnings) {
            const previewTableBody = document.getElementById('category-preview-table-body');
            const previewCount = document.getElementById('category-preview-count');
            const warningsContainer = document.getElementById('category-preview-warnings');
            const warningsList = document.getElementById('category-warning-list');

            if (!previewTableBody || !previewCount) return;

            // Clear previous content
            previewTableBody.innerHTML = '';
            if (warningsList) warningsList.innerHTML = '';
            if (warningsContainer) warningsContainer.classList.add('hidden');

            // Update count
            previewCount.textContent = `${data.length} item ditemukan`;

            // Generate table rows
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                // Add row number
                const indexCell = document.createElement('td');
                indexCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                indexCell.textContent = index + 1;
                row.appendChild(indexCell);

                // Add data cells
                const fields = ['asset_type', 'subcategory_name', 'description'];

                fields.forEach(field => {
                    const cell = document.createElement('td');
                    cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';

                    if (field === 'asset_type' && item[field]) {
                        // Format asset_type nicely
                        cell.textContent = item[field] === 'medical' ? 'Medical' :
                                           item[field] === 'non_medical' ? 'Non Medical' :
                                           item[field];
                    } else {
                        cell.textContent = item[field] || '-';
                    }

                    row.appendChild(cell);
                });

                previewTableBody.appendChild(row);
            });

            // Show warnings if any
            if (warnings && warnings.length > 0 && warningsList && warningsContainer) {
                warnings.forEach(warning => {
                    const li = document.createElement('li');
                    li.textContent = warning;
                    warningsList.appendChild(li);
                });
                warningsContainer.classList.remove('hidden');

                // Disable import button if there are critical warnings
                const importBtn = document.getElementById('category-import-btn');
                const hasCriticalWarnings = warnings.some(warning =>
                    warning.includes('Missing Asset Type') ||
                    warning.includes('Missing Sub Category Name') ||
                    warning.includes('Invalid Asset Type')
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

        // Handle category import form submission with AJAX
        const categoryImportForm = document.getElementById('category-import-form');
        categoryImportForm?.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent traditional form submission

            // Get form data
            const formData = new FormData(this);

            // Add the Excel file to the form data if needed
            const originalFileInput = document.getElementById('category_excel_file');
            if (originalFileInput && originalFileInput.files.length > 0) {
                formData.append('excel_file', originalFileInput.files[0]);
            }

            // Show loading state
            const importBtn = document.getElementById('category-import-btn');
            const originalBtnText = importBtn.innerHTML;
            importBtn.disabled = true;
            importBtn.innerHTML = `
                <div class="flex items-center justify-center">
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    <span>Memproses...</span>
                </div>
            `;

            // Send AJAX request
            fetch('{{ route('categories.import') }}', {
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

                if (data.success === true || (data.status >= 200 && data.status < 300)) {
                    // Success response
                    console.log('Import successful:', data);

                    // Close the modal
                    const modal = document.getElementById('importCategoryModal');
                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                    // Show success notification
                    showToast('Sub kategori berhasil diimpor!', 'success');

                    // Reload the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Error response
                    console.error('Import error:', data);

                    // Show error notification toast
                    console.error('Import error details:', data);

                    let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                    let errorDetails = [];

                    // Add validation errors if present - Based on the actual server response structure in the logs
                    if (data.data && data.data.errors) {
                        console.log('Server returned detailed errors:', data.data.errors);

                        if (Array.isArray(data.data.errors)) {
                            data.data.errors.forEach(error => {
                                if (typeof error === 'string') {
                                    errorDetails.push(error);
                                } else if (error.message) {
                                    errorDetails.push(error.message);
                                } else if (error.subcategory_name && error.reason) {
                                    errorDetails.push(`"${error.subcategory_name}" - ${error.reason}`);
                                } else if (error.row && error.reason) {
                                    errorDetails.push(`Baris ${error.row}: ${error.reason}`);
                                } else if (error.reason) {
                                    errorDetails.push(error.reason);
                                }
                            });
                        }
                    }

                    // Format the error message with details if available
                    if (errorDetails.length > 0) {
                        errorMessage = `${errorMessage}<ul class="mt-2 ml-4 list-disc">`;
                        errorDetails.forEach(detail => {
                            errorMessage += `<li>${detail}</li>`;
                        });
                        errorMessage += '</ul>';
                    }

                    // Create and show the toast notification
                    showToast(errorMessage, 'error');
                }
            })
            .catch(error => {
                // Reset button state
                importBtn.disabled = false;
                importBtn.innerHTML = originalBtnText;

                console.error('Import fetch error:', error);

                // Try to get more detailed error if available
                let errorMessage = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';

                if (error.response) {
                    // The server responded with a status code outside the 2xx range
                    try {
                        // Try to parse the error response
                        error.response.json().then(data => {
                            if (data.message) {
                                errorMessage = data.message;

                                // Add details if available
                                if (data.errors) {
                                    errorMessage += '<ul class="mt-2 ml-4 list-disc">';
                                    if (typeof data.errors === 'object') {
                                        Object.values(data.errors).flat().forEach(err => {
                                            errorMessage += `<li>${err}</li>`;
                                        });
                                    } else if (Array.isArray(data.errors)) {
                                        data.errors.forEach(err => {
                                            errorMessage += `<li>${err}</li>`;
                                        });
                                    }
                                    errorMessage += '</ul>';
                                }

                                showToast(errorMessage, 'error');
                            }
                        }).catch(() => {
                            // If we can't parse the JSON, just show the status text
                            showToast(`Error: ${error.response.statusText || errorMessage}`, 'error');
                        });
                    } catch (e) {
                        // If any error in parsing, use default message
                        showToast(errorMessage, 'error');
                    }
                } else {
                    // Network error or something prevented the request
                    showToast(error.message || errorMessage, 'error');
                }
            });
        });
    });
</script>
@endpush

<!-- Include XLSX.js library -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endsection
