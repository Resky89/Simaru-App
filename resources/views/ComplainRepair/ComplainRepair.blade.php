@extends('Layout.app')

@section('title', 'Keluhan & Perbaikan')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">KELUHAN & PERBAIKAN</h1>

                        <div class="flex gap-3">
                            <!-- Button Export PDF -->
                            @if(hasPermission('complaint:export'))
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

                            <!-- Create Complaint Button -->
                            @if(hasPermission('complaint:create'))
                                <button id="createComplaintBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-base">Buat Keluhan</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama aset atau deskripsi..."
                                value="{{ $search ?? '' }}"
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <select id="sortOrder"
                                class="w-[150px] h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" {{ ($sort_order ?? '') == '' ? 'selected' : '' }}>Urutan Default</option>
                                <option value="desc" {{ ($sort_order ?? '') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ ($sort_order ?? '') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                            <select id="statusFilter"
                                class="w-[160px] h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" {{ ($status ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                                <option value="new" {{ ($status ?? '') == 'new' ? 'selected' : '' }}>Baru</option>
                                <option value="in progress" {{ ($status ?? '') == 'in progress' ? 'selected' : '' }}>Sedang
                                    Diproses</option>
                                <option value="finished" {{ ($status ?? '') == 'finished' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Disetujui
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Complaint & Repair Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Keluhan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal Selesai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Pelapor</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="complaintsTableBody">
                                @forelse($complaints ?? [] as $complaint)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $complaint['asset_name'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $complaint['description'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @php
                                                $statusClass = '';
                                                $status = $complaint['status'] ?? '';

                                                if ($status == 'new') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                } elseif ($status == 'in progress') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                } elseif ($status == 'finished') {
                                                    $statusClass = 'bg-emerald-100 text-emerald-800';
                                                } elseif ($status == 'approved') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } else {
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                }

                                                // Translate status text to Indonesian
                                                $statusText = 'Tidak Diketahui';
                                                if ($status == 'new')
                                                    $statusText = 'Baru';
                                                elseif ($status == 'in progress')
                                                    $statusText = 'Sedang Diproses';
                                                elseif ($status == 'finished')
                                                    $statusText = 'Selesai';
                                                elseif ($status == 'approved')
                                                    $statusText = 'Disetujui';
                                            @endphp
                                            <span
                                                class="px-3 py-1.5 rounded-full text-xs font-medium {{ $statusClass }} inline-block min-w-[90px] text-center whitespace-nowrap">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ isset($complaint['complaint_date']) ? \Carbon\Carbon::parse($complaint['complaint_date'])->locale('id')->isoFormat('DD MMMM YYYY') : '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($complaint['finished_date']) && $complaint['finished_date'] && $complaint['finished_date'] != '-')
                                                {{ \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            {{ $complaint['reporter_name'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="viewComplaintDetails({{ $complaint['id'] }})"
                                                    class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                @if(hasPermission('repair:medical') || hasPermission('repair:non-medical'))
                                                    @if($complaint['status'] == 'new' || $complaint['status'] == 'in progress')
                                                        <button
                                                            class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors repair-complaint-btn"
                                                            data-id="{{ $complaint['id'] }}"
                                                            data-asset="{{ $complaint['asset_name'] ?? 'Unknown' }}"
                                                            data-status="{{ $complaint['status'] }}" title="Lakukan Perbaikan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endif
                                                @if(hasPermission('complaint:delete'))
                                                    <button
                                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-complaint-btn"
                                                        data-id="{{ $complaint['id'] }}"
                                                        data-name="{{ $complaint['asset_name'] ?? 'Unknown' }}"
                                                        title="Hapus Keluhan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
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
                                        <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada
                                            keluhan ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($complaints_pagination) && $complaints_pagination)
                        <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                            <div class="flex items-center space-x-2">
                                <button
                                    onclick="window.location.href='{{ isset($complaints_pagination['has_prev']) && $complaints_pagination['has_prev'] ? request()->fullUrlWithQuery(['page' => $complaints_pagination['current_page'] - 1]) : '#' }}'"
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($complaints_pagination['has_prev']) || !$complaints_pagination['has_prev'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !isset($complaints_pagination['has_prev']) || !$complaints_pagination['has_prev'] ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Sebelumnya
                                </button>
                                <div class="flex gap-2">
                                    @php
                                        $currentPage = $complaints_pagination['current_page'] ?? 1;
                                        $lastPage = isset($complaints_pagination['total_pages']) ? $complaints_pagination['total_pages'] : (isset($complaints_pagination['total_items']) && isset($complaints_pagination['limit']) && $complaints_pagination['limit'] > 0 ? ceil($complaints_pagination['total_items'] / $complaints_pagination['limit']) : 1);
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
                                    onclick="window.location.href='{{ isset($complaints_pagination['has_next']) && $complaints_pagination['has_next'] ? request()->fullUrlWithQuery(['page' => $complaints_pagination['current_page'] + 1]) : '#' }}'"
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ !isset($complaints_pagination['has_next']) || !$complaints_pagination['has_next'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !isset($complaints_pagination['has_next']) || !$complaints_pagination['has_next'] ? 'disabled' : '' }}>
                                    Selanjutnya
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 mt-4 md:mt-0">
                                <span class="text-sm text-gray-600">
                                    @php
                                        $currentPage = $complaints_pagination['current_page'] ?? 1;
                                        $perPage = $complaints_pagination['limit'] ?? 10;
                                        $total = $complaints_pagination['total_items'] ?? 0;
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                </span>
                                <select id="perPageSelect"
                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                    onchange="changePerPage(this.value)">
                                    <option value="10" {{ (isset($complaints_pagination['limit']) && $complaints_pagination['limit'] == 10) ? 'selected' : '' }}>10 per halaman</option>
                                    <option value="25" {{ (isset($complaints_pagination['limit']) && $complaints_pagination['limit'] == 25) ? 'selected' : '' }}>25 per halaman</option>
                                    <option value="50" {{ (isset($complaints_pagination['limit']) && $complaints_pagination['limit'] == 50) ? 'selected' : '' }}>50 per halaman</option>
                                    <option value="100" {{ (isset($complaints_pagination['limit']) && $complaints_pagination['limit'] == 100) ? 'selected' : '' }}>100 per halaman</option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Create Complaint Modal -->
    @if(hasPermission('complaint:create'))
        <div id="createComplaintModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="createComplaintModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">BUAT KELUHAN</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Error messages container -->
                        <div id="errorMessages" class="px-6 pt-4">
                            @if ($errors->any())
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                                    <p class="font-bold">Error validasi:</p>
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Form -->
                        <form id="complaintForm" action="{{ route('complaint.create') }}" method="POST"
                            enctype="multipart/form-data" data-no-loading>
                            @csrf
                            <input type="hidden" name="handle_ajax" value="0">
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Complaint Information Section -->
                                    <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Keluhan</h3>

                                    <!-- Asset Selection -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Aset<span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="text" id="assetSearch" placeholder="Cari aset..."
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20" />
                                            <input type="hidden" id="assetId" name="asset_id" />
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <div id="assetDropdown"
                                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg overflow-y-auto max-h-60 hidden">
                                                <div class="p-2" id="assetDropdownContent">
                                                    <!-- Options will be populated dynamically -->
                                                </div>
                                                <div id="assetLoadingIndicator" class="p-4 text-center text-gray-500 hidden">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    <p class="mt-1">Memuat...</p>
                                                </div>
                                                <div id="assetNoResults" class="p-2 text-center text-gray-500 hidden">
                                                    Tidak ada aset ditemukan
                                                </div>
                                                <div id="assetLoadMore" class="p-2 text-center border-t border-gray-200 hidden">
                                                    <button type="button" class="text-[#213268] hover:underline text-sm">Muat
                                                        lebih banyak</button>
                                                </div>
                                            </div>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Aset harus dipilih</div>
                                        </div>
                                        <div id="selectedAssetInfo" class="mt-2 p-2 bg-gray-100 rounded-lg hidden">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-medium" id="selectedAssetName"></p>
                                                    <p class="text-sm text-gray-500" id="selectedAssetId"></p>
                                                </div>
                                                <button type="button" id="clearAssetSelection"
                                                    class="text-red-600 hover:text-red-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Deskripsi<span
                                                class="text-red-500">*</span></label>
                                        <textarea id="description" name="description" rows="4"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 resize-none"
                                            placeholder="Jelaskan masalahnya..."></textarea>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Deskripsi harus diisi</div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Gambar<span
                                                class="text-red-500">*</span></label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- Image preview -->
                                            <div id="imagePreview" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <img id="previewImg" src="#" alt="Pratinjau"
                                                        class="w-full h-auto max-h-64 object-contain mx-auto rounded">
                                                    <button type="button" id="removeImage"
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
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
                                                <p class="mt-1 text-sm text-gray-600">Tarik gambar atau <span
                                                        class="text-[#213268] font-semibold">pilih file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png
                                                    (Ukuran maks: 5MB)</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di area ini untuk
                                                    memilih file</p>
                                            </div>
                                            <input id="imageFile" name="image_file" type="file"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                accept="image/*" />
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Gambar harus diunggah</div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Buat Keluhan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Complaint Confirmation Modal -->
    @if(hasPermission('complaint:delete'))
        <div id="deleteComplaintModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteComplaintModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Hapus Keluhan</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteComplaintForm" data-no-loading>
                            @csrf
                            <input type="hidden" id="deleteComplaintId" name="complaint_id">
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus keluhan
                                            ini? Tindakan ini tidak dapat dibatalkan.</p>
                                        <p id="deleteComplaintName" class="text-base font-semibold text-center mt-2"></p>
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

    <!-- Repair Complaint Modal -->
    @if(hasPermission('repair:medical') || hasPermission('repair:non-medical'))
        <div id="repairComplaintModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="repairComplaintModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">LAKUKAN PERBAIKAN</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Error messages container -->
                        <div id="repairErrorMessages" class="px-6 pt-4"></div>

                        <!-- Form -->
                        <form id="repairForm" action="{{ route('complaint.repair.create') }}" method="POST"
                            enctype="multipart/form-data" data-no-loading>
                            @csrf
                            <input type="hidden" name="complaint_id" id="repairComplaintId">
                            <div class="p-6">
                                <!-- Required fields note -->
                                <div class="text-sm text-gray-600 mb-4">
                                    Bidang dengan tanda <span class="text-red-500">*</span> wajib diisi
                                </div>

                                <div class="space-y-6">
                                    <!-- ASSET INFORMATION SECTION -->
                                    <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                        <h3 class="text-[#213268] font-semibold text-lg mb-4">Informasi Aset</h3>

                                        <!-- Asset Image -->
                                        <div
                                            class="w-full h-40 bg-white mb-4 rounded-lg shadow-sm overflow-hidden relative flex items-center justify-center">
                                            <img id="repair_asset_image" src="{{ asset('images/placeholder.png') }}"
                                                alt="Asset Image" class="w-full h-full object-contain p-2"
                                                onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Left Column -->
                                            <div class="space-y-4">
                                                <!-- Asset Code -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">KODE ASET</label>
                                                    <input type="text" id="repairAssetCode"
                                                        class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                        readonly>
                                                </div>

                                                <!-- Asset Name -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">NAMA ASET</label>
                                                    <input type="text" id="repairAssetName"
                                                        class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                        readonly>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="space-y-4">
                                                <!-- Serial Number -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">NOMOR SERI</label>
                                                    <input type="text" id="repairSerialNumber"
                                                        class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                        readonly>
                                                </div>

                                                <!-- Model -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">MODEL</label>
                                                    <input type="text" id="repairModel"
                                                        class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- COMPLAINT INFORMATION SECTION -->
                                    <div class="bg-yellow-100 rounded-lg p-4 mb-6">
                                        <h3 class="text-[#213268] font-semibold text-lg mb-4">Informasi Keluhan</h3>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                            <!-- Complaint Date -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">TANGGAL KELUHAN</label>
                                                <input type="text" id="repairComplaintDate"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>

                                            <!-- Reporter -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">PELAPOR</label>
                                                <input type="text" id="repairReporterName"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>
                                        </div>

                                        <!-- Complaint Description -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700">DESKRIPSI KELUHAN</label>
                                            <textarea id="repairComplaintDescription" rows="3"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none resize-none"
                                                readonly></textarea>
                                        </div>

                                        <!-- Complaint Image -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">GAMBAR KELUHAN</label>
                                            <div
                                                class="w-full h-40 bg-white rounded-lg shadow-sm overflow-hidden relative flex items-center justify-center">
                                                <img id="repair_complaint_image" src="{{ asset('images/placeholder.png') }}"
                                                    alt="Complaint Image" class="w-full h-full object-contain p-2"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- REPAIR DETAILS SECTION -->
                                    <div class="bg-green-100 rounded-lg p-4 mb-6">
                                        <h3 class="text-[#213268] font-semibold text-lg mb-4">Detail Perbaikan</h3>

                                        <div class="space-y-4">
                                            <!-- Final Result -->
                                            <div>
                                                <label for="finalResult" class="block text-sm font-medium text-gray-700">
                                                    HASIL AKHIR<span class="text-red-500">*</span>
                                                </label>
                                                <select id="finalResult" name="final_result"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                    <option value="" disabled selected>Pilih hasil akhir</option>
                                                    <option value="Good">Baik</option>
                                                    <option value="Slightly Damage">Sedikit Rusak</option>
                                                    <option value="Heavy Damage">Rusak Parah</option>
                                                    <option value="Waiting for Part">Menunggu Spare Part</option>
                                                </select>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Hasil akhir
                                                    harus dipilih</div>
                                            </div>

                                            <!-- Repair Cost -->
                                            <div>
                                                <label for="repairCost" class="block text-sm font-medium text-gray-700">
                                                    BIAYA PERBAIKAN<span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative mt-1">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                                    </div>
                                                    <input type="text" id="repairCost" name="repair_cost"
                                                        class="mt-1 block w-full pl-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                        placeholder="Biaya perbaikan" onkeyup="formatCurrency(this)"
                                                        onblur="formatCurrency(this, 'blur')">
                                                </div>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya perbaikan
                                                    harus diisi</div>
                                            </div>

                                            <!-- Parts Replaced -->
                                            <div>
                                                <label for="partsReplaced" class="block text-sm font-medium text-gray-700">
                                                    KOMPONEN YANG DIGANTI<span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="partsReplaced" name="parts_replaced"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                    placeholder="Daftar komponen yang diganti">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Komponen yang
                                                    diganti harus diisi</div>
                                            </div>
                                            <!-- Repair Description -->
                                            <div>
                                                <label for="repairDescription" class="block text-sm font-medium text-gray-700">
                                                    DESKRIPSI PERBAIKAN<span class="text-red-500">*</span>
                                                </label>
                                                <textarea id="repairDescription" name="repair_description" rows="3"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                    placeholder="Jelaskan pekerjaan perbaikan..."></textarea>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Deskripsi
                                                    perbaikan harus diisi</div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- DOCUMENTATION SECTION -->
                                    <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                        <h3 class="text-[#213268] font-semibold text-lg mb-4">Dokumentasi</h3>

                                        <!-- Image Upload -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">GAMBAR PERBAIKAN<span
                                                    class="text-red-500">*</span></label>
                                            <div
                                                class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                <!-- Image preview -->
                                                <div id="repairImagePreview" class="mt-2 mb-4 w-full hidden">
                                                    <div
                                                        class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                        <img id="repairPreviewImg" src="#" alt="Pratinjau"
                                                            class="w-full h-auto max-h-64 object-contain mx-auto rounded">
                                                        <button type="button" id="removeRepairImage"
                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <svg class="mx-auto h-12 w-12 text-[#213268]"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-1 text-sm text-gray-600">Tarik gambar atau <span
                                                            class="text-[#213268] font-semibold">pilih file</span></p>
                                                    <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png
                                                        (Ukuran maks: 5MB)</p>
                                                    <p class="mt-1 text-xs text-[#213268] font-medium">Klik di area ini untuk
                                                        memilih file</p>
                                                </div>
                                                <input id="repairImageFile" name="file" type="file"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                    accept="image/*" />
                                            </div>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Gambar perbaikan harus
                                                diunggah</div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full py-3 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transition-colors duration-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Simpan Perbaikan
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

    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 flex items-center';

            if (type === 'success') {
                toast.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
            } else {
                toast.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
            }

            toast.innerHTML = `
                        <div class="py-1">
                             <svg class="h-6 w-6 mr-4 ${type === 'success' ? 'text-green-500' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                ${type === 'success'
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">${type === 'success' ? 'Berhasil!' : 'Gagal!'}</p>
                            <p>${message}</p>
                            </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.remove()">×</span>
                    `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('complaint:create'))
                const createButtons = document.querySelectorAll('#createComplaintBtn');
                createButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('complaint:export'))
                    const exportButtons = document.querySelectorAll('#exportBtn');
                    exportButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('complaint:delete'))
                    const deleteButtons = document.querySelectorAll('.delete-complaint-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('repair:medical') && !hasPermission('repair:non-medical'))
                    const repairButtons = document.querySelectorAll('.repair-complaint-btn');
                    repairButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

            window.formatCurrency = function(input, blur) {
                let input_val = input.value;

                if (input_val === "") { return; }

                if (input_val.indexOf(",") >= 0) {
                    var decimal_pos = input_val.indexOf(",");

                    var left_side = input_val.substring(0, decimal_pos);
                    var right_side = input_val.substring(decimal_pos);

                    left_side = left_side.replace(/\D/g, "");
                    right_side = right_side.replace(/\D/g, "");

                    right_side = right_side.substring(0, 2);

                    left_side = left_side.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                    input_val = left_side + "," + right_side;
                } else {
                    input_val = input_val.replace(/\D/g, "");

                    input_val = input_val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                    if (blur === "blur") {
                        input_val += ",00";
                    }
                }

                input.value = input_val;
            };

            const imageFile = document.getElementById('imageFile');
            const previewImg = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            const removeImage = document.getElementById('removeImage');
            const complaintForm = document.getElementById('complaintForm');
            const errorMsgDiv = document.getElementById('errorMessages');
            const createComplaintBtn = document.getElementById('createComplaintBtn');
            const createComplaintModal = document.getElementById('createComplaintModal');
            const createComplaintModalContent = document.getElementById('createComplaintModalContent');
            const closeModalBtns = document.querySelectorAll('.close-modal');
            const exportBtn = document.getElementById('exportBtn');
            const searchInput = document.getElementById('searchInput');
            const sortOrder = document.getElementById('sortOrder');
            const statusFilter = document.getElementById('statusFilter');
            const perPageSelect = document.getElementById('perPageSelect');
            const assetSearch = document.getElementById('assetSearch');
            const assetDropdown = document.getElementById('assetDropdown');
            const assetDropdownContent = document.getElementById('assetDropdownContent');
            const assetLoadingIndicator = document.getElementById('assetLoadingIndicator');
            const assetNoResults = document.getElementById('assetNoResults');
            const assetId = document.getElementById('assetId');
            const selectedAssetInfo = document.getElementById('selectedAssetInfo');
            const selectedAssetName = document.getElementById('selectedAssetName');
            const selectedAssetId = document.getElementById('selectedAssetId');
            const clearAssetSelection = document.getElementById('clearAssetSelection');
            const deleteComplaintModal = document.getElementById('deleteComplaintModal');
            const deleteComplaintModalContent = document.getElementById('deleteComplaintModalContent');
            const repairComplaintModal = document.getElementById('repairComplaintModal');
            const repairComplaintModalContent = document.getElementById('repairComplaintModalContent');
            const repairForm = document.getElementById('repairForm');
            const repairErrorMsgDiv = document.getElementById('repairErrorMessages');
            const repairComplaintId = document.getElementById('repairComplaintId');
            const repairAssetName = document.getElementById('repairAssetName');
            const repairImagePreview = document.getElementById('repairImagePreview');

            function resetComplaintForm() {
                // Reset the form if it exists
                if (complaintForm) {
                    complaintForm.reset();
                }

                // Clear error messages
                if (errorMsgDiv) {
                    errorMsgDiv.innerHTML = '';
                }

                // Clear asset selection
                if (assetId) {
                    assetId.value = '';
                }
                if (assetSearch) {
                    assetSearch.value = '';
                }
                if (selectedAssetInfo) {
                    selectedAssetInfo.classList.add('hidden');
                }

                // Clear image preview
                if (imagePreview) {
                    imagePreview.classList.add('hidden');
                }
                if (imageFile) {
                    imageFile.value = '';
                }

                // Remove any error styling
                const errorFields = complaintForm?.querySelectorAll('.border-red-500');
                errorFields?.forEach(field => field.classList.remove('border-red-500'));
                const errorMessages = complaintForm?.querySelectorAll('.error-message');
                errorMessages?.forEach(msg => msg.classList.add('hidden'));
            }

            function clearRepairForm() {
                // Reset the form if it exists
                if (repairForm) {
                    repairForm.reset();
                }

                // Clear error messages
                if (repairErrorMsgDiv) {
                    repairErrorMsgDiv.innerHTML = '';
                }

                // Clear image preview
                if (repairImagePreview) {
                    repairImagePreview.classList.add('hidden');
                }
                if (document.getElementById('repairImageFile')) {
                    document.getElementById('repairImageFile').value = '';
                }

                // Remove any error styling
                const errorFields = repairForm?.querySelectorAll('.border-red-500');
                errorFields?.forEach(field => field.classList.remove('border-red-500'));
                const errorMessages = repairForm?.querySelectorAll('.error-message');
                errorMessages?.forEach(msg => msg.classList.add('hidden'));
            }

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif

                function openModal(modal, content) {
                    if (!modal || !content) return;

                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                }

            function closeModal(modal, content) {
                if (!modal || !content) return;

                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function debounce(func, wait) {
                let timeout;
                return function () {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }

            function applyFilters() {
                const searchTerm = searchInput?.value || '';
                const sort_order = sortOrder?.value || '';
                const status = statusFilter?.value || '';
                const limit = perPageSelect?.value || 10;

                const url = new URL(window.location.href);

                if (searchTerm) url.searchParams.set('search', searchTerm);
                else url.searchParams.delete('search');

                if (sort_order) url.searchParams.set('sort_order', sort_order);
                else url.searchParams.delete('sort_order');

                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');

                url.searchParams.set('limit', limit);

                url.searchParams.set('page', 1);

                window.location.href = url.toString();
            }

            window.changePerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', limit);
                window.location.href = url.toString();
            }

            if (imageFile) {
                imageFile.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file && previewImg && imagePreview) {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            previewImg.src = e.target.result;
                            imagePreview.classList.remove('hidden');
                        }

                        reader.readAsDataURL(file);
                    }
                });
            }

            if (removeImage) {
                removeImage.addEventListener('click', function () {
                    if (imageFile) {
                        imageFile.value = '';
                    }
                    if (imagePreview) {
                        imagePreview.classList.add('hidden');
                    }
                    if (previewImg) {
                        previewImg.src = '#';
                    }
                });
            }

            if (createComplaintBtn && createComplaintModal && createComplaintModalContent) {
                createComplaintBtn.addEventListener('click', function () {
                    resetComplaintForm();
                    openModal(createComplaintModal, createComplaintModalContent);
                });
            }

            if (closeModalBtns && closeModalBtns.length > 0) {
                closeModalBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const modal = this.closest('[id$="Modal"]');
                        const content = modal?.querySelector('[id$="ModalContent"]');
                        if (modal && content) {
                            closeModal(modal, content);
                            if (modal.id === 'createComplaintModal') {
                                resetComplaintForm();
                            } else if (modal.id === 'repairComplaintModal') {
                                clearRepairForm();
                            }
                        }
                    });
                });
            }

            if (createComplaintModal) {
                createComplaintModal.addEventListener('click', function (event) {
                    if (event.target === this && createComplaintModalContent) {
                        closeModal(createComplaintModal, createComplaintModalContent);
                        resetComplaintForm();
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', debounce(function () {
                    applyFilters();
                }, 500));
            }

            if (sortOrder) {
                sortOrder.addEventListener('change', function () {
                    applyFilters();
                });
            }

            if (statusFilter) {
                statusFilter.addEventListener('change', function () {
                    applyFilters();
                });
            }

            if (exportBtn) {
                exportBtn.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    const searchParams = url.searchParams;
                    const exportUrl = "{{ route('complaint.export.pdf') }}?" + searchParams.toString();
                    window.open(exportUrl, '_blank', 'noopener,noreferrer');
                });
            }

            const assets = @json($assets ?? []);
            let assetSearchTimeout;

            if (assetSearch && assetDropdown) {
                document.addEventListener('click', function (e) {
                    if (!assetSearch.contains(e.target) && !assetDropdown.contains(e.target)) {
                        assetDropdown.classList.add('hidden');
                    }
                });
            }

            if (assetSearch && assetDropdown && assetId && assetLoadingIndicator && assetNoResults && assetDropdownContent) {
                assetSearch.addEventListener('focus', function () {
                    if (!assetId.value) {
                        assetPage = 1;
                        hasMoreAssets = true;
                        currentAssetSearch = '';

                        assetDropdown.classList.remove('hidden');
                        searchAssets('', assetPage, false);
                    }
                });
            }

            if (assetSearch && assetLoadingIndicator && assetNoResults && assetDropdownContent && assetDropdown) {
                assetSearch.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim();

                    assetPage = 1;
                    hasMoreAssets = true;
                    currentAssetSearch = searchTerm;

                    assetDropdown.classList.remove('hidden');
                    clearTimeout(assetSearchTimeout);
                    assetSearchTimeout = setTimeout(function () {
                        searchAssets(searchTerm, assetPage, false);
                    }, 300);
                });
            }

            let assetPage = 1;
            let isLoadingAssets = false;
            let hasMoreAssets = true;
            let currentAssetSearch = '';

            function searchAssets(searchTerm, page = 1, append = false) {
                if (!assetLoadingIndicator || !assetNoResults || !assetDropdownContent) {
                    console.error('Required DOM elements for asset search are missing');
                    return;
                }

                if (!append) {
                    assetLoadingIndicator.classList.remove('hidden');
                    assetNoResults.classList.add('hidden');
                    assetDropdownContent.innerHTML = '';
                    document.getElementById('assetLoadMore').classList.add('hidden');
                }

                isLoadingAssets = true;
                const searchUrl = `/assets?json=true&exclude_status=dispose&search=${encodeURIComponent(searchTerm)}&limit=10&page=${page}`;

                fetch(searchUrl, {
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
                        let fetchedAssets = [];

                        if (Array.isArray(data)) {
                            fetchedAssets = data;
                        } else if (data.assets && Array.isArray(data.assets)) {
                            fetchedAssets = data.assets;
                        } else if (data.data && Array.isArray(data.data)) {
                            fetchedAssets = data.data;
                        }

                        hasMoreAssets = fetchedAssets.length >= 10;

                        displayFilteredAssets(fetchedAssets, searchTerm, append);
                    })
                    .catch(error => {
                        console.error('Error searching assets:', error);

                        if (assetLoadingIndicator) {
                            assetLoadingIndicator.classList.add('hidden');
                        }

                        if (!append && assetDropdownContent) {
                            assetDropdownContent.innerHTML = `
                                        <div class="p-2 text-center text-red-500">
                                            Gagal mencari aset. Silakan coba lagi.
                                        </div>
                                    `;
                        }

                        isLoadingAssets = false;

                        if (assets && assets.length > 0) {
                            let filteredAssets = assets;
                            if (searchTerm) {
                                filteredAssets = assets.filter(asset =>
                                    (asset.asset_name && asset.asset_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                                    (asset.asset_master_name && asset.asset_master_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                                    (asset.asset_code && asset.asset_code.toLowerCase().includes(searchTerm.toLowerCase())) ||
                                    (asset.asset_id && asset.asset_id.toString().includes(searchTerm))
                                );
                            }
                            filteredAssets = filteredAssets.filter(asset => asset.current_status !== 'dispose');

                            displayFilteredAssets(filteredAssets, searchTerm, append);
                        }
                    });
            }

            function displayFilteredAssets(filteredAssets, searchTerm, append = false) {
                if (!assetDropdownContent || !assetLoadingIndicator || !assetNoResults) {
                    console.error('Required DOM elements for displaying assets are missing');
                    return;
                }

                if (!append) {
                    assetDropdownContent.innerHTML = '';
                }

                assetLoadingIndicator.classList.add('hidden');
                const loadMoreBtn = document.getElementById('assetLoadMore');

                if (!filteredAssets || filteredAssets.length === 0) {
                    if (!append) {
                        assetNoResults.classList.remove('hidden');
                        loadMoreBtn.classList.add('hidden');
                    }
                    isLoadingAssets = false;
                    return;
                }

                assetNoResults.classList.add('hidden');

                filteredAssets.forEach(asset => {
                    const div = document.createElement('div');
                    div.className = 'p-2 hover:bg-gray-100 cursor-pointer rounded transition-colors';
                    div.innerHTML = `
                                <div class="font-medium">${asset.asset_master_name || asset.asset_name || 'Aset Tidak Diketahui'}</div>
                                <div class="text-xs text-gray-500">Kode: ${asset.asset_code || 'N/A'}</div>
                            `;

                    div.addEventListener('click', function () {
                        selectAsset(asset);
                    });

                    assetDropdownContent.appendChild(div);
                });

                if (hasMoreAssets) {
                    loadMoreBtn.classList.remove('hidden');
                    const loadMoreButton = loadMoreBtn.querySelector('button');

                    const newLoadMoreButton = loadMoreButton.cloneNode(true);
                    loadMoreButton.parentNode.replaceChild(newLoadMoreButton, loadMoreButton);

                    newLoadMoreButton.addEventListener('click', function () {
                        assetPage++;
                        searchAssets(currentAssetSearch, assetPage, true);
                        this.disabled = true;
                        this.innerHTML = 'Memuat...';
                        setTimeout(() => {
                            this.disabled = false;
                            this.innerHTML = 'Muat lebih banyak';
                        }, 1000);
                    });
                } else {
                    loadMoreBtn.classList.add('hidden');
                }

                isLoadingAssets = false;
            }

            if (assetDropdown) {
                assetDropdown.addEventListener('scroll', function () {
                    if (!hasMoreAssets || isLoadingAssets) return;

                    if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                        assetPage++;
                        searchAssets(currentAssetSearch, assetPage, true);
                    }
                });
            }

            function selectAsset(asset) {
                if (!assetId || !assetSearch || !assetDropdown || !selectedAssetName ||
                    !selectedAssetId || !selectedAssetInfo) {
                    console.error('Required DOM elements for selecting asset are missing');
                    return;
                }

                assetId.value = asset.asset_id;
                assetSearch.value = asset.asset_master_name || asset.asset_name;
                assetDropdown.classList.add('hidden');

                selectedAssetName.textContent = asset.asset_master_name || asset.asset_name;
                selectedAssetId.textContent = `Code: ${asset.asset_code || 'N/A'}`;
                selectedAssetInfo.classList.remove('hidden');
            }

            clearAssetSelection?.addEventListener('click', function () {
                assetId.value = '';
                assetSearch.value = '';
                selectedAssetInfo.classList.add('hidden');
            });

            if (complaintForm) {
                complaintForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const assetSearchInput = document.getElementById('assetSearch');
                    const descriptionInput = document.getElementById('description');
                    const imageFileInput = document.getElementById('imageFile');

                    if (!assetSearchInput || !descriptionInput || !imageFileInput) {
                        showToast('Form elements tidak ditemukan', 'error');
                        return false;
                    }

                    const isAssetValid = validateField(assetSearchInput, !!document.getElementById('assetId')?.value);
                    const isDescriptionValid = validateField(descriptionInput);
                    const isImageValid = validateField(imageFileInput, imageFileInput.files && imageFileInput.files.length > 0);

                    if (!isAssetValid || !isDescriptionValid || !isImageValid) {
                        if (!isAssetValid) assetSearchInput.focus();
                        else if (!isDescriptionValid) descriptionInput.focus();
                        else if (!isImageValid) imageFileInput.focus();

                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
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
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                });
            }

            if (repairForm) {
                repairForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const repairDescription = document.getElementById('repairDescription');
                    const finalResult = document.getElementById('finalResult');
                    const repairCost = document.getElementById('repairCost');
                    const partsReplaced = document.getElementById('partsReplaced');
                    const repairImageFile = document.getElementById('repairImageFile');

                    if (!repairDescription || !finalResult || !repairCost || !partsReplaced || !repairImageFile) {
                        showToast('Form elements tidak ditemukan', 'error');
                        return false;
                    }

                    const isDescriptionValid = validateField(repairDescription);
                    const isResultValid = validateField(finalResult);
                    const isCostValid = validateField(repairCost);
                    const isPartsValid = validateField(partsReplaced);
                    const isImageValid = validateField(repairImageFile, repairImageFile.files && repairImageFile.files.length > 0);

                    if (!isDescriptionValid || !isResultValid || !isCostValid || !isPartsValid || !isImageValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    if (repairCost && repairCost.value) {
                        let originalCostValue = repairCost.value
                            .replace(/\./g, '')
                            .replace(',', '.');

                        let hiddenCostInput = document.getElementById('repair_cost_numeric');
                        if (!hiddenCostInput) {
                            hiddenCostInput = document.createElement('input');
                            hiddenCostInput.type = 'hidden';
                            hiddenCostInput.id = 'repair_cost_numeric';
                            hiddenCostInput.name = 'repair_cost';
                            repairForm.appendChild(hiddenCostInput);
                        }
                        hiddenCostInput.value = originalCostValue;

                        repairCost.name = 'repair_cost_formatted';
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
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
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                });
            }

            document.querySelectorAll('.delete-complaint-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const complaintId = button.getAttribute('data-id');
                    const complaintName = button.getAttribute('data-name');
                    const deleteComplaintForm = document.getElementById('deleteComplaintForm');
                    if (deleteComplaintForm) {
                        deleteComplaintForm.setAttribute('data-id', complaintId);
                    }

                    const deleteComplaintName = document.getElementById('deleteComplaintName');
                    if (complaintName && deleteComplaintName) {
                        deleteComplaintName.textContent = complaintName;
                    }

                    if (deleteComplaintModal && deleteComplaintModalContent) {
                        openModal(deleteComplaintModal, deleteComplaintModalContent);
                    }
                });
            });

            deleteComplaintModal?.addEventListener('click', function (event) {
                if (event.target === this && deleteComplaintModalContent) {
                    closeModal(deleteComplaintModal, deleteComplaintModalContent);
                    if (document.getElementById('deleteComplaintForm')) {
                        document.getElementById('deleteComplaintForm').reset();
                    }
                }
            });

            repairComplaintModal?.addEventListener('click', function (event) {
                if (event.target === this && repairComplaintModalContent) {
                    closeModal(repairComplaintModal, repairComplaintModalContent);
                    clearRepairForm();
                }
            });

            document.querySelectorAll('.repair-complaint-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const complaintId = button.getAttribute('data-id');
                    const assetName = button.getAttribute('data-asset');
                    const status = button.getAttribute('data-status');
                    const repairComplaintId = document.getElementById('repairComplaintId');
                    const repairAssetName = document.getElementById('repairAssetName');

                    if (repairComplaintId && repairAssetName) {
                        repairComplaintId.value = complaintId;
                        repairAssetName.value = assetName;
                        if (repairForm) {
                            repairForm.reset();
                        }
                        if (repairErrorMsgDiv) {
                            repairErrorMsgDiv.innerHTML = '';
                        }
                        const repairImagePreview = document.getElementById('repairImagePreview');
                        if (repairImagePreview) {
                            repairImagePreview.classList.add('hidden');
                        }

                        const originalButtonHTML = button.innerHTML;
                        button.innerHTML = `
                                    <svg class="animate-spin h-4 w-4 text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                `;
                        button.disabled = true;

                        let fetchPromise;
                        if (status === 'new') {
                            fetchPromise = fetch(`/complaint-repair/${complaintId}/start`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Failed to start repair process');
                                    }
                                    return response.json();
                                })
                                .then(startData => {
                                    if (startData.success) {
                                        const statusBadge = button.closest('tr').querySelector('td:nth-child(3) span');
                                        if (statusBadge) {
                                            statusBadge.textContent = 'Sedang Diproses';
                                            statusBadge.className = 'px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 inline-block min-w-[90px] text-center whitespace-nowrap';
                                        }
                                    }
                                    return fetchComplaintDetails(complaintId);
                                });
                        } else {
                            fetchPromise = fetchComplaintDetails(complaintId);
                        }

                        fetchPromise
                            .catch(error => {
                                console.error('Error processing repair action:', error);
                                showToast('Error loading complaint: ' + (error.message || 'Unknown error'), 'error');
                            })
                            .finally(() => {
                                button.innerHTML = originalButtonHTML;
                                button.disabled = false;

                                if (repairComplaintModal && repairComplaintModalContent) {
                                    openModal(repairComplaintModal, repairComplaintModalContent);
                                }
                            });
                    }
                });
            });

            function fetchComplaintDetails(complaintId) {
                if (!complaintId) return Promise.reject(new Error("Invalid complaint ID"));

                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'text-center py-4';
                loadingIndicator.innerHTML = `
                            <div class="inline-block w-8 h-8 border-4 border-[#213268] border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-gray-600">Memuat data keluhan...</p>
                        `;

                document.getElementById('repair_asset_image').style.opacity = '0.3';
                document.getElementById('repair_complaint_image').style.opacity = '0.3';

                document.getElementById('repairComplaintDescription').value = 'Memuat...';

                return fetch(`/complaint-repair/detail/${complaintId}?json=true`, {
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
                        if (data.success && data.complaint) {
                            const complaint = data.complaint;

                            document.getElementById('repairAssetName').value = complaint.asset_name || 'Tidak diketahui';
                            document.getElementById('repairAssetCode').value = complaint.asset_code || 'Tidak diketahui';
                            document.getElementById('repairSerialNumber').value = complaint.serial_number || 'Tidak diketahui';
                            document.getElementById('repairModel').value = complaint.model || 'Tidak diketahui';
                            document.getElementById('repairComplaintDescription').value = complaint.description || 'Tidak ada deskripsi';

                            if (complaint.complaint_date) {
                                const date = new Date(complaint.complaint_date);
                                const formattedDate = new Intl.DateTimeFormat('id-ID', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                }).format(date);
                                document.getElementById('repairComplaintDate').value = formattedDate;
                            } else {
                                document.getElementById('repairComplaintDate').value = 'Tidak diketahui';
                            }

                            document.getElementById('repairReporterName').value = complaint.reporter_name || 'Tidak diketahui';

                            if (complaint.asset_image_path) {
                                document.getElementById('repair_asset_image').src = `{{ config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api') }}/public${complaint.asset_image_path}`;
                            } else {
                                document.getElementById('repair_asset_image').src = `{{ asset('images/no-image.png') }}`;
                            }

                            if (complaint.complaint_picture_path) {
                                document.getElementById('repair_complaint_image').src = `{{ config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api') }}/public/images/${complaint.complaint_picture_path.split('/').pop()}`;
                            } else {
                                document.getElementById('repair_complaint_image').src = `{{ asset('images/no-image.png') }}`;
                            }

                            return complaint;
                        } else {
                            document.getElementById('repairComplaintDescription').value = 'Tidak dapat memuat deskripsi keluhan';
                            throw new Error('Failed to load complaint details');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching complaint details:', error);
                        document.getElementById('repairComplaintDescription').value = 'Terjadi kesalahan saat memuat data keluhan';
                        throw error;
                    })
                    .finally(() => {
                        document.getElementById('repair_asset_image').style.opacity = '1';
                        document.getElementById('repair_complaint_image').style.opacity = '1';
                    });
            }

            const deleteComplaintForm = document.getElementById('deleteComplaintForm');
            if (deleteComplaintForm) {
                deleteComplaintForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const complaintId = this.getAttribute('data-id');

                    if (!complaintId) {
                        showToast('ID keluhan tidak valid', 'error');
                        return;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                            <span>Memproses...</span>
                                    </div>
                                `;
                    }

                    fetch(`complaint-repair/complaints/${complaintId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ complaint_id: complaintId })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || `Server responded with status ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (deleteComplaintModal && deleteComplaintModalContent) {
                                closeModal(deleteComplaintModal, deleteComplaintModalContent);
                            }

                            if (data.success) {
                                showToast(data.message || 'Keluhan berhasil dihapus', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                showToast(data.message || 'Gagal menghapus keluhan', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Delete request failed:', error);

                            if (deleteComplaintModal && deleteComplaintModalContent) {
                                closeModal(deleteComplaintModal, deleteComplaintModalContent);
                            }

                            showToast(error.message || 'Gagal menghapus keluhan', 'error');

                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        });
                });
            }

            function validateField(field, isValid = null) {
                if (!field) return true;

                let errorElement = field.type === 'file'
                    ? field.parentElement?.parentElement?.querySelector('.error-message')
                    : field.parentElement?.querySelector('.error-message');

                if (isValid === null) {
                    if (field.type === 'select-one') {
                        isValid = field.value !== '';
                    } else if (field.type === 'file') {
                        isValid = field.files && field.files.length > 0;
                    } else if (field.id === 'assetSearch') {
                        isValid = document.getElementById('assetId')?.value !== '';
                    } else {
                        isValid = field.value.trim() !== '';
                    }
                }

                if (!isValid) {
                    field.classList.add('border-red-500');
                    if (errorElement) errorElement.classList.remove('hidden');
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                    return true;
                }
            }

            if (assetSearch) {
                assetSearch.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const description = document.getElementById('description');
            if (description) {
                description.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            if (imageFile) {
                imageFile.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const repairDescription = document.getElementById('repairDescription');
            if (repairDescription) {
                repairDescription.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const finalResult = document.getElementById('finalResult');
            if (finalResult) {
                finalResult.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const repairCost = document.getElementById('repairCost');
            if (repairCost) {
                repairCost.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const partsReplaced = document.getElementById('partsReplaced');
            if (partsReplaced) {
                partsReplaced.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const repairImageFile = document.getElementById('repairImageFile');
            if (repairImageFile) {
                repairImageFile.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.parentElement?.parentElement?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');

                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        const repairPreviewImg = document.getElementById('repairPreviewImg');
                        const repairImagePreview = document.getElementById('repairImagePreview');

                        if (repairPreviewImg && repairImagePreview) {
                            reader.onload = function (e) {
                                repairPreviewImg.src = e.target.result;
                                repairImagePreview.classList.remove('hidden');
                            }

                            reader.readAsDataURL(file);
                        }
                    }
                });
            }

            const removeRepairImage = document.getElementById('removeRepairImage');
            if (removeRepairImage) {
                removeRepairImage.addEventListener('click', function () {
                    const repairImageFile = document.getElementById('repairImageFile');
                    const repairImagePreview = document.getElementById('repairImagePreview');
                    const repairPreviewImg = document.getElementById('repairPreviewImg');

                    if (repairImageFile) {
                        repairImageFile.value = '';
                    }
                    if (repairImagePreview) {
                        repairImagePreview.classList.add('hidden');
                    }
                    if (repairPreviewImg) {
                        repairPreviewImg.src = '#';
                    }
                });
            }
        });

        window.viewComplaintDetails = function (id) {
            window.location.href = "{{ url('complaint-repair/detail') }}/" + id;
        }
    </script>
@endsection
