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
                            <span class="text-base">Download PDF</span>
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
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description</th>
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
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['asset_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $asset['description'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    @if(isset($asset['subcategory']) && isset($asset['subcategory']['asset_type']))
                                        @if($asset['subcategory']['asset_type'] == 'medical')
                                            Medical
                                        @elseif($asset['subcategory']['asset_type'] == 'non_medical')
                                            Non Medical
                                        @else
                                            {{ $asset['subcategory']['asset_type'] }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if(isset($asset['subcategory']) && isset($asset['subcategory']['subcategory_name']))
                                            {{ $asset['subcategory']['subcategory_name'] }}
                                        @else
                                            -
                                        @endif
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
                                                data-name="{{ $asset['asset_name'] ?? '' }}"
                                                data-description="{{ $asset['description'] ?? '' }}"
                                                data-subcategory-id="{{ $asset['subcategory']['subcategory_id'] ?? '' }}"
                                                data-model-number="{{ $asset['model_number'] ?? '' }}"
                                                data-serial-number="{{ $asset['serial_number'] ?? '' }}"
                                                data-purchase-date="{{ $asset['purchase_date'] ?? '' }}"
                                                data-purchase-cost="{{ $asset['purchase_cost'] ?? '' }}"
                                                data-warranty-end-date="{{ $asset['warranty_end_date'] ?? '' }}"
                                                data-current-status="{{ $asset['current_status'] ?? '' }}"
                                                data-condition="{{ $asset['condition'] ?? '' }}"
                                                data-room-id="{{ $asset['room']['room_id'] ?? '' }}"
                                                data-brand-id="{{ $asset['brand']['brand_id'] ?? '' }}"
                                                data-is-depreciable="{{ $asset['is_depreciable'] ? 'true' : 'false' }}"
                                                data-image-path="{{ $asset['picture_path'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-asset-btn"
                                                data-id="{{ $asset['asset_id'] ?? '' }}"
                                                data-name="{{ $asset['asset_name'] ?? '' }}">
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
                                    <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No assets found</td>
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

                <!-- Form -->
                <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Image upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Asset Image</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative flex flex-col items-center justify-center">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your image(s) or <span class="text-blue-600">browse</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                    </div>
                                    <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <!-- Preview image di dalam container -->
                                    <div id="preview-container" class="mt-4 w-full hidden">
                                        <img id="preview-image" class="max-h-40 mx-auto rounded-lg" alt="Asset Image">
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Asset Name</label>
                                    <input type="text" name="asset_name" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Asset name">
                                </div>

                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Subcategory</label>
                                    <select name="subcategory_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select subcategory</option>
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory['subcategory_id'] }}">{{ $subcategory['subcategory_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Condition</label>
                                    <select name="condition" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="good">Good</option>
                                        <option value="slighly damage">Slightly Damage</option></option>
                                        <option value="high damage">Highly Damage</option>
                                    </select>
                                </div>

                                <!-- Room Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Room</label>
                                    <select name="room_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room['room_id'] }}">
                                                {{ $room['room_name'] }} ({{ $room['building_name'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea name="description"
                                    class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                    placeholder="Asset description"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Model Number</label>
                                    <input type="text" name="model_number"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Model number">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Serial Number</label>
                                    <input type="text" name="serial_number"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Serial number">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Purchase Date</label>
                                    <input type="date" name="purchase_date"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Purchase Cost</label>
                                    <input type="number" name="purchase_cost" step="0.01"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Warranty End Date</label>
                                    <input type="date" name="warranty_end_date"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                </div>
                                <!-- Brand Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Brand</label>
                                    <select name="brand_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand['brand_id'] }}">{{ $brand['brand_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Depreciation Toggle Switch -->
                            <div class="flex items-center justify-between border-t pt-4">
                                <label for="is_depreciable" class="text-base font-semibold text-[#666666]">Enable Asset Depreciation</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_depreciable" id="is_depreciable" class="sr-only peer depreciation-toggle" value="1">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                        peer-checked:after:border-white after:content-[''] after:absolute
                                        after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                        after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-[#213268]"></div>
                                    <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">No</span>
                                </label>
                            </div>

                            <!-- Depreciation Fields (Hidden by default) -->
                            <div id="depreciation_fields" class="space-y-4 hidden border rounded-lg p-4 border-dashed border-gray-300">
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Depreciation Information</h3>

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Depreciation Method</label>
                                    <select name="depreciation_method" id="depreciation_method"
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
                                        <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Salvage Value</label>
                                        <input type="number" step="0.01" name="salvage_value" id="salvage_value"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Asset Life (months)</label>
                                        <input type="number" name="asset_life_months" id="asset_life_months"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Date Acquired</label>
                                        <input type="date" name="date_acquired" id="date_acquired"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
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

<!-- Edit Asset Modal - Fixed with proper depreciation fields section -->
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

                <!-- Form -->
                <form id="editAssetForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Asset Information Section -->
                            <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                            <!-- Image upload -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Asset Image</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative flex flex-col items-center justify-center">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your images(s) or <span class="text-blue-600">browse</span></p>
                                        <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                    </div>
                                    <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <!-- Preview image di dalam container -->
                                    <div id="edit_preview-container" class="mt-4 w-full hidden">
                                        <img id="edit_image_preview" class="max-h-40 mx-auto rounded-lg hidden" alt="Asset Image">
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Asset Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Asset Name</label>
                                    <input type="text" name="asset_name" id="edit_asset_name" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Asset name">
                                </div>

                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Subcategory</label>
                                    <select name="subcategory_id" id="edit_subcategory_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select subcategory</option>
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory['subcategory_id'] }}">{{ $subcategory['subcategory_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Condition</label>
                                    <select name="condition" id="edit_condition" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="good">Good</option>
                                        <option value="slighly damage">Slightly Damage</option></option>
                                        <option value="high damage">Highly Damage</option>
                                    </select>
                                </div>

                                <!-- Room Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Room</label>
                                    <select name="room_id" id="edit_room_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room['room_id'] }}">
                                                {{ $room['room_name'] }} ({{ $room['building_name'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea name="description" id="edit_description"
                                    class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                    placeholder="Asset description"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Model Number</label>
                                    <input type="text" name="model_number" id="edit_model_number"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Model number">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Serial Number</label>
                                    <input type="text" name="serial_number" id="edit_serial_number"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="Serial number">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Purchase Date</label>
                                    <input type="date" name="purchase_date" id="edit_purchase_date"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Purchase Cost</label>
                                    <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Warranty End Date</label>
                                    <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                </div>
                                <!-- Brand Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Brand</label>
                                    <select name="brand_id" id="edit_brand_id" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Select brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand['brand_id'] }}">{{ $brand['brand_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Depreciation Toggle Switch -->
                            <div class="flex items-center justify-between border-t pt-4">
                                <label for="edit_is_depreciable" class="text-base font-semibold text-[#666666]">Enable Asset Depreciation</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_depreciable" id="edit_is_depreciable" class="sr-only peer depreciation-toggle" value="1">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                        peer-checked:after:border-white after:content-[''] after:absolute
                                        after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                        after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-[#213268]"></div>
                                    <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">No</span>
                                </label>
                            </div>

                            <!-- Depreciation Fields (Hidden by default) -->
                            <div id="edit_depreciation_fields" class="space-y-4 hidden border rounded-lg p-4 border-dashed border-gray-300">
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
                                        <label class="block text-base font-semibold text-[#666666]">Asset Life (Months)</label>
                                        <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0">
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
                <form id="printQRForm" method="POST" action="{{ url('/assets/qr/print-pdf') }}">
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

<!-- Add a Print QR PDF button to show after generation -->
<div id="printQRPDFNotification" class="hidden fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center justify-between">
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
        </div>
        <div class="ml-4">
            <a id="printPdfLink" href="{{ url('/assets/qr/print-pdf') }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Print PDF
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Definisikan fungsi global untuk modal
    if (typeof window.BrandModalSystem === 'undefined') {
        window.BrandModalSystem = {
            initialized: false,

            // Fungsi utama inisialisasi
            init: function() {
                console.log('BrandModalSystem: Initializing brand modals');
                this.setupModalFunctions();
                this.attachEventHandlers();
                this.initialized = true;
            },

            // Setup fungsi dasar modal
            setupModalFunctions: function() {
                window.openModal = function(modal, content) {
                    console.log('Opening modal', modal.id);
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                };

                window.closeModal = function(modal, content) {
                    console.log('Closing modal', modal.id);
                    content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                    content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                };
            },

            // Pasang event listener menggunakan event delegation
            attachEventHandlers: function() {
                // Add brand button handler
                document.getElementById('addBrandBtn')?.addEventListener('click', function() {
                    const addModal = document.getElementById('addBrandModal');
                    const addContent = document.getElementById('brandModalContent');
                    if (addModal && addContent) {
                        openModal(addModal, addContent);
                    }
                });

                // Event delegation untuk edit dan delete buttons
                document.addEventListener('click', function(event) {
                    // Edit button handler
                    if (event.target.closest('.edit-brand-btn')) {
                        event.preventDefault();
                        const btn = event.target.closest('.edit-brand-btn');
                        const brandId = btn.dataset.id;
                        const brandName = btn.dataset.name;

                        const editModal = document.getElementById('editBrandModal');
                        const editContent = document.getElementById('editBrandModalContent');
                        const editForm = document.getElementById('editBrandForm');
                        const editInput = document.getElementById('editBrandInput');

                        if (editModal && editContent && editForm && editInput) {
                            editForm.action = "{{ route('brands.update','') }}/" + brandId;
                            editInput.value = brandName;
                            openModal(editModal, editContent);
                        }
                    }

                    // Delete button handler
                    if (event.target.closest('.delete-brand-btn')) {
                        event.preventDefault();
                        const btn = event.target.closest('.delete-brand-btn');
                        const brandId = btn.dataset.id;
                        const brandName = btn.dataset.name;

                        const deleteModal = document.getElementById('deleteBrandModal');
                        const deleteContent = document.getElementById('deleteBrandModalContent');
                        const deleteForm = document.getElementById('deleteBrandForm');
                        const deleteBrandNameEl = document.getElementById('deleteBrandName');

                        if (deleteModal && deleteContent && deleteForm && deleteBrandNameEl) {
                            deleteForm.action = "{{ route('brands.destroy', '') }}/" + brandId;
                            deleteBrandNameEl.textContent = brandName;
                            openModal(deleteModal, deleteContent);
                        }
                    }

                    // Close button handler
                    if (event.target.closest('.close-modal')) {
                        const modal = event.target.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        if (modal && content) {
                            closeModal(modal, content);
                        }
                    }
                });

                // Handle click outside modal
                const modals = document.querySelectorAll('[id$="Modal"]');
                modals.forEach(modal => {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            const content = this.querySelector('[id$="ModalContent"]');
                            if (content) {
                                closeModal(this, content);
                            }
                        }
                    });
                });
            }
        };

        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('#successNotification, #errorNotification');
            notifications.forEach(notification => {
                if (notification) {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }
            });
        }, 5000);

        // Definisikan fungsi global untuk inisialisasi
        window.initBrandModals = function() {
            if (!window.BrandModalSystem.initialized) {
                window.BrandModalSystem.init();
            } else {
                console.log('BrandModalSystem already initialized, refreshing event handlers');
                window.BrandModalSystem.attachEventHandlers();
            }
        };
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded: Starting brand modal init');
        setTimeout(function() {
            if (typeof window.initBrandModals === 'function') {
                window.initBrandModals();
            }
        }, 100);
    });

    // Function to change page
    window.changePage = function(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    // Function to change items per page
    window.changePerPage = function(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1); // Reset to first page when changing limit
        window.location.href = url.toString();
    }

    // Asset Modal System
    if (typeof window.AssetModalSystem === 'undefined') {
        window.AssetModalSystem = {
            initialized: false,

            init: function() {
                console.log('AssetModalSystem: Initializing asset modals');
                this.setupModalFunctions();
                this.attachEventHandlers();
                this.setupFormHandlers();
                this.initialized = true;
            },

            setupModalFunctions: function() {
                // Reuse existing modal functions if already defined
                if (typeof window.openModal !== 'function') {
                    window.openModal = function(modal, content) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                        }, 10);
                    };
                }

                if (typeof window.closeModal !== 'function') {
                    window.closeModal = function(modal, content) {
                        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    };
                }
            },

            toggleDepreciationFields: function(form) {
                const isDepreciable = form.querySelector('#edit_is_depreciable').checked;
                const depreciationFields = document.getElementById('edit_depreciation_fields');
                const statusText = form.querySelector('.depreciation-status');

                if (statusText) {
                    statusText.textContent = isDepreciable ? 'Yes' : 'No';
                }

                if (depreciationFields) {
                    if (isDepreciable) {
                        depreciationFields.classList.remove('hidden');

                        // Enable input fields
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.classList.remove('bg-gray-100');
                        });
                    } else {
                        depreciationFields.classList.add('hidden');

                        // Don't disable fields to ensure they're submitted with the form
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            // input.disabled = true; // Don't disable as disabled fields aren't submitted
                            input.classList.add('bg-gray-100');
                        });
                    }
                }
            },

            setupFormHandlers: function() {
                // Setup depreciation toggle for add form
                const addForm = document.querySelector('#addAssetModal form');
                if (addForm) {
                    const depreciableCheckbox = addForm.querySelector('[name="is_depreciable"]');
                    if (depreciableCheckbox) {
                        depreciableCheckbox.addEventListener('change', () => this.toggleDepreciationFields(addForm));
                        // Initial setup
                        this.toggleDepreciationFields(addForm);
                    }

                    // Setup file preview for add form
                    const fileInput = addForm.querySelector('input[type="file"]');
                    const previewContainer = addForm.querySelector('#image_preview_container');
                    if (fileInput && previewContainer) {
                        fileInput.addEventListener('change', function() {
                            if (this.files && this.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = previewContainer.querySelector('img') || document.createElement('img');
                                    img.src = e.target.result;
                                    img.classList.add('w-full', 'h-auto', 'max-h-48', 'object-contain');
                                    if (!img.parentNode) {
                                        previewContainer.innerHTML = '';
                                        previewContainer.appendChild(img);
                                    }
                                    previewContainer.classList.remove('hidden');
                                }
                                reader.readAsDataURL(this.files[0]);
                            }
                        });
                    }
                }

                // Setup depreciation toggle for edit form
                const editForm = document.querySelector('#editAssetModal form');
                if (editForm) {
                    const depreciableCheckbox = editForm.querySelector('[name="is_depreciable"]');
                    if (depreciableCheckbox) {
                        depreciableCheckbox.addEventListener('change', () => this.toggleDepreciationFields(editForm));
                    }

                    // Setup file preview for edit form
                    const fileInput = editForm.querySelector('input[type="file"]');
                    const previewContainer = editForm.querySelector('#edit_image_preview_container');
                    if (fileInput && previewContainer) {
                        fileInput.addEventListener('change', function() {
                            if (this.files && this.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = previewContainer.querySelector('img') || document.createElement('img');
                                    img.src = e.target.result;
                                    img.classList.add('w-full', 'h-auto', 'max-h-48', 'object-contain');
                                    if (!img.parentNode) {
                                        previewContainer.innerHTML = '';
                                        previewContainer.appendChild(img);
                                    }
                                    previewContainer.classList.remove('hidden');
                                }
                                reader.readAsDataURL(this.files[0]);
                            }
                        });
                    }
                }

                // Tambahkan ini di akhir fungsi setupFormHandlers (baris sekitar 1240-1241)
                document.getElementById('editAssetForm')?.addEventListener('submit', function(e) {
                    // Jika is_depreciable dicentang, pastikan semua field depreciation enabled
                    if (document.getElementById('edit_is_depreciable').checked) {
                        const depreciationFields = document.getElementById('edit_depreciation_fields');
                        if (depreciationFields) {
                            const inputFields = depreciationFields.querySelectorAll('input, select');
                            inputFields.forEach(input => {
                                // Re-enable all fields before submitting to ensure values are sent
                                input.disabled = false;
                            });
                        }
                    }
                });
            },

            // Attach event handlers
            attachEventHandlers: function() {
                // Add asset button
                document.getElementById('addAssetBtn')?.addEventListener('click', function() {
                    const addModal = document.getElementById('addAssetModal');
                    const addContent = document.getElementById('addAssetModalContent');
                    if (addModal && addContent) {
                        openModal(addModal, addContent);
                    }
                });

                // Edit asset button handler using event delegation
                document.addEventListener('click', function(event) {
                    // If clicked on edit asset button or its child elements
                    if (event.target.closest('.edit-asset-btn')) {
                        event.preventDefault();
                        const btn = event.target.closest('.edit-asset-btn');
                        const assetId = btn.dataset.id;

                        // Show loading indicator or disable button
                        btn.classList.add('opacity-50', 'pointer-events-none');

                        // Fetch complete asset data from server
                        fetch(`{{ route('assets.get', '') }}/${assetId}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Re-enable button
                            btn.classList.remove('opacity-50', 'pointer-events-none');

                            if (data.error) {
                                console.error('Error fetching asset:', data.error);
                                alert('Failed to load asset data: ' + data.error);
                                return;
                            }

                            // Set up the edit form with the complete asset data
                            setupEditAssetForm(data.asset);
                        })
                        .catch(error => {
                            // Re-enable button
                            btn.classList.remove('opacity-50', 'pointer-events-none');
                            console.error('Error fetching asset:', error);
                            alert('Failed to load asset data. Please try again.');
                        });
                    }

                    // Delete asset button handler - moved inside the click event listener
                    if (event.target.closest('.delete-asset-btn')) {
                        event.preventDefault();
                        const deleteBtn = event.target.closest('.delete-asset-btn');
                        const assetId = deleteBtn.dataset.id;
                        const assetName = deleteBtn.dataset.name;

                        const deleteModal = document.getElementById('deleteAssetModal');
                        const deleteContent = document.getElementById('deleteAssetModalContent');
                        const deleteForm = document.getElementById('deleteAssetForm');
                        const deleteAssetNameEl = document.getElementById('deleteAssetName');

                        if (deleteModal && deleteContent && deleteForm && deleteAssetNameEl) {
                            deleteForm.action = `{{ route('assets.destroy', '') }}/${assetId}`;
                            deleteAssetNameEl.textContent = assetName;
                            openModal(deleteModal, deleteContent);
                        }
                    }
                }.bind(this));

                // Close modal handlers
                document.querySelectorAll('.close-modal').forEach(button => {
                    button.addEventListener('click', function() {
                        const modal = this.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        if (modal && content) {
                            closeModal(modal, content);
                        }
                    });
                });
            }
        };

        // Global init function
        window.initAssetModals = function() {
            if (!window.AssetModalSystem.initialized) {
                window.AssetModalSystem.init();
            } else {
                console.log('AssetModalSystem already initialized, refreshing event handlers');
                window.AssetModalSystem.attachEventHandlers();
            }
        };
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded: Starting asset modal init');
        setTimeout(function() {
            if (typeof window.initAssetModals === 'function') {
                window.initAssetModals();
            }
        }, 100);
    });

    // Add this function to handle depreciation toggle behavior
    function setupDepreciationToggle(prefix = '') {
        const toggleId = prefix ? `${prefix}is_depreciable` : 'is_depreciable';
        const fieldsId = prefix ? `${prefix}depreciation_fields` : 'depreciation_fields';

        const toggle = document.getElementById(toggleId);
        const fields = document.getElementById(fieldsId);

        if (!toggle || !fields) {
            console.error(`Depreciation toggle elements not found. Toggle ID: ${toggleId}, Fields ID: ${fieldsId}`);
            return;
        }

        const statusText = toggle.closest('label').querySelector('.depreciation-status');

        // Set initial state
        updateDepreciationFields(toggle.checked);

        // Add event listener
        toggle.addEventListener('change', function() {
            updateDepreciationFields(this.checked);
        });

        function updateDepreciationFields(isChecked) {
            // Update status text
            if (statusText) {
                statusText.textContent = isChecked ? 'Yes' : 'No';
            }

            // Show/hide fields
            if (isChecked) {
                fields.classList.remove('hidden');

                // Enable input fields inside
                const inputFields = fields.querySelectorAll('input, select, textarea');
                inputFields.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('bg-gray-100');

                    // Mark required fields
                    if (['depreciation_method', 'acquisition_cost', 'salvage_value', 'asset_life_months', 'date_acquired']
                        .some(field => input.id.includes(field))) {
                        input.required = true;
                    }
                });
            } else {
                fields.classList.add('hidden');

                // Make fields not required when depreciation is disabled
                const inputFields = fields.querySelectorAll('input, select, textarea');
                inputFields.forEach(input => {
                    input.required = false;
                    // Don't disable the fields as disabled fields aren't submitted
                    // input.disabled = true;
                    input.classList.add('bg-gray-100');
                });
            }
        }
    }

    // Initialize modal systems
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded: Starting modal initialization');

        // Setup depreciation toggles after DOM is loaded
        setupDepreciationToggle(''); // For add modal
        setupDepreciationToggle('edit_'); // For edit modal

        // Add form submission handlers to ensure depreciation fields are enabled
        document.querySelector('form[action*="assets.store"]')?.addEventListener('submit', function(e) {
            if (document.getElementById('is_depreciable').checked) {
                // Re-enable all depreciation fields right before submission
                const depreciationFields = document.getElementById('depreciation_fields');
                if (depreciationFields) {
                    const inputFields = depreciationFields.querySelectorAll('input, select');
                    inputFields.forEach(input => {
                        input.disabled = false;
                    });
                }
            }
        });

        // Also add for edit form
        document.getElementById('editAssetForm')?.addEventListener('submit', function(e) {
            if (document.getElementById('edit_is_depreciable').checked) {
                // Re-enable all depreciation fields right before submission
                const depreciationFields = document.getElementById('edit_depreciation_fields');
                if (depreciationFields) {
                    const inputFields = depreciationFields.querySelectorAll('input, select');
                    inputFields.forEach(input => {
                        input.disabled = false;
                    });
                }
            }
        });

        if (typeof window.initAssetModals === 'function') {
            window.initAssetModals();
        }
    });

    // Function to set up the edit asset form with data from the server
    function setupEditAssetForm(asset) {
        console.log('Setting up edit asset form with data:', asset);

        // Get form element
        const form = document.getElementById('editAssetForm');
        if (!form) {
            console.error('Edit asset form not found');
            return;
        }

        // Set form action
        form.action = `{{ route('assets.update', '') }}/${asset.asset_id}`;

        // Reset form first to clear any previous data
        form.reset();

        // Fill basic text inputs
        document.getElementById('edit_asset_name').value = asset.asset_name || '';
        document.getElementById('edit_description').value = asset.description || '';
        document.getElementById('edit_model_number').value = asset.model_number || '';
        document.getElementById('edit_serial_number').value = asset.serial_number || '';
        document.getElementById('edit_purchase_cost').value = asset.purchase_cost || '';

        // Handle dates (ensure formatting is correct)
        if (asset.purchase_date) {
            const purchaseDate = asset.purchase_date.split(' ')[0]; // Get just the date part
            document.getElementById('edit_purchase_date').value = purchaseDate;
        }

        if (asset.warranty_end_date) {
            const warrantyDate = asset.warranty_end_date.split(' ')[0]; // Get just the date part
            document.getElementById('edit_warranty_end_date').value = warrantyDate;
        }

        // Properly select values in dropdowns - after they've been populated with options
        setTimeout(() => {
            setSelectValue('edit_subcategory_id', asset.subcategory?.subcategory_id);
            setSelectValue('edit_room_id', asset.room?.room_id);
            setSelectValue('edit_brand_id', asset.brand?.brand_id);
            setSelectValue('edit_condition', asset.condition);
        }, 100);

        // Set depreciation toggle
        const depreciableToggle = document.getElementById('edit_is_depreciable');
        const depreciationStatus = form.querySelector('.depreciation-status');
        const depreciationFields = document.getElementById('edit_depreciation_fields');

        if (depreciableToggle && depreciationFields) {
            // Set the toggle state based on asset data
            depreciableToggle.checked = asset.is_depreciable == 1;

            // Update the status text
            if (depreciationStatus) {
                depreciationStatus.textContent = asset.is_depreciable == 1 ? 'Yes' : 'No';
            }

            // Show/hide depreciation fields based on toggle state
            if (asset.is_depreciable == 1) {
                depreciationFields.classList.remove('hidden');

                // Enable fields
                const inputs = depreciationFields.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('bg-gray-100');
                });

                // Fill depreciation data if available
                if (asset.depreciation) {
                    // Set depreciation method
                    setSelectValue('edit_depreciation_method', asset.depreciation.depreciation_method);

                    // Fill other depreciation fields
                    document.getElementById('edit_acquisition_cost').value = asset.depreciation.acquisition_cost || '';
                    document.getElementById('edit_salvage_value').value = asset.depreciation.salvage_value || '';
                    document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '';

                    // Handle date acquired
                    if (asset.depreciation.date_acquired) {
                        const dateAcquired = asset.depreciation.date_acquired.split(' ')[0];
                        document.getElementById('edit_date_acquired').value = dateAcquired;
                    }
                }
            } else {
                depreciationFields.classList.add('hidden');

                // Disable fields
                const inputs = depreciationFields.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('bg-gray-100');
                });
            }
        }

        // Handle image preview if available
        if (asset.picture_path) {
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');

            if (imagePreview && previewContainer) {
                // Define the base URL - using the confirmed server location
                const baseUrl = "http://localhost:5000/public";

                // Process the image URL
                let imageUrl = asset.picture_path;

                // If URL is not absolute, prepend the base URL
                if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('//')) {
                    // Remove leading slash if present to avoid double slashes
                    if (imageUrl.startsWith('/')) {
                        imageUrl = imageUrl.substring(1);
                    }
                    imageUrl = `${baseUrl}/${imageUrl}`;
                }

                console.log('Using image URL:', imageUrl);

                // Set the image source and display it
                imagePreview.src = imageUrl;
                imagePreview.classList.remove('hidden');
                previewContainer.classList.remove('hidden');
            }
        } else {
            // Hide the preview if no image
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');
            if (imagePreview && previewContainer) {
                imagePreview.classList.add('hidden');
                previewContainer.classList.add('hidden');
            }
        }

        // Open the modal
        const editModal = document.getElementById('editAssetModal');
        const editContent = document.getElementById('editAssetModalContent');
        if (editModal && editContent) {
            openModal(editModal, editContent);
        }
    }

    // Helper function to properly set dropdown values
    function setSelectValue(selectId, value) {
        const select = document.getElementById(selectId);
        if (!select || value === undefined || value === null) {
            return;
        }

        // Convert to string for comparison
        const valueStr = String(value);

        // Try to find exact match first
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === valueStr) {
                select.selectedIndex = i;
                return;
            }
        }

        // If no exact match found, try case-insensitive match
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value.toLowerCase() === valueStr.toLowerCase()) {
                select.selectedIndex = i;
                return;
            }
        }

        console.warn(`No matching option found for ${selectId} with value "${valueStr}"`);
    }

    // Function untuk menambahkan/menghapus atribut required pada field depreciation
    function toggleDepreciationFields(asset) {
        const isDepreciable = document.getElementById('edit_is_depreciable').checked;
        const depreciationFields = document.getElementById('edit_depreciation_fields');
        const depreciationStatus = document.querySelector('.depreciation-status');

        if (depreciationStatus) {
            depreciationStatus.textContent = isDepreciable ? 'Yes' : 'No';
        }

        if (!depreciationFields) return;

        if (isDepreciable) {
            depreciationFields.classList.remove('hidden');

            // Enable fields dan set required attribute
            const requiredFields = [
                'edit_depreciation_method',
                'edit_acquisition_cost',
                'edit_salvage_value',
                'edit_asset_life_months',
                'edit_date_acquired'
            ];

            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = false;
                    field.required = true;
                    field.classList.remove('bg-gray-100');
                }
            });

            // Fill depreciation data if available
            if (asset && asset.depreciation) {
                // Set depreciation method
                setSelectValue('edit_depreciation_method', asset.depreciation.depreciation_method);

                // Fill other depreciation fields
                document.getElementById('edit_acquisition_cost').value = asset.depreciation.acquisition_cost || asset.purchase_cost || '';
                document.getElementById('edit_salvage_value').value = asset.depreciation.salvage_value || '';
                document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '';

                // Handle date acquired
                if (asset.depreciation.date_acquired) {
                    const dateAcquired = asset.depreciation.date_acquired.split(' ')[0];
                    document.getElementById('edit_date_acquired').value = dateAcquired;
                }
            } else if (asset) {
                // Isi dengan data default jika tidak ada data depreciation
                document.getElementById('edit_acquisition_cost').value = asset.purchase_cost || '';
                document.getElementById('edit_date_acquired').value = asset.purchase_date ? asset.purchase_date.split(' ')[0] : '';
            }
        } else {
            depreciationFields.classList.add('hidden');

            // Don't disable fields to ensure values are submitted, just make them not required
            const inputs = depreciationFields.querySelectorAll('input, select');
            inputs.forEach(input => {
                // input.disabled = true; // Don't disable as disabled fields aren't submitted
                input.required = false;
                input.classList.add('bg-gray-100');
            });
        }

        // Handle image preview jika tersedia sebagai bagian dari asset
        if (asset && asset.picture_path) {
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');
            if (imagePreview && previewContainer) {
                imagePreview.src = asset.picture_path;
                imagePreview.classList.remove('hidden');
                previewContainer.classList.remove('hidden');
            }
        } else if (asset) {
            // Hide preview jika tidak ada gambar
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');
            if (imagePreview && previewContainer) {
                imagePreview.classList.add('hidden');
                previewContainer.classList.add('hidden');
            }
        }
    }

    // Menambahkan event listener untuk toggleDepreciationFields setiap kali toggle diubah
    document.addEventListener('DOMContentLoaded', function() {
        const depreciableToggle = document.getElementById('edit_is_depreciable');
        if (depreciableToggle) {
            depreciableToggle.addEventListener('change', function() {
                // Ambil data asset dari fungsi terakhir
                const assetName = document.getElementById('edit_asset_name').value;
                const assetId = document.getElementById('editAssetForm').action.split('/').pop();

                // Buat objek minimum asset untuk diteruskan ke toggleDepreciationFields
                const minimalAsset = {
                    asset_id: assetId,
                    asset_name: assetName,
                    purchase_cost: document.getElementById('edit_purchase_cost').value,
                    purchase_date: document.getElementById('edit_purchase_date').value,
                    is_depreciable: this.checked ? 1 : 0
                };

                toggleDepreciationFields(minimalAsset);
            });
        }
    });

    // Consolidated function to fetch asset data for editing
    function fetchAssetForEdit(assetId) {
        console.log('Fetching asset data for ID:', assetId);

        // Show loading indicator or spinner here if needed

        return fetch(`{{ route('assets.get', '') }}/${assetId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            if (!data.asset) {
                throw new Error('No asset data returned from server');
            }

            // If fresh dropdown data is available, update the dropdowns
            if (data.subcategories && data.subcategories.length > 0) {
                updateDropdownOptions('edit_subcategory_id', data.subcategories, 'subcategory_id', 'subcategory_name');
            }

            if (data.rooms && data.rooms.length > 0) {
                updateDropdownOptions('edit_room_id', data.rooms, 'room_id', 'room_name', (room) => {
                    return `${room.room_name} (${room.building_name})`;
                });
            }

            if (data.brands && data.brands.length > 0) {
                updateDropdownOptions('edit_brand_id', data.brands, 'brand_id', 'brand_name');
            }

            return data.asset;
        });
    }

    // Helper function to update dropdown options
    function updateDropdownOptions(selectId, items, valueField, textField, textFormatter = null) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Save the current selected value
        const currentValue = select.value;

        // Keep the first option (usually "Select...")
        const firstOption = select.options.length > 0 ? select.options[0] : null;

        // Clear existing options
        select.innerHTML = '';

        // Add back the first option if it existed
        if (firstOption) {
            select.appendChild(firstOption);
        }

        // Add new options
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = textFormatter ? textFormatter(item) : item[textField];
            select.appendChild(option);
        });

        // Try to restore the previous selection
        if (currentValue) {
            setSelectValue(selectId, currentValue);
        }
    }

    // Set up the edit button click handler
    document.addEventListener('DOMContentLoaded', function() {
        // Use event delegation for edit buttons
        document.addEventListener('click', function(event) {
            const editButton = event.target.closest('.edit-asset-btn');
            if (!editButton) return;

            event.preventDefault();

            const assetId = editButton.dataset.id;

            // Disable button and show loading state
            editButton.classList.add('opacity-50', 'pointer-events-none');

            // Fetch asset data and dropdown options
            fetch(`{{ route('assets.get', '') }}/${assetId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data from server:', data);

                if (data.error) {
                    throw new Error(data.error);
                }

                if (!data.asset) {
                    throw new Error('No asset data returned from server');
                }

                // Update dropdowns with fresh data before filling form
                if (data.subcategories && data.subcategories.length > 0) {
                    updateDropdownOptions('edit_subcategory_id', data.subcategories, 'subcategory_id', 'subcategory_name');
                }

                if (data.rooms && data.rooms.length > 0) {
                    updateDropdownOptions('edit_room_id', data.rooms, 'room_id', 'room_name', (room) => {
                        return `${room.room_name} (${room.building_name || ''})`;
                    });
                }

                if (data.brands && data.brands.length > 0) {
                    updateDropdownOptions('edit_brand_id', data.brands, 'brand_id', 'brand_name');
                }

                // Now fill the form with asset data
                setupEditAssetForm(data.asset);
            })
            .catch(error => {
                console.error('Error fetching asset data:', error);
                alert('Failed to load asset data: ' + error.message);
            })
            .finally(() => {
                // Re-enable button
                editButton.classList.remove('opacity-50', 'pointer-events-none');
            });
        });
    });

    // Helper function to update dropdown options
    function updateDropdownOptions(selectId, items, valueField, textField, textFormatter = null) {
        const select = document.getElementById(selectId);
        if (!select) {
            console.error(`Dropdown with ID ${selectId} not found`);
            return;
        }

        console.log(`Updating ${selectId} dropdown with ${items.length} options`);

        // Save the current selected value if any
        const currentValue = select.value;

        // Keep the first option (usually "Select...")
        const firstOption = select.options.length > 0 ? select.options[0].cloneNode(true) : null;

        // Clear existing options
        select.innerHTML = '';

        // Add back the first option if it existed
        if (firstOption) {
            select.appendChild(firstOption);
        }

        // Add new options
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = textFormatter ? textFormatter(item) : item[textField];
            select.appendChild(option);
        });

        // Try to restore the previous selection or set to new value if it exists
        if (currentValue) {
            setSelectValue(selectId, currentValue);
        }
    }

    // Function to set up the edit asset form with data from the server
    function setupEditAssetForm(asset) {
        console.log('Setting up edit asset form with data:', asset);

        // Get form element
        const form = document.getElementById('editAssetForm');
        if (!form) {
            console.error('Edit asset form not found');
            return;
        }

        // Set form action
        form.action = `{{ route('assets.update', '') }}/${asset.asset_id}`;

        // Reset form first to clear any previous data
        form.reset();

        // Fill basic text inputs
        document.getElementById('edit_asset_name').value = asset.asset_name || '';
        document.getElementById('edit_description').value = asset.description || '';
        document.getElementById('edit_model_number').value = asset.model_number || '';
        document.getElementById('edit_serial_number').value = asset.serial_number || '';
        document.getElementById('edit_purchase_cost').value = asset.purchase_cost || '';

        // Handle dates (ensure formatting is correct)
        if (asset.purchase_date) {
            const purchaseDate = asset.purchase_date.split(' ')[0]; // Get just the date part
            document.getElementById('edit_purchase_date').value = purchaseDate;
        }

        if (asset.warranty_end_date) {
            const warrantyDate = asset.warranty_end_date.split(' ')[0]; // Get just the date part
            document.getElementById('edit_warranty_end_date').value = warrantyDate;
        }

        // Properly select values in dropdowns - after they've been populated with options
        setTimeout(() => {
            setSelectValue('edit_subcategory_id', asset.subcategory?.subcategory_id);
            setSelectValue('edit_room_id', asset.room?.room_id);
            setSelectValue('edit_brand_id', asset.brand?.brand_id);
            setSelectValue('edit_condition', asset.condition);
        }, 100);

        // Set depreciation toggle
        const depreciableToggle = document.getElementById('edit_is_depreciable');
        const depreciationStatus = form.querySelector('.depreciation-status');
        const depreciationFields = document.getElementById('edit_depreciation_fields');

        if (depreciableToggle && depreciationFields) {
            // Set the toggle state based on asset data
            depreciableToggle.checked = asset.is_depreciable == 1;

            // Update the status text
            if (depreciationStatus) {
                depreciationStatus.textContent = asset.is_depreciable == 1 ? 'Yes' : 'No';
            }

            // Show/hide depreciation fields based on toggle state
            if (asset.is_depreciable == 1) {
                depreciationFields.classList.remove('hidden');

                // Enable fields
                const inputs = depreciationFields.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('bg-gray-100');
                });

                // Fill depreciation data if available
                if (asset.depreciation) {
                    // Set depreciation method
                    setSelectValue('edit_depreciation_method', asset.depreciation.depreciation_method);

                    // Fill other depreciation fields
                    document.getElementById('edit_acquisition_cost').value = asset.depreciation.acquisition_cost || '';
                    document.getElementById('edit_salvage_value').value = asset.depreciation.salvage_value || '';
                    document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '';

                    // Handle date acquired
                    if (asset.depreciation.date_acquired) {
                        const dateAcquired = asset.depreciation.date_acquired.split(' ')[0];
                        document.getElementById('edit_date_acquired').value = dateAcquired;
                    }
                }
            } else {
                depreciationFields.classList.add('hidden');

                // Disable fields
                const inputs = depreciationFields.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('bg-gray-100');
                });
            }
        }

        // Handle image preview if available
        if (asset.picture_path) {
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');

            if (imagePreview && previewContainer) {
                // Define the base URL - using the confirmed server location
                const baseUrl = "http://localhost:5000/public";

                // Process the image URL
                let imageUrl = asset.picture_path;

                // If URL is not absolute, prepend the base URL
                if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('//')) {
                    // Remove leading slash if present to avoid double slashes
                    if (imageUrl.startsWith('/')) {
                        imageUrl = imageUrl.substring(1);
                    }
                    imageUrl = `${baseUrl}/${imageUrl}`;
                }

                console.log('Using image URL:', imageUrl);

                // Set the image source and display it
                imagePreview.src = imageUrl;
                imagePreview.classList.remove('hidden');
                previewContainer.classList.remove('hidden');
            }
        } else {
            // Hide the preview if no image
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');
            if (imagePreview && previewContainer) {
                imagePreview.classList.add('hidden');
                previewContainer.classList.add('hidden');
            }
        }

        // Open the modal
        const editModal = document.getElementById('editAssetModal');
        const editContent = document.getElementById('editAssetModalContent');
        if (editModal && editContent) {
            openModal(editModal, editContent);
        }
    }

    // Helper function to properly set dropdown values
    function setSelectValue(selectId, value) {
        const select = document.getElementById(selectId);
        if (!select || value === undefined || value === null) {
            return;
        }

        // Convert to string for comparison
        const valueStr = String(value);

        // Try to find exact match first
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === valueStr) {
                select.selectedIndex = i;
                return;
            }
        }

        // If no exact match found, try case-insensitive match
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value.toLowerCase() === valueStr.toLowerCase()) {
                select.selectedIndex = i;
                return;
            }
        }

        console.warn(`No matching option found for ${selectId} with value "${valueStr}"`);
    }

    // Add the image preview functionality for both add and edit modals
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview for Add Asset modal
        const addImageInput = document.querySelector('#addAssetModal input[type="file"]');
        const addPreviewContainer = document.getElementById('preview-container');
        const addPreviewImage = document.getElementById('preview-image');

        if (addImageInput && addPreviewContainer && addPreviewImage) {
            addImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addPreviewImage.src = e.target.result;
                        addPreviewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Image preview for Edit Asset modal
        const editImageInput = document.querySelector('#editAssetModal input[type="file"]');
        const editPreviewContainer = document.getElementById('edit_preview-container');
        const editPreviewImage = document.getElementById('edit_image_preview');

        if (editImageInput && editPreviewContainer && editPreviewImage) {
            editImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editPreviewImage.src = e.target.result;
                        editPreviewImage.classList.remove('hidden');
                        editPreviewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });

    // Consolidated code for form submission handler - this replaces the redundant implementations
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener for edit asset form
        const editForm = document.getElementById('editAssetForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Enable all depreciation fields before form submission
                const isDepreciable = document.getElementById('edit_is_depreciable').checked;
                if (isDepreciable) {
                    const depreciationFields = document.getElementById('edit_depreciation_fields');
                    if (depreciationFields) {
                        const inputs = depreciationFields.querySelectorAll('input, select');

                        // Validate that all required fields are filled
                        let allFilled = true;
                        inputs.forEach(input => {
                            // Re-enable fields so their values are sent
                            input.disabled = false;

                            // Check if field is empty
                            if (input.required && !input.value.trim()) {
                                allFilled = false;
                                input.classList.add('border-red-500');
                            } else {
                                input.classList.remove('border-red-500');
                            }
                        });

                        // Stop submission if any fields are empty
                        if (!allFilled) {
                            e.preventDefault();
                            alert('Please fill all required depreciation fields');
                            return false;
                        }
                    }
                }
            });
        }
    });

    // Single implementation of updateDropdownOptions - remove any duplicates
    function updateDropdownOptions(selectId, options, valueKey, textKey, textFormatter) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Store the currently selected value before clearing options
        const currentValue = select.value;

        // Clear existing options except the first one (if it's a placeholder)
        while (select.options.length > 1 && select.options[0].disabled) {
            select.remove(1);
        }

        // If no placeholder exists, remove all options
        if (select.options.length > 0 && !select.options[0].disabled) {
            select.innerHTML = '';
        }

        // Add new options
        options.forEach(option => {
            const optionValue = option[valueKey];
            const optionText = textFormatter ? textFormatter(option) : option[textKey];
            const optElement = document.createElement('option');
            optElement.value = optionValue;
            optElement.textContent = optionText;
            select.appendChild(optElement);
        });

        return currentValue;
    }

    // Function to change asset page
    window.changeAssetPage = function(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    // Function to change asset items per page
    window.changeAssetPerPage = function(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1); // Reset to first page when changing limit
        window.location.href = url.toString();
    }

    // Function to change brand page
    window.changeBrandPage = function(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('brand_page', page);
        window.location.href = url.toString();
    }

    // Function to change brand items per page
    window.changeBrandPerPage = function(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('brand_limit', limit);
        url.searchParams.set('brand_page', 1); // Reset to first page when changing limit
        window.location.href = url.toString();
    }

    // Select All functionality
    const selectAllCheckbox = document.getElementById('select-all-assets');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('table tbody input.asset-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

    // Debug code to check room data structure
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Debugging room data structure in ViewAsset.blade.php');

        // Check room data structure in add modal
        const roomSelect = document.querySelector('select[name="room_id"]');
        if (roomSelect) {
            console.log('Add Modal Room Select Options:', Array.from(roomSelect.options).map(opt => ({
                value: opt.value,
                text: opt.textContent
            })));
        }

        // Inspect all room data passed to the view
        const roomData = @json($rooms);
        console.log('Room Data Passed to View:', roomData);

        // Check if building_name exists at root level
        if (roomData && roomData.length > 0) {
            const firstRoom = roomData[0];
            console.log('First Room Object Structure:', firstRoom);
            console.log('Has building_name at root?', 'building_name' in firstRoom);
            console.log('Has building object?', 'building' in firstRoom);

            if ('building' in firstRoom) {
                console.log('Building object structure:', firstRoom.building);
            }
        }
    });

    // Removed duplicate event handlers for printQRBtn and printQRForm

    // Select all assets checkbox
    document.getElementById('select-all-assets')?.addEventListener('change', function() {
        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // QR Code and Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we have a success message about QR generation
        @if(session('success') && str_contains(session('success'), 'QR codes generated'))
            document.getElementById('printQRPDFNotification').classList.remove('hidden');

            // Get the stored asset_ids from localStorage
            const storedAssetIds = localStorage.getItem('selectedAssetIds');
            if (storedAssetIds) {
                // Update the Print PDF link to include the selected asset IDs
                const printPdfLink = document.getElementById('printPdfLink');
                if (printPdfLink) {
                    printPdfLink.href = "{{ url('/assets/qr/print-pdf') }}?asset_ids=" + storedAssetIds;
                    console.log('Updated print PDF link with asset IDs:', storedAssetIds);
                }
            }

            // Hide after 10 seconds
            setTimeout(function() {
                document.getElementById('printQRPDFNotification').classList.add('hidden');
            }, 10000);
        @endif

        // SINGLE Print QR button event handler - make sure no other event handlers exist for this button!
        const printQRBtn = document.getElementById('printQRBtn');
        if (printQRBtn) {
            // Remove any existing event listeners (just to be sure)
            const newPrintQRBtn = printQRBtn.cloneNode(true);
            printQRBtn.parentNode.replaceChild(newPrintQRBtn, printQRBtn);

            // Add the only event listener we want
            newPrintQRBtn.addEventListener('click', function(e) {
                if (e) e.preventDefault(); // Prevent default action
                if (e) e.stopPropagation(); // Stop event propagation

                // Get selected assets
                const selectedCheckboxes = document.querySelectorAll('.asset-checkbox:checked');
                console.log('Selected checkboxes:', selectedCheckboxes.length);

                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => {
                    console.log('Checkbox dataset:', checkbox.dataset);
                    return checkbox.dataset.assetId;
                });

                console.log('Selected asset IDs:', selectedIds);

                if (selectedIds.length === 0) {
                    alert('Please select at least one asset to print QR code.');
                    return;
                }

                // Store the selected IDs in localStorage for later use
                localStorage.setItem('selectedAssetIds', selectedIds.join(','));

                // Set the selected IDs to the hidden input
                document.getElementById('selectedAssetIds').value = selectedIds.join(',');
                console.log('Hidden input value set to:', document.getElementById('selectedAssetIds').value);

                // Update the count text
                document.getElementById('selectedAssetsCount').textContent =
                    `Selected Assets: ${selectedIds.length}`;

                // Show the modal
                const printQRModal = document.getElementById('printQRModal');
                const printQRModalContent = document.getElementById('printQRModalContent');
                if (printQRModal && printQRModalContent) {
                    openModal(printQRModal, printQRModalContent);
                }

                return false; // Prevent default action
            });
        }

        // SINGLE Form submission handler
        const printQRForm = document.getElementById('printQRForm');
        if (printQRForm) {
            // Remove any existing event listeners (just to be sure)
            const newPrintQRForm = printQRForm.cloneNode(true);
            printQRForm.parentNode.replaceChild(newPrintQRForm, printQRForm);

            // Add the only event listener we want
            newPrintQRForm.addEventListener('submit', function(e) {
                // Prevent default to handle form submission manually
                e.preventDefault();

                // Get the selected asset IDs
                const selectedIds = document.getElementById('selectedAssetIds').value;
                console.log('Form submission - selected IDs value:', selectedIds);

                if (!selectedIds) {
                    alert('No assets selected.');
                    return false;
                }

                console.log('Submitting form with asset IDs:', selectedIds);

                // Get the form data
                const qrSize = document.querySelector('select[name="qr_size"]').value;
                const quantity = document.querySelector('input[name="quantity"]').value || 1;

                // Close the modal
                const printQRModal = document.getElementById('printQRModal');
                const printQRModalContent = document.getElementById('printQRModalContent');
                if (printQRModal && printQRModalContent) {
                    closeModal(printQRModal, printQRModalContent);
                }

                // Open PDF directly in a new tab instead of showing notification
                window.open("{{ url('/assets/qr/print-pdf') }}?asset_ids=" + selectedIds + "&qr_size=" + qrSize + "&quantity=" + quantity, '_blank');

                // Still submit the form to generate the PDF
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ url('/assets/qr/generate-bulk') }}";
                form.style.display = 'none';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                // Add asset IDs
                const assetIdsInput = document.createElement('input');
                assetIdsInput.type = 'hidden';
                assetIdsInput.name = 'asset_ids';
                assetIdsInput.value = selectedIds;
                form.appendChild(assetIdsInput);

                // Add QR size
                const qrSizeInput = document.createElement('input');
                qrSizeInput.type = 'hidden';
                qrSizeInput.name = 'qr_size';
                qrSizeInput.value = qrSize;
                form.appendChild(qrSizeInput);

                // Add quantity
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = 'quantity';
                quantityInput.value = quantity;
                form.appendChild(quantityInput);

                // Add form to the document and submit it
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Make sure close buttons work
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });
    });

    // REMOVE ALL OTHER EVENT LISTENERS FOR printQRBtn
    // ...

</script>
@endpush
@endsection
