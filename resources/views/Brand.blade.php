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

                    <!-- Brand Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">ID</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Merk</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $brand['brand_id'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $brand['brand_name'] }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('brand:edit'))
                                                    <button
                                                        class="edit-brand-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-brand-id="{{ $brand['brand_id'] }}"
                                                        data-brand-name="{{ $brand['brand_name'] }}">
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
                                                        data-brand-name="{{ $brand['brand_name'] }}">
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
                                    class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($brands_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
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

                                    @for($i = $startPage; $i <= $endPage; $i++)
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

                                <button
                                    class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($brands_pagination['current_page'] ?? 1) >= ($brands_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
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
                                    onchange="changePerPage(this.value)">
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

    @if(hasPermission('brand:create'))
        <!-- Modal Add Brand -->
        <div id="addBrandModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addBrandModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH MERK</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form action="{{ route('brands.store') }}" method="POST" id="addBrandForm" data-no-loading novalidate>
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Brand Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Merk <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="brand_name" id="add_brand_name" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Nama Merk harus diisi
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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

    @if(hasPermission('brand:edit'))
        <!-- Modal Edit Brand -->
        <div id="editBrandModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editBrandModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT MERK</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editBrandForm" method="POST" data-no-loading novalidate>
                                @csrf
                                @method('PUT')
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Merk <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="edit_brand_name" name="brand_name" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Nama Merk harus diisi
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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

    @if(hasPermission('brand:delete'))
        <!-- Delete Brand Modal -->
        <div id="deleteBrandModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteBrandModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS MERK</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus merk: <span
                                            id="delete_brand_name" class="font-bold"></span>? Tindakan ini tidak dapat
                                        dibatalkan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <form id="deleteBrandForm" action="" method="POST" data-no-loading class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="deleteBrandId" name="brand_id">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
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

    @if(hasPermission('brand:import'))
        <!-- Import Brand Modal -->
        <div id="importBrandModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="importBrandModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPORT MERK</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Step 1: File Selection -->
                        <div id="import-brand-step-1" class="block">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Import Instructions -->
                                    <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                        <p class="font-medium text-blue-600 mb-2">Petunjuk Import:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Gunakan format template Excel untuk mengimpor</li>
                                            <li>Kolom yang diperlukan: Nama Merk</li>
                                            <li>Maksimal 100 data per import</li>
                                            <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                        </ul>
                                        <div class="mt-3 flex justify-end">
                                            <a href="{{ asset('docs/ImportBrandTemplate.xlsx') }}" download
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
                                            <div id="brand-excel-file-name" class="mt-2 mb-4 w-full hidden">
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
                                                        <span id="brand-file-name-text"
                                                            class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="remove-brand-excel"
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
                                                <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span
                                                        class="text-[#213268] font-semibold">telusuri file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                                    untuk memilih file</p>
                                            </div>
                                            <input type="file" id="brand_excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Error Message -->
                                    <div id="brand-excel-error" class="hidden text-red-500 text-sm"></div>

                                    <!-- Loading Indicator -->
                                    <div id="brand-excel-loading" class="hidden text-center py-2">
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
                                        <button type="button" id="brand-preview-btn" disabled
                                            class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Pratinjau Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Import Progress -->
                        <div id="import-brand-step-2" class="hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Preview Header -->
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                        <span class="text-sm text-gray-500" id="brand-preview-count">0 item ditemukan</span>
                                    </div>

                                    <!-- Preview Table -->
                                    <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                        <table class="w-full">
                                            <thead class="sticky top-0 bg-[#213268] text-white">
                                                <tr>
                                                    <th class="p-3 text-left text-xs font-semibold">No</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Nama Merk</th>
                                                </tr>
                                            </thead>
                                            <tbody id="brand-preview-table-body">
                                                <!-- Preview data will be inserted here -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Warning/Error Messages -->
                                    <div id="brand-preview-warnings"
                                        class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                        <p class="font-medium mb-2">Peringatan:</p>
                                        <ul class="list-disc pl-5" id="brand-warning-list">
                                            <!-- Warning messages will be inserted here -->
                                        </ul>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <button type="button" id="brand-back-to-upload-btn"
                                            class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                            Kembali
                                        </button>
                                        <form action="{{ route('brands.import') }}" method="POST" id="brand-import-form"
                                            class="w-2/3" data-no-loading enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="excel_data" id="brand_excel_data">
                                            <button type="submit" id="brand-import-btn"
                                                class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                                Import Data
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Import Result -->
                        <div id="import-brand-step-3" class="hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-semibold text-[#213268]">Import Berhasil!</p>
                                        <p class="mt-2 text-sm text-gray-600">Merk Anda telah berhasil diimpor.</p>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button"
                                            class="close-modal px-6 py-2 bg-[#213268] text-white rounded-lg hover:bg-[#152451] transition-colors duration-200">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add JavaScript initialization here for permission awareness

            @if(!hasPermission('brand:create'))
                // Disable related elements if user doesn't have permission
                const addButtons = document.querySelectorAll('#addBrandBtn');
                addButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('brand:import'))
                    // Disable import functionality if user doesn't have permission
                    const importButtons = document.querySelectorAll('#importBrandBtn');
                    importButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('brand:edit'))
                    // Disable edit functionality if user doesn't have permission
                    const editButtons = document.querySelectorAll('.edit-brand-btn');
                    editButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('brand:delete'))
                    // Disable delete functionality if user doesn't have permission
                    const deleteButtons = document.querySelectorAll('.delete-brand-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

            // Define the showToast function first
            window.showToast = function(message, type = 'success') {
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
                <div class="flex-1">
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
                    closeBtn.onclick = function () {
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

                // Auto-remove notification after 5 seconds for success, 10 seconds for error
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, type === 'success' ? 5000 : 10000);
            };

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

            // Now we can call the function with session data
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

                @if(session('error'))
                    showToast({!! json_encode(session('error')) !!}, 'error');
                @endif

                // Search and filter functionality
                const searchInput = document.getElementById('searchInput');
            const sortOrder = document.getElementById('sortOrder');

            // Function to handle search and filtering
            function applyFilters() {
                const searchValue = searchInput?.value.trim() || '';
                const sortValue = sortOrder?.value || '';

                // Create URL with filter parameters
                const url = new URL(window.location.href);

                // Clear existing parameters we're going to set
                ['search', 'sort', 'page'].forEach(param => {
                    url.searchParams.delete(param);
                });

                // Add new parameters if they have values
                if (searchValue) url.searchParams.set('search', searchValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                // Reset to page 1 when filters change
                url.searchParams.set('page', 1);

                // Navigate to the new URL
                window.location.href = url.toString();
            }

            // Add event listeners with debounce for search
            let searchTimeout;
            searchInput?.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            // Add event listeners for select filters
            sortOrder?.addEventListener('change', applyFilters);

            // Set initial values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput) searchInput.value = urlParams.get('search') || '';
            if (sortOrder) {
                const sortValue = urlParams.get('sort');
                if (sortValue) {
                    sortOrder.value = sortValue;
                }
            }

            // Page navigation functions
            window.changePage = function (page) {
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            }

            window.changePerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', limit);
                url.searchParams.set('page', 1); // Reset to first page when changing limit
                window.location.href = url.toString();
            }

            // Modal functionality
            function openModal(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }

            function closeModal(modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');

                    // Reset forms when closing modals
                    if (modal.id === 'addBrandModal') {
                        resetForm('addBrandForm');
                    } else if (modal.id === 'editBrandModal') {
                        resetForm('editBrandForm');
                    } else if (modal.id === 'importBrandModal') {
                        // Reset the import form
                        if (document.getElementById('brand_excel_file')) {
                            document.getElementById('brand_excel_file').value = '';
                        }
                        if (document.getElementById('brand-excel-file-name')) {
                            document.getElementById('brand-excel-file-name').classList.add('hidden');
                        }
                        if (document.getElementById('brand-excel-error')) {
                            document.getElementById('brand-excel-error').classList.add('hidden');
                        }
                        if (document.getElementById('brand-preview-btn')) {
                            document.getElementById('brand-preview-btn').disabled = true;
                        }

                        // Show step 1, hide step 2 and 3
                        if (document.getElementById('import-brand-step-1')) {
                            document.getElementById('import-brand-step-1').classList.remove('hidden');
                        }
                        if (document.getElementById('import-brand-step-2')) {
                            document.getElementById('import-brand-step-2').classList.add('hidden');
                        }
                        if (document.getElementById('import-brand-step-3')) {
                            document.getElementById('import-brand-step-3').classList.add('hidden');
                        }
                    }
                }, 300);
            }

            // Function to reset a form and clear validation errors
            function resetForm(formId) {
                const form = document.getElementById(formId);
                if (!form) return;

                // Reset the form fields
                form.reset();

                // Clear validation errors
                form.querySelectorAll('input, select, textarea').forEach(field => {
                    field.classList.remove('border-red-500');
                    const errorElement = field.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                        errorElement.classList.add('hidden');
                    }
                });
            }

            // Handle Edit Brand button click
            document.querySelectorAll('.edit-brand-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const brandId = this.getAttribute('data-brand-id');
                    const brandName = this.getAttribute('data-brand-name');

                    document.getElementById('edit_brand_name').value = brandName;
                    document.getElementById('editBrandForm').action = `/brands/${brandId}`;
                    console.log('Edit form action set to:', `/brands/${brandId}`);

                    const modal = document.getElementById('editBrandModal');
                    const content = document.getElementById('editBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            });

            // Handle Delete Brand button click
            document.querySelectorAll('.delete-brand-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const brandId = this.getAttribute('data-brand-id');
                    const brandName = this.getAttribute('data-brand-name');

                    // Set brand name in confirmation message
                    document.getElementById('delete_brand_name').textContent = brandName;

                    // Update form action with the correct route and log it
                    const formAction = "{{ url('brands') }}/" + brandId;
                    document.getElementById('deleteBrandForm').action = formAction;
                    console.log('Delete form action set to:', formAction);

                    // Set brand ID in hidden input
                    document.getElementById('deleteBrandId').value = brandId;

                    const modal = document.getElementById('deleteBrandModal');
                    const content = document.getElementById('deleteBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            });

            // Handle Add Brand button click
            const addBrandBtn = document.getElementById('addBrandBtn');
            if (addBrandBtn) {
                addBrandBtn.addEventListener('click', function () {
                    const modal = document.getElementById('addBrandModal');
                    const content = document.getElementById('addBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            }

            // Import Brand button click
            const importBrandBtn = document.getElementById('importBrandBtn');
            if (importBrandBtn) {
                importBrandBtn.addEventListener('click', function () {
                    const modal = document.getElementById('importBrandModal');
                    const content = document.getElementById('importBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            }

            // Close buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            // Close modal when clicking on background or overlay
            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                modal.addEventListener('click', function (e) {
                    // Check if the click is directly on the modal's overlay area
                    if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                        e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                        const content = this.querySelector('[id$="ModalContent"]');
                        closeModal(this, content);
                    }
                });
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        }
                    });
                }
            });

            // ===== IMPORT BRAND FUNCTIONALITY =====
            // File input handling for brands
            const brandExcelFile = document.getElementById('brand_excel_file');
            const brandFileNameContainer = document.getElementById('brand-excel-file-name');
            const brandFileNameText = document.getElementById('brand-file-name-text');
            const removeBrandExcel = document.getElementById('remove-brand-excel');
            const brandPreviewBtn = document.getElementById('brand-preview-btn');
            const brandExcelError = document.getElementById('brand-excel-error');
            const brandExcelLoading = document.getElementById('brand-excel-loading');

            if (brandExcelFile) {
                brandExcelFile.addEventListener('change', function (e) {
                    if (brandExcelError) brandExcelError.classList.add('hidden');

                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();

                        if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                            if (brandExcelError) {
                                brandExcelError.textContent = 'Format file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau file CSV.';
                                brandExcelError.classList.remove('hidden');
                            }
                            this.value = '';
                            if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                            if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                            return;
                        }

                        if (brandFileNameText) brandFileNameText.textContent = file.name;
                        if (brandFileNameContainer) brandFileNameContainer.classList.remove('hidden');
                        if (brandPreviewBtn) brandPreviewBtn.disabled = false;
                    } else {
                        if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                        if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                    }
                });
            }

            if (removeBrandExcel) {
                removeBrandExcel.addEventListener('click', function () {
                    if (brandExcelFile) brandExcelFile.value = '';
                    if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                    if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                    if (brandExcelError) brandExcelError.classList.add('hidden');
                });
            }

            // Preview button for brands
            if (brandPreviewBtn) {
                brandPreviewBtn.addEventListener('click', function () {
                    if (!brandExcelFile || !brandExcelFile.files || !brandExcelFile.files[0]) {
                        if (brandExcelError) {
                            brandExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                            brandExcelError.classList.remove('hidden');
                        }
                        return;
                    }

                    const file = brandExcelFile.files[0];

                    if (brandExcelLoading) brandExcelLoading.classList.remove('hidden');
                    if (brandExcelError) brandExcelError.classList.add('hidden');

                    const reader = new FileReader();

                    reader.onload = function (e) {
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
                                throw new Error('File tidak berisi data atau header tidak ditemukan.');
                            }

                            // Process the Excel data for brands
                            processBrandExcelData(rows);

                            if (brandExcelLoading) brandExcelLoading.classList.add('hidden');

                            // Show step 2
                            document.getElementById('import-brand-step-1').classList.add('hidden');
                            document.getElementById('import-brand-step-2').classList.remove('hidden');
                        } catch (error) {
                            console.error('Excel parsing error:', error);
                            if (brandExcelLoading) brandExcelLoading.classList.add('hidden');
                            if (brandExcelError) {
                                brandExcelError.textContent = 'Error memproses file: ' + error.message;
                                brandExcelError.classList.remove('hidden');
                            }
                        }
                    };

                    reader.onerror = function () {
                        console.error('FileReader error:', reader.error);
                        if (brandExcelLoading) brandExcelLoading.classList.add('hidden');
                        if (brandExcelError) {
                            brandExcelError.textContent = 'Error membaca file. Silakan coba file lain.';
                            brandExcelError.classList.remove('hidden');
                        }
                    };

                    reader.readAsArrayBuffer(file);
                });
            }

            // Back button for brand import
            const brandBackBtn = document.getElementById('brand-back-to-upload-btn');
            if (brandBackBtn) {
                brandBackBtn.addEventListener('click', function () {
                    document.getElementById('import-brand-step-2').classList.add('hidden');
                    document.getElementById('import-brand-step-1').classList.remove('hidden');
                });
            }

            // Function to process Excel data for brands
            function processBrandExcelData(data) {
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
                    item.brand_name = getValue(['brand_name', 'brand name', 'name', 'nama brand', 'nama_brand', 'nama merk', 'merk']);

                    // Validate required fields
                    if (!item.brand_name) {
                        warnings.push(`Baris ${rowIndex + 2}: Nama Merk tidak ditemukan`);
                    }

                    // Add row index for reference
                    item._rowNum = rowIndex + 2; // +2 because we've removed the header row and arrays are 0-indexed

                    previewData.push(item);
                });

                // Check for duplicate brand names
                const brandNameMap = {};
                previewData.forEach(item => {
                    if (item.brand_name) {
                        const key = item.brand_name.toLowerCase();
                        if (!brandNameMap[key]) {
                            brandNameMap[key] = [];
                        }
                        brandNameMap[key].push(item._rowNum);
                    }
                });

                // Add duplicate warnings
                Object.entries(brandNameMap).forEach(([key, rows]) => {
                    if (rows.length > 1) {
                        warnings.push(`Nama Merk duplikat "${key}" ditemukan di baris: ${rows.join(', ')}`);
                    }
                });

                // Update hidden field with JSON data for form submission
                document.getElementById('brand_excel_data').value = JSON.stringify(previewData);

                // Show preview with warnings
                showBrandDataPreview(previewData, warnings);
            }

            // Function to show data preview for brands
            function showBrandDataPreview(data, warnings) {
                const previewTableBody = document.getElementById('brand-preview-table-body');
                const previewCount = document.getElementById('brand-preview-count');
                const warningsContainer = document.getElementById('brand-preview-warnings');
                const warningsList = document.getElementById('brand-warning-list');

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
                    const brandNameCell = document.createElement('td');
                    brandNameCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                    brandNameCell.textContent = item.brand_name || '-';
                    row.appendChild(brandNameCell);

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
                    const importBtn = document.getElementById('brand-import-btn');
                    const hasCriticalWarnings = warnings.some(warning =>
                        warning.includes('Nama Merk tidak ditemukan')
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

            // Handle brand import form submission with AJAX
            const brandImportForm = document.getElementById('brand-import-form');
            brandImportForm?.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent traditional form submission

                // Get form data
                const formData = new FormData(this);

                // Add the Excel file to the form data if needed
                const originalFileInput = document.getElementById('brand_excel_file');
                if (originalFileInput && originalFileInput.files.length > 0) {
                    formData.append('excel_file', originalFileInput.files[0]);
                }

                // Show loading state
                const importBtn = document.getElementById('brand-import-btn');
                const originalBtnText = importBtn.innerHTML;
                importBtn.disabled = true;
                importBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            <span>Mengimpor...</span>
                        </div>
                    `;

                // Send AJAX request
                fetch('{{ route('brands.import') }}', {
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
                            throw new Error('Format respons tidak valid');
                        }
                    })
                    .then(data => {
                        // Reset button state
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalBtnText;

                        if (data.success === true || (data.status >= 200 && data.status < 300)) {
                            // Close the modal
                            const modal = document.getElementById('importBrandModal');
                            closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                            // Show success notification
                            showToast('Merk berhasil diimpor!', 'success');

                            // Reload the page to show updated data
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Error response
                            console.error('Import error:', data);

                            // Try to get more detailed error if available
                            let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                            let errorDetails = [];

                            // Add validation errors if present
                            if (data.data && data.data.errors) {
                                console.log('Server returned detailed errors:', data.data.errors);

                                if (Array.isArray(data.data.errors)) {
                                    data.data.errors.forEach(error => {
                                        if (typeof error === 'string') {
                                            errorDetails.push(error);
                                        } else if (error.message) {
                                            errorDetails.push(error.message);
                                        } else if (error.brand_name && error.reason) {
                                            errorDetails.push(`"${error.brand_name}" - ${error.reason}`);
                                        } else if (error.row && error.reason) {
                                            errorDetails.push(`${error.reason}`);
                                        } else if (error.reason) {
                                            errorDetails.push(error.reason);
                                        }
                                    });
                                }
                            }

                            // Create HTML content for the error message
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
                        // Reset button state
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalBtnText;

                        console.error('Import fetch error:', error);
                        showToast('Terjadi kesalahan saat mengimpor data: ' + error.message, 'error');
                    });
            });

            // Form validation for Add Brand
            const addBrandForm = document.getElementById('addBrandForm');
            if (addBrandForm) {
                addBrandForm.addEventListener('submit', function (event) {
                    const brandNameInput = document.getElementById('add_brand_name');
                    const isValid = validateField(brandNameInput);

                    if (!isValid) {
                        event.preventDefault();
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                    } else {
                        // Prevent multiple submissions by disabling the button
                        const submitBtn = this.querySelector('button[type="submit"]');
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

            // Form validation for Edit Brand
            const editBrandForm = document.getElementById('editBrandForm');
            if (editBrandForm) {
                editBrandForm.addEventListener('submit', function (event) {
                    const brandNameInput = document.getElementById('edit_brand_name');
                    const isValid = validateField(brandNameInput);

                    if (!isValid) {
                        event.preventDefault();
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                    } else {
                        // Prevent multiple submissions by disabling the button
                        const submitBtn = this.querySelector('button[type="submit"]');
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

            // Prevent multiple submissions for Delete Brand form
            const deleteBrandForm = document.getElementById('deleteBrandForm');
            if (deleteBrandForm) {
                deleteBrandForm.addEventListener('submit', function (event) {
                    // Prevent multiple submissions by disabling the button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        // Save original button text
                        const originalText = submitBtn.innerHTML;

                        // Disable the button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Menghapus...</span>
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
                });
            }

            // Function to validate field and show error styling
            function validateField(field) {
                if (!field) return false;

                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    if (field.nextElementSibling) {
                        field.nextElementSibling.classList.remove('hidden');
                    }
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (field.nextElementSibling) {
                        field.nextElementSibling.classList.add('hidden');
                    }
                    return true;
                }
            }

            // Add input event listeners to clear error styling when typing
            const addBrandNameInput = document.getElementById('add_brand_name');
            if (addBrandNameInput) {
                addBrandNameInput.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    if (this.nextElementSibling) {
                        this.nextElementSibling.classList.add('hidden');
                    }
                });
            }

            const editBrandNameInput = document.getElementById('edit_brand_name');
            if (editBrandNameInput) {
                editBrandNameInput.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    if (this.nextElementSibling) {
                        this.nextElementSibling.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    <!-- Include XLSX.js library -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endsection