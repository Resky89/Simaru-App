@extends('Layout.app')

@section('title', 'Vendor')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Vendor Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h2 class="text-2xl font-semibold text-[#213268]">VENDOR</h2>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('vendor:import'))
                                <button id="importVendorBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            @if(hasPermission('vendor:create'))
                                <button id="addVendorBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Vendor</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama vendor..."
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

                    <!-- Vendor Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Vendor</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kontak Person</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">No. Telepon</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Email</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendors as $vendor)
                                    <tr>

                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['vendor_name'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['contact_person'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['phone_number'] }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['email'] }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('vendor:edit'))
                                                    <button
                                                        class="edit-vendor-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-vendor-id="{{ $vendor['vendor_id'] }}"
                                                        data-vendor-name="{{ $vendor['vendor_name'] }}"
                                                        data-contact-person="{{ $vendor['contact_person'] }}"
                                                        data-phone-number="{{ $vendor['phone_number'] }}"
                                                        data-email="{{ $vendor['email'] }}"
                                                        data-website="{{ $vendor['website'] ?? '' }}"
                                                        data-address="{{ $vendor['address'] ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if(hasPermission('vendor:delete'))
                                                    <button
                                                        class="delete-vendor-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-vendor-id="{{ $vendor['vendor_id'] }}">
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
                                        <td colspan="6" class="p-3 text-center text-gray-500">Tidak ada vendor ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
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
                                    Menampilkan {{ $pagination['from'] }} sampai {{ $pagination['to'] }} dari
                                    {{ $pagination['total'] }} data
                                @else
                                    Menampilkan 1 sampai {{ count($vendors) }} dari {{ count($vendors) }} data
                                @endif
                            </span>
                            <select id="perPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeVendorPerPage(this.value)">
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


    <!-- Modal Add Vendor -->
    @if(hasPermission('vendor:create'))
        <div id="addVendorModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="vendorModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">TAMBAH VENDOR</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form with JavaScript for debugging -->
                        <div class="p-6">
                            <form id="createVendorForm" action="{{ route('vendor.store') }}" method="POST" data-no-loading
                                novalidate>
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Vendor Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Vendor <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="vendor_name" id="add_vendor_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Vendor harus diisi
                                        </div>
                                    </div>

                                    <!-- Contact Person Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Kontak
                                        </label>
                                        <input type="text" name="contact_person" id="add_contact_person"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Phone Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nomor Telepon
                                        </label>
                                        <input type="tel" name="phone_number" id="add_phone_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Email Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Email</label>
                                        <input type="email" name="email" id="add_email"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Website Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Website</label>
                                        <input type="url" name="website" id="add_website"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Address Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Alamat</label>
                                        <textarea name="address" id="add_address" rows="3"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitVendorBtn"
                                        class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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

    <!-- Modal Edit Vendor -->
    @if(hasPermission('vendor:edit'))
        <div id="editVendorModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editVendorModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT VENDOR</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editVendorForm" action="" method="POST" data-no-loading novalidate>
                                @csrf
                                @method('PUT')
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Vendor ID Input (Hidden) -->
                                    <input type="hidden" id="editVendorId" name="vendor_id">

                                    <!-- Vendor Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Vendor <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="editVendorName" name="vendor_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini" required>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Vendor harus diisi
                                        </div>
                                    </div>

                                    <!-- Contact Person Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Kontak
                                        </label>
                                        <input type="text" id="editContactPerson" name="contact_person"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Phone Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nomor Telepon
                                        </label>
                                        <input type="text" id="editPhoneNumber" name="phone_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                    </div>

                                    <!-- Email Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Email</label>
                                        <input type="email" id="editEmail" name="email"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Format Email tidak valid
                                        </div>
                                    </div>

                                    <!-- Website Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Website</label>
                                        <input type="text" id="editWebsite" name="website"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik di sini">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Format Website tidak valid
                                        </div>
                                    </div>

                                    <!-- Address Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Alamat</label>
                                        <textarea id="editAddress" name="address"
                                            class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 min-h-[100px]"
                                            placeholder="Ketik di sini"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
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

    <!-- Modal Delete Vendor -->
    @if(hasPermission('vendor:delete'))
        <div id="deleteVendorModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteVendorModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">HAPUS VENDOR</h2>
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
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus vendor ini?
                                        Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <form id="deleteVendorForm" action="" method="POST" class="w-1/2" data-no-loading>
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="deleteVendorId" name="vendor_id">
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

    <!-- Import Vendor Modal -->
    @if(hasPermission('vendor:import'))
        <div id="importVendorModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="importVendorModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPOR VENDOR</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Step 1: File Selection -->
                        <div id="import-vendor-step-1" class="block">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Import Instructions -->
                                    <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                        <p class="font-medium text-blue-600 mb-2">Petunjuk Impor:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Gunakan format template Excel untuk mengimpor</li>
                                            <li>Kolom yang diperlukan: Nama Vendor, Kontak Person, Nomor Telepon</li>
                                            <li>Maksimal 100 data per impor</li>
                                            <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                        </ul>
                                        <div class="mt-3 flex justify-end">
                                            <button type="button"
                                                onclick="window.location.href='{{ asset('docs/ImportVendorTemplate.xlsx') }}'"
                                                download
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                    </path>
                                                </svg>
                                                Unduh Template
                                            </button>
                                        </div>
                                    </div>

                                    <!-- File Upload -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#213268]">File Excel</label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- File preview -->
                                            <div id="vendor-excel-file-name" class="mt-2 mb-4 w-full hidden">
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
                                                        <span id="vendor-file-name-text"
                                                            class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="remove-vendor-excel"
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
                                            <input type="file" id="vendor_excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Error Message -->
                                    <div id="vendor-excel-error" class="hidden text-red-500 text-sm"></div>

                                    <!-- Loading Indicator -->
                                    <div id="vendor-excel-loading" class="hidden text-center py-2">
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
                                        <button type="button" id="vendor-preview-btn" disabled
                                            class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Pratinjau Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Import Progress -->
                        <div id="import-vendor-step-2" class="hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Preview Header -->
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                        <span class="text-sm text-gray-500" id="vendor-preview-count">0 item ditemukan</span>
                                    </div>

                                    <!-- Preview Table -->
                                    <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                        <table class="w-full">
                                            <thead class="sticky top-0 bg-[#213268] text-white">
                                                <tr>
                                                    <th class="p-3 text-left text-xs font-semibold">No</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Nama Vendor</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Kontak Person</th>
                                                    <th class="p-3 text-left text-xs font-semibold">No. Telepon</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Email</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Website</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Alamat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vendor-preview-table-body">
                                                <!-- Preview data will be inserted here -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Warning/Error Messages -->
                                    <div id="vendor-preview-warnings"
                                        class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                        <p class="font-medium mb-2">Peringatan:</p>
                                        <ul class="list-disc pl-5" id="vendor-warning-list">
                                            <!-- Warning messages will be inserted here -->
                                        </ul>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <button type="button" id="vendor-back-to-upload-btn"
                                            class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                            Kembali
                                        </button>
                                        <form action="{{ route('vendor.import') }}" method="POST" id="vendor-import-form"
                                            class="w-2/3" data-no-loading enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="excel_data" id="vendor_excel_data">
                                            <button type="submit" id="vendor-import-btn"
                                                class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                                Impor Data
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Import Result -->
                        <div id="import-vendor-step-3" class="hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-semibold text-[#213268]">Impor Berhasil!</p>
                                        <p class="mt-2 text-sm text-gray-600">Data vendor Anda telah berhasil diimpor.</p>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('vendor:create'))
                const addButtons = document.querySelectorAll('#addVendorBtn');
                addButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('vendor:import'))
                    const importButtons = document.querySelectorAll('#importVendorBtn');
                    importButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('vendor:edit'))
                    const editButtons = document.querySelectorAll('.edit-vendor-btn');
                    editButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('vendor:delete'))
                    const deleteButtons = document.querySelectorAll('.delete-vendor-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

            const addVendorBtn = document.getElementById('addVendorBtn');
            const addVendorModal = document.getElementById('addVendorModal');
            const editVendorModal = document.getElementById('editVendorModal');
            const deleteVendorModal = document.getElementById('deleteVendorModal');
            const importVendorModal = document.getElementById('importVendorModal');
            const closeButtons = document.querySelectorAll('.close-modal');

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif

            window.changeVendorPerPage = function(limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', limit);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }

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

            window.changePage = function (page) {
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
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

                    if (modal.id === 'importVendorModal') {
                        const fileInput = document.getElementById('vendor_excel_file');
                        if (fileInput) fileInput.value = '';

                        const fileNameContainer = document.getElementById('vendor-excel-file-name');
                        if (fileNameContainer) fileNameContainer.classList.add('hidden');

                        const previewBtn = document.getElementById('vendor-preview-btn');
                        if (previewBtn) previewBtn.disabled = true;

                        const errorMsg = document.getElementById('vendor-excel-error');
                        if (errorMsg) {
                            errorMsg.textContent = '';
                            errorMsg.classList.add('hidden');
                        }

                        const loading = document.getElementById('vendor-excel-loading');
                        if (loading) loading.classList.add('hidden');

                        const step1 = document.getElementById('import-vendor-step-1');
                        const step2 = document.getElementById('import-vendor-step-2');
                        const step3 = document.getElementById('import-vendor-step-3');

                        if (step1) step1.classList.remove('hidden');
                        if (step2) step2.classList.add('hidden');
                        if (step3) step3.classList.add('hidden');

                        const previewTable = document.getElementById('vendor-preview-table-body');
                        if (previewTable) previewTable.innerHTML = '';

                        const warningsContainer = document.getElementById('vendor-preview-warnings');
                        const warningsList = document.getElementById('vendor-warning-list');

                        if (warningsContainer) warningsContainer.classList.add('hidden');
                        if (warningsList) warningsList.innerHTML = '';
                    }
                    else if (modal.id === 'addVendorModal') {
                        const form = document.getElementById('createVendorForm');
                        if (form) {
                            form.reset();

                            const inputs = form?.querySelectorAll('input, textarea, select');
                            inputs?.forEach(input => {
                                input.classList.remove('border-red-500');
                                const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                                if (errorElement) errorElement.classList.add('hidden');
                            });

                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = 'Simpan';
                            }
                        }
                    }
                    else if (modal.id === 'editVendorModal') {
                        const form = document.getElementById('editVendorForm');
                        if (form) form.reset();

                        const inputs = form?.querySelectorAll('input, textarea, select');
                        inputs?.forEach(input => {
                            input.classList.remove('border-red-500');
                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        });
                    }
                }, 300);
            }

            if (addVendorBtn) {
                addVendorBtn.addEventListener('click', () => {
                    const form = document.getElementById('createVendorForm');
                    if (form) {
                        form.reset();
                        const errorElements = form.querySelectorAll('.error-message');
                        errorElements.forEach(el => el.classList.add('hidden'));

                        const inputs = form.querySelectorAll('input, textarea');
                        inputs.forEach(input => input.classList.remove('border-red-500'));
                    }

                    const submitBtn = document.getElementById('submitVendorBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = 'Simpan';
                    }

                    openModal(addVendorModal, addVendorModal.querySelector('[id$="ModalContent"]'));
                });
            }

            function preventMultipleSubmits(form, buttonSelector) {
                if (!form) return;

                form.addEventListener('submit', function (event) {
                    if (this.checkValidity()) {
                        const submitBtn = this.querySelector(buttonSelector);
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

                            setTimeout(() => {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }, 10000);
                        }
                    }
                });
            }

            const createVendorForm = document.getElementById('createVendorForm');
            const editVendorForm = document.getElementById('editVendorForm');
            const deleteVendorForm = document.getElementById('deleteVendorForm');
            const vendorImportForm = document.getElementById('vendor-import-form');

            preventMultipleSubmits(createVendorForm, 'button[type="submit"]');
            preventMultipleSubmits(editVendorForm, 'button[type="submit"]');
            preventMultipleSubmits(deleteVendorForm, 'button[type="submit"]');
            preventMultipleSubmits(vendorImportForm, 'button[type="submit"]');

            document.querySelectorAll('.edit-vendor-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const vendorId = button.getAttribute('data-vendor-id');
                    const vendorName = button.getAttribute('data-vendor-name');
                    const contactPerson = button.getAttribute('data-contact-person');
                    const phoneNumber = button.getAttribute('data-phone-number');
                    const email = button.getAttribute('data-email');
                    const website = button.getAttribute('data-website');
                    const address = button.getAttribute('data-address');

                    const formAction = "{{ url('vendor/update') }}/" + vendorId;
                    const editForm = document.getElementById('editVendorForm');
                    if (editForm) {
                        editForm.action = formAction;
                    }

                    const idField = document.getElementById('editVendorId');
                    const nameField = document.getElementById('editVendorName');
                    const contactField = document.getElementById('editContactPerson');
                    const phoneField = document.getElementById('editPhoneNumber');
                    const emailField = document.getElementById('editEmail');
                    const websiteField = document.getElementById('editWebsite');
                    const addressField = document.getElementById('editAddress');

                    if (idField) idField.value = vendorId;
                    if (nameField) nameField.value = vendorName;
                    if (contactField) contactField.value = contactPerson;
                    if (phoneField) phoneField.value = phoneNumber;
                    if (emailField) emailField.value = email;
                    if (websiteField) websiteField.value = website || '';
                    if (addressField) addressField.value = address || '';

                    if (editVendorModal) {
                        const modalContent = editVendorModal.querySelector('[id$="ModalContent"]');
                        if (modalContent) {
                            openModal(editVendorModal, modalContent);
                        }
                    }
                });
            });

            document.querySelectorAll('.delete-vendor-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const vendorId = button.getAttribute('data-vendor-id');

                    const formAction = "{{ url('vendor/delete') }}/" + vendorId;
                    const deleteForm = document.getElementById('deleteVendorForm');
                    if (deleteForm) {
                        deleteForm.action = formAction;
                    }

                    const idField = document.getElementById('deleteVendorId');
                    if (idField) {
                        idField.value = vendorId;
                    }

                    if (deleteVendorModal) {
                        const modalContent = deleteVendorModal.querySelector('[id$="ModalContent"]');
                        if (modalContent) {
                            openModal(deleteVendorModal, modalContent);
                        }
                    }
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modal = button.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                });
            });

            const modals = [addVendorModal, editVendorModal, deleteVendorModal, importVendorModal].filter(modal => modal);
            modals.forEach(modal => {
                modal.addEventListener('click', function (e) {
                    if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                        e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                        const content = this.querySelector('[id$="ModalContent"]');
                        closeModal(this, content);
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    modals.forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        }
                    });
                }
            });

            const submitVendorBtn = document.getElementById('submitVendorBtn');

            function handleCreateVendorSubmit(event) {
                event.preventDefault();

                const vendorNameInput = document.getElementById('add_vendor_name');
                let isValid = true;

                if (!validateField(vendorNameInput)) isValid = false;

                if (!isValid) {
                    showToast('Silakan isi nama vendor dengan benar', 'error');
                    return false;
                }

                createVendorForm.submit();
            }

            if (createVendorForm) {
                createVendorForm.addEventListener('submit', handleCreateVendorSubmit);
            }

            if (submitVendorBtn) {
                submitVendorBtn.addEventListener('click', function (e) {
                    if (!e.defaultPrevented) {
                        handleCreateVendorSubmit(e);
                    }
                });
            }

            if (editVendorForm) {
                editVendorForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const vendorNameInput = document.getElementById('editVendorName');
                    let isValid = true;

                    if (!validateField(vendorNameInput)) isValid = false;

                    if (!isValid) {
                        showToast('Silakan isi nama vendor dengan benar', 'error');
                        return false;
                    }

                    this.submit();
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

            function validateEmail(field) {
                if (!field) return true;
                if (!field.value.trim()) return true;

                let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(field.value.trim())) {
                    field.classList.add('border-red-500');
                    if (errorElement) {
                        errorElement.textContent = 'Format Email tidak valid';
                        errorElement.classList.remove('hidden');
                    }
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                    return true;
                }
            }

            function validateUrl(field) {
                if (!field) return true;
                if (!field.value.trim()) return true;

                let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                const urlRegex = /^(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;

                if (!urlRegex.test(field.value.trim())) {
                    field.classList.add('border-red-500');
                    if (errorElement) {
                        errorElement.textContent = 'Format Website tidak valid';
                        errorElement.classList.remove('hidden');
                    }
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                    return true;
                }
            }

            const addFormFields = [
                document.getElementById('add_vendor_name'),
                document.getElementById('add_contact_person'),
                document.getElementById('add_phone_number'),
                document.getElementById('add_email'),
                document.getElementById('add_website')
            ];

            addFormFields.forEach(field => {
                if (field) {
                    field.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }
            });

            const editFormFields = [
                document.getElementById('editVendorName'),
                document.getElementById('editContactPerson'),
                document.getElementById('editPhoneNumber'),
                document.getElementById('editEmail'),
                document.getElementById('editWebsite')
            ];

            editFormFields.forEach(field => {
                if (field) {
                    field.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }
            });

            function showToast(message, type = 'info') {
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                    document.body.appendChild(toastContainer);
                }

                const toast = document.createElement('div');

                let bgColor, borderColor, textColor, icon;
                if (type === 'success') {
                    bgColor = 'bg-green-100';
                    borderColor = 'border-green-500';
                    textColor = 'text-green-700';
                    icon = `<svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`;
                } else if (type === 'error') {
                    bgColor = 'bg-red-100';
                    borderColor = 'border-red-500';
                    textColor = 'text-red-700';
                    icon = `<svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`;
                } else {
                    bgColor = 'bg-blue-100';
                    borderColor = 'border-blue-500';
                    textColor = 'text-blue-700';
                    icon = `<svg class="h-6 w-6 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`;
                }

                toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300`;
                toast.setAttribute('role', 'alert');

                toast.innerHTML = `
                    <div class="flex items-center">
                        <div class="py-1">
                            ${icon}
                        </div>
                        <div>
                            <p class="font-bold">${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Info!'}</p>
                            <p>${message}</p>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;

                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                }, 10);

                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    setTimeout(() => {
                        if (toast.parentNode === toastContainer) {
                            toastContainer.removeChild(toast);
                        }
                    }, 300);
                }, 5000);
            }

            const searchInput = document.getElementById('searchInput');
            const sortOrder = document.getElementById('sortOrder');

            function applyFilters() {
                const searchValue = searchInput?.value.trim() || '';
                const sortValue = sortOrder?.value || '';

                const url = new URL(window.location.href);

                ['search', 'sort'].forEach(param => {
                    url.searchParams.delete(param);
                });

                if (searchValue) url.searchParams.set('search', searchValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                url.searchParams.set('page', 1);

                window.location.href = url.toString();
            }

            let searchTimeout;
            searchInput?.addEventListener('input', function () {
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

            const importVendorBtn = document.getElementById('importVendorBtn');

            if (importVendorBtn) {
                importVendorBtn.addEventListener('click', () => {
                    openModal(importVendorModal, importVendorModal.querySelector('[id$="ModalContent"]'));
                });
            }

            const vendorExcelFile = document.getElementById('vendor_excel_file');
            const vendorFileNameContainer = document.getElementById('vendor-excel-file-name');
            const vendorFileNameText = document.getElementById('vendor-file-name-text');
            const removeVendorExcel = document.getElementById('remove-vendor-excel');
            const vendorPreviewBtn = document.getElementById('vendor-preview-btn');
            const vendorExcelError = document.getElementById('vendor-excel-error');
            const vendorExcelLoading = document.getElementById('vendor-excel-loading');

            if (vendorExcelFile) {
                vendorExcelFile.addEventListener('change', function (e) {
                    if (vendorExcelError) vendorExcelError.classList.add('hidden');

                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();

                        if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                            if (vendorExcelError) {
                                vendorExcelError.textContent = 'Tipe file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                                vendorExcelError.classList.remove('hidden');
                            }
                            this.value = '';
                            if (vendorFileNameContainer) vendorFileNameContainer.classList.add('hidden');
                            if (vendorPreviewBtn) vendorPreviewBtn.disabled = true;
                            return;
                        }

                        if (vendorFileNameText) vendorFileNameText.textContent = file.name;
                        if (vendorFileNameContainer) vendorFileNameContainer.classList.remove('hidden');
                        if (vendorPreviewBtn) vendorPreviewBtn.disabled = false;
                    } else {
                        if (vendorFileNameContainer) vendorFileNameContainer.classList.add('hidden');
                        if (vendorPreviewBtn) vendorPreviewBtn.disabled = true;
                    }
                });
            }

            if (removeVendorExcel) {
                removeVendorExcel.addEventListener('click', function () {
                    if (vendorExcelFile) vendorExcelFile.value = '';
                    if (vendorFileNameContainer) vendorFileNameContainer.classList.add('hidden');
                    if (vendorPreviewBtn) vendorPreviewBtn.disabled = true;
                    if (vendorExcelError) vendorExcelError.classList.add('hidden');
                });
            }

            if (vendorPreviewBtn) {
                vendorPreviewBtn.addEventListener('click', function () {
                    if (!vendorExcelFile || !vendorExcelFile.files || !vendorExcelFile.files[0]) {
                        if (vendorExcelError) {
                            vendorExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                            vendorExcelError.classList.remove('hidden');
                        }
                        return;
                    }

                    const file = vendorExcelFile.files[0];

                    if (vendorExcelLoading) vendorExcelLoading.classList.remove('hidden');
                    if (vendorExcelError) vendorExcelError.classList.add('hidden');

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

                            processVendorExcelData(rows);

                            if (vendorExcelLoading) vendorExcelLoading.classList.add('hidden');

                            document.getElementById('import-vendor-step-1').classList.add('hidden');
                            document.getElementById('import-vendor-step-2').classList.remove('hidden');
                        } catch (error) {
                            console.error('Excel parsing error:', error);
                            if (vendorExcelLoading) vendorExcelLoading.classList.add('hidden');
                            if (vendorExcelError) {
                                vendorExcelError.textContent = 'Gagal memproses file: ' + error.message;
                                vendorExcelError.classList.remove('hidden');
                            }
                        }
                    };

                    reader.onerror = function () {
                        console.error('FileReader error:', reader.error);
                        if (vendorExcelLoading) vendorExcelLoading.classList.add('hidden');
                        if (vendorExcelError) {
                            vendorExcelError.textContent = 'Gagal membaca file. Silakan coba file lainnya.';
                            vendorExcelError.classList.remove('hidden');
                        }
                    };

                    reader.readAsArrayBuffer(file);
                });
            }

            const vendorBackBtn = document.getElementById('vendor-back-to-upload-btn');
            if (vendorBackBtn) {
                vendorBackBtn.addEventListener('click', function () {
                    document.getElementById('import-vendor-step-2').classList.add('hidden');
                    document.getElementById('import-vendor-step-1').classList.remove('hidden');
                });
            }

            function processVendorExcelData(data) {
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

                    item.vendor_name = getValue(['vendor_name', 'vendor name', 'name', 'nama vendor', 'nama_vendor']);
                    item.contact_person = getValue(['contact_person', 'contact person', 'contactperson', 'kontak', 'kontak_person', 'cp']);
                    item.phone_number = getValue(['phone_number', 'phone number', 'phonenumber', 'no_telp', 'no telp', 'telepon', 'nomor_telepon', 'hp']);
                    item.email = getValue(['email', 'email_address', 'email address']);
                    item.website = getValue(['website', 'web', 'site', 'url']);
                    item.address = getValue(['address', 'alamat', 'location', 'lokasi']);

                    if (!item.vendor_name) {
                        warnings.push(`Row ${rowIndex + 2}: Nama Vendor tidak boleh kosong`);
                    }

                    item._rowNum = rowIndex + 2;

                    previewData.push(item);
                });

                const vendorNameMap = {};
                previewData.forEach(item => {
                    if (item.vendor_name) {
                        const key = item.vendor_name.toLowerCase();
                        if (!vendorNameMap[key]) {
                            vendorNameMap[key] = [];
                        }
                        vendorNameMap[key].push(item._rowNum);
                    }
                });

                Object.entries(vendorNameMap).forEach(([key, rows]) => {
                    if (rows.length > 1) {
                        warnings.push(`Vendor duplikat "${key}" ditemukan di baris: ${rows.join(', ')}`);
                    }
                });

                document.getElementById('vendor_excel_data').value = JSON.stringify(previewData);

                showVendorDataPreview(previewData, warnings);
            }

            function showVendorDataPreview(data, warnings) {
                const previewTableBody = document.getElementById('vendor-preview-table-body');
                const previewCount = document.getElementById('vendor-preview-count');
                const warningsContainer = document.getElementById('vendor-preview-warnings');
                const warningsList = document.getElementById('vendor-warning-list');

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

                    const fields = ['vendor_name', 'contact_person', 'phone_number', 'email', 'website', 'address'];

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

                    const importBtn = document.getElementById('vendor-import-btn');
                    const hasCriticalWarnings = warnings.some(warning =>
                        warning.includes('Nama Vendor tidak boleh kosong')
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

            vendorImportForm?.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                const originalFileInput = document.getElementById('vendor_excel_file');
                if (originalFileInput && originalFileInput.files.length > 0) {
                    formData.append('excel_file', originalFileInput.files[0]);
                }

                const importBtn = document.getElementById('vendor-import-btn');
                const originalBtnText = importBtn.innerHTML;
                importBtn.disabled = true;
                importBtn.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        <span>Memproses...</span>
                    </div>
                `;

                fetch('{{ route('vendor.import') }}', {
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
                            console.log('Import successful:', data);

                            const modal = document.getElementById('importVendorModal');
                            closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                            showToast('Data vendor berhasil diimpor!', 'success');

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            console.error('Import error:', data);

                            let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                            let errorDetails = [];

                            if (data.data && data.data.errors) {
                                console.log('Server returned detailed errors:', data.data.errors);

                                if (Array.isArray(data.data.errors)) {
                                    data.data.errors.forEach(error => {
                                        if (typeof error === 'string') {
                                            errorDetails.push(error);
                                        } else if (error.message) {
                                            errorDetails.push(error.message);
                                        } else if (error.vendor_name && error.reason) {
                                            errorDetails.push(`"${error.vendor_name}" - ${error.reason}`);
                                        } else if (error.row && error.reason) {
                                            errorDetails.push(`Baris ${error.row}: ${error.reason}`);
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

                        let errorMessage = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';
                        showToast(error.message || errorMessage, 'error');
                    });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endpush
