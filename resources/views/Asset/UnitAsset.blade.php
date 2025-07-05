@extends('Layout.app')

@section('title', 'Manajemen Aset')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Asset Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ASET</h1>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('asset:import'))
                                <button id="importAssetBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            <button id="printQRBtn"
                                class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span class="text-base">Cetak QR</span>
                            </button>
                            @if(hasPermission('asset:export'))
                                <button id="exportBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-base">Ekspor PDF</span>
                                </button>
                            @endif
                            @if(hasPermission('asset:create'))
                                <button id="addAssetBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-base">Tambah Aset</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput"
                                placeholder="Cari berdasarkan nama aset, kode, atau kategori..."
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

                            <select id="statusFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Status</option>
                                <option value="">All Status</option>
                                <option value="available">Tersedia</option>
                                <option value="check out">Dipinjam</option>
                                <option value="dispose">Dihapus</option>
                                <option value="lost">Hilang</option>
                                <option value="under repair">Dalam Perbaikan</option>
                            </select>

                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" disabled selected>Urutkan</option>
                                <option value="newest" {{ request()->query('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">
                                        <input type="checkbox" id="select-all-assets" class="checkbox checkbox-sm" />
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByCode">
                                            <span class="text-xs">Kode Aset</span>
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kategori Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($assets) && count($assets) > 0)
                                    @foreach($assets as $asset)
                                        <tr data-asset-id="{{ $asset['asset_id'] ?? '' }}">
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                <input type="checkbox" class="asset-checkbox checkbox checkbox-sm"
                                                    data-asset-id="{{ $asset['asset_id'] ?? '' }}" />
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_code'] ?? '-' }}</td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                {{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '-' }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                @if(isset($asset['asset_master']) && isset($asset['asset_master']['asset_master_code']))
                                                    @php
                                                        $code = $asset['asset_master']['asset_master_code'];
                                                        $assetType = 'Non Medical';
                                                        if (strpos($code, 'MED-') === 0) {
                                                            $assetType = 'Medis';
                                                        } elseif (strpos($code, 'NMED-') === 0) {
                                                            $assetType = 'Non Medis';
                                                        }
                                                    @endphp
                                                    {{ $assetType }}
                                                @else
                                                    Non Medis
                                                @endif
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                {{ $asset['asset_master']['subcategory_name'] ?? '-' }}
                                            </td>
                                            <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                @php
                                                    $statusText = 'UNKNOWN';
                                                    $statusColor = 'bg-gray-500';

                                                    if (isset($asset['current_status'])) {
                                                        switch (strtolower($asset['current_status'])) {
                                                            case 'available':
                                                                $statusText = 'TERSEDIA';
                                                                $statusColor = 'bg-[#659B09]';
                                                                break;
                                                            case 'check out':
                                                                $statusText = 'DIPINJAM';
                                                                $statusColor = 'bg-[#F59E0B]';
                                                                break;
                                                            case 'lost':
                                                                $statusText = 'HILANG';
                                                                $statusColor = 'bg-[#EF4444]';
                                                                break;
                                                            case 'dispose':
                                                                $statusText = 'DIHAPUSKAN';
                                                                $statusColor = 'bg-[#ACC3EF]';
                                                                break;
                                                            case 'under repair':
                                                                $statusText = 'PERBAIKAN';
                                                                $statusColor = 'bg-[#25B1FF]';
                                                                break;
                                                            default:
                                                                $statusText = strtoupper($asset['current_status']);
                                                        }
                                                    }
                                                @endphp
                                                <span
                                                    class="px-2 py-1 rounded-md text-xs text-white {{ $statusColor }}">{{ $statusText }}</span>
                                            </td>
                                            <td class="p-3 border-t border-[#EEF1F4] text-center">
                                                <div class="flex justify-center items-center space-x-2">
                                                    <a href="{{ route('asset-details', $asset['asset_id']) }}"
                                                        class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                                        title="Lihat Detail Aset">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    @if(hasPermission('asset:edit'))
                                                        <button
                                                            class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-asset-btn"
                                                            data-id="{{ $asset['asset_id'] ?? '' }}"
                                                            data-name="{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '' }}"
                                                            data-serial-number="{{ $asset['serial_number'] ?? '' }}"
                                                            data-purchase-date="{{ $asset['purchase_date'] ?? '' }}"
                                                            data-purchase-cost="{{ $asset['purchase_cost'] ?? '' }}"
                                                            data-warranty-end-date="{{ $asset['warranty_end_date'] ?? '' }}"
                                                            data-current-status="{{ $asset['current_status'] ?? '' }}"
                                                            data-condition="{{ $asset['condition'] ?? '' }}"
                                                            data-room-id="{{ $asset['room_id'] ?? '' }}"
                                                            data-is-depreciable="{{ isset($asset['asset_master']) && isset($asset['asset_master']['is_depreciable']) && $asset['asset_master']['is_depreciable'] ? 'true' : 'false' }}"
                                                            data-image-path="{{ $asset['picture_path'] ?? '' }}"
                                                            title="Edit Aset">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if(hasPermission('asset:delete'))
                                                        <button
                                                            class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-asset-btn"
                                                            data-id="{{ $asset['asset_id'] ?? '' }}"
                                                            data-name="{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '' }}"
                                                            title="Hapus Aset">
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
                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada aset
                                            yang ditemukan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for Assets -->
                    @if(isset($assets_pagination) && $assets_pagination)
                        <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($assets_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changeAssetPage({{ ($assets_pagination['current_page'] ?? 1) - 1 }})" {{ ($assets_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Sebelumnya
                                </button>

                                <div class="flex gap-2">
                                    @php
                                        $currentPage = $assets_pagination['current_page'] ?? 1;
                                        $lastPage = $assets_pagination['last_page'] ?? 1;
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

                                <button
                                    class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($assets_pagination['current_page'] ?? 1) >= ($assets_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    onclick="changeAssetPage({{ ($assets_pagination['current_page'] ?? 1) + 1 }})" {{ ($assets_pagination['current_page'] ?? 1) >= ($assets_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
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
                                    @if(isset($assets_pagination) && is_array($assets_pagination))
                                        @php
                                            $currentPage = $assets_pagination['current_page'] ?? 1;
                                            $perPage = $assets_pagination['per_page'] ?? 10;
                                            $total = $assets_pagination['total'] ?? count($assets ?? []);
                                            $from = ($currentPage - 1) * $perPage + 1;
                                            $to = min($currentPage * $perPage, $total);
                                        @endphp
                                        Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                    @else
                                        Menampilkan 1 sampai {{ count($assets ?? []) }} dari {{ count($assets ?? []) }} data
                                    @endif
                                </span>
                                <select id="assetPerPageSelect"
                                    class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                    onchange="changeAssetPerPage(this.value)">
                                    <option value="10" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                    <option value="25" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                    <option value="50" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Asset Modal -->
    @if(hasPermission('asset:create'))
        <div id="addAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-4 border-b">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH ASET</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Add Asset Form -->
                        <form action="{{ route('assets.store') }}" method="POST" id="addAssetForm" data-no-loading novalidate>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Asset Information Section -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#213268] mb-4">Informasi Aset</h3>

                                        <!-- Master Asset selection -->
                                        <div class="mb-5">
                                            <label for="asset_master_search"
                                                class="block text-base font-semibold text-[#666666] mb-2">Master Aset <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="asset_master_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari master aset..." autocomplete="off" required>
                                                <input type="hidden" name="asset_master_id" id="selected_asset_master_id"
                                                    required>
                                                <input type="hidden" id="selected_is_depreciable" value="false">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Master aset harus
                                                    dipilih</div>

                                                <!-- Dropdown -->
                                                <div id="asset_master_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="asset_master_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat master aset...</span>
                                                    </div>
                                                    <ul id="asset_master_list" class="py-1"></ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Serial Number -->
                                        <div class="mb-5">
                                            <label for="serial_number"
                                                class="block text-base font-semibold text-[#666666] mb-2">Nomor Seri</label>
                                            <input type="text" name="serial_number" id="serial_number"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Masukkan nomor seri">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor seri harus diisi
                                            </div>
                                        </div>

                                        <!-- Purchase Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div>
                                                <label for="purchase_date"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pembelian</label>
                                                <input type="date" name="purchase_date" id="purchase_date"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pembelian
                                                    harus diisi</div>
                                            </div>
                                            <div>
                                                <label for="purchase_cost"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Biaya
                                                    Pembelian</label>
                                                <input type="number" name="purchase_cost" id="purchase_cost" step="0.01"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pembelian
                                                    harus diisi</div>
                                            </div>
                                        </div>

                                        <!-- Warranty -->
                                        <div class="mb-5">
                                            <label for="warranty_end_date"
                                                class="block text-base font-semibold text-[#666666] mb-2">Tanggal Berakhir
                                                Garansi</label>
                                            <input type="date" name="warranty_end_date" id="warranty_end_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        </div>

                                        <!-- Building and Room Selection -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div>
                                                <label for="building_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Gedung <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="building_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari gedung..." autocomplete="off" required>
                                                    <input type="hidden" name="building_id" id="selected_building_id">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Gedung harus
                                                        dipilih</div>

                                                    <div id="building_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="building_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat gedung...</span>
                                                        </div>
                                                        <ul id="building_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="room_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Ruangan <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="room_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled
                                                        required>
                                                    <input type="hidden" name="room_id" id="selected_room_id" required>
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Ruangan harus
                                                        dipilih</div>

                                                    <div id="room_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="room_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat ruangan...</span>
                                                        </div>
                                                        <ul id="room_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Condition and Responsible User -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label for="condition"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Kondisi</label>
                                                <select name="condition" id="condition"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                    <option value="">Pilih Kondisi</option>
                                                    <option value="good">Baik</option>
                                                    <option value="slightly damage">Sedikit Rusak</option>
                                                    <option value="high damage">Sangat Rusak</option>
                                                </select>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Kondisi harus
                                                    dipilih</div>
                                            </div>
                                            <div>
                                                <label for="user_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Karyawan yang
                                                    Bertanggung Jawab</label>
                                                <div class="relative">
                                                    <input type="text" id="user_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                                    <input type="hidden" name="user_id" id="selected_user_id">

                                                    <div id="user_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="user_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat karyawan...</span>
                                                        </div>
                                                        <ul id="user_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Depreciation Fields Section -->
                                    <div id="depreciation_fields"
                                        class="space-y-5 border rounded-lg p-5 border-dashed border-gray-300 hidden">
                                        <h3 class="text-lg font-semibold text-[#213268] mb-3">Informasi Penyusutan</h3>

                                        <div class="mb-4">
                                            <label for="depreciation_method"
                                                class="block text-base font-semibold text-[#666666] mb-2">Metode Penyusutan
                                                <span class="text-red-500">*</span></label>
                                            <select name="depreciation_method" id="depreciation_method"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                disabled>
                                                <option value="">Pilih Metode</option>
                                                <option value="Straight Line">Garis Lurus</option>
                                                <option value="Declining Balance">Saldo Menurun</option>
                                                <option value="Double Declining Balance">Saldo Menurun Ganda</option>
                                                <option value="150% Declining Balance">Saldo Menurun 150%</option>
                                                <option value="Sum of the Year's Digits">Jumlah Tahun Angka (SYD)</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus
                                                dipilih</div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                            <div>
                                                <label for="acquisition_cost"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Biaya Akusisi
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" name="acquisition_cost" id="acquisition_cost" step="0.01"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00" disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya akusisi harus
                                                    diisi</div>
                                            </div>
                                            <div>
                                                <label for="salvage_value"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Nilai Sisa <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number" name="salvage_value" id="salvage_value" step="0.01"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00" disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus
                                                    diisi</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label for="asset_life_months"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Usia Aset (bulan)
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" name="asset_life_months" id="asset_life_months"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Usia aset harus
                                                    diisi</div>
                                            </div>
                                            <div>
                                                <label for="date_acquired"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Tanggal Pengadaan
                                                    <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_acquired" id="date_acquired"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pengadaan
                                                    harus diisi</div>
                                            </div>
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

    <!-- Edit Asset Modal -->
    @if(hasPermission('asset:edit'))
        <div id="editAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-4 border-b">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">UBAH ASET</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Edit Asset Form -->
                        <form id="editAssetForm" method="POST" data-no-loading novalidate>
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Asset Information Section -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#213268] mb-4">Informasi Aset</h3>

                                        <!-- Master Asset selection -->
                                        <div class="mb-5">
                                            <label for="edit_asset_master_search"
                                                class="block text-base font-semibold text-[#666666] mb-2">Master Aset <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="edit_asset_master_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari master aset..." autocomplete="off" required>
                                                <input type="hidden" name="asset_master_id" id="edit_selected_asset_master_id"
                                                    required>
                                                <input type="hidden" id="edit_selected_is_depreciable" value="false">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Master aset harus
                                                    dipilih</div>

                                                <!-- Dropdown -->
                                                <div id="edit_asset_master_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="edit_asset_master_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat master aset...</span>
                                                    </div>
                                                    <ul id="edit_asset_master_list" class="py-1"></ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Serial Number -->
                                        <div class="mb-5">
                                            <label for="edit_serial_number"
                                                class="block text-base font-semibold text-[#666666] mb-2">Nomor Seri</label>
                                            <input type="text" name="serial_number" id="edit_serial_number"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Masukkan nomor seri">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor seri harus diisi
                                            </div>
                                        </div>

                                        <!-- Purchase Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div>
                                                <label for="edit_purchase_date"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pembelian</label>
                                                <input type="date" name="purchase_date" id="edit_purchase_date"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pembelian
                                                    harus diisi</div>
                                            </div>
                                            <div>
                                                <label for="edit_purchase_cost"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Biaya
                                                    Pembelian</label>
                                                <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pembelian
                                                    harus diisi</div>
                                            </div>
                                        </div>

                                        <!-- Warranty -->
                                        <div class="mb-5">
                                            <label for="edit_warranty_end_date"
                                                class="block text-base font-semibold text-[#666666] mb-2">Tanggal Berakhir
                                                Garansi</label>
                                            <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        </div>

                                        <!-- Building and Room Selection -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div>
                                                <label for="edit_building_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Gedung <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_building_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari gedung..." autocomplete="off" required>
                                                    <input type="hidden" name="building_id" id="edit_selected_building_id">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Gedung harus
                                                        dipilih</div>

                                                    <div id="edit_building_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_building_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat gedung...</span>
                                                        </div>
                                                        <ul id="edit_building_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="edit_room_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Ruangan <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_room_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled
                                                        required>
                                                    <input type="hidden" name="room_id" id="edit_selected_room_id" required>
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Ruangan harus
                                                        dipilih</div>

                                                    <div id="edit_room_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_room_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat ruangan...</span>
                                                        </div>
                                                        <ul id="edit_room_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Condition and Responsible User -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label for="edit_condition"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Kondisi</label>
                                                <select name="condition" id="edit_condition"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                    <option value="">Pilih Kondisi</option>
                                                    <option value="good">Baik</option>
                                                    <option value="slightly damage">Sedikit Rusak</option>
                                                    <option value="high damage">Sangat Rusak</option>
                                                </select>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Kondisi harus
                                                    dipilih</div>
                                            </div>
                                            <div>
                                                <label for="edit_user_search"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Karyawan yang
                                                    Bertanggung Jawab</label>
                                                <div class="relative">
                                                    <input type="text" id="edit_user_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                                    <input type="hidden" name="user_id" id="edit_selected_user_id">

                                                    <div id="edit_user_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_user_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat karyawan...</span>
                                                        </div>
                                                        <ul id="edit_user_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Depreciation Fields Section -->
                                    <div id="edit_depreciation_fields"
                                        class="space-y-5 border rounded-lg p-5 border-dashed border-gray-300 hidden">
                                        <h3 class="text-lg font-semibold text-[#213268] mb-3">Informasi Penyusutan</h3>

                                        <div class="mb-4">
                                            <label for="edit_depreciation_method"
                                                class="block text-base font-semibold text-[#666666] mb-2">Metode Penyusutan
                                                <span class="text-red-500">*</span></label>
                                            <select name="depreciation_method" id="edit_depreciation_method"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                disabled>
                                                <option value="">Pilih Metode</option>
                                                <option value="Straight Line">Garis Lurus</option>
                                                <option value="Declining Balance">Saldo Menurun</option>
                                                <option value="Double Declining Balance">Saldo Menurun Ganda</option>
                                                <option value="150% Declining Balance">Saldo Menurun 150%</option>
                                                <option value="Sum of the Year's Digits">Jumlah Tahun Angka (SYD)</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus
                                                dipilih</div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                            <div>
                                                <label for="edit_acquisition_cost"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Biaya Akusisi
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" name="acquisition_cost"
                                                    id="edit_acquisition_cost"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00" disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya akusisi harus
                                                    diisi</div>
                                            </div>
                                            <div>
                                                <label for="edit_salvage_value"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Nilai Sisa <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" name="salvage_value" id="edit_salvage_value"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00" disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus
                                                    diisi</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label for="edit_asset_life_months"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Usia Aset (bulan)
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Usia aset harus
                                                    diisi</div>
                                            </div>
                                            <div>
                                                <label for="edit_date_acquired"
                                                    class="block text-base font-semibold text-[#666666] mb-2">Tanggal Pengadaan
                                                    <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_acquired" id="edit_date_acquired"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    disabled>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pengadaan
                                                    harus diisi</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
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

    <!-- Delete Asset Modal -->
    @if(hasPermission('asset:delete'))
        <div id="deleteAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS ASET</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="deleteAssetForm" method="POST" data-no-loading>
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
                                            ini? Aksi ini tidak dapat dibatalkan.</p>
                                        <p id="deleteAssetName" class="text-base font-semibold text-center mt-2"></p>
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
            // Auto-hide the success notification after 5 seconds
            setTimeout(function () {
                const notification = document.getElementById('successNotification');
                if (notification) {
                    notification.style.transition = "opacity 1s ease";
                    notification.style.opacity = 0;
                    setTimeout(function () {
                        notification.remove();
                    }, 1000);
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
            // Auto-hide the error notification after 5 seconds
            setTimeout(function () {
                const notification = document.getElementById('errorNotification');
                if (notification) {
                    notification.style.transition = "opacity 1s ease";
                    notification.style.opacity = 0;
                    setTimeout(function () {
                        notification.remove();
                    }, 1000);
                }
            }, 5000);
        </script>
    @endif

    <!-- Print QR Modal -->
    <div id="printQRModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="printQRModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">CETAK QR CODE</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="printQRForm" action="{{ route('assets.qr.print-direct') }}" method="post" data-no-loading
                        target="_blank">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4">
                                <input type="hidden" name="asset_ids" id="printQRAssetIds" value="">

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Ukuran Label</label>
                                    <select name="qr_size" id="qr_size"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="80">80 x 50 mm (Label Stiker)</option>
                                        <option value="100">100 x 50 mm (Label Stiker)</option>
                                        <option value="60">60 x 40 mm (Label Kecil)</option>
                                    </select>
                                </div>

                                <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-800 mt-2">
                                    <p class="font-medium">Tips Pencetakan Label:</p>
                                    <ul class="list-disc pl-5 mt-1 text-xs space-y-1">
                                        <li>Pastikan label stiker terpasang dengan benar di printer</li>
                                        <li>Sesuaikan ukuran label dengan media stiker yang digunakan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 p-6 pt-0">
                            <button type="button" id="printPdfBtn"
                                class="w-1/2 h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                Download PDF
                            </button>
                            <button type="submit"
                                class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Cetak Label
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Asset Modal -->
    @if(hasPermission('asset:import'))
        <div id="importAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="importAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPOR ASET</h2>
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
                                        <p class="font-medium text-blue-600 mb-2">Instruksi Pengimporan:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Gunakan format Excel untuk mengimpor</li>
                                            <li>Kolom yang diperlukan: Asset Master ID, Nomor Seri, ID Ruangan, dll.</li>
                                            <li>Maksimal 100 catatan per impor</li>
                                            <li>Jenis file yang didukung: .xlsx, .xls, .csv</li>
                                        </ul>
                                        <div class="mt-3 flex justify-end">
                                            <a href="{{ asset('docs/ImportAssetTemplate.xlsx') }}" download
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
                                                <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span
                                                        class="text-[#213268] font-semibold">jelajahi file</span></p>
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
                                        <span class="text-sm text-gray-500" id="preview-count">0 item ditemukan</span>
                                    </div>

                                    <!-- Preview Table -->
                                    <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                        <table class="w-full">
                                            <thead class="sticky top-0 bg-[#213268] text-white">
                                                <tr>
                                                    <th class="p-3 text-left text-xs font-semibold">No</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Asset Master ID</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Serial Number</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Room ID</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Tanggal Pembelian</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Biaya Pembelian</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Tanggal Berakhir Garansi
                                                    </th>
                                                    <th class="p-3 text-left text-xs font-semibold">User ID</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Status Saat Ini</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Kondisi</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Metode Depresiasi</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Biaya Pengadaan</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Nilai Sisa</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Usia Aset (bulan)</th>
                                                    <th class="p-3 text-left text-xs font-semibold">Tanggal Pengadaan</th>
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
                                        <form action="{{ route('assets.import') }}" method="POST" id="import-form" class="w-2/3"
                                            data-no-loading enctype="multipart/form-data">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(!hasPermission('asset:create'))
                    const addButtons = document.querySelectorAll('#addAssetBtn');
                    addButtons.forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                    @if(!hasPermission('asset:import'))
                        const importButtons = document.querySelectorAll('#importAssetBtn');
                        importButtons.forEach(btn => {
                            if (btn) btn.style.display = 'none';
                        });
                    @endif

                    @if(!hasPermission('asset:export'))
                        const exportButtons = document.querySelectorAll('#exportBtn');
                        exportButtons.forEach(btn => {
                            if (btn) btn.style.display = 'none';
                        });
                    @endif

                    @if(!hasPermission('asset:edit'))
                        const editButtons = document.querySelectorAll('.edit-asset-btn');
                        editButtons.forEach(btn => {
                            if (btn) btn.style.display = 'none';
                        });
                    @endif

                    @if(!hasPermission('asset:delete'))
                        const deleteButtons = document.querySelectorAll('.delete-asset-btn');
                        deleteButtons.forEach(btn => {
                            if (btn) btn.style.display = 'none';
                        });
                    @endif

                initAssetMasterListeners();
                initSearchComponents();
                initEventHandlers();
                checkUrlParams();

                const purchaseCostField = document.getElementById('purchase_cost');
                const acquisitionCostField = document.getElementById('acquisition_cost');
                const depreciationFields = document.getElementById('depreciation_fields');

                if (purchaseCostField && acquisitionCostField && depreciationFields) {
                    purchaseCostField.addEventListener('input', function () {
                        if (!depreciationFields.classList.contains('hidden')) {
                            acquisitionCostField.value = this.value;
                        }
                    });
                }

                const editPurchaseCostField = document.getElementById('edit_purchase_cost');
                const editAcquisitionCostField = document.getElementById('edit_acquisition_cost');
                const editDepreciationFields = document.getElementById('edit_depreciation_fields');

                if (editPurchaseCostField && editAcquisitionCostField && editDepreciationFields) {
                    editPurchaseCostField.addEventListener('input', function () {
                        if (!editDepreciationFields.classList.contains('hidden')) {
                            editAcquisitionCostField.value = this.value;
                        }
                    });
                }

                initUserSearch(
                    document.getElementById('user_search'),
                    document.getElementById('user_dropdown'),
                    document.getElementById('user_list'),
                    document.getElementById('user_loading'),
                    document.getElementById('selected_user_id')
                );

                initAssetMasterSearch(
                    document.getElementById('asset_master_search'),
                    document.getElementById('asset_master_dropdown'),
                    document.getElementById('asset_master_list'),
                    document.getElementById('asset_master_loading'),
                    document.getElementById('selected_asset_master_id'),
                    document.getElementById('selected_is_depreciable'),
                    document.getElementById('depreciation_fields')
                );

                document.getElementById('addAssetForm')?.addEventListener('submit', function (event) {
                    const assetMasterSearch = document.getElementById('asset_master_search');
                    const selectedAssetMasterId = document.getElementById('selected_asset_master_id');
                    const buildingSearch = document.getElementById('building_search');
                    const selectedBuildingId = document.getElementById('selected_building_id');
                    const roomSearch = document.getElementById('room_search');
                    const selectedRoomId = document.getElementById('selected_room_id');
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const depreciationFields = document.getElementById('depreciation_fields');
                    const isDepreciable = !depreciationFields.classList.contains('hidden');
                    const isAssetMasterValid = validateField(assetMasterSearch, selectedAssetMasterId.value ? true : false);
                    const isBuildingValid = validateField(buildingSearch, selectedBuildingId.value ? true : false);
                    const isRoomValid = validateField(roomSearch, selectedRoomId.value ? true : false);

                    let isValid = isAssetMasterValid && isBuildingValid && isRoomValid;

                    if (isDepreciable) {
                        const depreciation_method = document.getElementById('depreciation_method');
                        const acquisition_cost = document.getElementById('acquisition_cost');
                        const salvage_value = document.getElementById('salvage_value');
                        const asset_life_months = document.getElementById('asset_life_months');
                        const date_acquired = document.getElementById('date_acquired');
                        const isDepreciationMethodValid = validateField(depreciation_method);
                        const isAcquisitionCostValid = validateField(acquisition_cost);
                        const isSalvageValueValid = validateField(salvage_value);
                        const isAssetLifeMonthsValid = validateField(asset_life_months);
                        const isDateAcquiredValid = validateField(date_acquired);

                        isValid = isValid && isDepreciationMethodValid && isAcquisitionCostValid &&
                            isSalvageValueValid && isAssetLifeMonthsValid && isDateAcquiredValid;
                    }

                    if (!isValid) {
                        event.preventDefault();
                        showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                        return;
                    }

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menyimpan...</span></div>';

                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });

                document.getElementById('editAssetForm')?.addEventListener('submit', function (event) {
                    const assetMasterSearch = document.getElementById('edit_asset_master_search');
                    const selectedAssetMasterId = document.getElementById('edit_selected_asset_master_id');
                    const roomSearch = document.getElementById('edit_room_search');
                    const selectedRoomId = document.getElementById('edit_selected_room_id');
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const depreciationFields = document.getElementById('edit_depreciation_fields');
                    const isDepreciable = !depreciationFields.classList.contains('hidden');
                    const isAssetMasterValid = validateField(assetMasterSearch, selectedAssetMasterId.value ? true : false);
                    const isRoomValid = validateField(roomSearch, selectedRoomId.value ? true : false);

                    let isValid = isAssetMasterValid && isRoomValid;

                    if (isDepreciable) {
                        const depreciation_method = document.getElementById('edit_depreciation_method');
                        const acquisition_cost = document.getElementById('edit_acquisition_cost');
                        const salvage_value = document.getElementById('edit_salvage_value');
                        const asset_life_months = document.getElementById('edit_asset_life_months');
                        const date_acquired = document.getElementById('edit_date_acquired');
                        const isDepreciationMethodValid = validateField(depreciation_method);
                        const isAcquisitionCostValid = validateField(acquisition_cost);
                        const isSalvageValueValid = validateField(salvage_value);
                        const isAssetLifeMonthsValid = validateField(asset_life_months);
                        const isDateAcquiredValid = validateField(date_acquired);

                        isValid = isValid && isDepreciationMethodValid && isAcquisitionCostValid &&
                            isSalvageValueValid && isAssetLifeMonthsValid && isDateAcquiredValid;
                    }

                    if (!isValid) {
                        event.preventDefault();
                        showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                        return;
                    }

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Memperbarui...</span></div>';

                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });

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
                        if (errorElement) errorElement.classList.remove('hidden');
                    } else {
                        field.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                    }

                    return isValid;
                }

                document.getElementById('asset_master_search')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('building_search')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('serial_number')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('purchase_date')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('purchase_cost')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('room_search')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('condition')?.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_asset_master_search')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_serial_number')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_purchase_date')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_purchase_cost')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_room_search')?.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                document.getElementById('edit_condition')?.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                function showToast(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.id = type + 'Notification' + Date.now();
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

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
                        notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
                        notification.innerHTML = `
                            <div class="flex items-start">
                                <div class="py-1">
                                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold">Error!</p>
                                    <div>${message}</div>
                                </div>
                                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                            </div>
                        `;
                    }

                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => notification.remove(), 500);
                    }, 5000);
                }

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

                function debounce(func, wait, immediate) {
                    let timeout;
                    return function () {
                        const context = this, args = arguments;
                        const later = function () {
                            timeout = null;
                            if (!immediate) func.apply(context, args);
                        };
                        const callNow = immediate && !timeout;
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                        if (callNow) func.apply(context, args);
                    };
                }

                function toggleDepreciationFields(depreciationFields, isDepreciable) {
                    if (!depreciationFields) return;

                    const inputs = depreciationFields.querySelectorAll('input, select');

                    if (isDepreciable) {
                        depreciationFields.classList.remove('hidden');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.required = true;

                            const label = input.closest('.space-y-2')?.querySelector('label');
                            if (label) {
                                if (!label.innerHTML.includes('<span class="text-red-500">*</span>')) {
                                    label.innerHTML += ' <span class="text-red-500">*</span>';
                                }
                            }

                            input.addEventListener('input', function () {
                                this.classList.remove('border-red-500');
                                const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                                if (errorElement) errorElement.classList.add('hidden');
                            });

                            if (input.tagName.toLowerCase() === 'select') {
                                input.addEventListener('change', function () {
                                    this.classList.remove('border-red-500');
                                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                                    if (errorElement) errorElement.classList.add('hidden');
                                });
                            }
                        });
                    } else {
                        depreciationFields.classList.add('hidden');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.required = false;

                            input.classList.remove('border-red-500');
                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');

                            const label = input.closest('.space-y-2')?.querySelector('label');
                            if (label) {
                                label.innerHTML = label.innerHTML.replace(' <span class="text-red-500">*</span>', '');
                            }
                        });
                    }
                }

                function setFieldValue(fieldId, value) {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.value = value || '';
                    }
                }

                function setSelectValue(selectId, value) {
                    const select = document.getElementById(selectId);
                    if (select && value) {
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value == value) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }

                window.openModal = function (modal, content) {
                    if (!modal || !content) return;

                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                        content.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                    }, 10);
                };

                window.closeModal = function (modal) {
                    if (!modal) return;

                    const content = modal.querySelector('.transform');
                    if (!content) return;

                    const modalId = modal.id;

                    if (modalId === 'addAssetModal') {
                        resetAddAssetForm();
                    } else if (modalId === 'editAssetModal') {
                        resetEditAssetForm();
                    } else if (modalId === 'deleteAssetModal') {
                        resetDeleteAssetForm();
                    } else if (modalId === 'printQRModal') {
                        resetPrintQRForm();
                    } else if (modalId === 'importAssetModal') {
                        resetImportAssetModal();
                    }

                    content.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                    content.classList.add('opacity-0', 'scale-95', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                };

                function resetAddAssetForm() {
                    const form = document.getElementById('addAssetForm');
                    if (!form) return;

                    form.reset();

                    const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                    hiddenInputs.forEach(input => {
                        input.value = '';
                    });

                    const allInputs = form.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])');
                    allInputs.forEach(input => {
                        input.value = '';
                        input.classList.remove('border-red-500');
                    });

                    const radioCheckboxInputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
                    radioCheckboxInputs.forEach(input => {
                        input.checked = input.defaultChecked;
                    });

                    const selects = form.querySelectorAll('select');
                    selects.forEach(select => {
                        if (select.options.length > 0) {
                            select.selectedIndex = 0;
                        }
                        select.classList.remove('border-red-500');
                    });

                    const errorMessages = form.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => {
                        msg.classList.add('hidden');
                    });

                    const dropdowns = form.querySelectorAll('[id$="_dropdown"]');
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });

                    const searchFields = form.querySelectorAll('[id$="_search"], [id$="_master_search"]');
                    searchFields.forEach(field => {
                        field.value = '';
                    });

                    const specialDisplays = form.querySelectorAll('[id$="_display"], [id$="_selected_display"]');
                    specialDisplays.forEach(display => {
                        display.classList.add('hidden');
                    });

                    const depreciationFields = form.querySelector('#depreciation_fields');
                    if (depreciationFields) {
                        depreciationFields.classList.add('hidden');
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.required = false;
                            if (input.tagName === 'INPUT') {
                                input.value = '';
                            } else if (input.tagName === 'SELECT' && input.options.length > 0) {
                                input.selectedIndex = 0;
                            }
                        });
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = 'Simpan';
                    }
                }

                function resetEditAssetForm() {
                    const form = document.getElementById('editAssetForm');
                    if (!form) return;

                    form.reset();

                    form.action = '';

                    const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                    hiddenInputs.forEach(input => {
                        input.value = '';
                    });

                    const allInputs = form.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])');
                    allInputs.forEach(input => {
                        input.value = '';
                        input.classList.remove('border-red-500');
                    });

                    const radioCheckboxInputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
                    radioCheckboxInputs.forEach(input => {
                        input.checked = input.defaultChecked;
                    });

                    const selects = form.querySelectorAll('select');
                    selects.forEach(select => {
                        if (select.options.length > 0) {
                            select.selectedIndex = 0;
                        }
                        select.classList.remove('border-red-500');
                    });

                    const errorMessages = form.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => {
                        msg.classList.add('hidden');
                    });

                    const dropdowns = form.querySelectorAll('[id$="_dropdown"]');
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });

                    const searchFields = form.querySelectorAll('[id$="_search"], [id$="_master_search"]');
                    searchFields.forEach(field => {
                        field.value = '';
                    });

                    const specialDisplays = form.querySelectorAll('[id$="_display"], [id$="_selected_display"]');
                    specialDisplays.forEach(display => {
                        display.classList.add('hidden');
                    });

                    const depreciationFields = form.querySelector('#edit_depreciation_fields');
                    if (depreciationFields) {
                        depreciationFields.classList.add('hidden');
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.required = false;
                            if (input.tagName === 'INPUT') {
                                input.value = '';
                            } else if (input.tagName === 'SELECT' && input.options.length > 0) {
                                input.selectedIndex = 0;
                            }
                        });
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = 'Perbarui';
                    }
                }

                function resetDeleteAssetForm() {
                    const form = document.getElementById('deleteAssetForm');
                    if (!form) return;

                    form.reset();

                    form.action = '';

                    document.getElementById('deleteAssetName').textContent = '';

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = 'Hapus';
                    }
                }

                function resetPrintQRForm() {
                    const form = document.getElementById('printQRForm');
                    if (!form) return;

                    form.reset();

                    document.getElementById('printQRAssetIds').value = '';

                    const printDirectRadio = document.getElementById('print_direct');
                    if (printDirectRadio) {
                        printDirectRadio.checked = true;
                    }

                    const quantityInput = document.getElementById('quantity');
                    if (quantityInput) {
                        quantityInput.value = '1';
                    }
                }

                function resetImportAssetModal() {
                    document.getElementById('import-step-1')?.classList.remove('hidden');
                    document.getElementById('import-step-2')?.classList.add('hidden');

                    const fileInput = document.getElementById('excel_file');
                    if (fileInput) fileInput.value = '';

                    const fileNameContainer = document.getElementById('excel-file-name');
                    if (fileNameContainer) fileNameContainer.classList.add('hidden');

                    const previewBtn = document.getElementById('preview-btn');
                    if (previewBtn) previewBtn.disabled = true;

                    document.getElementById('excel-error')?.classList.add('hidden');
                    document.getElementById('excel-loading')?.classList.add('hidden');
                    document.getElementById('preview-warnings')?.classList.add('hidden');

                    const previewTableBody = document.getElementById('preview-table-body');
                    if (previewTableBody) previewTableBody.innerHTML = '';

                    const warningList = document.getElementById('warning-list');
                    if (warningList) warningList.innerHTML = '';

                    document.getElementById('excel_data')?.setAttribute('value', '');

                    const importBtn = document.getElementById('import-btn');
                    if (importBtn) {
                        importBtn.disabled = false;
                        importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        importBtn.innerHTML = 'Impor Data';
                    }
                }

                window.setupWithData = function (assetId) {
                    const editModal = document.getElementById('editAssetModal');
                    const editModalContent = document.getElementById('editAssetModalContent');

                    const form = document.getElementById('editAssetForm');
                    if (form) {
                        form.reset();
                        form.action = `{{ url('assets') }}/${assetId}`;
                    }

                    if (editModal && editModalContent) {
                        openModal(editModal, editModalContent);
                    }

                    fetch(`{{ url('assets') }}/${assetId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (!result.success) {
                                console.error('Error fetching asset data:', result.message);
                                alert('Gagal memuat data aset: ' + (result.message || 'Galat tidak diketahui'));
                                return;
                            }

                            const asset = result.data;

                            setFieldValue('edit_serial_number', asset.serial_number);
                            setFieldValue('edit_purchase_date', asset.purchase_date);
                            setFieldValue('edit_purchase_cost', asset.purchase_cost);
                            setFieldValue('edit_warranty_end_date', asset.warranty_end_date);

                            const assetMasterId = asset.asset_master_id || (asset.asset_master && asset.asset_master.asset_master_id);
                            if (assetMasterId) {
                                document.getElementById('edit_selected_asset_master_id').value = assetMasterId;

                                const assetMasterName = asset.asset_master && asset.asset_master.asset_name
                                    ? asset.asset_master.asset_name
                                    : 'Asset Master ID: ' + assetMasterId;

                                document.getElementById('edit_asset_master_search').value = assetMasterName;

                                const isDepreciable = asset.asset_master && asset.asset_master.is_depreciable === true;
                                document.getElementById('edit_selected_is_depreciable').value = isDepreciable ? 'true' : 'false';

                                const depreciationFields = document.getElementById('edit_depreciation_fields');
                                if (depreciationFields) {
                                    toggleDepreciationFields(depreciationFields, isDepreciable);
                                }
                            }

                            let buildingId = '';
                            let buildingName = '';

                            if (asset.room && asset.room.building) {
                                buildingId = asset.room.building.building_id;
                                buildingName = asset.room.building.building_name;
                            } else if (asset.building_id) {
                                buildingId = asset.building_id;
                                buildingName = asset.building_name || 'Gedung ID: ' + buildingId;
                            } else if (asset.room && asset.room.building_id) {
                                buildingId = asset.room.building_id;
                                buildingName = asset.room.building_name || 'Gedung ID: ' + buildingId;
                            } else if (asset.building_name) {
                                buildingName = asset.building_name;
                            }

                            if (!buildingName && typeof asset.building_name === 'string' && asset.building_name.trim() !== '') {
                                buildingName = asset.building_name;
                            }

                            if (buildingId || buildingName) {
                                if (buildingId) {
                                    document.getElementById('edit_selected_building_id').value = buildingId;
                                }

                                document.getElementById('edit_building_search').value = buildingName;

                                const roomSearch = document.getElementById('edit_room_search');
                                if (roomSearch) {
                                    roomSearch.disabled = false;
                                    roomSearch.placeholder = "Cari ruangan...";
                                }

                                let roomId = '';
                                let roomName = '';

                                if (asset.room_id) {
                                    roomId = asset.room_id;
                                } else if (asset.room && asset.room.room_id) {
                                    roomId = asset.room.room_id;
                                }

                                if (asset.room && asset.room.room_name) {
                                    roomName = asset.room.room_name;
                                } else if (asset.room_name) {
                                    roomName = asset.room_name;
                                }

                                if (typeof asset.room_name === 'string' && asset.room_name.trim() !== '') {
                                    roomName = asset.room_name;
                                }

                                if (roomId) {
                                    document.getElementById('edit_selected_room_id').value = roomId;
                                }

                                if (roomName) {
                                    document.getElementById('edit_room_search').value = roomName;
                                } else if (roomId) {
                                    document.getElementById('edit_room_search').value = 'Ruangan ID: ' + roomId;

                                    if (buildingId) {
                                        loadRoomsForBuilding(
                                            '',
                                            buildingId,
                                            document.getElementById('edit_room_list'),
                                            document.getElementById('edit_room_loading'),
                                            document.getElementById('edit_selected_room_id'),
                                            document.getElementById('edit_room_search'),
                                            document.getElementById('edit_room_dropdown')
                                        );
                                    }
                                }

                                if (!roomId && !roomName && buildingId) {
                                    loadRoomsForBuilding(
                                        '',
                                        buildingId,
                                        document.getElementById('edit_room_list'),
                                        document.getElementById('edit_room_loading'),
                                        document.getElementById('edit_selected_room_id'),
                                        document.getElementById('edit_room_search'),
                                        document.getElementById('edit_room_dropdown')
                                    );
                                }
                            }

                            setSelectValue('edit_condition', asset.condition || 'good');

                            if (asset.user_id) {
                                document.getElementById('edit_selected_user_id').value = asset.user_id;

                                let userDisplay = `User ID: ${asset.user_id}`;

                                if (asset.user) {
                                    if (asset.user.employee_number) {
                                        userDisplay = asset.user.employee_number;
                                        if (asset.user.name) userDisplay += ` - ${asset.user.name}`;
                                    } else if (asset.user.name) {
                                        userDisplay = asset.user.name;
                                    }
                                } else {
                                    const user = window.usersData?.find(u => u.user_id == asset.user_id);
                                    if (user) {
                                        if (user.employee_number) {
                                            userDisplay = user.employee_number;
                                            if (user.name) userDisplay += ` - ${user.name}`;
                                        } else if (user.name) {
                                            userDisplay = user.name;
                                        }
                                    }
                                }

                                document.getElementById('edit_user_search').value = userDisplay;
                            } else if (asset.employee_number) {
                                document.getElementById('edit_user_search').value = asset.employee_number;
                            }

                            const depreciationFields = document.getElementById('edit_depreciation_fields');
                            if (depreciationFields) {
                                const hasDepreciationData =
                                    asset.depreciation_method ||
                                    asset.acquisition_cost ||
                                    asset.salvage_value ||
                                    asset.asset_life_months ||
                                    asset.date_acquired ||
                                    (asset.depreciation && Object.keys(asset.depreciation).length > 0);

                                const isDepreciable = asset.asset_master && asset.asset_master.is_depreciable === true;

                                if (hasDepreciationData || isDepreciable) {
                                    toggleDepreciationFields(depreciationFields, true);

                                    const depData = asset.depreciation || asset;
                                    setFieldValue('edit_acquisition_cost', depData.acquisition_cost || '');
                                    setFieldValue('edit_salvage_value', depData.salvage_value || '');
                                    setFieldValue('edit_asset_life_months', depData.asset_life_months || '');
                                    setFieldValue('edit_date_acquired', depData.date_acquired || '');

                                    const depMethodSelect = document.getElementById('edit_depreciation_method');
                                    const depreciationMethod = depData.depreciation_method || '';

                                    if (depMethodSelect && depreciationMethod) {
                                        let found = false;
                                        for (let i = 0; i < depMethodSelect.options.length; i++) {
                                            if (depMethodSelect.options[i].value === depreciationMethod) {
                                                depMethodSelect.selectedIndex = i;
                                                found = true;
                                                break;
                                            }
                                        }

                                        if (!found) {
                                            const methodLower = depreciationMethod.toLowerCase();
                                            for (let i = 0; i < depMethodSelect.options.length; i++) {
                                                const optionText = depMethodSelect.options[i].textContent.toLowerCase();
                                                if (optionText.includes(methodLower) || methodLower.includes(optionText)) {
                                                    depMethodSelect.selectedIndex = i;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    toggleDepreciationFields(depreciationFields, false);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching asset data:', error);
                        });
                };

                function initAssetMasterListeners() {
                    const addSelectedIsDepreciable = document.getElementById('selected_is_depreciable');
                    const addDepreciationFields = document.getElementById('depreciation_fields');

                    if (addSelectedIsDepreciable && addDepreciationFields) {
                        const observer = new MutationObserver(function (mutations) {
                            mutations.forEach(function (mutation) {
                                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                                    const isDepreciable = addSelectedIsDepreciable.value === 'true';
                                    toggleDepreciationFields(addDepreciationFields, isDepreciable);
                                }
                            });
                        });

                        const config = { attributes: true, attributeFilter: ['value'] };
                        observer.observe(addSelectedIsDepreciable, config);

                        addSelectedIsDepreciable.addEventListener('change', function () {
                            const isDepreciable = this.value === 'true';
                            toggleDepreciationFields(addDepreciationFields, isDepreciable);
                        });

                        const isInitiallyDepreciable = addSelectedIsDepreciable.value === 'true';
                        toggleDepreciationFields(addDepreciationFields, isInitiallyDepreciable);
                    }

                    const editAssetMasterSelect = document.getElementById('edit_asset_master_id');
                    const editDepreciationFields = document.getElementById('edit_depreciation_fields');

                    if (editAssetMasterSelect && editDepreciationFields) {
                        editAssetMasterSelect.addEventListener('change', function () {
                            const selectedOption = this.options[this.selectedIndex];
                            const isDepreciable = selectedOption.getAttribute('data-depreciable') === 'true';
                            toggleDepreciationFields(editDepreciationFields, isDepreciable);
                        });
                    }
                }

                function initEventHandlers() {
                    document.querySelectorAll('.edit-asset-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const assetId = this.getAttribute('data-id');
                            setupWithData(assetId);
                        });
                    });

                    document.querySelectorAll('.delete-asset-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const assetId = this.getAttribute('data-id');
                            const assetName = this.getAttribute('data-name');
                            const deleteModal = document.getElementById('deleteAssetModal');
                            const deleteContent = document.getElementById('deleteAssetModalContent');

                            if (deleteModal && deleteContent) {
                                document.getElementById('deleteAssetName').textContent = assetName;
                                const deleteForm = document.getElementById('deleteAssetForm');
                                if (deleteForm) {
                                    deleteForm.action = `{{ url('assets') }}/${assetId}`;
                                }
                                openModal(deleteModal, deleteContent);
                            }
                        });
                    });

                    const addAssetBtn = document.getElementById('addAssetBtn');
                    const addAssetModal = document.getElementById('addAssetModal');
                    const addAssetModalContent = document.getElementById('addAssetModalContent');

                    if (addAssetBtn && addAssetModal && addAssetModalContent) {
                        addAssetBtn.addEventListener('click', function () {
                            openModal(addAssetModal, addAssetModalContent);
                        });
                    }

                    document.getElementById('printQRBtn')?.addEventListener('click', function () {
                        const checkedAssets = document.querySelectorAll('.asset-checkbox:checked');
                        const assetIds = Array.from(checkedAssets).map(checkbox => checkbox.getAttribute('data-asset-id'));

                        if (assetIds.length === 0) {
                            showToast('Silakan pilih setidaknya satu aset untuk mencetak kode QR.', 'error');
                            return;
                        }

                        document.getElementById('printQRAssetIds').value = assetIds.join(',');

                        const printQRModal = document.getElementById('printQRModal');
                        const printQRModalContent = document.getElementById('printQRModalContent');
                        openModal(printQRModal, printQRModalContent);
                    });

                    document.getElementById('select-all-assets')?.addEventListener('change', function () {
                        const checkboxes = document.querySelectorAll('.asset-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                    });

                    document.querySelectorAll('.close-modal').forEach(button => {
                        button.addEventListener('click', function () {
                            const modal = this.closest('[id$="Modal"]');
                            closeModal(modal);
                        });
                    });

                    document.querySelectorAll('.fixed.inset-0.bg-black.bg-opacity-50').forEach(overlay => {
                        overlay.addEventListener('click', function (e) {
                            if (e.target === this) {
                                const modal = this.parentElement;
                                if (modal) {
                                    closeModal(modal);
                                }
                            }
                        });
                    });

                    const editForm = document.getElementById('editAssetForm');
                    if (editForm) {
                        editForm.addEventListener('submit', function (e) {
                            e.preventDefault();

                            const formData = new FormData(this);
                            const assetId = this.action.split('/').pop();

                            if (!formData.has('current_status')) {
                                formData.append('current_status', 'available');
                            }

                            const depreciationFields = document.getElementById('edit_depreciation_fields');
                            if (depreciationFields && !depreciationFields.classList.contains('hidden')) {
                                const fieldsToCheck = [
                                    { id: 'edit_depreciation_method', name: 'depreciation_method' },
                                    { id: 'edit_acquisition_cost', name: 'acquisition_cost' },
                                    { id: 'edit_salvage_value', name: 'salvage_value' },
                                    { id: 'edit_asset_life_months', name: 'asset_life_months' },
                                    { id: 'edit_date_acquired', name: 'date_acquired' }
                                ];

                                fieldsToCheck.forEach(field => {
                                    const element = document.getElementById(field.id);
                                    if (element && element.value && !formData.has(field.name)) {
                                        formData.append(field.name, element.value);
                                    }
                                });
                            }

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `{{ url('assets') }}/${assetId}`;
                            form.style.display = 'none';

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);

                            for (const [key, value] of formData.entries()) {
                                if (key === '_method') continue;

                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = value;
                                form.appendChild(input);
                            }

                            document.body.appendChild(form);
                            form.submit();
                        });
                    }

                    const printQRForm = document.getElementById('printQRForm');
                    if (printQRForm) {
                        printQRForm.addEventListener('submit', function (e) {
                            e.preventDefault();

                            const formData = new FormData(this);
                            const action = this.action;

                            const modal = document.getElementById('printQRModal');
                            if (modal) {
                                closeModal(modal);
                            }

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = action;
                            form.target = '_blank';
                            form.style.display = 'none';

                            for (const [key, value] of formData.entries()) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = value;
                                form.appendChild(input);
                            }

                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);

                            document.body.appendChild(form);
                            form.submit();
                        });
                    }
                }

                function initRoomSearch(
                    searchInput,
                    dropdown,
                    roomList,
                    loadingIndicator,
                    selectedRoomId
                ) {
                    if (!searchInput || !dropdown || !roomList) return;

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (roomList.children.length === 0) {
                            loadRooms('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        loadRooms(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadRooms(searchTerm) {
                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        roomList.innerHTML = '';

                        try {
                            const response = await fetch(`{{ url('/rooms') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Failed to fetch rooms from server');
                            }

                            const data = await response.json();
                            const rooms = data.rooms || [];

                            roomList.innerHTML = '';

                            if (rooms.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Ruangan tidak ditemukan';
                                roomList.appendChild(noResults);
                            } else {
                                rooms.forEach(room => {
                                    const li = document.createElement('li');
                                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                    const buildingName = room.building ? room.building.building_name :
                                        (room.building_name || 'Gedung Tidak Diketahui');

                                    const roomDisplay = `${room.room_name} (${buildingName})`;

                                    li.textContent = roomDisplay;
                                    li.setAttribute('data-id', room.room_id);
                                    li.setAttribute('data-name', roomDisplay);

                                    li.addEventListener('click', function () {
                                        selectedRoomId.value = this.getAttribute('data-id');

                                        searchInput.value = this.getAttribute('data-name');

                                        dropdown.classList.add('hidden');
                                    });

                                    roomList.appendChild(li);
                                });
                            }
                        } catch (error) {
                            console.error('Error loading rooms:', error);
                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';
                            errorItem.textContent = 'Galat memuat ruangan';
                            roomList.appendChild(errorItem);
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }
                }

                function initAssetMasterSearch(
                    searchInput,
                    dropdown,
                    assetMasterList,
                    loadingIndicator,
                    selectedAssetMasterId,
                    selectedIsDepreciable,
                    depreciationFields
                ) {
                    if (!searchInput || !dropdown || !assetMasterList) return;

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (assetMasterList.children.length === 0) {
                            loadAssetMasters('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        loadAssetMasters(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadAssetMasters(searchTerm) {
                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        assetMasterList.innerHTML = '';

                        try {
                            const response = await fetch(`{{ route('asset-master') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Failed to fetch asset masters');
                            }

                            const data = await response.json();
                            let assetMasters = data.masterAssets || [];

                            assetMasterList.innerHTML = '';

                            if (assetMasters.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Master aset tidak ditemukan';
                                assetMasterList.appendChild(noResults);
                            } else {
                                assetMasters.forEach(item => {
                                    const li = document.createElement('li');
                                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                    const assetMasterName = item.asset_name || 'Unknown';

                                    li.textContent = assetMasterName;
                                    li.setAttribute('data-id', item.asset_master_id);
                                    li.setAttribute('data-name', assetMasterName);

                                    const isDepreciable = item.is_depreciable === true;

                                    li.setAttribute('data-depreciable', isDepreciable);

                                    li.addEventListener('click', function () {
                                        selectedAssetMasterId.value = this.getAttribute('data-id');

                                        searchInput.value = this.getAttribute('data-name');

                                        const isDepreciable = this.getAttribute('data-depreciable') === 'true';
                                        selectedIsDepreciable.setAttribute('value', isDepreciable.toString());
                                        const event = new Event('change');
                                        selectedIsDepreciable.dispatchEvent(event);

                                        toggleDepreciationFields(depreciationFields, isDepreciable);

                                        dropdown.classList.add('hidden');
                                    });

                                    assetMasterList.appendChild(li);
                                });
                            }
                        } catch (error) {
                            console.error('Error loading asset masters:', error);
                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';
                            errorItem.textContent = 'Galat memuat master aset';
                            assetMasterList.appendChild(errorItem);
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }
                }

                function initUserSearch(
                    searchInput,
                    dropdown,
                    userList,
                    loadingIndicator,
                    selectedUserId
                ) {
                    if (!searchInput || !dropdown || !userList) {
                        return;
                    }

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (userList.children.length === 0) {
                            loadUsers('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        loadUsers(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);

                    async function loadUsers(searchTerm) {
                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        userList.innerHTML = '';

                        try {
                            const apiUrl = `{{ route('user') }}?search=${encodeURIComponent(searchTerm || '')}&status=active`;

                            const response = await fetch(apiUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error(`Failed to fetch users from server: ${response.status} ${response.statusText}`);
                            }

                            const data = await response.json();
                            const users = data.users || data.data || [];

                            userList.innerHTML = '';

                            if (users.length === 0) {
                                const noResults = document.createElement('li');
                                noResults.className = 'px-4 py-2 text-gray-500 italic';
                                noResults.textContent = 'Pengguna tidak ditemukan';
                                userList.appendChild(noResults);
                            } else {
                                users.forEach(user => {
                                    const li = document.createElement('li');
                                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                    let displayText = '';
                                    if (user.employee_number) {
                                        displayText = user.employee_number;
                                        if (user.name) {
                                            displayText += ` - ${user.name}`;
                                        }
                                    } else {
                                        displayText = user.name || `User ID: ${user.user_id}`;
                                    }

                                    li.textContent = displayText;
                                    li.setAttribute('data-id', user.user_id);
                                    li.setAttribute('data-employee-number', user.employee_number || '');
                                    li.setAttribute('data-name', displayText);

                                    li.addEventListener('click', function () {
                                        selectedUserId.value = this.getAttribute('data-id');

                                        const employeeNumber = this.getAttribute('data-employee-number');
                                        if (employeeNumber) {
                                            searchInput.value = employeeNumber;
                                        } else {
                                            searchInput.value = this.getAttribute('data-name');
                                        }

                                        dropdown.classList.add('hidden');
                                    });

                                    userList.appendChild(li);
                                });
                            }
                        } catch (error) {
                            console.error('Error loading users:', error);
                            const errorItem = document.createElement('li');
                            errorItem.className = 'px-4 py-2 text-red-500';
                            errorItem.textContent = `Galat memproses data pengguna: ${error.message}`;
                            userList.appendChild(errorItem);
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }
                }

                function initSearchComponents() {
                    initDropdown(
                        document.getElementById('edit_building_search'),
                        document.getElementById('edit_building_dropdown'),
                        document.getElementById('edit_building_list'),
                        function (searchTerm) {
                            loadBuildings(
                                searchTerm,
                                document.getElementById('edit_building_list'),
                                document.getElementById('edit_building_loading'),
                                document.getElementById('edit_selected_building_id'),
                                document.getElementById('edit_building_search'),
                                document.getElementById('edit_building_dropdown'),
                                document.getElementById('edit_room_search')
                            );
                        }
                    );

                    initDropdown(
                        document.getElementById('edit_room_search'),
                        document.getElementById('edit_room_dropdown'),
                        document.getElementById('edit_room_list'),
                        function (searchTerm) {
                            const buildingId = document.getElementById('edit_selected_building_id').value;
                            if (buildingId) {
                                loadRoomsForBuilding(
                                    searchTerm,
                                    buildingId,
                                    document.getElementById('edit_room_list'),
                                    document.getElementById('edit_room_loading'),
                                    document.getElementById('edit_selected_room_id'),
                                    document.getElementById('edit_room_search'),
                                    document.getElementById('edit_room_dropdown')
                                );
                            } else {
                                const roomList = document.getElementById('edit_room_list');
                                if (roomList) {
                                    roomList.innerHTML = '';
                                    roomList.appendChild(createDropdownItem('Pilih gedung terlebih dahulu', 'px-4 py-2 text-gray-500 italic'));
                                }
                            }
                        }
                    );

                    initDropdown(
                        document.getElementById('building_search'),
                        document.getElementById('building_dropdown'),
                        document.getElementById('building_list'),
                        function (searchTerm) {
                            loadBuildings(
                                searchTerm,
                                document.getElementById('building_list'),
                                document.getElementById('building_loading'),
                                document.getElementById('selected_building_id'),
                                document.getElementById('building_search'),
                                document.getElementById('building_dropdown'),
                                document.getElementById('room_search')
                            );
                        }
                    );

                    initDropdown(
                        document.getElementById('room_search'),
                        document.getElementById('room_dropdown'),
                        document.getElementById('room_list'),
                        function (searchTerm) {
                            const buildingId = document.getElementById('selected_building_id').value;
                            if (buildingId) {
                                loadRoomsForBuilding(
                                    searchTerm,
                                    buildingId,
                                    document.getElementById('room_list'),
                                    document.getElementById('room_loading'),
                                    document.getElementById('selected_room_id'),
                                    document.getElementById('room_search'),
                                    document.getElementById('room_dropdown')
                                );
                            } else {
                                const roomList = document.getElementById('room_list');
                                if (roomList) {
                                    roomList.innerHTML = '';
                                    roomList.appendChild(createDropdownItem('Pilih gedung terlebih dahulu', 'px-4 py-2 text-gray-500 italic'));
                                }
                            }
                        }
                    );

                    initAssetMasterSearch(
                        document.getElementById('asset_master_search'),
                        document.getElementById('asset_master_dropdown'),
                        document.getElementById('asset_master_list'),
                        document.getElementById('asset_master_loading'),
                        document.getElementById('selected_asset_master_id'),
                        document.getElementById('selected_is_depreciable'),
                        document.getElementById('depreciation_fields')
                    );

                    initAssetMasterSearch(
                        document.getElementById('edit_asset_master_search'),
                        document.getElementById('edit_asset_master_dropdown'),
                        document.getElementById('edit_asset_master_list'),
                        document.getElementById('edit_asset_master_loading'),
                        document.getElementById('edit_selected_asset_master_id'),
                        document.getElementById('edit_selected_is_depreciable'),
                        document.getElementById('edit_depreciation_fields')
                    );

                    if (document.getElementById('user_search')) {
                        initUserSearch(
                            document.getElementById('user_search'),
                            document.getElementById('user_dropdown'),
                            document.getElementById('user_list'),
                            document.getElementById('user_loading'),
                            document.getElementById('selected_user_id')
                        );
                    }

                    if (document.getElementById('edit_user_search')) {
                        initUserSearch(
                            document.getElementById('edit_user_search'),
                            document.getElementById('edit_user_dropdown'),
                            document.getElementById('edit_user_list'),
                            document.getElementById('edit_user_loading'),
                            document.getElementById('edit_selected_user_id')
                        );
                    }
                }

                function initDropdown(searchInput, dropdown, list, searchFunction) {
                    if (!searchInput || !dropdown || !list) return;

                    searchInput.addEventListener('focus', function () {
                        dropdown.classList.remove('hidden');
                        if (list.children.length === 0) {
                            searchFunction('');
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    const debouncedSearch = debounce(function (e) {
                        searchFunction(e.target.value);
                    }, 300);

                    searchInput.addEventListener('input', debouncedSearch);
                }

                function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                    const li = document.createElement('li');
                    li.className = className;
                    li.textContent = text;
                    return li;
                }

                async function loadBuildings(searchTerm, buildingList, loadingIndicator, selectedBuildingId, searchInput, dropdown, roomSearchInput) {
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    buildingList.innerHTML = '';

                    try {
                        const response = await fetch(`{{ route('buildings') }}?search=${encodeURIComponent(searchTerm || '')}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load buildings');
                        }

                        const data = await response.json();
                        const buildings = data.data || [];

                        if (buildings.length === 0) {
                            buildingList.appendChild(createDropdownItem('Tidak ada gedung yang ditemukan', 'px-4 py-2 text-gray-500 italic'));
                        } else {
                            buildings.forEach(building => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                li.textContent = building.building_name;
                                li.setAttribute('data-id', building.building_id);
                                li.setAttribute('data-name', building.building_name);

                                li.addEventListener('click', function () {
                                    selectedBuildingId.value = this.getAttribute('data-id');
                                    searchInput.value = this.getAttribute('data-name');

                                    if (roomSearchInput) {
                                        roomSearchInput.disabled = false;
                                        roomSearchInput.placeholder = "Cari ruangan...";

                                        const isEditModal = roomSearchInput.id === 'edit_room_search';

                                        const roomIdField = isEditModal ? 'edit_selected_room_id' : 'selected_room_id';
                                        document.getElementById(roomIdField).value = '';
                                        roomSearchInput.value = '';

                                        const roomLoadingId = isEditModal ? 'edit_room_loading' : 'room_loading';
                                        const roomLoadingIndicator = document.getElementById(roomLoadingId);
                                        if (roomLoadingIndicator) {
                                            roomLoadingIndicator.classList.remove('hidden');
                                        }

                                        const roomListId = isEditModal ? 'edit_room_list' : 'room_list';
                                        const roomDropdownId = isEditModal ? 'edit_room_dropdown' : 'room_dropdown';
                                        loadRoomsForBuilding(
                                            '',
                                            this.getAttribute('data-id'),
                                            document.getElementById(roomListId),
                                            document.getElementById(roomLoadingId),
                                            document.getElementById(roomIdField),
                                            roomSearchInput,
                                            document.getElementById(roomDropdownId)
                                        );

                                        document.getElementById(roomDropdownId).classList.remove('hidden');
                                    }

                                    dropdown.classList.add('hidden');
                                });

                                buildingList.appendChild(li);
                            });
                        }
                    } catch (error) {
                        console.error('Error loading buildings:', error);
                        buildingList.innerHTML = '';
                        buildingList.appendChild(createDropdownItem(`Error: ${error.message}`, 'px-4 py-2 text-red-500'));
                    } finally {
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    }
                }

                async function loadRoomsForBuilding(searchTerm, buildingId, roomList, loadingIndicator, selectedRoomId, searchInput, dropdown) {
                    if (!buildingId) {
                        searchInput.value = '';
                        searchInput.placeholder = 'Pilih gedung terlebih dahulu';
                        searchInput.disabled = true;
                        return;
                    }

                    searchInput.disabled = false;
                    searchInput.placeholder = "Cari ruangan...";

                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    roomList.innerHTML = '';

                    if (dropdown) dropdown.classList.remove('hidden');

                    try {
                        const apiUrl = `{{ route('rooms') }}?building_id=${encodeURIComponent(buildingId)}&search=${encodeURIComponent(searchTerm || '')}`;

                        const response = await fetch(apiUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Failed to load rooms: ${response.status} ${response.statusText}`);
                        }

                        const data = await response.json();

                        let rooms = [];
                        if (Array.isArray(data)) {
                            rooms = data;
                        } else if (data.data && Array.isArray(data.data)) {
                            rooms = data.data;
                        } else if (data.rooms && Array.isArray(data.rooms)) {
                            rooms = data.rooms;
                        } else {
                            console.error('Unexpected API response format:', data);
                            throw new Error('Invalid response format from server');
                        }

                        if (rooms.length === 0) {
                            roomList.appendChild(createDropdownItem('Tidak ada ruangan ditemukan untuk gedung ini', 'px-4 py-2 text-gray-500 italic'));
                        } else {
                            rooms.forEach(room => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                const roomName = room.room_name || room.name || '';
                                const roomId = room.room_id || room.id || '';

                                if (!roomName || !roomId) {
                                    console.warn('Room missing required properties:', room);
                                    return;
                                }

                                li.textContent = roomName;
                                li.setAttribute('data-id', roomId);
                                li.setAttribute('data-name', roomName);

                                li.addEventListener('click', function () {
                                    selectedRoomId.value = this.getAttribute('data-id');
                                    searchInput.value = this.getAttribute('data-name');
                                    dropdown.classList.add('hidden');
                                });

                                roomList.appendChild(li);
                            });
                        }
                    } catch (error) {
                        console.error('Error loading rooms:', error);
                        roomList.innerHTML = '';
                        roomList.appendChild(createDropdownItem(`Error: ${error.message}`, 'px-4 py-2 text-red-500'));
                    } finally {
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    }
                }

                function checkUrlParams() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('success')) {
                        alert('Aset berhasil diperbarui!');
                    }
                }

                window.changeAssetPage = function (page) {
                    const limit = document.getElementById('assetPerPageSelect')?.value || 10;

                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);
                    window.location.href = url.toString();
                };

                window.changeAssetPerPage = function (perPage) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', perPage);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                };

                const importAssetBtn = document.getElementById('importAssetBtn');
                const importAssetModal = document.getElementById('importAssetModal');
                const importAssetModalContent = document.getElementById('importAssetModalContent');

                if (importAssetBtn && importAssetModal && importAssetModalContent) {
                    importAssetBtn.addEventListener('click', function () {
                        openModal(importAssetModal, importAssetModalContent);
                    });
                }

                const excelFile = document.getElementById('excel_file');
                const excelFileNameContainer = document.getElementById('excel-file-name');
                const excelFileNameText = document.getElementById('file-name-text');
                const removeExcelBtn = document.getElementById('remove-excel');
                const previewBtn = document.getElementById('preview-btn');
                const excelErrorMsg = document.getElementById('excel-error');
                const excelLoadingIndicator = document.getElementById('excel-loading');

                if (excelFile) {
                    excelFile.addEventListener('change', function (e) {
                        if (excelErrorMsg) excelErrorMsg.classList.add('hidden');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const fileExt = file.name.split('.').pop().toLowerCase();

                            if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                                if (excelErrorMsg) {
                                    excelErrorMsg.textContent = 'Jenis file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                                    excelErrorMsg.classList.remove('hidden');
                                }
                                this.value = '';
                                if (excelFileNameContainer) excelFileNameContainer.classList.add('hidden');
                                if (previewBtn) previewBtn.disabled = true;
                                return;
                            }

                            if (excelFileNameText) excelFileNameText.textContent = file.name;
                            if (excelFileNameContainer) excelFileNameContainer.classList.remove('hidden');
                            if (previewBtn) previewBtn.disabled = false;
                        } else {
                            if (excelFileNameContainer) excelFileNameContainer.classList.add('hidden');
                            if (previewBtn) previewBtn.disabled = true;
                        }
                    });
                }

                if (removeExcelBtn) {
                    removeExcelBtn.addEventListener('click', function () {
                        if (excelFile) excelFile.value = '';
                        if (excelFileNameContainer) excelFileNameContainer.classList.add('hidden');
                        if (previewBtn) previewBtn.disabled = true;
                        if (excelErrorMsg) excelErrorMsg.classList.add('hidden');
                    });
                }

                if (previewBtn) {
                    previewBtn.addEventListener('click', function () {
                        if (!excelFile || !excelFile.files || !excelFile.files[0]) {
                            if (excelErrorMsg) {
                                excelErrorMsg.textContent = 'Silakan pilih file terlebih dahulu.';
                                excelErrorMsg.classList.remove('hidden');
                            }
                            return;
                        }

                        const file = excelFile.files[0];

                        if (excelLoadingIndicator) excelLoadingIndicator.classList.remove('hidden');
                        if (excelErrorMsg) excelErrorMsg.classList.add('hidden');

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            try {
                                const data = new Uint8Array(e.target.result);
                                const workbook = XLSX.read(data, { type: 'array' });
                                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                                const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                                if (rows.length < 2) {
                                    throw new Error('The file contains no data or is missing headers.');
                                }

                                processExcelData(rows);

                                if (excelLoadingIndicator) excelLoadingIndicator.classList.add('hidden');

                                document.getElementById('import-step-1').classList.add('hidden');
                                document.getElementById('import-step-2').classList.remove('hidden');
                            } catch (error) {
                                console.error('Excel parsing error:', error);
                                if (excelLoadingIndicator) excelLoadingIndicator.classList.add('hidden');
                                if (excelErrorMsg) {
                                    excelErrorMsg.textContent = 'Error processing file: ' + error.message;
                                    excelErrorMsg.classList.remove('hidden');
                                }
                            }
                        };

                        reader.onerror = function () {
                            console.error('FileReader error:', reader.error);
                            if (excelLoadingIndicator) excelLoadingIndicator.classList.add('hidden');
                            if (excelErrorMsg) {
                                excelErrorMsg.textContent = 'Galat membaca file. Silakan coba file lain.';
                                excelErrorMsg.classList.remove('hidden');
                            }
                        };

                        reader.readAsArrayBuffer(file);
                    });
                }

                const backToUploadBtn = document.getElementById('back-to-upload-btn');
                if (backToUploadBtn) {
                    backToUploadBtn.addEventListener('click', function () {
                        document.getElementById('import-step-2').classList.add('hidden');
                        document.getElementById('import-step-1').classList.remove('hidden');
                    });
                }

                function processExcelData(data) {
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

                        item.asset_master_id = getValue(['asset_master_id', 'asset master id', 'master id', 'master_id', 'kode master aset']);
                        item.serial_number = getValue(['serial_number', 'serial number', 'serialnumber', 'serial', 'nomor serial']);
                        item.room_id = getValue(['room_id', 'room id', 'room', 'nama ruangan']);
                        item.purchase_date = getValue(['purchase_date', 'purchase date', 'date', 'purchasedate', 'tanggal pembelian']);
                        item.purchase_cost = getValue(['purchase_cost', 'purchase cost', 'cost', 'price', 'biaya pembelian']);
                        item.warranty_end_date = getValue(['warranty_end_date', 'warranty end date', 'warranty', 'warrantyenddate', 'tanggal akhir garansi']);
                        item.condition = getValue(['condition', 'asset condition', 'asset_condition', 'kondisi']) || 'good';
                        item.user_id = getValue(['user_id', 'user id', 'user', 'userid', 'nomor karyawan']);
                        item.current_status = getValue(['current_status', 'current status', 'status']) || 'available';

                        const rawDepreciationMethod = getValue(['depreciation_method', 'depreciation method', 'method', 'metode depresiasi']);

                        let normalizedMethod = null;

                        if (rawDepreciationMethod) {
                            const depMethodLower = typeof rawDepreciationMethod === 'string'
                                ? rawDepreciationMethod.toLowerCase().trim()
                                : String(rawDepreciationMethod).toLowerCase().trim();

                            if (depMethodLower === 'straight line') {
                                normalizedMethod = 'straight_line';
                            } else if (depMethodLower === 'declining balance') {
                                normalizedMethod = 'declining_balance';
                            } else if (depMethodLower === 'double declining balance') {
                                normalizedMethod = 'double_declining_balance';
                            } else if (depMethodLower === '150% declining balance') {
                                normalizedMethod = 'declining_balance_150';
                            } else if (depMethodLower === 'sum of the year\'s digits') {
                                normalizedMethod = 'sum_of_years_digits';
                            } else {
                                normalizedMethod = depMethodLower;
                            }
                        }

                        item.depreciation_method = normalizedMethod;
                        item.acquisition_cost = getValue(['acquisition_cost', 'acquisition cost', 'acquisitioncost', 'biaya perolehan']);
                        item.salvage_value = getValue(['salvage_value', 'salvage value', 'salvagevalue', 'nilai sisa']);
                        item.asset_life_months = getValue(['asset_life_months', 'asset life months', 'asset life', 'umur aset', 'umur aset (bulan)']);
                        item.date_acquired = getValue(['date_acquired', 'date acquired', 'dateacquired', 'tanggal perolehan']);

                        if (!item.asset_master_id) {
                            warnings.push(`Row ${rowIndex + 1}:  Asset Master ID Tidak Ditemukan`);
                        }

                        if (!item.serial_number) {
                            warnings.push(`Row ${rowIndex + 1}: Nomor Seri Tidak Ditemukan`);
                        }

                        if (!item.room_id) {
                            warnings.push(`Row ${rowIndex + 1}: ID Ruangan Tidak Ditemukan`);
                        }

                        item._rowNum = rowIndex + 1;

                        previewData.push(item);
                    });

                    const serialNumberMap = {};
                    previewData.forEach(item => {
                        if (item.serial_number) {
                            if (!serialNumberMap[item.serial_number]) {
                                serialNumberMap[item.serial_number] = [];
                            }
                            serialNumberMap[item.serial_number].push(item._rowNum);
                        }
                    });

                    Object.entries(serialNumberMap).forEach(([serialNumber, rows]) => {
                        if (rows.length > 1) {
                            warnings.push(`Nomor Seri Duplikat "${serialNumber}" ditemukan di baris: ${rows.join(', ')}`);
                        }
                    });

                    document.getElementById('excel_data').value = JSON.stringify(previewData);

                    showDataPreview(previewData, warnings);
                }

                function showDataPreview(data, warnings) {
                    const previewTableBody = document.getElementById('preview-table-body');
                    const previewCount = document.getElementById('preview-count');
                    const warningsContainer = document.getElementById('preview-warnings');
                    const warningsList = document.getElementById('warning-list');

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

                        const fields = ['asset_master_id', 'serial_number', 'room_id', 'purchase_date', 'purchase_cost',
                            'warranty_end_date', 'user_id', 'current_status', 'condition', 'depreciation_method',
                            'acquisition_cost', 'salvage_value', 'asset_life_months', 'date_acquired'];

                        fields.forEach(field => {
                            const cell = document.createElement('td');
                            cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';

                            if (typeof item[field] === 'boolean') {
                                const isTrue = item[field];
                                cell.innerHTML = isTrue ?
                                    '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ya</span>' :
                                    '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Tidak</span>';
                            } else {
                                cell.textContent = item[field] || '-';
                            }

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

                        const importBtn = document.getElementById('import-btn');
                        const hasCriticalWarnings = warnings.some(warning =>
                            warning.includes('Asset Master ID Tidak Ditemukan') ||
                            warning.includes('Nomor Seri Tidak Ditemukan') ||
                            warning.includes('ID Ruangan Tidak Ditemukan')
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

                const importForm = document.getElementById('import-form');
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

                    fetch('{{ route('assets.import') }}', {
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
                                const modal = document.getElementById('importAssetModal');
                                closeModal(modal);
                                showNotification('success', data.message || 'Aset berhasil diimpor!');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                console.error('Import error:', data);

                                let errorMessage = data.message || 'Galat terjadi selama pengimporan.';

                                if (data.data && data.data.errors && Array.isArray(data.data.errors)) {
                                    const detailedErrors = data.data.errors.map(error => {
                                        if (error.row && error.reason) {
                                            return `Row ${error.row}: ${error.asset_master_code ? error.asset_master_code + ' - ' : ''}${error.reason || 'Unknown error'}`;
                                        } else if (typeof error === 'string') {
                                            return error;
                                        } else if (error.message) {
                                            return error.message;
                                        }
                                        return 'Unknown error';
                                    });

                                    if (detailedErrors.length > 0) {
                                        errorMessage += '<ul class="mt-2 ml-4 list-disc">';
                                        detailedErrors.forEach(err => {
                                            errorMessage += `<li>${err}</li>`;
                                        });
                                        errorMessage += '</ul>';
                                    }
                                } else if (data.errors) {
                                    errorMessage += '<ul class="mt-2 ml-4 list-disc">';

                                    if (Array.isArray(data.errors)) {
                                        data.errors.forEach(error => {
                                            if (typeof error === 'string') {
                                                errorMessage += `<li>${error}</li>`;
                                            } else if (error.message) {
                                                errorMessage += `<li>${error.message}</li>`;
                                            } else if (error.reason) {
                                                errorMessage += `<li>${error.reason}</li>`;
                                            }
                                        });
                                    } else {
                                        Object.entries(data.errors).forEach(([field, errors]) => {
                                            if (Array.isArray(errors)) {
                                                errors.forEach(error => {
                                                    errorMessage += `<li>${error}</li>`;
                                                });
                                            } else if (typeof errors === 'string') {
                                                errorMessage += `<li>${errors}</li>`;
                                            }
                                        });
                                    }

                                    errorMessage += '</ul>';
                                }

                                showNotification('error', errorMessage);
                            }
                        })
                        .catch(error => {
                            importBtn.disabled = false;
                            importBtn.innerHTML = originalBtnText;

                            console.error('Import fetch error:', error);

                            showNotification('error', 'An unexpected error occurred. Please try again.');
                        });
                });

                function showNotification(type, message) {
                    const notification = document.createElement('div');
                    notification.id = type + 'Notification' + Date.now();
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

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

                        if (hasHTML) {
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

                const searchInput = document.getElementById('searchInput');
                const assetTypeFilter = document.getElementById('assetTypeFilter');
                const statusFilter = document.getElementById('statusFilter');
                const sortOrder = document.getElementById('sortOrder');

                function applyFilters() {
                    const searchValue = searchInput?.value.trim() || '';
                    const typeValue = assetTypeFilter?.value || '';
                    const statusValue = statusFilter?.value || '';
                    const sortValue = sortOrder?.value || '';

                    const url = new URL(window.location.href);

                    ['search', 'asset_type', 'current_status', 'sort', 'page'].forEach(param => {
                        url.searchParams.delete(param);
                    });

                    if (searchValue) url.searchParams.set('search', searchValue);
                    if (typeValue) url.searchParams.set('asset_type', typeValue);
                    if (statusValue) url.searchParams.set('current_status', statusValue);
                    if (sortValue) url.searchParams.set('sort', sortValue);

                    url.searchParams.set('page', 1);

                    window.location.href = url.toString();
                }

                let searchTimeout;
                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });

                assetTypeFilter?.addEventListener('change', applyFilters);
                statusFilter?.addEventListener('change', applyFilters);
                sortOrder?.addEventListener('change', applyFilters);

                const urlParams = new URLSearchParams(window.location.search);
                if (searchInput) searchInput.value = urlParams.get('search') || '';
                if (assetTypeFilter) {
                    const typeValue = urlParams.get('asset_type');
                    if (typeValue) {
                        assetTypeFilter.value = typeValue;
                    }
                }
                if (statusFilter) {
                    const statusValue = urlParams.get('current_status');
                    if (statusValue) {
                        statusFilter.value = statusValue;
                    }
                }
                if (sortOrder) {
                    const sortValue = urlParams.get('sort');
                    if (sortValue) {
                        sortOrder.value = sortValue;
                    }
                }

                window.changeAssetPage = function (page) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);
                    window.location.href = url.toString();
                };

                window.changeAssetPerPage = function (perPage) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('limit', perPage);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                };

                document.getElementById('exportBtn')?.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    const searchParams = url.searchParams;

                    const exportUrl = "{{ route('assets.export.pdf') }}?" + searchParams.toString();

                    window.open(exportUrl, '_blank');
                });

                document.getElementById('deleteAssetForm')?.addEventListener('submit', function (event) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menghapus...</span></div>';

                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });

                // Column header sorting
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
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    @endpush
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printForm = document.getElementById('printQRForm');
        const qrSizeSelect = document.getElementById('qr_size');
        const printPdfBtn = document.getElementById('printPdfBtn');

        if (printForm) {
            printForm.addEventListener('submit', function () {
                sessionStorage.setItem('reloadAfterPrint', 'true');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            });

            if (printPdfBtn) {
                printPdfBtn.addEventListener('click', function () {
                    const assetIds = document.getElementById('printQRAssetIds').value;
                    const qrSize = document.getElementById('qr_size').value;
                    const pdfForm = document.createElement('form');
                    pdfForm.method = 'POST';
                    pdfForm.action = "{{ route('assets.qr.print-pdf') }}";
                    pdfForm.target = '_blank';
                    pdfForm.style.display = 'none';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    pdfForm.appendChild(csrfInput);

                    const assetIdsInput = document.createElement('input');
                    assetIdsInput.type = 'hidden';
                    assetIdsInput.name = 'asset_ids';
                    assetIdsInput.value = assetIds;
                    pdfForm.appendChild(assetIdsInput);

                    const qrSizeInput = document.createElement('input');
                    qrSizeInput.type = 'hidden';
                    qrSizeInput.name = 'qr_size';
                    qrSizeInput.value = qrSize;
                    pdfForm.appendChild(qrSizeInput);

                    document.body.appendChild(pdfForm);
                    pdfForm.submit();

                    const modal = document.getElementById('printQRModal');
                    if (modal) {
                        const modalContent = document.getElementById('printQRModalContent');
                        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    }
                });
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('printQRModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modal);
                }
            }
        });
    });
</script>
