@extends('Layout.app')

@section('title', 'Asset Management')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Asset Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ASSET</h1>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button id="importAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                            </svg>
                            <span class="text-base">Import Excel</span>
                        </button>
                        <button id="printQRBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="text-base">Print QR</span>
                        </button>
                        <button id="exportBtn" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span class="text-base">Export PDF</span>
                        </button>
                        <button id="addAssetBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Add Asset</span>
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Search by asset name, code, or category..."
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
                            <option value="" disabled selected>Select Type</option>
                            <option value="">All Types</option>
                            <option value="medical">Medical</option>
                            <option value="non_medical">Non Medical</option>
                        </select>

                        <select id="statusFilter"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Status</option>
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="check out">Check Out</option>
                            <option value="dispose">Dispose</option>
                            <option value="lost">Lost</option>
                            <option value="under repair">Under Repair</option>
                        </select>

                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Sort Order</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
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
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Code</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Type</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Category Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($assets) && count($assets) > 0)
                                @foreach($assets as $asset)
                                <tr data-asset-id="{{ $asset['asset_id'] ?? '' }}">
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        <input type="checkbox" class="asset-checkbox checkbox checkbox-sm" data-asset-id="{{ $asset['asset_id'] ?? '' }}" />
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
                                                    $assetType = 'Medical';
                                                } elseif (strpos($code, 'NMED-') === 0) {
                                                    $assetType = 'Non Medical';
                                                }
                                            @endphp
                                            {{ $assetType }}
                                        @else
                                            Non Medical
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        {{ $asset['asset_master']['subcategory_name'] ?? '-' }}
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        @php
                                            $statusText = 'UNKNOWN';
                                            $statusColor = 'bg-gray-500';

                                            if(isset($asset['current_status'])) {
                                                switch(strtolower($asset['current_status'])) {
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
                                                    default:
                                                        $statusText = strtoupper($asset['current_status']);
                                                }
                                            }
                                        @endphp
                                        <span class="px-2 py-1 rounded-md text-xs text-white {{ $statusColor }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <button class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors view-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                onclick="window.location.href='{{ route('asset-details', ['id' => $asset['asset_id'] ?? '']) }}'">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button class="p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors edit-asset-btn"
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
                                                data-image-path="{{ $asset['picture_path'] ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                data-name="{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No assets found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Assets -->
                @if(isset($assets_pagination) && $assets_pagination)
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($assets_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changeAssetPage({{ ($assets_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($assets_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        <div class="flex gap-2">
                            @php
                                $currentPage = $assets_pagination['current_page'] ?? 1;
                                $lastPage = $assets_pagination['last_page'] ?? 1;
                            @endphp

                            @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                   class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($assets_pagination['current_page'] ?? 1) >= ($assets_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changeAssetPage({{ ($assets_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($assets_pagination['current_page'] ?? 1) >= ($assets_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($assets ?? []) }} of {{ count($assets ?? []) }} entries
                            @endif
                        </span>
                        <select id="assetPerPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeAssetPerPage(this.value)">
                            <option value="10" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($assets_pagination['per_page']) && $assets_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Asset Modal -->
<div id="addAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="addAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD ASSET</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Add Asset Form -->
                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Asset Master Dropdown -->
                                <div class="mb-4">
                                    <label for="asset_master_id" class="block text-gray-700 text-sm font-bold mb-2">Asset Master</label>
                                    <div class="relative">
                                        <input type="text" id="asset_master_search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Search asset master...">
                                        <input type="hidden" name="asset_master_id" id="selected_asset_master_id">
                                        <input type="hidden" id="selected_is_depreciable" value="false">

                                        <!-- Dropdown -->
                                        <div id="asset_master_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                            <!-- Loading indicator -->
                                            <div id="asset_master_loading" class="flex justify-center py-2">
                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                            <ul id="asset_master_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>
                                <div class="mb-4">
                                    <label for="serial_number" class="block text-gray-700 text-sm font-bold mb-2">Serial Number</label>
                                    <input type="text" name="serial_number" id="serial_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Serial number">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="purchase_date" class="block text-gray-700 text-sm font-bold mb-2">Purchase Date</label>
                                    <input type="date" name="purchase_date" id="purchase_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label for="purchase_cost" class="block text-gray-700 text-sm font-bold mb-2">Purchase Cost</label>
                                    <input type="number" name="purchase_cost" id="purchase_cost" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="0.00">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="warranty_end_date" class="block text-gray-700 text-sm font-bold mb-2">Warranty End Date</label>
                                    <input type="date" name="warranty_end_date" id="warranty_end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <!-- Room Dropdown -->
                                <div class="mb-4">
                                    <label for="room_id" class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                                    <div class="relative">
                                        <input type="text" id="room_search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Search for room...">
                                        <input type="hidden" name="room_id" id="selected_room_id">
                                        <div id="room_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                            <!-- Loading indicator -->
                                            <div id="room_loading" class="flex justify-center py-2">
                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                            <ul id="room_list" class="max-h-56 overflow-y-auto"></ul>
                                        </div>
                                    </div>
                                    <div id="selected_room_display" class="hidden">
                                        <span id="selected_room_name"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="condition" class="block text-gray-700 text-sm font-bold mb-2">Condition</label>
                                    <select name="condition" id="condition" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="good">Good</option>
                                        <option value="slightly damage">Slightly Damage</option>
                                        <option value="high damage">Highly Damage</option>
                                    </select>
                                </div>
                                <!-- User ID Field -->
                                <div class="mb-4">
                                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Karyawan yang Bertanggung Jawab</label>
                                    <div class="relative">
                                        <input type="text" id="user_search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Cari karyawan (nomor karyawan)...">
                                        <input type="hidden" name="user_id" id="selected_user_id">
                                        <div id="user_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                            <!-- Loading indicator -->
                                            <div id="user_loading" class="flex justify-center py-2">
                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                            <ul id="user_list" class="max-h-56 overflow-y-auto"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Depreciation Fields Section -->
                            <div id="depreciation_fields" class="space-y-4 border rounded-lg p-4 border-dashed border-gray-300 hidden">
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Depreciation Information</h3>

                                <div class="mb-4">
                                    <label for="depreciation_method" class="block text-gray-700 text-sm font-bold mb-2">Depreciation Method</label>
                                    <select name="depreciation_method" id="depreciation_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Straight Line">Straight Line</option>
                                        <option value="Double Declining Balance">Double Declining Balance</option>
                                        <option value="150% Declining Balance">150% Declining Balance</option>
                                        <option value="Sum of the Year's Digits">Sum of the Year's Digits</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="acquisition_cost" class="block text-gray-700 text-sm font-bold mb-2">Acquisition Cost</label>
                                        <input type="number" name="acquisition_cost" id="acquisition_cost" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="0.00">
                                    </div>
                                    <div class="mb-4">
                                        <label for="salvage_value" class="block text-gray-700 text-sm font-bold mb-2">Salvage Value</label>
                                        <input type="number" name="salvage_value" id="salvage_value" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="asset_life_months" class="block text-gray-700 text-sm font-bold mb-2">Asset Life (months)</label>
                                        <input type="number" name="asset_life_months" id="asset_life_months" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div class="mb-4">
                                        <label for="date_acquired" class="block text-gray-700 text-sm font-bold mb-2">Date Acquired</label>
                                        <input type="date" name="date_acquired" id="date_acquired" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Asset Modal -->
<div id="editAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ASSET</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Edit Asset Form -->
                <form id="editAssetForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="edit_asset_master_id" class="block text-gray-700 text-sm font-bold mb-2">Asset Master</label>
                                    <div class="relative">
                                        <input type="text" id="edit_asset_master_search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Search asset master...">
                                        <input type="hidden" name="asset_master_id" id="edit_selected_asset_master_id">
                                        <input type="hidden" id="edit_selected_is_depreciable" value="false">

                                        <!-- Dropdown -->
                                        <div id="edit_asset_master_dropdown" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none sm:text-sm hidden">
                                            <!-- Loading indicator -->
                                            <div id="edit_asset_master_loading" class="flex justify-center py-2">
                                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                            <ul id="edit_asset_master_list" class="max-h-56 overflow-y-auto"></ul>
                                    </div>
                                </div>
                            </div>
                                <div class="mb-4">
                                    <label for="edit_serial_number" class="block text-gray-700 text-sm font-bold mb-2">Serial Number</label>
                                    <input type="text" name="serial_number" id="edit_serial_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Serial number">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="edit_purchase_date" class="block text-gray-700 text-sm font-bold mb-2">Purchase Date</label>
                                    <input type="date" name="purchase_date" id="edit_purchase_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label for="edit_purchase_cost" class="block text-gray-700 text-sm font-bold mb-2">Purchase Cost</label>
                                    <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="0.00">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Warranty End Date</label>
                                    <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                </div>
                                <!-- Room Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Room</label>
                                    <div class="relative">
                                        <input type="text" id="edit_room_search"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Search for room..." autocomplete="off">
                                        <input type="hidden" name="room_id" id="edit_selected_room_id" required>
                                        <div id="edit_room_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                            <div id="edit_room_loading" class="p-2 text-gray-500 text-center">
                                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>Loading rooms...</span>
                                            </div>
                                            <ul id="edit_room_list" class="py-1"></ul>
                                        </div>
                                    </div>
                                    <div id="edit_selected_room_display" class="hidden">
                                        <span id="edit_selected_room_name"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Condition</label>
                                    <select name="condition" id="edit_condition" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="good">Good</option>
                                        <option value="slightly damage">Slightly Damage</option>
                                        <option value="high damage">Highly Damage</option>
                                    </select>
                                </div>
                                <!-- User ID Field -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Karyawan yang Bertanggung Jawab</label>
                                    <div class="relative">
                                        <input type="text" id="edit_user_search"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                        <input type="hidden" name="user_id" id="edit_selected_user_id">
                                        <div id="edit_user_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                            <div id="edit_user_loading" class="p-2 text-gray-500 text-center">
                                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>Loading users...</span>
                                            </div>
                                            <ul id="edit_user_list" class="py-1"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Depreciation Fields Section -->
                            <div id="edit_depreciation_fields" class="space-y-4 border rounded-lg p-4 border-dashed border-gray-300 hidden">
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Depreciation Information</h3>

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Depreciation Method</label>
                                    <select name="depreciation_method" id="edit_depreciation_method"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="Straight Line">Straight Line</option>
                                        <option value="Double Declining Balance">Double Declining Balance</option>
                                        <option value="150% Declining Balance">150% Declining Balance</option>
                                        <option value="Sum of the Year's Digits">Sum of the Year's Digits</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Acquisition Cost</label>
                                        <input type="number" step="0.01" name="acquisition_cost" id="edit_acquisition_cost"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Salvage Value</label>
                                        <input type="number" step="0.01" name="salvage_value" id="edit_salvage_value"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Asset Life (months)</label>
                                        <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Date Acquired</label>
                                        <input type="date" name="date_acquired" id="edit_date_acquired"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Asset Modal -->
<div id="deleteAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE ASSET</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="deleteAssetForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Are you sure you want to delete this asset? This action cannot be undone.</p>
                                <p id="deleteAssetName" class="text-base font-semibold text-center mt-2"></p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successNotification" class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>
<script>
    // Auto-hide the success notification after 5 seconds
    setTimeout(function() {
        const notification = document.getElementById('successNotification');
        if (notification) {
            notification.style.transition = "opacity 1s ease";
            notification.style.opacity = 0;
            setTimeout(function() {
                notification.remove();
            }, 1000);
        }
    }, 5000);
</script>
@endif

@if(session('error'))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>
<script>
    // Auto-hide the error notification after 5 seconds
    setTimeout(function() {
        const notification = document.getElementById('errorNotification');
        if (notification) {
            notification.style.transition = "opacity 1s ease";
            notification.style.opacity = 0;
            setTimeout(function() {
                notification.remove();
            }, 1000);
        }
    }, 5000);
</script>
@endif

<!-- Modified Print QR Modal -->
<div id="printQRModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="printQRModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">PRINT QR CODE</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="printQRForm" method="POST" action="{{ url('/assets/qr/print-pdf') }}" target="_blank">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <div class="space-y-2">
                                <p class="text-base text-gray-600">Select the size of QR codes to print.</p>
                                <p id="selectedAssetsCount" class="font-semibold text-center"></p>
                            </div>

                            <!-- QR Size Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">QR Size (mm)</label>
                                <select name="qr_size" required class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    <option value="40">40mm x 40mm</option>
                                    <option value="50" selected>50mm x 50mm</option>
                                    <option value="60">60mm x 60mm</option>
                                    <option value="80">80mm x 80mm</option>
                                </select>
                            </div>

                            <!-- Hidden field to store selected asset IDs -->
                            <input type="hidden" id="selectedAssetIds" name="asset_ids" value="">

                            <!-- Hidden field for quantity dengan nilai default 1 -->
                            <input type="hidden" name="quantity" value="1">

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Generate PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="printQRPDFNotification" class="hidden fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold">QR Codes Generated!</p>
                <p>Your QR codes are ready to print.</p>
            </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.classList.add('hidden')">×</span>
        </div>
</div>

<!-- Import Asset Modal -->
<div id="importAssetModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="importAssetModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPORT ASSETS</h2>
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
                                <p class="font-medium text-blue-600 mb-2">Import Instructions:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Use the Excel template format for importing</li>
                                    <li>Required columns: Asset Master ID, Serial Number, Room ID, etc.</li>
                                    <li>Maximum 100 records per import</li>
                                    <li>File types supported: .xlsx, .xls, .csv</li>
                                </ul>
                                <div class="mt-3 flex justify-end">
                                    <a href="{{ asset('docs/ImportAssetTemplate.xlsx') }}" download class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download Template
                                    </a>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#213268]">Excel File</label>
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
                                        <p class="mt-1 text-sm text-gray-600">Drag your Excel file or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: xlsx, xls, csv</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="excel-error" class="hidden text-red-500 text-sm"></div>

                            <!-- Loading Indicator -->
                            <div id="excel-loading" class="hidden text-center py-2">
                                <div class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-sm text-gray-600">Processing Excel data...</p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="button" id="preview-btn" disabled class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Preview Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Import Progress -->
                <div id="import-step-2" class="hidden">
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Preview Header -->
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-[#213268]">Data Preview</h3>
                                <span class="text-sm text-gray-500" id="preview-count">0 items found</span>
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
                                            <th class="p-3 text-left text-xs font-semibold">Purchase Date</th>
                                            <th class="p-3 text-left text-xs font-semibold">Purchase Cost</th>
                                            <th class="p-3 text-left text-xs font-semibold">Warranty End Date</th>
                                            <th class="p-3 text-left text-xs font-semibold">User ID</th>
                                            <th class="p-3 text-left text-xs font-semibold">Current Status</th>
                                            <th class="p-3 text-left text-xs font-semibold">Condition</th>
                                            <th class="p-3 text-left text-xs font-semibold">Depreciation Method</th>
                                            <th class="p-3 text-left text-xs font-semibold">Acquisition Cost</th>
                                            <th class="p-3 text-left text-xs font-semibold">Salvage Value</th>
                                            <th class="p-3 text-left text-xs font-semibold">Asset Life (months)</th>
                                            <th class="p-3 text-left text-xs font-semibold">Date Acquired</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-table-body">
                                        <!-- Preview data will be inserted here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Warning/Error Messages -->
                            <div id="preview-warnings" class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                <p class="font-medium mb-2">Warnings:</p>
                                <ul class="list-disc pl-5" id="warning-list">
                                    <!-- Warning messages will be inserted here -->
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button type="button" id="back-to-upload-btn" class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Back
                                </button>
                                <form action="{{ route('assets.import') }}" method="POST" id="import-form" class="w-2/3" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="excel_data" id="excel_data">
                                    <button type="submit" id="import-btn" class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                        Import Data
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Import Result -->
                <div id="import-step-3" class="hidden">
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg font-semibold text-[#213268]">Import Successful!</p>
                                <p class="mt-2 text-sm text-gray-600">Your assets have been imported successfully.</p>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="close-modal px-6 py-2 bg-[#213268] text-white rounded-lg hover:bg-[#152451] transition-colors duration-200">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store user data in a global variable
    window.usersData = @json($users ?? []);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debounce utility function to limit how often a function can be called
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

        // Reusable function to toggle depreciation fields visibility
        function toggleDepreciationFields(depreciationFields, isDepreciable) {
            if (!depreciationFields) return;

            const inputs = depreciationFields.querySelectorAll('input, select');

                if (isDepreciable) {
                    depreciationFields.classList.remove('hidden');
                    inputs.forEach(input => {
                        input.disabled = false;
                        input.required = true;
                    });
                } else {
                    depreciationFields.classList.add('hidden');
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.required = false;
                    });
                }
        }

        // Helper function to set field value safely
        function setFieldValue(fieldId, value) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = value || '';
            }
        }

        // Helper function to set select dropdown value
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

        // Function to open modal with animation
        window.openModal = function(modal, content) {
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                content.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            }, 10);
        };

        // Function to close modal with animation
        window.closeModal = function(modal) {
            if (!modal) return;

            const content = modal.querySelector('.transform');
            if (!content) return;

            // Reset form jika ada di dalam modal
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => {
                form.reset();

                // Reset hidden inputs yang mungkin tidak terpengaruh oleh form.reset()
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    input.value = '';
                });

                // Reset semua text inputs
                const textInputs = form.querySelectorAll('input[type="text"], input[type="search"]');
                textInputs.forEach(input => {
                    input.value = '';
                });

                // Reset select elements
                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                });

                // Sembunyikan dropdown yang mungkin terbuka
                const dropdowns = form.querySelectorAll('[id$="_dropdown"]');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });

                // Reset depreciation fields
                const depreciationFields = form.querySelector('#depreciation_fields') || form.querySelector('#edit_depreciation_fields');
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
            });

            content.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            content.classList.add('opacity-0', 'scale-95', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        // Single implementation of setupWithData for editing assets
        window.setupWithData = function(assetId) {
        console.log('Setting up edit modal for asset ID:', assetId);

        // Get the edit modal elements
        const editModal = document.getElementById('editAssetModal');
        const editModalContent = document.getElementById('editAssetModalContent');

            // Reset form and show loading
        const form = document.getElementById('editAssetForm');
        if (form) {
            form.reset();  // Clear previous values
            form.action = `{{ url('assets') }}/${assetId}`; // Set form action URL
        }

        // Open the modal while loading
        if (editModal && editModalContent) {
            openModal(editModal, editModalContent);
        }

        // Fetch asset data from the server
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
            console.log('API response:', result);

            if (!result.success) {
                console.error('Error fetching asset data:', result.message);
                alert('Failed to load asset data: ' + (result.message || 'Unknown error'));
                return;
            }

            const asset = result.data;
            console.log('Asset data received:', asset);

                // Fill in basic fields
            setFieldValue('edit_serial_number', asset.serial_number);
            setFieldValue('edit_purchase_date', asset.purchase_date);
            setFieldValue('edit_purchase_cost', asset.purchase_cost);
            setFieldValue('edit_warranty_end_date', asset.warranty_end_date);
            setFieldValue('edit_user_id', asset.user_id);

            // Set the asset master information
                const assetMasterId = asset.asset_master_id || (asset.asset_master && asset.asset_master.asset_master_id);
                if (assetMasterId) {
                document.getElementById('edit_selected_asset_master_id').value = assetMasterId;

                // Set display name
                const assetMasterName = asset.asset_master && asset.asset_master.asset_name
                    ? asset.asset_master.asset_name
                    : 'Asset Master ID: ' + assetMasterId;

                document.getElementById('edit_asset_master_search').value = assetMasterName;

                // Set depreciable flag
                const isDepreciable = asset.asset_master && asset.asset_master.is_depreciable === true;
                document.getElementById('edit_selected_is_depreciable').value = isDepreciable ? 'true' : 'false';

                // Show/hide depreciation fields
                const depreciationFields = document.getElementById('edit_depreciation_fields');
                if (depreciationFields) {
                        toggleDepreciationFields(depreciationFields, isDepreciable);
                    }
                }

                // Set room information with proper checks
            if (asset.room_id) {
                // Set the room selection for the searchable dropdown
                document.getElementById('edit_selected_room_id').value = asset.room_id;

                // Set the room display name
                    let roomName = "Room ID: " + asset.room_id;
                if (asset.room) {
                    const buildingName = asset.room.building ? asset.room.building.building_name :
                                        (asset.room.building_name ? asset.room.building_name : 'Unknown Building');
                    roomName = `${asset.room.room_name} (${buildingName})`;
                } else {
                    // Try to find the room in the available rooms data
                        const rooms = @json($rooms ?? []);
                    const selectedRoom = rooms.find(room => room.room_id == asset.room_id);
                    if (selectedRoom) {
                        roomName = `${selectedRoom.room_name} (${selectedRoom.building_name || 'Unknown Building'})`;
                    }
                }

                // Update the search input and display
                document.getElementById('edit_room_search').value = roomName;
                    const roomNameEl = document.getElementById('edit_selected_room_name');
                    if (roomNameEl) roomNameEl.textContent = roomName;
            }

                // Set condition
            setSelectValue('edit_condition', asset.condition || 'good');

            // Handle depreciation fields
            const depreciationFields = document.getElementById('edit_depreciation_fields');
            if (depreciationFields) {
                    // Check if the asset has depreciation data or is depreciable
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

                        // Fill depreciation data from either direct properties or nested object
                        const depData = asset.depreciation || asset;
                        setFieldValue('edit_acquisition_cost', depData.acquisition_cost || '');
                        setFieldValue('edit_salvage_value', depData.salvage_value || '');
                        setFieldValue('edit_asset_life_months', depData.asset_life_months || '');
                        setFieldValue('edit_date_acquired', depData.date_acquired || '');

                        // Handle depreciation method dropdown
                    const depMethodSelect = document.getElementById('edit_depreciation_method');
                        const depreciationMethod = depData.depreciation_method || '';

                        if (depMethodSelect && depreciationMethod) {
                            // Try exact match first
                            let found = false;
                            for (let i = 0; i < depMethodSelect.options.length; i++) {
                                if (depMethodSelect.options[i].value === depreciationMethod) {
                                    depMethodSelect.selectedIndex = i;
                                    found = true;
                                    break;
                                }
                            }

                            // If no exact match, try fuzzy match
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

                // Update user selection display
                if (asset.user_id) {
                    // Log information for debugging
                    console.log('Updating user field for user_id:', asset.user_id);

                    // Set the hidden input for user ID
                    setFieldValue('edit_selected_user_id', asset.user_id);

                    // Find user in the global users data by user_id
                    const users = window.usersData || [];
                    console.log('Available users data:', users);

                    const user = users.find(u => u.user_id == asset.user_id);
                    console.log('Found user in global data:', user);

                    // Check asset.user data
                    console.log('User data from asset:', asset.user);

                    // Start with User ID as fallback
                    let userDisplay = `User ID: ${asset.user_id}`;

                    // Check user from global data
                    if (user) {
                        if (user.employee_number) {
                            userDisplay = user.employee_number;
                            console.log('Using employee_number from global data:', user.employee_number);
                        } else if (user.name) {
                            userDisplay = user.name;
                            console.log('Using name from global data:', user.name);
                        }
                    }
                    // Try from the asset.user data if available
                    else if (asset.user) {
                        if (asset.user.employee_number) {
                            userDisplay = asset.user.employee_number;
                            console.log('Using employee_number from asset.user:', asset.user.employee_number);
                        } else if (asset.user.name) {
                            userDisplay = asset.user.name;
                            console.log('Using name from asset.user:', asset.user.name);
                        }
                    }

                    console.log('Final user display value:', userDisplay);
                    document.getElementById('edit_user_search').value = userDisplay;
                }

        })
        .catch(error => {
            console.error('Error fetching asset data:', error);
            alert('Failed to load asset data. Please try again: ' + error.message);
        });
        };

        // Initialize the asset master dropdown handlers once
        function initAssetMasterListeners() {
            // Add modal - tambahkan listener untuk selected_is_depreciable
            const addSelectedIsDepreciable = document.getElementById('selected_is_depreciable');
            const addDepreciationFields = document.getElementById('depreciation_fields');

            if (addSelectedIsDepreciable && addDepreciationFields) {
                // Observer untuk memantau perubahan nilai
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                            const isDepreciable = addSelectedIsDepreciable.value === 'true';
                            toggleDepreciationFields(addDepreciationFields, isDepreciable);
                        }
                    });
                });

                // Konfigurasi observer
                const config = { attributes: true, attributeFilter: ['value'] };
                observer.observe(addSelectedIsDepreciable, config);

                // Tambahkan juga event listener untuk direct changes
                addSelectedIsDepreciable.addEventListener('change', function() {
                    const isDepreciable = this.value === 'true';
                    toggleDepreciationFields(addDepreciationFields, isDepreciable);
                });

                // Cek juga saat inisialisasi
                const isInitiallyDepreciable = addSelectedIsDepreciable.value === 'true';
                toggleDepreciationFields(addDepreciationFields, isInitiallyDepreciable);
            }

            // Edit modal
            const editAssetMasterSelect = document.getElementById('edit_asset_master_id');
            const editDepreciationFields = document.getElementById('edit_depreciation_fields');

            if (editAssetMasterSelect && editDepreciationFields) {
                editAssetMasterSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const isDepreciable = selectedOption.getAttribute('data-depreciable') === 'true';
                    toggleDepreciationFields(editDepreciationFields, isDepreciable);
                });
            }
        }

        // Consolidated function to initialize event handlers for buttons and modals
        function initEventHandlers() {
            // Edit Asset Button handlers
            document.querySelectorAll('.edit-asset-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const assetId = this.getAttribute('data-id');
                    setupWithData(assetId);
                });
            });

            // Delete Asset Button handlers
            document.querySelectorAll('.delete-asset-btn').forEach(button => {
                button.addEventListener('click', function() {
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

            // Add Asset button handler
            const addAssetBtn = document.getElementById('addAssetBtn');
            const addAssetModal = document.getElementById('addAssetModal');
            const addAssetModalContent = document.getElementById('addAssetModalContent');

            if (addAssetBtn && addAssetModal && addAssetModalContent) {
                addAssetBtn.addEventListener('click', function() {
                    openModal(addAssetModal, addAssetModalContent);
                });
            }

            // Print QR button handler
            document.getElementById('printQRBtn')?.addEventListener('click', function() {
                const checkedAssets = document.querySelectorAll('.asset-checkbox:checked');
                const assetIds = Array.from(checkedAssets).map(checkbox => checkbox.getAttribute('data-asset-id'));

                if (assetIds.length === 0) {
                    alert('Please select at least one asset to print QR codes.');
                    return;
                }

                // Update the hidden input with selected asset IDs
                document.getElementById('selectedAssetIds').value = assetIds.join(',');

                // Update the count display
                document.getElementById('selectedAssetsCount').textContent = `Selected Assets: ${assetIds.length}`;

                // Open the print QR modal
                const printQRModal = document.getElementById('printQRModal');
                const printQRModalContent = document.getElementById('printQRModalContent');
                openModal(printQRModal, printQRModalContent);
            });

            // Select all assets checkbox
            document.getElementById('select-all-assets')?.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.asset-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Close modal buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('[id$="Modal"]');
                    closeModal(modal);
                });
            });

            // Close on outside click
            document.querySelectorAll('.fixed.inset-0.bg-black.bg-opacity-50').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        const modal = this.parentElement;
                        if (modal) {
                            closeModal(modal);
                        }
                    }
                });
            });

            // Edit form submission handler
            const editForm = document.getElementById('editAssetForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const assetId = this.action.split('/').pop();

                    // Set the current_status to 'available' if it doesn't exist in the form
                    if (!formData.has('current_status')) {
                        formData.append('current_status', 'available');
                    }

                    // Check if depreciation fields are visible and add them to formData if they are
                    const depreciationFields = document.getElementById('edit_depreciation_fields');
                    if (depreciationFields && !depreciationFields.classList.contains('hidden')) {
                        // Make sure depreciation data is included
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

                    // Use a traditional form submission
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('assets') }}/${assetId}`;
                    form.style.display = 'none';

                    // Append all form data
                    for (const [key, value] of formData.entries()) {
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

            // Print QR form handler
            const printQRForm = document.getElementById('printQRForm');
            if (printQRForm) {
                printQRForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent the default form submission

                    const formData = new FormData(this);
                    const action = this.action;

                    // Close the modal
                        const modal = document.getElementById('printQRModal');
                        if (modal) {
                            closeModal(modal);
                        }

                    // Create and submit a form to open in a new tab
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;
                    form.target = '_blank'; // Open in new tab
                    form.style.display = 'none';

                    // Append all form data
                    for (const [key, value] of formData.entries()) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        form.appendChild(input);
                    }

                    // Add CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();

                    // Show notification
                    const notification = document.getElementById('printQRPDFNotification');
                    if (notification) {
                        notification.classList.remove('hidden');
                        setTimeout(() => {
                            notification.classList.add('hidden');
                        }, 5000);
                    }

                    // Reload the current page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        }

        // Initialize room search functionality
        function initRoomSearch(
            searchInput,
            dropdown,
            roomList,
            loadingIndicator,
            selectedRoomId,
            selectedRoomName,
            selectedRoomDisplay
        ) {
            if (!searchInput || !dropdown || !roomList) return;

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                dropdown.classList.remove('hidden');
                if (roomList.children.length === 0) {
                    loadRooms(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                loadRooms(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load rooms
            async function loadRooms(searchTerm) {
                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                try {
                    // Use the existing rooms data from the page
                    let rooms = @json($rooms ?? []);

                    // Filter rooms based on search term
                    if (searchTerm) {
                        searchTerm = searchTerm.toLowerCase();
                        rooms = rooms.filter(room =>
                            (room.room_name && room.room_name.toLowerCase().includes(searchTerm)) ||
                            (room.building_name && room.building_name.toLowerCase().includes(searchTerm))
                        );
                    }

                    // Sort rooms by name for better UX
                    rooms.sort((a, b) => (a.room_name || '').localeCompare(b.room_name || ''));

                    // Populate dropdown
                    roomList.innerHTML = '';

                    if (rooms.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'No rooms found';
                        roomList.appendChild(noResults);
                    } else {
                        rooms.forEach(room => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            li.textContent = `${room.room_name} (${room.building_name || 'Unknown Building'})`;
                            li.setAttribute('data-id', room.room_id);
                            li.setAttribute('data-name', `${room.room_name} (${room.building_name || 'Unknown Building'})`);

                            li.addEventListener('click', function() {
                                // Set the selected room ID and name
                                selectedRoomId.value = this.getAttribute('data-id');
                                if (selectedRoomName) {
                                    selectedRoomName.textContent = this.getAttribute('data-name');
                                }

                                // Update the search input
                                searchInput.value = this.getAttribute('data-name');

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            roomList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading rooms:', error);
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Error loading rooms';
                    roomList.appendChild(errorItem);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize asset master search functionality
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

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                dropdown.classList.remove('hidden');
                if (assetMasterList.children.length === 0) {
                    loadAssetMasters(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                loadAssetMasters(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load asset masters
            async function loadAssetMasters(searchTerm) {
                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                assetMasterList.innerHTML = '';

                try {
                    // Fetch asset masters data from the API
                    const response = await fetch(`{{ route('asset-master.data') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch asset masters');
                    }

                    const result = await response.json();
                    let assetMasters = result.masterAssets || [];

                    // Populate dropdown
                    assetMasterList.innerHTML = '';

                    if (assetMasters.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'No asset masters found';
                        assetMasterList.appendChild(noResults);
                    } else {
                        assetMasters.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const assetMasterName = item.asset_name || 'Unknown';

                            li.textContent = assetMasterName;
                            li.setAttribute('data-id', item.asset_master_id);
                            li.setAttribute('data-name', assetMasterName);

                            // Get is_depreciable value
                            const isDepreciable = item.is_depreciable === true;

                            li.setAttribute('data-depreciable', isDepreciable);

                            li.addEventListener('click', function() {
                                // Set the selected asset master ID and name
                                selectedAssetMasterId.value = this.getAttribute('data-id');

                                // Update the search input
                                searchInput.value = this.getAttribute('data-name');

                                // Set is_depreciable flag
                                const isDepreciable = this.getAttribute('data-depreciable') === 'true';
                                selectedIsDepreciable.setAttribute('value', isDepreciable.toString());
                                // Trigger change event
                                const event = new Event('change');
                                selectedIsDepreciable.dispatchEvent(event);

                                // Show/hide depreciation fields based on is_depreciable
                                toggleDepreciationFields(depreciationFields, isDepreciable);

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            assetMasterList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading asset masters:', error);
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Error loading asset masters';
                    assetMasterList.appendChild(errorItem);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize user search functionality
        function initUserSearch(
            searchInput,
            dropdown,
            userList,
            loadingIndicator,
            selectedUserId
        ) {
            if (!searchInput || !dropdown || !userList) return;

            // Toggle dropdown visibility
            searchInput.addEventListener('focus', function() {
                dropdown.classList.remove('hidden');
                if (userList.children.length === 0) {
                    loadUsers(''); // Initial load on focus
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Search input handler with debounce
            const debouncedSearch = debounce(function(e) {
                loadUsers(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);

            // Function to load users
            async function loadUsers(searchTerm) {
                // Show loading indicator
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                userList.innerHTML = '';

                try {
                    // Use locally available data instead of fetching from server
                    let users = window.usersData || [];

                    // Filter users based on search term
                    if (searchTerm) {
                        searchTerm = searchTerm.toLowerCase();
                        users = users.filter(user => {
                            return (user.employee_number && user.employee_number.toLowerCase().includes(searchTerm)) ||
                                   (user.name && user.name.toLowerCase().includes(searchTerm)) ||
                                   (user.user_id && user.user_id.toString().includes(searchTerm));
                        });
                    }

                    // Populate dropdown
                    userList.innerHTML = '';

                    if (users.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'No users found';
                        userList.appendChild(noResults);
                    } else {
                        users.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            // Display employee_number with user's name if available
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

                            li.addEventListener('click', function() {
                                // Set the selected user ID and display
                                selectedUserId.value = this.getAttribute('data-id');

                                // Update the search input with employee number
                                const employeeNumber = this.getAttribute('data-employee-number');
                                if (employeeNumber) {
                                    searchInput.value = employeeNumber;
                                } else {
                                    searchInput.value = this.getAttribute('data-name');
                                }

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            userList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Error processing user data';
                    userList.appendChild(errorItem);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        }

        // Initialize all search components
        function initSearchComponents() {
            // Room search for add modal
            initRoomSearch(
                document.getElementById('room_search'),
                document.getElementById('room_dropdown'),
                document.getElementById('room_list'),
                document.getElementById('room_loading'),
                document.getElementById('selected_room_id'),
                document.getElementById('selected_room_name'),
                document.getElementById('selected_room_display')
            );

            // Room search for edit modal
            initRoomSearch(
                document.getElementById('edit_room_search'),
                document.getElementById('edit_room_dropdown'),
                document.getElementById('edit_room_list'),
                document.getElementById('edit_room_loading'),
                document.getElementById('edit_selected_room_id'),
                document.getElementById('edit_selected_room_name'),
                document.getElementById('edit_selected_room_display')
            );

            // Asset master search for add modal
            initAssetMasterSearch(
                document.getElementById('asset_master_search'),
                document.getElementById('asset_master_dropdown'),
                document.getElementById('asset_master_list'),
                document.getElementById('asset_master_loading'),
                document.getElementById('selected_asset_master_id'),
                document.getElementById('selected_is_depreciable'),
                document.getElementById('depreciation_fields')
            );

            // Asset master search for edit modal
            initAssetMasterSearch(
                document.getElementById('edit_asset_master_search'),
                document.getElementById('edit_asset_master_dropdown'),
                document.getElementById('edit_asset_master_list'),
                document.getElementById('edit_asset_master_loading'),
                document.getElementById('edit_selected_asset_master_id'),
                document.getElementById('edit_selected_is_depreciable'),
                document.getElementById('edit_depreciation_fields')
            );

            // User search for add modal
            initUserSearch(
                document.getElementById('user_search'),
                document.getElementById('user_dropdown'),
                document.getElementById('user_list'),
                document.getElementById('user_loading'),
                document.getElementById('selected_user_id')
            );

            // User search for edit modal
            initUserSearch(
                document.getElementById('edit_user_search'),
                document.getElementById('edit_user_dropdown'),
                document.getElementById('edit_user_list'),
                document.getElementById('edit_user_loading'),
                document.getElementById('edit_selected_user_id')
            );
        }

        // Function to check URL parameters
        function checkUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                alert('Asset updated successfully!');
            }
        }

        // Function to handle changing page
        window.changeAssetPage = function(page) {
            const limit = document.getElementById('assetPerPageSelect')?.value || 10;

            // Traditional page reload method
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        // Function to handle changing items per page
        window.changeAssetPerPage = function(perPage) {
            // Traditional page reload method
            const url = new URL(window.location.href);
            url.searchParams.set('limit', perPage);
            url.searchParams.set('page', 1); // Reset to first page
            window.location.href = url.toString();
        };

        // Initialize everything
        initAssetMasterListeners();
        initEventHandlers();
        initSearchComponents();
        checkUrlParams();

        // Import functionality
        const importAssetBtn = document.getElementById('importAssetBtn');
        const importAssetModal = document.getElementById('importAssetModal');
        const importAssetModalContent = document.getElementById('importAssetModalContent');

        if (importAssetBtn && importAssetModal && importAssetModalContent) {
            importAssetBtn.addEventListener('click', function() {
                openModal(importAssetModal, importAssetModalContent);
            });
        }

        // File input handling
        const excelFile = document.getElementById('excel_file');
        const excelFileNameContainer = document.getElementById('excel-file-name');
        const excelFileNameText = document.getElementById('file-name-text');
        const removeExcelBtn = document.getElementById('remove-excel');
        const previewBtn = document.getElementById('preview-btn');
        const excelErrorMsg = document.getElementById('excel-error');
        const excelLoadingIndicator = document.getElementById('excel-loading');

        if (excelFile) {
            excelFile.addEventListener('change', function(e) {
                if (excelErrorMsg) excelErrorMsg.classList.add('hidden');

                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileExt = file.name.split('.').pop().toLowerCase();

                    if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                        if (excelErrorMsg) {
                            excelErrorMsg.textContent = 'Invalid file type. Please upload an Excel file (.xlsx, .xls) or CSV file.';
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
            removeExcelBtn.addEventListener('click', function() {
                if (excelFile) excelFile.value = '';
                if (excelFileNameContainer) excelFileNameContainer.classList.add('hidden');
                if (previewBtn) previewBtn.disabled = true;
                if (excelErrorMsg) excelErrorMsg.classList.add('hidden');
            });
        }

        // Preview button handling
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                if (!excelFile || !excelFile.files || !excelFile.files[0]) {
                    if (excelErrorMsg) {
                        excelErrorMsg.textContent = 'Please select a file first.';
                        excelErrorMsg.classList.remove('hidden');
                    }
                    return;
                }

                const file = excelFile.files[0];

                if (excelLoadingIndicator) excelLoadingIndicator.classList.remove('hidden');
                if (excelErrorMsg) excelErrorMsg.classList.add('hidden');

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
                            throw new Error('The file contains no data or is missing headers.');
                        }

                        // Process the Excel data
                        processExcelData(rows);

                        if (excelLoadingIndicator) excelLoadingIndicator.classList.add('hidden');

                        // Show step 2
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

                reader.onerror = function() {
                    console.error('FileReader error:', reader.error);
                    if (excelLoadingIndicator) excelLoadingIndicator.classList.add('hidden');
                    if (excelErrorMsg) {
                        excelErrorMsg.textContent = 'Error reading the file. Please try another file.';
                        excelErrorMsg.classList.remove('hidden');
                    }
                };

                reader.readAsArrayBuffer(file);
            });
        }

        // Back button handling
        const backToUploadBtn = document.getElementById('back-to-upload-btn');
        if (backToUploadBtn) {
            backToUploadBtn.addEventListener('click', function() {
                document.getElementById('import-step-2').classList.add('hidden');
                document.getElementById('import-step-1').classList.remove('hidden');
            });
        }

        // Function to process Excel data
        function processExcelData(data) {
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
                item.asset_master_id = getValue(['asset_master_id', 'asset master id', 'master id', 'master_id', 'kode master aset']);
                item.serial_number = getValue(['serial_number', 'serial number', 'serialnumber', 'serial', 'nomor serial']);
                item.room_id = getValue(['room_id', 'room id', 'room', 'nama ruangan']);
                item.purchase_date = getValue(['purchase_date', 'purchase date', 'date', 'purchasedate', 'tanggal pembelian']);
                item.purchase_cost = getValue(['purchase_cost', 'purchase cost', 'cost', 'price', 'biaya pembelian']);
                item.warranty_end_date = getValue(['warranty_end_date', 'warranty end date', 'warranty', 'warrantyenddate', 'tanggal akhir garansi']);
                item.condition = getValue(['condition', 'asset condition', 'asset_condition', 'kondisi']) || 'good';
                item.user_id = getValue(['user_id', 'user id', 'user', 'userid', 'nomor karyawan']);
                item.current_status = getValue(['current_status', 'current status', 'status']) || 'available';

                // Get depreciation method and normalize it
                const rawDepreciationMethod = getValue(['depreciation_method', 'depreciation method', 'method', 'metode depresiasi']);

                // Map the display values from the dropdown to server-expected values
                let normalizedMethod = null;

                if (rawDepreciationMethod) {
                    // Convert to lowercase and trim for more accurate matching
                    const depMethodLower = typeof rawDepreciationMethod === 'string'
                        ? rawDepreciationMethod.toLowerCase().trim()
                        : String(rawDepreciationMethod).toLowerCase().trim();

                    // Map dropdown display values to server-expected values
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
                        // If it's already in server format, keep it
                        normalizedMethod = depMethodLower;
                    }
                }

                item.depreciation_method = normalizedMethod;
                item.acquisition_cost = getValue(['acquisition_cost', 'acquisition cost', 'acquisitioncost', 'biaya perolehan']);
                item.salvage_value = getValue(['salvage_value', 'salvage value', 'salvagevalue', 'nilai sisa']);
                item.asset_life_months = getValue(['asset_life_months', 'asset life months', 'asset life', 'umur aset', 'umur aset (bulan)']);
                item.date_acquired = getValue(['date_acquired', 'date acquired', 'dateacquired', 'tanggal perolehan']);

                // Validate required fields
                if (!item.asset_master_id) {
                    warnings.push(`Row ${rowIndex + 1}: Missing Asset Master ID`);
                }

                if (!item.serial_number) {
                    warnings.push(`Row ${rowIndex + 1}: Missing Serial Number`);
                }

                if (!item.room_id) {
                    warnings.push(`Row ${rowIndex + 1}: Missing Room ID`);
                }

                // Add row index for reference
                item._rowNum = rowIndex + 1;

                previewData.push(item);
            });

            // Check for duplicate serial numbers
            const serialNumberMap = {};
            previewData.forEach(item => {
                if (item.serial_number) {
                    if (!serialNumberMap[item.serial_number]) {
                        serialNumberMap[item.serial_number] = [];
                    }
                    serialNumberMap[item.serial_number].push(item._rowNum);
                }
            });

            // Add duplicate warnings
            Object.entries(serialNumberMap).forEach(([serialNumber, rows]) => {
                if (rows.length > 1) {
                    warnings.push(`Duplicate Serial Number "${serialNumber}" found in rows: ${rows.join(', ')}`);
                }
            });

            // Update hidden field with JSON data for form submission
            document.getElementById('excel_data').value = JSON.stringify(previewData);

            // Show preview with warnings
            showDataPreview(previewData, warnings);
        }

        // Function to show data preview
        function showDataPreview(data, warnings) {
            const previewTableBody = document.getElementById('preview-table-body');
            const previewCount = document.getElementById('preview-count');
            const warningsContainer = document.getElementById('preview-warnings');
            const warningsList = document.getElementById('warning-list');

            if (!previewTableBody || !previewCount) return;

            // Clear previous content
            previewTableBody.innerHTML = '';
            if (warningsList) warningsList.innerHTML = '';
            if (warningsContainer) warningsContainer.classList.add('hidden');

            // Update count
            previewCount.textContent = `${data.length} items found`;

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
                const fields = ['asset_master_id', 'serial_number', 'room_id', 'purchase_date', 'purchase_cost',
                                'warranty_end_date', 'user_id', 'current_status', 'condition', 'depreciation_method',
                                'acquisition_cost', 'salvage_value', 'asset_life_months', 'date_acquired'];

                fields.forEach(field => {
                    const cell = document.createElement('td');
                    cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';

                    // Apply special formatting for boolean fields or use default text
                    if (typeof item[field] === 'boolean') {
                        const isTrue = item[field];
                        cell.innerHTML = isTrue ?
                            '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Yes</span>' :
                            '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">No</span>';
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
                const importBtn = document.getElementById('import-btn');
                const hasCriticalWarnings = warnings.some(warning =>
                    warning.includes('Missing Asset Master ID') ||
                    warning.includes('Missing Serial Number') ||
                    warning.includes('Missing Room ID')
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

        // Handle import form submission with AJAX
        const importForm = document.getElementById('import-form');
        importForm?.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent traditional form submission

            // Get form data
            const formData = new FormData(this);

            // Add the Excel file to the form data if needed
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
                    <span>Importing...</span>
                </div>
            `;

            // Send AJAX request
            fetch('{{ route('assets.import') }}', {
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
                    const modal = document.getElementById('importAssetModal');
                    closeModal(modal);

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

                    // Check for detailed error information in the API response
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

                        // Handle different error formats
                        if (Array.isArray(data.errors)) {
                            // Array of error messages
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
                            // Object with field names as keys
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
                            <p class="font-bold">Success!</p>
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

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const assetTypeFilter = document.getElementById('assetTypeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const sortOrder = document.getElementById('sortOrder');

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const typeValue = assetTypeFilter?.value || '';
            const statusValue = statusFilter?.value || '';
            const sortValue = sortOrder?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'type', 'current_status', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (typeValue) url.searchParams.set('type', typeValue);
            if (statusValue) url.searchParams.set('current_status', statusValue);
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
        statusFilter?.addEventListener('change', applyFilters);
        sortOrder?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (assetTypeFilter) {
            const typeValue = urlParams.get('type');
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

        // Update pagination functions to preserve filters
        window.changeAssetPage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        window.changeAssetPerPage = function(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', perPage);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        };

        // Export PDF functionality
        document.getElementById('exportBtn')?.addEventListener('click', () => {
            // Get current URL parameters
            const url = new URL(window.location.href);
            const searchParams = url.searchParams;

            // Create the PDF export URL with the same parameters
            const exportUrl = "{{ route('assets.export.pdf') }}?" + searchParams.toString();

            // Redirect to the export URL
            window.open(exportUrl, '_blank');
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endpush
@endsection
