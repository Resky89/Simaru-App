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
                        <button id="printQRBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="text-base">Print QR</span>
                        </button>
                        <button id="downloadPDFBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
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
                                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] view-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                onclick="window.location.href='{{ route('asset-details', ['id' => $asset['asset_id'] ?? '']) }}'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                data-name="{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '' }}"
                                                data-model-number="{{ $asset['model_number'] ?? '' }}"
                                                data-serial-number="{{ $asset['serial_number'] ?? '' }}"
                                                data-purchase-date="{{ $asset['purchase_date'] ?? '' }}"
                                                data-purchase-cost="{{ $asset['purchase_cost'] ?? '' }}"
                                                data-warranty-end-date="{{ $asset['warranty_end_date'] ?? '' }}"
                                                data-current-status="{{ $asset['current_status'] ?? '' }}"
                                                data-condition="{{ $asset['condition'] ?? '' }}"
                                                data-room-id="{{ $asset['room_id'] ?? '' }}"
                                                data-is-depreciable="{{ isset($asset['asset_master']) && isset($asset['asset_master']['is_depreciable']) && $asset['asset_master']['is_depreciable'] ? 'true' : 'false' }}"
                                                data-image-path="{{ $asset['picture_path'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                data-name="{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
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
                    // Set the hidden input for user ID
                    setFieldValue('edit_selected_user_id', asset.user_id);

                    // Find user in the global users data by user_id
                    const users = window.usersData || [];
                    const user = users.find(u => u.user_id == asset.user_id);

                    let userDisplay = `User ID: ${asset.user_id}`;

                    // If found in global data, use employee_number
                    if (user && user.employee_number) {
                        userDisplay = user.employee_number;
                    }
                    // Try from the asset.user data if available
                    else if (asset.user && asset.user.employee_number) {
                        userDisplay = asset.user.employee_number;
                    }
                    // Fallback to name if no employee_number
                    else if (asset.user && asset.user.name) {
                        userDisplay = asset.user.name;
                    }

                    setFieldValue('edit_user_search', userDisplay);
                }

            // Handle asset image preview
            const previewImage = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');

            if (previewImage && previewContainer && asset.picture_path) {
                previewImage.src = `{{ config('app.backend_url') }}/public${asset.picture_path}`;
                previewImage.classList.remove('hidden');
                previewContainer.classList.remove('hidden');
            } else if (previewContainer) {
                previewContainer.classList.add('hidden');
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

            // Image preview handlers
            const imagePreviewHandlers = [
                { input: 'image_file', preview: 'preview-image', container: 'preview-container' },
                { input: 'edit_image_file', preview: 'edit_image_preview', container: 'edit_preview-container' }
            ];

            imagePreviewHandlers.forEach(handler => {
                const input = document.getElementById(handler.input);
                const preview = document.getElementById(handler.preview);
                const container = document.getElementById(handler.container);

                if (input && preview && container) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.classList.remove('hidden');
                                container.classList.remove('hidden');
                            };
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            preview.classList.add('hidden');
                            container.classList.add('hidden');
                        }
                    });
                }
            });

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

                            // Display employee_number if available, otherwise user_id
                            const displayName = user.employee_number
                                ? `${user.employee_number}`
                                : `User ID: ${user.user_id}`;

                            li.textContent = displayName;
                            li.setAttribute('data-id', user.user_id);
                            li.setAttribute('data-name', displayName);

                            li.addEventListener('click', function() {
                                // Set the selected user ID and display
                                selectedUserId.value = this.getAttribute('data-id');

                                // Update the search input
                                searchInput.value = this.getAttribute('data-name');

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
    });
</script>
@endpush
@endsection
